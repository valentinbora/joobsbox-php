<?php
/**
 * Install Controller
 * 
 * Manages the application installation
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @package Joobsbox_Controller
 */
 
/**
 * Manages the application installation
 * @package Joobsbox_Controller
 * @category Joobsbox
 */
class InstallController extends Zend_Controller_Action {
	protected $_model;
	
	public function indexAction(){
		$this->_redirect("install/step1");
	}
	
	/**
	 * @todo add timezone box using timezone_identifiers_list()
	 */
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
				$config->general->common_title = $sitename;
				
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
	
	/**
	 * @todo install base database schema
	 * @todo install first user
	 */
	public function step2Action() {
		configureTheme(APPLICATION_THEME, 'install');

		$db = Zend_Registry::get("db");
		$sql = file("sql/base.sql");
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
	}

	/**
	 * 
	 */
	public function step3Action() {
		configureTheme(APPLICATION_THEME, 'install');

		$username = trim($_POST['username']);
		$password = $_POST['password'];
		$realname = $_POST['realname'];
		if(trim($password) == "" || trim($username) == "") {
		    $this->view->error = 1;
		} else {
		    $db = Zend_Registry::get("db");
		    $db->delete('users', array("username='$username'"));
		    $db->insert('users', array(
			'username' => $username,
			'password' => md5(Zend_Registry::get('staticSalt') . $password . sha1($password)),
			'password_salt' => sha1($password),
			'realname' => $realname
		    ));
		}
	}
}
