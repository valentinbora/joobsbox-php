<?php

define('APPLICATION_DIRECTORY', dirname(__FILE__));
define('LIBRARY_DIRECTORY', dirname(__FILE__) . "/library");
define('CONFIG_LOCATION', APPLICATION_DIRECTORY . "/config/config.xml");
define('DB_CONFIG_LOCATION', APPLICATION_DIRECTORY . "/config/db.xml");

ini_set("include_path", APPLICATION_DIRECTORY . PATH_SEPARATOR . LIBRARY_DIRECTORY . PATH_SEPARATOR . ini_get("include_path"));

require "Joobsbox/Application/Development.php";
require "config/config.php";
require "config/router.php";
require "config/viewRenderer.php";

Zend_Controller_Front::getInstance()->setControllerDirectory(APPLICATION_DIRECTORY . '/Joobsbox/Controllers');

Zend_Registry::set("PluginLoader",      new Joobsbox_Plugin_Loader);
Zend_Registry::set("EventHelper", 		  new Joobsbox_Helpers_Event);
Zend_Registry::set("FilterHelper", 		  new Joobsbox_Helpers_Filter);
Zend_Registry::set("TranslationHelper", new Joobsbox_Helpers_TranslationHash);
Zend_Registry::get("TranslationHelper")->regenerateHash();

require APPLICATION_DIRECTORY . "/Joobsbox/Application/ErrorHandler.php";

Zend_Controller_Action_HelperBroker::addPath(APPLICATION_DIRECTORY . '/Joobsbox/Helpers', 'Joobsbox_Helpers');

$front = Zend_Controller_Front::getInstance();
$front->setParam('disableOutputBuffering', true)->registerPlugin(new Joobsbox_Plugin_Controller);

configureTheme();

if(isset($joobsbox_render_var)) {
  setLayout('integration');
  if(isset($joobsbox_integration_text)) {
    $viewRenderer->view->integrationText = $joobsbox_integration_text;
  }
	$front->returnResponse(true);
}
Zend_Registry::get("EventHelper")->fireEvent("joobsbox_init");

$response = $front->dispatch();

if(isset($joobsbox_render_var)) {
	$joobsbox_render_var = $response->getBody();
}
