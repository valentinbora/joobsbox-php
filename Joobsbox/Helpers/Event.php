<?php
/**
 * Event trigger Helper
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 
 */
 
/**
 * Event trigger Helper
 *
 * Example usage:
 * <code>
 * $this->Event('event_name');
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license)
 * @license	   New BSD License
 * 
 */
class Joobsbox_Helpers_Event extends Zend_Controller_Action_Helper_Abstract
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
		call_user_func_array(array(new Joobsbox_Helpers_Event, "fireEvent"), $args);
	}
}