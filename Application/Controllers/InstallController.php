<?php
/**
 * Install Controller
 * 
 * Manages the application installation
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controllers
 */
 
/**
 * Manages the application installation
 * @package Joobsbox_Controllers
 * @category Joobsbox
 */
class InstallController extends Zend_Controller_Action {
	protected $_model;
	
	public function init() {
		if(file_exists("config/db.ini.php")) {
			$this->_redirect('');
		}
	}
	
	public function indexAction(){
		$this->_redirect("install/step1");
	}
	
	public function step1Action() {
		configureTheme(APPLICATION_THEME, 'install');
		
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
			
			// Try connecting to the database
			$db = Zend_Db::factory('PDO_MYSQL', array("host" => $dbhost, "username" => $dbuser, "password" => $dbpass, "dbname" => $dbname));
			try {
				$db->query("SET NAMES 'utf8'");
			} catch(Exception $e) {
				$this->view->dberror = 1;
			}
			
			if(!isset($this->view->dberror)) {
				// Connection works - we save the data
				$config = parse_ini_file('config/config.ini.php', true);
				$config = new Zend_Config($config, true);
				$config->general->COMMON_TITLE = $sitename;
				
				$configWriter = new Zend_Config_Writer_Ini();
				$configWriter->write('config/config.ini.php', $config);
				
				// Save database info
				$config = new Zend_Config(array(
					"host"		=> $dbhost,
					"username"	=> $dbuser,
					"password"	=> $dbpass,
					"dbname"	=> $dbname
				));
				
				$configWriter = new Zend_Config_Writer_Ini();
				$configWriter->write('config/db.ini.php', $config);
				$this->_redirect('install/step2');
			}
		}
	}
	
	public function step2Action() {
		/**
		 * @todo install base database schema
		 * @todo install first user
		 */
		configureTheme(APPLICATION_THEME, 'install');
	}
}
