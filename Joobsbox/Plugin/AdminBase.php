<?php
class Joobsbox_Plugin_AdminBase
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