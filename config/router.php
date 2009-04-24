<?php
$translate = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Application/languages/url_ro.mo', 'ro');
Zend_Controller_Router_Route::setDefaultTranslator($translate);
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();

$rssRoute = new Zend_Controller_Router_Route(  
	'rss/@category/:category',  
    array(
		'controller' => 'rss',  
		'action' => 'index'
	),
	null,
	$translate
);

$mainRoute = new Zend_Controller_Router_Route(
    '@publish',
    array(
        'controller' => 'publish',
        'action'     => 'index'
    )
);

$router->addRoute("publish", $mainRoute);
$router->addRoute("rss", $rssRoute);

$mainRoute->assemble(array());
$mainRoute->assemble(array());

$front->setRouter($router);