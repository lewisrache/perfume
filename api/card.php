<?php

// TODO - namespace? includes?

require_once(__DIR__ . "/db.php");

class CardModel {

	public $name;
	public $id;
	public $text;
	public $manacost;
	public $power;
	public $toughness;
	public $num_owned;
	public $type_str;
	public $flavour;
	public $types;
	public $set_id;
	public $rarity;
	public $associated_cards;
	public $refnum;
}

class CardInterface {

	protected $dbh;

	function __construct() {
		$this->dbh = DB::getDB();
	}

	public function exists($refnum) {
		$query = "SELECT id FROM card WHERE refnum = :refnum";
		$result = $this->dbh->execQuery($query, array(':refnum'=>$refnum));
		return (count($result) > 0);
	}

	public function create($card) {
		if ($this->exists($card->refnum)) {
			throw new Exception("Card ({$card->name}) already exists!");
		}
		$query = "INSERT INTO card 
			(name, refnum, text, flavour, manacost, power, toughness, type, num_own, rarity, set_id)
			VALUES
			(:name, :refnum, :text, :flavour, :manacost, :power, :toughness, :type, :num_own, :rarity, :set_id)";
		$data = array(
			':name' => $card->name,
			':refnum' => $card->refnum,
			':text' => $card->text,
			':flavour' => $card->flavour,
			':manacost' => $card->manacost,
			':power' => $card->power,
			':toughness' => $card->toughness,
			':type' => $card->type_str,
			':num_own' => 0,
			':rarity' => $card->rarity,
			':set_id' => $card->set_id
		);
		$this->dbh->execQuery($query, $data);
		$card_id = $this->dbh->lastInsertId();
		return $card_id;
	}

	public function update($card) {

	}

	public function setNumOwned($card_id, $num_owned) {
		$query = "UPDATE card SET num_own = :num_own WHERE id = :id";
		$this->dbh->execQuery($query, array(':num_own'=>$num_owned, ':id'=>$card_id));
	}

	public function getById($card_id) {
		$query = "SELECT * FROM card WHERE id = :id";
		$result = $this->dbh->execQuery($query, array(':id'=>$card_id));
		if (count($result) === 0) {
			throw new Exception("Card $card_id not found");
		}
		$card_data = $result[0];
		$card = new CardModel();
		$card->id = $card_data['id'];
		$card->name = $card_data['name'];
		$card->refnum = $card_data['refnum'];
		$card->manacost = $card_data['manacost'];
		$card->flavour = $card_data['flavour'];
		$card->text = $card_data['text'];
		$card->power = $card_data['power'];
		$card->toughness = $card_data['toughness'];
		$card->type_str = $card_data['type'];
		$card->num_own = $card_data['num_own'];
		$card->rarity = $card_data['rarity'];
		$card->set_id = $card_data['set_id'];

		$types_query = "SELECT type.id, type.name FROM type, card_types
			WHERE type.id = card_types.type_id AND card_types.card_id = :id";
		$types = $this->dbh->execQuery($types_query, array(':id' => $card_id));
		$card->types = $types;

		return $card;
	}

	public function getByRefnum($refnum) {

	}

	public function getAll() {

	}

	public function search() {

	}
}
