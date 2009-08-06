<?php
class Logger extends Joobsbox_Plugin_Base {
	function startup() {
	  try {
		  $firebugWriter = new Zend_Log_Writer_Firebug();
		  $this->firebugLogger = new Zend_Log($firebugWriter);
		} catch(Exception $e) {
		  $this->firebugLogDisabled = true;
		}
		
		if(file_exists(APPLICATION_DIRECTORY . '/Joobsbox/Logs/messages')) {
		  if(!is_writable(APPLICATION_DIRECTORY . '/Joobsbox/Logs/messages')) {
		    $session = new Zend_Session_Namespace('AdminPanel');
		    $session->alerts[] = $this->_helper->translate("Your main log file doesn't have write permissions. Please give write permissions to Joobsbox/Logs/messages");
		    $this->fileLogDisabled = true;
		  }
		} else {
		  if(!is_writable(APPLICATION_DIRECTORY . '/Joobsbox/Logs/')) {
		    $session = new Zend_Session_Namespace('AdminPanel');
		    $session->alerts[] = $this->_helper->translate("Your main log file could not be created because directory Joobsbox/Logs is not writable. Please give write permissions.");
		    $this->fileLogDisabled = true;
		  }
		}
		
	  try {
	    $fileWriter = new Zend_Log_Writer_Stream(APPLICATION_DIRECTORY . '/Joobsbox/Logs/messages');
      $this->fileLogger = new Zend_Log($fileWriter);
    } catch(Exception $e) {
      $this->fileLogDisabled = true;
    }
	}
	
	function log($what) {
	  if(!isset($this->fileLogDisabled)) {
	    $this->fileLogger->log($what, Zend_Log::INFO);
	  }
	  if(!isset($this->firebugLogDisabled)) {
		  $this->firebugLogger->log($what, Zend_Log::INFO);
		}
	}
	
	function event_error($what) {
	  $this->log($what);
	}
	
	function event_log($what) {
		$this->log($what);
	}
}
