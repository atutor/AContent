<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH. 'classes/Utility.class.php');

class ContentManager
{
	/* db handler	*/
	var $db;

	/*	array		*/
	var $_menu;

	/*	array		*/
	var $_menu_info;

	/*	array		*/
	var $_menu_in_order;

	/* int			*/
	var $course_id;

	// private
	var $num_sections;
	var $max_depth;
	var $content_length;
	var $dao;
	var $contentDAO;
	

	/* constructor	*/
	function ContentManager($course_id) {
		if (!($course_id > 0)) {
			return;
		}
		$this->course_id = $course_id;
		$this->dao = new DAO();
		$this->contentDAO = new ContentDAO();
		
		
		
		
		/* x could be the ordering or even the content_id	*/
		/* don't really need the ordering anyway.			*/
		/* $_menu[content_parent_id][x] = array('content_id', 'ordering', 'title') */
		$_menu = array();

		/* number of content sections */
		$num_sections = 0;

		$max_depth = array();
		$_menu_info = array();

//		$sql = "SELECT content_id, content_parent_id, ordering, title, UNIX_TIMESTAMP(release_date) AS u_release_date, content_type 
//		          FROM ".TABLE_PREFIX."content 
//		         WHERE course_id=$this->course_id 
//		         ORDER BY content_parent_id, ordering";
//		$result = mysql_query($sql, $this->db);

		$rows = $this->contentDAO->getContentByCourseID($this->course_id);
		
		
		if (is_array($rows)) {
			foreach ($rows as $row) {
				
				
				
				$num_sections++;
				$_menu[$row['content_parent_id']][] = array('content_id'=> $row['content_id'],
															'ordering'	=> $row['ordering'], 
															'title'		=> htmlspecialchars($row['title']),
															'content_type' => $row['content_type'],
															'optional' => $row['optional']);
	
				$_menu_info[$row['content_id']] = array('content_parent_id' => $row['content_parent_id'],
														'title'				=> htmlspecialchars($row['title']),
														'ordering'			=> $row['ordering'],
														'content_type' => $row['content_type'],
														'optional' => $row['optional']);
	
				/* 
				 * add test content asscioations
				 * find associations per content page, and add it as a sublink.
				 * @author harris
				 */
				$test_rows = $this->getContentTestsAssoc($row['content_id']);
				if (is_array($test_rows)) {
					foreach ($test_rows as $test_row){
						$_menu[$row['content_id']][] = array(	'test_id'	=> $test_row['test_id'],
																'title'		=> htmlspecialchars($test_row['title']),
																'content_type' => CONTENT_TYPE_CONTENT);
					} // end of foreach
				} // end of if
				/* End of add test content asscioations */
	
				if ($row['content_parent_id'] == 0) {
					$max_depth[$row['content_id']] = 1;
				} else {
					$max_depth[$row['content_id']] = $max_depth[$row['content_parent_id']]+1;
				}
			} // end of foreach
		} // end of if

		$this->_menu = $_menu;

		$this->_menu_info =  $_menu_info;

		$this->num_sections = $num_sections;

		if (count($max_depth) > 1) {
			$this->max_depth = max($max_depth);
		} else {
			$this->max_depth = 0;
		}

		// generate array of all the content ids in the same order that they appear in "content navigation"
		if ($this->getNextContentID(0) > 0) {
			$this->_menu_in_order[] = $next_content_id = $this->getNextContentID(0);
		}
		while ($next_content_id > 0)
		{
			$next_content_id = $this->getNextContentID($next_content_id);
			
			if (in_array($next_content_id, $this->_menu_in_order)) break;
			else if ($next_content_id > 0) $this->_menu_in_order[] = $next_content_id;
		}
		
		$this->content_length = count($_menu[0]);
	}

	// This function is called by initContent to construct $this->_menu_in_order, an array to 
	// holds all the content ids in the same order that they appear in "content navigation"
	function getNextContentID($content_id, $order=0) {
		// return first root content when $content_id is not given
		if (!$content_id) {
			return $this->_menu[0][0]['content_id'];
		}
		
		$myParent = $this->_menu_info[$content_id]['content_parent_id'];
		$myOrder  = $this->_menu_info[$content_id]['ordering'];
		
		// calculate $myOrder, add in the number of tests in front of this content page
		if (is_array($this->_menu[$myParent])) {
			$num_of_tests = 0;
			foreach ($this->_menu[$myParent] as $menuContent) {
				if ($menuContent['content_id'] == $content_id) break;
				if (isset($menuContent['test_id'])) $num_of_tests++;
			}
		}
		$myOrder += $num_of_tests;
		// end of calculating $myOrder
		
		/* if this content has children, then take the first one. */
		if ( isset($this->_menu[$content_id]) && is_array($this->_menu[$content_id]) && ($order==0) ) {
			/* has children */
			// if the child is a test, keep searching for the content id
			foreach ($this->_menu[$content_id] as $menuID => $menuContent)
			{
				if (!empty($menuContent['test_id'])) continue;
				else 
				{
					$nextMenu = $this->_menu[$content_id][$menuID]['content_id'];
					break;
				}
			}
			
			// all children are tests
			if (!isset($nextMenu))
			{
				if (isset($this->_menu[$myParent][$myOrder]['content_id'])) {
					// has sibling
					return $this->_menu[$myParent][$myOrder]['content_id'];
				}
				else { // no sibling
					$nextMenu = $this->getNextContentID($myParent, 1);
				}
			}
			return $nextMenu;
		} else {
			/* no children */
			if (isset($this->_menu[$myParent][$myOrder]) && $this->_menu[$myParent][$myOrder] != '') {
				/* Has sibling */
				return $this->_menu[$myParent][$myOrder]['content_id'];
			} else {
				/* No more siblings */
				if ($myParent != 0) {
					return $this->getNextContentID($myParent, 1);
				}
			}
		}
	}
	
