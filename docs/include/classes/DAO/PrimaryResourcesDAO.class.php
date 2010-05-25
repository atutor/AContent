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
* DAO for "primary_resources" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class PrimaryResourcesDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   content_id, file_name, language_code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function Create($content_id, $file_name, $lang)
	{
		global $addslashes;
		
		$content_id = intval($content_id);
		$file_name = $addslashes($file_name);
		$lang = $addslashes($lang);

		$sql = "INSERT INTO ".TABLE_PREFIX."primary_resources 
		           SET content_id=$content_id, 
		               resource='$file_name', 
		               language_code='$lang'";
	    
		return $this->execute($sql);
	}
	
	/**
	* Delete rows by content_id
	* @access  public
	* @param   content_id
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	public function Delete($cid)
	{
		$pri_resource_ids = array();
		
		// Get all primary resources ID out that're associated with this content
		$rows = $this->getByContent($cid);
		
		if (is_array($rows)){
			foreach ($rows as $row) $pri_resource_ids[] = $row['primary_resource_id'];
		}
		
		if (!empty($pri_resource_ids)){
			$glued_pri_ids = implode(",", $pri_resource_ids);

			// Delete all secondary a4a
			$sql = 'DELETE c, d FROM '.TABLE_PREFIX.'secondary_resources c 
			     LEFT JOIN '.TABLE_PREFIX.'secondary_resources_types d 
			            ON c.secondary_resource_id=d.secondary_resource_id 
			         WHERE primary_resource_id IN ('.$glued_pri_ids.')';

			// If successful, remove all primary resources
			if ($this->execute($sql)){
				$sql = 'DELETE a, b FROM '.TABLE_PREFIX.'primary_resources a 
				     LEFT JOIN '.TABLE_PREFIX.'primary_resources_types b 
				            ON a.primary_resource_id=b.primary_resource_id 
				         WHERE content_id='.$cid;
				return $this->execute($sql);
			}
		}
		return true;
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
		$sql = "DELETE FROM ".TABLE_PREFIX."primary_resources
		         WHERE resource = '".$resourceName."'";
		return $this->execute($sql);
	}
	
	/**
	* Return rows by content_id
	* @access  public
	* @param   cid: content_id
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function getByContent($cid)
	{
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'primary_resources WHERE content_id='.$cid;
	    return $this->execute($sql);
	}
}
?>