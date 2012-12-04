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
 * DAO for "course_categories" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class CourseCategoriesDAO extends DAO {

	/**
	 * Create new row
	 * @access  public
	 * @param   category_name
	 * @return  category id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($categoryName)
	{
		global $addslashes, $msg;

		$categoryName = $addslashes(trim($categoryName));
		
		if ($this->isFieldsValid($categoryName))
		{
			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."course_categories (category_name)
			       VALUES ('".$categoryName."')";

			if (!$this->execute($sql))
			{
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
			else
			{
				return mysql_insert_id();
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update an existing record
	 * @access  public
	 * @param   categoryID
	 *          categoryName
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function Update($categoryID, $categoryName)
	{
		global $addslashes, $msg;
		
		$categoryName = $addslashes(trim($categoryName));
		$categoryID = intval($categoryID);
		
		if ($this->isFieldsValid($categoryName, $categoryID, 'update')) {
			$sql = "UPDATE ".TABLE_PREFIX."course_categories 
	           SET category_name='".$categoryName."'
	         WHERE category_id = ".$categoryID;
		
			return $this->execute($sql);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Delete course
	 * @access  public
	 * @param   category ID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($categoryID)
	{
		// move the courses that belong to $categoryID to "uncategorized"
		$sql = "UPDATE ".TABLE_PREFIX."courses 
		           SET category_id=".TR_COURSECATEGORY_UNCATEGORIZED."
		         WHERE category_id = ".$categoryID;
		
		if ($this->execute($sql))
		{
			$sql = "DELETE FROM ".TABLE_PREFIX."course_categories WHERE category_id = ".$categoryID;
			return $this->execute($sql);
		}
		else
			return false;
	}

	/**
	 * Return course category information by given category id
	 * @access  public
	 * @param   category id
	 * @return  one row
	 * @author  Cindy Qi Li
	 */
	public function get($categoryID)
	{
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'course_categories WHERE category_id='.$categoryID;
		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Return all course categories information
	 * @access  public
	 * @param   None
	 * @return  rows
	 * @author  Cindy Qi Li
	 */
	public function getAll()
	{
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'course_categories ORDER BY category_name';
		return $this->execute($sql);
	}

	/**
	 * Validate fields preparing for insert and update
	 * @access  private
	 * @param   $categoryName: Must have
	 *          $categoryID: optional. only required when $actionType is "update"
	 *          $actionType: optional. Must be one of the values: insert, update. The default value is "insert".
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($categoryName, $categoryID = 0, $actionType = "insert")
	{
		global $msg;
		
		$missing_fields = array();
		
		if ($categoryName == '')
		{
			$missing_fields[] = _AT('category_name');
		}
		if ($actionType == 'update' && intval($categoryID) == 0)
		{
			$missing_fields[] = _AT('category_id');
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