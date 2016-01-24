<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/page.js"></script>';

if($_POST['submit'] == _AT('cancel')){
    $msg->addFeedback('CANCELLED');
    header('Location: index.php?tab=pages');
    exit;
}


require('classes/TemplateCommons.php');

$commons=new TemplateCommons('../templates');

$template=$_GET['temp'];
// non existing template name

if(!$commons->template_exists('page_templates', $template)) {
    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    Header('Location: index.php');
    exit;
}

if(isset ($_POST['submit'])) {
    $commons->save_file("page_templates/".$template,$template.".html",$_POST['page_text']);
}
if(isset ($_POST['uploadscrn'])) {
    echo $commons->upload_image("page_templates/".$template,"screenshot.png");
}
require(TR_INCLUDE_PATH.'header.inc.php');
$html_path=realpath("../templates/page_templates")."/". $template."/".$template.".html";
$screenshot_path=realpath("../templates/page_templates")."/". $template."/screenshot.png";

if(file_exists($html_path)) $savant->assign('html_code', file_get_contents($html_path));
else $savant->assign('html_code', "");
if(file_exists($screenshot_path)) $savant->assign('screenshot',true);
$savant->assign('template', $template);
$savant->assign('types', $type);
$savant->assign('base_path', $_base_path);
$savant->assign('referer', $_SERVER['HTTP_REFERER']);
$savant->display('template_editor/page_tool.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');

?>