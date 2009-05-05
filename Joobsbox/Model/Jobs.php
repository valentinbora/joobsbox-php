<?php
/**
 * Jobs Model definition
 * 
 * Provides postings and categories retrieval in different forms
 *
 * @author Valentin Bora <contact@valentinbora.com>
 * @version 1.0
 * @category Joobsbox
 * @package Joobsbox_Model
 */

/**
 * @category Joobsbox
 * @package Joobsbox_Model
 */
class Joobsbox_Model_Jobs extends Joobsbox_Plugin_EventsFilters {
	protected $_db;
	protected $_conf;
	
	protected $_postings_table;
	protected $_categories_table;
	const INCLUDE_NON_PUBLIC = true;
	
	/**
	 * Initializes the model
	 * 
	 * @returns void
	 */
	public function __construct() {
		$this->_db = Zend_Registry::get("db");
		$this->_db->setFetchMode(Zend_Db::FETCH_ASSOC);
		/**
		 * Set up the configuration parameters for database table names and prefix
		 */
		$this->_conf = Zend_Registry::get("conf");
		$this->_postings_table 		= $this->_conf->db->prefix . $this->_conf->dbtables->postings;
		$this->_categories_table 	= $this->_conf->db->prefix  . $this->_conf->dbtables->categories;
    }
	
	/**
	 * Retrieves a set of postings grouped by categories, with a maximum number of postings for each category
	 * 
	 * @param integer $returnImmediately if set to 1, it returns a jobIterator. Else it returns a jobFetchObject which enables further manipulation
	 * @param boolean $includeNonPublic if true, it includes non public jobs as well
	 * @returns jobFetchObject
	 */
    public function fetchAllJobs($returnImmediately=1, $includeNonPublic=false) {
		$select = $this->_db->select()->from($this->_postings_table);
		
		if(!$includeNonPublic) {
			$select->where('Public = 1');
		}
		
		$this->fireEvent("retrieve_jobs");
		
		if($returnImmediately) {
			$stmt	= $this->filter("all_jobs", $select->query()->fetchAll());
			return jobsIterator($stmt);
		}
		
		return new Joobsbox_Iterator_Jobs_FetchObject($select, $this->_db);
    }
	
	/**
	 * Retrieves a job array by given id
	 * 
	 * @param integer $id job id
	 * @returns array
	 */
	public function fetchJobById($id) {
		$sql 	= "SELECT * FROM " . $this->_postings_table . " WHERE ID = ?";
		return $this->_db->fetchRow($sql, $id);
	}
	
	/**
	 * Retrieves a set of categories
	 * 
	 * @returns categoriesIteratorObject
	 */
	public function fetchCategories() {
		$sql	= "
			SELECT 
				ID, 
				Name,
				OrderIndex,
				Parent,
				(SELECT COUNT(*) FROM " . $this->_postings_table . " WHERE CategoryID=" . $this->_categories_table . ".ID) nrPostings
			FROM 
				" . $this->_categories_table . "
			GROUP BY 
				ID
			ORDER BY
				OrderIndex";
		$categories = $this->_db->fetchAll($sql);
		
		return new Joobsbox_Iterator_Categories($categories);
	}
	
	/**
	 * Retrieves a set of postings grouped by categories, with a maximum number of postings for each category
	 * 
	 * @param integer $maxJobsPerCateg maximum number of postings for each category
	 * @returns array
	 */
	public function fetchNJobsPerCategory($maxJobsPerCateg=10) {
		$maxJobsPerCateg = $this->_conf->general->jobs_per_categ;
		$jobs 			 = $this->fetchAllJobs(0)->order("ID DESC")->fetch();
		$categoriesById  = $this->fetchCategories()->toArray();
		$cats 			 = $this->fetchCategories()->filterEmpty()->orderByOrderIndex()->toArray();
		
		$result 		 = array();
		
		if(count($cats))
		foreach($cats as $index => $category) {
			$categId = $category['ID'];
			
			while($categoriesById[$categId]['Parent'] != 0) {
				$categId = $categoriesById[$categId]['Parent'];
			}
			$result[$categoriesById[$categId]['Name']] = array();
		}
		
		foreach($jobs as $job) {
			$categId = $job['CategoryID'];
			
			while($cats[$categId]['Parent'] != 0) {
				$categId = $cats[$categId]['Parent'];
			}
			
			$categName = $cats[$categId]['Name'];
			if(!isset($result[$categName])) {
				$result[$cats[$job['CategoryID']]['Name']] = array();
			}
			if(count($result[$categName]) < $maxJobsPerCateg) {
				$result[$categName][] = $job;
			}
		}
		
		return $this->filter("njobs_per_categ", $result);
	}
}
