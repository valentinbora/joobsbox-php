<?php
class Themes extends Joobsbox_Plugin_AdminBase
{
	function init() {
	  	$form = new Zend_Form;
  		$form->setAction($_SERVER['REQUEST_URI'])->setMethod('post')->setAttrib("id", "formPublish");

      $themes = array();
      foreach(new DirectoryIterator('Joobsbox/Themes') as $theme) {
        $name = $theme->getFilename();
        if(!$theme->isDot() && $theme->isDir() && $name != '_admin') {
          $themes[$theme->getFilename()] = $theme->getFilename();
        }
      }
      
      $adminThemes = array();
      foreach(new DirectoryIterator('Joobsbox/Themes/_admin') as $theme) {
        $name = $theme->getFilename();
        if(!$theme->isDot() && $theme->isDir()) {
          $adminThemes[$theme->getFilename()] = $theme->getFilename();
        }
      }
      
  		$theme = $form->createElement('select', 'theme')
  			->setLabel('Choose a theme:')
  			->setMultiOptions($themes);
  			
  		$adminTheme = $form->createElement('select', 'adminTheme')
  			->setLabel('Choose an admin theme:')
  			->setMultiOptions($adminThemes);

  		$submit = $form->createElement('submit', 'submit')
  			->setLabel("Set");

  		$config = Zend_Registry::get("conf");

  		$form->addElement($theme)
  		     ->addElement($adminTheme)
  		     ->addElement($submit);

  		$this->form = $form;
  		$this->view->form = $form->render();

  		if ($this->getRequest()->isPost()) {
          $this->validateForm();
  		    return;
      } else {
        $theme->setValue($this->view->conf->general->theme);
        $adminTheme->setValue($this->view->conf->general->admin_theme);
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
  		
  		$conf->general->theme = $_POST['theme'];
  		$conf->general->admin_theme = $_POST['adminTheme'];
      
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
