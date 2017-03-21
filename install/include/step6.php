<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

require('../svn.php');

$svn_data   = explode("|", $svn_data);
$build      = $svn_data[0];
$build_date = $svn_data[1];

$os = php_uname('s') . ' '. php_uname('r'). ' '. php_uname('v'). ' '. php_uname('m');


if (isset($_POST['submit'])) {
	unset($_POST['submit']);
	unset($action);

	if ($_POST['log_yes']) {

		$request  = '&upgrade=' . urlencode($stripslashes($_POST['log_upgrade']));
		$request .= '&version=' . urlencode($stripslashes($new_version));
		$request .= '&build='   . urlencode($stripslashes($build));
		$request .= '&build_date=' . urlencode($stripslashes($build_date));
		$request .= '&os='      . urlencode($stripslashes($_POST['log_os']));
		$request .= '&server='  . urlencode($stripslashes($_POST['log_server']));
		$request .= '&php='     . urlencode($stripslashes($_POST['log_php']));
		$request .= '&mysql='   . urlencode($stripslashes($_POST['log_mysql']));

		if ($_POST['step1']['old_path'] != '') {
			// get some usage data from this upgrade:
			
			if(defined('MYSQLI_ENABLED')){
			        $db = new mysqli($_POST['step1']['db_host'], $_POST['step1']['db_login'], $_POST['step1']['db_password'], $_POST['step1']['db_name'], $_POST['step1']['db_port']);
                    $db->set_charset("utf8");	   
			}else{
			    $db     = @mysql_connect($_POST['step1']['db_host'] . ':' . $_POST['step1']['db_port'], $_POST['step1']['db_login'], urldecode($_POST['step1']['db_password']));
			    @mysql_select_db($_POST['step1']['db_name'], $db);
            }
			$db_size = 0; // db size in bytes
			$sql = 'SHOW TABLE STATUS';
			
			if(defined('MYSQLI_ENABLED')){
			    $result = $db->query($sql);
			    while ($row =  $result->fetch_assoc()) {
			        $db_size += $row['Data_length']+$row['Index_length'];
			    }
			}else{
                $result = mysql_query($sql, $db);
                while ($row = mysql_fetch_assoc($result)) {
                    $db_size += $row['Data_length']+$row['Index_length'];
                }
            }
			$request .= '&db='      . $db_size;     // db size in bytes
		}

		if ($_POST['log_url_yes']) {
			$request .= '&url=' . urlencode($stripslashes($_POST['log_url']));
		}

//		$header = "POST /install_log.php HTTP/1.1\r\n";
//		$header .= "Host: transformable.ca\r\n";
//		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
//		$header .= "Content-Length: " . strlen($request) . "\r\n\r\n";
//		$fp = fsockopen('www.transformable.ca', 80, $errno, $errstr, 30);
//
//		if ($fp) {
//			fputs($fp, $header . $request . "\r\n\r\n");
//			fclose($fp);
//		}
	}

	store_steps($step);
	$step++;
	return;
}

print_progress($step);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="step" value="<?php echo $step; ?>" />
<?php
	if ($_POST['step1']['old_path'] != '') {
		echo '<input type="hidden" name="log_upgrade" value="1" />';
	} else {
		echo '<input type="hidden" name="log_upgrade" value="0" />';
	}
		print_hidden($step);
	?>
<br />
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1" colspan="2">Submit the following information to the atutor.ca server anonymously? The information we gather helps us plan our development resources to better suit the needs of the community. You may optionally choose to send the URL of your AContent installation.</td>
	</tr>
	<tr>
		<td class="row1" width="20%"><b>AContent Version:</b></td>
		<td class="row1"><?php echo $new_version; ?> (build <?php echo $build . ' - '.$build_date; ?>)</td>
	</tr>
	<tr>
		<td class="row1" nowrap="nowrap"><b>Operating System:</b></td>
		<td class="row1"><?php echo $os; ?> <input type="hidden" name="log_os" value="<?php echo $os; ?>" /></td>
	</tr>
	<tr>
		<td class="row1"><b>Web Server:</b></td>
		<td class="row1"><?php echo $_SERVER['SERVER_SOFTWARE']; ?> <input type="hidden" name="log_server" value="<?php echo $_SERVER['SERVER_SOFTWARE']; ?>" /></td>
	</tr>
	<tr>
		<td class="row1"><b>PHP Version:</b></td>
		<td class="row1"><?php echo phpversion(); ?> <input type="hidden" name="log_php" value="<?php echo phpversion(); ?>" /></td>
	</tr>
	<tr>
		<td class="row1"><b>MySQL Version:</b></td>
		<td class="row1"><?php
            if(defined('MYSQLI_ENABLED')){
                    if ($_POST['step1']['old_path'] != '') {
                        $db     = new mysqli($_POST['step1']['db_host'], $_POST['step1']['db_login'], urldecode($_POST['step1']['db_password']), null, $_POST['step1']['db_port']);
                    } else {
                        $db     = new mysqli($_POST['step2']['db_host'],  $_POST['step2']['db_login'], $_POST['step2']['db_password'], null,  $_POST['step2']['db_port']);
                    }			
            			
            	}else{
                    if ($_POST['step1']['old_path'] != '') {
                        $db     = @mysql_connect($_POST['step1']['db_host'] . ':' . $_POST['step1']['db_port'], $_POST['step1']['db_login'], urldecode($_POST['step1']['db_password']));
                    } else {
                        $db     = @mysql_connect($_POST['step2']['db_host'] . ':' . $_POST['step2']['db_port'], $_POST['step2']['db_login'], $_POST['step2']['db_password']);
                    }
             }       
			$sql    = 'SELECT VERSION() AS version';
			 if(defined('MYSQLI_ENABLED')){
                $result = $db->query($sql);
                $row    = $result->fetch_assoc();			 
			 
			}else{
                $result = @mysql_query($sql, $db);
                $row    = @mysql_fetch_assoc($result);
			}
			echo $row['version'];
			?> <input type="hidden" name="log_mysql" value="<?php echo $row['version']; ?>" /></td>
	</tr>
	<tr>
		<td class="row1" valign="top"><div class="optional" title="Optional Field">?</div><b>Optional URL:</b></td>
		<td class="row1"><?php
			$url = 'http' . ((isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'on') ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'], 0, -strlen('install/install.php'));
			echo $url; ?><input type="hidden" name="log_url" value="<?php echo $url; ?>" /><br />
		<input type="checkbox" name="log_url_yes" value="1" id="url_yes" checked="checked"/><label for="url_yes">Include this URL as well.</label></td>
	</tr>
	</table>
<input type="hidden" name="log_yes" value="1" />
<br />
<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p>

</form>