<?php
class Pinocchio {
	function filter_job_description($jobDescription) {
		$jobDescription .= "<span style=\"color: red;\">Salut. Uite si nasul meu. Semnat, Pinocchio Plugin.<br/>Facem plugin chaining hahahaaaaa.</span>\n";
		return array($jobDescription);
	}
}