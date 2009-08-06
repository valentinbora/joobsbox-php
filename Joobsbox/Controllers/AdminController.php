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
  private $alerts     = array();
  private $notices    = array();
  private $pluginPath = "plugins/";
  private $currentPlugin;
  private $corePlugins = array("Postings", "Categories", "Themes", "Settings", "Plugins", "Users");

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
    $this->_helper->Event("admin_panel_init");
    
    $session = new Zend_Session_Namespace("Admin");
    if(!isset($session->rand)) {
      $session->rand = time();
      $this->_redirect("/admin");
    } else {
      $session->rand = time();
    }

    $this->_conf = Zend_Registry::get("conf");
    configureTheme("_admin/" . $this->_conf->general->admin_theme, 'index', '/themes/_admin/' . $this->_conf->general->admin_theme . '/layouts');
    
    if(isset($this->_conf->admin->menu)) {
      $this->corePlugins = explode(",", $this->_conf->admin->menu);
    }

    $this->plugins = array();
    $this->menuPlugins = array();
    $this->dashboardCandidates = array();
    foreach(new DirectoryIterator($this->pluginPath) as $plugin) {
      $name = $plugin->getFilename();
      if($plugin->isDir() && $name[0] != '.' && $name[0] != '_') {
	      require_once "plugins/$name/$name.php";
      	$class = new ReflectionClass(ucfirst($name));
      	if($class->hasMethod('init')) {
      	  $this->plugins[$name] = array();
      	  if(file_exists($this->pluginPath . $name . '/config.xml')) {
      	    $this->plugins[$name] = new Zend_Config_Xml($this->pluginPath . $name . '/config.xml');
      	  }
      	  if(in_array($name, $this->corePlugins)) {
      	    $this->menuPlugins[$name] = $this->plugins[$name];
      	  }
      	}
      	if($class->hasMethod('dashboard')) {
      	  $this->dashboardCandidates[$name] = 1;
      	}
      }
    }
    
    uksort($this->menuPlugins, array($this, "sortFunction"));
    
    $otherPlugins = Zend_Registry::get("plugins");
    foreach($otherPlugins as $pluginName => $plugin) {
      if(in_array($pluginName, $this->corePlugins) || !$plugin->isAdmin) {
        unset($otherPlugins[$pluginName]);
      }
    }

    $this->view->corePlugins = $this->corePlugins;
    $this->view->otherPlugins= $otherPlugins;
    $this->view->pluginPath = $this->pluginPath;
    $this->view->plugins = $this->menuPlugins;
    $this->view->pluginsThemePath = str_replace("index.php", "", $this->view->baseUrl);
    $this->view->locale  = Zend_Registry::get("Zend_Locale");
    
    $this->alerts = array_merge($this->alerts, $this->_helper->FlashMessenger->getMessages());
  }
  
  public function sortmenuAction() {
    $conf = new Zend_Config_Xml("config/config.xml", null, array(
		  'skipExtends'        => true,
      'allowModifications' => true)
    );
    
    $order = implode(",", $_POST['item']);
		
		$conf->admin->menu = $order;
		
		// Write the configuration file
    $writer = new Zend_Config_Writer_Xml(array(
      'config'   => $conf,
      'filename' => 'config/config.xml')
    );
    $writer->write();
    exit(0);
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
    $this->view->alerts = $this->alerts;
    $this->view->notices = $this->notices;
  }

  private function prepareDashboard() {
    $dashboardPlugins = explode(",", $this->_conf->admin->dashboard);
    $this->view->dashboard = array();

    foreach($dashboardPlugins as $pluginName) {
      $pluginName = trim($pluginName);
      if(isset($this->dashboardCandidates[$pluginName])) {
	      $plugin = $this->loadPlugin($pluginName, false);
      	if(method_exists($plugin, "dashboard")) {
      	  $plugin->dashboard();
      	}
      	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
      	$this->view->dashboard[$pluginName] = array(
  	      "options"	=> $this->plugins[ucfirst($pluginName)],
    	    "content" => $viewRenderer->view->render('dashboard.phtml')
  	    );
      }
    }

    // Make some checks
    try {
      $search = new Joobsbox_Model_Search;
      if(!$search->_enabled) {
        // Oopsie
        $this->alerts[] = $this->view->translate("Search doesn't work because Joobsbox/SearchIndexes doesn't have write permissions. Please allow the server to write to that folder!");
      }
    } catch(Exception $e) {
      
    }

    // Coming from elsewhere
    $session = new Zend_Session_Namespace('Admin_Notices');
    if(isset($session->message)) {
      $this->notices = array_merge($this->notices, $session->message);
      unset($session->message);
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
    $this->view->notices = $this->notices;
  }

  private function loadPlugin($pluginName, $return = true) {
    $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
    $view->js->addPath('/plugins/' . $pluginName . '/js');
    $view->js->addPath('/plugins/' . $pluginName);
    $view->css->addPath('/plugins/' . $pluginName . '/css');
    $view->css->addPath('/plugins/' . $pluginName);
    
    $pluginUrl = $_SERVER['REQUEST_URI'];
    if($pluginUrl[strlen($pluginUrl)-1] != '/') {
      $pluginUrl .= '/';
    }
    $view->pluginUrl = $pluginUrl;
    $view->js->write('var pluginUrl="' . $pluginUrl . '";');

    require_once $this->pluginPath . $pluginName . '/' . $pluginName . '.php';
    $plugin = new $pluginName;
    
    $plugin->setPluginName($pluginName);
    
    $view->currentPluginName = $pluginName;
    $plugin->view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;;
    $plugin->path = $plugin->view->path = $this->view->baseUrl . '/' . $this->pluginPath . $pluginName . "/";
    $plugin->view->themePath =  str_replace("index.php", "", $plugin->path); 
    $plugin->dirPath = $this->pluginPath . $pluginName . '/';
    $plugin->_helper = $this->_helper;
    $plugin->alerts  = &$this->alerts;
    $plugin->notices  = &$this->notices;
    $plugin->corePlugins = $this->corePlugins;
    $plugin->request = $this->getRequest();
    $plugin->ajax = false;
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $viewRenderer->view->addScriptPath($this->pluginPath . $pluginName . '/views');
    $viewRenderer->setNoController(true);
    $viewRenderer->setViewScriptPathNoControllerSpec(':action.:suffix');
    
    if(method_exists($plugin, "init")) {
      $plugin->init();
    }

    if($return) {
      Zend_Registry::get("TranslationHelper")->regenerateHash();
      
      $controllerAction = $this->getRequest()->getParam('action');
      $action = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], $controllerAction)+strlen($controllerAction)+1);
      if($pos = strpos($action, '/') !== FALSE) {
	      $action = substr($action, 0, strpos($action, '/'));
      }
      $fullAction = $action . "Action";

      if(method_exists($plugin, $fullAction)) {
	      call_user_func(array($plugin, $fullAction));
        $this->render($action);
	    } elseif(method_exists($plugin, "indexAction")) {
	      call_user_func(array($plugin, "indexAction"));
      }
    }

    $translate = Zend_Registry::get("Zend_Translate");
    $locale	   = Zend_Registry::get("Zend_Locale");
    
    if(file_exists($this->pluginPath . $pluginName . '/languages/' . $locale . '.mo') && substr($locale, 0, 2) != 'en')
      $translate->addTranslation($this->pluginPath . $pluginName . '/languages/' . $locale . '.mo', $locale);
    
    Zend_Registry::set("Translation_Hash", $translate->getMessages());
    Zend_Registry::get("TranslationHelper")->regenerateHash();

    $tit = "title_" . Zend_Registry::get("Zend_Locale");
    $this->view->headTitle()->prepend($this->plugins[$pluginName]->main->$tit);

    if(!$return) {
      $viewRenderer->setNoRender();
    }

    if($plugin->ajax) {
      echo $viewRenderer->view->render('dashboard.phtml'); die();
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
