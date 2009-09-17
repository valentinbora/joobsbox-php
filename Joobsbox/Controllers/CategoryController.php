<?php
/**
 * Category Controller
 * 
 * Manages category display
 *
 * @category Joobsbox
 * @package  Joobsbox_Controller
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @version  1.0
 * @link     http://docs.joobsbox.com/php
 */
 
/**
 * Category controller class definition
 *
 * @category Joobsbox
 * @package  Joobsbox_Controller
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 */
class CategoryController extends Zend_Controller_Action
{
    /**
     * Handles the category page
     *
     * @return void
     */
    public function indexAction() 
    {
        $this->_model = new Joobsbox_Model_Jobs();

        $categoryLink = $this->getRequest()->getParam("action");
        $categoryLink = explode(".", $categoryLink);
        $categoryLink = $categoryLink[0];
        $categoryLink = $this->_helper->filter("category_link", $categoryLink);
            
        $category     = $this->_model->fetchCategories()->getCategory($categoryLink);
    
        if ($category) {
            $categoryId = $category['id'];
            $jobs = $this->_model->fetchAllJobs(0)->where("categoryid = '$categoryId'")->fetch();
            $this->view->category = array("name" => $category['name'], "id" => $categoryId);
            $this->view->jobs = $jobs->toArray();
        } else {
            $this->_helper->event("category_not_exist", $categoryLink);
            throw new Exception($this->view->translate("This category does not exist."));
        }
    }

    /**
     * Forwards category requests to indexAction
     *
     * @param string $method The method called
     * @param array  $args   The array of arguments 
     *
     * @return void
     */ 
    public function __call($method, $args) 
    {
        if (!method_exists($this, $method)) {
            $this->_forward("index");
        }
    }
};
