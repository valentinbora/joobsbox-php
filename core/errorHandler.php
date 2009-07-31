<?php
function customErrorHandler($errNo, $errStr, $errFile, $errLine) {
	global $buf;
	$errorTypes = array (
              E_ERROR              => 'Error',
              E_WARNING            => 'Warning',
              E_PARSE              => 'Parsing Error',
              E_NOTICE             => 'Notice',
              E_CORE_ERROR         => 'Core Error',
              E_CORE_WARNING       => 'Core Warning',
              E_COMPILE_ERROR      => 'Compile Error',
              E_COMPILE_WARNING    => 'Compile Warning',
              E_USER_ERROR         => 'User Error',
              E_USER_WARNING       => 'User Warning',
              E_USER_NOTICE        => 'User Notice',
              E_STRICT             => 'Runtime Notice',
              E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
          );

	Zend_Registry::get("EventHelper")->fireEvent("error", $errStr . " - " . $errFile . " - " . $errLine);
	return false;
}
set_error_handler("customErrorHandler");