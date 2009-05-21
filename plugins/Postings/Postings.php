<?php
/**
 * Joobsbox Postings plugin
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Joobsbox
 * @package    Joobsbox_Plugins
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   New BSD License
 */
 
/**
 * Postings plugin class
 * @package	Joobsbox_Plugins
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   New BSD License
 */

class Postings extends Joobsbox_Plugin_AdminBase
{
	function init() {
		$this->jobsModel = $this->getModel("Jobs");
		$this->view->categories = $this->jobsModel->fetchCategories();
	}
	
	public function dashboard() {
	
	}
	
	function editAction() {
		$session = new Zend_Session_Namespace("PublishJob");
		$session->editJobId = $this->getRequest()->getParam("edit");
		
		$this->getActionHelper('redirector')->direct("", "publish");
	}
	
	function indexAction() {
		if(isset($_POST['action']) && method_exists($this, $_POST['action'] . 'Action')) {
			$method = $_POST['action'] . 'Action';
			$this->$method();
		}
		$pending = $this->jobsModel->fetchAllJobs(0, Joobsbox_Model_Jobs::INCLUDE_NON_PUBLIC)->where("Public = ?", 0)->order('ID DESC')->fetch()->toArray();
		
		$this->view->pending  = $pending;
		$this->view->postings = $this->jobsModel->fetchAllJobs(0)->order('ID DESC')->fetch()->toArray();
	}
	
	private function deleteAction() {
		$this->jobOperationsModel = $this->getModel("JobOperations");
		
		foreach($_POST['job'] as $job => $a) {
			$job = (int)$job;
			$this->jobOperationsModel->delete($this->jobOperationsModel->getAdapter()->quoteInto('ID = ?', $job));
		}
		echo "ok";
		die();
	}
	
	private function acceptAction() {
		$this->jobOperationsModel = $this->getModel("JobOperations");
		$this->searchModel = $this->getModel("Search");
		
		foreach($_POST['job'] as $job => $a) {
			$job = (int)$job;
			Zend_Registry::get("EventHelper")->fireEvent("job_accepted", $job);
			$x = $this->jobOperationsModel->update(array('Public' => 1, 'ChangedDate' => new Zend_Db_Expr('NOW()')), $this->jobOperationsModel->getAdapter()->quoteInto('ID = ?', $job));
			
			$this->searchModel->addJob($this->jobsModel->fetchJobById($job));
		}
		echo "ok";
		die();
	}
}
