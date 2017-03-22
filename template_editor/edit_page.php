<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />'."\n";
$_custom_head .= '<script type="text/javascript" src="template_editor/js/page.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>'."\n";



//$_custom_head .='      <script type="text/javascript" src="https://github.com/niklasvh/html2canvas/releases/download/0.4.1/html2canvas.js"></script>'."\n"; 
//$_custom_head .='      <script type="text/javascript" src="http://www.nihilogic.dk/labs/canvas2image/base64.js"></script>'."\n";   
//$_custom_head .='      <script type="text/javascript" src="http://www.nihilogic.dk/labs/canvas2image/canvas2image.js"></script>'."\n";
//$_custom_head .='<script type="text/javascript" src="'.$_base_href.'include/jscripts/html2canvas/dist/thumbnailer.js"></script>'."\n";
$_custom_head .='<script type="text/javascript" src="'.$_base_href.'include/jscripts/html2canvas/html2canvas.js"></script>'."\n";
//$_custom_head .='<script type="text/javascript" src="'.$_base_href.'include/jscripts/html2canvas/dist/base64.js"></script>'."\n";
//$_custom_head .='<script type="text/javascript" src="'.$_base_href.'include/jscripts/html2canvas/dist/canvas2image.js"></script>'."\n";
//$_custom_head .= '<script type="text/javascript" src="include/jscripts/jquery.js"></script>'."\n";
$_custom_head .='<script type="text/javascript">jQuery.noConflict();</script>'."\n";

if($_POST['submit'] == _AT('cancel')){
    $msg->addFeedback('CANCELLED');
    header('Location: index.php?tab=pages');
    exit;
}


require('classes/TemplateCommons.php');

$commons=new TemplateCommons('../templates');
$type = "page_templates";
$template=$_GET['temp'];
// non existing template name
if(!is_writable($_SERVER['DOCUMENT_ROOT'].$_base_path.'templates/'.$type)){
    $msg->addWarning('TEMPLATE_DIR_NOT_WRITABLE');
    $temp_unwritable = TRUE;
}else{
    $msg->addFeedback('TEMPLATE_DIR_WRITABLE');
}

if(!$commons->template_exists('page_templates', $template)) {
    if(!isset($temp_unwritable)){
    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    Header('Location: index.php');
    exit;
    }
}

if(isset ($_POST['submit'])) {
    $commons->save_file("page_templates/".$template,$template.".html",$_POST['page_text']);
}
if(isset ($_POST['uploadscrn'])) {
   $commons->upload_image("page_templates/".$template,"screenshot.png");
}

require(TR_INCLUDE_PATH.'header.inc.php');
$html_path=realpath("../templates/page_templates")."/". $template."/".$template.".html";
$screenshot_path=realpath("../templates/page_templates")."/". $template."/screenshot.png";

if(file_exists($html_path)) $savant->assign('html_code', file_get_contents($html_path));
else $savant->assign('html_code', "");
if(file_exists($screenshot_path)) $savant->assign('screenshot',true);
$savant->assign('template', $template);
$savant->assign('page_text', $_POST['page_text']);
$savant->assign('types', $type);
$savant->assign('base_path', $_base_path);
$savant->assign('referer', $_SERVER['HTTP_REFERER']);
$savant->display('template_editor/page_tool.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');

?>