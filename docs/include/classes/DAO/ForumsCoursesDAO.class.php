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
* DAO for "forums_courses" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ForumsCoursesDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   forum_id, course_id
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function Create($forum_id, $course_id)
	{
		$sql =	'INSERT INTO ' . TABLE_PREFIX . 'forums_courses' . 
				'(forum_id, course_id) ' .
				'VALUES (' . $forum_id . ", $course_id)";
	    return $this->execute($sql);
	}
	
	/**
	* Delete row by course ID
	* @access  public
	* @param   courseID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByCourseID($courseID)
	{
	    include_once(TR_INCLUDE_PATH.'classes/DAO/ForumsDAO.class.php');
	    $forumsDAO = new ForumsDAO();
	    
		$all_forums = $this->getByCourse($courseID);
	    if (is_array($all_forums)) {
	    	foreach ($all_forums as $forums) {
	    		$forumsDAO->Delete($forums['forum_id']);
	    	}
	    }
		$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses 
	             WHERE course_id = ".$courseID."";
	    return $this->execute($sql);
	}
	
	/**
	* Delete row by forum ID
	* @access  public
	* @param   forumID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function DeleteByForumID($forumID)
	{
	    $sql = "DELETE FROM ".TABLE_PREFIX."forums_courses 
	             WHERE forum_id = ".$forumID."";
	    return $this->execute($sql);
	}
	
	/**
	* Return rows by course ID
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getByCourse($course_id)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE course_id = '".$course_id."'";
	    return $this->execute($sql);
	}

	/**
	* Return rows by forum ID
	* @access  public
	* @param   name
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getByForum($forum_id)
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE forum_id = '".$forum_id."'";
	    return $this->execute($sql);
	}
}
?>