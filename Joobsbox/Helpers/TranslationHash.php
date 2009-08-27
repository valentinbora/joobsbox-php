<?php
/**
 * Translation Hash Helper
 * 
 * Manages the Translation Hash
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Regenerates the translation hash to be used on clientside
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class Joobsbox_Helpers_TranslationHash
{
	function init() {
		$this->regenerateHash();
	}
	
	function regenerateHash() {
	  if(!Zend_Registry::isRegistered("Translation_Hash")) {
	    return;
	  }
		$translation = Zend_Json::encode(Zend_Registry::get("Translation_Hash"));

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$viewRenderer->view->translateHash = 'var translateHash=' . $translation . ';';
	}
}
