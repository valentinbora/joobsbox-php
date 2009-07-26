<?php
class Google_Analytics extends Joobsbox_Plugin_AdminBase
{
  private $analyticsId, $form, $email, $password, $admin = false;
  
	function init() {
      $this->form = new Zend_Form();
      $this->form->setAction($_SERVER['REQUEST_URI'])->setMethod('post');
      
      $this->analyticsId = $this->getConfiguration("analyticsId");
      $this->email = $this->getConfiguration("email");
      $this->password = $this->getConfiguration("password");
      
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
