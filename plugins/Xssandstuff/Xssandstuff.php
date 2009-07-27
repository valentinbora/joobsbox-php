<?php

class Xssandstuff extends Joobsbox_Plugin_Base
{
	private $purifier;
	
	public function __construct() {
		require_once dirname(__FILE__) . '/library/HTMLPurifier.includes.php';
		$this->purifier = new HTMLPurifier();
	}
	
	public function filter_purify_html($html) {
	
		return array($this->purifier->purify($html));
	}
}
