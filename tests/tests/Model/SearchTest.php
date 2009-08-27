<?php

class SearchTest extends PHPUnit_Framework_TestCase
{
    private $model;

    public function setUp() {
      $this->model = new Joobsbox_Model_Search;
    }
    
    public function testIndexAttributeType() {
      $this->assertObjectHasAttribute("index", $this->model);
      $this->assertType('Zend_Search_Lucene_Proxy', $this->model->index);
    }
}
