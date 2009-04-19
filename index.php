<?php
error_reporting(E_ALL | E_NOTICE | E_STRICT);
define('APPLICATION_DIRECTORY', dirname(__FILE__));

$paths = array(
	get_include_path(), 
	APPLICATION_DIRECTORY, 
	APPLICATION_DIRECTORY . '/Application/Iterators', 
	APPLICATION_DIRECTORY . '/Application/Models', 
	APPLICATION_DIRECTORY . '/Application/Helpers'
);

set_include_path(implode(PATH_SEPARATOR, $paths));

require "config/config.php";
require "config/router.php";
require "config/viewRenderer.php";
require "core/development.php";
require "core/pluginLoader.php";

Zend_Controller_Front::getInstance()->addControllerDirectory(realpath(APPLICATION_DIRECTORY . '/Application/Controllers'));

Zend_Controller_Action_HelperBroker::addPrefix('Application_Helpers');

new PluginLoader();

Zend_Registry::set("EventHelper", 		new Application_Helpers_Event);
Zend_Registry::set("FilterHelper", 		new Application_Helpers_Filter);
Zend_Registry::set("TranslationHelper", new Application_Helpers_TranslationHash);
Zend_Registry::get("TranslationHelper")->regenerateHash();

if(!isset($testing)) {
	Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
	
	configureTheme();
	
	if(isset($joobsbox_render_var)) {
		Zend_Controller_Front::getInstance()->returnResponse(true);
	}

	$response = Zend_Controller_Front::getInstance()->dispatch();

	if(isset($joobsbox_render_var)) {
		$joobsbox_render_var = $response->getBody();
	}
}