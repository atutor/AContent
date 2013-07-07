<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
$_custom_head .= '<script type="text/javascript" src="template_editor/js/structure.js"></script>';
$_custom_head .= '<script type="text/javascript" src="template_editor/js/jquery.ui.sortable.js"></script>';

require(TR_INCLUDE_PATH.'header.inc.php');

$template=$_GET['temp'];

$xmlpath=realpath("../templates/structures")."/". $template."/content.xml";
$xmlDoc = new DOMDocument();

$xmlDoc->load($xmlpath);
$x = $xmlDoc->documentElement;
$savant->assign('xml_script', $xmlDoc->saveXML($xmlDoc->documentElement));
$savant->display('template_editor/structure_tool.tmpl.php');

require(TR_INCLUDE_PATH.'footer.inc.php');

?>
