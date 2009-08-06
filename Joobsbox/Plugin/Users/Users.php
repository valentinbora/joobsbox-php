<?php
/**
 * Joobsbox Users plugin
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Joobsbox
 * @package    Joobsbox_Plugins
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://joobsbox.com/joobsbox-php-license
 */
 
/**
 * Users plugin class
 * @package	Joobsbox_Plugins
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://joobsbox.com/joobsbox-php-license
 */

class Users extends Joobsbox_Plugin_AdminBase
{
  public $textItems, $checkItems, $userData; 
  private $_model;
  
	function init() {
	  $this->_model = new Joobsbox_Model_Users;
	}
	
	public function addAction() {
    
	}
	
	function indexAction() {
	  // Edit your current profile
	  $currentUsername = Zend_Auth::getInstance()->getIdentity();
	  $this->userData = $this->_model->getData($currentUsername);
	  
    if($this->userData !== FALSE) {
  		$form = new Zend_Form;
  		$form->setAction($_SERVER['REQUEST_URI'])->setMethod('post');
	
  		$username = $form->createElement('text', 'username')
  			->setLabel('Username:')
  			->addFilter('StripTags')
  			->addFilter('StringTrim')
  			->addValidator('notEmpty')
  			->setValue($this->userData['username'])
  			->setRequired(true);
  			
  		$old_password = $form->createElement('password', 'old_password')
  		  ->addFilter('StringTrim')
  		  ->setLabel('Current password:');
			
  		$password = $form->createElement('password', 'password')
  		  ->addFilter('StringTrim')
  		  ->setLabel('Password:');
		  
  		$password_v = $form->createElement('password', 'password_v')
  		  ->addFilter('StringTrim')
  		  ->setLabel('Password (verification):');
    
		  $email = $form->createElement('text', 'email')
		    ->setLabel('Email:')
		    ->addFilter('StringTrim')
		    ->setValue($this->userData['email']);
		  
  		$submit = $form->createElement('submit', 'submit')
  			->setLabel("Modify");
			
  		$form->addElement($username)
  		  ->addElement($old_password)
  		  ->addElement($password)
  		  ->addElement($password_v)
  		  ->addElement($email)
  		  ->addElement($submit);
		
  		$this->form = $form;
  		$this->view->form = $form->render();
		
  		if ($this->getRequest()->isPost()) {
          $this->validateForm();
  		    return;
      }
		
  		$this->view->form = $this->form->render();
  	}
	}
	
	private function validateForm() {
		$form = $this->form;
		$form->isValid($_POST);
		$values = $form->getValues();
		
		if(!strlen($values['old_password'])) {
		  $form->getElement("old_password")->markAsError()->addError($this->view->translate("To change any information you must input your current password."));
		} else {
		  $password = md5(Zend_Registry::get("staticSalt") . $values['old_password'] . sha1($values['old_password']));

		  if($password != $this->userData['password']) {
		    $form->getElement("old_password")->markAsError()->addError($this->view->translate("This password is not correct."));
		  }
		}
		
		if(strlen($values['password']) && $values['password'] != $values['password_v']) {
		  $form->getElement("password")->markAsError()->addError($this->view->translate("To change your password, the new password and its verification must match."));
		}
    
    if(count($form->getMessages()) == 0 && $form->isValid($_POST)) {
      $values = $form->getValues();
      unset($values['password_v']);
      
      if(!strlen($values['password'])) {
        unset($values['password']);
      }
      $this->_model->updateData($values);
      
      if(isset($values['password'])) {
        Zend_Auth::getInstance()->clearIdentity();
    		header("Location: " . BASE_URL . "/admin");
    		exit();
      }
		} else {
			$values = $form->getValues();
			$messages = $form->getMessages();
			$form->populate($values);
			$this->view->form = $form;
		}
		
	}
}
