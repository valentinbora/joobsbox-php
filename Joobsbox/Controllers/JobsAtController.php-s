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

class JobsAtController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		$this->_model = new Joobsbox_Model_Search();
		$query = $this->getRequest()->getParam('action');
		
		$results = $this->_model->searchTag('Company', $query);

		$resultsArray = array();
		foreach($results as $result) {
			$resultsArray[] = array(
				"ID"		=> $result->ID,
				"Title"		=> $result->Title,
				"Location"	=> $result->Location,
				"Company"	=> $result->Company
			);
		}
		$this->view->searchedCompany = $this->_helper->filter("purify_html", $query);
		$this->view->jobs = $resultsArray;
	}
	
	public function __call($function, $args) {
	  $this->_forward('index');
	}
}
