<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* DAO for "tests_questions_assoc" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class TestsQuestionsAssocDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   test_id, question_id, weight, order, required
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function Create($test_id, $question_id, $weight, $order, $required)
	{
		$sql = "INSERT INTO " . TABLE_PREFIX . "tests_questions_assoc" . 
				"(test_id, question_id, weight, ordering, required) " .
				"VALUES ($test_id, $question_id, $weight, $order, $required)";
	    return $this->execute($sql);
	}
	
	/**
	* Delete rows by question id
	* @access  public
	* @param   questionID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByQuestionID($questionID)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
	             WHERE question_id = '".$questionID."'";
	    return $this->execute($sql);
	}
	
	/**
	* Return all associated questions of the given test
	* @access  public
	* @param   testID
	* @return  table rows if successful. false if unsuccessful
	* @author  Cindy Qi Li
	*/
	function getByTestID($testID)
	{
	    $sql = "SELECT TQ.*, TQA.weight, TQA.ordering, TQA.required 
	              FROM ".TABLE_PREFIX."tests_questions TQ 
	             INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA 
	             USING (question_id) 
	             WHERE TQA.test_id=".$testID."
	             ORDER BY TQA.ordering, TQA.question_id";
	    return $this->execute($sql);
	}
}
?>