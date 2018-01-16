<html>
<head>
<meta charset="utf-8">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<link rel="stylesheet" href="bootstrap-sortable-master/Contents/bootstrap-sortable.css">
<link rel="stylesheet" href="bootstrap-chosen-master/bootstrap-chosen.css">
<script src="bootstrap-sortable-master/Scripts/bootstrap-sortable.js"></script>
<script src="bootstrap-sortable-master/Scripts/moment.min.js"></script>
<script src="http://harvesthq.github.io/chosen/chosen.jquery.js"></script>

<script>
$(function() {
	$('.chosen-select').chosen();
	$('.chosen-select-deselect').chosen({ allow_single_deselect: true });
});
</script>
<body>
<?php 
	require_once("api/includes.php");
	$all_sets = Set::getAll();
?>
<div class="col-lg-3">
	<form method="GET" id="import_cards">
		<label for="sets_to_import">Sets to import</label>
		<select id="sets_to_import" multiple name="import_sets[]" class="chosen-select">
			<?php foreach($all_sets as $set) { ?>
				<option value="<?= $set['name'] ?>"><?= $set['name'] . " (".$set['code'].")" ?></option>
			<?php } ?>
		</select>
		<input type="submit" value="Import">
	</form>
</div>

<?php

require_once("api/includes.php");

if (isset($_GET['import_sets'])) {
	$valid_sets = $_GET['import_sets'];
	if (!is_array($valid_sets)) {
		$valid_sets = array($valid_sets);
	}
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
	die("Choose a set!");
}

$sets = array();

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
		$card_obj->manacost = (isset($card->manaCost) ? $card->manaCost : NULL);
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

?>
<div class="col-lg-3">
	<input type="button" onclick="window.location='add_cards.php'" value="Go add cards to your library!"/>
</div>
</body>
</html>
