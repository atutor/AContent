<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/layout.js"></script>';

require(TR_INCLUDE_PATH.'header.inc.php');
require('classes/TemplateCommons.php');
$commons=new TemplateCommons('../templates');

$template=$_GET['temp'];
// non existing template name
if(!$commons->template_exists('layout', $template)) {
    Header('Location: index.php');
    exit;
}
$css_path=realpath("../templates/layout")."/". $template."/".$template.".css";

$savant->assign('template', $template);
$savant->assign('css_code', file_get_contents($css_path));
$savant->display('template_editor/layout_tool.tmpl.php');

?>

<?php
require(TR_INCLUDE_PATH.'footer.inc.php');

?>