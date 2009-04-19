<?php
error_reporting(E_ALL | E_NOTICE);
define('APPLICATION_DIRECTORY', dirname(__FILE__));

set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_DIRECTORY);
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(APPLICATION_DIRECTORY . '/config'));
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(APPLICATION_DIRECTORY . '/Application/Iterators'));
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(APPLICATION_DIRECTORY . '/Application/Models'));
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(APPLICATION_DIRECTORY . '/Application/Helpers'));

require("config/config.php");
require("config/viewRenderer.php");
require("core/development.php");
require("core/pluginLoader.php");

Zend_Controller_Action_HelperBroker::addPrefix('Application_Helpers');

include("config/router.php");

new PluginLoader();

Zend_Registry::set("EventHelper", 		new Application_Helpers_Event);
Zend_Registry::set("FilterHelper", 		new Application_Helpers_Filter);
Zend_Registry::set("TranslationHelper", new Application_Helpers_TranslationHash);
Zend_Registry::get("TranslationHelper")->regenerateHash();

Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl)->setControllerDirectory(APPLICATION_DIRECTORY . '/Application/Controllers');
if(isset($joobsbox_render_var)) {
	Zend_Controller_Front::getInstance()->returnResponse(true);
}

$response = Zend_Controller_Front::getInstance()->dispatch();

if(isset($joobsbox_render_var)) {
	$joobsbox_render_var = $response->getBody();
}