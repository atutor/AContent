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

if (!defined('TR_INCLUDE_PATH')) { exit; }
error_reporting(E_ALL ^ E_NOTICE);
//if(isset ($_POST['db_login']) && isset($_POST['db_password'])){
//define('DB_HOST', $_POST['db_host']);
//define('DB_USER', $_POST['db_login']);
//define('DB_PASSWORD', $_POST['db_password']);
//define('DB_PORT', $_POST['db_port']);
//}
/* AContent default configuration options */
/* used on: step3.php, step4.php, step5.php */
$_defaults['admin_username'] = 'admin';
$_defaults['admin_password'] = '';
$_defaults['admin_email'] = '';

$_defaults['site_name'] = 'AContent';
$_defaults['header_img'] = '';
$_defaults['header_logo'] = '';
$_defaults['home_url'] = '';

$_defaults['email_confirmation'] = 'TRUE';

$_defaults['max_file_size'] = '1048576';
$_defaults['ill_ext'] = 'exe, asp, php, php3, bat, cgi, pl, com, vbs, reg, pcd, pif, scr, bas, inf, vb, vbe, wsc, wsf, wsh';
$_defaults['cache_dir'] = '';

$_defaults['theme_categories'] = 'FALSE';
$_defaults['content_dir'] = realpath('../').DIRECTORY_SEPARATOR.'content';

require('include/classes/sqlutility.php');
require('DAO.class.php');
require('../include/lib/mysql_funcs.inc.php');

/* test for mysqli presence */
if(function_exists('mysqli_connect')){
	define('MYSQLI_ENABLED', 1);
} 
/*
// NO LONGER NEEDED WITH prepare/bind_parm BEING USED
function my_add_null_slashes( $string ) {
    global $db;
    if(defined('MYSQLI_ENABLED')){
        return $db->real_escape_string(stripslashes($string));
    }else{
        return mysql_real_escape_string(stripslashes($string));
    }

}

function my_null_slashes($string) {
	return $string;
}

if ( get_magic_quotes_gpc() == 1 ) {
    $addslashes   = 'my_add_null_slashes';
    $stripslashes = 'stripslashes';
} else {
    if(defined('MYSQLI_ENABLED')){
        // mysqli_real_escape_string requires 2 params, breaking wherever
        // current $addslashes with 1 param exists. So hack with trim and 
        // manually run mysqli_real_escape_string requires during db sanitization
        //$addslashes   = 'trim';
        $addslashes   = 'my_add_null_slashes';
    }else{
        $addslashes   = 'mysql_real_escape_string';
    }
    $stripslashes = 'my_null_slashes';
}
*/
function queryFromFile($sql_file_path)
{
	global $db, $progress, $errors;
    $dao = new DAO();
	$tables = array();
	
  if (!file_exists($sql_file_path)) {
    $progress[] = $sql_file_path . ': file not exists.';
    return false;
  }

  $sql_query = trim(fread(fopen($sql_file_path, 'r'), filesize($sql_file_path)));
  SqlUtility::splitSqlFile($pieces, $sql_query);

  foreach ($pieces as $piece) 
  {
  	$piece = trim($piece);
    // [0] contains the prefixed query
    // [4] contains unprefixed table name


		if ($_POST['tb_prefix'] || ($_POST['tb_prefix'] == '')) 
			$prefixed_query = SqlUtility::prefixQuery($piece, $_POST['tb_prefix']);
		else
			$prefixed_query = $piece;

		if ($prefixed_query != false ) 
		{
    	$table = $_POST['tb_prefix'].$prefixed_query[4];
      
      if($prefixed_query[1] == 'CREATE TABLE')
      {
      	if($dao->execute($prefixed_query[0]) !== false)
					$progress[] = 'Table <strong>'.$table . '</strong> created successfully.';
        else 
					if (at_db_errno($db) == 1050)
						$progress[] = 'Table <strong>'.$table . '</strong> already exists. Skipping.';
					else
						$errors[] = 'Table <strong>' . $table . '</strong> creation failed.';
      }
			elseif($prefixed_query[1] == 'INSERT INTO')
				$dao->execute($prefixed_query[0]);
      elseif($prefixed_query[1] == 'REPLACE INTO')
        $dao->execute($prefixed_query[0]);
      elseif($prefixed_query[1] == 'ALTER TABLE')
      {
				if($dao->execute($prefixed_query[0])  !== false)
					$progress[] = 'Table <strong>'.$table.'</strong> altered successfully.';
				else
					if(at_db_errno() == 1060)
						$progress[] = 'Table <strong>'.$table . '</strong> fields already exists. Skipping.';
					elseif (at_db_errno() == 1091)
						$progress[] = 'Table <strong>'.$table . '</strong> fields already dropped. Skipping.';
					else
						$errors[] = 'Table <strong>'.$table.'</strong> alteration failed.';
      }
      elseif($prefixed_query[1] == 'DROP TABLE')
				$dao->execute($prefixed_query[1] . ' ' .$table);
      elseif($prefixed_query[1] == 'UPDATE')
                $dao->execute($prefixed_query[0] );
		}
	}
	return true;
}

