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

include(TR_INCLUDE_PATH.'classes/DAO/ForumsDAO.class.php');
include(TR_INCLUDE_PATH.'classes/DAO/ForumsCoursesDAO.class.php');
include(TR_INCLUDE_PATH.'classes/DAO/ContentForumsAssocDAO.class.php');

/**
 * A class for DiscussionToolsParser
 * based on:
 *  http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imsdt_v1p0_localised.xsd
 */
class DiscussionToolsImport {
	//global variables
	var $fid;	//the forum id that is imported 

	//constructor
	function DiscussionToolsImport(){}

	//import
	function import($forum_obj, $cid, $course_id){
		$title = $forum_obj->getTitle();
		$text = $forum_obj->getText();

		$this->fid = $this->createForum($title, $text, $course_id);
		$this->associateForum($cid, $this->fid);
	}

	
	/**
	 * create a forum
	 * @param	string	title
	 * @param	string  text/description
	 * @return	added forum's id
	 */
	function createForum($title, $text, $course_id){
		$forumsDAO = new ForumsDAO();
		$forums_id = $forumsDAO->Create($title, $text);
		
		$forumsCoursesDAO = new ForumsCoursesDAO();
		$forumsCoursesDAO->Create($forums_id, $course_id);
		
		return $forums_id;
	}	


	/**
	 * create an association between forum and content
	 * @param	int		content id
	 * @return	
	 */
	function associateForum($cid, $fid){
		$contentForumsAssocDAO = new ContentForumsAssocDAO();
		return $contentForumsAssocDAO->Create($cid, $fid);
	}

	/**
	 * Return the fid that was created by this import
	 * @return	int	 forum id.
	 */
	function getFid(){
		return $this->fid;
	}
}
?>