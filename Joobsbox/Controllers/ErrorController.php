<?php
/**
 * Error Controller
 * 
 * Manages error display
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
class ErrorController extends Zend_Controller_Action 
{
	public function errorAction() 
    { 
        $this->_helper->viewRenderer->setViewSuffix('phtml');
        $errors = $this->_getParam('error_handler'); 
	
        switch ($errors->type) { 
			// Message received from the application
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
				$this->view->message = $errors->exception->getMessage();
				$this->getResponse()->setHttpResponseCode(404);
				break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->message = $this->view->translate('The page could not be found!'); 
                break; 
            default: 
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->message = $this->view->translate('An error has occured within the application'); 
                break; 
        } 

		    $dev = Zend_Registry::get("conf")->general->dev;
        $this->view->dev       = $dev;
        $this->view->exception = $errors->exception; 
        $this->view->request   = $errors->request; 
    }  
}