function print_errors( $errors ) {
	?>
	<br />
	<table border="0" class="errbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="errbox">
	<td>
		<h3 class="err"><img src="images/bad.gif" align="top" alt="" class="img" /> Error</h3>
		<?php
			echo '<ul>';
			foreach ($errors as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?>
		</td>
	</tr>
	</table>	<br />
<?php
}

function print_feedback( $feedback ) {
	?>
	<br />
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="fbkbox">
	<td><h3 class="feedback2"><img src="images/feedback.gif" align="top" alt="" class="img" /> Feedback</h3>
		<?php
			echo '<ul>';
			foreach ($feedback as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?></td>
	</tr>
	</table>
	<br />
<?php
}

function store_steps($step) {

	foreach($_POST as $key => $value) {
		if (substr($key, 0, strlen('step')) == 'step') {
			continue;
		} else if ($key == 'step') {
			continue;
		} else if ($key == 'action') {
			continue;
		} else if ($key == 'submit') {
			continue;
		}

		$_POST['step'.$step][$key] = urlencode(stripslashes($value));
	}
}


function print_hidden($current_step) {
	for ($i=1; $i<$current_step; $i++) {
		if (is_array($_POST['step'.$i])) {
			foreach($_POST['step'.$i] as $key => $value) {
				echo '<input type="hidden" name="step'.$i.'['.$key.']" value="'.$value.'" />'."\n";
			}
		}
	}
}

function print_progress($step) {
	global $install_steps;
	
	echo '<div class="install"><h3>Installation Progress</h3><p>';

	$num_steps = count($install_steps);
	for ($i=0; $i<$num_steps; $i++) {
		if ($i == $step) {
			echo '<strong style="margin-left: 12px; color: #006699;">Step '.$i.': '.$install_steps[$i]['name'].'</strong>';
		} else {
			echo '<small style="margin-left: 10px; color: gray;">';
			if ($step > $i) {
				echo '<img src="../images/check.gif" height="9" width="9" alt="Step Done!" /> ';
			} else {
				echo '<img src="../images/clr.gif" height="9" width="9" alt="" /> ';
			}
			echo 'Step '.$i.': '.$install_steps[$i]['name'].'</small>';
		}
		if ($i+1 < $num_steps) {
			echo '<br />';
		}
	}
	echo '</p></div><br />';

	echo '<h3>'.$install_steps[$step]['name'].'</h3>';
}


if (version_compare(phpversion(), '5.0') < 0) {
	function scandir($dirstr) {
		$files = array();
		$fh = opendir($dirstr);
		while (false !== ($filename = readdir($fh))) {
			array_push($files, $filename);
		}
		closedir($fh);
		return $files;
	}
}

/**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
function debug($var, $title='') {
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}
?>