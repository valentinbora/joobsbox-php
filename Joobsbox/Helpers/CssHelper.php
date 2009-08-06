<?php
/**
 * CSS loader
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 */
 
/**
 * CSS loader
 *
 * Example usage:
 * <code>
 * $this->css->load('file.css')
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license)
 * @license	   New BSD License
 * 
 */
class Joobsbox_Helpers_CssHelper extends Zend_Controller_Action_Helper_Abstract
{
   private $css;
   public $cssPaths;
   
   public function __construct() {
     $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
     $theme = $view->theme;

      $this->cssPaths = array(
        '/themes/' . $theme . '/css', 
        '/themes/core/css',
        ''
      );
     
     if(!Zend_Registry::isRegistered("css")) {
       $this->css = array();
       Zend_Registry::set("css", $this->css);
     } else {
       $this->css = Zend_Registry::get("css");
     }
   }
   
   public function load() {
     $args = func_get_args();
     $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
     $theme = $view->theme;

     foreach($args as $what) {
       foreach($this->cssPaths as $path) {
         $path .= '/' . $what;

         if(file_exists(APPLICATION_DIRECTORY . $path) && !isset($this->css[$path])) {
           $view->headLink()->appendStylesheet($view->baseUrl . $path);
           $this->css[$path] = 1;
           Zend_Registry::set("css", $this->css);
         }
       }
      }
      return $this;
  }
  
  public function addPath($path) {
    $this->cssPaths[] = $path;
  }
}
