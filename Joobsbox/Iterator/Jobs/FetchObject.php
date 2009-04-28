<?php
/**
 * Jobs Fetch Object definition
 * 
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterator_Object
 */
 
/*
 * @package Joobsbox_Iterator_Object
 */
class Joobsbox_Iterator_Jobs_FetchObject extends Zend_Db_Select {

	public function __construct($selectObject, $db) {
		$x = parent::__construct($db);
		$this->_parts = $selectObject->_parts;
	}
	
	public function fetch() {
		$stmt	= $this->query()->fetchAll();
		return new Joobsbox_Iterator_Jobs($stmt);
	}
}