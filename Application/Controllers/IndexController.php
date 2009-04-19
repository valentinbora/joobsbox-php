<?php
/**
 * Index Controller
 * 
 * Manages the homepage
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controllers
 */
 
/**
 * Manages the homepage
 * @package Joobsbox_Controllers
 * @category Joobsbox
 */
class IndexController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		require_once "Application/Models/Jobs.php";
		$this->_model = new Model_Jobs();
		
		$this->view->jobs = $this->_model->fetchNJobsPerCategory();
		
		$this->_helper->event("received_jobs");
	}
}
