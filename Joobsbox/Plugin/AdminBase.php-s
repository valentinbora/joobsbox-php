<?php
/**
 * Admin plugin base
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Plugin
 * @copyright Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license http://www.joobsbox.com/joobsbox-php-license
 */

 /**
 * Provides some basic intrinsic functionality for each admin plugin to extend on
 * 
 * @package Joobsbox_Plugin
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class Joobsbox_Plugin_AdminBase extends Joobsbox_Plugin_Base
{
	protected function getModel($modelName) {
		$modelName = "Joobsbox_Model_$modelName";
		return new $modelName;
	}
	
	protected function getRequest() {
		return $this->request;
	}
	
	protected function getActionHelper($helper) {
		return Zend_Controller_Action_HelperBroker::getStaticHelper($helper); 
	}
}