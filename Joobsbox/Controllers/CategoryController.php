<?php
/**
 * Category Controller
 * 
 * Manages category display
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * @package Joobsbox_Controller
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class CategoryController extends Zend_Controller_Action
{
	public function indexAction() {
		$this->_model = new Joobsbox_Model_Jobs();
		
		$categoryName = $this->getRequest()->getParam("action");
		$categoryName = explode(".", $categoryName);
		$categoryName = $categoryName[0];
		$categoryName = $this->_helper->filter("category_name", $categoryName);
		
		$category     = $this->_model->fetchCategories()->getCategory($categoryName);
		
		if($category) {
			$categoryId = $category['id'];
			$jobs = $this->_model->fetchAllJobs(0)->where("categoryid = '$categoryId'")->fetch();
			$this->view->category = array("name" => $categoryName, "id" => $categoryId);
			$this->view->jobs = $jobs->toArray();
		} else {
		  $this->_helper->event("category_not_exist", $categoryName);
			throw new Exception($this->view->translate("This category does not exist."));
		}
	}

	public function __call($method, $args) {
		if(!method_exists($this, $method)) {
			$this->_forward("index");
		}
	}
};
