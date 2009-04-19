<?php
/**
 * Error Controller
 * 
 * Manages error display
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controllers
 */
 
/**
 * @package Joobsbox_Controllers
 */
class ErrorController extends Zend_Controller_Action 
{
	public function errorAction() 
    { 
        $this->_helper->viewRenderer->setViewSuffix('phtml');
        $errors = $this->_getParam('error_handler'); 
	
        switch ($errors->type) { 
			// Mesaj primit de la aplicatie
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
				$this->view->message = $errors->exception->getMessage();
				$this->getResponse()->setHttpResponseCode(404);
				break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->message = 'Pagina ceruta nu a putut fi gasita.'; 
                break; 
            default: 
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->message = 'A aparut o eroare in aplicatie.'; 
                break; 
        } 

		$dev = Zend_Registry::get("conf");
		$dev = $dev['general']['DEV'];
        $this->view->dev       = $dev;
        $this->view->exception = $errors->exception; 
        $this->view->request   = $errors->request; 
    }  
}