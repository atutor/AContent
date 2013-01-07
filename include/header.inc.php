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

if (!defined('TR_INCLUDE_PATH')) { exit; }

//Timer, to display "Time Spent" in footer, debug information
$mtime = microtime(); 
$mtime = explode(' ', $mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime; 
//Timer Ends

global $myLang;
global $savant;
global $onload;
global $_custom_css;
global $_custom_head;
global $_base_path;
global $_pages;
global $framed, $popup;
global $_current_user, $_course_id, $_sequence_links, $_tool_shortcuts;
global $validate_content;
global $contentManager;
global $course_base_href, $content_base_href;

include_once(TR_INCLUDE_PATH.'classes/Menu.class.php');

$menu =new Menu();
$_top_level_pages = $menu->getTopPages();

$_all_pages =  $menu->getAllPages();

$_current_root_page = $menu->getRootPage();

$_breadcrumb_path = $menu->getPath();

$current_page = $menu->getCurrentPage();

$_sub_menus = $menu->getSubMenus();
$back_to_page = $menu->getBackToPage();
$_pages = $menu->getAllPages();   // add "param" element into $_pages items





$savant->assign('path', $_breadcrumb_path);
$savant->assign('top_level_pages', $_top_level_pages);
$savant->assign('current_top_level_page', $_current_root_page);
$savant->assign('sub_menus', $_sub_menus);
if ($back_to_page <> '') $savant->assign('back_to_page', $back_to_page);
$savant->assign('current_page', $_base_path.$current_page);

if (isset($_pages[$current_page]['title'])) {
	$_page_title = $_all_pages[$current_page]['title'];
} else {
	$_page_title = _AT($_all_pages[$current_page]['title_var']);
}
$savant->assign('page_title', htmlspecialchars($_page_title, ENT_COMPAT, "UTF-8"));
if($_SESSION['course_id'] && $_current_user && $_current_user->isAdmin()){
	$owner_dao = new DAO();
	$sql = "SELECT U.first_name, U.last_name, U.login, U.user_id FROM ".TABLE_PREFIX."users U, ".TABLE_PREFIX."courses C WHERE C.course_id = $_SESSION[course_id] AND C.user_id = U.user_id";
	$course_owner = $owner_dao->execute($sql);
	$savant->assign('course_owner', $course_owner['0']);
}
if ($_course_id > 0) {
	$sequence_links = $contentManager->generateSequenceCrumbs($_content_id);
	$savant->assign('sequence_links', $sequence_links);
}

if (isset($_current_user))
{
  $savant->assign('user_name', $_current_user->getUserName());
  if ($_course_id > 0) $savant->assign('isAuthor', $_current_user->isAuthor($_course_id));
  if( $_current_user->isAdmin()){
  	$savant->assign('isAdmin',  $_current_user->isAdmin());
  }
}

if ($myLang->isRTL()) {
	$savant->assign('rtl_css', '<link rel="stylesheet" href="'.$_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME'].'/rtl.css" type="text/css" />');
} else {
	$savant->assign('rtl_css', '');
}



$_tmp_base_href = TR_BASE_HREF;
if (isset($course_base_href) || isset($content_base_href)) {
	$_tmp_base_href .= $course_base_href;
	if ($content_base_href) {
		$_tmp_base_href .= $content_base_href;
	}
}

// Setup array of content tools for shortcuts tool bar.
$savant->assign('tool_shortcuts', $_tool_shortcuts);  // array of content tools for shortcuts tool bar.

$savant->assign('content_base_href', $_tmp_base_href);
$savant->assign('lang_code', $_SESSION['lang']);
$savant->assign('lang_charset', $myLang->getCharacterSet());
$savant->assign('base_path', $_base_path);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);

$theme_img  = $_base_path . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);




// course categories for search tool
require_once(TR_INCLUDE_PATH.'classes/DAO/CourseCategoriesDAO.class.php');
$courseCategoriesDAO = new CourseCategoriesDAO();
$savant->assign('categories', $courseCategoriesDAO->getAll());

// get custom css
$custom_css = '';
if (isset($_custom_css)) {
	$custom_head = '<link rel="stylesheet" href="'.$_custom_css.'" type="text/css" />';
}

if (isset($_custom_head)) {
	$custom_head .= '
' . $_custom_head;
}

if (isset($_pages[$current_page]['guide'])) 
{
	$script_name = substr($_SERVER['PHP_SELF'], strlen($_base_path));
	$savant->assign('guide', TR_GUIDES_PATH .'index.php?p='. htmlentities_utf8($script_name));
}

$savant->assign('custom_head', $custom_head);

if ($onload) $savant->assign('onload', $onload);
$savant->assign('course_id', $_course_id);

if ($framed || $popup) {
	$savant->assign('framed', 1);
    $savant->assign('popup', 1);
    $savant->display('include/fm_header.tmpl.php');

} else {
	$savant->display('include/header.tmpl.php');
}

?>
