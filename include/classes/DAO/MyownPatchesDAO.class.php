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
 * DAO for "myown_patches" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class MyownPatchesDAO extends DAO {

	/**
	 * Create new row
	 * @access  public
	 * @param   system_patch_id, applied_versin, description, sql_statement
	 * @return  myown_patch_id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($system_patch_id, $applied_version, 
	                       $description, $sql_statement)
	{
		$sql = "INSERT INTO ".TABLE_PREFIX."myown_patches 
	               (system_patch_id, 
	                applied_version,
	                description,
	                sql_statement,
	                status,
	                last_modified)
		        VALUES (?, ?, ?,  ?, 'Created', now())";	
		$values = array($system_patch_id, $applied_version, $description, $sql_statement);	
		$types = "ssss";
		if (!$this->execute($sql, $values, $types))
		{
			$msg->addError('DB_NOT_UPDATED');
			return false;
		}
		else
		{
			return $this->ac_insert_id();
		}
	}

	/**
	 * Update a row
	 * @access  public
	 * @param   myown_patch_id, system_patch_id, applied_versin, description, sql_statement
	 * @return  true, if successful. Otherwise, false
	 * @author  Cindy Qi Li
	 */
	public function Update($myown_patch_id, $system_patch_id, $applied_version, 
	                       $description, $sql_statement)
	{

		$sql = "UPDATE ".TABLE_PREFIX."myown_patches 
		           SET system_patch_id = ?,
		               applied_version = ?,
		               description = ?,
		               sql_statement = ?,
		               status = 'Created',
		               last_modified = now()
		         WHERE myown_patch_id = ?";	
		$values = array($system_patch_id, 
		                        $applied_version, 
		                        $description, 
		                        $sql_statement,  
		                        $myown_patch_id);
		$types = "ssssi";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Update an existing myown_patches record
	 * @access  public
	 * @param   myownPatchID: myown_patches.myown_patch_id
	 *          fieldName: the name of the table field to update
	 *          fieldValue: the value to update
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function UpdateField($myownPatchID, $fieldName, $fieldValue)
	{
		// check if the required fields are filled
		if (($fieldName == 'system_patch_id' || $fieldName == 'applied_version') && $fieldValue == '')
			return array(_AT('TR_ERROR_EMPTY_FIELD'));

		$sql = "UPDATE ".TABLE_PREFIX."myown_patches 
		           SET ".$fieldName."= ?
		         WHERE myown_patch_id = ?";
	    $values = array($fieldValue,$myownPatchID);
	    $types = "si";	
		return $this->execute($sql, $values, $types);
	}
	
	/**
	 * Delete a patch
	 * @access  public
	 * @param   patchID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($patchID)
	{

		$sql = "DELETE FROM ".TABLE_PREFIX."myown_patches
		         WHERE myown_patch_id = ?";
		         
		$values = $patchID;
		$types = "i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Return all my own patches
	 * @access  public
	 * @param   none
	 * @return  all table rows
	 * @author  Cindy Qi Li
	 */
	public function getAll()
	{
		$sql = "SELECT * from ".TABLE_PREFIX."myown_patches m order by last_modified desc";
		
		return $this->execute($sql);
	}

	/**
	 * Return the patch info with the given patch id
	 * @access  public
	 * @param   $patchID
	 * @return  patch row
	 * @author  Cindy Qi Li
	 */
	public function getByID($patchID)
	{
		
		$sql = "SELECT * from ".TABLE_PREFIX."myown_patches where myown_patch_id=?";
		$values = $patchID;
		$types = "i";
		$rows = $this->execute($sql, $values, $types);
		
		if (is_array($rows)) return $rows[0];
		else return false;
	}

}
?>