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
  public $textItems, $checkItems; 
  private $_model;
  
	function init() {
	  $this->_model = new Joobsbox_Model_Users;
	}
	
	public function addAction() {
    
	}
	
	function indexAction() {
	  // Edit your current profile
	  $currentUsername = Zend_Auth::getInstance()->getIdentity();
	  $userData = $this->_model->getData($currentUsername);
    if($userData !== FALSE) {
  		$form = new Zend_Form;
  		$form->setAction($_SERVER['REQUEST_URI'])->setMethod('post');
	
  		$username = $form->createElement('text', 'username')
  			->setLabel('Username:')
  			->addFilter('StripTags')
  			->addFilter('StringTrim')
  			->addValidator('notEmpty')
  			->setValue($userData['username'])
  			->setRequired(true);
			
  		$password = $form->createElement('password', 'password')
  		  ->setLabel('Password:');
		  
  		$password_v = $form->createElement('password', 'password_v')
  		  ->setLabel('Password (verification):');
    
		  $email = $form->createElement('text', 'email')
		    ->setLabel('Email:')
		    ->setValue($userData['email']);
		  
  		$submit = $form->createElement('submit', 'submit')
  			->setLabel("Modify");
			
  		$form->addElement($username)
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
		
    if($form->isValid($_POST)) {
      $values = $form->getValues();
      
      dd($values);			
		} else {
			$values = $form->getValues();
			$messages = $form->getMessages();
			$form->populate($values);
			$this->view->form = $form;
		}
		
	}
}
