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
* DAO for "content_forums_assoc" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ContentForumsAssocDAO extends DAO {

	/**
	* Insert a new row
	* @access  public
	* @param   content_id, forum_id
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function Create($content_id, $forum_id)
	{
	/*$content_id = intval($content_id);
	$forum_id = intval($forum_id);
	
		$sql =	'INSERT INTO ' . TABLE_PREFIX . 'content_forums_assoc' . 
				'(content_id, forum_id) ' .
				'VALUES (' . $content_id . ", $forum_id)";
				*/
		$sql =	'INSERT INTO ' . TABLE_PREFIX . 'content_forums_assoc' . 
				'(content_id, forum_id) ' .
				'VALUES (?,?)';
		$values = array($content_id, $forum_id);
		$types = "ii";
		
		if ($this->execute($sql, $values, $types)) {
			// update the courses.modified_date to the current timestamp
			include_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
			$coursesDAO = new CoursesDAO();
			$coursesDAO->updateModifiedDate($content_id, "content_id");
			return true;
		} else {
			$msg->addError('DB_NOT_UPDATED');
			return false;
		}
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
		/*
		$contentID = intval($contentID);
	    $sql = "DELETE FROM ".TABLE_PREFIX."content_forums_assoc 
	             WHERE content_id = ".$contentID."";
	             */
	    $sql = "DELETE FROM ".TABLE_PREFIX."content_forums_assoc 
	             WHERE content_id = ?";
	    $values = $contentID;
	    $types = "i";
		if ($this->execute($sql, $values, $types)) {
			// update the courses.modified_date to the current timestamp
			include_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
			$coursesDAO = new CoursesDAO();
			$coursesDAO->updateModifiedDate($contentID, "content_id");
			return true;
		} else {
			$msg->addError('DB_NOT_UPDATED');
			return false;
		}
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
		/*
		$forumID = intval($forumID);
		
	    $sql = "DELETE FROM ".TABLE_PREFIX."content_forums_assoc 
	             WHERE forum_id = ".$forumID."";
	             */
	    $sql = "DELETE FROM ".TABLE_PREFIX."content_forums_assoc 
	             WHERE forum_id = ?";
	    $values = $forumID;
	    $types = "i";
	             
		if ($this->execute($sql, $values, $types)) {
			// update the courses.modified_date to the current timestamp
			include_once(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
			include_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
			
			$forumsCoursesDAO = new ForumsCoursesDAO();
			$course_rows = $forumsCoursesDAO->getByForum($forumID);
			
			if (is_array($course_rows)) {
				foreach ($course_rows as $row) {
					$coursesDAO = new CoursesDAO();
					$coursesDAO->updateModifiedDate($row['course_id']);
				}
			}
			return true;
		} else {
			$msg->addError('DB_NOT_UPDATED');
			return false;
		}
	}
	
	
	
	/**
	* Delete row by forum ID and content ID
	* @access  public
	* @param   forumID, contentID
	* @return  true or false
	* @author  Cindy Qi Li
	*/
	function Delete($forumID, $contentID) {
		/*
		$forumID = intval($forumID);
		$contentID = intval($contentID);
		
		 $sql = "DELETE FROM ".TABLE_PREFIX."content_forums_assoc 
					 WHERE content_id = '".$contentID."' AND forum_id = '".$forumID."'";
					 */
		 $sql = "DELETE FROM ".TABLE_PREFIX."content_forums_assoc 
					 WHERE content_id = ? AND forum_id = ?";
		$values = array($contentID, $forumID);
		$types = "ii";
			if ($this->execute($sql, $values, $types)) {
				// update the courses.modified_date to the current timestamp
				include_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
				$coursesDAO = new CoursesDAO();
				$coursesDAO->updateModifiedDate($contentID, "content_id");
				return true;
			} else {
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
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
		/*
		$content_id = intval($content_id);
	    $sql = "SELECT f.forum_id, f.title, f.description
	              FROM ".TABLE_PREFIX."content_forums_assoc cfa, ".TABLE_PREFIX."forums f 
	             WHERE cfa.content_id = '".$content_id."'
	               AND cfa.forum_id = f.forum_id";
	               */
	    $sql = "SELECT f.forum_id, f.title, f.description
	              FROM ".TABLE_PREFIX."content_forums_assoc cfa, ".TABLE_PREFIX."forums f 
	             WHERE cfa.content_id = ? 
	               AND cfa.forum_id = f.forum_id";	   
	    $values = $content_id;     
	    $types = "i";
	    return $this->execute($sql, $values, $types);
	}
}
?>