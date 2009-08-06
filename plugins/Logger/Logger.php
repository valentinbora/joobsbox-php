<?php
class Logger extends Joobsbox_Plugin_Base {
	function __construct() {
	  try {
		  $firebugWriter = new Zend_Log_Writer_Firebug();
		  $this->firebugLogger = new Zend_Log($firebugWriter);
		} catch(Exception $e) {
		  $this->firebugLogDisabled = true;
		}
		
		if(is_writable(APPLICATION_DIRECTORY . '/Joobsbox/Logs/messages')) {
		  try {
		    $fileWriter = new Zend_Log_Writer_Stream(APPLICATION_DIRECTORY . '/Joobsbox/Logs/messages');
        $this->fileLogger = new Zend_Log($fileWriter);
      } catch(Exception $e) {
        $this->fileLogDisabled = true;
      }
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