	function getContent($parent_id=-1, $length=-1) {
		if ($parent_id == -1) {
			$my_menu_copy = $this->_menu;
			if ($length != -1) {
				$my_menu_copy[0] = array_slice($my_menu_copy[0], 0, $length);
			}
			return $my_menu_copy;
		}
		return $this->_menu[$parent_id];
	}


	function &getContentPath($content_id) {
		$path = array();

		$path[] = array('content_id' => $content_id, 'title' => $this->_menu_info[$content_id]['title']);

		$this->getContentPathRecursive($content_id, $path);

		$path = array_reverse($path);
		return $path;
	}


	function getContentPathRecursive($content_id, &$path) {
		$parent_id = $this->_menu_info[$content_id]['content_parent_id'];

		if ($parent_id > 0) {
			$path[] = array('content_id' => $parent_id, 'title' => $this->_menu_info[$parent_id]['title']);
			$this->getContentPathRecursive($parent_id, $path);
		}
	}
	

	function addContent($course_id, $content_parent_id, $ordering, $title, $text, $keywords, 
	                    $related, $formatting, $head = '', $use_customized_head = 0, 
	                    $test_message = '', $content_type = CONTENT_TYPE_CONTENT) {
		global $_current_user, $_course_id;
		
	    if (!isset($_current_user) || (!$_current_user->isAuthor($this->course_id) && !$_current_user->isAdmin())) {
			return false;
		}

		// shift the new neighbouring content down
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering+1 
		         WHERE ordering>=$ordering 
		           AND content_parent_id=$content_parent_id 
		           AND course_id=$_course_id";
		 
		$this->contentDAO->execute($sql);
		
		/* main topics all have minor_num = 0 */
		$cid = $this->contentDAO->Create($_course_id, $content_parent_id, $ordering, 0, $formatting,
		                          $keywords, '', $title, $text, $head, $use_customized_head,
		                          $test_message, $content_type);
		
		return $cid;
	}
	
	function editContent($content_id, $title, $text, $keywords, $formatting, 
	                     $head, $use_customized_head, $test_message) {
	    global $_current_user;
	    
		if (!isset($_current_user) || !$_current_user->isAuthor($this->course_id) && !$_current_user->isAdmin()) {
			return FALSE;
		}

		$this->contentDAO->Update($content_id, $title, $text, $keywords, $formatting, $head, $use_customized_head,
		                          $test_message);
	}

	function moveContent($content_id, $new_content_parent_id, $new_content_ordering) {
		global $msg, $_current_user, $_course_id;
		
	    if (!isset($_current_user) || (!$_current_user->isAuthor($this->course_id) && !$_current_user->isAdmin())) {
			return FALSE;
		}

		/* first get the content to make sure it exists	*/
//		$sql	= "SELECT ordering, content_parent_id FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
//		$result	= mysql_query($sql, $this->db);
		if (!($row = $this->getContentPage($content_id)) ) {
			return FALSE;
		}
		$old_ordering		= $row['ordering'];
		$old_content_parent_id	= $row['content_parent_id'];
		
		$sql	= "SELECT max(ordering) max_ordering FROM ".TABLE_PREFIX."content WHERE content_parent_id=$old_content_parent_id AND course_id=$_course_id";
//		$result	= mysql_query($sql, $this->db);
//		$row = mysql_fetch_assoc($result);
		$row = $this->contentDAO->execute($sql);
		$max_ordering = $row[0]['max_ordering'];
		
		if ($content_id == $new_content_parent_id) {
			$msg->addError("NO_SELF_AS_PARENT");
			return;
		}
		
		if ($old_content_parent_id == $new_content_parent_id && $old_ordering == $new_content_ordering) {
			$msg->addError("SAME_LOCATION");
			return;
		}
		
		$content_path = $this->getContentPath($new_content_parent_id);
		foreach ($content_path as $parent){
			if ($parent['content_id'] == $content_id) {
				$msg->addError("NO_CHILD_AS_PARENT");
				return;
			}
		}
		
		// if the new_content_ordering is greater than the maximum ordering of the parent content, 
		// set the $new_content_ordering to the maximum ordering. This happens when move the content 
		// to the last element under the same parent content.
		if ($old_content_parent_id == $new_content_parent_id && $new_content_ordering > $max_ordering) 
			$new_content_ordering = $max_ordering;
		if (($old_content_parent_id != $new_content_parent_id) || ($old_ordering != $new_content_ordering)) {
			// remove the gap left by the moved content
			$sql = "UPDATE ".TABLE_PREFIX."content 
			           SET ordering=ordering-1 
			         WHERE ordering>$old_ordering 
			           AND content_parent_id=$old_content_parent_id 
			           AND content_id<>$content_id 
			           AND course_id=$_course_id";
//			$result = mysql_query($sql, $this->db);
			$this->contentDAO->execute($sql);

			// shift the new neighbouring content down
			$sql = "UPDATE ".TABLE_PREFIX."content 
			           SET ordering=ordering+1 
			         WHERE ordering>=$new_content_ordering 
			           AND content_parent_id=$new_content_parent_id 
			           AND content_id<>$content_id 
			           AND course_id=$_course_id";
//			$result = mysql_query($sql, $this->db);
			$this->contentDAO->execute($sql);

			$sql	= "UPDATE ".TABLE_PREFIX."content 
			              SET content_parent_id=$new_content_parent_id, ordering=$new_content_ordering 
			            WHERE content_id=$content_id AND course_id=$_course_id";
//			$result	= mysql_query($sql, $this->db);
			$this->contentDAO->execute($sql);
		}
	}
	
