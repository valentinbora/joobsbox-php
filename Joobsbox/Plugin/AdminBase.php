<?php
class Joobsbox_Plugin_AdminBase
{
	protected function getModel($modelName) {
		$modelName = "Joobsbox_Model_$modelName";
		return new $modelName;
	}
}