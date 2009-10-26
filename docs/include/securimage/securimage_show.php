<?php
$_user_location	= 'public';
define('AF_INCLUDE_PATH', '../');
require (AF_INCLUDE_PATH.'vitals.inc.php');
session_start();

include 'securimage.php';

$img = new securimage();

$img->show(); // alternate use:  $img->show('/path/to/background.jpg');
?>