	function deleteContent($content_id) {
		global $_current_user, $_course_id;
		
		if (!isset($_current_user) || !$_current_user->isAuthor($this->course_id) && !$_current_user->isAdmin()) {
			return false;
		}

		/* check if exists */
//		$sql	= "SELECT ordering, content_parent_id FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
//		$result	= mysql_query($sql, $this->db);
//		if (!($row = @mysql_fetch_assoc($result)) ) {
		if (!($row = $this->getContentPage($content_id)) ) {
			return false;
		}
		$ordering			= $row['ordering'];
		$content_parent_id	= $row['content_parent_id'];

		/* check if this content has sub content	*/
		$children = $this->_menu[$content_id];

		if (is_array($children) && (count($children)>0) ) {
			/* delete its children recursively first*/
			foreach ($children as $x => $info) {
				if ($info['content_id'] > 0) {
					$this->deleteContentRecursive($info['content_id']);
				}
			}
		}

		$this->contentDAO->Delete($content_id);

		/* re-order the rest of the content */
		$sql = "UPDATE ".TABLE_PREFIX."content SET ordering=ordering-1 WHERE ordering>=$ordering AND content_parent_id=$content_parent_id AND course_id=$_course_id";
		$this->contentDAO->execute($sql);
		
		// unset last-visited content id
		require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');
		$userCoursesDAO = new UserCoursesDAO();
		$userCoursesDAO->UpdateLastCid($_SESSION['user_id'], $_course_id, 0);
		
		unset($_SESSION['s_cid']);
		unset($_SESSION['from_cid']);
		
		/* delete this content page					*/
//		$sql	= "DELETE FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
//		$result = mysql_query($sql, $this->db);

		/* delete this content from member tracking page	*/
//		$sql	= "DELETE FROM ".TABLE_PREFIX."member_track WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
//		$result = mysql_query($sql, $this->db);

//		$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$content_id OR related_content_id=$content_id";
//		$result = mysql_query($sql, $this->db);

		/* delete the content tests association */
//		$sql	= "DELETE FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id=$content_id";
//		$result = mysql_query($sql, $this->db);

		/* delete the content forum association */
//		$sql	= "DELETE FROM ".TABLE_PREFIX."content_forums_assoc WHERE content_id=$content_id";
//		$result = mysql_query($sql, $this->db);

		/* Delete all AccessForAll contents */
//		require_once(TR_INCLUDE_PATH.'classes/A4a/A4a.class.php');
//		$a4a = new A4a($content_id);
//		$a4a->deleteA4a();

		/* remove the "resume" to this page, b/c it was deleted */
//		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET last_cid=0 WHERE course_id=$_SESSION[course_id] AND last_cid=$content_id";
//		$result = mysql_query($sql, $this->db);

		return true;
	}


	/* private. call from deleteContent only. */
	function deleteContentRecursive($content_id) {
		/* check if this content has sub content	*/
		$children = $this->_menu[$content_id];

		if (is_array($children) && (count($children)>0) ) {
			/* delete its children recursively first*/
			foreach ($children as $x => $info) {
				if ($info['content_id'] > 0) {
					$this->deleteContent($info['content_id']);
				}
			}
		}

		// delete this content page
		$this->contentDAO->Delete($content_id);
//		$sql	= "DELETE FROM ".TABLE_PREFIX."content WHERE content_id=$content_id AND course_id=$_SESSION[course_id]";
//		$result = mysql_query($sql, $this->db);

		/* delete this content from member tracking page	*/
//		$sql	= "DELETE FROM ".TABLE_PREFIX."member_track WHERE content_id=$content_id";
//		$result = mysql_query($sql, $this->db);

		/* delete the content tests association */
//		$sql	= "DELETE FROM ".TABLE_PREFIX."content_tests_assoc WHERE content_id=$content_id";
//		$result = mysql_query($sql, $this->db);
	}

	function getContentPage($content_id) {
		include_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
		$contentDAO = new ContentDAO();
		return $contentDAO->get($content_id);
	}
	
	/** 
	 * Return a list of tests associated with the selected content
	 * @param	int		the content id that all tests are associated with it.
	 * @return	array	list of tests
	 * @date	Sep 10, 2008
	 * @author	Harris
	 */
	function getContentTestsAssoc($content_id){
		$sql	= "SELECT ct.test_id, t.title 
		             FROM (SELECT * FROM ".TABLE_PREFIX."content_tests_assoc 
		                    WHERE content_id=$content_id) AS ct 
		             LEFT JOIN ".TABLE_PREFIX."tests t ON ct.test_id=t.test_id
		            ORDER BY t.title";
		return $this->dao->execute($sql);
	}

	function cleanOutput($value) {
		return stripslashes(htmlspecialchars($value));
	}


	/* @See include/html/editor_tabs/properties.inc.php */
	/* Access: Public */
	function getNumSections() {
		return $this->num_sections;
	}

	/* Access: Public */
	function getMaxDepth() {
		return $this->max_depth;
	}

	/* Access: Public */
	function getContentLength() {
		return $this->content_length;
	}

	/* @See include/html/dropdowns/local_menu.inc.php */
	function getLocationPositions($parent_id, $content_id) {
		$siblings = $this->getContent($parent_id);
		for ($i=0;$i<count($siblings); $i++){
			if ($siblings[$i]['content_id'] == $content_id) {
				return $i;
			}
		}
		return 0;	
	}

	/* Access: Private */
	function getNumbering($content_id) {
		$path = $this->getContentPath($content_id);
		$parent = 0;
		$numbering = '';
		foreach ($path as $page) {
			$num = $this->getLocationPositions($parent, $page['content_id']) +1;
			$parent = $page['content_id'];
			$numbering .= $num.'.';
		}
		$numbering = substr($numbering, 0, -1);

		return $numbering;
	}

