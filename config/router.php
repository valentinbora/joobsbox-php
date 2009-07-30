<?php
$conf = Zend_Registry::get("conf");
$translateUrl = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Joobsbox/Languages', null, array('disableNotices' => true, 'scan' => Zend_Translate::LOCALE_DIRECTORY, 'ignore' => '$'));
$translateUrl->setLocale(Zend_Registry::get("Zend_Locale"));
Zend_Registry::set("Joobsbox_Translate_URL", $translateUrl);
Zend_Controller_Router_Route::setDefaultTranslator($translateUrl);
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();

$rssRoute = new Zend_Controller_Router_Route(  
	'rss/@category/:category',  
    array(
		'controller' => 'rss',  
		'action' => 'index'
	)
);

$mainRoute = new Zend_Controller_Router_Route(
    ':@controller/:@action/*',
    array(
        'controller' => 'index',
        'action'     => 'index'
    )
);

$router->addRoute("main", $mainRoute);
$router->addRoute("rss", $rssRoute);

$mainRoute->assemble(array());
$mainRoute->assemble(array());

$front->setRouter($router);
