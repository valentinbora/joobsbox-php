<?php
/**
 * Job Controller
 * 
 * Manages job display
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
class JobController extends Zend_Controller_Action
{	
	public function jobAction() {
		$jobUrl = $this->getRequest()->getParams();
		$jobUrl = $jobUrl['action'];
		$jobUrl = str_replace(".html", "", $jobUrl);
		$jobId  = explode("-", $jobUrl);
		$jobId  = end($jobId);
		$jobId  = (int)$jobId;
		
		$jobs	= new Joobsbox_Model_Jobs();
		$job	= $jobs->fetchJobById($jobId);
		
		if(!$job) {
			throw new Zend_Controller_Action_Exception($this->view->translate('The requested job does not exist!'));
		}
		
		$this->_helper->event("display_job", $job);
		$this->view->job = $job;
	}
	
	public function __call($method, $args) {
		if(!method_exists($this, $method)) {
			$this->_forward("job");
		}
	}
}
