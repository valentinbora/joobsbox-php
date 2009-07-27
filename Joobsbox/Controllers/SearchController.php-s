<?php
/**
 * Search Controller
 * 
 * Manages search
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

class SearchController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		$this->_model = new Joobsbox_Model_Search();
		
		$query = $this->getRequest()->getParam("txtSearch");
		
		if(!strlen($query)) {
		  throw new Exception($this->view->translate("You cannot search for nothing!"));
		}
		
		$results = $this->_model->search($query);
		
		$resultsArray = array();
		foreach($results as $result) {
			$resultsArray[] = array(
				"ID"		=> $result->ID,
				"Title"		=> $result->Title,
				"Location"	=> $result->Location,
				"Company"	=> $result->Company
			);
		}
		$this->view->searchedString = $this->_helper->filter("purify_html", $query);
		$this->view->jobs = $resultsArray;
	}
	
	public function regenerateAction() {
		$this->_model = new Joobsbox_Model_Search();
		$this->_model->resetIndex();
		$jobs = new Joobsbox_Model_Jobs();
		
		$jobs = $jobs->fetchAllJobs();

		foreach($jobs as $job) {
		  $this->_model->addJob($job);
		}
		$this->_model->commit();
		dd("There are now " . $this->_model->_index->count() . " jobs indexed");
		dd("Done");
	}
}
