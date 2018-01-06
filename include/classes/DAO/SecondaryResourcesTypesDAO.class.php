<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* DAO for "secondary_resources_types" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class SecondaryResourcesTypesDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   secondary_resource, type_id
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function Create($secondary_resource, $type_id)
	{
		/*$secondary_resource = intval($secondary_resource);
		$type_id = intval($type_id);

		$sql = "INSERT INTO ".TABLE_PREFIX."secondary_resources_types 
		                SET secondary_resource_id=$secondary_resource, 
		                    type_id=$type_id";*/
		$sql = "INSERT INTO ".TABLE_PREFIX."secondary_resources_types 
		                SET secondary_resource_id=$secondary_resource, 
		                    type_id=$type_id";
		$values = array($secondary_resource, $type_id);
		$types = "ii";
		return $this->execute($sql, $values, $types);
	}
	
	/**
	* Delete rows that primary or secondary resource name is the given $resourceName
	* @access  public
	* @param   $resourceName: primary or secondary resource name
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	public function DeleteByResourceName($resourceName)
	{
		/*global $addslashes;
		$resourceName = $addslashes($resourceName);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types
		         WHERE secondary_resource_id in (SELECT secondary_resource_id 
		                      FROM ".TABLE_PREFIX."secondary_resources
		                     WHERE secondary_resource = '".$resourceName."'
		                        OR primary_resource_id in (SELECT primary_resource_id
		                                      FROM ".TABLE_PREFIX."primary_resources
		                                     WHERE resource='".$resourceName."'))";
		                                     */
		$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types
		         WHERE secondary_resource_id in (SELECT secondary_resource_id 
		                      FROM ".TABLE_PREFIX."secondary_resources
		                     WHERE secondary_resource = ?
		                        OR primary_resource_id in (SELECT primary_resource_id
		                                      FROM ".TABLE_PREFIX."primary_resources
		                                     WHERE resource=?))";
		$values = array($resourceName, $resourceName);
		$types = "ss";
		return $this->execute($sql, $values, $types);
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
		$resource_id = intval($resource_id);
		
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'secondary_resources_types WHERE secondary_resource_id=?';
	    $values = $resource_id;
	    $types = "i";
	    return $this->execute($sql, $values, $types);
	}
}
?>