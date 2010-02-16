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
	public function Create($course_id, $content_parent_id, $ordering, $last_modified, $revision, $formatting, $keywords, 
	                       $content_path, $title, $text, $head, $use_customized_head, $test_message, 
	                       $allow_test_export, $content_type)
	{
//		global $addslashes;
//
//		$keywords = $addslashes(strtolower(trim($keywords)));
//		$title = $addslashes(strtolower(trim($title)));
//		$text = $addslashes(strtolower(trim($text)));
//		$head = $addslashes(strtolower(trim($head)));
		
		if ($this->isFieldsValid($course_id, $title))
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
			               allow_test_export,
			               content_type
			               )
			       VALUES (".$course_id.",
			               ".$content_parent_id.",
			               ".$ordering.",
			               '".$last_modified."', 
			               ".$revision.",
			               ".$formatting.",
			               '".$keywords."',
			               '".$content_path."', 
			               '".$title."',
			               '".$text."',
			               '".$head."',
			               ".$use_customized_head.",
			               '".$test_message."',
			               ".$allow_test_export.",
			               ".$content_type.")";

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
	 * Update an existing user record
	 * @access  public
	 * @param   userID: user ID (1 [admin] or 2 [user])
	 *          login: login name
	 *          pwd: password
	 *          email: email
	 *          first_name: first name
	 *          last_name: last name
	 *          status
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Update($userID, $user_group_id, $login, $email, $first_name, $last_name, 
	                       $is_author, $organization, $phone, $address, $city,
	                       $province, $country, $postal_code, $status)
	{
		global $addslashes, $msg;

		/* email check */
		$login = $addslashes(strtolower(trim($login)));
		$email = $addslashes(trim($email));
		$first_name = $addslashes(str_replace('<', '', trim($first_name)));
		$last_name = $addslashes(str_replace('<', '', trim($last_name)));
		$organization = $addslashes(trim($organization));
		$phone = $addslashes(trim($phone));
		$address = $addslashes(trim($address));
		$city = $addslashes(trim($city));
		$province = $addslashes(trim($province));
		$country = $addslashes(trim($country));
		$postal_code = $addslashes(trim($postal_code));
		
		if ($this->isFieldsValid('update', $user_group_id,$login, $email,$first_name, $last_name,
		                         $is_author, $organization, $phone, $address, $city,
	                             $province, $country, $postal_code))
		{
			/* insert into the db */
			$sql = "UPDATE ".TABLE_PREFIX."users
			           SET login = '".$login."',
			               user_group_id = '".$user_group_id."',
			               first_name = '".$first_name."',
			               last_name = '".$last_name."',
			               email = '".$email."',
			               is_author = ".$is_author.",
			               organization = '".$organization."',
			               phone = '".$phone."',
			               address = '".$address."',
			               city = '".$city."',
			               province = '".$province."',
			               country = '".$country."',
			               postal_code = '".$postal_code."',
			               status = '".$status."'
			         WHERE user_id = ".$userID;

			return $this->execute($sql);
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
		global $addslashes;
		
		$sql = "UPDATE ".TABLE_PREFIX."content 
		           SET ".$fieldName."='".$addslashes($fieldValue)."'
		         WHERE content_id = ".$contentID;
		
		return $this->execute($sql);
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
		$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE content_id = ".$contentID;
		return $this->execute($sql);
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
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE content_id='.$contentID;
		if ($rows = $this->execute($sql))
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
		         WHERE course_id=$courseID 
		         ORDER BY content_parent_id, ordering";
		
		return $this->execute($sql);
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
		         WHERE course_id=".$course_id." 
		           AND content_parent_id=".$content_parent_id;
		$rows = $this->execute($sql);
		return intval($rows[0]['ordering']);
	}

	/**
	 * Validate fields preparing for insert and update
	 * @access  private
	 * @param   $course_id, $title
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($course_id, $title)
	{
		global $msg;
		
		$missing_fields = array();
		
		if (intval($course_id) == 0)
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