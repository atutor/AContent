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

		$sql = "INSERT INTO ".TABLE_PREFIX."primary_resources 
		           SET content_id=?, 
		               resource=?, 
		               language_code=?";	   
		$filename = convertAmp($file_name);
		$values = array($content_id, $filename, $lang);
		$types = "iss";
		return $this->execute($sql, $values, $types);
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
		$cid = intval($cid);
		
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
				         WHERE content_id=?';
				$values = $cid;
				$types = "i";
				return $this->execute($sql, $values, $types);
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
		         WHERE resource = ?";
		$values = $resourceName;
		$types = "s";
		return $this->execute($sql, $values, $types);
	}
	
    /**
     * Delete rows by using resource id
     * @access  public
     * @param   $resourceID: primary resource ID
     * @return  true or false
     * @author  Harris Wong
     * @date    Oct 6, 2010
     */
    function DeleteByResourceID($resourceID){

        $sql = 'DELETE c, d FROM '.TABLE_PREFIX.'secondary_resources c LEFT JOIN '.TABLE_PREFIX."secondary_resources_types d ON c.secondary_resource_id=d.secondary_resource_id WHERE primary_resource_id=?";
        $values =$resourceID;
        $types = "i";
        $result = $this->execute($sql, $values, $types);
        
        // If successful, remove all primary resources
        if ($result){
            $sql = 'DELETE a, b FROM '.TABLE_PREFIX.'primary_resources a LEFT JOIN '.TABLE_PREFIX."primary_resources_types b ON a.primary_resource_id=b.primary_resource_id WHERE a.primary_resource_id=?";
        $values =$resourceID;
        $types = "i";
        $result = $this->execute($sql, $values, $types);
        }
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

	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'primary_resources WHERE content_id=? ORDER BY primary_resource_id';
	    $values = $cid;
	    $types = "i";
	    return $this->execute($sql, $values, $types);
	}

    /**
     * Return rows by primary resource name
     * @access  public
     * @param   $cid: the content id
     * @param   $lang: the language code. 
     * @param   $resourceName: primary resource name
     * @return  table rows
     * @author  Harris Wong
     * @date    Oct 6, 2010
     */
    public function getByResourceName($cid, $lang, $resource_name){
    
		$sql = "SELECT * FROM ".TABLE_PREFIX."primary_resources 
		        WHERE content_id=".$cid."
		          AND language_code = '".$lang."'
		          AND resource='".$resource_name."'";
		$values = array($cid, $lang, $resource_name);
		$types = "iss";
		return $this->execute($sql, $values, $types);
    }
}
?>