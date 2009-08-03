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
   private $js;
   
   public function __construct() {
     if(!Zend_Registry::isRegistered("js")) {
       $this->js = array();
       Zend_Registry::set("js", $this->js);
     } else {
       $this->js = Zend_Registry::get("js");
     }
   }
   
   public function load() {
      $args = func_get_args();
      $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
      $theme = $view->theme;
      $paths = array(
        '/themes/' . $theme . '/js/', 
        '/themes/core/js/',
        ''
      );
      
      foreach($args as $what) {
        foreach($paths as $path) {
          $path .= $what;
          if(file_exists(APPLICATION_DIRECTORY . $path) && !isset($this->js[$path])) {
            $view->headScript()->appendFile($view->baseUrl . $path);
            $this->js[$path] = 1;
            Zend_Registry::set("js", $this->js);
            break;
          }
        }
      }
      return $this;
  }
}
