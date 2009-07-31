<?php
class Logger extends Joobsbox_Plugin_Base {
	function __construct() {
		$firebugWriter = new Zend_Log_Writer_Firebug();
		$this->firebugLogger = new Zend_Log($firebugWriter);
		
		$fileWriter = new Zend_Log_Writer_Stream(APPLICATION_DIRECTORY . '/Joobsbox/Logs/messages');
    $this->fileLogger = new Zend_Log($fileWriter);
	}
	
	function log($what) {
		$this->firebugLogger->log($what, Zend_Log::INFO);
		$this->fileLogger->log($what, Zend_Log::INFO);
	}
	
	function event_error($what) {
	  $this->log($what);
	}
	
	function event_retrieve_jobs() {
		$this->log("Retrieved jobs");
	}
	
	function event_received_jobs() {
		$this->log("Am primit joburi.");
	}
	
	function event_log($what) {
		$this->log($what);
	}
}
