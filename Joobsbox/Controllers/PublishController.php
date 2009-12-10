<?php
/**
 * Publish Controller
 * 
 * Manages new postings
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
class PublishController extends Zend_Controller_Action 
{
	public $form;
	public function indexAction() 
    { 
		/*
		 *
		 *	@todo email site admin when inserted
		 *
		 */
		$this->_model = new Joobsbox_Model_Jobs;
		$this->form = new Joobsbox_Form_Publish;
		
		// Render the form
		$this->view->form = $this->form->render;
		
		if ($this->getRequest()->isPost()) {
            $this->validateForm();
            return;
        }
		
		$this->view->form = $this->form->render();	
    }
	
	private function validateForm() {
		$form = $this->form;
		
		$publishNamespace = new Zend_Session_Namespace('PublishJob');
		$values = $form->getValues();

        if ($form->isValid($_POST)) {        
            $form = $this->_helper->filter('publish_form_submit', $this->form);
            
		    $jobOperations = new Joobsbox_Model_JobOperations;
			$searchModel = new Joobsbox_Model_Search;	
			$values = $form->getValues();
			$hash = md5(implode("", $values));

			if(isset($publishNamespace->jobHash) && $publishNamespace->jobHash == $hash) {
				throw new Exception($this->view->translate("You are not allowed to add the same job multiple times."));
			}
			
			if(isset($publishNamespace->editJobId)) {
				// We have to modify it, nothing more to discuss
				try {
					$where = $jobOperations->getAdapter()->quoteInto('id = ?', $publishNamespace->editJobId);
					$values['id'] = $publishNamespace->editJobId;
					$jobOperations->update(array(
						'categoryid'	=> $values['category'],
						'title'			=> $values['title'],
						'description'	=> $this->_helper->filter("purify_html", $values['description']),
						'toapply'		=> $values['application'],
						'company'		=> $values['company'],
						'location'		=> $values['location'],
						'changeddate'	=> date("Y-m-d"),
						'expirationdate' => strtotime($values['expirationdate']),
						'public'		=> 1
					), $where);

					$this->view->editSuccess = 1;
					$searchModel->addJob($values);
					unset($publishNamespace->editJobId);
					$this->_helper->event("job_edited", $values);
					$publishNamespace->jobHash = $hash;
					Joobsbox_Helpers_Cache::clearAllCache();
				} catch (Exception $e) {
					throw new Exception($this->view->translate("An error occured while saving the job. Please try again."));
				}
			} else {
				// Ok, here we go: insert the job into the database --- bombs away!
				$values = $form->getValues();

				try {
				    // Needs posting_ttl configuration directive
				    $this->_conf = Zend_Registry::get("conf");
				  
					$values['id'] = $jobOperations->insert(array(
						'categoryid'        => $values['category'],
						'title'			    => $values['title'],
						'description'       => $this->_helper->filter("purify_html", $values['description']),
						'toapply'		    => $values['application'],
						'company'		    => $values['company'],
						'location'		    => $values['location'],
						'changeddate'       => date("Y-m-d"),
						'postedat'		    => date("Y-m-d"),
						'expirationdate'    => strtotime("+" . $this->_conf->general->posting_ttl . " days"),
						'public'		    => 0
					));
                    
					$this->view->addSuccess = 1;
					$this->_helper->event("job_posted", $values);
					$publishNamespace->jobHash = $hash;
				} catch (Exception $e) {
					throw new Exception($this->view->translate("An error occured while saving the job. Please try again."));
				}
			}
		} else {
			$values = $form->getValues();
			$messages = $form->getMessages();
			$form->populate($values);
			$this->form = $form;
			$this->view->form = $this->form->render();	
		}
	}
}
