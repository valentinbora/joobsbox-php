<?php
class Zend_View_Helper_MakeLink extends Zend_View_Helper_Abstract {
	public function MakeLink($link) {
		$link = str_replace("&amp;", "and", $link);
		$link = preg_replace("%[^\w\s-]%", "", $link);
		$link = preg_replace("%\s+%", "-", $link);
		$link = preg_replace("%-$%", "", $link);
		return $link;
	}
}