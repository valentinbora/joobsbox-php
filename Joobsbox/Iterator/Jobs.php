<?php
/**
 * Jobs Iterator definition
 * 
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Iterator
 */
 
/*
 * @package Joobsbox_Iterator
 */
class Joobsbox_Iterator_Jobs extends ArrayIterator {
	public $_contents;
	protected $_key;
	protected $_jobsModel;
	
	function __construct($contents) {
		$this->_contents = $contents;
	}
	
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
	
	function filterByCategoryName($categoryName) {
		if (Zend_Registry::isRegistered("categories_indexed_by_id")) {
			$categories = Zend_Registry::get("categories_indexed_by_id");
		} else {
			$this->_jobsModel = new Joobsbox_Model_Jobs;
			$categories = $this->_jobsModel->fetchCategories(NULL, INDEXED_BY_ID);
			Zend_Registry::set("categories_indexed_by_id", $categories);
		}
		
		$jobs = $this->_contents;
		foreach($jobs as $key => $job) {
			if($categories[$job['CategoryID']] != $categoryName) {
				unset($jobs[$key]);
			}
		}
		$jobs = array_values($jobs);
		return new jobsIteratorObject($jobs);
	}
	
	function filterByCategoryId($categoryId) {
		$jobs = $this->_contents;
		foreach($jobs as $key => $job) {
			if($job['CategoryID'] != $categoryId) {
				unset($jobs[$key]);
			}
		}
		$jobs = array_values($jobs);
		return new jobsIteratorObject($jobs);
	}
	
	function toArray() {
		return $this->_contents;
	}
}