<?php
class Upgrades extends Joobsbox_Plugin_AdminBase
{
  private $requestBase = 'http://localhost/joobsbox-downloads';
  
	function indexAction() {
	  $this->checkCore();
	}
	
	function init() {
	  
	}
	
	function dashboard() {
		
	}
	
	public function upgradecoreAction() {
	  @ini_set('memory_limit', '256M');
	  
// Must set timeout !!!!!

    if(file_exists(APPLICATION_DIRECTORY . "/Joobsbox/Temp/Core_Upgrade_Log")) {
      $log = unserialize(file_get_contents(APPLICATION_DIRECTORY . "/Joobsbox/Temp/Core_Upgrade_Log"));
    } else {
      $log = array(
        'lastActionMessage' => $this->view->translate("Starting file download"),
        'actions' => array(),
        'messageLog' => array()
      );
      $this->updateLogFile($log);
    }
	  
	  /******************* LATEST FILE DOWNLAD *******************/
	  /***********************************************************/
	  if(!isset($log['actions']['downloaded'])) {
  	  // Download file from our repository
  	  $client = new Zend_Http_Client($this->requestBase . '/download/core');
	  
  	  try {
  	    $response = $client->request();
    	  $file = $response->getBody();
    	  file_put_contents(APPLICATION_DIRECTORY . '/Joobsbox/Temp/latest.zip', $file);
    	  
  	    $log['messageLog'][] = $this->view->translate("Downloaded latest JoobsBox archive.");
  	    $log['actions']['downloaded'] = true;
  	    
  	    $this->updateLogFile($log);
  	    
  	  } catch(Exception $e) {
  	    $this->view->coreUpgrade = false;
  	    
  	    $log['messageLog'][] = $this->view->translate("Couldn't connect to the JoobsBox download server. Please check connection or contact your system's administrator.");
  	    $log['actions']['FAILED'] = true;
  	    
  	    $this->updateLogFile($log);
  	  }
  	}
  	
  	/*************************** UNZIP *************************/
	  /***********************************************************/
	  require_once APPLICATION_DIRECTORY . "/Joobsbox/Filesystem/Zip.php";
	  if(file_exists(APPLICATION_DIRECTORY . "/Joobsbox/Temp/joobsbox")) {
	    // Move to trash
	    @rename(APPLICATION_DIRECTORY . "/Joobsbox/Temp/joobsbox", APPLICATION_DIRECTORY . "/Joobsbox/Temp/.Trash/joobsbox");
	  }
	  $archive = new PclZip(APPLICATION_DIRECTORY . '/Joobsbox/Temp/latest.zip');
	  $files   = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);
	  if($files) {
	    $log['messageLog'][] = $this->view->translate("Unzipped latest archive.");
	  } else {
	    $log['messageLog'][] = $this->view->translate("An error occured while unpacking the zip file. Please try again.");
	    $log['actions']['FAILED'] = true;
	  }
	  
  	$this->updateLogFile($log);
  	
  	/************************ UPDATE FILES *********************/
	  /***********************************************************/
	  $log['messageLog'][] = $this->view->translate("Updating files...");
	  
	  if(file_exists(APPLICATION_DIRECTORY . '/Joobsbox/Temp/backup/')) {
	    rename(APPLICATION_DIRECTORY . '/Joobsbox/Temp/backup/', APPLICATION_DIRECTORY . '/Joobsbox/Temp/.Trash/backup/');
	  }
	  
	  mkdir(APPLICATION_DIRECTORY . '/Joobsbox/Temp/backup/');
	  $log['messageLog'][] = $this->view->translate("Created main application backup location Joobsbox/Temp/backup/");
	  $log['messageLog'][] = $this->view->translate("Backing up and upgrading files as needed");
	  
    $this->updateLogFile($log);
    
    $this->recurseAndUpgrade(0, $files);
    
	  die();
	}
	
	private function recurseAndUpgrade($index, $files) {
	  if(!isset($files[$index])) return;
	  
	  // Get rid of joobsbox/ from the filename
	  $name = str_replace("joobsbox/", "", $files[$index]['filename']);
	  
	  if($files[$index]['folder']) {// If it's a folder
	    if(!file_exists($name)) {     // That doesn't exist
	      mkdir($name);               // Create it
	    } else {                    // Otherwise
        // Back it up
	      mkdir(APPLICATION_DIRECTORY . "/Joobsbox/Temp/backup/" . $name);
	    }
	  } else {                      // If it's a file
	  	if(file_exists($name) && $files[$index]['contents'] != file_get_contents(APPLICATION_DIRECTORY . '/' . $name)) {    // That already exists
	      // Back it up
	      copy(APPLICATION_DIRECTORY . '/' . $name, APPLICATION_DIRECTORY . "/Joobsbox/Temp/backup/" . $name);
	      // Write it
	      file_put_contents($name, $files[$index]['contents']);
	    }
	  }
	  // Life goes on
	  $this->recurseAndUpgrade($index + 1, $files);
	}
	
	private function updateLogFile($log) {
	  file_put_contents(APPLICATION_DIRECTORY . "/Joobsbox/Temp/Core_Upgrade_Log", serialize($log));
	}
	
	public function upgradecoreprogressAction() {
    if(file_exists(APPLICATION_DIRECTORY . "/Joobsbox/Temp/Core_Upgrade_Log")) {
      // Read file and split per lines
      $log = unserialize(file_get_contents(APPLICATION_DIRECTORY . "/Joobsbox/Temp/Core_Upgrade_Log"));
      echo end($log['messageLog']);
    } else {
      echo $this->view->translate("Couldn't find core upgrade log.");
    }
    die();
	}
	
	private function checkCore() {
	  $failed = false;
	  
	  // Interrogate latest version number from the repository
	  $client = new Zend_Http_Client($this->requestBase . '/getversion');
	  try {
	    $response = $client->request();
	    if($response->isError()) { // Means we couldn't connect or something
  	    $this->view->coreUpgrade = false;
  	    $this->view->error = $this->view->translate('There was a problem contacting the download server.');
  	  } else {
  	    $latest_version = $response->getBody();
  	    $current_version = file_get_contents(APPLICATION_DIRECTORY . "/Joobsbox/Version");
  	    if($latest_version > $current_version) {
  	      $this->view->coreUpgrade = true;
  	      $this->view->latestVersion = $latest_version;
  	    } else {
  	      $this->view->coreUpgrade = false;
  	    }
  	  }
	  } catch(Exception $e) { // Couldn't connect to the server due to connection or other problems
      $this->view->coreUpgrade = false;
	    $this->view->error = $this->view->translate("Couldn't connect to the JoobsBox download server. Please check connection or contact your system's administrator.");
	  }
  }
}
