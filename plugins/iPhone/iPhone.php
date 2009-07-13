<?php
class iPhone extends Joobsbox_Plugin_Base {
	public function init() {
	
	}

/*	function event_app_init () {
//		dd("Shiiiit");

		configureTheme(APPLICATION_THEME, 'iphoneindex');
	} */
	public function filter_head_html(){
		$meta = '<meta name = "viewport" content = "width = device-width user-scalable = no">';
		return array($meta);
	}
}
