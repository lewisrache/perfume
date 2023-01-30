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
</head>
<body>
<?php
require_once(__DIR__ . "/api/includes.php");

if (isset($_POST['yes_delete'])) {
	$dir = 'sqlite:api/mtg.db';
	$dbh  = new PDO($dir) or die("cannot open the database");
	$dbh->query("DELETE FROM card_types WHERE card_id = 'NEW'");

	header('Location: clean_data.php');
	die();
}
		
?>
<div class="col-lg-3">
	<form id="delete_problems" method="POST">
		<input type="hidden" name="yes_delete" value="true">
		<input type="submit" value="Yes, Delete">
	</form>
</div>
</body>
</html>
