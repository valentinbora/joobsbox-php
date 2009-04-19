<?php
require_once "Application/Models/Plugin.php";

class PluginLoader {
	function __construct() {
		$event_handlers = array();
		$filters		= array();
		
		$dir = new DirectoryIterator("plugins");
		$plugins = array();
		foreach($dir as $file) {
			$className = $file->getFilename();
			if($file->isDir() && $className[0] != '.' && $className[0] != '_') {
				if(file_exists("plugins/$className/$className.php")) {
					include "plugins/$className/$className.php";
					$class = new ReflectionClass($className);
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
					if(method_exists($plugins[$className], 'initPlugin')) {
						$plugins[$className]->initPlugin();
					}
					if(method_exists($plugins[$className], 'setPluginName')) {
						$plugins[$className]->setPluginName($className);
					}
				}
			}
		}
		Zend_Registry::set("event_handler_plugins", $event_handlers);
		Zend_Registry::set("filter_plugins", $filters);
		Zend_Registry::set("plugins", $plugins);
	}
}