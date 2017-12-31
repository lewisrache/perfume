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
<script src="http://harvesthq.github.io/chosen/chosen.jquery.js"></script>
<style>
.updated {
	background-color: rgba(141, 255, 138, 0.4);
}
</style>
<script>
$(function() {
	$('.chosen-select').chosen();
	$('.chosen-select-deselect').chosen({ allow_single_deselect: true });
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

	$stmt = $dbh->prepare("update card set num_own = :num_own where id = :id");
	$stmt->execute(array(':num_own'=>$num_own,':id'=>$card_id));
}


$cards = $dbh->query("select card.name, card.id, card.num_own, sets.code from card, sets where sets.id = card.set_id order by card.name asc");
$card_id_to_own = array();

if (isset($card_id)) { ?>
<div class="updated">Updated</div>
<?php }?>
<form id="add_card" method="POST">
	<div class="col-lg-3" style="float:left">
	<select name="card_id" id="card_select" class="chosen-select" onchange="updateOwned()">
	<?php foreach($cards as $idx => $card) { 
		$card['num_own'] = ($card['num_own'] == "" ? 0 : $card['num_own']);
		$card_id_to_own[$card['id']] = $card['num_own'];
		if (isset($card_id) && $card_id == $card['id']) {
			$initial_value = $card['num_own'];
			?><option value="<?= $card['id'] ?>" selected="selected"><?= $card['name'] . " (" . $card['code'] . ")" ?></option><?php
		} else {
			if ($idx === 0) {
				$initial_value = $card['num_own'];
			}
			?><option value="<?= $card['id'] ?>"><?= $card['name'] . " (" . $card['code'] . ")" ?></option><?php
		}
	} ?>
	</select>
	<input type="number" id="owned" name="num_own" value="<?= $initial_value ?>">
	<input type="submit" value="Update">
	</div>
</form>
<div class="col-lg-3" style="float:left">
<table>
<tbody>
<?php
	$all_sets = Set::getAll();
	foreach($all_sets as $set) {
		?><tr><td><?= $set['name'] . " (" . $set['code'] . ")" ?></td></tr>
	<?php }
?>
</tbody>
</table>
</div>
<script>
var cards_to_numbers = Array();
<?php foreach($card_id_to_own as $id => $num) { ?>
	cards_to_numbers[<?= $id ?>] = <?= $num ?>;
<?php } ?>
function updateOwned() {
	var card_id = $('#card_select').val();
	$('#owned').val(cards_to_numbers[card_id]);
}
</script>
</body>
</html>
