<?php
/************************************************************************/
/* AContent                                                         */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* DAO for "primary_resources_types" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class PrimaryResourcesTypesDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   primary_resource_id, type_id
	* @return  true, if successful; false, otherwise
	* @author  Cindy Qi Li
	*/
	public function Create($primary_resource_id, $type_id)
	{
		$primary_resource_id= intval($primary_resource_id);
		$type_id = intval($type_id);

		$sql = "INSERT INTO ".TABLE_PREFIX."primary_resources_types 
		                SET primary_resource_id=$primary_resource_id, 
		                    type_id=$type_id";
		return $this->execute($sql);
	}
	
	/**
	* Delete rows that primary resource name is the given $resourceName
	* @access  public
	* @param   $resourceName: primary resource name
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByResourceName($resourceName)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."primary_resources_types
		         WHERE primary_resource_id in (SELECT primary_resource_id 
		                      FROM ".TABLE_PREFIX."primary_resources
		                     WHERE resource = '".$resourceName."')";
		return $this->execute($sql);
	}
	
	/**
	* Return a config row by content_id
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function getByResourceID($resource_id)
	{
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'primary_resources_types WHERE primary_resource_id='.$resource_id;
	    return $this->execute($sql);
	}
}
?>