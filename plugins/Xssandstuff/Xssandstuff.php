<?php

class Xssandstuff extends Joobsbox_Plugin_Base
{
	public function filter_purify_html($html) {
	  require_once dirname(__FILE__) . '/library/htmLawed/htmLawed.php';
		return array(htmLawed($html, array('safe'=>1, 'deny_attribute'=>'style')));
	}
}
