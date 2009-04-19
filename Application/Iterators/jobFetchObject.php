<?php
class jobFetchObject extends Zend_Db_Select {

	public function __construct($selectObject, $db) {
		$x = parent::__construct($db);
		$this->_parts = $selectObject->_parts;
	}
	
	public function fetch() {
		$stmt	= $this->query()->fetchAll();
		return jobsIterator($stmt);
	}
}