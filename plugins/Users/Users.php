<?php
/**
 * Joobsbox Users plugin
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Joobsbox
 * @package    Joobsbox_Plugins
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://joobsbox.com/joobsbox-php-license
 */
 
/**
 * Users plugin class
 * @package	Joobsbox_Plugins
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://joobsbox.com/joobsbox-php-license
 */

class Users extends Joobsbox_Plugin_AdminBase
{
  public $textItems, $checkItems; 
  
	function init() {
	}
	
	public function addAction() {
    
	}
	
	function indexAction() {
	  // Edit your current profile
	  $currentUsername = Zend_Auth::getInstance()->getIdentity();
	  
		$form = new Zend_Form;
		$form->setAction($_SERVER['REQUEST_URI'])->setMethod('post');
	
		$username = $form->createElement('text', 'username')
			->setLabel('Username:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('notEmpty')
			->setRequired(true);
			
		$password = $form->createElement('password', 'password')
		  ->setLabel('Password:');
		  
		$password_v = $form->createElement('password', 'password_v')
		  ->setLabel('Password (verification):');
    
		
		$submit = $form->createElement('submit', 'submit')
			->setLabel("Set");
			
		$config = Zend_Registry::get("conf");
			
		foreach($this->textItems as $category => $items) {
		  foreach($items as $key => $label) {
		    $item = $form->createElement('text', $key)
		      ->setLabel($label)
		      ->addValidator('notEmpty')
		      ->setRequired(true)
		      ->setValue($config->$category->$key);
		    $form->addElement($item);
		  }
		  $form->addDisplayGroup(array_keys($items), $category, array(
         'legend' => ucfirst($category)
      ));
		}
		
		foreach($this->checkItems as $category => $items) {
		  foreach($items as $key => $label) {
		    $item = $form->createElement('checkbox', $key)
		      ->setLabel($label)
		      ->setChecked($config->$category->$key);
		  }
		  $form->getDisplayGroup($category)->addElement($item);
		}
		
		// Locale select
		$locales = Zend_Registry::get("Zend_Locale")->getTranslationList('language', 'en');
		foreach($locales as $key => $value) {
		  if(!file_exists("Joobsbox/Languages/$key")) {
		    unset($locales[$key]);
		  }
		}
		$locale = $form->createElement('select', 'locale')
		  ->setMultiOptions($locales)
		  ->setLabel($this->view->translate("Language"))
		  ->setValue($config->general->locale);
		$form->getDisplayGroup('general')->addElement($locale);
		
		// Timezone select
		$tzfile = file("config/timezones.ini.php");
		$timezones = array();
		foreach($tzfile as $value) {
		  $value = trim($value);
		  $value = str_replace('"', '', $value);
		  $timezones[$value] = $value;
		}
		$timezone = $form->createElement('select', 'site_timezone')
		  ->setMultiOptions($timezones)
		  ->setLabel($this->view->translate("Timezone"))
		  ->setValue($config->general->timezone);
		$form->getDisplayGroup('general')->addElement($timezone);
			
		$form->addElement($submit);
		
		$this->form = $form;
		$this->view->form = $form->render();
		
		if ($this->getRequest()->isPost()) {
        $this->validateForm();
		    return;
    }
		
		$this->view->form = $this->form->render();
	}
	
	private function validateForm() {
		$form = $this->form;
		
    if($form->isValid($_POST)) {
      
			$values = $form->getValues();
			$conf = new Zend_Config_Ini("config/config.ini.php", null, array(
			  'skipExtends'        => true,
        'allowModifications' => true)
      );
      
      foreach($this->textItems as $category => $items) {
  		  foreach($items as $key => $label) {
  		    $conf->$category->$key = $values[$key];
  		  }
  		}
  		
  		foreach($this->checkItems as $category => $items) {
  		  foreach($items as $key => $label) {
  		    $conf->$category->$key = (isset($_POST[$key]))?$_POST[$key]:0;
  		  }
  		}
  		
  		$conf->general->timezone = $_POST['site_timezone'];
  		$conf->general->locale = $_POST['locale'];
  		
  		// Write the configuration file
      $writer = new Zend_Config_Writer_Ini(array(
        'config'   => $conf,
        'filename' => 'config/config.ini.php')
      );
      $writer->write();
      header("Location: " . $_SERVER['REQUEST_URI']);
      exit();
		} else {
			$values = $form->getValues();
			$messages = $form->getMessages();
			$form->populate($values);
			$this->view->form = $form;
		}
		
	}
}
