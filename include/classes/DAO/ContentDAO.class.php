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

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class ContentDAO extends DAO {

	/**
	 * Create new content
	 * @access  public
	 * @param   
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($course_id, $content_parent_id, $ordering, $revision, $formatting, $keywords, 
	                       $content_path, $title, $text, $head, $use_customized_head, $test_message, 
	                       $content_type)
	{
		global $msg;
		
		if ($this->isFieldsValid('create', $course_id, $title))
		{
			/* insert into the db */
			
			$sql = "INSERT INTO ".TABLE_PREFIX."content
			              (course_id,
			               content_parent_id,
			               ordering,
			               last_modified,
			               revision,
			               formatting,
			               keywords,
			               content_path,
			               title,
			               text,
			               head,
			               use_customized_head,
			               test_message,
			               content_type
			               )
			       VALUES (?, ?, ?, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";	
		    $values = array($course_id, 
                             $content_parent_id, 
                             $ordering, 
                             $revision, 
                             $formatting, 
                             $keywords, 
                             $content_path, 
                             $title, 
                             $text, 
                             $head, 
                             $use_customized_head, 
                             $test_message, 
                             $content_type);	
		    $types = "iiiiisssssisi";	

			if (!$this->execute($sql,$values,$types))
			{
				
				$msg->addError('DB_NOT_UPDATED');
				return false;
			}
			else
			{

				$cid = $this->ac_insert_id();
				
				// update the courses.modified_date to the current timestamp
				include_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
				$coursesDAO = new CoursesDAO();
				$coursesDAO->updateModifiedDate($cid, "content_id");
				
				return $cid;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update an existing content record
	 * @access  public
	 * @param   userID: user ID (1 [admin] or 2 [user])
	 *          login: login name
	 *          pwd: password
	 *          email: email
	 *          first_name: first name
	 *          last_name: last name
	 *          status
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Update($content_id, $title, $text, $keywords, $formatting, 
	                     $head, $use_customized_head, $test_message)
	{
		global $msg;

		if ($this->isFieldsValid('update', $content_id, $title))
		{
			/* insert into the db */

			$sql = "UPDATE ".TABLE_PREFIX."content
			           SET title = ?,
			               text = ?,
			               keywords = ?,
			               formatting = ?,
			               head = ?,
			               use_customized_head = ?,
			               test_message = ?
			         WHERE content_id =?";
            $values = array($title, 
                            $text, 
                            $keywords, 
                            $formatting, 
                            $head, 
                            $use_customized_head, 
                            $test_message, 
                            $content_id );
            $types = "sssisisi";
			if ($this->execute($sql, $values,$types)) {
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
		else {
			return false;
		}
	}

	/**
	 * Update one field of an existing content record
	 * @access  public
	 * @param   contentID
	 *          fieldName: the name of the table field to update
	 *          fieldValue: the value to update
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function UpdateField($contentID, $fieldName, $fieldValue)
	{
		$sql = "UPDATE ".TABLE_PREFIX."content 
		           SET ".$fieldName."= ?
		         WHERE content_id = ?";
		$values = array($fieldValue, $contentID);
		$types = "si";
		if ($this->execute($sql,$values,$types)) {
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
	 * Delete content
	 * @access  public
	 * @param   content ID
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($contentID)
	{
		global $msg;
		$contentID = intval($contentID);
		
		require_once(TR_INCLUDE_PATH.'classes/A4a/A4a.class.php');
		$a4a = new A4a($contentID);
		$a4a->deleteA4a();
		
		// delete the content tests association
		include_once(TR_INCLUDE_PATH.'classes/DAO/ContentTestsAssocDAO.class.php');
		$contentTestsAssocDAO = new ContentTestsAssocDAO();
		$contentTestsAssocDAO->DeleteByContentID($contentID);
		
		// delete the content forums association
		include_once(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');
		$contentForumsAssocDAO = new ContentForumsAssocDAO();
		$contentForumsAssocDAO->DeleteByContentID($contentID);
		
		$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE content_id = ?";
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
	 * Return content information by given content id
	 * @access  public
	 * @param   content id
	 * @return  content row
	 * @author  Cindy Qi Li
	 */
	public function get($contentID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."content WHERE content_id=?";
		$values = $contentID;
		$types = "i";
	if ($rows = $this->execute($sql, $values, $types))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Return all content rows by given course id
	 * @access  public
	 * @param   course id
	 * @return  content rows
	 * @author  Cindy Qi Li
	 */
	public function getContentByCourseID($courseID)
	{
		$sql = "SELECT *, 
		               UNIX_TIMESTAMP(last_modified) AS u_ts 
		          FROM ".TABLE_PREFIX."content 
		         WHERE course_id=? 
		         ORDER BY content_parent_id, ordering";
		$values=$courseID;
		$types="i";
		return $this->execute($sql, $values, $types);
	}

	/**
	 * Return max ordering based on given course id and content parent id 
	 * @access  public
	 * @param   course_id, content_parent_id
	 * @return  max ordering: int
	 * @author  Cindy Qi Li
	 */
	public function getMaxOrdering($course_id, $content_parent_id)
	{
        $sql = "SELECT MAX(ordering) AS ordering FROM ".TABLE_PREFIX."content 
		         WHERE course_id=? 
		           AND content_parent_id=?";
		$values = array($course_id, $content_parent_id);
		$types = "ii";
		$rows = $this->execute($sql, $values, $types);
		return intval($rows[0]['ordering']);
	}

	/**
	 * Validate fields preparing for insert and update
	 * @access  private
	 * @param   $action_type: "create" or "update"
	 *          $row_id: when action_type is "create", row_id is course_id
	 *                   when action_type is "update", row_id is content_id
	 *          $title: content title
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($action_type, $row_id, $title)
	{
		global $msg;
		
		$missing_fields = array();
		
		if (intval($row_id) == 0)
		{
			if ($action_type == 'create') $missing_fields[] = _AT('course_id');
			if ($action_type == 'update') $missing_fields[] = _AT('content_id');
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