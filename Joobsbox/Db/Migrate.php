<?php
/**
 * Database migrator
 * 
 * Upgrades or downgrades the database definition as needed
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Db
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Upgrades or downgrades the database definition as needed
 * @package Joobsbox_Db
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

Class Joobsbox_Db_Migrate {
  private $currentDbVersion, $db;
  
  function __construct() {
    // get the database version
    $this->db = Zend_Registry::get("db");
  }
  
  public function getCurrentVersion() {
    $row = $this->db->fetchRow("SELECT value FROM info WHERE option = 'db_version'");
    dd($row);
  }
}