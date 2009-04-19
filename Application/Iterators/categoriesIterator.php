<?php
/**
 * Categories Iterator definition
 * 
 * This file defines the {@link categoriesIteratorObject()} which contains a Category collection and is able to manipulate it
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterators_Objects
 */
 
/**
 * Includes the categoryObject definition, an ArrayObject that simply extends a Category with added functionality
 * @see categoryObject()
 */
require_once dirname(__FILE__) . "/categoryObject.php";


function categoriesIterator($contents) {
	return new categoriesIteratorObject($contents);
}

/**
 * Categories Iterator object
 * 
 *
 * @package Joobsbox_Iterators_Objects
 */
class categoriesIteratorObject extends ArrayIterator {
	/**
     * Contains the Category collection, indexed by Category IDs
     *
     * @access public
     * @var array
    */
	public $_contents;
	/**
     * Contains the Category collection as key => value pairs
     *
     * @access public
     * @var array
    */
	protected $_contentsNames;
	protected $_key;
	protected $_jobsModel;
	
	/**
	 * categoriesIterator constructor - stores the received array of categories
	 *
	 * 
	 * @param array $contents array of categories
	 */
	
	function __construct($contents) {
		$this->_contentsNames = array();
		
		foreach($contents as $category) {
			$category['Children'] = array();
			$category['CollectionParent'] = $this;
			
			$this->_contents[$category['ID']] = new Category($category);
			$this->_contentsNames[$category['ID']] = $category['Name'];
		}
		
		ksort($this->_contents);
		
		foreach($this->_contents as $key => $category) {
			if($category['Parent']) {
				$this->_contents[$category['Parent']]['Children'][] = $category['ID'];
			}
		}
	}
	
	/***************/
	// GETTERS
	/***************/
	
	/**
	 * Retrieves a Category based on either name or id
	 * 
	 * @param string|integer $info - category id or name
	 * @returns Category
	 * @example getCategory(5)
	 * @example getCategory('Production')
	 */
	function getCategory() {
		$args = func_get_args();
		if(!count($args)) {
			throw new Exception("You must provide a category name or id");
		}
		foreach($args as $arg) {
			if(isset($this->_contents[$arg])) {
				return $this->_contents[$arg];
			}
			if(($id = array_search($arg, $this->_contentsNames)) !== FALSE) {
				return $this->_contents[$id];
			}
		}
		return FALSE;
	}
	
	/**
	 * Retrieves a Category array based on either name or id of categories
	 * 
	 * @param integer [...] - a comma separated value of category ids or names
	 * @returns array
	 */
	function getCategories() {
		$result = array();
		$args = func_get_args();
		if(!count($args)) {
			throw new Exception("You must provide at least a category name or id");
		}
		foreach($args as $index => $arg) {
			if(isset($this->_contents[$arg])) {
				$result[] = $this->_contents[$arg];
			} else 
			if(($id = array_search($arg, $this->_contentsNames)) !== FALSE) {
				$result[] = $this->_contents[$id];
			}
		}
		return $result;
	}
	
	/***************/
	// FILTERS
	/***************/
	function filterRootNodes() {
		$filtered = $this->_contents;

		foreach($filtered as $key => $value) {
			if($value['Parent'] != 0) {
				unset($filtered[$key]);
			}
		}
		
		return categoriesIterator($filtered);
	}
	
	function filterEmpty() {
		$filtered = $this->_contents;

		foreach($filtered as $key => $value) {
			if($value['nrPostings'] == 0) {
				unset($filtered[$key]);
			}
		}
		
		return categoriesIterator($filtered);
	}
	
	function getIndexNamePairs() {
		return $this->_contentsNames;
	}
	
	/***************/
	// ORDERS
	/***************/
	function orderByOrderIndex() {
		uasort($this->_contents, "compareOrderIndex");
		return $this;
	}

	function toArray() {
		return $this->_contents;
	}
	
	/***************/
	// MUST-HAVES
	/***************/
	function current() {
		return current($this->_contents);
	}
	
	function key() {
		return $this->_key;
	}
	
	function next() {
		return next($this->_contents);
	}
	
	function rewind() {
		reset($this->_contents);
	}
	
	function seek($position) {
		return $this->_contents[$position];
	}
	
	function valid() {
		return ($this->current() !== FALSE);
	}
	
	function count() {
		return count($this->_contents);
	}
}

function compareOrderIndex($a, $b) {
	$a = $a['OrderIndex'];
	$b = $b['OrderIndex'];
	
	if($a == $b) {
		return 0;
	}
	return ($a<$b)?-1:1;
}