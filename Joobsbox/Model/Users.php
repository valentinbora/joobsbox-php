<?php

/**
* Users Model definition
* 
* @category Joobsbox
* @package  Joobsbox_Model
* @author   Valentin Bora <contact@valentinbora.com>
* @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
* @version  1.0
* @link     http://docs.joobsbox.com/php
*/

 /**
 * Users model class
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 */

class Joobsbox_Model_Users
{
    protected $db, $users_table, $conf;

    /**
    * Constructor function, sets up configuration and database for access
    *
    */
    public function __construct()
    {
        $this->db = Zend_Registry::get("db");
        $this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
        $this->conf = Zend_Registry::get("conf");
        $this->users_table     = $this->conf->db->prefix . $this->conf->dbtables->users;
    }

    /**
    * Retrieves user data for a specified username
    * 
    * @param string $username Username
    *
    * @return array
    */
    public function getData($username)
    {
        $sql  = "SELECT id, username, realname, email, password, password_salt FROM " . $this->users_table . " WHERE username = ?";
        return $this->db->fetchRow($sql, $username);
    }

    /**
    * Update user data for a specified ID
    * 
    * @param array $data Associative array of user data to update. Must include username.
    *
    * @return array
    */
    public function updateData($data)
    {
        // Treat data well
        unset($data['id']); // No messing up with the IDs
        unset($data['submit']);
        unset($data['old_password']);
        // Password salting

        if (isset($data['password'])) {
            $data['password'] = trim($data['password']);

            if ($data['password'] != "") {
                $data['password_salt'] = sha1($data['password']);
                $data['password'] = md5(Zend_Registry::get('staticSalt') . $data['password'] . sha1($data['password']));
            }
        }

        $this->db->update($this->users_table, $data, $this->db->quoteInto('username = ?', Zend_Auth::getInstance()->getIdentity()));
    }

    /**
    * Tries to authenticate user
    * 
    * @param string $username Username
    * @param string $password Password
    *
    * @return boolean
    */
    public function authenticate($username, $password)
    {
        $auth = Zend_Auth::getInstance();
        $authAdapter = Zend_Registry::get("authAdapter");
        $password = md5(Zend_Registry::get("staticSalt") . $password . sha1($password));
        $authAdapter->setIdentity($username)->setCredential($password);
        return $auth->authenticate($authAdapter);
    }
}
