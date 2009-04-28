<?php
// Class autoload functionality
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader
	->registerNamespace('Joobsbox_');

// Static parameters
$conf = new Zend_Config_Ini("config/config.ini.php");
Zend_Registry::set("conf", $conf);

// Timezone
date_default_timezone_set($conf->general->timezone);

// Translation
$translate = new Zend_Translate('gettext', 'Joobsbox/Languages/main', $conf->general->locale, array('scan' => Zend_Translate::LOCALE_FILENAME));
Zend_Registry::set("Zend_Locale", $conf->general->locale);
Zend_Registry::set("Translation_Hash", $translate->getMessages());
Zend_Registry::set('Zend_Translate', $translate);

// Database parameters
if(file_exists('config/db.ini.php')) {
	$db = Zend_Db::factory('PDO_MYSQL', new Zend_Config_Ini('config/db.ini.php'));
	Zend_Db_Table_Abstract::setDefaultAdapter($db);
	Zend_Registry::set("db", $db);
	$db->query('SET NAMES "utf8"');
	
	getStaticSalt();
	
	// Authentication
	$auth = Zend_Auth::getInstance();
	$authAdapter = new Zend_Auth_Adapter_DbTable(
		Zend_Registry::get("db"),
		'users',
		'username',
		'password',
		"MD5(CONCAT('"
		. Zend_Registry::get('staticSalt')
		. "', ?, password_salt))"
	);
	Zend_Registry::set("authAdapter", $authAdapter);
}

function getStaticSalt() {
	if(!file_exists(APPLICATION_DIRECTORY . "/config/passwordSalt.php")) {
		$salt = "";
		for ($i = 0; $i < 50; $i++) {
			$salt .= chr(rand(97, 122));
		}
		file_put_contents("config/passwordSalt.php", $salt);
		Zend_Registry::set('staticSalt', $salt);
	} else {
		Zend_Registry::set('staticSalt', file_get_contents(APPLICATION_DIRECTORY . "/config/passwordSalt.php"));
	}
}

if(isset($joobsbox_base_url)) {
	$baseUrl = $joobsbox_base_url;
} else {
	$baseUrl = str_replace("\\", "/", dirname($_SERVER['PHP_SELF']));
	if($baseUrl == "/")
		$baseUrl = "";
}

define("BASE_URL", $baseUrl);
define("APPLICATION_THEME", "joobsbox");

unset($conf, $db, $translate);