<?php

require_once(__DIR__ . "/db.php");

class Type {
	public $name;
	public $id;

	protected static $dbh;

	function __construct($name = "") {
		if (strlen($name) > 0) {
			$this->name = $name;
		}
		self::$dbh = DB::getDB();
	}

	public function createOrGet() {
		$query = "SELECT id FROM type WHERE name = :name";
		$result = self::$dbh->execQuery($query, array(':name'=>$this->name));
		if (count($result) > 0) {
			$this->id = $result[0]['id'];
		} else {
			$query = "INSERT INTO type (name) VALUES (:name)";
			$result = self::$dbh->execQuery($query, array(':name'=>$this->name));
			$this->id = self::$dbh->lastInsertId();
		}
		return $this->id;
	}
}
