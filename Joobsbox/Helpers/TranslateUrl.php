<?php
class Zend_View_Helper_TranslateUrl extends Zend_View_Helper_Abstract {
	public function TranslateUrl($url) {
		return Zend_Registry::get("Joobsbox_Translate_URL")->translate($url);
	}
}