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
* DAO for "content_tests_assoc" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ContentTestsAssocDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   content_id, test_id
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function Create($content_id, $test_id)
	{
		$sql =	'INSERT INTO ' . TABLE_PREFIX . 'content_tests_assoc' . 
				'(content_id, test_id) ' .
				'VALUES (' . $content_id . ", $test_id)";
	    return $this->execute($sql);
	}
	
	/**
	* Delete row by content ID
	* @access  public
	* @param   contentID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByContentID($contentID)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."content_tests_assoc 
	             WHERE content_id = ".$contentID."";
	    return $this->execute($sql);
	}
	
	/**
	* Delete row by test ID
	* @access  public
	* @param   testID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByTestID($testID)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."content_tests_assoc 
	             WHERE test_id = ".$testID."";
	    return $this->execute($sql);
	}
	
	/**
	* Return rows by content ID
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getByContent($content_id)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id = '".$content_id."'";
	    return $this->execute($sql);
	}
}
?>