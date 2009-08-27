<?php

error_reporting(E_ALL | E_NOTICE);
$testing = 1;
define('APPLICATION_DIRECTORY', realpath("../"));

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . APPLICATION_DIRECTORY);

require_once APPLICATION_DIRECTORY . "/config/config.php";
require_once APPLICATION_DIRECTORY . "/config/viewRenderer.php";
require_once APPLICATION_DIRECTORY . "/Joobsbox/Application/Test.php";

$front = Zend_Controller_Front::getInstance();
$front->resetInstance();
$front->setControllerDirectory(APPLICATION_DIRECTORY . '/Joobsbox/Controllers');

//configureTheme();

/********/
function dt($stuff) {
  echo "----------\n";
	if(is_array($stuff)) {
		print_r($stuff);
	} else {
		if(is_object($stuff)) {
			var_dump($stuff);
		} else {
			echo $stuff;
		}
	}
	echo "\n----------\n";
	die();
}