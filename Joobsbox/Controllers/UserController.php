<?php
/**
 * User management controller
 * 
 * Implements login and logout
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/*
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class UserController extends Zend_Controller_Action {
	
	public function loginAction(){
	  setLayout("login");

		$referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		$sess = new Zend_Session_Namespace("auth");

		if(!isset($sess->loginSuccessRedirectUrl)) {
			if(($pos = strpos($referer, $_SERVER['REQUEST_URI'])) !== FALSE && $pos+strlen($_SERVER['REQUEST_URI']) != strlen($referer)) {
				$sess->loginSuccessRedirectUrl = $referer;
			} else {
				$sess->loginSuccessRedirectUrl = BASE_URL;
			}
		}
		
		$this->loginForm = new Zend_Form;
		$this->loginForm->setAction($_SERVER['REQUEST_URI'])->setMethod('post')->setAttrib("id", "loginForm");
	
		$username = $this->loginForm->createElement('text', 'username')
			->setLabel('Username:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('HtmlEntities')
			->addValidator('notEmpty')
			->setRequired(true);
			
		$password = $this->loginForm->createElement('password', 'password')
			->setLabel('Password:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('HtmlEntities');
		
		$submit = $this->loginForm->createElement('submit', 'submit')
			->setLabel("Login");
			
		$this->loginForm->addElement($username)
			 ->addElement($password)
			 ->addElement($submit)
			 ->addElement($submit);
			 
		// </createForm>
		
		if ($this->getRequest()->isPost()) {
		  $this->login();
			return;
    } else {
			if(Zend_Auth::getInstance()->hasIdentity()) {
				$this->_redirect("");
			}
		}
		
		$this->view->form = $this->loginForm;
	}
	
	public function testAction() {
	    echo md5(Zend_Registry::get('staticSalt') . 'admin' . sha1('admin')) . '<br/>' . sha1('admin');
	    die();
	}
	
	private function login() {
		$form = $this->loginForm;
		
		if ($form->isValid($_POST)) {
			$values = $form->getValues();
			$model = new Joobsbox_Model_Users;
			$result = $model->authenticate($values['username'], $values['password']);
			
			if($result->isValid()) {
				$sess = new Zend_Session_Namespace("auth");
				$redirectUrl = $sess->loginSuccessRedirectUrl;
				header("Location: $redirectUrl");
				unset($sess->loginSuccessRedirectUrl);
				exit();
			} else {
				$values = $form->getValues();
				$messages = $form->getMessages();
				$form->populate($values);
				$this->view->form = $form;
				$this->view->loginError = $this->view->translate("Username and/or password incorrect");
			}
		} else {
			$values = $form->getValues();
			$messages = $form->getMessages();
			$form->populate($values);
			$this->view->form = $form;
		}
	}
	
	public function indexAction() {
		$this->_forward("login");
	}
	
	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect("");
	}
}
