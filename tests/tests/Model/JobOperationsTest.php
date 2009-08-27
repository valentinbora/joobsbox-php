<?php

class JobOperationsTest extends PHPUnit_Framework_TestCase
{
    private $jobs;

    public function setUp() {
      $this->jobs = new Joobsbox_Model_JobOperations;
    }
    
    public function testZendDbTableAttributes() {
      $this->assertObjectHasAttribute("_name", $this->jobs);
      $this->assertObjectHasAttribute("_primary", $this->jobs);
    }
}
