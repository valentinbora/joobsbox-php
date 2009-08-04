<?php
function customErrorHandler($errNo, $errStr, $errFile, $errLine) {
	Zend_Registry::get("EventHelper")->fireEvent("error", $errStr . " - " . $errFile . " - " . $errLine);

	if(isset(Zend_Registry::get("conf")->main->dev) && Zend_Registry::get("conf")->main->dev) {
	  return true;
	} else {
	  return false;
	}
}

set_error_handler("customErrorHandler");