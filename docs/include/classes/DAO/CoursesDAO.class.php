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
 * DAO for "courses" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class CoursesDAO extends DAO {

	/**
	 * Create new course
	 * @access  public
	 * @param   
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($user_id, $content_packaging, $access, $title, $description, $course_dir_name, 
	                       $max_quota, $max_file_size, $copyright,
	                       $primary_language, $icon, $side_menu)
	{
		global $addslashes;

		$title = $addslashes(trim($title));
		$decsription = $addslashes(trim($description));
		$copyright = $addslashes(trim($copyright));
		
		if ($this->isFieldsValid($user_id, $title))
		{
			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."courses
			              (user_id,
			               content_packaging,
			               access,
			               title,
			               description,
			               course_dir_name,
			               max_quota,
			               max_file_size,
			               copyright,
			               primary_language,
			               icon,
			               side_menu,
			               created_date
			               )
			       VALUES (".$user_id.",
			               '".$content_packaging."',
			               '".$access."',
			               '".$title."',
			               '".$decsription."', 
			               '".$course_dir_name."',
			               '".$max_quota."',
			               '".$max_file_size."',
			               '".$copyright."',
			               '".$primary_language."',
			               '".$icon."',
			               '".$side_menu."',
			               now())";

			if (!$this->execute($sql))
			{
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
			else
			{
				return mysql_insert_id();
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update an existing course record
	 * @access  public
	 * @param   courseID: course ID
	 *          fieldName: the name of the table field to update
	 *          fieldValue: the value to update
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function UpdateField($courseID, $fieldName, $fieldValue)
	{
		global $addslashes, $msg;
		
		// check if ther courseID is provided
		if (intval($courseID) == 0)
		{
			$missing_fields[] = _AT('course_id');
		}
		
		if ($missing_fields)
		{
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}
		
		if ($msg->containsErrors())
			return false;
		
		// check if the course title is provided
		if ($fieldName == 'title' && !$this->isFieldsValid($courseID, $fieldValue))
			return false;
		
		$sql = "UPDATE ".TABLE_PREFIX."courses 
		           SET ".$fieldName."='".$addslashes($fieldValue)."'
		         WHERE course_id = ".$courseID;
		
		return $this->execute($sql);
	}
	
	/**
	 * Delete course
	 * @access  public
	 * @param   course ID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($courseID)
	{
		require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');
		
		unset($_SESSION['s_cid']);
		
		// delete course content dir
		$content_dir = TR_CONTENT_DIR.$courseID.'/';
		FileUtility::clr_dir($content_dir);
		
		// delete tests and tests related data
		$sql = "DELETE FROM ".TABLE_PREFIX."content_tests_assoc
		         WHERE content_id in (SELECT content_id FROM ".TABLE_PREFIX."content WHERE course_id = ".$courseID.")";
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id = ".$courseID;
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_results 
		         WHERE test_id in (SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID.")";
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
		         WHERE test_id in (SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID.")";
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_groups 
		         WHERE test_id in (SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID.")";
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_answers 
		         WHERE question_id in (SELECT question_id FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID.")";
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE course_id = ".$courseID;
		$this->execute($sql);
				
		$sql = "DELETE FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID;
		$this->execute($sql);
		
		// delete content
		$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE course_id = ".$courseID;
		$this->execute($sql);
		
		// delete user <-> course association
		$sql = "DELETE FROM ".TABLE_PREFIX."user_courses WHERE course_id = ".$courseID;
		$this->execute($sql);
		
		// delete the course
		$sql = "DELETE FROM ".TABLE_PREFIX."courses WHERE course_id = ".$courseID;
		return $this->execute($sql);
	}

	/**
	 * Return course information by given course id
	 * @access  public
	 * @param   course id
	 * @return  course row
	 * @author  Cindy Qi Li
	 */
	public function get($courseID)
	{
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'courses WHERE course_id='.$courseID;
		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Return course information by given course id
	 * @access  public
	 * @param   keywords: for keywords to include, use '+' in front.
	 *                    for keywords to exclude, use '-' in front.
	 *                    for example '+a -b' means find all courses with keyword 'a', without 'b'
	 *          start: start receiving from this record number, 0 if not specified
	 *          maxResults: Number of results desired. If 0, returns all
	 * @return  course row
	 * @author  Cindy Qi Li
	 */
	public function getSearchResult($keywords, $start=0, $maxResults=0)
	{
		// full-text search
//		$sql = "SELECT DISTINCT course_id, title, description, created_date
//		          FROM (SELECT cs.course_id as course_id, cs.title as title, cs.description as description
//		                       MATCH(cs.title, cs.description) AGAINST ('".$keywords."') as score1,
//		                       MATCH(ct.keywords, ct.title, ct.text) AGAINST ('".$keywords."') score2
//		                  FROM ".TABLE_PREFIX."courses cs, ".TABLE_PREFIX."content ct
//		                 WHERE cs.access='public'
//		                   AND cs.course_id = ct.course_id
//		                   AND (MATCH(cs.title, cs.description) AGAINST ('".$keywords."' in boolean mode)
//		                    OR MATCH(ct.keywords, ct.title, ct.text) AGAINST ('".$keywords."' in boolean mode))
//		                 ORDER BY score1+score2 desc) a";
        
		// sql search
		$sql = "SELECT DISTINCT cs.course_id, cs.title, cs.description, cs.created_date
		          FROM ".TABLE_PREFIX."courses cs, ".TABLE_PREFIX."content ct, ".TABLE_PREFIX."users u
		         WHERE cs.access='public'
		           AND cs.course_id = ct.course_id
		           AND cs.user_id = u.user_id
		           AND (cs.title like '%".$keywords."%'
		                OR cs.description like '%".$keywords."%'
		                OR ct.keywords like '%".$keywords."%'
		                OR ct.title like '%".$keywords."%'
		                OR ct.text like '%".$keywords."%'
		                OR u.first_name like '%".$keywords."%'
		                OR u.last_name like '%".$keywords."%')";
		
		if ($maxResults > 0) $sql .= " LIMIT ".$start.", ".$maxResults;
		
		return $this->execute($sql);
	}

	/**
	 * Validate fields preparing for insert and update
	 * @access  private
	 * @param   $courseID, $title
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($courseID, $title)
	{
		global $msg;
		
		$missing_fields = array();
		
		if (intval($courseID) == 0)
		{
			$missing_fields[] = _AT('course_id');
		}
		if ($title == '')
		{
			$missing_fields[] = _AT('title');
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