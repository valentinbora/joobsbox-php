<?php
/**
 * Plugin Loader
 * 
 * Loads all plugins and keeps records of what plugin implements what event handlers and filters
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Plugin
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

/**
 * Plugin Loader Class
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Plugin
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class Joobsbox_Plugin_Loader {
	function __construct() {
		$event_handlers = array();
		$filters		= array();
		
		$dir = new DirectoryIterator(APPLICATION_DIRECTORY . "/plugins");
		$plugins = array();
		foreach($dir as $file) {
			$className = $file->getFilename();
			if($file->isDir() && $className[0] != '.' && $className[0] != '_') {
				if(file_exists("plugins/$className/$className.php")) {
					Zend_Loader::loadFile("plugins/$className/$className.php");
					$class = new ReflectionClass($className);
					if($class->getParentClass() && ($class->getParentClass()->getName() == "Joobsbox_Plugin_Base" || $class->getParentClass()->getName() == "Joobsbox_Plugin_AdminBase")) {
						foreach($class->getMethods() as $method) {
							$methodName = $method->getName();

							if(strpos($methodName, "event_") !== FALSE) {
								$eventName = substr($methodName, strpos($methodName, "_")+1);
								$event_handlers[$eventName][] = $className;
							} elseif(strpos($methodName, "filter_") !== FALSE) {
								$filterName = substr($methodName, strpos($methodName, "_")+1);
								$filters[$filterName][] = $className;
							}
						}
						$plugins[$className] = new $className;
						
						if(method_exists($plugins[$className], 'setPluginName')) {
							$plugins[$className]->setPluginName($className);
						}
						if(method_exists($plugins[$className], 'initPlugin')) {
							$plugins[$className]->initPlugin();
						}
					}
				}
			}
		}
		
		Zend_Registry::set("event_handler_plugins", $event_handlers);
		Zend_Registry::set("filter_plugins", $filters);
		Zend_Registry::set("plugins", $plugins);
	}
}
