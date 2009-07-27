<?php
class Joobsbox_Plugin_Controller extends Zend_Controller_Plugin_Abstract
{
  private $eventHelper;
  
  public function __construct() {
    $this->eventHelper = Zend_Registry::get("EventHelper");
  }
  
  public function routeStartup(Zend_Controller_Request_Abstract $request) {
    $this->eventHelper->fireEvent("route_startup", $request);
  }

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {
    $this->eventHelper->fireEvent("route_shutdown", $request);
  }
  
  public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
    $this->eventHelper->fireEvent("pre_front_controller", $request);
  }

  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    $this->eventHelper->fireEvent("pre_dispatch", $request);
  }

  public function postDispatch(Zend_Controller_Request_Abstract $request) {
    $this->eventHelper->fireEvent("post_dispatch", $request);
  }

  public function dispatchLoopShutdown() {
    $this->eventHelper->fireEvent("post_front_controller");
  }
}
