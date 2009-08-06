<?php
/**
 * JavaScript loader
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 */
 
/**
 * JavaScript loader
 *
 * Example usage:
 * <code>
 * $this->js->load('jquery.js')
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license)
 * @license	   New BSD License
 * 
 */
class Joobsbox_Helpers_JsHelper extends Zend_Controller_Action_Helper_Abstract
{
   private $jsCollection;
   private $paths;
   
   public function __construct() {
     $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
     $theme = $view->theme;
     
     $this->paths = array(
       '/themes/' . $theme . '/js', 
       '/themes/core/js',
       ''
     );
     
     if(!Zend_Registry::isRegistered("Joobsbox_Js_Loader_MaxPrio")) {
       Zend_Registry::set("Joobsbox_Js_Loader_MaxPrio", 0);
       Zend_Registry::set("Joobsbox_Js_Loader_MinPrio", 0);
     }
     
     if(!Zend_Registry::isRegistered("jsCollection")) {
       $this->jsCollection = array();
       Zend_Registry::set("jsCollection", $this->jsCollection);
     } else {
       $this->jsCollection = Zend_Registry::get("jsCollection");
     }
   }
   
   public function load() {
      $args = func_get_args();
      $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
      $theme = $view->theme;
      
      $maxPrio = Zend_Registry::get("Joobsbox_Js_Loader_MaxPrio");
      $minPrio = Zend_Registry::get("Joobsbox_Js_Loader_MinPrio");
      
      foreach($args as $what) {
        if(is_array($what)) {
          $prio = $this->getPrio($what[1]);
          $what = $what[0];
        } else {
          $prio = 0;
        }
        
        if($prio < $maxPrio) $maxPrio = $prio;
        if($prio > $minPrio) $minPrio = $prio;
        
        foreach($this->paths as $path) {
          $path .= '/' . $what;

          if(file_exists(APPLICATION_DIRECTORY . $path) && !isset($this->js[$path])) {
            $this->jsCollection[$view->baseUrl . $path] = $prio;
            if($prio == 0) {
              $view->headScript()->appendFile($view->baseUrl . $path);
            } else {
              $view->headScript()->offsetSetFile($prio, $view->baseUrl . $path);
            }
            asort($this->jsCollection);
            Zend_Registry::set("jsCollection", $this->jsCollection);
            break;
          }
        }
      }
      return $this;
  }
  
  public function getPrio($prio) {
    if(is_int($prio)) return $prio;
    
    $maxPrio = Zend_Registry::get("Joobsbox_Js_Loader_MaxPrio");
    $minPrio = Zend_Registry::get("Joobsbox_Js_Loader_MinPrio");

    switch($prio) {
      case "highest":
        return $maxPrio - 50;
      case "high":
        return $maxPrio - 1;
      case "normal":
        return 0;
      case "low":
        return $minPrio + 1;
      case "lowest":
        return $minPrio + 50;
      default:
        return 0;
    }
  }
  
  public function write($script) {
    $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
    $view->headScript()->appendScript($script);
    return $this;
  }
  
  public function addPath($path) {
    $this->paths[] = $path;
  }
  
  public function getPath() {
    return $this->paths;
  }
}
