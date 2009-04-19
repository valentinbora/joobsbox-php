<?php
class Twitter extends Plugin {
	
	function event_job_accepted($jobData) {
		// Make a tiny URL
		$url = "http://omani.ac/api/shorten.xml";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "url=http://" . $_SERVER['SERVER_NAME'] . urlencode($this->baseUrl . '/job/' . $this->_helper->MakeLink($jobData['title']) . "-" . $jobData['id'] . '.html'));
		
		$xml = curl_exec($ch);
		curl_close($ch);
		
		$xml = new SimpleXMLElement($xml);
		if($xml->status == "OK") {
			$url = $xml->url[0];
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/update.json?status=" . urlencode($jobData['title'] . ' at ' . $jobData['company'] . ' in ' . $jobData['location']));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERPWD, "joobsbox:silviusavin");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/update.json?status=" . urlencode($jobData['title'] . ' @ ' . $jobData['company'] . ' - ' . $jobData['location'] . '   ' . $url));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERPWD, "joobsbox:silviusavin");
		
		// Tiny URL
		

		// grab URL and pass it to the browser
		curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);
	}
}