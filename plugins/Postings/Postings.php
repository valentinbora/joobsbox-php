<?php
class Postings extends Joobsbox_Plugin_AdminBase
{
	function init() {
		$this->jobsModel = $this->getModel("Jobs");
		$this->view->categories = $this->jobsModel->fetchCategories();
	}
	
	public function dashboard() {
	
	}
	
	function indexAction() {
		if(isset($_POST['action']) && method_exists($this, $_POST['action'] . 'Action')) {
			$method = $_POST['action'] . 'Action';
			$this->$method();
		}
		$pending = $this->jobsModel->fetchAllJobs(0, Joobsbox_Model_Jobs::INCLUDE_NON_PUBLIC)->where("Public = ?", 0)->order('ID DESC')->fetch()->toArray();
		
		$this->view->pending  = $pending;
		$this->view->postings = $this->jobsModel->fetchAllJobs(0)->order('ID DESC')->fetch()->toArray();
	}
	
	private function deleteAction() {
		$this->jobOperationsModel = $this->getModel("JobOperations");
		
		foreach($_POST['job'] as $job => $a) {
			$job = (int)$job;
			$this->jobOperationsModel->delete($this->jobOperationsModel->getAdapter()->quoteInto('ID = ?', $job));
		}
		echo "ok";
		die();
	}
	
	private function acceptAction() {
		$this->jobOperationsModel = $this->getModel("JobOperations");
		
		foreach($_POST['job'] as $job => $a) {
			$job = (int)$job;
			Zend_Registry::get("EventHelper")->fireEvent("job_accepted", $job);
			$this->jobOperationsModel->update(array('Public' => 1, 'ChangedDate' => new Zend_Db_Expr('NOW()')), $this->jobOperationsModel->getAdapter()->quoteInto('ID = ?', $job));
		}
		echo "ok";
		die();
	}
	
	function modifyAction() {
		$categories    = array();
		$categoryModel = $this->getModel("CategoryOperations");
		$jobsModel     = $this->jobsModel;
		
		$currentCategories = $jobsModel->fetchCategories();
		
		$data = stripslashes($_POST['data']);
		$categories = Zend_Json::decode($data);
		$categories = $categories['categories'];
		$mustReload = false;
		
		$foundCategories = array();
		
		$db = $categoryModel->getAdapter();
		
		$orderDirector = array();
		
		foreach($categories as $category) {
			$parentId = str_replace("node_", "", $category['parentId']);

			if(!isset($orderDirectory[$parentId])) {
				$orderDirectory[$parentId] = 0;
			}
			$orderDirectory[$parentId]++;
			
			if(strlen($category['id'])) {
				$id = $category['id'];
				$id = str_replace("node_", "", $id);
				$foundCategories[] = $id;

				if($currentCategories->getCategory($id)) {
					////////////////////////////////////////////
					// CATEGORY EXISTS - REORDERED || RENAMED
					////////////////////////////////////////////

					if($category['name'] 				!= $currentCategories->getCategory($id)->getProperty("Name")
						|| $orderDirectory[$parentId] 	!= $currentCategories->getCategory($id)->getProperty("OrderIndex")
						|| $parentId					!= $currentCategories->getCategory($id)->getProperty("Parent")) 
					{
						$orderIndex = $orderDirectory[$parentId];
						
						$categoryNameBackup = $category['name'];
						$category['name'] = preg_replace('%[^\w\.-\s]%', '', $category['name']);
						if($category['name'] != $categoryNameBackup) {
							$mustReload = true;
						}
						$data = array(
							"OrderIndex" => $orderIndex,
							"Name"		 => $category['name'],
							"Parent"	 => $parentId
						);
						$where = $db->quoteInto('ID = ?', $id);
						$categoryModel->update($data, $where);
					}
				}
			} else {
				///////////////////
				// CREATE CATEGORY
				///////////////////
				
				$orderIndex = $orderDirectory[$parentId];
				$category['name'] = preg_replace('%[^\w\.-\s]%', '', $category['name']);
				$categoryModel->insert(array(
					"Name"		=> $category['name'],
					"OrderIndex"=> $orderIndex,
					"Parent"	=> $parentId
				));
				$mustReload = true;
			}
		}
		
		/////////////////////
		// DELETE CATEGORIES
		/////////////////////
		$foundCategories[] = 1; // Uncategorized category
		$mustDelete = array_diff(array_keys($currentCategories->toArray()), $foundCategories);
		
		foreach($mustDelete as $categoryID) {
			if($categoryID > 1) {
				$where = $db->quoteInto('ID = ?', $categoryID);
				$db->update($jobsModel->jobs_table_name, array("CategoryID" => 1), 'CategoryID = ' . $categoryID);

				$categoryModel->delete($where);
			}
		}
		
		echo json_encode(array("mustReload" => $mustReload));
		die();
	}
}