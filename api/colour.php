<?php

require_once(__DIR__ . "/db.php");

class Colour {

	public $code;
	public $name;
	public $id;

	protected static $dbh;

	function __construct() {
		self::$dbh = DB::getDB();
	}

	public function get($code) {
		$query = "SELECT code, name, id FROM colour WHERE code = :code";
		$result = self::$dbh->execQuery($query, array(':code'=>$code));
		if (count($result) > 0) {
			$this->code = $result[0]['code'];
			$this->name = $result[0]['name'];
			$this->id = $result[0]['id'];
		} else {
			throw new Exception("Colour $code not found");
		}
		return $this->id;
	}
}
