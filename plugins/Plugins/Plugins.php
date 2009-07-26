<?php
class Plugins extends Joobsbox_Plugin_AdminBase
{
  private $aPlugins = array();
  
	function init() {  	

      $this->aPlugins = Zend_Registry::get("plugins");
      
      foreach($this->aPlugins as $key => $value) {
        if(in_array($key, $this->corePlugins)) {
          unset($this->aPlugins[$key]);
        }
      }
    
      $this->view->aPlugins = $this->aPlugins;
	}
}
