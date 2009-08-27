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
  private $currentPlugin;
  private $corePlugins;
  private $corePluginPath;
  private $pluginPath;
  private $pluginUrl;
  private $corePluginUrl;
  private $pluginPaths = array();

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
    $url = $_SERVER['REQUEST_URI'];
    if(substr($url, -1) != '/') {
      header("Location: " . $url . '/');
      exit();
    }
    
    $this->corePluginPath = APPLICATION_DIRECTORY . "/Joobsbox/Plugin";
    $this->corePluginUrl = $this->view->noScriptBaseUrl . "/Joobsbox/Plugin";
    $this->pluginPath = APPLICATION_DIRECTORY . "/plugins";
    $this->pluginUrl = $this->view->noScriptBaseUrl . "/plugins";
    
    $this->_helper->Event("admin_panel_init");
    $this->_conf = Zend_Registry::get("conf");
    
    if(file_exists(APPLICATION_DIRECTORY . "/Joobsbox/Version")) {
      $this->view->version = file_get_contents(APPLICATION_DIRECTORY . "/Joobsbox/Version");
    } else{
      $this->view->version = "0.9.20090701";
      @file_put_contents(APPLICATION_DIRECTORY . "/Joobsbox/Version", "0.9.20090701");
    }
    
    configureTheme("_admin/" . $this->_conf->general->admin_theme, 'index', '/themes/_admin/' . $this->_conf->general->admin_theme . '/layouts');
    
    // Get plugin order from configuration file
    if(isset($this->_conf->admin->menu)) {
      $this->corePlugins = explode(",", $this->_conf->admin->menu);
    }

    // Initialize plugins
    $this->plugins = array();
    $this->menuPlugins = array();
    $this->dashboardCandidates = array();
    
    // Search for them
    foreach(new DirectoryIterator($this->corePluginPath) as $plugin) {
      $name = $plugin->getFilename();
      
      if($plugin->isDir() && $name[0] != '.' && $name[0] != '_') {
	      require_once $this->corePluginPath . "/$name/$name.php";
	      // Analyze prerequisites
      	$class = new ReflectionClass(ucfirst($name));
      	
    	  if(file_exists($this->corePluginPath . '/' . $name . '/config.xml')) {
    	    $this->plugins[$name] = new Zend_Config_Xml($this->corePluginPath . '/' . $name . '/config.xml', null, array("allowModifications" => true));
    	    $this->plugins[$name]->paths = array();
    	    $this->plugins[$name]->paths->dirPath = $this->corePluginPath;
    	    $this->plugins[$name]->paths->urlPath = $this->corePluginUrl;
  	      $this->menuPlugins[$name] = $this->plugins[$name];
  	      $this->pluginPaths[$name] = $this->corePluginPath . '/' . $name;
    	  }
    	  
      	if($class->hasMethod('dashboard')) {
      	  $this->dashboardCandidates[$name] = 1;
      	}
      }
    }
    
    // Search for the other plugins - dashboard purposes
    foreach(new DirectoryIterator($this->pluginPath) as $plugin) {
      $name = $plugin->getFilename();
      
      if($plugin->isDir() && $name[0] != '.' && $name[0] != '_') {
	      require_once $this->pluginPath . "/$name/$name.php";
	      // Analyze prerequisites
      	$class = new ReflectionClass(ucfirst($name));
      	
    	  if(file_exists($this->pluginPath . '/' . $name . '/config.xml')) {
    	    $this->plugins[$name] = new Zend_Config_Xml($this->pluginPath . '/' . $name . '/config.xml', null, array("allowModifications" => true));
    	    $this->plugins[$name]->paths = array();
    	    $this->plugins[$name]->paths->dirPath = $this->pluginPath;
    	    $this->plugins[$name]->paths->urlPath = $this->pluginUrl;
    	    $this->pluginPaths[$name] = $this->pluginPath . '/' . $name;
    	  }
    	  
      	if($class->hasMethod('dashboard')) {
      	  $this->dashboardCandidates[$name] = 1;
      	}
      }
    }
    
    if(isset($this->corePlugins) && count(array_diff($this->corePlugins, array_keys($this->plugins)))) {
      $this->corePlugins = array_keys($this->plugins);
      // Write it to config so that we don't miss it furtherwise
      $tmp = new Zend_Config_Xml("config/config.xml", null, array('allowModifications' => true));
      $tmp->admin->menu = implode(",", $this->corePlugins);
      
      $writer = new Zend_Config_Writer_Xml(array('config'   => $tmp, 'filename' => 'config/config.xml'));
      $writer->write();
      unset($tmp, $writer);
    }
    
    uksort($this->menuPlugins, array($this, "sortFunction"));
    
    $this->view->corePlugins = $this->corePlugins;
    $this->view->corePluginPath = $this->corePluginPath;
    $this->view->pluginPath = $this->pluginPath;
    $this->view->plugins = $this->menuPlugins;
    $this->view->pluginsThemePath = str_replace("index.php", "", $this->view->baseUrl);
    $this->view->locale  = Zend_Registry::get("Zend_Locale");
    
    $session = new Zend_Session_Namespace("AdminPanel");
    $this->alerts = array_merge($this->alerts, $this->_helper->FlashMessenger->getMessages());
    $this->alerts = array_merge($this->alerts, array_unique($session->alerts));
    unset($session->alerts);

    /* Load stuff */
    $this->view->css->load("reset.css", "global.css", "admin.css");
    
    $this->view->headScript()
    	->prependScript($this->view->translateHash . 'var baseUrl = "' . $this->view->baseUrl . '";' . ' var href = "' . $_SERVER['REQUEST_URI'] . '";', 'text/javascript', array('charset' => 'UTF-8'));
    $this->view->js->load('functions.js');
    $this->view->asset->load("jquery", "jquery-pngfix");
    $this->view->js->load(array('global.js', 100));
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
    $session = new Zend_Session_Namespace('AdminPanel');
    if(isset($session->notices)) {
      $this->notices = array_merge($this->notices, $session->notices);
      unset($session->notices);
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
    
    $view->js->addPath('/Joobsbox/Plugin/' . $pluginName . '/js');
    $view->js->addPath('/Joobsbox/Plugin/' . $pluginName);
    $view->css->addPath('/Joobsbox/Plugin/' . $pluginName . '/css');
    $view->css->addPath('/Joobsbox/Plugin/' . $pluginName);
    
    $pluginUrl = $_SERVER['REQUEST_URI'];
    if($pluginUrl[strlen($pluginUrl)-1] != '/') {
      $pluginUrl .= '/';
    }
    $view->pluginUrl = $pluginUrl;
    $view->js->write('var pluginUrl="' . $pluginUrl . '";');

    require_once $this->pluginPaths[$pluginName] . '/' . $pluginName . '.php';
    $plugin = new $pluginName;
    
    $plugin->setPluginName($pluginName);
    
    $view->currentPluginName = $pluginName;
    $plugin->view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;;
    $plugin->path = $plugin->view->path = $this->plugins[$pluginName]->paths->urlPath . '/' . $pluginName;
    $plugin->dirPath = $this->pluginPath . $pluginName . '/';
    $plugin->view->dirPath = $this->pluginPaths[$pluginName] . '/';
    $plugin->_helper = $this->_helper;
    $plugin->alerts  = &$this->alerts;
    $plugin->notices  = &$this->notices;
    $plugin->corePlugins = $this->corePlugins;
    $plugin->request = $this->getRequest();
    $plugin->ajax = false;
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $viewRenderer->view->addScriptPath($this->pluginPath . '/' . $pluginName . '/views');
    $viewRenderer->view->addScriptPath($this->corePluginPath . '/' . $pluginName . '/views');
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
