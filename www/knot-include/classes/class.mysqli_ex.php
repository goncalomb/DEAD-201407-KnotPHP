<?php

class mysqli_ex extends mysqli {

	private $_queryCounter = 0;

	public function __construct($config) {
		parent::__construct('p:' . $config['host'], $config['username'], $config['password']);
		$this->set_charset('utf8');
		// Create/Select database.
		if (!@$this->select_db($config['name'])) {
			$escaped_name = $this->real_escape_string($config['name']);
			$this->query("CREATE DATABASE `$escaped_name` CHARSET=utf8");
			$this->select_db($config['name']);
		}
	}

	public function query($query) {
		$this->_queryCounter++;
		return parent::query($query);
	}

	public function multi_query($query) {
		$this->_queryCounter++;
		return parent::multi_query($query);
	}

	public function prepare($query) {
		return new mysqli_stmt_ex($this, $query, $this->_queryCounter);
	}

	public function stmt_init() {
		return new mysqli_stmt_ex($this, null, $this->_queryCounter);
	}

	public function query_count() {
		return $this->_queryCounter;
	}

}

?>
