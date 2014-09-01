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
 * DAO for "forums" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ForumsDAO extends DAO {

	/**
	 * Create new forums
	 * @access  public
	 * @param   
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($title, $description)
	{
		global $addslashes;

		$title = $addslashes(trim($title));
		$decsription = $addslashes(trim($description));
		
		if ($this->isFieldsValid($title))
		{
			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."forums
			              (title, description, created_date)
			       VALUES ('".$title."',
			               '".$decsription."',
			               now())";

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
		else
		{
			return false;
		}
	}

	/**
	 * Delete a forum
	 * @access  public
	 * @param   forum ID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($forumID)
	{
		$forumID = intval($forumID);
		require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');
		require_once(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');
		$contentForumsAssocDAO = new ContentForumsAssocDAO();
		
		// delete the forum and related data
		$contentForumsAssocDAO->DeleteByForumID($forumID);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."forums
		         WHERE forum_id = ".$forumID;
		$this->execute($sql);
	}

	/**
	 * Return by given forum id
	 * @access  public
	 * @param   forum id
	 * @return  one row
	 * @author  Cindy Qi Li
	 */
	public function get($forumID)
	{
		$forumID = intval($forumID);
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'forums WHERE forum_id='.$forumID;
		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Validate fields preparing for insert and update
	 * @access  private
	 * @param   $title
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($title)
	{
		global $msg;
		
		$missing_fields = array();
		
		if ($title == '')
		{
			$missing_fields[] = _AT('title');
		}
		
		if ($missing_fields)
		{
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}
		
		if (!$msg->containsErrors())
			return true;
		else
			return false;
	}
}
?>