	function getPreviousContent($content_id) {
		if (is_array($this->_menu_in_order))
		{
			foreach ($this->_menu_in_order as $content_location => $this_content_id)
			{
				if ($this_content_id == $content_id) break;
			}
			
			for ($i=$content_location-1; $i >= 0; $i--)
			{
				$content_type = $this->_menu_info[$this->_menu_in_order[$i]]['content_type'];
				
				if ($content_type == CONTENT_TYPE_CONTENT || $content_type == CONTENT_TYPE_WEBLINK)
					return array('content_id'	=> $this->_menu_in_order[$i],
				    	         'ordering'		=> $this->_menu_info[$this->_menu_in_order[$i]]['ordering'],
				        	     'title'		=> $this->_menu_info[$this->_menu_in_order[$i]]['title']);
			}
		}
		return NULL;
	}
	
	/**
	 * return the array of the next content node of the given $content_id
	 * when $content_id = 0 or is not set, return the first content node
	 * @param $content_id
	 * @return an array of the next content node
	 */
	function getNextContent($content_id) {
		//echo("dentro a get");
		if (is_array($this->_menu_in_order))
		{
			// find out the location of the $content_id
			if (!$content_id) $content_location = 0; // the first content location when $content_id = 0 or is not set
			else
			{
				foreach ($this->_menu_in_order as $content_location => $this_content_id)
				{
					if ($this_content_id == $content_id) 
					{
						$content_location++;
						break;
					}
				}
			}
			
			// the next content node must be at or after the $content_location
			// and with the content type CONTENT or WEBLINK
			for ($i=$content_location; $i < count($this->_menu_in_order); $i++)
			{
				$content_type = $this->_menu_info[$this->_menu_in_order[$i]]['content_type'];
				
				if ($content_type == CONTENT_TYPE_CONTENT || $content_type == CONTENT_TYPE_WEBLINK)
					return(array('content_id'	=> $this->_menu_in_order[$i],
				    	         'ordering'		=> $this->_menu_info[$this->_menu_in_order[$i]]['ordering'],
				        	     'title'		=> $this->_menu_info[$this->_menu_in_order[$i]]['title']));
			}
		} 
		
		return NULL;
		
	}
	
	/* @See include/header.inc.php */
	function generateSequenceCrumbs($cid) {
		global $_base_path;

		$sequence_links = array();
		
		$first = $this->getNextContent(0); // get first
		//echo("TITOLO : ". $first['title']);
		if ($first) {
			$first['title'] = $this->getNumbering($first['content_id']).' '.$first['title'];
		}
		if ($first) {
			$first['url'] = $_base_path.'home/course/content.php?_cid='.$first['content_id'];
			$sequence_links['first'] = $first;
		}

		if (!$cid && $_SESSION['s_cid']) {
			$resume['title'] = $this->_menu_info[$_SESSION['s_cid']]['title'];

			$resume['url'] = $_base_path.'home/course/content.php?_cid='.$_SESSION['s_cid'];
			
			$sequence_links['resume'] = $resume;
		} else {
			if ($cid) {
				$previous = $this->getPreviousContent($cid);
			}
			$next = $this->getNextContent($cid ? $cid : 0);

			$next['url'] = $_base_path.'home/course/content.php?_cid='.$next['content_id'];
			if (isset($previous['content_id'])) {
				$previous['url'] = $_base_path.'home/course/content.php?_cid='.$previous['content_id'];
			}
			
			if (isset($previous['content_id'])) {
				$sequence_links['previous'] = $previous;
			} else if ($cid) {
//				$previous['url']   = $_base_path . url_rewrite('index.php');
//				$previous['url']   = $_base_path . 'home/course/index.php';
//				$previous['title'] = _AT('home');
//				$sequence_links['previous'] = $previous;
			}
			if (!empty($next['content_id'])) {
				$sequence_links['next'] = $next;
			}
		}

		return $sequence_links;
	}

