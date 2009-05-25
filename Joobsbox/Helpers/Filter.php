<?php
/**
 * Filter call Helper
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Filter call Helper
 *
 * Example usage:
 * <code>
 * echo $this->Filter('event_name', $dataToFilter);
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 * 
 */
class Joobsbox_Helpers_Filter extends Zend_Controller_Action_Helper_Abstract
{
    public function filter() {
		$args = func_get_args();
		$filterName = $args[0];
		array_shift($args); // delete filterName from arguments array
		$plugins = Zend_Registry::get("plugins");
		$filters = Zend_Registry::get("filter_plugins");
		if(isset($filters[$filterName])) {
			foreach($filters[$filterName] as $pluginClassName) {
				if(method_exists($plugins[$pluginClassName], "filter_$filterName")) {
					$args = call_user_func_array(array($plugins[$pluginClassName], "filter_$filterName"), $args);
				}
			}
		}
		if(count($args) == 1)
			return $args[0];
		return $args;
	}
	
	public function direct($eventName) {
		$args = func_get_args();
		return call_user_func_array(array($this, "filter"), $args);
	}
}

class Zend_View_Helper_Filter extends Zend_View_Helper_Abstract {
	public function Filter() {
		$args = func_get_args();
		return call_user_func_array(array(new Joobsbox_Helpers_Filter, "direct"), $args);
	}
}