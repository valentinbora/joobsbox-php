<?php

class CategoryOperationsTest extends PHPUnit_Framework_TestCase
{
    private $jobs;

    public function setUp() {
      $this->jobs = new Joobsbox_Model_CategoryOperations;
    }
    
    public function testZendDbTableAttributes() {
      $this->assertObjectHasAttribute("_name", $this->jobs);
      $this->assertObjectHasAttribute("_primary", $this->jobs);
    }
}
