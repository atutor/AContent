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

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/structure.js"></script>';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/jquery.ui.sortable.js"></script>';

if($_POST['submit'] == _AT('cancel')){
    header('Location: '. $_POST['referer']);
    exit;
}

require(TR_INCLUDE_PATH.'header.inc.php');
require('classes/TemplateCommons.php');
$commons=new TemplateCommons('../templates');

$template=$_GET['temp'];
// non existing template name
if(!$commons->template_exists('structure', $template)) {
    Header('Location: index.php');
    exit;
}
// save the changes
if(isset ($_POST['submit'])) {      
    $dom=$commons->parse_to_XML($_POST['xml_text']);
    $commons->save_xml($dom, "structures/".$template, "content.xml");
}
// edit an existing template
$xmlpath=realpath("../templates/structures")."/". $template."/content.xml";
$xmlDoc = new DOMDocument();
$xmlDoc->load($xmlpath);
$x = $xmlDoc->documentElement;
$page_temp_list=$commons->get_template_list("page_template");

$savant->assign('template', $template);
$savant->assign('xml_script', $xmlDoc->saveXML($xmlDoc->documentElement));
$savant->assign('image_path', TR_BASE_HREF.'images');
$savant->assign('page_temp_list', $page_temp_list);
$savant->assign('referer', $_SERVER['HTTP_REFERER']);
$savant->display('template_editor/structure_tool.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');

?>
