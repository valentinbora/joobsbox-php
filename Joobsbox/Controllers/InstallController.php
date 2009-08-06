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
	    Zend_Registry::get("PluginLoader")->disablePlugins();
	  
	    $params = $this->getRequest()->getParams();
	    
	    $config = new Zend_Config_Xml("config/config.xml", null, array(
			  'skipExtends'        => true,
        'allowModifications' => true)
      );
	    
	    if(isset($params['lang'])) {
	      $config->general->locale = $params['lang'];

        // Write the configuration file
        $writer = new Zend_Config_Writer_Xml(array(
          'config'   => $config,
          'filename' => 'config/config.xml')
        );
        $writer->write();
        $this->_redirect("install");
	    }
	    
      if(isset($config->general->restrict_install) && $config->general->restrict_install && file_exists("config/db.xml")) {
		      throw new Exception($this->view->translate("This JoobsBox is already installed. Manually remove the restrict_install line from config/config.xml if you want to reinstall it."));
	    }
	}
	
	public function indexAction(){
		$this->_redirect("install/step1");
	}
	
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
				$config = new Zend_Config_Xml("config/config.xml", null, array(
  			  'skipExtends'        => true,
          'allowModifications' => true)
        );

    		$config->general->common_title = $sitename;
				$config->db->prefix = $dbprefix;

        // Write the configuration file
        $writer = new Zend_Config_Writer_Xml(array(
          'config'   => $config,
          'filename' => 'config/config.xml')
        );
        
        try {
          $writer->write();
        } catch (Exception $e) {
          $this->view->dberror = $this->view->translate("config/config.xml is not writable. Please adjust the file permissions using FTP or SSH.");
        }
				
				  
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
				$configWriter = new Zend_Config_Writer_Xml();
				$configWriter->write('config/db.xml', $config);
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
		$session = new Zend_Session_Namespace('Install');

	  $config = new Zend_Config_Xml('config/config.xml');
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
		$db->delete($config->db->prefix . "categories", array("Name='Uncategorized'"));
		$db->insert($config->db->prefix . "categories", array(
		    'ID'    => 0,
		    'Name'  => 'Uncategorized',
		    'Link'  => 'Uncategorized',
		    'OrderIndex' => 100,
		    'Parent'=> 0
		));
		
		// Make the form
		$this->adminForm = new Zend_Form;
		$this->adminForm->setAction($this->view->baseUrl . "/install/step2")->setMethod('post')->setLegend('Administrator credentials');
	
	  $notEmpty = new Zend_Validate_NotEmpty();
		$realname = $this->adminForm->createElement('text', 'realname')
			->setLabel('Your name:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('HtmlEntities')
			->addValidator($notEmpty->setMessage($this->view->translate("Real name is mandatory")))
			->setRequired(true);
			
		$notEmpty = clone $notEmpty;
		$username = $this->adminForm->createElement('text', 'username')
			->setLabel('Username:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('HtmlEntities')
			->addValidator($notEmpty->setMessage($this->view->translate("Username is mandatory")))
			->setRequired(true);
			
		$notEmpty = clone $notEmpty;
		$password = $this->adminForm->createElement('text', 'password')
			->setLabel('Password:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('HtmlEntities')
			->addValidator($notEmpty->setMessage($this->view->translate("Password is mandatory")))
			->setRequired(true);
		
		$notEmpty = clone $notEmpty;
		$emailValidator    = new Zend_Validate_EmailAddress();
		$email = $this->adminForm->createElement('text', 'email')
			->setLabel('Email:')
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addFilter('HtmlEntities')
			->addValidator($notEmpty->setMessage($this->view->translate("Email is mandatory")))
			->addValidator($emailValidator->setMessage($this->view->translate("Email is invalid")))
			->setRequired(true);
			
		$submit = $this->adminForm->createElement('submit', 'Save')
			->setLabel('Save');
			
		$this->adminForm
		  ->addElement($realname)
		  ->addElement($username)
		  ->addElement($password)
		  ->addElement($email)
		  ->addElement($submit);
		
		if ($this->getRequest()->isPost()) {
        $this->validateAdminUser();
		    return;
    }
		$this->view->form = $this->adminForm->render();
	}
	
	public function validateAdminUser() {
	  $form = $this->adminForm;
		$values = $form->getValues();
    
    if ($form->isValid($_POST)) {
      $db = Zend_Registry::get("db");
      $values = $form->getValues();

      $username = $values['username'];
      $password = $values['password'];
      
      $config = new Zend_Config_Xml("config/config.xml");
      
	    $db->delete($config->db->prefix . 'users', array("username='$username'"));
	    $db->insert($config->db->prefix . 'users', array(
		    'username' => $values['username'],
    		'password' => md5(Zend_Registry::get('staticSalt') . $values['password'] . sha1($password)),
    		'password_salt' => sha1($values['password']),
    		'realname' => $values['realname'],
    		'email' => $values['email']
	    ));
	    
	    $config = new Zend_Config_Xml('config/config.xml', null, array('allowModifications' => true));
  		$config->general->restrict_install = 1;

      $writer = new Zend_Config_Writer_Xml(array('config' => $config, 'filename' => 'config/config.xml'));
      $writer->write();

  		$authAdapter = Zend_Registry::get("authAdapter");
  		$authAdapter->setIdentity($username)->setCredential($password);
  		$auth = Zend_Auth::getInstance();
  		$result = $auth->authenticate($authAdapter);
  		
  		$session = new Zend_Session_Namespace('AdminPanel');
  		$session->notices[] = $this->view->translate("Congratulations! Your JoobsBox is working now. Feel free to configure some categories.");
  		$this->_redirect("admin");
  	} else {
  		$values = $form->getValues();
  		$messages = $form->getMessages();
  		$form->populate($values);
  		$this->view->form = $form->render();
  	}
	}
}
