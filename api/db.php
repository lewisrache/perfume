<?php

class DB {

	protected static $db;

	public static function getDB() {
		if (!isset(self::$db)) {
			$dir = 'sqlite:'.__DIR__.'/mtg.db';
			self::$db = new EnhancedPDO($dir) or die("cannot open the database");
			self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		return self::$db;
	}
}

class EnhancedPDO extends PDO {

	public function execQuery($query, $data = []) {

		$stmt = $this->prepare($query);
		if (!$stmt->execute($data)) {
			throw new Exception("failure to execute query");
		}

		return $stmt->fetchAll();

	}

}
