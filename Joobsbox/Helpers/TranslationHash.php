<?php
class Joobsbox_Helpers_TranslationHash
{
	function init() {
		$this->regenerateHash();
	}
	
	function regenerateHash() {
		$translation = "{";
		foreach(Zend_Registry::get("Translation_Hash") as $key => $value) {
			if(strlen($value) < 120) {
				$key = addslashes($key);
				$translation .= "'$key':'" . htmlentities($value, ENT_QUOTES, 'UTF-8') . "',";
			}
		}
		$translation[strlen($translation)-1] = '}';
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$viewRenderer->view->translateHash = 'var translateHash=' . $translation . ';';
	}
}