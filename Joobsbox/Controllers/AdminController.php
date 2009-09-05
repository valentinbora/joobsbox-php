<?php
/**
 * Admin Controller
 * 
 * Manages the admin panel
 *
 * @category Joobsbox
 * @package  Joobsbox_Controller
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @version  1.0
 * @link     http://docs.joobsbox.com/php
 */

/**
 * Manages the admin panel
 * 
 * @category Joobsbox
 * @package  Joobsbox_Controller
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 */
class AdminController extends Zend_Controller_Action
{
    private $_alerts     = array();
    private $_notices    = array();
    private $_corePlugins;
    private $_corePluginPath;
    private $_pluginPath;
    private $_pluginUrl;
    private $_corePluginUrl;

    /**
    * Function to sort plugins in the admin menu by usort
    *
    * @param string $x name of first plugin
    * @param string $y name of second plugin
    *
    * @return int -1 or 1 to reflect order for usort
    */
    private function _sortFunction($x, $y) 
    {
        if (in_array($x, $this->_corePlugins) && in_array($y, $this->_corePlugins)) {
            if (array_search($x, $this->_corePlugins) < array_search($y, $this->_corePlugins)) {
                return -1;
            } else {
                return 1;
            }
        } else {
            if (in_array($x, $this->_corePlugins)) {
                return -1;
            } else {
                return 1;
            }
        }
    }

    /**
    * Initialization method. Sets up the admin environment
    *
    * @todo this method is too complex. Split up into multiple smaller methods
    * @return void
    */
    public function init() 
    {
        $url = $_SERVER['REQUEST_URI'];
        if (substr($url, -1) != '/') {
            header("Location: " . $url . '/');
            exit();
        }

        $this->_corePluginPath = APPLICATION_DIRECTORY . "/Joobsbox/Plugin";
        $this->_corePluginUrl = $this->view->noScriptBaseUrl . "/Joobsbox/Plugin";
        $this->_pluginPath = APPLICATION_DIRECTORY . "/plugins";
        $this->_pluginUrl = $this->view->noScriptBaseUrl . "/plugins";

        $this->_helper->Event("admin_panel_init");
        $this->_conf = Zend_Registry::get("conf");

        if (file_exists(APPLICATION_DIRECTORY . "/Joobsbox/Version")) {
            $this->view->version = file_get_contents(APPLICATION_DIRECTORY . "/Joobsbox/Version");
        } else {
            $this->view->version = "0.9.20090701";
            @file_put_contents(APPLICATION_DIRECTORY . "/Joobsbox/Version", "0.9.20090701");
        }

        configureTheme("_admin/" . $this->_conf->general->admin_theme, 'index', '/themes/_admin/' . $this->_conf->general->admin_theme . '/layouts');

        // Get plugin order from configuration file
        if (isset($this->_conf->admin->menu)) {
            $this->_corePlugins = explode(",", $this->_conf->admin->menu);
        }

        // Initialize plugins
        $this->plugins = array();
        $this->menuPlugins = array();
        $this->dashboardCandidates = array();

        // Search for them
        foreach (new DirectoryIterator($this->_corePluginPath) as $plugin) {
            $name = $plugin->getFilename();

            if ($plugin->isDir() && $name[0] != '.' && $name[0] != '_') {
                include_once $this->_corePluginPath . "/$name/$name.php";
                // Analyze prerequisites
                $class = new ReflectionClass(ucfirst($name));
    
                if (file_exists($this->_corePluginPath . '/' . $name . '/config.xml')) {
                    $this->plugins[$name] = new Zend_Config_Xml($this->_corePluginPath . '/' . $name . '/config.xml', null, array("allowModifications" => true));
                    $this->plugins[$name]->paths = array();
                    $this->plugins[$name]->paths->dirPath = $this->_corePluginPath;
                    $this->plugins[$name]->paths->urlPath = $this->_corePluginUrl;
                    $this->menuPlugins[$name] = $this->plugins[$name];
                    $this->_pluginPaths[$name] = $this->_corePluginPath . '/' . $name;
                }
  
                if ($class->hasMethod('dashboard')) {
                    $this->dashboardCandidates[$name] = 1;
                }
            }
        }

        // Search for the other plugins - dashboard purposes
        foreach (new DirectoryIterator($this->_pluginPath) as $plugin) {
            $name = $plugin->getFilename();

            if ($plugin->isDir() && $name[0] != '.' && $name[0] != '_') {
                include_once $this->_pluginPath . "/$name/$name.php";
                // Analyze prerequisites
                $class = new ReflectionClass(ucfirst($name));

                if (file_exists($this->_pluginPath . '/' . $name . '/config.xml')) {
                    $this->plugins[$name] = new Zend_Config_Xml($this->_pluginPath . '/' . $name . '/config.xml', null, array("allowModifications" => true));
                    $this->plugins[$name]->paths = array();
                    $this->plugins[$name]->paths->dirPath = $this->_pluginPath;
                    $this->plugins[$name]->paths->urlPath = $this->_pluginUrl;
                    $this->_pluginPaths[$name] = $this->_pluginPath . '/' . $name;
                }

                if ($class->hasMethod('dashboard')) {
                    $this->dashboardCandidates[$name] = 1;
                }
            }
        }

        if (isset($this->_corePlugins) && count(array_diff($this->_corePlugins, array_keys($this->plugins)))) {
            $this->_corePlugins = array_keys($this->plugins);
            // Write it to config so that we don't miss it furtherwise
            $tmp = new Zend_Config_Xml("config/config.xml", null, array('allowModifications' => true));
            $tmp->admin->menu = implode(",", $this->_corePlugins);

            $writer = new Zend_Config_Writer_Xml(array('config'   => $tmp, 'filename' => 'config/config.xml'));
            $writer->write();
            unset($tmp, $writer);
        }

        uksort($this->menuPlugins, array($this, "_sortFunction"));

        $this->view->corePlugins = $this->_corePlugins;
        $this->view->corePluginPath = $this->_corePluginPath;
        $this->view->pluginPath = $this->_pluginPath;
        $this->view->plugins = $this->menuPlugins;
        $this->view->pluginsThemePath = str_replace("index.php", "", $this->view->baseUrl);
        $this->view->locale  = Zend_Registry::get("Zend_Locale");

        $session = new Zend_Session_Namespace("AdminPanel");
        $this->_alerts = array_merge($this->_alerts, $this->_helper->FlashMessenger->getMessages());
        $this->_alerts = array_merge($this->_alerts, array_unique($session->alerts));
        unset($session->alerts);

        /* Load stuff */
        $this->view->css->load("reset.css", "global.css", "admin.css");

        $this->view->headScript()->prependScript($this->view->translateHash . 'var baseUrl = "' . $this->view->baseUrl . '";' . ' var href = "' . $_SERVER['REQUEST_URI'] . '";', 'text/javascript', array('charset' => 'UTF-8'));
        $this->view->js->load('functions.js');
        $this->view->asset->load("jquery", "jquery-pngfix");
        $this->view->js->load(array('global.js', 100));
    }
  
