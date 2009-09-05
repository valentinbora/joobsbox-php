<?php
/**
 * Postings Operations Model definition
 * 
 * Zend_Db_Table to postings table for manipulation
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @version  1.0
 * @link     http://docs.joobsbox.com/php
 */

 /**
 * Job Operations class definition
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 * @link     http://framework.zend.com/manual/en/zend.db.table.html Zend_Db_Table_Abstract
 */

class Joobsbox_Model_JobOperations extends Zend_Db_Table_Abstract
{
    private $_name = 'jobs';
    private $_primary = "id";
    
    /**
     * Sets up Zend_Db_Table
     */ 
    function __construct() 
    {
        $this->_conf = Zend_Registry::get("conf");
        $this->_name = $this->_conf->db->prefix . $this->_conf->dbtables->postings;
        
        parent::__construct();
    }
}
