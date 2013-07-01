<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

require(TR_INCLUDE_PATH.'header.inc.php');

$template=$_GET['temp'];
echo $template. "</br>";

$xmlpath=realpath("../templates/structures")."/". $template."/content.xml";
$xmlDoc = new DOMDocument();
$xmlDoc->load($xmlpath);

$x = $xmlDoc->documentElement;
display($x,"--");
require(TR_INCLUDE_PATH.'footer.inc.php');

function display($element,$prfx) {
    echo "<ol>";
    foreach ($element->childNodes AS $item) {
        if($item->nodeName!="#text")
            echo "<li>". $item->nodeName. "  (".$item->getAttribute('name').")</li>";
        if($item->hasChildNodes()) {
            echo "<li>";
            display($item,$prfx."- - ");
            echo "</li>";
        }
    }
    echo "</ol>";
}
?>
