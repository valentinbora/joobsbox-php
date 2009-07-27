<?php
/**
 * Jobs Fetch Object definition
 * 
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterator_Object
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/*
 * @package Joobsbox_Iterator_Object
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
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
