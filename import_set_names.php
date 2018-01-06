<?php

require_once("api/includes.php");

$handle = fopen("data/AllSets.json", 'r');

if ($handle) {
	$ctr = 0;
	$code_found = false;
	$code = "";
	$name = "";
	while (($line = fgets($handle)) !== false) {
		if (preg_match('/^\s{2}"([^"]+)"/', $line, $matches)) {
			$code = $matches[1];
			$code_found = true;
		} else if ($code_found) {
			if (preg_match('/"name": "([^"]*)"/', $line, $matches)) {
				$name = $matches[1];
				$set = new Set($name, $code);
				$set_id = $set->createOrGet();
				echo "$name ($code)\n";
			}
			$code_found = false; 
			$ctr++;
		}
	}
	echo "TOTAL: $ctr\n";
	fclose($handle);
}
