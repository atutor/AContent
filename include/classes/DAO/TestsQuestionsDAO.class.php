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
 * DAO for "tests_questions" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class TestsQuestionsDAO extends DAO {

	/**
	 * Update an the given field to a given value of an existing record
	 * @access  public
	 * @param   questionID: question ID
	 *          fieldName: the name of the table field to update
	 *          fieldValue: the value to update
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function UpdateField($questionID, $fieldName, $fieldValue)
	{
		$sql = "UPDATE ".TABLE_PREFIX."tests_questions
		           SET ".$fieldName."=?
		         WHERE question_id = ?";
		$values = array( $fieldValue, $questionID);
		$types = "si";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Delete a row
	 * @access  public
	 * @param   question ID
	 * @return  true, if successful
	 *          false if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($questionID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE question_id = ?";
		$values = $questionID;
		$types = "i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Return information by a given question id
	 * @access  public
	 * @param   questionID: category id
	 * @return  the row if successful, otherwise, return false
	 * @author  Cindy Qi Li
	 */
	public function get($questionID)
	{

		// We use TABLE_PREFIX to prevent error: Table ac_tests_questions doesn't exist
		// when editing or deleting created tests
		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions
		             WHERE question_id=?";
		$values = $questionID;
		$types = "i";
		$rows = $this->execute($sql, $values, $types);

		if (is_array($rows)) return $rows[0];
		else return false;
	}

	/**
	 * Return information by an array of question ids
	 * @access  public
	 * @param   questionIDsArray: an array of question ids
	 * @return  the row if successful, otherwise, return false
	 * @author  Cindy Qi Li
	 */
	public function getByQuestionIDs($questionIDsArray)
	{
		if (!is_array($questionIDsArray) || count($questionIDsArray) == 0) return false;

		$num_of_ids = count($questionIDsArray);

		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions
		             WHERE question_id in (".substr(str_repeat("? , ", $num_of_ids), 0, -2).")";
		$types = str_pad("", $num_of_ids, "i");
		return $this->execute($sql, $questionIDsArray, $types);
	}

	/**
	 * Return content information by given course id and category id
	 * @access  public
	 * @param   courseID
	 *          categoryID
	 * @return  rows
	 * @author  Cindy Qi Li
	 */
	public function getByCourseIDAndCategoryID($courseID, $categoryID)
	{

		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions
		         WHERE course_id = ?
		           AND category_id = ?
		         ORDER BY question";
	    $values = array($courseID, $categoryID);
	    $types="ii";
		return $this->execute($sql, $values, $types);

	}

	/**
	 * Return content information by given course id and question type
	 * @access  public
	 * @param   courseID
	 *          type: question type
	 * @return  rows
	 * @author  Cindy Qi Li
	 */
	public function getByCourseIDAndType($courseID, $type)
	{

		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions
		         WHERE course_id= ?
		           AND type = ?";
	    $values = array($courseID, $type);
	    $types = "ii";
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
