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
	* @param   test_id, question_id, weight, order
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function Create($testID, $questionID, $weight, $order)
	{
		global $addslashes;
		
		$testID = intval($testID);
		$questionID = intval($questionID);
		$weight = $addslashes($weight);
		$order = intval($order);
		
		$sql = "INSERT INTO " . TABLE_PREFIX . "tests_questions_assoc" . 
				"(test_id, question_id, weight, ordering) " .
				"VALUES ($testID, $questionID, $weight, $order)";
	    return $this->execute($sql);
	}
	
	/**
	* Update an existing row
	* @access  public
	* @param   test_id, question_id, weight, order
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function Update($testID, $questionID, $weight, $order)
	{
		global $addslashes;
		
		$testID = intval($testID);
		$questionID = intval($questionID);
		$weight = $addslashes($weight);
		$order = intval($order);
		
		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions_assoc 
		              SET weight=".$weight.", ordering=".$order." 
		            WHERE question_id=".$questionID." AND test_id=".$testID;
		return $this->execute($sql);
	}
	
	/**
	* Delete a row by test id and question id
	* @access  public
	* @param   testID, questionID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function Delete($testID, $questionID)
	{		
		$testID = intval($testID);
		$questionID = intval($questionID);

	    $sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
	             WHERE test_id = ".$testID."
	               AND question_id = ".$questionID;
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
		$questionID = intval($questionID);
	    $sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
	             WHERE question_id = ".$questionID;
	    return $this->execute($sql);
	}
	
	/**
	* Delete rows by test id
	* @access  public
	* @param   testID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByTestID($testID)
	{
		$testID = intval($testID);
	    $sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
	             WHERE test_id = ".$testID;
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
		$testID = intval($testID);
	    $sql = "SELECT TQ.*, TQA.test_id, TQA.weight, TQA.ordering 
	              FROM ".TABLE_PREFIX."tests_questions TQ 
	             INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA 
	             USING (question_id) 
	             WHERE TQA.test_id=".$testID."
	             ORDER BY TQA.ordering, TQA.question_id";
	    return $this->execute($sql);
	}

	/**
	* Return all associated questions with the weight 0 in the given test
	* @access  public
	* @param   testID
	* @return  table rows if successful. false if unsuccessful
	* @author  Cindy Qi Li
	*/
	function getZeroWeightRowsByTestID($testID)
	{
		$testID = intval($testID);
	    $sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions_assoc QA, ".TABLE_PREFIX."tests_questions Q 
	             WHERE QA.test_id=$testID 
	               AND QA.weight=0 
	               AND QA.question_id=Q.question_id 
	               AND Q.type<>4";
	    return $this->execute($sql);
	}
	
	/**
	* Return the maximum ordering number in the given test
	* @access  public
	* @param   testID
	* @return  the maximum ordering number
	* @author  Cindy Qi Li
	*/
	function getMaxOrderByTestID($testID)
	{
		$testID = intval($testID);
		$sql = "SELECT MAX(ordering) AS max_ordering FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=".$testID;
	    $rows = $this->execute($sql);
	    return $rows[0]['max_ordering'];
	}
}
?>