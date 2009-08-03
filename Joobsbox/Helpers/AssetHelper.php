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
   public function load() {
 		 $args = func_get_args();
 		 $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

 		 foreach($args as $what) {
       switch($what) {
            case 'jquery':
              $view->js->load('/public/js/lib/jquery.js');
              break;
            case 'jquery-ui':
            case 'jqueryui':
              $view->js->load('/public/js/lib/jquery-ui/js/jquery-ui-1.7.custom.min.js');
              $view->css->load('/public/js/lib/jquery-ui/css/cupertino/jquery-ui-1.7.1.custom.css');
              break;
            case 'jquery-corner':
              $view->js->load('/public/js/lib/jquery.corner.js', 'text/javascript');
              break;
            case 'jquery-pngfix':
              $view->js->load('/public/js/lib/jquery.pngFix.pack.js', 'text/javascript');
              break;
      }
    }
  }
}
