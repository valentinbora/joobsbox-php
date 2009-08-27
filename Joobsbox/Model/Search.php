<?php
/**
 * Search Model definition
 * 
 * Zend_Search_Lucene implementation for search
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Model
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

 /**
 * @category Joobsbox
 * @package Joobsbox_Model
 * @copyright  Copyright (c) 2009 Joobsbox. (http://www.joobsbox.com)
 * @license	   http://www.joobsbox.com/joobsbox-php-license
 */

class Joobsbox_Model_Search extends Joobsbox_Plugin_EventsFilters {
	public $index;
	public $_enabled = true;
	private $_path;
	
	public function __construct() {
	  $this->_path = APPLICATION_DIRECTORY . "/Joobsbox/SearchIndexes/";
	  
		if(file_exists($this->_path . "main")) {
			$this->index = Zend_Search_Lucene::open($this->_path . "main");
		} else {
		  if(is_writable($this->_path)) {
			  $this->index = Zend_Search_Lucene::create($this->_path . "main");
			} else {
			  $this->_enabled = false;
			}
		}
		if($this->_enabled) {
		  Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ()); 
		  Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8'); 
		}
	}
	
	public function search($string) {
	  if(!$this->_enabled) return array();
		$query = Zend_Search_Lucene_Search_QueryParser::parse($string);
		return $this->index->find($query);
	}
	
	public function searchTag($tag, $value) {
	  if(!$this->_enabled) return array();
	  Zend_Search_Lucene::setDefaultSearchField($tag);
	  $query = Zend_Search_Lucene_Search_QueryParser::parse($value);
		return $this->index->find($query);
	}
	
	public function deleteJob($jobId) {
	  if(!$this->_enabled) return false();
		$term = new Zend_Search_Lucene_Index_Term($jobId, 'id');
		$hits  = $this->index->termDocs($term);
		if(count($hits)) {
			foreach($hits as $hit) {
				$this->index->delete($hit->id);
			}
		}
		$this->fireEvent("job_deleted_from_searchindex", $jobId);
	}
	
	public function addJob($jobData) {
	  if(!$this->_enabled) return false;

	  $jobData = $this->filter("add_job_to_searchindex", $jobData);

		// Delete old job with the same id from index
		$term = new Zend_Search_Lucene_Index_Term($jobData['id'], 'id');
		$hits  = $this->index->termDocs($term);
		if(count($hits)) {
			foreach($hits as $hit) {
				$this->index->delete($hit->id);
			}
		}
		
		// Add the job now
		$job = new Zend_Search_Lucene_Document();
		$job->addField(Zend_Search_Lucene_Field::Keyword('DocumentType', 'job', 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Keyword('id', $jobData['id'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('title', $jobData['title'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('description', $jobData['description'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('company', $jobData['company'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Keyword('categoryid', $jobData['categoryid'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('location', $jobData['location'], 'utf-8'));
		
		$this->fireEvent("job_added_to_searchindex", $jobData);
		
		$this->index->addDocument($job);
		$this->index->commit();
	}
	
	public function resetIndex() {
	  if(!$this->_enabled) return false();
		for ($count = 0; $count < $this->index->count(); $count++) {
        $this->index->delete($count);
    }
		$this->commit();
	}
	
	public function commit() {
	  if(!$this->_enabled) return false();
		$this->index->commit();
		$this->index->optimize();
	}
}
