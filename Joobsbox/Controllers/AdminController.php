<?php
/**
 * Admin Controller
 * 
 * Manages the admin panel
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

/**
 * Manages the admin panel
 * @category Joobsbox
 * @package Joobsbox_Controller
 * @copyright Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license http://www.joobsbox.com/joobsbox-php-license
 */
class AdminController extends Zend_Controller_Action
{
  private $alerts = array();
  private $pluginPath = "plugins/";
  private $currentPlugin;
  private $corePlugins = array("Categories", "Postings", "Themes", "Settings");

  function sortFunction($x, $y) {
    if(in_array($x, $this->corePlugins) && in_array($y, $this->corePlugins)) {
      if(array_search($x, $this->corePlugins) < array_search($y, $this->corePlugins)) {
        return -1;
      } else {
        return 1;
      }
    } else {
      if(in_array($x, $this->corePlugins)) {
        return -1;
      } else {
        return 1;
      }
    }
  }

  public function init() {
    $session = new Zend_Session_Namespace("Admin");
    if(!isset($session->rand)) {
      $session->rand = time();
      $this->_redirect("/admin");
    } else {
      $session->rand = time();
    }

    $this->_conf = Zend_Registry::get("conf");
    configureTheme("_admin/" . $this->_conf->general->admin_theme);

    $this->plugins = array();
    $this->dashboardCandidates = array();
    foreach(new DirectoryIterator($this->pluginPath) as $plugin) {
      $name = $plugin->getFilename();
      if($plugin->isDir() && $name[0] != '.') {
	      require_once "plugins/$name/$name.php";
      	$class = new ReflectionClass(ucfirst($name));
      	if($class->hasMethod('init')) {
      	  $this->plugins[$name] = array();
      	  if(file_exists($this->pluginPath . $name . '/config.ini.php')) {
      	    $this->plugins[$name] = new Zend_Config_Ini($this->pluginPath . $name . '/config.ini.php');
      	  }
      	}
      	if($class->hasMethod('dashboard')) {
      	  $this->dashboardCandidates[$name] = 1;
      	}
      }
    }
    
    uksort($this->plugins, array($this, "sortFunction"));

    $this->view->corePlugins = $this->corePlugins;
    $this->view->pluginPath = $this->pluginPath;
    $this->view->plugins = $this->plugins;
    $this->view->locale  = Zend_Registry::get("Zend_Locale");
    
    $this->alerts = array_merge($this->alerts, $this->_helper->FlashMessenger->getMessages());
    $this->view->alerts = $this->alerts;
  }

  public function indexAction() {
    if(!$this->verifyAccess()) {
      $sess = new Zend_Session_Namespace("auth");
      $sess->loginSuccessRedirectUrl = $_SERVER['REQUEST_URI'];
      $this->_redirect("user/login");
    }

    $this->prepareDashboard();
    $this->view->currentPluginName = "dashboard";

    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $viewRenderer->setNoController(false);
    $viewRenderer->setNoRender(false);
    
  }

  private function prepareDashboard() {
    $dashboardPlugins = file("config/adminDashboard.php");
    $this->view->dashboard = array();

    foreach($dashboardPlugins as $pluginName) {
      $pluginName = trim($pluginName);
      if(isset($this->dashboardCandidates[$pluginName])) {
	      $plugin = $this->loadPlugin($pluginName, true);
      	if(method_exists($plugin, "dashboard")) {
      	  $plugin->dashboard();
      	}
      	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
      	$this->view->dashboard[$pluginName] = array(
  	      "options"	=> $this->plugins[ucfirst($pluginName)],
    	    "content" 	=> $viewRenderer->view->render('dashboard.phtml')
  	    );
      }
    }
    
    // Make some checks
    $search = new Joobsbox_Model_Search;
    if(!$search->_enabled) {
      // Oopsie
      $this->alerts[] = $this->view->translate("Search doesn't work because Joobsbox/SearchIndexes doesn't have write permissions. Please allow the server to write to that folder!");
    }
  }

  private function router() {
    if(!$this->verifyAccess()) {
      $this->_redirect("user/login");
    }

    $action = $this->getRequest()->getParam('action');
    $pluginNames = array_keys($this->plugins);
    if(($pluginIndex = array_search($action, array_map('strtolower', array_keys($this->plugins)))) !== FALSE) {
      $this->loadPlugin($pluginNames[$pluginIndex]);
    }

    $this->view->alerts = $this->alerts;
  }

  private function loadPlugin($pluginName, $return = true) {
    $pluginUrl = $_SERVER['REQUEST_URI'];
    if($pluginUrl[strlen($pluginUrl)-1] != '/') {
      $pluginUrl .= '/';
    }
    $this->view->pluginUrl = $pluginUrl;

    require_once $this->pluginPath . $pluginName . '/' . $pluginName . '.php';
    $plugin = new $pluginName;
    $this->view->currentPluginName = $pluginName;
    $plugin->view = $this->view;
    $plugin->path = $plugin->view->path = $this->view->baseUrl . '/' . $this->pluginPath . $pluginName . "/";
    $plugin->_helper = $this->_helper;
    $plugin->alerts  = &$this->alerts;
    $plugin->corePlugins = $this->corePlugins;
    $plugin->request = $this->getRequest();
    $plugin->init();

    if($return) {
      $controllerAction = $this->getRequest()->getParam('action');
      $action = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], $controllerAction)+strlen($controllerAction)+1);
      if($pos = strpos($action, '/') !== FALSE) {
	      $action = substr($action, 0, strpos($action, '/'));
      }
      $action .= "Action";
      if(method_exists($plugin, $action)) {
	      call_user_func(array($plugin, $action));
      } elseif(method_exists($plugin, "indexAction")) {
	      call_user_func(array($plugin, "indexAction"));
      }
    }

    $translate = Zend_Registry::get("Zend_Translate");
    $locale	   = Zend_Registry::get("Zend_Locale");
    if(file_exists($this->pluginPath . $pluginName . '/languages/' . $locale . '.mo'))
      $translate->addTranslation($this->pluginPath . $pluginName . '/languages/' . $locale . '.mo', $locale);
    Zend_Registry::set("Translation_Hash", $translate->getMessages());
    Zend_Registry::get("TranslationHelper")->regenerateHash();

    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
    $viewRenderer->view->addScriptPath($this->pluginPath . $pluginName . '/views');
    $viewRenderer->setNoController(true);
    $viewRenderer->setViewScriptPathNoControllerSpec(':action.:suffix');
    $tit = "title_" . Zend_Registry::get("Zend_Locale");
    $this->view->headTitle()->prepend($this->plugins[$pluginName]->main->$tit);

    if(!$return) {
      $viewRenderer->setNoRender();
    }

    return $plugin;
  }

  public function verifyAccess() {
    return Zend_Auth::getInstance()->hasIdentity();
  }

  public function __call($methodName, $params) {
    if(!method_exists($this, $methodName)) {
      $this->router();
    }
  }
}