	/** Generate javascript to hide all root content folders, except the one with current content page
	 * access: private
	 * @return print out javascript function initContentMenu()
	 */
	function initMenu(){
		global $_base_path, $_course_id;
		
		echo '
function initContentMenu() {
  tree_collapse_icon = "'.$_base_path.'images/tree/tree_collapse.gif";
  tree_expand_icon = "'.$_base_path.'images/tree/tree_expand.gif";
		
';
		
		$sql = "SELECT content_id
		          FROM ".TABLE_PREFIX."content 
		         WHERE course_id=$this->course_id
		           AND content_type = ".CONTENT_TYPE_FOLDER;
		$rows = $this->dao->execute($sql);

		// collapse all root content folders
		if (is_array($rows)) {
			foreach ($rows as $row) {
				echo '
  if (trans.utility.getcookie("t.c'.$_course_id.'_'.$row['content_id'].'") == "1")
  {
    jQuery("#folder"+'.$row['content_id'].').show();
    jQuery("#tree_icon"+'.$row['content_id'].').attr("src", tree_collapse_icon);
    jQuery("#tree_icon"+'.$row['content_id'].').attr("alt", "'._AT("collapse").'");
    jQuery("#tree_icon"+'.$row['content_id'].').attr("title", "'._AT("collapse").'");
  }
  else
  {
    jQuery("#folder"+'.$row['content_id'].').hide();
    jQuery("#tree_icon"+'.$row['content_id'].').attr("src", tree_expand_icon);
    jQuery("#tree_icon"+'.$row['content_id'].').attr("alt", "'._AT("expand").'");
    jQuery("#tree_icon"+'.$row['content_id'].').attr("title", "'._AT("expand").'");
  }
';
			}
		}
		
		// expand the content folder that has current content
		if (isset($_SESSION['s_cid']) && $_SESSION['s_cid'] > 0) {
			$current_content_path = $this->getContentPath($_SESSION['s_cid']);
			
			for ($i=0; $i < count($current_content_path)-1; $i++)
				echo '
  jQuery("#folder"+'.$current_content_path[$i]['content_id'].').show();
  jQuery("#tree_icon"+'.$current_content_path[$i]['content_id'].').attr("src", tree_collapse_icon);
  jQuery("#tree_icon"+'.$current_content_path[$i]['content_id'].').attr("alt", "'._AT("collapse").'");
  trans.utility.setcookie("t.c'.$_course_id.'_'.$current_content_path[$i]['content_id'].'", "1", 1);
';
		}
		echo '}'; // end of javascript function initContentMenu()
	}
	
	/* @See include/html/dropdowns/menu_menu.inc.php */
	function printMainMenu( ) {
		global $_current_user, $_course_id;
		
		if (!($this->course_id > 0)) {
			return;
		}
		
		global $_base_path;
		
		$parent_id    = 0;
		$depth        = 0;
		$path         = '';
		$children     = array();
		$truncate     = true;
		$ignore_state = true;

		$this->start = true;
		
		// if change the location of this line, change function switchEditMode(), else condition accordingly
		echo '<div id="editable_table">';
		
		if (isset($_current_user) && ($_current_user->isAuthor($this->course_id) || $_current_user->isAdmin()) && !Utility::isMobileTheme())
		{
		global $_config;
			echo ' <div class="menuedit">';
			if($_config['enable_template_structure'] == '1'){
			  echo '<a href="'.$_base_path.'home/editor/edit_content_struct.php?_course_id='.$_course_id.'">
    		  <img id="img_create_top_folder" src="'.$_base_path.'images/addstruct.gif" alt="'._AT('add_top_structure').'" title="'._AT('add_top_structure').'" style="border:0;height:1.2em" />
   				 </a>'."\n";
			}
			echo "\n".'
				<a href="'.$_base_path.'home/editor/edit_content_folder.php?_course_id='.$_course_id.'">
				  <img id="img_create_top_folder" src="'.$_base_path.'images/mfolder.gif" alt="'._AT("add_top_folder").'" title="'._AT("add_top_folder").'" style="border:0;height:1.2em" />
				</a>'."\n".'
				<a href="'.$_base_path.'home/editor/edit_content.php?_course_id='.$_course_id.'">
				  <img id="img_create_top_content" src="'.$_base_path.'images/mpage.gif" alt="'._AT("add_top_page").'" title="'._AT("add_top_page").'" style="border:0;height:1.2em" />
				</a>'."\n".'
				<a href="javascript:void(0)" onclick="javascript:switchEditMode();">
				  <img id="img_switch_edit_mode" src="'.$_base_path.'images/medit.gif" alt="'._AT("enter_edit_mode").'" title="'._AT("enter_edit_mode").'" style="border:0;height:1.2em" />
				</a>
			  </div>'."\n";
		}
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
		
		// javascript for inline editor
		echo '
<script type="text/javascript">
';
		// only expand the content folder that has the current content page
		$this->initMenu();
		
		echo '
function switchEditMode() {
  title_edit = "'._AT("enter_edit_mode").'";
  img_edit = "'.$_base_path.'images/medit.gif";
	
  title_view = "'._AT("exit_edit_mode").'";
  img_view = "'.$_base_path.'images/mlock.gif";
	
  if (jQuery("#img_switch_edit_mode").attr("src") == img_edit)
  {
    jQuery("#img_switch_edit_mode").attr("src", img_view);
    jQuery("#img_switch_edit_mode").attr("alt", title_view);
    jQuery("#img_switch_edit_mode").attr("title", title_view);
    inlineEditsSetup();
  }
  else
  { // refresh the content navigation to exit the edit mode
    jQuery.post("'. TR_BASE_HREF. 'home/course/refresh_content_nav.php?_course_id='.$_course_id.'", {}, 
                function(data) {jQuery("#editable_table").replaceWith(data); initContentMenu();});
  }
}

function inlineEditsSetup() {
  jQuery("#editable_table").find(".inlineEdits").each(function() {
    jQuery(this).text(jQuery(this).attr("title"));
  });
	
  var tableEdit = fluid.inlineEdits("#editable_table", {
    selectors : {
      text : ".inlineEdits",
      editables : "span:has(span.inlineEdits)"
    },
    defaultViewText: "",
      applyEditPadding: false,
      useTooltip: true,
      listeners: {
        afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
          if (newValue != oldValue) 
          {
            rtn = jQuery.post("'. TR_BASE_HREF. 'home/course/content_nav_inline_editor_submit.php", { "field":viewNode.id, "value":newValue }, 
                  function(data) {}, "json");
          }
        }
      }
   });

  jQuery(".fl-inlineEdit-edit").css("width", "80px")
};

initContentMenu();
</script>
';
		echo '</div>';
	}

