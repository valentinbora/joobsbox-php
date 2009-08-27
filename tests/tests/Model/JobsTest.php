<?php

class JobsTest extends PHPUnit_Framework_TestCase
{
    private $jobs;

    public function setUp() {
      $this->jobs = new Joobsbox_Model_Jobs;
    }
    
    public function testModelExists() {
      $this->assertNotNull($this->jobs, 'Where is the model?');
    }
    
    public function testFetchAllJobsWithImmediateReturnIsJoobsboxIteratorJobs()
    {
      $jobs = $this->jobs->fetchAllJobs();
      $this->assertType('Joobsbox_Iterator_Jobs', $jobs);
    }
    
    public function testFetchAllJobsWithImmediateReturnIsJoobsboxIteratorJobsFetchObject()
    {
      $jobs = $this->jobs->fetchAllJobs(0);
      $this->assertType('Joobsbox_Iterator_Jobs_FetchObject', $jobs);
    }
    
    public function testFetchJobByNonExistantId()
    {
      $job = $this->jobs->fetchJobById(-1);
      $this->assertFalse($job);
    }
    
    public function testFetchCategories() {
      $categories = $this->jobs->fetchCategories();
      $this->assertType('Joobsbox_Iterator_Categories', $categories);
    }
    
    public function testFetchNJobsPerCategory() {
      $data = $this->jobs->fetchNJobsPerCategory();
      $this->assertType('array', $data);
    }
}