    /**
     * Used to save sorted menu from the client side
     *
     * @return void
     */
    public function sortmenuAction() 
    {
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

    /**
     *  Handles admin homepage
     *
     * @return void
     */
    public function indexAction() 
    {
        if (!$this->verifyAccess()) {
            $sess = new Zend_Session_Namespace("auth");
            $sess->loginSuccessRedirectUrl = $_SERVER['REQUEST_URI'];
            $this->_redirect("user/login");
        }

        $this->_prepareDashboard();
        $this->view->currentPluginName = "dashboard";

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setNoController(false);
        $viewRenderer->setNoRender(false);
        $this->view->alerts = $this->_alerts;
        $this->view->notices = $this->_notices;
    }

    /**
     *  Used to prepare and render dashboard plugins
     *
     * @return void
     */
    private function _prepareDashboard() 
    {
        $dashboardPlugins = explode(",", $this->_conf->admin->dashboard);
        $this->view->dashboard = array();

        foreach ($dashboardPlugins as $pluginName) {
            $pluginName = trim($pluginName);
            if (isset($this->dashboardCandidates[$pluginName])) {
                $plugin = $this->_loadPlugin($pluginName, false);
                if (method_exists($plugin, "dashboard")) {
                    $plugin->dashboard();
                }
                $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
                $this->view->dashboard[$pluginName] = array(
                  "options" => $this->plugins[ucfirst($pluginName)],
                  "content" => $viewRenderer->view->render('dashboard.phtml')
                );
            }
        }

        // Make some checks
        try {
            $search = new Joobsbox_Model_Search;
            if (!$search->enabled) {
                // Oopsie
                $this->_alerts[] = $this->view->translate("Search doesn't work because Joobsbox/SearchIndexes doesn't have write permissions. Please allow the server to write to that folder!");
            }
        } catch(Exception $e) {
        }

        // Coming from elsewhere
        $session = new Zend_Session_Namespace('AdminPanel');
        if (isset($session->notices)) {
            $this->_notices = array_merge($this->_notices, $session->notices);
            unset($session->notices);
        }
    }

    /**
     * Used to route plugin calls to their specific pages
     *
     * @return void
     */
    private function _router() 
    {
        if (!$this->verifyAccess()) {
            $this->_redirect("user/login");
        }

        $action = $this->getRequest()->getParam('action');
        $pluginNames = array_keys($this->plugins);
        if (($pluginIndex = array_search($action, array_map('strtolower', array_keys($this->plugins)))) !== false) {
            $this->_loadPlugin($pluginNames[$pluginIndex]);
        }

        $this->view->alerts = $this->_alerts;
        $this->view->notices = $this->_notices;
    }

    /**
     *  Used to load a plugin with a specific name and inject its various dependencies
     *
     * @param string  $pluginName plugin name to load
     * @param boolean $return     if $return is true, it won't render the plugin
     *
     * @return mixed the requested plugin object
     */
    private function _loadPlugin($pluginName, $return = true)
    {
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
        if ($pluginUrl[strlen($pluginUrl)-1] != '/') {
            $pluginUrl .= '/';
        }
        $view->pluginUrl = $pluginUrl;
        $view->js->write('var pluginUrl="' . $pluginUrl . '";');

        include_once $this->_pluginPaths[$pluginName] . '/' . $pluginName . '.php';
        $plugin = new $pluginName;

        $plugin->setPluginName($pluginName);

        $view->currentPluginName = $pluginName;
        $plugin->view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;;
        $plugin->path = $plugin->view->path = $this->plugins[$pluginName]->paths->urlPath . '/' . $pluginName;
        $plugin->dirPath = $this->_pluginPath . $pluginName . '/';
        $plugin->view->dirPath = $this->_pluginPaths[$pluginName] . '/';
        $plugin->_helper = $this->_helper;
        $plugin->alerts  = &$this->_alerts;
        $plugin->notices  = &$this->_notices;
        $plugin->corePlugins = $this->_corePlugins;
        $plugin->request = $this->getRequest();
        $plugin->ajax = false;

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->view->addScriptPath($this->_pluginPath . '/' . $pluginName . '/views');
        $viewRenderer->view->addScriptPath($this->_corePluginPath . '/' . $pluginName . '/views');
        $viewRenderer->setNoController(true);
        $viewRenderer->setViewScriptPathNoControllerSpec(':action.:suffix');

        if (method_exists($plugin, "init")) {
            $plugin->init();
        }

        if ($return) {
            Zend_Registry::get("TranslationHelper")->regenerateHash();
            
            $controllerAction = $this->getRequest()->getParam('action');
            $action = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], $controllerAction)+strlen($controllerAction)+1);
            if ($pos = strpos($action, '/') !== false) {
                $action = substr($action, 0, strpos($action, '/'));
            }
            $fullAction = $action . "Action";
            
