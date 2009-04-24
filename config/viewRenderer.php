<?php
//echo $baseUrl;die();
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
$viewRenderer->initView(); 
$viewRenderer->view->baseUrl=$baseUrl;
$viewRenderer->view->publicUrl=$baseUrl . "/public";

function configureTheme($theme = APPLICATION_THEME) {
	global $baseUrl, $viewRenderer;
	$viewRenderer->view->themeUrl		= $baseUrl . "/public/" . $theme;
	$viewRenderer->view->themeImages	= $baseUrl . "/public/" . $theme . "/images";
	$viewRenderer->view->theme			= $theme;
	$viewRenderer->view->setScriptPath(APPLICATION_DIRECTORY . '/Application/themes/' . $theme . '/views/scripts');

	$viewRenderer->view->setEncoding("UTF-8");
	$viewRenderer->view->setHelperPath(APPLICATION_DIRECTORY . "/Application/Helpers/");
	
	$conf = Zend_Registry::get("conf");
	$conf['general']['THEME'] = $theme;
	Zend_Registry::set("conf", $conf);
	$viewRenderer->view->conf = $conf;
	
	if($conf['general']['STANDALONE']) {
		if($layout = Zend_Layout::getMvcInstance()) {
			$layout->setLayoutPath(APPLICATION_DIRECTORY . '/Application/themes/' . $conf['general']['THEME'] . '/layouts');
		} else {
			Zend_Layout::startMvc(array(
				'layoutPath' => APPLICATION_DIRECTORY . '/Application/themes/' . $conf['general']['THEME'] . '/layouts',
				'layout' => 'index'
			));
		}
	}
}