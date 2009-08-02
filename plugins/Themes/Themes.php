<?php
class Themes extends Joobsbox_Plugin_AdminBase
{
  private $currentlyProcessedTheme;
  
	function init() {
	  	$this->buildThemeIndexes();
      $params = $this->request->getParams();

      if(isset($params['form_action']) && $params['form_action'] == "activate") {
        $this->activateTheme($params['theme_name']);
      }
      
      if(isset($params['form_action']) && $params['form_action'] == "delete") {
        $this->deleteTheme($params['theme_name']);
      }
  }
  
  private function buildThemeIndexes() {
    $config = Zend_Registry::get("conf");
    
    $themes = array();
    foreach(new DirectoryIterator('themes') as $theme) {
      $name = $theme->getFilename();
      if($name[0] != '.' && $theme->isDir() && $name != '_admin' && $name != 'core') {
        $themes[$theme->getFilename()] = $theme->getFilename();
      }
    }
    $this->view->themes = $themes;
    
    $adminThemes = array();
    foreach(new DirectoryIterator('themes/_admin') as $theme) {
      $name = $theme->getFilename();
      if(!$theme->isDot() && $theme->isDir()) {
        $adminThemes[$theme->getFilename()] = $theme->getFilename();
      }
    }
  }
	
	private function activateTheme($theme) {
	  
		if(in_array($theme, $this->view->themes)) {
		  $conf = new Zend_Config_Ini("config/config.ini.php", null, array(
			  'skipExtends'        => true,
        'allowModifications' => true)
      );
      $conf->general->theme = $theme;
      // Write the configuration file
      $writer = new Zend_Config_Writer_Ini(array(
        'config'   => $conf,
        'filename' => 'config/config.ini.php')
      );
      $writer->write();
		} else {
		  throw new Exception($this->view->translate("The theme you are trying to activate doesn't exist!"));
		}
	}
	
	private function deleteTheme($theme) {
	  if(in_array($theme, $this->view->themes)) {
	    $this->currentlyProcessedTheme = realpath("themes/" . $theme) . '/';
	    if(!file_exists("themes/.Trash")) {
	      @mkdir("themes/.Trash");
	      @exec("chmod go+w themes/.Trash");
	    }
	    
	    if(file_exists("themes/.Trash")) {  
	      @exec(escapeshellcmd('mv "themes/' . $theme . '" "themes/.Trash/"'));
	      if(file_exists("themes/" . $theme)) {
	        $this->alerts[] = $this->view->translate("Could not move the selected theme to trash. Maybe the directory 'themes/.Trash/' doesn't have the required permissions.");
		    } else {
		      header("Location: " . $_SERVER['REQUEST_URI']);
		      exit();
		    }
		  } else {
		    $this->alerts[] = $this->view->translate("The directory 'themes/.Trash/' doesn't exist and I couldn't create it. I tried, but still didn't succeed.");
		  }
		} else {
		  $this->alerts[] = $this->view->translate("The theme you are trying to delete doesn't exist!");
		}
	}
}
