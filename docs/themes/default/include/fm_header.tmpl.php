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

if (!defined('TR_INCLUDE_PATH')) { exit; }

/* available header.tmpl.php variables:
 *
 * ======================================
 * top_level_pages           array(array('url', 'title'))     the top level pages. ATutor default creates tabs.
 * section_title             string                           the name of the current section. either name of the course, administration, my start page, etc.
 * page_title                string                           the title of the current page.
 * path                      array(array('url', 'title'))     the path to the current page.
 * back_to_page              array('url', 'title')            the link back to the part of the current page, if needed.
 * current_top_level_page    string                           full url to the current top level page in "top_leve_pages"
 * current_sub_level_page    string                           full url to the current sub level page in the "sub_level_pages"
 * sub_level_pages           array(array('url', 'title'))     the sub level pages.
 */

// will have to be moved to the header.inc.php
global $system_courses;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $this->lang_code; ?>"> 

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo DEFAULT_LANGUAGE_CODE; ?>" lang="<?php echo DEFAULT_LANGUAGE_CODE; ?>"> 

<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="Transformable - Copyright 2009 by ATRC http://atrc.utoronto.ca/" />
	<meta name="keywords" content="Transformable,free, open source, accessibility checker, accessibility reviewer, accessibility evaluator, accessibility evaluation, WCAG evaluation, 508 evaluation, BITV evaluation, evaluate accessibility, test accessibility, review accessibility, ATRC, WCAG 2, STANCA, BITV, Section 508." />
	<meta name="description" content="Transformable is a Web accessibility evalution tool designed to help Web content developers and Web application developers ensure their Web content is accessible to everyone regardless to the technology they may be using, or their abilities or disabilities." />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
<?php echo $this->rtl_css; ?>
	<script src="<?php echo $this->base_path; ?>include/jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
	<script src="<?php echo $this->base_path; ?>jscripts/infusion/jquery.autoHeight.js" type="text/javascript"></script>
	<script src="<?php echo $this->base_path; ?>include/jscripts/handleAjaxResponse.js" type="text/javascript"></script>
	<script src="<?php echo $this->base_path; ?>include/jscripts/transformable.js" type="text/javascript"></script>
<?php echo $this->custom_css; ?>
</head>

<body onload="<?php echo $this->onload; ?>"><div class="input-form"><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<br /><div align="right"><a href="javascript:window.close()"><?php echo _AT('close_file_manager'); ?></a></div>
<a name="content" title="<?php echo _AT("content_start"); ?>"></a>
<div id="ajax-msg">
</div>

<?php global $msg; $msg->printAll(); ?>