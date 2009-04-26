<?php
class Twitter extends Plugin {
	public function init() {
		
	}
	
	function event_job_accepted($jobId) {
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
		curl_setopt($ch, CURLOPT_USERPWD, "joobsbox:silviusavin");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_exec($ch);
		curl_close($ch);
	}
}