<?php
/**
 * EventsFilters Model definition
 * 
 * Provies plugin access
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Plugin
 */

 /**
 * @package Joobsbox_Plugin
 * @category Joobsbox
 */
class Joobsbox_Plugin_EventsFilters {
	protected function fireEvent() {
		$eventHelper = Zend_Registry::get("EventHelper");
		$args = func_get_args();
		call_user_func_array(array($eventHelper, "fireEvent"), $args);
	}
	protected function filter() {
		$filterHelper = Zend_Registry::get("FilterHelper");
		$args = func_get_args();
		return call_user_func_array(array($filterHelper, "filter"), $args);
	}
}