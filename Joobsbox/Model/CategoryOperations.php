<?php
/**
 * Categories Operations Model definition
 * 
 * Zend_Db_Table to categories table for manipulation
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @version  1.0
 * @link     http://docs.joobsbox.com/php
*/

/**
 * Category Operations model definition
 * 
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 * @link     http://framework.zend.com/manual/en/zend.db.table.html Zend_Db_Table_Abstract
*/
class Joobsbox_Model_CategoryOperations extends Zend_Db_Table_Abstract
{
    protected $_name = 'categories';

    /**
     * Sets up Zend_Db_Table             
     *
     * @return void
     */  
    public function __construct() 
    {
        $this->_conf = Zend_Registry::get("conf");
        $this->_name = $this->_conf->db->prefix . $this->_conf->dbtables->categories;
        parent::__construct();
    }
}
