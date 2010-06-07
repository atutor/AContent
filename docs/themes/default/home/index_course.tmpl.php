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

// determine the from url is search.php or index.php
global $_base_path;
require_once(TR_INCLUDE_PATH.'classes/Utility.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UserCoursesDAO.class.php');

// This template is called by home/index.php and home/search.php
// Find out the caller URL and direct the page back to the caller 
// after adding/removing the course from "My Courses"
$caller_url_parts = explode('/', $_SERVER['PHP_SELF']); 
$caller_script = $caller_url_parts[count($caller_url_parts)-1];

// construct the caller query string
//if (count($_GET) > 0)
//{
////	$url_param = '?';
//	$counter = 0;
//	foreach ($_GET as $param => $value)
//	{
//		$counter++;
//		if ($param == 'action' || $param == 'cid') 
//		{
//			$counter--;
//			continue;
//		}
//		else if ($counter > 1)
//		{
//			$url_param .= '&';
//		}
//		$url_param .= $param.'='.urlencode($value);
//	}
//}

if (count($_GET) > 0)
{
	foreach ($_GET as $param => $value)
	{
		if ($param == 'action' || $param == 'cid') 
			continue;
		else
			$url_param .= $param.'='.urlencode($value).'&';
	}
}

$caller_url = $caller_script. '?'.(isset($url_param) ? $url_param : '');
$url_param = substr($url_param, 0, -1);

if (isset($this->search_text)) $keywords = explode(' ', $this->search_text);
if (isset($_SESSION['user_id'])) $userCoursesDAO = new UserCoursesDAO();
?>

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo $this->title; ?></legend>
<?php if (is_array($this->courses)) { ?>
  <div class="results">
    <ol>
<?php 
	$num_results = count($this->courses);
	
	// if the requested page number exceeds the max number of pages, set the current page to the last page
	$num_pages = ceil($num_results / RESULTS_PER_PAGE);
	if ($this->curr_page_num > $num_pages) $this->curr_page_num = $num_pages;
	
	
	$start_num = ($this->curr_page_num - 1) * RESULTS_PER_PAGE;
	$end_num = min($this->curr_page_num * RESULTS_PER_PAGE, $num_results);
?>
      <li class="course" style="font-weight:bold">
        <div><?php echo _AT('results').' '.($start_num+1) .'-'.$end_num.' '._AT('of').' '.$num_results.' '. ($this->search_text<>'' ? _AT('for').' "<em>'.$this->search_text.'</em>"':'');?>
          <span style="float: right"><img src="<?php echo TR_BASE_HREF; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/my_own_course.gif" alt="<?php echo _AT('my_authoring_course'); ?>" title="<?php echo _AT('my_authoring_course'); ?>" />&nbsp;&nbsp;&nbsp;<?php echo _AT('authoring_img_info'); ?></span>
        </div>
				
      </li>
<?php 	for ($i = $start_num; $i < $end_num; $i++) {
		// only display the first 200 character of course description
		$row = $this->courses[$i];
		
		// find whether the current user is the author of this course
		$user_role = $userCoursesDAO->get($_SESSION['user_id'], $row['course_id']);
		
		$len_description = 330;
		if (strlen($row['description']) > $len_description)
			$description = Utility::highlightKeywords(substr($row['description'], 0, $len_description), $keywords).' ...';
		else
			$description = Utility::highlightKeywords($row['description'], $keywords);
?>
      <li class="course">
        <h3>
<?php if ($user_role['role'] == TR_USERROLE_AUTHOR) {?>
          <img src="<?php echo TR_BASE_HREF; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/my_own_course.gif" alt="<?php echo _AT('my_authoring_course'); ?>" title="<?php echo _AT('my_authoring_course'); ?>" />
<?php } else {?>
          <img src="<?php echo TR_BASE_HREF; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/others_course.png" alt="<?php echo _AT('others_course'); ?>" title="<?php echo _AT('others_course'); ?>" />
<?php } ?>
          <a href="<?php echo TR_BASE_HREF; ?>home/course/index.php?_course_id=<?php echo $row['course_id']; ?>"><?php echo Utility::highlightKeywords($row['title'], $keywords); ?></a>
<?php if ($user_role['role'] == TR_USERROLE_VIEWER) {?>
          <a href="<?php echo TR_BASE_HREF; ?>home/<?php echo $caller_url; ?>action=remove&cid=<?php echo $row['course_id']; ?>">
            <img src="<?php echo TR_BASE_HREF; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/delete.gif" alt='<?php echo htmlspecialchars(_AT('remove_from_list')); ?>' title='<?php echo htmlspecialchars(_AT('remove_from_list')); ?>' border="0" />
          </a>
<?php } if ($user_role['role'] == NULL && $_SESSION['user_id']>0) {?>
          <a href="<?php echo TR_BASE_HREF; ?>home/<?php echo $caller_url; ?>action=add&cid=<?php echo $row['course_id'];?>">
            <img src="<?php echo TR_BASE_HREF; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/add.gif" alt="<?php echo htmlspecialchars(_AT('add_into_list')); ?>" title="<?php echo htmlspecialchars(_AT('add_into_list')); ?>" border="0" />
          </a>
<?php }?>
          <a href="<?php echo TR_BASE_HREF; ?>home/imscc/ims_export.php?course_id=<?php echo $row['course_id']; ?>">
            <img src="<?php echo TR_BASE_HREF; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/export.png" alt="<?php echo _AT('export'); ?>" title="<?php echo _AT('export'); ?>" border="0" />
          </a>
        </h3>
        <div><?php echo $description; ?></div>
      </li>				
<?php } // end of foreach; ?>
    </ol>
<?php 	print_paginator($this->curr_page_num, $num_results, $url_param, RESULTS_PER_PAGE);?>
  </div>
<?php } // end of if
else {
	echo _AT("no_results_for_keywords", $this->search_text);
} // end of else?>
</fieldset>
</div>
