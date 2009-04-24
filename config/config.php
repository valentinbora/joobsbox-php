<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_NOTICE | E_STRICT);
date_default_timezone_set("Europe/Bucharest");

// Class autoload functionality
include("Zend/Loader.php");
Zend_Loader::registerAutoload();

// Static parameters
$conf = parse_ini_file("config.ini.php", 1);
Zend_Registry::set("conf", $conf);

// Translation
$translate = new Zend_Translate('gettext', APPLICATION_DIRECTORY . '/Application/languages/main', null, array('scan' => Zend_Translate::LOCALE_FILENAME));
Zend_Registry::set("Zend_Locale", $conf['general']['LOCALE']);

Zend_Registry::set("Translation_Hash", $translate->getMessages());
Zend_Registry::set('Zend_Translate', $translate);

// Database parameters
require "db.ini.php";
$db = Zend_Db::factory('PDO_MYSQL', $params);
Zend_Db_Table_Abstract::setDefaultAdapter($db);
Zend_Registry::set("db", $db);

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