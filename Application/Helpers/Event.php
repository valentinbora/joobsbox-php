<?php
class Application_Helpers_Event extends Zend_Controller_Action_Helper_Abstract
{
    public function fireEvent() {
		$args = func_get_args();
		$eventName = $args[0];
		array_shift($args); // delete eventName from arguments array
		$plugins = Zend_Registry::get("plugins");
		$eventHandlers = Zend_Registry::get("event_handler_plugins");
		if(isset($eventHandlers[$eventName])) {
			foreach($eventHandlers[$eventName] as $pluginClassName) {
				if(method_exists($plugins[$pluginClassName], "event_$eventName")) {
					call_user_func_array(array($plugins[$pluginClassName], "event_$eventName"), $args);
				}
			}
		}
	}
		
	public function direct($eventName) {
		$args = func_get_args();
		call_user_func_array(array($this, "fireEvent"), $args);
	}
}

class Zend_View_Helper_Event extends Zend_View_Helper_Abstract {
	public function Event() {
		$args = func_get_args();
		call_user_func_array(array(new Application_Helpers_Event, "fireEvent"), $args);
	}
}