<?php
$testing = true;

require_once '../index.php';
 
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function testIndexAction()
    {
		require "../core/bootstrap.php";
		
		$this->dispatch('/');
		$this->assertController('index');
		$this->assertAction('index');
		$this->assertResponseCode(200);
    } 
}
