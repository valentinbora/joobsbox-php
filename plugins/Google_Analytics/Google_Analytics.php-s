<?php
class Google_Analytics extends Joobsbox_Plugin_AdminBase
{
  private $analyticsId, $form, $email, $password, $profileId, $admin = false, $lastQuery;
  
  public function dashboard() {
    if(!$this->ajax) return;
    
    $lastQuery = $this->lastQuery;
    if(!strlen($lastQuery)) {
      $lastQuery = array('lastQueryTime' => 0);
    } else {
      $lastQuery = unserialize($lastQuery);
    }
    
    if(time() - $lastQuery['lastQueryTime'] > 3 * 60 * 60) {
      require_once "lib/gapi-1.3/gapi.class.php";
      $ga = new gapi($this->email, $this->password);
      $ga->requestReportData($this->profileId, array('month'), array('pageviews','visits'), "", "", date("Y-m-d", strtotime("-30 days")), date("Y-m-d"));
      $results = $ga->getResults();
    
      $data = array(
        'visits'    => 0,
        'pageviews' => 0
      );
      
      foreach($results as $result) {
        $data['visits'] += $result->getVisits();
        $data['pageviews'] += $result->getPageviews();
      }
      
      $this->addConfiguration("lastQuery", serialize(array("lastQueryTime" => time(), "data" => $data)));
    } else {
      $data = $lastQuery['data'];
    }
    
    $this->view->data = $data;
  }
  
  private function assignCredentials() {
    $this->analyticsId = $this->getConfiguration("analyticsId");
    $this->email = $this->getConfiguration("email");
    $this->password = $this->getConfiguration("password");
    $this->profileId = $this->getConfiguration("profileId");
    $this->lastQuery = $this->getConfiguration("lastQuery");
  }
  
	function init() {
	    $this->view->tops = array();
	    $this->view->data = array();
	  
	    $this->assignCredentials();
	  
	    if(isset($_POST['ajax'])) {
	      $this->view->ajax = true;
	      $this->ajax = true;
	      $this->dashboard();
	    }
  }

  public function indexAction() {
	    /********* Display metrics *********/
	    $lastQuery = $this->lastQuery;
      if(!strlen($lastQuery)) {
        $lastQuery = array('lastQueryTime' => 0);
      } else {
        $lastQuery = unserialize($lastQuery);
      }
      
	    if(time() - $lastQuery['lastQueryTime'] > 3 * 60 * 60 && strlen($this->email)) {
        require_once "lib/gapi-1.3/gapi.class.php";
        $this->assignCredentials();
        $ga = new gapi($this->email, $this->password);
        $ga->requestReportData($this->profileId, array('month'), array('pageviews','visits'), "", "", date("Y-m-d", strtotime("-30 days")), date("Y-m-d"));
        $results = $ga->getResults();

        $data = array(
          'visits'    => 0,
          'pageviews' => 0
        );

        foreach($results as $result) {
          $data['visits'] += $result->getVisits();
          $data['pageviews'] += $result->getPageviews();
        }
        
        $lastQuery['lastQueryTime'] = time();
        
        $this->addConfiguration("lastQuery", serialize(array("lastQueryTime" => time(), "data" => $data)));
        
        $ga->requestReportData($this->profileId, array('pagePath', 'pageTitle'), array('pageviews'), array('-pageviews'), "", date("Y-m-d", strtotime("-30 days")), date("Y-m-d"));
        $results = $ga->getResults();
        $tops = array();
        foreach($results as $result) {
          $tops[] = array(
            "pageViews"   => $result->getPageviews(),
            "pagePath"    => $result->getPagepath(),
            "pageTitle"   => $result->getPagetitle()
          );
        }
        
        file_put_contents($this->dirPath . "export.txt", serialize(array("countData" => $data, "tops" => $tops)));
      } else {
        if(file_exists($this->dirPath . "export.txt")) {
          $file = unserialize(file_get_contents($this->dirPath . "export.txt"));
          $data = $file['countData'];
          $tops = $file['tops'];
        }
      }
      
      if(isset($tops)) {
        $this->view->tops = $tops;
        $this->view->data = $data;
        $this->view->lastQuery = date("r", $lastQuery['lastQueryTime']);
      }
	  
      $this->form = new Zend_Form();
      $this->form->setAction($_SERVER['REQUEST_URI'])->setMethod('post');
      
      $analyticsId = $this->form->createElement('text', 'analyticsId')
        ->setLabel($this->view->translate("Web Property ID"))
        ->setValue($this->analyticsId);
      
      $this->form->addElement($analyticsId);
      
      $this->form->addDisplayGroup(array('analyticsId'), "Tracking", array(
         'legend' => ucfirst($this->view->translate("Tracking"))
      ));
      
      $email = $this->form->createElement('text', 'email')
        ->setLabel($this->view->translate("Google Analytics Email"))
        ->setValue($this->email);
      
      $password = $this->form->createElement('password', 'password')
        ->setLabel($this->view->translate("Google Analytics Password"));
        
      $this->form->addElement($email)->addElement($password);

      $this->form->addDisplayGroup(array('email', 'password'), "Reports", array(
         'legend' => ucfirst($this->view->translate("Reports"))
      ));

       $submit = $this->form->createElement('submit', 'submit')
          ->setLabel($this->view->translate("Save"));
      
       $this->form->addElement($submit);

      if ($this->request->isPost()) {
          $this->validateForm();
  		    return;
      }

      $this->view->form = $this->form->render();
	    
	  	if(strlen($this->email) && strlen($this->password) && strlen($this->analyticsId)) {
	  	  if(strlen($this->profileId = $this->getConfiguration("profileId")) == 0) {
	  	    $this->changeProfileId($this->analyticsId);
	  	  }
	  	    
	  	  
  	  }
  }
  
  private function changeProfileId($webPropertyId) {
    require "lib/gapi-1.3/gapi.class.php";
	  
    $ga = new gapi($this->email, $this->password);
    $ga->requestAccountData();

    foreach($ga->getResults() as $result) {
      $properties = $result->getProperties();
      
      if($properties['webPropertyId'] == $webPropertyId) {
        $this->addConfiguration("profileId", $properties['profileId']);
        return;
      }
    }
  }
  
  private function validateForm() {
    if(isset($_POST['ajax'])) return;
    
    if ($this->form->isValid($_POST)) {
	    $values = $this->form->getValues();
	    if(isset($values['analyticsId'])) {
        $this->addConfiguration("analyticsId",trim($values['analyticsId']));
      } else {
        $this->deleteConfigurationByName('analyticsId');
      }
      
      if(isset($values['email']) && isset($values['password'])) {
        $this->email = trim($values['email']);
	      $this->password = trim($values['password']);
        $this->addConfiguration("email", $this->email);
        $this->addConfiguration("password", $this->password);
        if(strlen(trim($values['analyticsId']))) {
          $this->changeProfileId(trim($values['analyticsId']));
        }
      } else {
        $this->deleteConfigurationByName('email');
        $this->deleteConfigurationByName('password');
      }

      $this->view->form = $this->form->render();
    }
  }
  
  function event_admin_panel_init() {
    $this->admin = true;
  }
  
  function event_post_dispatch() {
    if($this->admin) return 0;
    $this->analyticsId = $this->getConfiguration("analyticsId");
    
    if(strlen($this->analyticsId) == 0) return;
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
    $viewRenderer->view->headScript()->appendScript('
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
    var pageTracker = _gat._getTracker("' . $this->analyticsId . '");
    pageTracker._trackPageview();
    } catch(err) {}');
  }
}
