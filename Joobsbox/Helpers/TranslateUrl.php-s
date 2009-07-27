<?php
/**
 * URL Translation Helper
 * 
 * Manages URL translation in views
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Manages URL translation in views
 *
 * Example usage:
 * <code>
 * echo $this->translateUrl("Some URL to translate");
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 * 
 */
class Zend_View_Helper_TranslateUrl extends Zend_View_Helper_Abstract {
	public function TranslateUrl($url) {
		return Zend_Registry::get("Joobsbox_Translate_URL")->translate($url);
	}
}