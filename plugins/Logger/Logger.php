<?php
class Logger {
	function __construct() {
		$this->writer = new Zend_Log_Writer_Firebug();
		$this->logger = new Zend_Log($this->writer);
	}
	
	function log($what) {
		$this->logger->log($what, Zend_Log::INFO);
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