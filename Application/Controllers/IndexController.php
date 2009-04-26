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
		if(!file_exists("config/db.ini.php")) {
			$this->_redirect('install/step1');
		}
		require_once "Application/Models/Jobs.php";
		$this->_model = new Model_Jobs();
		
		$this->view->jobs = $this->_model->fetchNJobsPerCategory();
		
		$this->_helper->event("received_jobs");
	}
}
