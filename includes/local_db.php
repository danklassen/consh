<?php

class LocalDB {
	private $connection;
	private $connected = false;

	public function __construct() {
		$this->connected = false;
	}

	public function getConnection() {
		if (!$this->connected) {
			$this->connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
			mysql_select_db(DB_DATABASE, $this->connection);
			$this->connected = true;
		}
		return $this->connection;
	}

	public function execute($sql) {
		$con = $this->getConnection();
		return mysql_query($sql, $con);
	}
}