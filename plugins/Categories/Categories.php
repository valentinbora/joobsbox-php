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
				$db->update(Zend_Registry::get("conf")->dbtables->postings, array("CategoryID" => 1), 'CategoryID = ' . $categoryID);
				$categoryModel->delete($where);
			}
		}
		
		echo json_encode(array("mustReload" => $mustReload));
		die();
	}
}
