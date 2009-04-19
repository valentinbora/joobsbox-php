<?php
class AdminPlugin
{
	protected function getModel($modelName) {
		Zend_Loader::loadFile($modelName . ".php", null, true);
		$modelName = "Model_$modelName";
		return new $modelName;
	}
}