<?php

require_once(__DIR__ . "/db.php");

class Set {
	public $name;
	public $code;
	public $id;

	protected static $dbh;

	function __construct($name = "", $code = "") {
		if (strlen($name) > 0) {
			$this->name = $name;
		}
		if (strlen($code) > 0) {
			$this->code = $code;
		}
		self::$dbh = DB::getDB();
	}

	public function createOrGet() {
		$query = "SELECT id FROM sets WHERE code = :code";
		$result = self::$dbh->execQuery($query, array(':code'=>$this->code));
		if (count($result) > 0) {
			$this->id = $result[0]['id'];
		} else {
			$query = "INSERT INTO sets (name, code) VALUE (:name, :code)";
			$result = self::$dbh->execQuery($query, array(':name'=>$this->name, ':code'=>$this->code));
			$this->id = self::$dbh->lastInsertId();
		}
		return $this->id;
	}

	public static function getAll() {
		if (!isset(self::$dbh)) {
			self::$dbh = DB::getDB();
		}

		$query = "SELECT id, name, code FROM sets ORDER BY name ASC";
		$result = self::$dbh->execQuery($query);

		return $result;
	}
}	