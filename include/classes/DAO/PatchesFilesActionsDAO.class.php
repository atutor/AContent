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
 * DAO for "patches_files_actions" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class PatchesFilesActionsDAO extends DAO {

	/**
	 * Create new row
	 * @access  public
	 * @param   $patches_files_id, $action, $code_from, $code_to
	 * @return  patches_files_actions_id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($patches_files_id, $action, $code_from, $code_to)
	{
		global $addslashes;
		$patches_files_id = intval($patches_files_id);
		$actions = $addslashes($actions);
		
		$sql = "INSERT INTO " . TABLE_PREFIX. "patches_files_actions " .
					 "(patches_files_id, 
					   action,
					   code_from,
					   code_to)
					  VALUES
					  (".$patches_files_id.",
					   '".$action."',
					   '".$addslashes($code_from)."',
					   '".$addslashes($code_to)."')";
		
		if (!$this->execute($sql))
		{
			$msg->addError('DB_NOT_UPDATED');
			return false;
		}
		else
		{
			//return mysql_insert_id();
			return ac_insert_id();
		}
	}
}
?>