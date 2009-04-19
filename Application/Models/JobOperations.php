<?php
/**
 * Postings Operations Model definition
 * 
 * Zend_Db_Table to postings table for manipulation
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Models
 */

 /**
 * @category Joobsbox
 * @package Joobsbox_Models
 */

class Model_JobOperations extends Zend_Db_Table_Abstract {
	protected $_name = 'jobs';
	protected $_primary = "ID";
	
	function __construct() {
		$this->_conf = Zend_Registry::get("conf");
		$this->_name = $this->_conf['db']['prefix'] . $this->_conf['dbtables']['postings'];
		
		parent::__construct();
	}
}