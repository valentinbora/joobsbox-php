<?php
/**
 * Search Controller
 * 
 * Manages search
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 */
 
/**
 * @package Joobsbox_Controller
 */

class SearchController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		$this->_model = new Joobsbox_Model_Search();
		
		$query = $this->getRequest()->getParam("txtSearch");

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
		$this->view->jobs = $resultsArray;
	}
	
	public function regenerateAction() {
		$this->_model = new Joobsbox_Model_Search();
	}
}