	/* @See tools/sitemap/index.php */
	function printSiteMapMenu() {
		$parent_id    = 0;
		$depth        = 1;
		$path         = '';
		$children     = array();
		$truncate     = false;
		$ignore_state = true;

		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state, 'sitemap');
	}

	/* @See index.php */
	function printTOCMenu($cid, $top_num) {
		$parent_id    = $cid;
		$depth        = 1;
		$path         = $top_num.'.';
		$children     = array();
		$truncate     = false;
		$ignore_state = false;

		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
	}

	/* @See index.php include/html/dropdowns/local_menu.inc.php */
	function printSubMenu($cid, $top_num) {
		$parent_id    = $cid;
		$depth        = 1;
		$path         = $top_num.'.';
		$children     = array();
		$truncate     = true;
		$ignore_state = false;
	
		$this->start = true;
		$this->printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state);
	}

	/* @See include/html/menu_menu.inc.php	*/
	/* Access: PRIVATE */
	function printMenu($parent_id, $depth, $path, $children, $truncate, $ignore_state, $from = '') {
		global $cid, $_my_uri, $_base_path, $rtl, $substr, $strlen, $_current_user;
		static $temp_path;

		if (!isset($temp_path)) {
			if ($cid) {
				$temp_path	= $this->getContentPath($cid);
			} else {
				$temp_path	= $this->getContentPath($_SESSION['s_cid']);
			}
		}

		$highlighted = array();
		if (is_array($temp_path)) {
			foreach ($temp_path as $temp_path_item) {
				$_SESSION['menu'][$temp_path_item['content_id']] = 1;
				$highlighted[$temp_path_item['content_id']] = true;
			}
		}

		if ($this->start) {
			reset($temp_path);
			$this->start = false;
		}

		if ( isset($this->_menu[$parent_id]) && is_array($this->_menu[$parent_id]) ) {
			$top_level = $this->_menu[$parent_id];
			$counter = 1;
			$num_items = count($top_level);
			
//			if ($parent_id <> 0) echo '<li>';
			
//			echo '<ul id="folder'.$parent_id.$from.'">'."\n";
			echo '<div id="folder'.$parent_id.$from.'">'."\n";
			
			foreach ($top_level as $garbage => $content) {
				
				$link = '';
				//tests do not have content id
				$content['content_id'] = isset($content['content_id']) ? $content['content_id'] : '';

				if (!$ignore_state) {
					$link .= '<a name="menu'.$content['content_id'].'"></a>';
				}

				$on = false;

				if ( (($_SESSION['s_cid'] != $content['content_id']) || ($_SESSION['s_cid'] != $cid)) && ($content['content_type'] == CONTENT_TYPE_CONTENT || $content['content_type'] == CONTENT_TYPE_WEBLINK)) 
				{ // non-current content nodes with content type "CONTENT_TYPE_CONTENT"
					if (isset($highlighted[$content['content_id']])) {
						$link .= '<strong>';
						$on = true;
					}

					//content test extension  @harris
					//if this is a test link.
					if (isset($content['test_id'])){
						$title_n_alt =  $content['title'];
						$in_link = $_base_path.'tests/preview.php?tid='.$content['test_id'].'&_cid='.$parent_id;
						$img_link = ' <img src="'.$_base_path.'images/check.gif" title="'.$title_n_alt.'" alt="'.$title_n_alt.'" />';
					} else {
						$in_link = $_base_path.'home/course/content.php?_cid='.$content['content_id'];
						$img_link = '';
					}
					
					$full_title = $content['title'];
//					$link .= $img_link . ' <a href="'.$_base_path.url_rewrite($in_link).'" title="';
					$link .= $img_link . ' <a href="'.$in_link.'" title="';
					$base_title_length = 29;

					$link .= $content['title'].'">';

					if ($truncate && ($strlen($content['title']) > ($base_title_length-$depth*4)) ) {
						$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, ($base_title_length-$depth*4)-4))).'...';
					}
//					$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, $base_title_length-4))).'...';
					
					if (isset($content['test_id']))
						$link .= $content['title'];
					else
						$link .= '<span class="inlineEdits" id="menu-'.$content['content_id'].'" title="'.$full_title.'">'.
						         $content['title'].'</span>';
					
					$link .= '</a>';
					if ($on) {
						$link .= '</strong>';
					}
					
					// instructors have privilege to delete content
					if (isset($_current_user) && ($_current_user->isAuthor($this->course_id) || $_current_user->isAdmin()) && !isset($content['test_id']) && !Utility::isMobileTheme()) {
					//catia
						if($content['optional'] == 1) 
						//1 the content is optional
							$link .= '<a href="'.$_base_path.'home/editor/delete_content.php?_cid='.$content['content_id'].'"><img src="'.TR_BASE_HREF.'images/x.gif" alt="'._AT("delete_content").'" title="'._AT("delete_content").'" style="border:0" class="delete_ex" /></a>';
						else
						//0 the content is mandatory
							$link .= '<img style="margin-left:2px" src="'.$_base_path.'images/must.png" title="'._AT('mandatory_content').'" class="mandatory_ex"/>';
					}
					
				} 
				else 
				{ // current content page & nodes with content type "CONTENT_TYPE_FOLDER"
					$base_title_length = 26;

					if (isset($highlighted[$content['content_id']])) {
						$link .= '<strong>';
						$on = true;
					}

					if ($content['content_type'] == CONTENT_TYPE_CONTENT || $content['content_type'] == CONTENT_TYPE_WEBLINK)
					{ // current content page
						$full_title = $content['title'];
						$link .= '<a href="'.$_my_uri.'"><img src="'.$_base_path.'images/clr.gif" alt="'._AT('you_are_here').': '.
						         $content['title'].'" height="1" width="1" border="0" /></a><strong style="color:red" title="'.$content['title'].'">'."\n";
						
						if ($truncate && ($strlen($content['title']) > ($base_title_length-$depth*4)) ) {
							$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, ($base_title_length-$depth*4)-4))).'...';
						}
//						$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, $base_title_length-4))).'...';
						$link .= '<a name="menu'.$content['content_id'].'"></a><span class="inlineEdits" id="menu-'.$content['content_id'].'" title="'.$full_title.'">'.
						         $content['title'].'</span></strong>';
						
						// instructors have privilege to delete content
						if (isset($_current_user) && ($_current_user->isAuthor($this->course_id) || $_current_user->isAdmin()) && !Utility::isMobileTheme()) {
						
								//catia
							if($content['optional'] == 1) 
							//1 the content is optional
								$link .= '<a href="'.$_base_path.'home/editor/delete_content.php?_cid='.$content['content_id'].'"><img src="'.TR_BASE_HREF.'images/x.gif" alt="'._AT("delete_content").'" title="'._AT("delete_content").'" style="border:0"  class="delete_ex" /></a>';
							else
							//0 the content is mandatory
								$link .= '<img src="'.$_base_path.'images/must.png" title="'._AT('mandatory_content').'" style="margin-left:2px;" class="mandatory_ex"/>';
						}
					}
					else
					{ // nodes with content type "CONTENT_TYPE_FOLDER"
//						$link .= '<a href="javascript:void(0)" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\'); "><img src="'.$_base_path.'images/clr.gif" alt="'._AT('content_folder').': '.$content['title'].'" height="1" width="1" border="0" /></a>'."\n";
						
						$full_title = $content['title'];
						if (isset($_current_user) && ($_current_user->isAuthor($this->course_id) || $_current_user->isAdmin()) && !Utility::isMobileTheme()) {
//							$link .= '<a href="'.$_base_path.url_rewrite("editor/edit_content_folder.php?_cid=".$content['content_id']).'" title="'.$full_title. _AT('click_edit').'">'."\n";
							$link .= '<a href="'.$_base_path.'home/editor/edit_content_folder.php?_cid='.$content['content_id'].'" title="'.$full_title. _AT('click_edit').'">'."\n";
						}
						else {
							$link .= '<span style="cursor:pointer" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\', \''._AT("expand").'\', \''._AT('collapse').'\', '.$this->course_id.'); ">'."\n";
						}
						
						if ($truncate && ($strlen($content['title']) > ($base_title_length-$depth*4)) ) {
							$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, ($base_title_length-$depth*4)-4))).'...';
						}
