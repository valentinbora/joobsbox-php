<?php
/**
 * Search Model definition
 * 
 * Zend_Search_Lucene implementation for search
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Models
 */

 /**
 * @category Joobsbox
 * @package Joobsbox_Models
 */

require_once("jobsIterator.php");
require_once("jobFetchObject.php");
require_once("EventsFilters.php");

class Model_Search extends EventsFilters {
	protected $_index;
	
    public function __construct() {
		if(file_exists("application/SearchIndexes/main")) {
			$this->_index = Zend_Search_Lucene::open("application/SearchIndexes/main");
		} else {
			$this->_index = Zend_Search_Lucene::create("application/SearchIndexes/main");
		}
    }
	
	public function search($string) {
		$query = Zend_Search_Lucene_Search_QueryParser::parse($string);
		return $this->_index->find($query);
	}
	
	public function deleteJob($jobId) {
		$term = new Zend_Search_Lucene_Index_Term($jobId, 'ID');
		$hits  = $this->_index->termDocs($term);
		if(count($hits)) {
			foreach($hits as $hit) {
				$this->_index->delete($hit->id);
			}
		}
	}
	
	public function addJob($jobData) {
		// Delete old job with the same id from index
		$term = new Zend_Search_Lucene_Index_Term($jobData['ID'], 'ID');
		$hits  = $this->_index->termDocs($term);
		if(count($hits)) {
			foreach($hits as $hit) {
				$this->_index->delete($hit->id);
			}
		}
		
		// Add the job now
		$job = new Zend_Search_Lucene_Document();
		$job->addField(Zend_Search_Lucene_Field::Keyword('DocumentType', 'job'));
		$job->addField(Zend_Search_Lucene_Field::Keyword('ID', $jobData['ID']));
		$job->addField(Zend_Search_Lucene_Field::Text('Title', $jobData['Title']));
		$job->addField(Zend_Search_Lucene_Field::Text('Description', $jobData['Description']));
		$job->addField(Zend_Search_Lucene_Field::Text('Company', $jobData['Company']));
		$job->addField(Zend_Search_Lucene_Field::Keyword('CategoryID', $jobData['CategoryID']));
		$job->addField(Zend_Search_Lucene_Field::Text('Location', $jobData['Location']));
		$this->_index->addDocument($job);
	}
	
	public function resetIndex() {
		$hits  = $this->search("*");
		if(count($hits)) {
			foreach($hits as $hit) {
				$this->_index->delete($hit->id);
			}
		}
		$this->commit();
	}
	
	public function commit() {
		$this->_index->commit();
		$this->_index->optimize();
	}
}