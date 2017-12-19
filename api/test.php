<?php

require_once(__DIR__ . "/card.php");

$good_id = 1;

$bad_id = 100000;

$card_api = new CardInterface();

if ($card_api->exists($good_id)) echo "Good ID test passed\n";
if (!$card_api->exists($bad_id)) echo "Bad ID test passed\n";


$card = new CardModel();
$card->name = "TEST CARD";
$card->refnum = 909090;
$card->text = "This is test text";
$card->flavour = "This is flavour text";
$card->power = 1;
$card->toughness = 1;
$card->type_str = "Test - Test";
$card->rarity = "Common";
$card->set_id = 0;

$card_id = $card_api->create($card);
if ($card_id > 0) echo "Card create test passed\n";

$card_get = $card_api->getById($good_id);
print_r($card_get);

try {
	$card_api->getById($bad_id);
} catch(Exception $e) {
	echo "Get by Bad ID test passed\n";
}

