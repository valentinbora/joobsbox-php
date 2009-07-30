<?php
/**
 * Install Controller
 * 
 * Manages the application installation
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
 
/**
 * Manages the application installation
 * @package Joobsbox_Controller
 * @category Joobsbox
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */
class InstallController extends Zend_Controller_Action {
	protected $_model;
	
	public function init() {
	    $params = $this->getRequest()->getParams();
	    if(isset($params['lang'])) {
	      $conf = new Zend_Config_Ini("config/config.ini.php", null, array(
  			  'skipExtends'        => true,
          'allowModifications' => true)
        );

    		$conf->general->locale = $params['lang'];

        // Write the configuration file
        $writer = new Zend_Config_Writer_Ini(array(
          'config'   => $conf,
          'filename' => 'config/config.ini.php')
        );
        $writer->write();
        $this->_redirect("install");
	    }
	    $config = new Zend_Config_Ini("config/config.ini.php");
	    if(isset($config->general->restrict_install) && $config->general->restrict_install && file_exists("config/db.ini.php")) {
		      $this->_redirect("");
	    }
	}
	
	public function indexAction(){
		$this->_redirect("install/step1");
	}
	
	/**
	 * @todo add timezone box using timezone_identifiers_list()
	 */
	public function step1Action() {
		configureTheme(APPLICATION_THEME, 'install');
		$locale = Zend_Registry::get("Zend_Locale")->getTranslationList('language', 'en');
		foreach($locale as $key => $value) {
		  if(!file_exists("Joobsbox/Languages/$key")) {
		    unset($locale[$key]);
		  }
		}
		$this->view->locales = $locale;

		if(isset($_POST['step1'])) {
			// Gather site data
			$sitename = trim($_POST['sitename']);
			$sitename = nl2br($sitename);
			// Gather database info
			$dbname	  = trim($_POST['dbname']);
			$dbuser	  = trim($_POST['dbuser']);
			$dbpass   = $_POST['dbpass'];
			$dbhost   = $_POST['dbhost'];
			$dbprefix = trim($_POST['dbprefix']);
			
			if(!strlen($dbhost) || !strlen($dbhost) || !strlen($dbpass)) {
			  $this->view->dberror = $this->view->translate("Joobsbox really needs a database. Please let it have one.");
			}
			
			// Try connecting to the database
			$db = Zend_Db::factory('PDO_MYSQL', array("host" => $dbhost, "username" => $dbuser, "password" => $dbpass, "dbname" => $dbname));
			try {
          $db->query('SHOW DATABASES');
      } catch (Zend_Db_Adapter_Exception $e) {
          $this->view->dberror = $this->view->translate("There was an error connecting to the database. Make sure the connection information you provided is correct.");
      } catch (Zend_Exception $e) {
          $this->view->dberror = $this->view->translate("There was an error connecting to the database. Make sure the connection information you provided is correct.");
      }
			
			if(!isset($this->view->dberror)) {
				// Connection works - we save the data
				$config = parse_ini_file('config/config.ini.php', true);
				$config = new Zend_Config($config, true);
				$config->general->common_title = $sitename;
				$config->db->prefix = $dbprefix;
				
				if(!is_writable("config/config.ini.php")) {
				  $this->view->dberror = $this->view->translate("config/config.ini.php is not writable. Please adjust the file permissions using FTP or SSH.");
				  return;
				}
				$configWriter = new Zend_Config_Writer_Ini();
				$configWriter->write('config/config.ini.php', $config);
				
				// Save database info
				$config = new Zend_Config(array(
					"host"		  => $dbhost,
					"username"	=> $dbuser,
					"password"	=> $dbpass,
					"dbname"  	=> $dbname
				));
				
				if(!is_writable("config/")) {
				  $this->view->dberror = $this->view->translate("config directory is not writable. Please adjust the directory permissions using FTP or SSH.");
				  return;
				}
				$configWriter = new Zend_Config_Writer_Ini();
				$configWriter->write('config/db.ini.php', $config);
				$this->_redirect('install/step2');
			}
		}
	}
	
	/**
	 * @todo install base database schema
	 * @todo install first user
	 */
	public function step2Action() {
		configureTheme(APPLICATION_THEME, 'install');
		
		$config = new Zend_Config_Ini('config/config.ini.php');
		$db = Zend_Registry::get("db");
		$sql = file_get_contents("sql/base.sql");
		$sql = str_replace("{#prefix#}", $config->db->prefix, $sql);
		$sql = str_replace("\r\n", "\n", $sql);
		$sql = explode("\n", $sql);
		$qry = "";
		foreach($sql as $line) {
		    if(trim($line) != "" && strpos($line, "--") === FALSE) {
			$qry .= $line;
			if(preg_match("/;[\040]*\$/", $line)) {
			    $db->query($qry);
			    $qry = "";
			}
		    }
		}
		$db->delete($config->db->prefix . "categories", array("ID=0"));
		$db->insert($config->db->prefix . "categories", array(
		    'ID'    => 0,
		    'Name'  => 'Uncategorized',
		    'Link'  => 'Uncategorized',
		    'OrderIndex' => 100,
		    'Parent'=> 0
		));
	}

	/**
	 * 
	 */
	public function step3Action() {
		configureTheme(APPLICATION_THEME, 'install');

		$username = trim($_POST['username']);
		$password = $_POST['password'];
		$realname = $_POST['realname'];

		$config = new Zend_Config_Ini('config/config.ini.php');
		
		if(trim($password) == "" || trim($username) == "") {
		    $this->view->error = $this->view->translate("You have to enter an admin username and password");
		} else {
		    $db = Zend_Registry::get("db");
		    $db->delete($config->db->prefix . 'users', array("username='$username'"));
		    $db->insert($config->db->prefix . 'users', array(
			'username' => $username,
			'password' => md5(Zend_Registry::get('staticSalt') . $password . sha1($password)),
			'password_salt' => sha1($password),
			'realname' => $realname
		    ));
		
		  $config = parse_ini_file('config/config.ini.php', true);
  		$config = new Zend_Config($config, true);
  		$config->general->restrict_install = 1;
		
  		$configWriter = new Zend_Config_Writer_Ini();
  		$configWriter->write('config/config.ini.php', $config);

  		$authAdapter = Zend_Registry::get("authAdapter");
  		$authAdapter->setIdentity($username)->setCredential($password);
  		$auth = Zend_Auth::getInstance();
  		$result = $auth->authenticate($authAdapter);
  	}
	}
}
