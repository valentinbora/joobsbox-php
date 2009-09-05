<?php
/**
 * Application plugin foundation
 * 
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Plugin
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

 /**
 * Base Plugin class
 * Provides application plugins with storage and helpers
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Plugin
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class Joobsbox_Plugin_Base {
	public $_helper;
	public $isAdmin = false;
	/**
	 * @var string the plugin name set by Joobsbox_Plugin_Loader
	 */
	protected $_pluginName;
	
	/**
   * Retrieves the specified plugin option(s) from the database
   *
	 * @param string $name the option name to retrieve
	 * @returns array indexed array of database rows as associative arrays
  */
	final function getConfiguration($name) {
	  if(!Zend_Registry::isRegistered("db")) return "";
	  
		$db = Zend_Registry::get("db");
		$pf = Zend_Registry::get("conf");
		$pf = $pf->db->prefix;
		$data = $db->fetchAll("SELECT * FROM {$pf}plugin_data WHERE plugin_name=? AND option_name=?", array($this->_pluginName, $name));
		if(count($data)) {
		  return $data[0]['option_value'];
		} else {
		  return "";
		}
	}
	
	final public function getName() {
	  return $this->_pluginName;
	}
	
	/**
     * Deletes the specified plugin option from the database
     *
	 * @param int $id the option name to delete
	 * @returns int number of affected rows
     */
	final function deleteConfigurationById($id) {
		$db = Zend_Registry::get("db");
		$pf = Zend_Registry::get("conf");
		$pf = $pf->db->prefix;
		$id = (int)$id;
		return $db->delete($pf . "plugin_data", array("plugin_name='" . $this->_pluginName . "'", "id='" . $id . "'"));
	}
	
	/**
     * Deletes the specified plugin option(s) from the database
     *
	 * @param string $name the option name to delete
	 * @returns int number of affected rows
     */
	final function deleteConfigurationByName($name) {
		$db = Zend_Registry::get("db");
		$pf = Zend_Registry::get("conf");
		$pf = $pf->db->prefix;

		return $db->delete($pf . "plugin_data", array("plugin_name='" . $this->_pluginName . "'", "option_name=" . $db->quote($name) . ""));
	}
	
	/**
     * Adds the specified plugin option to the database
     *
	 * @param string $name the option name to store to
	 * @param string $value the option value to store
	 * @returns int id of the inserted row
     */
	final function addConfiguration($name, $value) {
		$db = Zend_Registry::get("db");
		$pf = Zend_Registry::get("conf");
		$pf = $pf->db->prefix;
		
		// Make sure each key per plugin is unique
		$this->deleteConfigurationByName($name);
		
		return $db->query("INSERT INTO {$pf}plugin_data (plugin_name, option_name, option_value) VALUES (?, ?, ?)", array(
			$this->_pluginName,
			$name,
			$value
		));
	}
	
	/**
    * pluginLoader means of securely configuring the plugin name so that plugins can't say they are someone else
    */
	final function setPluginName($pluginName) {
		$protection = debug_backtrace();

		if($protection[1]['class'] == 'Joobsbox_Plugin_Loader' || $protection[1]['class'] == "AdminController") {
			$this->_pluginName = $pluginName;
		} else {
		  throw new Exception("Who's trying to mangle the name?! Arrgh");
		}
	}
	
	/**
     * pluginLoader means of securely configuring the plugin
     */
	final function initPlugin() {
		$protection = debug_backtrace();
		if($protection[1]['class'] == 'Joobsbox_Plugin_Loader') {
			global $baseUrl;
			$this->_helper = new Plugin_Helper();
			$this->baseUrl = $baseUrl;
		}
	}
	
	/**
   * gives access to application models
   */
	protected function getModel($modelName) {
		$modelName = "Joobsbox_Model_$modelName";
		return new $modelName;
	}
	
	/**
   * gets current response object for manipulation
   * @return Zend_Controller_Request
   */
	protected function getRequest() {
    $front = Zend_Controller_Front::getInstance();
    return $front->getRequest();
	}
	
	/**
   * gets current response object for manipulation
   * @return Zend_Controller_Response
   */
  public function getResponse() {
    $front = Zend_Controller_Front::getInstance();
    return $front->getResponse();
  }
  
  public function __get($what) {
    switch($what) {
      case "view":
        return Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        break;
    }
  }
}

/**
 * Plugin Helper class
 */
class Plugin_Helper {
	function __call($methodName, $params) {
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		return call_user_func_array(array($viewRenderer->view, $methodName), $params);
	}
}
