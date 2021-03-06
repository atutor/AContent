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
 * DAO for "user_courses" table
 * @access    public
 * @author    Cindy Qi Li
 * @package    DAO
 */

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class UserCoursesDAO extends DAO {

    /**
     * Create a new row
     * @access  public
     * @param   user_id
     *          course_id
     *          last_cid
     * @return  true if a new row is inserted successfully or the record with (user_id, course_id) already exists 
     *          otherwise, false
     * @author  Cindy Qi Li
     */
    public function Create($user_id, $course_id, $role, $last_cid)
    {
        // check whether the record already exists
        if (!$this->isExist($user_id, $course_id))
        {
            /* insert into the db */
            $sql = "INSERT INTO ".TABLE_PREFIX."user_courses
                          (user_id, course_id, role, last_cid)
                   VALUES (?, ?, ?, ?)";
            $values=array($user_id, $course_id, $role, $last_cid);
            $types = "iiii";
            return $this->execute($sql, $values, $types);
        }
        else
        {
            return true;
        }
    }

    /**
     * Update last cid based on user id and course id
     * @access  public
     * @param   user_id
     *          course_id
     *          last_cid
     * @return  true if successful 
     *          otherwise, false
     * @author  Cindy Qi Li
     */
    public function UpdateLastCid($user_id, $course_id, $last_cid)
    {
        // only save last cid for courses that are on the user's course list
        if ($this->isExist($user_id, $course_id))
        {
            $sql = "UPDATE ".TABLE_PREFIX."user_courses
                       SET last_cid = ?
                     WHERE user_id = ?
                       AND course_id = ?";   
            $values = array($last_cid, $user_id, $course_id); 
            $types = "iii";
            return $this->execute($sql, $values, $types);
        }
        else return true;
    }

    /**
     * Delete a record
     * @access  public
     * @param   user id, course id
     * @return  true, if successful, otherwise, return false
     * @author  Cindy Qi Li
     */
    public function Delete($user_id, $course_id)
    {

        $sql = "DELETE FROM ".TABLE_PREFIX."user_courses 
                 WHERE user_id = ? AND course_id = ?";    
        $values = array($user_id, $course_id);    
        $types = "ii";
        return $this->execute($sql, $values, $types);
    }

    /**
     * Return course information by given user id & course_id
     * @access  public
     * @param   user id
     *          course_id
     * @return  one row from table "user_courses"
     * @author  Cindy Qi Li
     */
    public function get($user_id, $course_id)
    {
        $sql = "SELECT * FROM ".TABLE_PREFIX."user_courses
                 WHERE user_id=? 
                   AND course_id=?";
        $values = array($user_id, $course_id);
        $types = "ii";
        if ($rows = $this->execute($sql, $values, $types))
        {
            return $rows[0];
        }
        else return false;
    }

    /**
     * Return course information by given user id
     * @access  public
     * @param   user id
     * @return  course row
     * @author  Cindy Qi Li
     */
    public function getByUserID($user_id)
    {         
        $sql = "SELECT * FROM ".TABLE_PREFIX."user_courses uc, ".TABLE_PREFIX."courses c
                 WHERE uc.user_id=?
                   AND uc.course_id = c.course_id
                 ORDER BY c.title";     
        $values = $user_id;
        $types="i";
        return $this->execute($sql, $values, $types);
    }

    /**
     * Return course information by given user id
     * @access  public
     * @param   user id
     * @return  course row
     * @author  Cindy Qi Li
     */
    public function isExist($user_id, $course_id)
    {

        $sql = "SELECT * FROM ".TABLE_PREFIX."user_courses
                 WHERE user_id=?
                   AND course_id=?";
        $values = array($user_id, $course_id);
        $types = "ii";
        $rows = $this->execute($sql, $values, $types);
        
        return is_array($rows);
    }
    
    /**
     * Check if the course has any content
     * @access  public
     * @param   content id
     * @return  TRUE if course has any content
     * @author  Alexey Novak
     */
    public function hasContent($course_id) {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE course_id = ?';
        $values=$course_id;
        $types="i";
        $rows = $this->execute($sql,$values,$types);
        return is_array($rows);
    }
}
?>