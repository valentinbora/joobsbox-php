#!/usr/bin/env php
<?php
/**
 * Doctrine CLI script
 */
error_reporting(E_ALL);
ini_set("display_errors", "on"); 
 
define('APPLICATION_DIRECTORY', realpath(dirname(__FILE__) . '/../../'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_DIRECTORY),
    realpath(APPLICATION_DIRECTORY . "/Joobsbox/Db/Doctrine"),
    get_include_path(),
)));

require_once APPLICATION_DIRECTORY . '/Joobsbox/Db/Doctrine.php';        
require_once APPLICATION_DIRECTORY . '/Zend/Loader/Autoloader.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->pushAutoloader(array('Doctrine', 'autoload'));

$doctrineConfig = new Zend_Config_Xml(APPLICATION_DIRECTORY . "/config/db.xml", "doctrine");
$doctrineConfig = $doctrineConfig->toArray();

$conn = Doctrine_Manager::connection($doctrineConfig['connection_string']);

/*Doctrine::generateModelsFromDb('models', array('doctrine'), array('generateTableClasses' => true));
Doctrine::generateYamlFromModels('models');*/

$cli = new Doctrine_Cli($doctrineConfig);
$cli->run($_SERVER['argv']);