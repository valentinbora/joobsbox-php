<?php
/**
 * Categor Object definition
 * 
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterators_Objects
 */
 
/*
 * @package Joobsbox_Iterators_Objects
 */
class Category extends ArrayObject
{
	public function __set($name, $val) {
        $this[$name] = $val;
    }

    public function __get($name) {
        return $this[$name];
    }
	
	public function getChildren() {
		if(empty($this['Children'])) {
			return array();
		} else {
			return call_user_method_array('getCategories', $this['CollectionParent'], $this['Children']);
		}
	}
	
	public function getProperty($property) {
		return $this[$property];
	}
}