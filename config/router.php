<?php
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();

$rssRoute = new Zend_Controller_Router_Route(  
	'rss/categorie/:categorie',  
    array(
		'controller' => 'rss',  
		'action' => 'index'
	)
);
$router->addRoute("rssRouter", $rssRoute);
$front->setRouter($router);