<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
$template=strip_tags($addslashes($_GET['temp']));
header('Location: edit_layout.php?temp='.$template);

?>