<?php
/**
 * Categories Iterator definition
 * 
 * This file defines the {@link categoriesIteratorObject()} which contains a Category collection and is able to manipulate it
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterator
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Categories Iterator object
 * 
 * @package Joobsbox_Iterator
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class Joobsbox_Iterator_Categories extends ArrayIterator {
	/**
     * Contains the Category collection, indexed by Category IDs
     *
     * @access public
     * @var array
    */
	public $_contents = array();
	/**
     * Contains the Category collection as key => value pairs
     *
     * @access public
     * @var array
    */
	protected $_contentsNames;
	/**
     * Contains the Category collection as key => value pairs, indexed by category links
     *
     * @access public
     * @var array
    */
	protected $_contentsLinks;
	protected $_key;
	
	/**
	 * categoriesIterator constructor - stores the received array of categories
	 *
	 * 
	 * @param array $contents array of categories
	 */
	
	function __construct($contents) {
		$this->_contentsNames = array();
		
		if(count($contents)) {
		    foreach($contents as $category) {
			    $category['children'] = array();
			    $category['collectionparent'] = $this;
			    
			    $this->_contents[$category['id']] = new Joobsbox_Iterator_Categories_CategoryObject($category);
			    $this->_contentsNames[$category['id']] = $category['name'];
			    $this->_contentsLinks[$category['id']] = $category['link'];
		    }
		
		    ksort($this->_contents);
		    foreach($this->_contents as $key => $category) {
			    if($category['parent']) {
				    $this->_contents[$category['parent']]['children'][] = $category['id'];
			    }
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
			throw new Exception("You must provide a category name, id or link");
		}

		foreach($args as $arg) {
			if(isset($this->_contents[$arg])) {
				return $this->_contents[$arg];
			}
			if(($id = array_search($arg, $this->_contentsNames)) !== FALSE) {
				return $this->_contents[$id];
			}
			if(($id = array_search($arg, $this->_contentsLinks)) !== FALSE) {
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
			throw new Exception("You must provide at least a category name, id or link");
		}
		foreach($args as $index => $arg) {
			if(isset($this->_contents[$arg])) {
				$result[] = $this->_contents[$arg];
			} else 
			if(($id = array_search($arg, $this->_contentsNames)) !== FALSE) {
				$result[] = $this->_contents[$id];
			}
			if(($id = array_search($arg, $this->_contentsLinks)) !== FALSE) {
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

		if(count($filtered)) {
		    foreach($filtered as $key => $value) {
			    if($value['Parent'] != 0) {
				    unset($filtered[$key]);
			    }
		    }
		}
		
		return new Joobsbox_Iterator_Categories($filtered);
	}
	
	function filterEmpty() {
		$filtered = $this->_contents;

		if(count($filtered)) {
		    foreach($filtered as $key => $value) {
			    if($value['nrPostings'] == 0) {
				    unset($filtered[$key]);
			    }
		    }
		}
		return new Joobsbox_Iterator_Categories($filtered);
	}
	
	function getIndexNamePairs() {
		return $this->_contentsNames;
	}
	
	/***************/
	// ORDERS
	/***************/
	function orderByOrderIndex() {
		if(count($this->_contents)) {
		    uasort($this->_contents, "compareOrderIndex");
		}
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
	$a = $a['orderindex'];
	$b = $b['orderindex'];
	
	if($a == $b) {
		return 0;
	}
	return ($a<$b)?-1:1;
}
