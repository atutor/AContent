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

		$sql = "INSERT INTO " . TABLE_PREFIX . "tests_questions_assoc" . 
				"(test_id, question_id, weight, ordering) " .
				"VALUES (?, ?, ?, ?)";
		$values = array($testID, $questionID, $weight, $order);
		$types = "iiii";
	    return $this->execute($sql, $values, $types);
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

		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions_assoc 
		              SET weight=?, ordering=?  
		            WHERE question_id=? AND test_id=?";
		$values = array($weight, $order, $questionID, $testID);
		$types = "siii";
		
		return $this->execute($sql, $values, $types);
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

	    $sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
	             WHERE test_id = ?
	               AND question_id = ?";
	    $values = array($testID, $questionID);
	    $types = "ii";
	    return $this->execute($sql, $values, $types);
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
	             WHERE question_id = ?";
	    $values = $questionID;
	    $types = "i";
	    return $this->execute($sql, $values, $types);
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

	    $sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
	             WHERE test_id = ?";
	    $values = $testID;
	    $types = "i";
	    return $this->execute($sql, $values, $types);
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

	    $sql = "SELECT TQ.*, TQA.test_id, TQA.weight, TQA.ordering 
	              FROM ".TABLE_PREFIX."tests_questions TQ 
	             INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA 
	             USING (question_id) 
	             WHERE TQA.test_id=?
	             ORDER BY TQA.ordering, TQA.question_id";
	    $values = $testID;
	    $types = "i";
	    return $this->execute($sql, $values, $types);
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

	    $sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions_assoc QA, ".TABLE_PREFIX."tests_questions Q 
	             WHERE QA.test_id=? 
	               AND QA.weight=0 
	               AND QA.question_id=Q.question_id 
	               AND Q.type<>4";
	    $values = $testID;
	    $types = "i";
	    return $this->execute($sql, $values, $types);
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
	    $sql = "SELECT MAX(ordering) AS max_ordering FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=?";
        $values = $testID;
        $types = "i";
	    $rows = $this->execute($sql, $values, $types);
	    return $rows[0]['max_ordering'];
	}
}
?>