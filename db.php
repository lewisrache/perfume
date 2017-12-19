<?php

$dir = 'sqlite:mtg.db';
$dbh  = new PDO($dir) or die("cannot open the database");

$type = "Host";
			$type_check = $dbh->query("select id from type where name = '$type'");
print_r($type_check);
foreach($type_check as $row) {
$type_id = $row['id'];
}
if (!isset($type_id)) {
echo "hey";
				$dbh->query("insert into type (name) values ('$type')");
				$type_id = $dbh->lastInsertId();
}
die( $type_id);
			if ($type_check->rowCount() === 0) {
			} else {
				$type_id = $type_check->fetch()[0];
			}


