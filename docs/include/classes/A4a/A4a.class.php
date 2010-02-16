<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
 * Accessforall General class.
 * Based on the specification at: 
 *		http://www.imsglobal.org/accessibility/index.html
 *
 * @date	Oct 3rd, 2008
 * @author	Harris Wong
 */
class A4a {
	//variables
	var $cid = 0;						//content id
	var $resource_types = array();		//resource types hash mapping
	var $relative_path = '';			//relative path to the file 

	//Constructor
	function A4a($cid){
		$this->cid = intval($cid);
	}


	// Return resources type hash mapping.
	function getResourcesType($type_id=0){
		$type_id = intval($type_id);

		//if this is the first time calling this function, grab the list from db
		if (empty($resource_types)){
			include(TR_INCLUDE_PATH.'classes/DAO/ResourceTypesDAO.class.php');
			$resourceTypesDAO = new ResourceTypesDAO();
			$rows = $resourceTypesDAO->getAll();
			
			if (is_array($rows))
			{
				foreach ($rows as $row) $this->resource_types[$row['type_id']] = $row['type'];
			}
		}

		if (!empty($this->resource_types[$type_id])){
			return $this->resource_types[$type_id];		
		}
		return $this->resource_types;
	}

	
	// Get primary resources
	function getPrimaryResources(){
		$pri_resources = array(); // cid=>[resource, language code]

		include(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesDAO.class.php');
		$primaryResourcesDAO = new PrimaryResourcesDAO();
		$rows = $primaryResourcesDAO->getByContent($this->cid);
		
		if (is_array($rows)){
			foreach ($rows as $row) {
				$pri_resources[$row['primary_resource_id']]['resource'] = $row['resource'];
				if ($row['language_code'] != ''){
					$pri_resources[$row['primary_resource_id']]['language_code'] = $row['language_code'];
				}
			}
		}
		return $pri_resources;
	}


	// Get primary resources types
	function getPrimaryResourcesTypes($pri_resource_id=0){
		$pri_resource_id = intval($pri_resource_id);

		//quit if id not specified
		if ($pri_resource_id == 0) {
			return array();
		}

		$pri_resources_types = array();	// cid=>[type id]+
		
		include(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesTypesDAO.class.php');
		$primaryResourcesTypesDAO = new PrimaryResourcesTypesDAO();
		$rows = $primaryResourcesTypesDAO->getByResourceID($pri_resource_id);
		
		if (is_array($rows)){
			foreach ($rows as $row) {
				$pri_resources_types[$pri_resource_id][] = $row['type_id'];
			}
		}
		return $pri_resources_types;
	}


	// Get secondary resources 
	function getSecondaryResources($pri_resource_id=0){
		global $db;

		$pri_resource_id = intval($pri_resource_id);

		//quit if id not specified
		if ($pri_resource_id == 0) {
			return array();
		}

		$sec_resources = array(); // cid=>[resource, language code]
		include(TR_INCLUDE_PATH.'classes/DAO/SecondaryResourcesDAO.class.php');
		$secondaryResourcesDAO = new SecondaryResourcesDAO();
		$rows = $secondaryResourcesDAO->getByPrimaryResourceID($pri_resource_id);
		
		if (is_array($rows)){
			foreach ($rows as $row) {
				$sec_resources[$row['secondary_resource_id']]['resource'] = $row['secondary_resource'];
				if ($row['language_code'] != ''){
					$sec_resources[$row['secondary_resource_id']]['language_code'] = $row['language_code'];
				}
			}
		}
		return $sec_resources;
	}


	// Get secondary resources types
	function getSecondaryResourcesTypes($sec_resource_id=0){
		$sec_resource_id = intval($sec_resource_id);

		//quit if id not specified
		if ($sec_resource_id == 0) {
			return array();
		}

		$sec_resources_types = array();	// cid=>[type id]+
		include(TR_INCLUDE_PATH.'classes/DAO/SecondaryResourcesTypesDAO.class.php');
		$secondaryResourcesTypesDAO = new SecondaryResourcesTypesDAO();
		$rows = $secondaryResourcesTypesDAO->getByResourceID($sec_resource_id);
		
		if (is_array($rows)){
			foreach ($rows as $row) {
				$sec_resources_types[] = $row['type_id'];
			}
		}
		return $sec_resources_types;
	}


	// Insert primary resources into the db
	// @return primary resource id.
	function setPrimaryResource($content_id, $file_name, $lang){
		include(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesDAO.class.php');
		$primaryResourcesDAO = new PrimaryResourcesDAO();
		
		if ($primaryResourcesDAO->Create($content_id, $file_name, $lang)){
			return mysql_insert_id();
		}
		return false;
	}

	// Insert primary resource type
	function setPrimaryResourceType($primary_resource_id, $type_id){
		include(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesTypesDAO.class.php');
		$primaryResourcesTypesDAO = new PrimaryResourcesTypesDAO();
		$primaryResourcesTypesDAO->Create($primary_resource_id, $type_id);
	}

	// Insert secondary resource
	// @return secondary resource id
	function setSecondaryResource($primary_resource_id, $file_name, $lang){
		include(TR_INCLUDE_PATH.'classes/DAO/SecondaryResourcesDAO.class.php');
		$secondaryResourcesDAO = new SecondaryResourcesDAO();
		if ($secondaryResourcesDAO->Create($primary_resource_id, $file_name, $lang)){
			return mysql_insert_id();
		}
		return false;
	}

	// Insert secondary resource
	function setSecondaryResourceType($secondary_resource, $type_id){
		include(TR_INCLUDE_PATH.'classes/DAO/SecondaryResourcesTypesDAO.class.php');
		$secondaryResourcesTypesDAO = new SecondaryResourcesTypesDAO();
		$secondaryResourcesTypesDAO->Create($secondary_resource, $type_id);
	}

	
	// Set the relative path to all files
	function setRelativePath($path){
		$this->relative_path = $path . '/';
	}


	// Delete all materials associated with this content
	function deleteA4a(){
		include(TR_INCLUDE_PATH.'classes/DAO/PrimaryResourcesDAO.class.php');
		$primaryResourcesDAO = new PrimaryResourcesDAO();
		$rows = $primaryResourcesDAO->Delete($this->cid);
	}
}

?>