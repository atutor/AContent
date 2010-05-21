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
 * DAO for "users" table
 * @access	public
 * @author	Cindy Qi Li
 * @package	DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');

class UsersDAO extends DAO {

	/**
	 * Validate if the given login/pwd is valid
	 * @access  public
	 * @param   login: login id or email
	 *          pwd: password
	 * @return  user id, if login/pwd is valid
	 *          false, if login/pwd is invalid
	 * @author  Cindy Qi Li
	 */
	public function Validate($login, $pwd)
	{
		$sql = "SELECT user_id FROM ".TABLE_PREFIX."users 
		         WHERE (login='".$login."' OR email='".$login."') 
		           AND SHA1(CONCAT(password, '".$_SESSION[token]."'))='".$pwd."'";

		$rows = $this->execute($sql);
		if (is_array($rows))
		{
			return $rows[0]['user_id'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Create new user
	 * @access  public
	 * @param   user_group_id: user group ID (1 [admin] or 2 [user])
	 *          login: login name
	 *          pwd: password
	 *          email: email
	 *          first_name: first name
	 *          last_name: last name
	 * @return  user id, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Create($user_group_id, $login, $pwd, $email, $first_name, $last_name, 
	                       $is_author, $organization, $phone, $address, $city,
	                       $province, $country, $postal_code, $status)
	{
		global $addslashes;

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

		if ($this->isFieldsValid('new', $user_group_id, $login, $email,$first_name, $last_name,
		                         $is_author, $organization, $phone, $address, $city,
	                             $province, $country, $postal_code))
		{
			if ($status == "")
			{
				if (defined('TR_EMAIL_CONFIRMATION') && TR_EMAIL_CONFIRMATION)
				{
					$status = TR_STATUS_UNCONFIRMED;
				} else
				{
					$status = TR_STATUS_ENABLED;
				}
			}

			/* insert into the db */
			$sql = "INSERT INTO ".TABLE_PREFIX."users
			              (login,
			               password,
			               user_group_id,
			               first_name,
			               last_name,
			               email,
			               is_author,
			               organization,
			               phone,
			               address,
			               city,
			               province,
			               country,
			               postal_code,
			               web_service_id,
			               status,
			               create_date
			               )
			       VALUES ('".$login."',
			               '".$pwd."',
			               ".$user_group_id.",
			               '".$first_name."',
			               '".$last_name."', 
			               '".$email."',
			               ".$is_author.",
			               '".$organization."',
			               '".$phone."',
			               '".$address."',
			               '".$city."',
			               '".$province."',
			               '".$country."',
			               '".$postal_code."',
			               '".Utility::getRandomStr(32)."',
			               ".$status.", 
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
		global $addslashes;

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
	 * Delete user
	 * @access  public
	 * @param   user_id
	 * @return  true, if successful
	 *          false and add error into global var $msg, if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function Delete($userID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."users
		         WHERE user_id = ".$userID;

		return $this->execute($sql);
	}

	/**
	 * Return all users' information
	 * @access  public
	 * @param   none
	 * @return  user rows
	 * @author  Cindy Qi Li
	 */
	public function getAll()
	{
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'users ORDER BY user_id';
		return $this->execute($sql);
	}

	/**
	 * Return user information by given user id
	 * @access  public
	 * @param   user id
	 * @return  user row
	 * @author  Cindy Qi Li
	 */
	public function getUserByID($userID)
	{
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'users WHERE user_id='.$userID;
		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Return user information by given web service ID
	 * @access  public
	 * @param   web service ID
	 * @return  user row
	 * @author  Cindy Qi Li
	 */
	public function getUserByWebServiceID($webServiceID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."users WHERE web_service_id='".$webServiceID."'";
		if ($rows = $this->execute($sql))
		{
			return $rows[0];
		}
		else return false;
	}

	/**
	 * Return user information by given email
	 * @access  public
	 * @param   email
	 * @return  user row : if successful
	 *          false : if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function getUserByEmail($email)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."users WHERE email='".$email."'";

		$rows = $this->execute($sql);
		if (is_array($rows))
		{
			return $rows[0];
		}
		else
		return false;
	}

	/**
	 * Return user information by given first, last name
	 * @access  public
	 * @param   $firstName : first name
	 *          $lastName : last name
	 * @return  user row : if successful
	 *          false   if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function getUserByName($firstName, $lastName)
	{
		$sql = "SELECT user_id FROM ".TABLE_PREFIX."users
			        WHERE first_name='".$firstName."' 
			        AND last_name='".$lastName."'";

		$rows = $this->execute($sql);
		if (is_array($rows))
		{
			return $rows[0];
		}
		else
			return false;
	}

	/**
	 * Based on this->userID, return (first name, last name), if first name, last name not exists, return login name
	 * @access  public
	 * @param   $userID
	 * @return  first name, last name. if not exists, return login name
	 * @author  Cindy Qi Li
	 */
	public function getUserName($userID)
	{
		$row = $this->getUserByID($userID);
		
		if (!$row) return false;
		
		if ($row['first_name'] <> '' && $row['last_name'] <> '')
		{
			return $row['first_name']. ' '.$row['last_name'];
		}
		else if ($row['first_name'] <> '')
		{
			return $row['first_name'];
		}
		else if ($row['last_name'] <> '')
		{
			return $row['last_name'];
		}
		else
		{
			return $row['login'];
		}
	}
	
	/**
	 * Return given user's status
	 * @access  public
	 * @param   user id
	 * @return  user's status
	 * @author  Cindy Qi Li
	 */
	public function getStatus($userID)
	{
		$sql = "SELECT status FROM ".TABLE_PREFIX."users WHERE user_id='".$userID."'";
		$rows = $this->execute($sql);

		if ($rows)
		return $rows[0]['status'];
		else
		return false;
	}

	/**
	 * Set user's status
	 * @access  public
	 * @param   user id
	 *          status
	 * @return  true    if status is set successfully
	 *          false   if unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setStatus($userID, $status)
	{
		$sql = "Update ".TABLE_PREFIX."users SET status='".$status."' WHERE user_id='".$userID."'";
		return $this->execute($sql);
	}

	/**
	 * Update user's last login time to now()
	 * @access  public
	 * @param   user id
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setLastLogin($userID)
	{
		$sql = "Update ".TABLE_PREFIX."users SET last_login=now() WHERE user_id='".$userID."'";
		return $this->execute($sql);
	}

	/**
	 * Update user's first, last name
	 * @access  public
	 * @param   $userID : user ID
	 *          $firstName : first name
	 *          $lastName : last name
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setName($userID, $firstName, $lastName)
	{
		$sql = "Update ".TABLE_PREFIX."users SET first_name='".$firstName."', last_name='".$lastName."' WHERE user_id='".$userID."'";
		return $this->execute($sql);
	}

	/**
	 * Update user's password
	 * @access  public
	 * @param   $userID : user ID
	 *          $password : password
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setPassword($userID, $password)
	{
		$sql = "Update ".TABLE_PREFIX."users SET password='".$password."' WHERE user_id='".$userID."'";
		return $this->execute($sql);
	}

	/**
	 * Update user's email
	 * @access  public
	 * @param   $userID : user ID
	 *          $email : email
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setEmail($userID, $email)
	{
		$sql = "Update ".TABLE_PREFIX."users SET email='".$email."' WHERE user_id='".$userID."'";
		return $this->execute($sql);
	}

	/**
	 * Validates fields preparing for insert and update
	 * @access  private
	 * @param   $validate_type : new/update. When validating for update, don't check if the login, email, name are unique
	 *          $user_group_id : user ID
	 *          $login
	 *          $email
	 *          $first_name
	 *          $last_name
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	private function isFieldsValid($validate_type, $user_group_id, $login, $email, $first_name, $last_name,
	                               $is_author, $organization, $phone, $address, $city,
	                               $province, $country, $postal_code)
	{
		global $msg;
		
		$missing_fields = array();
		/* login name check */
		if ($login == '')
		{
			$missing_fields[] = _AT('login_name');
		}
		else
		{
			/* check for special characters */
			if (!$this->isLoginValid($login))
			{
				$msg->addError('LOGIN_CHARS');
			}
			else if ($validate_type == 'new' && $this->isLoginExists($login))
			{
				$msg->addError('LOGIN_EXISTS');
			}
		}

		if ($user_group_id == '' || $user_group_id <= 0)
		{
			$missing_fields[] = _AT('user_group');
		}
		if ($email == '')
		{
			$missing_fields[] = _AT('email');
		}
		else if (!$this->isEmailValid($email))
		{
			$msg->addError('EMAIL_INVALID');
		}

		if ($validate_type == 'new' && $this->isEmailExists($email))
		{
			$msg->addError('EMAIL_EXISTS');
		}

		if (!$first_name) {
			$missing_fields[] = _AT('first_name');
		}

		if (!$last_name) {
			$missing_fields[] = _AT('last_name');
		}

		// when user requests to be an author, author information is necessary
		if ($is_author <> 0 && $is_author <> 1)
		{
			$msg->addError('INVALID_CHECKBOX_STATUS');
		}
		
		if ($is_author == 1)
		{
			if (!$organization) $missing_fields[] = _AT('organization');
			if (!$phone) $missing_fields[] = _AT('phone');
			if (!$address) $missing_fields[] = _AT('address');
			if (!$city) $missing_fields[] = _AT('city');
			if (!$province) $missing_fields[] = _AT('province');
			if (!$country) $missing_fields[] = _AT('country');
			if (!$postal_code) $missing_fields[] = _AT('postal_code');
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
	 * Validate if the login name is valid
	 * @access  private
	 * @param   $login
	 * @return  true    if valid
	 *          false   if not valid
	 * @author  Cindy Qi Li
	 */
	private function isLoginValid($login)
	{
		return preg_match("/^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$/i", $login);
	}

	/**
	 * Validate if the login name already exists
	 * @access  private
	 * @param   $login
	 * @return  true    if login already exists
	 *          false   if login not exists
	 * @author  Cindy Qi Li
	 */
	private function isLoginExists($login)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."users WHERE login='".$login."'";

		return is_array($this->execute($sql));
	}

	/**
	 * Validate if the email is valid
	 * @access  private
	 * @param   $email
	 * @return  true    if valid
	 *          false   if not valid
	 * @author  Cindy Qi Li
	 */
	private function isEmailValid($email)
	{
		return preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $email);
	}

	/**
	 * Validate if the email already exists
	 * @access  private
	 * @param   $login
	 * @return  true    if email already exists
	 *          false   if email not exists
	 * @author  Cindy Qi Li
	 */
	private function isEmailExists($email)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."users WHERE email='".$email."'";

		return is_array($this->execute($sql));
	}

}
?>