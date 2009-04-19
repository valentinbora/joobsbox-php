<?php
/**
 * Category Controller
 * 
 * Manages category display
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controllers
 * @category Joobsbox
 */
 
/**
 * @package Joobsbox_Controllers
 * @category Joobsbox
 */
class CategorieController extends Zend_Controller_Action
{
	public function indexAction() {
		require_once "Application/Models/Jobs.php";
		$this->_model = new Model_Jobs();
		
		$categoryName = $this->getRequest()->getParam("action");
		$categoryName = explode(".", $categoryName);
		$categoryName = $categoryName[0];
		
		$category   = $this->_model->fetchCategories()->getCategory($categoryName);
		
		if($category) {
			$categoryId = $category['ID'];
			$jobs = $this->_model->fetchAllJobs(0)->where("CategoryID = '$categoryId'")->fetch();
			$this->view->category = array("Name" => $categoryName, "ID" => $categoryId);
			$this->view->jobs = $jobs->toArray();
		} else {
			throw new Exception($this->view->translate("This category does not exist."));
		}
	}

	public function __call($method, $args) {
		if(!method_exists($this, $method)) {
			$this->_forward("index");
		}
	}
};