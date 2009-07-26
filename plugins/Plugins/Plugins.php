<?php
class Plugins extends Joobsbox_Plugin_AdminBase
{
  private $aPlugins = array();
  
	function init() {  	
    if(isset($_POST['form_action'])) {
      if(method_exists($this, $_POST['form_action'])) {
        $this->$_POST['form_action']();
      }
    }
    $this->createPluginIndex();
  }
  
  private function deactivate() {
    if(isset($_POST['check'])) {
      foreach($_POST['check'] as $pluginName => $value) {
        exec(escapeshellcmd('mv "plugins/' . $pluginName . '" "plugins/_' . $pluginName . '"'));
      }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
  }
  
  private function activate() {
    if(isset($_POST['check'])) {
      foreach($_POST['check'] as $pluginName => $value) {
        $pluginName = substr($pluginName, 1);
        exec(escapeshellcmd('mv "plugins/_' . $pluginName . '" "plugins/' . $pluginName . '"'));
      }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
  }
  
  private function delete() {
    if(isset($_POST['check'])) {
      foreach($_POST['check'] as $pluginName => $value) {
        $pluginName = substr($pluginName, 1);
        exec(escapeshellcmd('mv "plugins/_' . $pluginName . '" "plugins/.Trash/' . $pluginName . '"'));
      }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
  }
  
  private function createPluginIndex() {
      $this->aPlugins = Zend_Registry::get("plugins");
      
      foreach($this->aPlugins as $key => $value) {
        if(in_array($key, $this->corePlugins)) {
          unset($this->aPlugins[$key]);
        }
      }
    
      $this->view->aPlugins = $this->aPlugins;
      
      $inactivePlugins = Zend_Registry::get("PluginLoader")->retrieveInactivePlugins();
      $this->view->inactivePlugins = $inactivePlugins;
	}
}
