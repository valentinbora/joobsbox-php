<?php
/**
 * Public Controller
 * 
 * Manages static files like js, css, images
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controllers
 */
 
/**
 * @package Joobsbox_Controllers
 */
class PublicController extends Zend_Controller_Action
{
	private $types = array(
		// Web type files
		"html"	=> "text/html",
		"css"	=> "text/css",
		"js"	=> "text/javascript",
		// Images
		"png"	=> "image/png",
		"jpg"	=> "image/jpeg",
		"gif"	=> "image/gif",
		// Others
		"txt"	=> "text/plain"
	);
	
	public function __construct() {
		if(strlen(BASE_URL)) {
		    $path = str_replace(BASE_URL . '/', '', $_SERVER['REQUEST_URI']);
		} else {
		    $path = $_SERVER['REQUEST_URI'];
		}
		
		$path = str_replace("public/", "", $path);
		$path = urldecode($path);
		if(realpath(APPLICATION_DIRECTORY . '/Application/themes/' . $path)) {
			$this->serveFile(realpath(APPLICATION_DIRECTORY . '/Application/themes/' . $path));
		} else {
			if(realpath(APPLICATION_DIRECTORY . '/public/' . $path)) {
				$this->serveFile(realpath(APPLICATION_DIRECTORY . '/public/' . $path));
			} else {
				header("HTTP/1.0 404 Not Found", true, 404);
			}
		}
		exit(0);
	}
	
	private function serveFile($path) {
		$ext = explode(".", $path);
		$ext = end($ext);
		$ext = strtolower($ext);
		header("Content-type: " . (isset($this->types[$ext]) ? $this->types[$ext] : "application/octet-stream"));
		readfile($path);
	}
}
