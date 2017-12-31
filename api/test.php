<?php

require_once(__DIR__ . "/card.php");

$good_id = 909090;

$bad_id = 100000;

echo "GOOD ID TEST: ";
if (Card::exists($good_id)) echo "passed\n"; else echo "failed\n";
echo "BAD ID TEST: ";
if (!Card::exists($bad_id)) echo "passed\n"; else echo "failed\n";


$card = new Card();
$card->name = "TEST CARD";
$card->refnum = 909090;
$card->text = "This is test text";
$card->flavour = "This is flavour text";
$card->power = 1;
$card->toughness = 1;
$card->type_str = "Test - Test";
$card->rarity = "Common";
$card->set_id = 0;

//echo "CARD CREATE TEST: ".
//$card_id = $card->create();
//if ($card_id > 0) echo "passed\n"; else echo "failed\n";

$card_get = new Card();
$card_get->refnum = $good_id;
$card_get->getById($card_get->getId());
print_r($card_get);

echo "Get by Bad ID test: ";
try {
	$card_get->getById($bad_id);
	echo "failed\n";
} catch(Exception $e) {
	echo "passed\n";
}

