<?php
/**
 * Index Controller
 * 
 * Manages the homepage
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license)
 */
 
/**
 * Manages the homepage
 * @package Joobsbox_Controller
 * @category Joobsbox
 * @copyright Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	http://www.joobsbox.com/joobsbox-php-license)
 */
class IndexController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		if(!file_exists(APPLICATION_DIRECTORY . "/config/db.xml")) {
			$this->_redirect('install/step1');
		}
		
		try {
		  $this->_model = new Joobsbox_Model_Jobs();
				
		  $this->view->jobs = $this->_model->fetchNJobsPerCategory();
		
		  $this->_helper->event("received_jobs");
		} catch(Exception $e) {
		  rename(APPLICATION_DIRECTORY . "/config/db.xml", APPLICATION_DIRECTORY . "/config/db.xml.bak");
      $this->_redirect('install/step1');
		}
	}
}
