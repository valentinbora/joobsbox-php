<?php
set_magic_quotes_runtime(false);
ini_set('magic_quotes_gpc', false);
ini_set("memory_limit", "64M");

// Class autoload functionality
require_once APPLICATION_DIRECTORY . '/Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance()->registerNamespace('Joobsbox_');
	
// Timezone default
date_default_timezone_set("GMT");

// Static parameters
$conf = new Zend_Config_Xml(APPLICATION_DIRECTORY . "/config/config.xml");
Zend_Registry::set("conf", $conf);

// Set up caching
if($conf->general->cache) {
  $frontendOptions = array(
  	'default_options' => array(
  		'cache_with_session_variables' => true,
  		'cache_with_cookie_variables' => true,
  		'make_id_with_session_variables' => false,
  		'make_id_with_cookie_variables' => false
  	),
  	'regexps' => array(
  		'/$' => array("cache" => true)
  	)
  );

  $backendOptions = array('cache_dir' => 'Joobsbox/Cache/');

  $cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);

  $urlChunks = explode("/", $_SERVER['REQUEST_URI']);
  if(!in_array("admin", $urlChunks)) {
  	$cache->start();
  }	

  Zend_Registry::set("cache", $cache);
}

// Timezone
date_default_timezone_set($conf->general->timezone);

$locale = $conf->general->locale;
if(!file_exists(APPLICATION_DIRECTORY . "/Joobsbox/Languages/$locale")) {
  $locale = "en";
}

try {	
  // Translation
  $translate = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Joobsbox/Languages/' . $locale . '/LC_MESSAGES/main.mo', $locale, array('disableNotices' => true));
  Zend_Registry::set("Zend_Locale", new Zend_Locale($locale));
} catch(Exception $e) {
  Zend_Registry::set("Zend_Locale", new Zend_Locale("en"));
  $translate = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Joobsbox/Languages/en/LC_MESSAGES/main.mo', 'en');
}

Zend_Registry::set("Translation_Hash", $translate->getMessages());
Zend_Registry::set('Zend_Translate', $translate);

// Database parameters
if(file_exists(APPLICATION_DIRECTORY . '/config/db.xml')) {
  try {
    $dbconf = new Zend_Config_Xml(APPLICATION_DIRECTORY . '/config/db.xml', 'zend_db');
	  $db = Zend_Db::factory($dbconf->dbadapter, $dbconf);
  	Zend_Db_Table_Abstract::setDefaultAdapter($db);
	  Zend_Registry::set("db", $db);
	  if(strlen($dbconf->start)) {
	    $db->query($dbconf->start);
	  }
	} catch (Exception $e) {
	  @rename(APPLICATION_DIRECTORY . "/config/db.xml", APPLICATION_DIRECTORY . "/config/db.xml.bak");
	  throw $e;
    exit();
	}

	getStaticSalt($conf);
	
	try {
  	// Authentication
  	$auth = Zend_Auth::getInstance();
  	$authAdapter = new Zend_Auth_Adapter_DbTable(
  		Zend_Registry::get("db"),
  		$conf->db->prefix . 'users',
  		'username',
  		'password'
  	);
  	Zend_Registry::set("authAdapter", $authAdapter);
  } catch(Exception $e) {

  }
}

function getStaticSalt($conf) {
	if(!isset($conf->db->passwordSalt)) {
		$salt = "";
		for ($i = 0; $i < 50; $i++) {
			$salt .= chr(rand(97, 122));
		}
		$tempConf = new Zend_Config_Xml("config/config.xml", null, array(
		  'skipExtends'        => true,
      'allowModifications' => true)
    );
    
		$tempConf->db->passwordSalt = $salt;
		
    $writer = new Zend_Config_Writer_Xml(array('config'   => $conf, 'filename' => 'config/config.xml'));
    $writer->write();
		Zend_Registry::set('staticSalt', $salt);
	} else {
		Zend_Registry::set('staticSalt', $conf->db->passwordSalt);
	}
}

if(!isset($testing)) {
  if(isset($joobsbox_base_url)) {
  	$baseUrl = $joobsbox_base_url;
  	if($baseUrl[strlen($baseUrl)-1] == '/') {
  	  $baseUrl = substr($baseUrl, 0, strlen($baseUrl)-1);
  	}
  } else {
    // Generate base url to build from
  	$baseUrl = str_replace("\\", "/", $_SERVER['SCRIPT_NAME']);
  	if(strpos($_SERVER['REQUEST_URI'], "index.php") !== FALSE) {

    } else {
  	  $baseUrl = substr($baseUrl, 0, strpos($baseUrl, "index.php"));
      $baseUrl = explode("/", $baseUrl);

      foreach($baseUrl as $key => $value) {
        if($value == "") {
          unset($baseUrl[$key]);
        }
      }
      $baseUrl = array_values($baseUrl);
      $baseUrl = "/" . implode("/", $baseUrl);
    }
  
    if($baseUrl == "/") $baseUrl = "";
  }
  define("BASE_URL", $baseUrl);
}
define("APPLICATION_THEME", $conf->general->theme);

unset($conf, $db, $translate);