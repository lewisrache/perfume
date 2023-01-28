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
<script>
$(function() {
});

function deleteCollection(id) {
	console.log('deleting '+id);
	$("#coll-id").val(id);
	$("#delete-collection").submit();
}
</script>
</head>
<body>
<?php

require_once(__DIR__ . "/api/includes.php");

$dir = 'sqlite:api/mtg.db';
$dbh  = new PDO($dir) or die("cannot open the database");

if (isset($_POST['collection_id'])) {
	$dbh->query("DELETE FROM sets WHERE id = ".(int)$_POST['collection_id']);
}

$all_sets = Collection::getAll(true);
?>
<form id="delete-collection" method="POST">
	<input name="collection_id" id="coll-id" value="" type="number" hidden>
</form>
<table class="table table-striped sortable" id="cards" style="width:90%">
<col style="width:5%">
<col style="width:18%">
<col style="width:37%">
<col style="width:10%">
<col style="width:20%">
<col style="width:5%">
<col style="width:5%">
<thead>
<tr>
<th>NAME</th><th>PERFUMES</th><th>ACTIONS</th>
</tr>
</thead>
<tbody>
<?php
foreach($all_sets as $collection) {
?>
	<tr class="<?= strtolower(str_replace(' ','_',$z['rarity'])) ?>_card">
		<td><?= $collection['name'] ?></td>
		<td class="card_name"><?= "COMING SOON?" ?></td>
		<td class="card_text"><input type="button" value="Delete" onclick="deleteCollection(<?= $collection['id'] ?>)"></td>
	</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
