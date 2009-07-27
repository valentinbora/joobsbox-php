<?php
$frontController = Zend_Controller_Front::getInstance();
$frontController->addControllerDirectory(realpath(APPLICATION_DIRECTORY . '/Application/Controllers'));
unset($frontController);
