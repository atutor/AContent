<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<script type="text/javascript" src="template_editor/js/structure.js"></script>';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/jquery.ui.sortable.js"></script>';

require(TR_INCLUDE_PATH.'header.inc.php');
require('classes/TemplateCommons.php');
$commons=new TemplateCommons('../templates');

$template=$_GET['temp'];
if(!$commons->template_exists('structure', $template)) {
    Header('Location: index.php');
    exit;
}
if(isset ($_POST['submit'])) {
    $dom=$commons->parse_to_XML($_POST['xml_text']);
    $commons->save_xml($dom, "structures/".$template, "content.xml");
}

$xmlpath=realpath("../templates/structures")."/". $template."/content.xml";
$xmlDoc = new DOMDocument();
$xmlDoc->load($xmlpath);
$x = $xmlDoc->documentElement;
$savant->assign('template', $template);
$savant->assign('xml_script', $xmlDoc->saveXML($xmlDoc->documentElement));
$savant->assign('image_path', TR_BASE_HREF.'images');
$savant->display('template_editor/structure_tool.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');

?>
