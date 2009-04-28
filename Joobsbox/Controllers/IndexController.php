<?php
/**
 * Index Controller
 * 
 * Manages the homepage
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 */
 
/**
 * Manages the homepage
 * @package Joobsbox_Controller
 * @category Joobsbox
 */
class IndexController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		if(!file_exists("config/db.ini.php")) {
			$this->_redirect('install/step1');
		}
		$this->_model = new Joobsbox_Model_Jobs();
		
		$this->view->jobs = $this->_model->fetchNJobsPerCategory();
		
		$this->_helper->event("received_jobs");
	}
}
