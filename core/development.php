<?php
 /**
 * @category Joobsbox
 * @package Joobsbox_Development_Functions
 */
function dd($stuff) {
	echo '<pre>';
	if(is_array($stuff)) {
		print_r($stuff);
	} else {
		if(is_object($stuff)) {
			var_dump($stuff);
		} else {
			echo $stuff;
		}
	}
	die();
}
