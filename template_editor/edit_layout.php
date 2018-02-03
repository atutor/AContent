<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2016                                                   */
/* ATutorSpaces Inc.                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= "\n".'<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />';
$_custom_head .= "\n".'<script type="text/javascript" src="template_editor/js/layout.js"></script>';

//$_custom_head .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="'.$_base_href.'include/jscripts/jquery.js"></script>'."\n";
$_custom_head .='<script type="text/javascript" src="'.$_base_href.'include/jscripts/html2canvas/html2canvas.js"></script>'."\n";
$_custom_head .='<script type="text/javascript">jQuery.noConflict();</script>'."\n";

$template=strip_tags($_GET['temp']);
$_custom_css = "templates/layouts"."/". $template."/".$template.".css";
$_custom_head .= "\n".'<link rel="stylesheet" href="'.$_custom_css.'" type="text/css" />';

$type="layouts";
$temp=htmlspecialchars($_GET['temp'], ENT_QUOTES, 'UTF-8');
if($_POST['submit'] == "Cancel") {
    $msg->addFeedback('CANCELLED');
    header('Location:index.php?tab='.$type.SEP.'temp='.$temp);
    exit;
}


require('classes/TemplateCommons.php');
$commons=new TemplateCommons('../templates');

// non existing template name
if(!$commons->template_exists('layouts', $template)) {
    header('Location: index.php');
    exit;
}
if(!is_writable($_SERVER['DOCUMENT_ROOT'].$_base_path.'templates/'.$type) || !is_writable($_SERVER['DOCUMENT_ROOT'].$_base_path.'templates/')){
    $msg->addWarning('TEMPLATE_DIR_NOT_WRITABLE');
    $temp_unwritable = TRUE;
}else{
    $msg->addFeedback('TEMPLATE_DIR_WRITABLE');
}
if(isset ($_POST['submit'])) { 
    if(!isset($temp_unwritable)){
        $commons->save_file("layouts/".$template,$template.".css",$_POST['css_text']);
        $msg->addFeedback('TEMPLATE_UPDATED');
        header('Location:'.$_SERVER['PHP_SELF'].'?mode='.intval($_POST['edit_mode']).SEP.'temp='.urlencode($template).SEP.'lastelement='.urlencode($_REQUEST['lastelement']));
        exit;
    }
}
if(isset ($_POST['upload'])) {
    echo $commons->upload_image("layouts/".$template."/".$template,"");
    header('Location:'.$_SERVER['PHP_SELF'].'?temp='.urlencode($template).SEP.'lastelement='.urlencode($_REQUEST['lastelement']));
    exit;
    
}
if(isset ($_POST['generate_scrn'])) {
    echo $commons->upload_image("layouts/".$template,"screenshot-". $template.".png");
    header('Location:'.$_SERVER['PHP_SELF'].'?temp='.urlencode($template).SEP.'lastelement='.urlencode($_REQUEST['lastelement']));
    exit;
}

$css_path=realpath("../templates/layouts")."/". $template."/".$template.".css";
$screenshot_path=realpath("../templates/layouts")."/". $template."/screenshot-".$template.".png";

// Check if the template DIR is writable
if(!is_writable('../templates')){
    $msg->addWarning('TEMPLATE_DIR_NOT_WRITABLE');
    $temp_unwritable = TRUE;
}else{
    $msg->addFeedback('TEMPLATE_DIR_WRITABLE');
    $temp_unwritable = TRUE;
}

require(TR_INCLUDE_PATH.'header.inc.php');

if(file_exists($css_path)) {
    $savant->assign('css_code', file_get_contents($css_path));
} else {
    $savant->assign('css_code', "");
}
if(file_exists($screenshot_path)){
     $savant->assign('screenshot',true);
}

$savant->assign('template', $template);

$savant->assign('lastelement', urldecode($_REQUEST['lastelement']));
$savant->assign('image_list', $commons->get_image_list("layouts/".$template."/".$template));
$savant->assign('base_path', $_base_path);
$savant->assign('referer', $_SERVER['HTTP_REFERER']);
$savant->display('template_editor/layout_tool.tmpl.php');


?>

<?php
require(TR_INCLUDE_PATH.'footer.inc.php');

?>