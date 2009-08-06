<?php

class JobsTest extends PHPUnit_Framework_TestCase
{
    private $jobs;

    public function testFetchAllJobs()
    {
        $this->jobs = new Joobsbox_Model_Jobs;
        $this->assertNotNull($this->jobs, 'Where is the model?');
    }
}
