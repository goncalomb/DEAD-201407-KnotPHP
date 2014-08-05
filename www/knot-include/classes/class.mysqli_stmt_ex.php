<?php

class mysqli_stmt_ex extends mysqli_stmt {

	private $_queryCounter;

	public function __construct(mysqli $link, $query, &$counter) {
		parent::__construct($link, $query);
		$this->_queryCounter = &$counter;
	}

	public function execute() {
		$this->_queryCounter++;
		return parent::execute();
	}

}

?>
