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

					if($category['name'] 				!= $currentCategories->getCategory($id)->getProperty("name")
						|| $index 	!= $currentCategories->getCategory($id)->getProperty("orderindex")) 
					{
						$orderIndex = $index;
						
						$categoryNameBackup = $category['name'];
						$category['name'] = preg_replace('%[^\w\.-\s]%', '', $category['name']);
						if($category['name'] != $categoryNameBackup) {
							$mustReload = true;
						}
						$data = array(
							"orderindex" => $orderIndex,
							"name"		 => $category['name'],
							"link"     => $this->view->MakeLink($category['name']),
							"parent"	 => 0
						);
						$where = $db->quoteInto('id = ?', $id);
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
					"name"		=> $category['name'],
					"orderindex"=> $orderIndex,
					"link"     => $this->view->MakeLink($category['name']),
					"parent"	=> 0
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
				$where = $db->quoteInto('id = ?', $categoryID);
				$db->update(Zend_Registry::get("conf")->dbtables->postings, array("categoryid" => 1), 'categoryid = ' . $categoryID);
				$categoryModel->delete($where);
			}
		}
		
		Joobsbox_Helpers_Cache::clearAllCache();
		
		echo json_encode(array("mustReload" => $mustReload));
		die();
	}
}
