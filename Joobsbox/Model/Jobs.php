<?php
/**
 * Jobs Model definition
 * 
 * Provides postings and categories retrieval in different forms
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @version  1.0
 * @link     http://docs.joobsbox.com/php
 */

/**
 * Jobs model class definition
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com> 
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 */
class Joobsbox_Model_Jobs extends Joobsbox_Plugin_EventsFilters
{
    protected $db, $conf, $postings_table, $categories_table;
    const INCLUDE_NON_PUBLIC = true;
  
    /**
    * Initializes the model
    * 
    * @return void
    */
    public function __construct() 
    {
        $this->db = Zend_Registry::get("db");
        $this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
        /**
         * Set up the configuration parameters for database table names and prefix
         */
        $this->conf = Zend_Registry::get("conf");
        $this->postings_table    = $this->conf->db->prefix . $this->conf->dbtables->postings;
        $this->categories_table  = $this->conf->db->prefix  . $this->conf->dbtables->categories;
    }
  
    /**
    * Retrieves a set of postings grouped by categories, with a maximum number of postings for each category
    * 
    * @param integer $returnImmediately if set to 1, it returns a jobIterator. Else it returns a jobFetchObject which enables further manipulation
    * @param boolean $includeNonPublic  if true, it includes non public jobs as well
    * @param boolean $checkExpiration   if true, checks if jobs have expired
    * 
    * @return Joobsbox_Iterator_Jobs|Joobsbox_Iterator_Jobs_FetchObject
    */
    public function fetchAllJobs($returnImmediately=1, $includeNonPublic=false, $checkExpiration = true)
    {
        $select = $this->db->select()->from($this->postings_table);

        if (!$includeNonPublic) {
            $select->where('public = 1');
        }

        if ($checkExpiration) {
            $select->where('expirationdate >= ?', time());
        }

        $this->fireEvent("retrieve_jobs");

        if ($returnImmediately) {
            $stmt = $this->filter("all_jobs", $select->query()->fetchAll());
            return new Joobsbox_Iterator_Jobs($stmt);
        }

        return new Joobsbox_Iterator_Jobs_FetchObject($select, $this->db);
    }
  
    /**
    * Retrieves a job array by given id
    * 
    * @param integer $id job id
    * 
    * @return array
    */
    public function fetchJobById($id) 
    {
        $sql  = "SELECT * FROM " . $this->postings_table . " WHERE id = ?";
        return $this->db->fetchRow($sql, $id);
    }
  
    /**
    * Retrieves a set of categories
    * 
    * @return categoriesIteratorObject
    */
    public function fetchCategories() 
    {
        $sql  = "
          SELECT 
            id, 
            name,
            link,
            orderindex,
            (SELECT COUNT(*) FROM " . $this->postings_table . " WHERE categoryid=" . $this->categories_table . ".id) nrPostings
          FROM 
            " . $this->categories_table . "
          GROUP BY 
            id
          ORDER BY
            orderindex";
        $categories = $this->db->fetchAll($sql);

        return new Joobsbox_Iterator_Categories($categories);
    }
  
    /**
    * Retrieves a set of postings grouped by categories, with a maximum number of postings for each category
    * 
    * @param integer $maxJobsPerCateg maximum number of postings for each category
    * 
    * @return array
    */
    public function fetchNJobsPerCategory($maxJobsPerCateg=10) 
    {
        $maxJobsPerCateg    = $this->conf->general->jobs_per_categ;
        $jobs               = $this->fetchAllJobs(0)->order("id DESC")->fetch();
        $categoriesById     = $this->fetchCategories()->toArray();
        $cats               = $this->fetchCategories()->filterEmpty()->orderByOrderIndex()->toArray();

        $result             = array();

        if (count($cats)) {
            foreach ($cats as $index => $category) {
                $categId = $category['id'];
  
                // while ($categoriesById[$categId]['parent'] != 0) {
                //     $categId = $categoriesById[$categId]['parent'];
                // }
                
                $result[$categoriesById[$categId]['name']] = array();
            }
        }
    
        foreach ($jobs as $job) {
            $categId = $job['categoryid'];

            /*while ($cats[$categId]['parent'] != 0) {
                $categId = $cats[$categId]['parent'];
            }*/

            if(isset($cats[$categId])) {
                $categName = $cats[$categId]['name'];
                if (!isset($result[$categName])) {
                    $result[$cats[$job['categoryid']]['name']] = array();
                }
                if (count($result[$categName]) < $maxJobsPerCateg) {
                    $result[$categName][] = $job;
                }
            }
        }

        return $this->filter("njobs_per_categ", $result);
    }
}
