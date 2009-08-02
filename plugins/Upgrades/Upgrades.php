<?php
class Upgrades extends Joobsbox_Plugin_AdminBase
{
	function init() {
	  $this->checkCore();
	}
	
	function dashboard() {
		
	}
	
	public function upgradecoreAction() {
	  $client = new Zend_Http_Client('http://localhost/joobsbox-downloads/download/core');
	  $response = $client->request();
	  $file = $response->getBody();
    // Do shit with the file
    die();
	}
	
	private function checkCore() {
	  $client = new Zend_Http_Client('http://localhost/joobsbox-downloads/getversion');
	  $response = $client->request();
	  $latest_version = $response->getBody();
	  $current_version = file_get_contents(APPLICATION_DIRECTORY . "/Joobsbox/Version");
	  if($latest_version > $current_version) {
	    $this->view->coreUpgrade = true;
	    $this->view->latestVersion = $latest_version;
	  } else {
	    $this->view->coreUpgrade = false;
	  }
	}
}
