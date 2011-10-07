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
				$course_id = mysql_insert_id();
				// create the user and course relationship
				$sql = "INSERT INTO ".TABLE_PREFIX."user_courses (user_id, course_id, role, last_cid)
				        VALUES (".$user_id.", ".$course_id.", ".TR_USERROLE_AUTHOR.", 0)";
				$this->execute($sql);
				
				// create the course content directory
				$path = TR_CONTENT_DIR . $course_id . '/';
				@mkdir($path, 0700);
			
				return $course_id;
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
		           SET ".$fieldName."='".$addslashes($fieldValue)."',
		               modified_date = now()
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
		require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
		require_once(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
		$contentDAO = new ContentDAO();
		$forumsCoursesDAO = new ForumsCoursesDAO();
		
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
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc 
		         WHERE test_id in (SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID.")";
		$this->execute($sql);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE course_id = ".$courseID;
		$this->execute($sql);
				
		$sql = "DELETE FROM ".TABLE_PREFIX."tests WHERE course_id = ".$courseID;
		$this->execute($sql);
		
		// delete forums that are associated with this course
		$forumsCoursesDAO->DeleteByCourseID($courseID);
		
		// loop thru content to delete using ContentDAO->Delete(), which deletes a4a objects as well
		$content_rows = $contentDAO->getContentByCourseID($courseID);
		if (is_array($content_rows)) {
			foreach ($content_rows as $content) {
				$contentDAO->Delete($content['content_id']);
			}
		}
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
	 * Update courses.modified_date to the current timestamp
	 * @access  public
	 * @param   id: course_id or content_id
	 *          id_type: "course_id" or "content_id", by default is "course_id"
	 * @return  true if successful, otherwise, return false
	 * @author  Cindy Qi Li
	 */
	public function updateModifiedDate($id, $id_type = "course_id")
	{
		if ($id_type != "course_id" && $id_type != "content_id") return false;
		
		if ($id_type == "course_id") {
			$sql = "UPDATE ".TABLE_PREFIX."courses SET modified_date=now() WHERE course_id=".$id;
		} else if ($id_type == "content_id") {
			$sql = "UPDATE ".TABLE_PREFIX."courses SET modified_date=now() 
			         WHERE course_id=(SELECT course_id FROM ".TABLE_PREFIX."content WHERE content_id=".$id.")";
		}
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
	 * Return courses in the order of time stamp
	 * @access  public
	 * @param   none
	 * @return  course row
	 * @author  Cindy Qi Li
	 */
	public function getByMostRecent()
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."courses 
		         WHERE access='public'
		         ORDER BY modified_date DESC, created_date DESC";
		return $this->execute($sql);
	}

	/**
	 * Return course information by given category id
	 * @access  public
	 * @param   category id
	 * @return  course row
	 * @author  Cindy Qi Li
	 */
	public function getByCategory($categoryID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."courses 
		         WHERE category_id=".$categoryID."
		           AND access='public'
		         ORDER BY title";
		return $rows = $this->execute($sql);
	}

	/**
	 * Return the array of (category_id, num_of_courses)
	 * @access  public
	 * @param   none
	 * @return  the array of (category_id, num_of_courses)
	 * @author  Cindy Qi Li
	 */
	public function getCategoriesAndNumOfCourses()
	{
		$sql = "SELECT category_id, count(*) num_of_courses 
		          FROM ".TABLE_PREFIX."courses
		         WHERE access = 'public' 
		         GROUP BY category_id";
		return $this->execute($sql);
	}

	/**
	 * Return course information by given course id
	 * @access  public
	 * @param   keywords: for keywords to include, use '+' in front.
	 *                    for keywords to exclude, use '-' in front.
	 *                    for example '+a -b' means find all courses with keyword 'a', without 'b'
	 *          catid: category id
	 *          start: start receiving from this record number, 0 if not specified
	 *          maxResults: Number of results desired. If 0, returns all
	 * @return  course row if successful, otherwise, return false
	 * @author  Cindy Qi Li
	 */
	public function getSearchResult($keywords, $catid='', $start=0, $maxResults=0)
	{
		require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
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
        
		// if the keywords is not given, return false
		$keywords = trim($keywords);
//		if ($keywords == '') return false;
		
		$all_keywords = Utility::removeEmptyItemsFromArray(explode(' ', $keywords));
		
//		if (!is_array($all_keywords) || count($all_keywords) == 0) return false;
		
		list($sql_where, $sql_order) = $this->getSearchSqlParams($all_keywords);
		
		if ($sql_where <> '') $sql_where = ' AND '. $sql_where;
		if (trim($catid) <> '') $sql_where .= ' AND category_id='.intval($catid);
		
		// sql search
		$sql = "SELECT DISTINCT cs.course_id, cs.title, cs.description, cs.created_date
		          FROM ".TABLE_PREFIX."courses cs, ".TABLE_PREFIX."content ct, ".TABLE_PREFIX."users u
		         WHERE cs.access='public'
		           AND cs.course_id = ct.course_id
		           AND cs.user_id = u.user_id";
		if ($sql_where <> '') $sql .= $sql_where;
		if ($sql_order <> '') $sql .= " ORDER BY ".$sql_order." DESC ";
		
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

	/**
	 * Based on the pass-in keywords array, return the WHERE and ORDER BY part in the search SQL
	 * @access  private
	 * @param   $all_keywords   the array of all the keywords including "OR"
	 * @return  the array of (sql_where, sql_order) if successful
	 *          otherwise, return empty array
	 * @author  Cindy Qi Li
	 */
	private function getSearchSqlParams($all_keywords)
	{
		if (!is_array($all_keywords) || count($all_keywords) == 0) return array();
		
		$sql_search_template = "(cs.title like '%{KEYWORD}%' ".
		                "OR cs.description like '%{KEYWORD}%' ".
		                "OR ct.keywords like '%{KEYWORD}%' ".
		                "OR ct.title like '%{KEYWORD}%' ".
		                "OR ct.text like '%{KEYWORD}%' ".
		                "OR u.first_name like '%{KEYWORD}%' ".
		                "OR u.last_name like '%{KEYWORD}%')";
		$sql_order_template = " 15* ((LENGTH(cs.title) - LENGTH(REPLACE(lower(cs.title),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) + ".
		                      " 12* ((LENGTH(cs.description) - LENGTH(REPLACE(lower(cs.description),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) + ".
		                      " 10* ((LENGTH(u.first_name) - LENGTH(REPLACE(lower(u.first_name),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) + ".
		                      " 10* ((LENGTH(u.last_name) - LENGTH(REPLACE(lower(u.last_name),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) + ".
		                      " 8* ((LENGTH(ct.keywords) - LENGTH(REPLACE(lower(ct.keywords),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) + ".
		                      " 4* ((LENGTH(ct.title) - LENGTH(REPLACE(lower(ct.title),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) + ".
		                      " 1* ((LENGTH(ct.text) - LENGTH(REPLACE(lower(ct.text),lower('{KEYWORD}'), ''))) / LENGTH(lower('{KEYWORD}'))) ";
		
		// get all OR conditions
		$found_first_or_item = false;
		foreach ($all_keywords as $i => $keyword)
		{
			if ($keyword == 'OR')
			{
				// if the first keyword is "OR" without the leading keyword,
				// OR, the last keyword is "OR" without the following keyword,
				// remove this "OR"
				if ((!isset($all_keywords[$i-1]) && !$found_first_or_item) ||
				    !isset($all_keywords[$i+1]))
				{
					unset($all_keywords[$i]);
					continue;
				}
				
				// The first "OR" joins the 2 keywords around it, 
				// the following "OR" only needs to join the keyword followed.
				// Removed the keywords that have been pushed into OR sql from 
				// the keywords array. 
				if (!$found_first_or_item)
				{
					$found_first_or_item = true;
					$sql_where_or .= str_replace('{KEYWORD}', $all_keywords[$i-1], $sql_search_template) .
					                 ' OR '. 
					                 str_replace('{KEYWORD}', $all_keywords[$i+1], $sql_search_template);
					$sql_order_or .= str_replace('{KEYWORD}', $all_keywords[$i-1], $sql_order_template) .
					              ' + '.
					              str_replace('{KEYWORD}', $all_keywords[$i+1], $sql_order_template);
					unset($all_keywords[$i-1]);  // the keyword before "OR"
					unset($all_keywords[$i]);    // "OR"
					unset($all_keywords[$i+1]);  // the keyword after "OR"
				}
				else
				{
					$sql_where_or .= ' OR '.str_replace('{KEYWORD}', $all_keywords[$i+1], $sql_search_template);
					$sql_order_or .= ' + '.str_replace('{KEYWORD}', $all_keywords[$i+1], $sql_order_template);
					unset($all_keywords[$i]);   // "OR"
					unset($all_keywords[$i+1]); // the keyword after "OR"
				}
			}
		}
		
		// the left-over in $all_keywords array is "AND" condition
		if (count($all_keywords) > 0)
		{
			foreach ($all_keywords as $keyword)
			{
				$sql_where .= str_replace('{KEYWORD}', $keyword, $sql_search_template). ' AND ';
				$sql_order .= str_replace('{KEYWORD}', $keyword, $sql_order_template). ' + ';
			}
		} 
		if ($sql_where_or == '') $sql_where = substr($sql_where, 0, -5);
		else $sql_where .= "(".$sql_where_or.")";
		
		if ($sql_order_or == '') $sql_order = substr($sql_order, 0, -3);
		else $sql_order .= $sql_order_or;
		
		return array($sql_where, $sql_order);
	}
}
?>