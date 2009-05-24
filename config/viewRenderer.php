<?php
//echo $baseUrl;die();
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
$viewRenderer->initView(); 
$viewRenderer->view->baseUrl=$baseUrl;
$viewRenderer->view->publicUrl=$baseUrl . "/public";

function configureTheme($theme = APPLICATION_THEME, $layoutName = 'index') {
	global $baseUrl, $viewRenderer;
	$viewRenderer->view->themeUrl		= $baseUrl . "/Joobsbox/Themes/" . $theme;
	$viewRenderer->view->themeImages	= $baseUrl . "/Joobsbox/Themes/" . $theme . "/images";
	$viewRenderer->view->theme			= $theme;
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