//						$content['title'] = htmlspecialchars(rtrim($substr(htmlspecialchars_decode($content['title']), 0, $base_title_length-4))).'...';
						if (isset($content['test_id']))
							$link .= $content['title'];
						else
							$link .= '<span class="inlineEdits" id="menu-'.$content['content_id'].'" title="'.$full_title.'">'.
							         $content['title'].'</span>';
						
						if (isset($_current_user) && ($_current_user->isAuthor($this->course_id) || $_current_user->isAdmin()) && !Utility::isMobileTheme()) {
							$link .= '</a>'."\n";
						}
						else {
							$link .= '</span>'."\n";
						}
						
						// instructors have privilege to delete content
						if (isset($_current_user) && ($_current_user->isAuthor($this->course_id) || $_current_user->isAdmin()) && !Utility::isMobileTheme()) {
							$link .= '<a href="'.$_base_path.'home/editor/delete_content.php?_cid='.$content['content_id'].'"><img src="'.TR_BASE_HREF.'images/x.gif" alt="'._AT("delete_content").'" title="'._AT("delete_content").'" style="border:0"  class="delete_ex" /></a>';
						}
//						echo '<div id="folder_content_'.$content['content_id'].'">';
					}
					
					if ($on) {
						$link .= '</strong>';
					}
				}

				if ($ignore_state) {
					$on = true;
				}

