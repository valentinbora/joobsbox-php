<?php
/**
 * Category Object definition
 * 
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterator_Object
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/*
 * @package Joobsbox_Iterator_Object
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class Joobsbox_Iterator_Categories_CategoryObject extends ArrayObject
{
	public function __set($name, $val) {
        $this[$name] = $val;
    }

    public function __get($name) {
        return $this[$name];
    }
	
	public function getChildren() {
		if(empty($this['children'])) {
			return array();
		} else {
			return call_user_method_array('getCategories', $this['CollectionParent'], $this['children']);
		}
	}
	
	public function getProperty($property) {
		return $this[$property];
	}
}
