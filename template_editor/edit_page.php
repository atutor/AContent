<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/page.js"></script>';

require(TR_INCLUDE_PATH.'header.inc.php');
require('classes/TemplateCommons.php');

$commons=new TemplateCommons('../templates');

$template=$_GET['temp'];
// non existing template name
if(!$commons->template_exists('page_template', $template)) {
    Header('Location: index.php');
    exit;
}
if(isset ($_POST['submit'])) {
    $commons->save_file("page_template/".$template,$template.".html",$_POST['page_text']);
}
if(isset ($_POST['uploadscrn'])) {
    echo $commons->upload_image("page_template/".$template,"screenshot.png");
}

$html_path=realpath("../templates/page_template")."/". $template."/".$template.".html";
$screenshot_path=realpath("../templates/page_template")."/". $template."/screenshot.png";

if(file_exists($html_path)) $savant->assign('html_code', file_get_contents($html_path));
else $savant->assign('html_code', "");
if(file_exists($screenshot_path)) $savant->assign('screenshot',true);
$savant->assign('template', $template);
$savant->assign('base_path', $_base_path);
$savant->display('template_editor/page_tool.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');

?>