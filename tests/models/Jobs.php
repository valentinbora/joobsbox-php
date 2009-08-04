<?php
require_once 'PHPUnit/Framework.php';
require_once 'TestHelper.php';

class Jobs extends PHPUnit_Framework_TestCase
{
    public function testFetchAllJobs()
    {
        $jobs = new Joobsbox_Model_Jobs;
        $this->assertNotNull($jobs, 'Where is the model?');
    }
}
