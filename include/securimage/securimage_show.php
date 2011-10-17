<?php
$_user_location	= 'public';
define('TR_INCLUDE_PATH', '../');
require (TR_INCLUDE_PATH.'vitals.inc.php');
session_start();

include 'securimage.php';

$img = new securimage();

$img->show(); // alternate use:  $img->show('/path/to/background.jpg');
?>
