<?php
require_once(__DIR__ . "/api/includes.php");
?>
<html>
<head>
<meta charset="utf-8">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<link rel="stylesheet" href="bootstrap-sortable-master/Contents/bootstrap-sortable.css">
<link rel="stylesheet" href="bootstrap-chosen-master/bootstrap-chosen.css">
<script src="bootstrap-sortable-master/Scripts/bootstrap-sortable.js"></script>
<script src="bootstrap-sortable-master/Scripts/moment.min.js"></script>
<script src="chosen_v1.8.2/chosen.jquery.js"></script>
<style>
.updated {
	background-color: rgba(141, 255, 138, 0.4);
}
</style>
<script>
$(function() {
	$('.chosen-select').chosen();
	$('.chosen-select-deselect').chosen({ allow_single_deselect: true });
	<?php if (isset($_POST['card_id'])) { ?>
		$('#update_banner')[0].innerHTML = "Updated ("+$('#card_select option[value=<?= $_POST['card_id'] ?>]')[0].innerHTML + ")";
	<?php } ?>
});
</script>

</head>
<body>
<?php
$dir = 'sqlite:api/mtg.db';
$dbh  = new PDO($dir) or die("cannot open the database");

if (isset($_POST['card_id']) && isset($_POST['num_own'])) {
	$card_id = $_POST['card_id'];
	$num_own = $_POST['num_own'];
	$subtypes = $_POST['subtypes'] ?? [];
	try {
		$originalSubtypes = json_decode($_POST['orig_subtypes']);
		if (is_null($originalSubtypes)) {
			$originalSubtypes = [];
		}
	} catch (Exception $e) {
		$originalSubtypes = [];
	}
	$stmt = $dbh->prepare("update card set num_own = :num_own where id = :id");
	$stmt->execute(array(':num_own'=>$num_own,':id'=>$card_id));
$to_add = array_filter($subtypes, function($type) use ($originalSubtypes) {
	return !in_array($type, $originalSubtypes);
});
	Card::addTypes($card_id, $to_add);
$to_remove = array_filter($originalSubtypes, function($type) use ($subtypes) {
	return !in_array($type, $subtypes);
});
	Card::removeTypes($card_id, $to_remove);
}

$types = Type::getAll();
$cards = $dbh->query("select card.name, card.id, card.num_own from card, sets where sets.id = card.set_id order by card.name asc");
$cards_to_types = Card::getCardsToTypes();
$card_id_to_own = array();

if (isset($card_id)) { ?>
<div class="updated" id="update_banner">Updated ()</div>
<?php }?>
<form id="add_card" method="POST">
	<div class="col-lg-3" style="float:left">
	<select name="card_id" id="card_select" class="chosen-select" onchange="updateOwned()">
		<option value="0">Select a Perfume</option>
	<?php foreach($cards as $idx => $card) { 
		$card['num_own'] = ($card['num_own'] == "" ? 0 : $card['num_own']);
		$card_id_to_own[$card['id']] = $card['num_own'];
		/*if (isset($card_id) && $card_id == $card['id']) {
			$initial_value = $card['num_own'];
			?><option value="<?= $card['id'] ?>" selected="selected"><?= $card['name'] ?></option><?php
		} else {
			if ($idx === 0) {
				$initial_value = $card['num_own'];
			}*/
			?><option value="<?= $card['id'] ?>"><?= $card['name'] ?></option><?php
		//}
	} ?>
	</select>
	<select name="subtypes[]" multiple id="subtype-select" class="chosen-select">
	<?php foreach($types as $idx => $type) { ?>
		<option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
	<?php } ?>
	</select>
	<input type="number" id="owned" name="num_own" value="0">
	<input type="hidden" id="original-subtypes" name="orig_subtypes" value="[]">
	<input type="submit" value="Update">

	<br>
	<br>
	<input type="button" onclick="window.location='cards_ui.php'" value="View Library"/>
	</div>
</form>
<script>
var cards_to_numbers = Array();
var cards_to_types = Array();
<?php foreach($card_id_to_own as $id => $num) { ?>
	cards_to_numbers[<?= $id ?>] = <?= $num ?>;
<?php } ?>
<?php foreach($cards_to_types as $id => $type_id) { ?>
	cards_to_types[<?= $id ?>] = JSON.parse("<?= json_encode($type_id) ?>");
<?php } ?>
function updateOwned() {
	var card_id = $('#card_select').val();
	$('#owned').val(cards_to_numbers[card_id]);
	$('#original-subtypes').val("["+cards_to_types[card_id]+"]");
	$('#subtype-select').val(cards_to_types[card_id]).trigger('chosen:updated');
}
</script>
</body>
</html>
