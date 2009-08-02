<?php
/**
 * Users Model definition
 * 
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

class Joobsbox_Model_Users {
	protected $_db, $_users_table, $_conf;
	
	public function __construct() {
	  $this->_db = Zend_Registry::get("db");
		$this->_db->setFetchMode(Zend_Db::FETCH_ASSOC);
		$this->_conf = Zend_Registry::get("conf");
		$this->_users_table 		= $this->_conf->db->prefix . $this->_conf->dbtables->users;
	}
	
	/**
	 * Retrieves user data for a specified username
	 * 
	 * @param string $username
	 * @returns array
	 */
	public function getData($username) {
	  $sql 	= "SELECT ID, username, realname, email, password, password_salt FROM " . $this->_users_table . " WHERE username = ?";
		return $this->_db->fetchRow($sql, $username);
	}
	
	/**
	 * Update user data for a specified ID
	 * 
	 * @param string $username
	 * @returns array
	 */
	public function updateData($data) {
	  // Treat data well
	  unset($data['ID']); // No messing up with the IDs
	  unset($data['submit']);
	  unset($data['old_password']);
	  // Password salting

	  if(isset($data['password'])) {
	    $data['password'] = trim($data['password']);

	    if($data['password'] != "") {
	      $data['password_salt'] = sha1($data['password']);
	      $data['password'] = md5(Zend_Registry::get('staticSalt') . $data['password'] . sha1($data['password']));
	    }
	  }
	  
	  $this->_db->update($this->_users_table, $data, $this->_db->quoteInto('username = ?', Zend_Auth::getInstance()->getIdentity()));
	}
}