//				echo '<li>'."\n";
				echo '<span>'."\n";
				
				if ( isset($this->_menu[$content['content_id']]) && is_array($this->_menu[$content['content_id']]) ) {
					/* has children */
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						} else {
							echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						}
					}

					if (($counter == $num_items) && ($depth > 0)) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						$children[$depth] = 0;
					} else if ($counter == $num_items) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						$children[$depth] = 0;
					} else {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
						$children[$depth] = 1;
					}

					if ($_SESSION['s_cid'] == $content['content_id']) {
						if (is_array($this->_menu[$content['content_id']])) {
							$_SESSION['menu'][$content['content_id']] = 1;
						}
					}

					if (isset($_SESSION['menu'][$content['content_id']]) && $_SESSION['menu'][$content['content_id']] == 1) {
						if ($on) {
//							echo '<img src="'.$_base_path.'images/tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').'" class="img-size-tree" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\'); " />'."\n";
							echo '<a href="javascript:void(0)" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\', \''._AT("expand").'\', \''._AT('collapse').'\', '.$this->course_id.'); "><img src="'.$_base_path.'images/tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').'" class="img-size-tree" /></a>'."\n";
							
						} else {
							echo '<a href="'.$_my_uri.'collapse='.$content['content_id'].'">'."\n";
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').' '.$content['title'].'" class="img-size-tree" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\', \''._AT("expand").'\', \''._AT('collapse').'\', '.$this->course_id.'); " />'."\n";
							echo '</a>'."\n";
						}
					} else {
						if ($on) {
//							echo '<img src="'.$_base_path.'images/tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').'" class="img-size-tree" />'."\n";
							echo '<a href="javascript:void(0)" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\', \''._AT("expand").'\', \''._AT('collapse').'\', '.$this->course_id.'); "><img src="'.$_base_path.'images/tree/tree_collapse.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('collapse').'" border="0" width="16" height="16" title="'._AT('collapse').'" class="img-size-tree" /></a>'."\n";
							
						} else {
							echo '<a href="'.$_my_uri.'expand='.$content['content_id'].'">'."\n";
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_expand.gif" id="tree_icon'.$content['content_id'].$from.'" alt="'._AT('expand').'" border="0" width="16" height="16" 	title="'._AT('expand').' '.$content['title'].'" class="img-size-tree" onclick="javascript: trans.utility.toggleFolder(\''.$content['content_id'].$from.'\', \''._AT("expand").'\', \''._AT('collapse').'\', '.$this->course_id.'); " />';
							echo '</a>'."\n";
						}
					}

				} else {
					/* doesn't have children */
					if ($counter == $num_items) {
						for ($i=0; $i<$depth; $i++) {
							if ($children[$i] == 1) {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							} else {
								echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							}
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" class="img-size-tree" />'."\n";
					} else {
						for ($i=0; $i<$depth; $i++) {
							if ($children[$i] == 1) {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							} else {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
							}
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
					}
					echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_horizontal.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />'."\n";
				}

				echo $link;
				
				echo "\n<br /></span>\n\n";
				
				if ( $ignore_state || (isset($_SESSION['menu'][$content['content_id']]) && $_SESSION['menu'][$content['content_id']] == 1)) {

					$depth ++;

					$this->printMenu($content['content_id'],
										$depth, 
										$path.$counter.'.', 
										$children,
										$truncate, 
										$ignore_state,
										$from);

										
					$depth--;

				}
				$counter++;
			} // end of foreach
//			echo "</ul>";
//			if ($parent_id <> 0) print "</li>\n\n";
			print "</div>\n\n";
		}
	}

	/* @See include/html/editor_tabs/properties.inc.php
	   @See editor/arrange_content.php
	    $print_type: "movable" or "related_content"
	 */
	function printActionMenu($menu, $parent_id, $depth, $path, $children, $print_type = 'movable') {
		
		global $cid, $_my_uri, $_base_path, $rtl;

		static $end;

		$top_level = $menu[$parent_id];

		if ( is_array($top_level) ) {
			$counter = 1;
			$num_items = count($top_level);
			foreach ($top_level as $current_num => $content) {
				if (isset($content['test_id'])){
					continue;
				}

				$link = $buttons = '';

				echo '<tr>'."\n";
				
				if ($print_type == 'movable')
				{
					if ($content['content_id'] == $_POST['moved_cid']) {
						$radio_selected = ' checked="checked" ';
					}
					else {
						$radio_selected = '';
					}
				
					$buttons = '<td>'."\n".
					           '   <small>'."\n".
					           '      <input type="image" name="move['.$parent_id.'_'.$content['ordering'].']" src="'.$_base_path.'images/before.gif" alt="'._AT('before_topic', $content['title']).'" title="'._AT('before_topic', $content['title']).'" style="height:1.5em; width:1.9em;" />'."\n";

					if ($current_num + 1 == count($top_level))
						$buttons .= '      <input type="image" name="move['.$parent_id.'_'.($content['ordering']+1).']" src="'.$_base_path.'images/after.gif" alt="'._AT('after_topic', $content['title']).'" title="'._AT('after_topic', $content['title']).'" style="height:1.5em; width:1.9em;" />'."\n";
					
					$buttons .= '   </small>'."\n".
					           '</td>'."\n".
					           '<td>';
					
					if ($content['content_type'] == CONTENT_TYPE_FOLDER)
						$buttons .= '<input type="image" name="move['.$content['content_id'].'_1]" src="'.$_base_path.'images/child_of.gif" style="height:1.25em; width:1.7em;" alt="'._AT('child_of', $content['title']).'" title="'._AT('child_of', $content['title']).'" />';
					else
						$buttons .= '&nbsp;';
						
					$buttons .= '</td>'."\n".
					           '<td><input name="moved_cid" value="'.$content['content_id'].'" type="radio" id="r'.$content['content_id'].'" '.$radio_selected .'/></td>'."\n";
				}
				
				$buttons .= '<td>'."\n";
				if ($print_type == "related_content")
				{
					if ($content['content_type'] == CONTENT_TYPE_CONTENT || $content['content_type'] == CONTENT_TYPE_WEBLINK)
					{
						$link .= '<input type="checkbox" name="related[]" value="'.$content['content_id'].'" id="r'.$content['content_id'].'" ';
						if (isset($_POST['related']) && in_array($content['content_id'], $_POST['related'])) {
							$link .= ' checked="checked"';
						}
						$link .= ' />'."\n";
					}
				}	
				
				if ($content['content_type'] == CONTENT_TYPE_FOLDER)
				{
					$link .= '<img src="'.$_base_path.'images/folder.gif" class="map_folder"/>';
				}
				$link .= '&nbsp;<label for="r'.$content['content_id'].'">'.$content['title'].'</label>'."\n";

				if ( is_array($menu[$content['content_id']]) && !empty($menu[$content['content_id']]) ) {
					/* has children */

					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo $buttons;
							unset($buttons);
							if ($end && ($i==0)) {
								echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
							} else {
								echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16" />';
							}
						} else {
							echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
						}
					}

					if (($counter == $num_items) && ($depth > 0)) {
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
						$children[$depth] = 0;
					} else {
						echo $buttons;
						if (($num_items == $counter) && ($parent_id == 0)) {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
							$end = true;
						} else {
							echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
						}
						$children[$depth] = 1;
					}

					if ($_SESSION['s_cid'] == $content['content_id']) {
						if (is_array($menu[$content['content_id']])) {
							$_SESSION['menu'][$content['content_id']] = 1;
						}
					}

					if ($_SESSION['menu'][$content['content_id']] == 1) {
						echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'"  class="img-size-tree"/>';

					} else {
						echo '<img src="'.$_base_path.'images/tree/tree_disabled.gif" alt="'._AT('toggle_disabled').'" border="0" width="16" height="16" title="'._AT('toggle_disabled').'"  class="img-size-tree"/>';
					}

				} else {
					/* doesn't have children */
					if ($counter == $num_items) {
						if ($depth) {
							echo $buttons;
							for ($i=0; $i<$depth; $i++) {
								if ($children[$i] == 1) {
									if ($end && ($i == 0)) {
										echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
									} else {
										echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
									}
								} else {
									echo '<img src="'.$_base_path.'images/clr.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
								}
							}
						} else {
							echo $buttons;
						}
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" border="0"  class="img-size-tree"/>';
					} else {
						if ($depth) {
							echo $buttons;
							$print = false;
							for ($i=0; $i<$depth; $i++) {
								if ($children[$i] == 1) {
									if ($end && !$print) {
										$print = true;
										echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16" class="img-size-tree" />';
									} else {
										echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_vertline.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
									}
								} else {
									echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_space.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
								}
							}
							$print = false;
						} else {
							echo $buttons;
						}
		
						echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
					}
					echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_horizontal.gif" alt="" border="0" width="16" height="16"  class="img-size-tree"/>';
				}

				echo '<small> ' . $link . '</small></td>'."\n".'</tr>'."\n";

				$this->printActionMenu($menu,
									$content['content_id'],
									++$depth, 
									$path.$counter.'.', 
									$children,
									$print_type);
				$depth--;

				$counter++;
			}
		}
	}
}

?>