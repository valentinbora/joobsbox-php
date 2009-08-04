<?php
error_reporting(E_ALL | E_NOTICE | E_STRICT);
$testing = 1;
define('APPLICATION_DIRECTORY', realpath("../"));

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . APPLICATION_DIRECTORY);

require "core/development.php";
require "config/config.php";
require "config/viewRenderer.php";

Zend_Controller_Front::getInstance()->setControllerDirectory(APPLICATION_DIRECTORY . 'Joobsbox/Controllers');

Zend_Registry::set("PluginLoader",      new Joobsbox_Plugin_Loader);
Zend_Registry::set("EventHelper", 		  new Joobsbox_Helpers_Event);
Zend_Registry::set("FilterHelper", 		  new Joobsbox_Helpers_Filter);
Zend_Registry::set("TranslationHelper", new Joobsbox_Helpers_TranslationHash);
Zend_Registry::get("TranslationHelper")->regenerateHash();

Zend_Controller_Action_HelperBroker::addPrefix("Joobsbox_Helpers");
Zend_Controller_Action_HelperBroker::addPrefix("Zend_Controller_Action_Helper");

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new Joobsbox_Plugin_Controller);

configureTheme();
Zend_Registry::get("EventHelper")->fireEvent("joobsbox_init");

function dt($stuff) {
  echo "----------\n";
	if(is_array($stuff)) {
		print_r($stuff);
	} else {
		if(is_object($stuff)) {
			var_dump($stuff);
		} else {
			echo $stuff;
		}
	}
	echo "\n----------\n";
	die();
}