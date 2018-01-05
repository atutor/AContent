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
 * DAO for "tests" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');

class TestsDAO extends DAO {

	/**
	 * Create a new row
	 * @access  public
	 * @param   
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($course_id, $title, $description)
	{

        $title = Utility::validateLength(trim($title), 100);
		$description = trim($description);
		
		if ($this->isFieldsValid($title))
		{
	        $sql = "INSERT INTO ".TABLE_PREFIX."tests " .
			       "(course_id, title, description)" .
			       "VALUES (?, ?, ?)";
			$values = array($course_id, $title, $description);
			$types="iss";
			
			if (!$this->execute($sql,$values,$types))
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
	 * Update an existing user record
	 * @access  public
	 * @param   testID, title, description
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Update($testID, $title, $description)
	{

		$title = Utility::validateLength(trim($title), 100);
		$description = trim($description);
		if ($this->isFieldsValid($title))
		{

			$sql = "UPDATE ".TABLE_PREFIX."tests " . 
			       "SET title=?, 
			            description=? 
			        WHERE test_id=?";	
			$values = array($title, $description, $testID);
			$types="ssi";		
			return $this->execute($sql, $values, $types);
		}
	}
	
	/**
	 * Delete content
	 * @access  public
	 * @param   test ID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($testID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."tests WHERE test_id = ?";
		$values = $testID;
		$types = "i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Return content information by given content id
	 * @access  public
	 * @param   test id
	 * @return  test row
	 * @author  Cindy Qi Li
	 */
	public function get($testID)
	{

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'tests WHERE test_id=?';
		$values = $testID;
		$types = "i";

		if ($rows = $this->execute($sql, $values, $types))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Return max ordering based on given course id and content parent id 
	 * @access  public
	 * @param   course_id
	 * @return  test rows
	 * @author  Cindy Qi Li
	 */
	public function getByCourseID($courseID)
	{
		
		$sql = "SELECT * 
		          FROM ".TABLE_PREFIX."tests 
		         WHERE course_id=?";
		$values = $courseID;
		$types = "i";
		return $this->execute($sql, $values, $types);

	}

	/**
	 * Validates fields preparing for insert and update
	 * @access  private
	 * @param   $title
	 *          $random
	 *          $num_questions
	 *          $pass_score_checkbox
	 *          $passscore
	 *          $passpercent
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($title)
	{
		global $msg;
		
		$missing_fields = array();
		
		if ($title == '') {
			$missing_fields[] = _AT('title');
		}
	
		if ($missing_fields) {
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}
	
		if (!$msg->containsErrors()) return true;
		else return false;
	}
}
?>