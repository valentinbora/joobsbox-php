<?php
require "../core/test.php";
$testing = true;

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function testIndexAction()
    {
      	  dt("Here");
		$this->dispatch("/index/index");
		$this->assertController("index");
		$this->assertAction("index");	
    } 
}
