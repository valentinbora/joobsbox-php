<?php
class Categories extends Joobsbox_Plugin_AdminBase
{
	function init() {
		$this->jobsModel = $this->getModel("Jobs");
		$this->view->categories = $this->jobsModel->fetchCategories();
	}
	
	function dashboard() {
		
	}
	
	function saveConfigurationAction() {
		$categories    = array();
		$categoryModel = $this->getModel("CategoryOperations");
		$jobsModel     = $this->jobsModel;
		
		$currentCategories = $jobsModel->fetchCategories();

		$data = stripslashes($_POST['categories']);
		$categories = Zend_Json::decode($data);
		$categories = $categories;
		$mustReload = false;
		
		$foundCategories = array();
		
		$db = $categoryModel->getAdapter();
		
		$orderDirector = array();
		
		foreach($categories as $index => $category) {
		  $existing = explode("_", $category["existing"]);
		  
		  if(($existing[0] == "existing")) {
				$id = $existing[1];
				$foundCategories[] = $id;

				if($currentCategories->getCategory($id)) {
					////////////////////////////////////////////
					// CATEGORY EXISTS - REORDERED || RENAMED
					////////////////////////////////////////////

					if($category['name'] 				!= $currentCategories->getCategory($id)->getProperty("Name")
						|| $index 	!= $currentCategories->getCategory($id)->getProperty("OrderIndex")) 
					{
						$orderIndex = $index;
						
						$categoryNameBackup = $category['name'];
						$category['name'] = preg_replace('%[^\w\.-\s]%', '', $category['name']);
						if($category['name'] != $categoryNameBackup) {
							$mustReload = true;
						}
						$data = array(
							"OrderIndex" => $orderIndex,
							"Name"		 => $category['name'],
							"Link"     => $this->view->MakeLink($category['name']),
							"Parent"	 => 0
						);
						$where = $db->quoteInto('ID = ?', $id);
						$categoryModel->update($data, $where);
					}
				}
			} else {
				///////////////////
				// CREATE CATEGORY
				///////////////////
				
				$orderIndex = $index;
				$category['name'] = preg_replace('%[^\w\.-\s]%', '', $category['name']);
				$categoryModel->insert(array(
					"Name"		=> $category['name'],
					"OrderIndex"=> $orderIndex,
					"Link"     => $this->view->MakeLink($category['name']),
					"Parent"	=> 0
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
				$db->update(Zend_Registry::get("conf")->dbtables->postings, array("CategoryID" => 1), 'CategoryID = ' . $categoryID);
				$categoryModel->delete($where);
			}
		}
		
		Joobsbox_Helpers_Cache::clearAllCache();
		
		echo json_encode(array("mustReload" => $mustReload));
		die();
	}
}
