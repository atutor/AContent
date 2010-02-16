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
 * DAO for "tests" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class TestsDAO extends DAO {

	/**
	 * Create a new record
	 * @access  public
	 * @param   $course_id, $title, $description, $format, $start_date, $end_date,
	            $order, $num_questions, $instructions, $content_id, $passscore, $passpercent,
	            $passfeedback, $failfeedback, $result_release, $random, $difficulty,
	            $num_takes, $anonymous, $out_of, $allow_guests, $display
	 * @return  test id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($course_id, $title, $description, $format, $start_date, $end_date,
	                       $order, $num_questions, $instructions, $content_id, $passscore, $passpercent,
	                       $passfeedback, $failfeedback, $result_release, $random, $difficulty,
	                       $num_takes, $anonymous, $out_of, $allow_guests, $display)
	{
		global $addslashes;

		if ($this->isFieldsValid($course_id, $title))
		{
			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."tests " .
				   "(course_id,
					 title,
					 description,
					 `format`,
					 start_date,
					 end_date,
					 randomize_order,
					 num_questions,
					 instructions,
					 content_id,
					 passscore,
					 passpercent,
					 passfeedback,
					 failfeedback,
					 result_release,
					 random,
					 difficulty,
					 num_takes,
					 anonymous,
					 out_of,
					 guests,
					 display) " .
				"VALUES 
				    (".$course_id.", 
				    '".$title."', 
				    '".$description."', 
				    ".$format.", 
				    '".$start_date."', 
				    '".$end_date."', 
				    ".$order.", 
				    ".$num_questions.", 
				    '".$instructions."', 
				    ".$content_id.", 
				    ".$passscore.", 
				    ".$passpercent.", 
				    '".$passfeedback."', 
				    '".$failfeedback."', 
				    ".$result_release.", 
				    ".$random.", 
				    ".$difficulty.", 
				    ".$num_takes.", 
				    ".$anonymous.", 
				    '".$out_of."', 
				    ".$allow_guests.", 
				    ".$display.")";
						
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
	 * Update an existing user record
	 * @access  public
	 * @param   userID: user ID
	 *          fieldName: the name of the table field to update
	 *          fieldValue: the value to update
	 * @return  true if successful
	 *          error message array if failed; false if update db failed
	 * @author  Cindy Qi Li
	 */
	public function UpdateField($userID, $fieldName, $fieldValue)
	{
		global $addslashes;
		
		// check if the required fields are filled
		if ($fieldValue == '') return array(_AT('TR_ERROR_EMPTY_FIELD'));
		
		if ($fieldName == 'login')
		{
			if (!$this->isLoginValid($fieldValue))
			{
				return array(_AT('TR_ERROR_LOGIN_CHARS'));
			}
			else if ($this->isLoginExists($fieldValue))
			{
				return array(_AT('TR_ERROR_LOGIN_EXISTS'));
			}
		}
				
		if ($fieldName == 'email')
		{
			if (!$this->isEmailValid($fieldValue))
			{
				return array(_AT('TR_ERROR_EMAIL_INVALID'));
			}
			else if ($this->isEmailExists($fieldValue))
			{
				return array(_AT('TR_ERROR_EMAIL_EXISTS'));
			}
		}
						
		$sql = "UPDATE ".TABLE_PREFIX."users 
		           SET ".$fieldName."='".$addslashes($fieldValue)."'
		         WHERE user_id = ".$userID;
		
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
	public function getContentByID($contentID)
	{
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE content_id='.$contentID;
		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
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