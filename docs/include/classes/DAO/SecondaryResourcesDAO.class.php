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
* DAO for "secondary_resources" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class SecondaryResourcesDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   primary_resource_id, file_name, language_code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function Create($primary_resource_id, $file_name, $lang)
	{
		global $addslashes;
		
		$primary_resource_id = intval($primary_resource_id);
		$file_name = $addslashes($file_name);
		$lang = $addslashes($lang);

		$sql = "INSERT INTO ".TABLE_PREFIX."secondary_resources 
		                SET primary_resource_id=$primary_resource_id, 
		                    secondary_resource='$file_name', 
		                    language_code='$lang'";
		return $this->execute($sql);
	}
	
	/**
	* Delete rows that primary or secondary resource name is the given $resourceName
	* @access  public
	* @param   $resourceName: primary or secondary resource name
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByResourceName($resourceName)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."secondary_resources
		         WHERE secondary_resource = '".$resourceName."'
		            OR primary_resource_id in (SELECT primary_resource_id
		                     FROM ".TABLE_PREFIX."primary_resources
		                    WHERE resource='".$resourceName."')";
		return $this->execute($sql);
	}
	
	/**
	* Return distinct rows by content_id
	* @access  public
	* @param   content_id
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function getByContent($content_id)
	{
		$sql = "SELECT DISTINCT secondary_resource_id, secondary_resource FROM ".TABLE_PREFIX."primary_resources a 
		          LEFT JOIN ".TABLE_PREFIX."secondary_resources s
					ON a.primary_resource_id = s.primary_resource_id 
				 WHERE content_id=".$content_id;
		return $this->execute($sql);
	}
	/**
	* Return rows by primary resource id
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function getByPrimaryResourceID($primary_resource_id)
	{
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'secondary_resources WHERE primary_resource_id='.$primary_resource_id;
	    return $this->execute($sql);
	}
}
?>