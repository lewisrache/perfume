<?php

require_once("api/includes.php");

if (isset($_GET['import_set'])) {
	$valid_sets = array(
		$_GET['import_set']
	);
} else {
	$valid_sets = array(
		"Unstable",
		"Battle for Zendikar",
		"Oath of the Gatewatch",
		"Eldritch Moon",
		"Shadows over Innistrad",
		"Kaladesh",
		"Aether Revolt",
		"Amonkhet",
		"Hour of Devastation",
		"Ixalan");
}

$handle = fopen("data/AllSets.json", 'r');

if ($handle) {
	$ctr = 0;
	$start_block = false;
	$in_block = false;
	$buffer1 = "";
	$name = "";
	while (($line = fgets($handle)) !== false) {
		if (preg_match('/^\s{2}"/', $line, $matches)) {
			$buffer1 = $line;
			$start_block = true;
		} else if (preg_match('/^\s{2}}/', $line, $matches)) {
			$in_block = false;
			if (array_key_exists($name, $sets)) {
				$sets[$name] .= "}}";
			}
		} else if ($in_block) {
			$sets[$name] .= $line;
		} else { 
			if ($start_block && preg_match('/"name": "([\w\s]*)"/', $line, $matches)) {
				$name = $matches[1];
				if (in_array($name, $valid_sets)) {
					$sets[$name] = "{" . $buffer1 . $line;
					$in_block = true;
					$ctr++;
				}
			}
			$start_block = false; 
		}
	}
	fclose($handle);
}

$colour_codes = array();

foreach($sets as $set) {
	$json = json_decode($set);
	$details = reset($json);

	$set = new Set($details->name, $details->code);
	$set_id = $set->createOrGet();
	foreach($details->cards as $card) {
		$card_obj = new Card();
		$card_obj->flavour = (isset($card->flavor) ? $card->flavor : "");
		$card_obj->manaCost = (isset($card->manaCost) ? $card->manaCost : NULL);
		$card_obj->text = (isset($card->text) ? $card->text : "");
		$card_obj->power = (isset($card->power) ? $card->power : "");
		$card_obj->toughness = (isset($card->toughness) ? $card->toughness : "");
		$card_obj->name = $card->name;
		$card_obj->type_str = $card->type;
		$card_obj->set_id = $set_id;
		$card_obj->rarity = $card->rarity;
		$card_obj->layout = $card->layout;
		$card_obj->refnum = $card->id;

		$card_id = $card_obj->createOrUpdate();

		foreach($card->types as $type_name) {
			$type = new Type($type_name);
			$type_id = $type->createOrGet();
			$card_obj->addType($type_id);
		}
		if (isset($card->subtypes)) {
			foreach($card->subtypes as $type_name) {
				$type = new Type($type_name);
				$type_id = $type->createOrGet();
				$card_obj->addType($type_id);
			}
		}

		if (isset($card->colorIdentity)) {
			foreach($card->colorIdentity as $colour_code) {
				if (!array_key_exists($colour_code, $colour_codes)) {
					$colour = new Colour();
					$colour_codes[$colour_code] = $colour->get($colour_code);
				}
				$card_obj->addColour($colour_codes[$colour_code]);
			}
		}
	}
}

