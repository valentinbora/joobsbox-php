<?php
/**
 * Search Model definition
 * 
 * Zend_Search_Lucene implementation for search
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @version  1.0
 * @link     http://docs.joobsbox.com/php 
 */

 /**
 * Search model class
 *
 * @category Joobsbox
 * @package  Joobsbox_Model
 * @author   Valentin Bora <contact@valentinbora.com>
 * @license  New BSD License http://www.joobsbox.com/joobsbox-php-license
 * @link     http://docs.joobsbox.com/php
 */

class Joobsbox_Model_Search extends Joobsbox_Plugin_EventsFilters
{
    public $index;
    public $enabled = true;
    private $_path;
    
    /**
     *  Initializes search model. If a Zend_Search_Lucene index is not found, one is created in /Joobsbox/SearchIndexes
     */
    public function __construct()
    {
        $this->_path = APPLICATION_DIRECTORY . "/Joobsbox/SearchIndexes/";
      
        if (file_exists($this->_path . "main")) {
            $this->index = Zend_Search_Lucene::open($this->_path . "main");
        } else {
            if (is_writable($this->_path)) {
                $this->index = Zend_Search_Lucene::create($this->_path . "main");
            } else {
                $this->enabled = false;
            }
        }
        if ($this->enabled) {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ()); 
            Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8'); 
        }
    }
    
    /**
     * Searches the index for a given query string
     *
     * @param string $queryString The string to be queried
     *
     * @return array Results as an associative array
     */ 
    public function search($queryString)
    {
        if (!$this->enabled) {
            return array();
        }
        $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
        return $this->index->find($query);
    }
    
    /**
     * Restricted search for a specific tag
     *
     * @param string $tag   The tag to be searched. Tags are related to database column names.
     * @param string $value The tag value to be searched for
     *
     * @return array Results as an associative array
     */
    public function searchTag($tag, $value)
    {
        if (!$this->enabled) {
            return array();
        }
        Zend_Search_Lucene::setDefaultSearchField($tag);
        $query = Zend_Search_Lucene_Search_QueryParser::parse($value);
        return $this->index->find($query);
    }
    
    /**
     * Delete a job from the index
     *
     * @param int $jobId The id for the job to be deleted from the index
     *
     * @return void
     */
    public function deleteJob($jobId)
    {
        if (!$this->enabled) {
            return false;
        }
        $term = new Zend_Search_Lucene_Index_Term($jobId, 'id');
        $hits  = $this->index->termDocs($term);
        if (count($hits)) {
            foreach ($hits as $hit) {
                $this->index->delete($hit->id);
            }
        }
        $this->fireEvent("job_deleted_from_searchindex", $jobId);
    }
    
    /**
     * Add a job to the search index
     *
     * @param array $jobData An associative array with job data, in the form it's inserted in the database
     * 
     * @return void
     */ 
    public function addJob($jobData)
    {
        if (!$this->enabled) {
            return false;
        }

        $jobData = $this->filter("add_job_to_searchindex", $jobData);

        // Delete old job with the same id from index
        $term = new Zend_Search_Lucene_Index_Term($jobData['id'], 'id');
        $hits  = $this->index->termDocs($term);
        if (count($hits)) {
            foreach ($hits as $hit) {
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
        $job->addField(Zend_Search_Lucene_Field::Keyword('category', $jobData['category'], 'utf-8'));
        $job->addField(Zend_Search_Lucene_Field::Text('location', $jobData['location'], 'utf-8'));

        $this->fireEvent("job_added_to_searchindex", $jobData);

        $this->index->addDocument($job);
        $this->index->commit();
    }
    
    /**
     * Resets the index by deleting all jobs
     *
     * @return void
     */ 
    public function resetIndex()
    {
        if (!$this->enabled) {
            return false;
        }
        for ($count = 0; $count < $this->index->count(); $count++) {
            $this->index->delete($count);
        }
        $this->commit();
    }
    
    /**
     * Commits changes to the index
     *
     * @return void
     */ 
    public function commit()
    {
        if (!$this->enabled) {
            return false;
        }
        $this->index->commit();
        $this->index->optimize();
    }
}
