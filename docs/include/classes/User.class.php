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
 * User
 * @access	public
 * @author	Cindy Qi Li
 * @package	User
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/UsersDAO.class.php');

class User {

	// all private
	var $userID;                               // set by setUserID
	var $userDAO;                              // DAO for this user

	/**
	 * Constructor
	 * doing nothing
	 * @access  public
	 * @param   None
	 * @author  Cindy Qi Li
	 */
	function User($user_id)
	{
		$this->userID = $user_id;

		$this->userDAO = new UsersDAO();
	}

	/**
	 * Based on this->userID, return (first name, last name), if first name, last name not exists, return login name
	 * @access  public
	 * @param   none
	 * @return  first name, last name. if not exists, return login name
	 * @author  Cindy Qi Li
	 */
	public function getUserName()
	{
		return $this->userDAO->getUserName($this->userID);
	}

	/**
	 * Return all info of this->userID 
	 * @access  public
	 * @param   none
	 * @return  table row
	 * @author  Cindy Qi Li
	 */
	public function getInfo()
	{
		return $this->userDAO->getUserByID($this->userID);
	}

	/**
	 * Check if user is an author 
	 * @access  public
	 * @param   $course_id: optional. 
	 *          if > 0, check whether the user is the author of the course with $course_id
	 *          else if = 0 or is not given, check whether the user has author privilege 
	 * @return  true : if is an author
	 *          false : if not an author
	 * @author  Cindy Qi Li
	 */
	public function isAuthor($course_id = 0)
	{
		if ($course_id == 0)
		{
			$row = $this->userDAO->getUserByID($this->userID);
			return $row['is_author'];
		}
		else
		{
			include_once(TR_INCLUDE_PATH.'classes/DAO/CoursesDAO.class.php');
			$coursesDAO = new CoursesDAO();
			$course_row = $coursesDAO->get($course_id);
			
			return ($course_row['user_id'] == $this->userID);
		}
	}

	/**
	 * Check if user is admin 
	 * @access  public
	 * @param   none
	 * @return  true : if is an admin
	 *          false : if not an admin
	 * @author  Cindy Qi Li
	 */
	public function isAdmin()
	{
		$row = $this->userDAO->getUserByID($this->userID);
		
		if ($row['user_group_id'] == TR_USER_GROUP_ADMIN)
			return true;
		else
			return false;
	}

	/**
	 * Update user's first, last name
	 * @access  public
	 * @param   $firstName : first name
	 *          $lastName : last name
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setName($firstName, $lastName)
	{
		return $this->userDAO->setName($this->userID, $firstName, $lastName);
	}

	/**
	 * Update user's password
	 * @access  public
	 * @param   $password
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setPassword($password)
	{
		return $this->userDAO->setPassword($this->userID, $password);
	}

	/**
	 * Update user's email
	 * @access  public
	 * @param   $email
	 * @return  true    if update successfully
	 *          false   if update unsuccessful
	 * @author  Cindy Qi Li
	 */
	public function setEmail($email)
	{
		return $this->userDAO->setEmail($this->userID, $email);
	}
}
?>