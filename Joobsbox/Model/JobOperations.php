<?php
/**
 * Postings Operations Model definition
 * 
 * Zend_Db_Table to postings table for manipulation
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Model
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

 /**
 * @category Joobsbox
 * @package Joobsbox_Model
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

class Joobsbox_Model_JobOperations extends Zend_Db_Table_Abstract {
	protected $_name = 'jobs';
	protected $_primary = "ID";
	
	function __construct() {
		$this->_conf = Zend_Registry::get("conf");
		$this->_name = $this->_conf->db->prefix . $this->_conf->dbtables->postings;
		
		parent::__construct();
	}
}
