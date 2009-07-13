<?php
class Hello extends Joobsbox_Plugin_Base {
  
	function filter_job_description($jobDescription) {
		$jobDescription = nl2br($jobDescription);
		return array($jobDescription);
	}

	function filter_job_description_admin($jobDescription) {
		$jobDescription = nl2br($jobDescription);
		return array($jobDescription);
	}
}