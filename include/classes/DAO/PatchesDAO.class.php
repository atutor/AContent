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
 * DAO for "patches" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class PatchesDAO extends DAO {

	/**
	 * Create new patch
	 * @access  public
	 * @param   system_patch_id: atutor patch id, 
	 *          applied_version
	 *          patch_folder
	 *          description
	 *          available_to
	 *          sql_statement, 
	 *          status
	 *          remove_permission_files,
	 *          backup_files
	 *          patch_files
	 *          author
	 * @return  patch id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($system_patch_id, $applied_version, 
	                       $patch_folder, $description, 
	                       $available_to, $sql_statement, 
	                       $status, $remove_permission_files,
	                       $backup_files, $patch_files, $author)
	{
	    
		$sql = "INSERT INTO " . TABLE_PREFIX. "patches " .
					 "(system_patch_id, 
					   applied_version,
					   patch_folder,
					   description,
					   available_to,
					   sql_statement,
					   status,
					   remove_permission_files,
					   backup_files,
					   patch_files,
					   author,
					   installed_date)
					  VALUES (?,?,?,?,?,?,?,?,?,?,?, now() )";
			$values = array($system_patch_id, 
			            $applied_version, 
			            $patch_folder, 
			            $description,
			            $available_to,
			            $sql_statement,
			            $status,
			            $remove_permission_files,
			            $backup_files,
			            $patch_files,
			            $author);
			$types = "sssssssssss";
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
	* update table "patches" accroding to the fields/values in the given array
	* @access  public
	* @param   patchID, fieldArray
	* @author  Cindy Qi Li
	*/
	public function UpdateByArray($patchID, $fieldArray)
	{

		$sql_prefix = "UPDATE ". TABLE_PREFIX. "patches set ";
		
		foreach ($fieldArray as $key => $value)
		{
			$sql_middle .= " ".$key . "='" . $value . "', ";

		}
		
		$sql = substr($sql_prefix . $sql_middle, 0, -2) . 
		       " WHERE patches_id = ?";
		$values = $patchID;
		$types .= "i";
		return $this->execute($sql, $values, $types);
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

		$sql = "SELECT * from ".TABLE_PREFIX."patches where patches_id=?";
		$values = $patchID;
		$types = "i";
		$rows = $this->execute($sql, $values, $types);
		
		if (is_array($rows)) return $rows[0];
		else return false;
	}
	
	/**
	 * Return patch information by given version
	 * @access  public
	 * @param   version
	 * @return  patch row
	 * @author  Cindy Qi Li
	 */
	public function getPatchByVersion($version)
	{
		
		$sql = "SELECT * FROM ".TABLE_PREFIX."patches 
		         WHERE applied_version = ? 
		         ORDER BY system_patch_id";
		$values = $version;
		$types = "s";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Return user information by given web service ID
	 * @access  public
	 * @param   web service ID
	 * @return  user row
	 * @author  Cindy Qi Li
	 */
	public function getInstalledPatchByIDAndVersion($patchID, $version)
	{
		$sql = "SELECT * from ".TABLE_PREFIX."patches 
		       WHERE system_patch_id = ?
		       AND applied_version = ?
		       AND status like '%Installed'";
        $values = array($patchID, $version);
        $types = "is";
		return $this->execute($sql, $values, $types);
	}

}
?>
