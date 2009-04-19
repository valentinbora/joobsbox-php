<?php
class Hello {
	function filter_salut($what) {
		return array(str_replace("a", "b", $what));
	}
	
	function filter_job_description($jobDescription) {
		$jobDescription .= "Vreau si eu sa ma bag in seama. Semnat, Hello Plugin.\n";
		$jobDescription = nl2br($jobDescription);
		return array($jobDescription);
	}
}