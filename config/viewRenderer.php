<?php
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
$viewRenderer->setNoRender();
$viewRenderer->initView(); 
$viewRenderer->view->baseUrl = BASE_URL;
$viewRenderer->view->publicUrl = BASE_URL . "/public";

function configureTheme($theme = APPLICATION_THEME, $layoutName = 'index') {
	global $baseUrl;
	
	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 

  if($layoutName == 'integration') {
	  $viewRenderer->view->themeUrl		  = BASE_URL . '/public/' . $theme;
	  $viewRenderer->view->themeImages	= BASE_URL . '/public/' . $theme . "/images";
	} else {
	  $viewRenderer->view->themeUrl		  = BASE_URL . "/Joobsbox/Themes/" . $theme;
	  $viewRenderer->view->themeImages	= BASE_URL . "/Joobsbox/Themes/" . $theme . "/images";
	}
	$viewRenderer->view->theme			  = $theme;
	$viewRenderer->view->asset        = new Joobsbox_Helpers_AssetHelper;
	$viewRenderer->view->setScriptPath(APPLICATION_DIRECTORY . '/Joobsbox/Themes/' . $theme . '/views/scripts');

	$viewRenderer->view->setEncoding("UTF-8");
	$viewRenderer->view->setHelperPath(APPLICATION_DIRECTORY . "/Joobsbox/Helpers/");
	
	$conf = Zend_Registry::get("conf");
	Zend_Registry::set("theme", $theme);
	$viewRenderer->view->conf = $conf;
	
	if($conf->general->standalone) {
		if($layout = Zend_Layout::getMvcInstance()) {
			$layout->setLayoutPath(APPLICATION_DIRECTORY . '/Joobsbox/Themes/' . $theme . '/layouts');
			$layout->setLayout($layoutName);
		} else {
			Zend_Layout::startMvc(array(
				'layoutPath' => APPLICATION_DIRECTORY . '/Joobsbox/Themes/' . $theme . '/layouts',
				'layout' => $layoutName
			));
		}
	}
}

function setLayout($layout) {
  configureTheme(APPLICATION_THEME, $layout);
}
