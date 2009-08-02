<?php
$conf = Zend_Registry::get("conf");

try {	
  // Translation
  $translateUrl = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Joobsbox/Languages/' . $locale . '/LC_MESSAGES/url.mo', $locale);
} catch(Exception $e) {
  $translateUrl = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Joobsbox/Languages/en/LC_MESSAGES/url.mo', 'en');
}
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

$mainRoute2 = new Zend_Controller_Router_Route(
    'index.php/:@controller/:@action/*',
    array(
        'controller' => 'index',
        'action'     => 'index'
    )
);

$router->addRoute("main", $mainRoute);
$router->addRoute("main2", $mainRoute2);
$router->addRoute("rss", $rssRoute);

$mainRoute->assemble(array());
$mainRoute->assemble(array());

$mainRoute2->assemble(array());
$mainRoute2->assemble(array());

$front->setRouter($router);
