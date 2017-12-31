<?php

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

$sets = array();

$handle = fopen("../../Downloads/AllSets.json", 'r');

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
//print_r(substr($sets["Hour of Devastation"],0,1000));
//echo "===\n";
//print_r(substr($sets["Hour of Devastation"],strlen($sets["Hour of Devastation"])-1000));

/*
$hou = json_decode($sets["Hour of Devastation"]);

print_r($hou->HOU->cards[0]);
print_r($hou->HOU->cards[1]);
print_r($hou->HOU->cards[2]);

print_r(array_keys((array)$hou->HOU));
*/
//var_dump($hou);
//echo(json_last_error_msg ());
//$handle = fopen("hou.txt", 'w');
//fwrite($handle, $sets['Hour of Devastation']);
//fclose($handle);

$dir = 'sqlite:mtg.db';
$dbh  = new PDO($dir) or die("cannot open the database");
$dbh->query("delete from sets");

$colour_codes = array();

foreach($sets as $set) {
	$json = json_decode($set);
	$details = reset($json);

	$set_query = "insert into sets (name, code) values ('{$details->name}', '{$details->code}')";
	$dbh->query($set_query);
	$set_id = $dbh->lastInsertId();

	$dbh->query("delete from card where set_id = $set_id");

	foreach($details->cards as $card) {
		$flavour = (isset($card->flavor) ? $card->flavor : "");
		$manacost = (isset($card->manaCost) ? $card->manaCost : NULL);
		$text = (isset($card->text) ? $card->text : "");
		$power = (isset($card->power) ? $card->power : "");
		$toughness = (isset($card->toughness) ? $card->toughness : "");
		$card_query = "insert into card (name, text, type, flavour, manacost, set_id, rarity, layout, power, toughness) values (:name,:text,:type,:flavour,:mana,:set,:rarity,:layout,:power,:toughness)";

		$stmt = $dbh->prepare($card_query);
		$stmt->bindParam(':name', $card->name);
		$stmt->bindParam(':text', $text);
		$stmt->bindParam(':type', $card->type);
		$stmt->bindParam(':flavour', $flavour);
		$stmt->bindParam(':mana', $manacost);
		$stmt->bindParam(':set', $set_id);
		$stmt->bindParam(':rarity', $card->rarity);
		$stmt->bindParam(':layout', $card->layout);
		$stmt->bindParam(':power', $power);
		$stmt->bindParam(':toughness', $toughness);
		$stmt->execute();
		$card_id = $dbh->lastInsertId();

		$dbh->query("delete from card_types where card_id = $card_id");
		$dbh->query("delete from card_colours where card_id = $card_id");

		foreach($card->types as $type) {
			types($type, $dbh, $card_id);
		}
		if (isset($card->subtypes)) {
			foreach($card->subtypes as $type) {
				types($type, $dbh, $card_id);
			}
		}

		if (isset($card->colorIdentity)) {
			foreach($card->colorIdentity as $colour_code) {
				if(!array_key_exists($colour_code, $colour_codes)) {
					$colour_check = $dbh->query("select id from colours where code='$colour_code'");
					$colour_codes[$colour_code] = $colour_check[0];
				}
				$dbh->query("insert into card_colours (card_id, colour_id) values ($card_id, {$colour_codes[$colour_code]})");
			}
		}
	}
}

function types($type, $dbh, $card_id) {
	$type_check = $dbh->query("select id from type where name = '$type'");
	$type_id = null;
	foreach($type_check as $row) {
		$type_id = $row['id'];
	}
	if ($type_id === null) {
		$dbh->query("insert into type (name) values ('$type')");
		$type_id = $dbh->lastInsertId();
	}

	$dbh->query("insert into card_types (card_id, type_id) values ($card_id, $type_id)");
}
