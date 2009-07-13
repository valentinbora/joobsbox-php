<?php
/**
 * Asset loader
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 */
 
/**
 * Asset loader
 *
 * Example usage:
 * <code>
 * $this->asset->load('jquery')
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license)
 * @license	   New BSD License
 * 
 */
class Joobsbox_Helpers_AssetHelper extends Zend_Controller_Action_Helper_Abstract
{
   public function load($what) {
     $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
     switch($what) {
        case 'jquery':
          $view->headScript()->appendFile($view->baseUrl . '/public/js/lib/jquery.js');
          break;
        case 'jquery-ui':
        case 'jqueryui':
          $view->headScript()->appendFile($view->baseUrl . '/public/js/lib/jquery-ui/js/jquery-ui-1.7.custom.min.js');
          $view->headLink()->appendStylesheet($view->baseUrl . '/public/js/lib/jquery-ui/css/cupertino/jquery-ui-1.7.1.custom.css');
          break;
     }
  }
}