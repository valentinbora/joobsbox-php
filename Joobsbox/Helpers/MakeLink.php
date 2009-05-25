<?php
/**
 * Link creation Helper
 * 
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Helpers
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Creates a valid link
 *
 * Example usage:
 * <code>
 * echo $this->MakeLink('my-possibly-badly-formatted-link');
 * </code>
 *
 * @package Joobsbox_Helpers
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 * 
 */
class Zend_View_Helper_MakeLink extends Zend_View_Helper_Abstract {
	public function MakeLink($link) {
		$link = str_replace("&amp;", "and", $link);
		$link = preg_replace("%[^\w\s-]%", "", $link);
		$link = preg_replace("%\s+%", "-", $link);
		$link = preg_replace("%-$%", "", $link);
		return $link;
	}
}