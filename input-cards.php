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
if (isset($_GET['coll-name'])) {
	$set = new Collection($_GET['coll-name']);
	$set_id = $set->createOrGet();
}
	$all_sets = Collection::getAll();
?>
<input type="button" onclick="window.location='index.php'" value="Return Home"/>

<div class="col-lg-3">
	<form method="GET" id="new-coll">
		<label for="coll">New Collection Name</label>
		<input id="coll" name="coll-name" type="text">
		<input type="submit" value="Create New Collection">
	</form>
</div>
<div class="col-lg-3">
	<form method="GET" id="import_cards">
		<label for="sets_to_import">Collection</label>
		<select id="sets_to_import" multiple name="import_sets" class="chosen-select">
			<?php foreach($all_sets as $set) { ?>
				<option value="<?= $set['name'] ?>"><?= $set['name'] ?></option>
			<?php } ?>
		</select>
		<label for="perf-name">Perfume Name</label>
		<input id="perf-name" name="perfume-name" type="text">
		<label for="perf-text">Perfume Notes</label>
		<input id="perf-text" name="perfume-text" type="text">
		<input type="submit" value="Import">
	</form>
</div>

<?php

require_once("api/includes.php");

if (isset($_GET['import_sets']) && isset($_GET['perfume-name']) && isset($_GET['perfume-text'])) {
	$valid_sets = $_GET['import_sets'];
	if (!is_array($valid_sets)) {
		$valid_sets = array($valid_sets);
	}
	$collection = $_GET['import_sets'];
	$perfume_name = $_GET['perfume-name'];
	$perfume_text = $_GET['perfume-text'];
	
} else {
	die("Enter information!");
}

$set = new Collection($collection);
$set_id = $set->createOrGet();

$card_obj = new Card();
$card_obj->text = $perfume_text;
$card_obj->name = $perfume_name;
$card_obj->set_id = $set_id;
$card_obj->refnum = strtolower(str_replace(' ','-',$perfume_name));
$card_id = $card_obj->createOrUpdate();
// types should be random tags
/*
foreach($sets as $set) {
	$json = json_decode($set);
	$details = reset($json);

	$set = new Collection($details->name, $details->code);
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
*/
?>
<div class="col-lg-3">
	<input type="button" onclick="window.location='add_cards.php'" value="Go add cards to your library!"/>
</div>
</body>
</html>
