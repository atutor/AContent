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
 * DAO for "user_groups" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class UserGroupPrivilegeDAO extends DAO {

	/**
	 * Create
	 * @access  public
	 * @param   userGroupID
	 *          privilegeID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($userGroupID, $privilegeID)
	{
		$sql = "INSERT INTO ".TABLE_PREFIX."user_group_privilege
		              (user_group_id,
		               privilege_id
		               )
		       VALUES (?,?)";
	    $values = array($userGroupID, $privilegeID);
	    $types = "ii";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Update an existing user group privilege record
	 * @access  public
	 * @param   userGroupID: user group ID
	 *          privilegeID: privilege ID
	 *          fieldName: the name of the table field to update
	 *          fieldValue: the value to update
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function UpdateField($userGroupID, $privilegeID, $fieldName, $fieldValue)
	{
		$sql = "UPDATE ".TABLE_PREFIX."user_group_privilege
		           SET ".$fieldName."='".$fieldValue."'
		         WHERE user_group_id = ?
		           AND privilege_id = ?";
		$values = array($userGroupID, $privilegeID);
		$types = "ii";
		return $this->execute($sql, $values, $types);
	}
	
	/**
	 * Delete a row
	 * @access  public
	 * @param   userGroupID
	 *          privilegeID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($userGroupID, $privilegeID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."user_group_privilege
		         WHERE user_group_id = ?
		           AND privilege_id = ?";
	    $values = array($userGroupID, $privilegeID);
	    $types = "ii";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Update an existing user group
	 * @access  public
	 * @param   userGroupID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function DeleteByUserGroupID($userGroupID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."user_group_privilege
		         WHERE user_group_id = ?";
        $values = $userGroupID;
        $types = "i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Get a row by userGroupID and privilegeID
	 * @access  public
	 * @param   userGroupID
	 *          privilegeID
	 * @return  a table row, if successful
	 *          false, if the row is not found
	 * @author  Cindy Qi Li
	 */
	public function Get($userGroupID, $privilegeID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."user_group_privilege
		         WHERE user_group_id = ?
		           AND privilege_id = ?";
	    $values = array($userGroupID, $privilegeID);
	    $types = "ii";
		$rows = $this->execute($sql, $values, $types);
		
		if (is_array($rows)) return $rows[0];
		else return false;
	}

}
?>