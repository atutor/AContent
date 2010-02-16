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
	* Delete a row
	* @access  public
	* @param   name
	* @return  true or false
	* @author  Cindy Qi Li
	function Delete($name)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."config 
	             WHERE name = '".$name."'";
	    return $this->execute($sql);
	}
	*/
	
	/**
	* Return a config row by name
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	function get($name)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."config WHERE name = '".$name."'";
	    $rows = $this->execute($sql);
	    return $rows[0];
	}
	*/
}
?>