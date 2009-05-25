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

class Joobsbox_Model_Search {
	protected $_index;
	
	public function __construct() {
		if(file_exists("Joobsbox/SearchIndexes/main")) {
			$this->_index = Zend_Search_Lucene::open("Joobsbox/SearchIndexes/main");
		} else {
			$this->_index = Zend_Search_Lucene::create("Joobsbox/SearchIndexes/main");
		}
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ()); 
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8'); 
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
		$job->addField(Zend_Search_Lucene_Field::Keyword('DocumentType', 'job', 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Keyword('ID', $jobData['ID'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('Title', $jobData['Title'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('Description', $jobData['Description'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('Company', $jobData['Company'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Keyword('CategoryID', $jobData['CategoryID'], 'utf-8'));
		$job->addField(Zend_Search_Lucene_Field::Text('Location', $jobData['Location'], 'utf-8'));
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