            if (method_exists($plugin, $fullAction)) {
                call_user_func(array($plugin, $fullAction));
                $this->render($action);
            } elseif (method_exists($plugin, "indexAction")) {
                call_user_func(array($plugin, "indexAction"));
            }
        }

        $translate = Zend_Registry::get("Zend_Translate");
        $locale    = Zend_Registry::get("Zend_Locale");

        if (file_exists($this->_pluginPath . $pluginName . '/languages/' . $locale . '.mo') && substr($locale, 0, 2) != 'en') {
            $translate->addTranslation($this->_pluginPath . $pluginName . '/languages/' . $locale . '.mo', $locale);
        }

        Zend_Registry::set("Translation_Hash", $translate->getMessages());
        Zend_Registry::get("TranslationHelper")->regenerateHash();

        $tit = "title_" . Zend_Registry::get("Zend_Locale");
        $this->view->headTitle()->prepend($this->plugins[$pluginName]->main->$tit);

        if (!$return) {
            $viewRenderer->setNoRender();
        }

        if ($plugin->ajax) {
            echo $viewRenderer->view->render('dashboard.phtml'); die();
        }
        
        return $plugin;
    }

    /**
     * Check that the current user is logged in
     *
     * @return void
     */
    public function verifyAccess() 
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    /**
     * Used to route plugin calls
     *
     * @param string $methodName method name to be called. This is a magic function
     * @param array  $params     method parameters
     *
     * @return void
     */
    public function __call($methodName, $params) 
    {
        if (!method_exists($this, $methodName)) {
            $this->_router();
        }
    }
}
