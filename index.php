<?php
if(!isset($joobsbox_render_var)) {
  error_reporting(E_ALL | E_NOTICE | E_STRICT);
}
define('APPLICATION_DIRECTORY', dirname(__FILE__));

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . APPLICATION_DIRECTORY);

require "core/development.php";
require "config/config.php";
require "config/router.php";
require "config/viewRenderer.php";

new Joobsbox_Plugin_Loader();

Zend_Controller_Front::getInstance()->setControllerDirectory('Joobsbox/Controllers');

Zend_Registry::set("EventHelper", 		new Joobsbox_Helpers_Event);
Zend_Registry::set("FilterHelper", 		new Joobsbox_Helpers_Filter);
Zend_Registry::set("TranslationHelper", new Joobsbox_Helpers_TranslationHash);
Zend_Registry::get("TranslationHelper")->regenerateHash();

Zend_Controller_Action_HelperBroker::addPrefix('Joobsbox_Helpers');

if(!isset($testing)) {
	$front = Zend_Controller_Front::getInstance();
	$front->setBaseUrl(BASE_URL)->setParam('disableOutputBuffering', true);

	configureTheme();
	
	if(isset($joobsbox_render_var)) {
	  setLayout('integration');
	  if(isset($joobsbox_integration_text)) {
	    $viewRenderer->view->integrationText = $joobsbox_integration_text;
	  }
		$front->returnResponse(true);
	}

	$response = $front->dispatch();

	if(isset($joobsbox_render_var)) {
		$joobsbox_render_var = $response->getBody();
	}
}
