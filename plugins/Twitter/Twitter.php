<?php
class Twitter extends Joobsbox_Plugin_Base {
  private $username, $password;

	public function init() {
	  $this->assignCredentials();
	  $this->view->credentialsOk = $this->checkCredentials();
    $this->form = new Zend_Form();
    $this->form->setAction($_SERVER['REQUEST_URI'])->setMethod('post');
    
    $username = $this->form->createElement('text', 'username')
      ->setLabel($this->view->translate("Username"))
      ->setValue($this->username)
      ->setRequired("true");
    $password = $this->form->createElement('text', 'password')
      ->setLabel($this->view->translate("Password"))
      ->setValue($this->password)
      ->setRequired("true");
     $submit = $this->form->createElement('submit', 'submit')
        ->setLabel($this->view->translate("Save"));
    
    $this->form
      ->addElement($username)
      ->addElement($password)
      ->addElement($submit);
    
    if ($this->request->isPost()) {
        $this->validateForm();
		    return;
    }

    $this->view->form = $this->form->render();
	}
	
	private function validateForm() {
	  if ($this->form->isValid($_POST)) {
	    $values = $this->form->getValues();
	    $this->username = $values['username'];
	    $this->password = $values['password'];
	    if($this->checkCredentials()) {
	      $this->addConfiguration("username", $this->username);
	      $this->addConfiguration("password", $this->password);
	    } else {
	      $this->alerts[] = $this->view->translate("The username/password combination you provided is not correct. Please check them.");
	    }
    }
    $this->view->form = $this->form->render();
	}
	
	private function assignCredentials() {
	  $this->username = $this->getConfiguration("username");
	  $this->password = $this->getConfiguration("password");
	}
	
	private function checkCredentials() {
	  $url = "http://twitter.com/statuses/user_timeline.json";
	  $ch = curl_init($url);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$x = Zend_Json::decode(curl_exec($ch));
		curl_close($ch);
    return !isset($x['error']);
	}
	
	function event_job_accepted($jobId) {
	  /******* CONFIGURATION **********/
	  $username = $this->getConfiguration("username");
	  $password = $this->getConfiguration("password");
	  /********************************/
	  
	  if(!(strlen($username) && strlen($password))) { // Don't even try to post it without username and pass
	    return;
	  }
	  
		$jobData = $this->getModel("Jobs")->fetchJobById($jobId);
		
		// Make a tiny URL
		$url = "http://omani.ac/api/shorten.xml";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "url=http://" . $_SERVER['SERVER_NAME'] . urlencode($this->baseUrl . '/job/' . $this->_helper->MakeLink($jobData['Title']) . "-" . $jobData['ID'] . '.html'));
		
		$xml = curl_exec($ch);
		curl_close($ch);
		
		$xml = new SimpleXMLElement($xml);
		if($xml->status == "OK") {
			$url = $xml->url[0];
		}
		
		// Now post to Twitter
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/update.json?status=" . urlencode($jobData['Title'] . ' @ ' . $jobData['Company'] . ' - ' . $jobData['Location'] . '   ' . $url));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_exec($ch);
		curl_close($ch);
	}
}