<?php
/**
 * Categories Operations Model definition
 * 
 * Zend_Db_Table to categories table for manipulation
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
class Joobsbox_Model_CategoryOperations extends Zend_Db_Table_Abstract {
	protected $_name = 'categories';
	protected $_primary = "ID";	
	protected $_sequence = true;
	
	public function __construct() {
		$this->_conf = Zend_Registry::get("conf");
		$this->_name = $this->_conf->db->prefix . $this->_conf->dbtables->categories;
		
		parent::__construct();
	}
}
