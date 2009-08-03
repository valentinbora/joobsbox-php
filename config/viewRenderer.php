<?php
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
$viewRenderer->setNoRender();
$viewRenderer->initView(); 
$viewRenderer->view->baseUrl = BASE_URL;
$viewRenderer->view->noScriptBaseUrl = str_replace("index.php", "", BASE_URL);
$viewRenderer->view->publicUrl = str_replace("index.php", "", BASE_URL) . "/public";

function configureTheme($theme = APPLICATION_THEME, $layoutName = 'index', $layoutPath = '/themes/core/layouts') {
	global $baseUrl;
	
	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 

  if($layoutName == 'integration') {
	  $viewRenderer->view->themeUrl		  = str_replace("index.php", "", BASE_URL) . '/public/' . $theme;
	  $viewRenderer->view->themeImages	= $viewRenderer->view->themeUrl . "/images";
	} else {
	  $viewRenderer->view->themeUrl		  = str_replace("index.php", "", BASE_URL) . "/themes/" . $theme;
	  $viewRenderer->view->themesUrl	  = str_replace("index.php", "", BASE_URL) . "/themes/";
	  $viewRenderer->view->themeImages	= $viewRenderer->view->themeUrl . "/images";
	}
	$viewRenderer->view->theme			  = $theme;
	$viewRenderer->view->asset        = new Joobsbox_Helpers_AssetHelper;
	$viewRenderer->view->css          = new Joobsbox_Helpers_CssHelper;
	$viewRenderer->view->js           = new Joobsbox_Helpers_JsHelper;
	
	$viewRenderer->view->setScriptPath(APPLICATION_DIRECTORY . '/themes/' . $theme . '/views/scripts');
	$viewRenderer->view->addScriptPath(APPLICATION_DIRECTORY . '/themes/core/views/scripts');

	$viewRenderer->view->setEncoding("UTF-8");
	$viewRenderer->view->setHelperPath(APPLICATION_DIRECTORY . "/Joobsbox/Helpers/");
	
	$conf = Zend_Registry::get("conf");
	Zend_Registry::set("theme", $theme);
	$viewRenderer->view->conf = $conf;
	
	if($conf->general->standalone) {
		if($layout = Zend_Layout::getMvcInstance()) {
			$layout->setLayoutPath(APPLICATION_DIRECTORY . $layoutPath);
			$layout->setLayout($layoutName);
		} else {
			Zend_Layout::startMvc(array(
				'layoutPath' => APPLICATION_DIRECTORY . $layoutPath,
				'layout' => $layoutName
			));
		}
	}
}

function setLayout($layout) {
  configureTheme(APPLICATION_THEME, $layout);
}
