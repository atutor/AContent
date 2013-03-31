<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/FileUtility.class.php');
$_custom_css = $_base_href.'include/jscripts/infusion/components/uploader/css/Uploader.css';

if ((isset($_REQUEST['popup']) && $_REQUEST['popup']) && 
	(!isset($_REQUEST['framed']) || !$_REQUEST['framed'])) {
	$popup = TRUE;
	$framed = FALSE;
} else if (isset($_REQUEST['framed']) && $_REQUEST['framed'] && isset($_REQUEST['popup']) && $_REQUEST['popup']) {
	$popup = TRUE;
	$framed = TRUE;
} else {
	$popup = FALSE;
	$framed = FALSE;
}

// If Flash is detected, call the necessary css and js, and configure settings to use the Fluid Uploader
if (isset($_SESSION['flash']) && $_SESSION['flash'] == "yes") {
	// Provide the option of switching between Fluid Uploader and simple single file uploader
	// and save the user preference as a cookie */
	if (!isset($_COOKIE["fluid_on"])) {
	$_custom_head .= '
		<script type="text/javascript">
		<!--
			trans.utility.setcookie("fluid_on", "yes", ' . (time()+1200) .');
		//-->
		</script>
';
	} 

    $fluid_dir = 'include/jscripts/infusion/';
    $framed = intval($_GET['framed']);
    $popup = intval($_GET['popup']);
    $current_path = TR_CONTENT_DIR.$_course_id.'/';

    if ($_GET['pathext'] != '') {
        $pathext = urldecode($_GET['pathext']);
    } else if ($_POST['pathext'] != '') {
        $pathext = $_POST['pathext'];
    }

    if($_GET['back'] == 1) {
        $pathext  = substr($pathext, 0, -1);
        $slashpos = strrpos($pathext, '/');
        if($slashpos == 0) {
            $pathext = '';
        } else {
            $pathext = substr($pathext, 0, ($slashpos+1));
        }

    }
}

global $msg;
if (isset($_GET['msg'])) $msg->addFeedback($_GET['msg']);

require('top.php');
$_SESSION['done'] = 1;

require(TR_INCLUDE_PATH.'../file_manager/filemanager_display.inc.php');

closedir($dir);

?>
<script type="text/javascript">
//<!--
function Checkall(form){ 
  for (var i = 0; i < form.elements.length; i++){    
    eval("form.elements[" + i + "].checked = form.checkall.checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
//-->
</script>
<?php require(TR_INCLUDE_PATH.'footer.inc.php'); ?>
