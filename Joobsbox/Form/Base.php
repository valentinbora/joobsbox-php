<?php

class Joobsbox_Form_Base extends Zend_Form
{
    protected function getModel($modelName) {
		$modelName = "Joobsbox_Model_$modelName";
		try {
		    $model = new $modelName;
		} catch (Exception $e) {
		    $model = null;
		}
		
		return $model;
	}
	
	public function appendElement($name = '')
	{
	    $element = $this->getElement($name);
	    $this->removeElement($name);
	    $this->addElement($element);
	}
}