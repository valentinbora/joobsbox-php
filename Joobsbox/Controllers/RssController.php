<?php
/**
 * RSS Controller
 * 
 * Manages the RSS feeds
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

class RssController extends Zend_Controller_Action {
	protected $_model;
	
	public function __call($method, $args) {
		if(!method_exists($this, $method)) {
			$this->_forward("index");
		}
	}
	
	public function indexAction(){
		$conf			= Zend_Registry::get("conf");
		$params 		= $this->getRequest()->getParams();
		$this->_model 	= new Joobsbox_Model_Jobs();
		$allJobs 		= false;
		
		if(isset($params['category'])) {
			$category = $this->_model->fetchCategories()->getCategory($params['category']);
			if($category) {
				$categoryId = $category->getProperty('id');
				$jobs = $this->_model->fetchAllJobs(0)
									 ->order('id DESC')
									 ->where("categoryid = '$categoryId'")
									 ->limit($conf->rss->all_jobs_count, 0)
									 ->fetch();
				$jobs = $jobs->toArray();
				if(count($jobs)) {
				  $lastUpdate = strtotime($jobs[0]['changeddate']);
				} else {
				  $lastUpdate = strtotime("today");
				}
			} else {
				header("HTTP/1.0 404 Not Found", true, 404);
                header("Status: 404 Not Found", true, 404);
				exit();
			}
		} else {
			$allJobs = true;
			$jobs = $this->_model->fetchAllJobs(0)
								 ->order('ID DESC')
								 ->limit($conf->rss->all_jobs_count, 0)
								 ->fetch();
			$jobs = $jobs->toArray();
			if(count($jobs)) {
			  $lastUpdate = strtotime($jobs[0]['changeddate']);
			} else {
			  $lastUpdate = strtotime("today");
			}
		}
		
		// Generate the feed
		$siteUrl = "http://" . $_SERVER["HTTP_HOST"];
		$data		= array(
			"title"			  => $conf->general->common_title . " - " . (($allJobs) ? ($this->view->translate("All jobs")) : ($params['category'])),
			"link"			  => $siteUrl . $_SERVER["REQUEST_URI"],
			"lastUpdate"	  => $lastUpdate,
			'charset'		  => 'utf-8',
			'description'	  => $conf->rss->description,
			'entries'		  => array()
		);
		if(count($jobs))
		foreach($jobs as $job) {
			$data['entries'][] = array(
				'title'		  => html_entity_decode($job['title'], ENT_QUOTES, "UTF-8"),
				'link'		  => $this->view->serverUrl() . $this->view->baseUrl('/job') . '/' . ($job['title']) . '-' . $job['id'] . '.html',
				'description' => nl2br(strip_tags(html_entity_decode($job['description'], ENT_QUOTES, "UTF-8"))),
			);
		}

		$feed = Zend_Feed::importArray($data, 'rss');
		echo $feed->saveXml();
		exit();
	}
}


