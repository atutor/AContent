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
 * DAO for "tests_questions_categories" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class TestsQuestionsCategoriesDAO extends DAO {
	
	/**
	 * Create a new row
	 * @access  public
	 * @param   course_id, title
	 * @return  category id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($courseID, $title)
	{
		if ($this->isFieldsValid('new', $title, $courseID))
		{
            $sql = "INSERT INTO ".TABLE_PREFIX."tests_questions_categories (course_id, title)
			        VALUES (?, ?)";
			$values = array($courseID, $title);
			$types = "is";
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
		else
		{
			return false;
		}
	}

	/**
	 * Update the title of an existing category
	 * @access  public
	 * @param   categoryID
	 *          title
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Update($categoryID, $title)
	{
		if ($this->isFieldsValid('update', $title, $categoryID))
		{
			/* insert into the db */
			$sql = "UPDATE ".TABLE_PREFIX."tests_questions_categories
			           SET title = ?
			         WHERE category_id = ?";
			$values = array($title, $categoryID);
			$types = "si";
			return $this->execute($sql, $values, $types);
		}
	}
	
	/**
	 * Delete content
	 * @access  public
	 * @param   category ID
	 * @return  true, if successful
	 *          false if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($categoryID)
	{		

		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_categories WHERE category_id = ?";
		$values = $categoryID;
		$types = "i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Return content information by given category id
	 * @access  public
	 * @param   categoryID: category id
	 * @return  the row if successful, otherwise, return false
	 * @author  Cindy Qi Li
	 */
	public function get($categoryID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories 
		             WHERE category_id=?";
		$values = $categoryID;
		$types = "i";
		$rows = $this->execute($sql, $values, $types);
		
		if (is_array($rows)) return $rows[0];
		else return false;
	}
	
	/**
	 * Return content information by given content id
	 * @access  public
	 * @param   courseID: course id
	 * @return  rows
	 * @author  Cindy Qi Li
	 */
	public function getByCourseID($courseID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions_categories 
		         WHERE course_id=?
		         ORDER BY title";		
		$values = $courseID;
		$types = "i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Validates fields preparing for insert and update
	 * @access  private
	 * @param   $validate_type : new/update. When "new", $ID is course_id. When "update", $ID is category_id
	 *          $title
	 *          $ID
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($validate_type, $title, $ID)
	{
		global $msg;
		
		$missing_fields = array();
		/* login name check */
		if ($title == '')
		{
			$missing_fields[] = _AT('title');
		}

		if ($ID == 0)
		{
			if ($validate_type == 'new') $missing_fields[] = _AT('course_id');
			if ($validate_type == 'update') $missing_fields[] = _AT('category_id');
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