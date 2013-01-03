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

// Emulate register_globals off. src: http://php.net/manual/en/faq.misc.php#faq.misc.registerglobals
function unregister_GLOBALS() {
   if (!ini_get('register_globals')) { return; }

   // Might want to change this perhaps to a nicer error
   if (isset($_REQUEST['GLOBALS'])) { die('GLOBALS overwrite attempt detected'); }

   // Variables that shouldn't be unset
   $noUnset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST','_SERVER','_ENV', '_FILES');
   $input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
  
   foreach ($input as $k => $v) {
       if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) { unset($GLOBALS[$k]); }
   }
}

function my_add_null_slashes( $string ) {
    return mysql_real_escape_string(stripslashes($string));
}

function my_null_slashes($string) {
	return $string;
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'stripslashes';
} else {
	$addslashes   = 'mysql_real_escape_string';
	$stripslashes = 'my_null_slashes';
}

/**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
function debug($var, $title='') {
	if (!defined('TR_DEVEL') || !TR_DEVEL) {
		return;
	}
	
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

/**
* This function is used for printing variables into log file for debugging.
* @access  public
* @param   mixed $var	The variable to output
* @param   string $log	The location of the log file. If not provided, use the default one.
* @author  Cindy Qi Li
*/
function debug_to_log($var, $log='') {
	if (!defined('TR_DEVEL') || !TR_DEVEL) {
		return;
	}
	
	if ($log == '') $log = TR_CONTENT_DIR. 'debug.log';
	$handle = fopen($log, 'a');
	fwrite($handle, "\n\n");
	fwrite($handle, date("F j, Y, g:i a"));
	fwrite($handle, "\n");
	fwrite($handle, var_export($var,1));
	
	fclose($handle);
}

/**
 * If MBString extension is loaded, then use it.
 * Otherwise we will have to use include/utf8 library
 */
 if (extension_loaded('mbstring')){
	 $strtolower = 'mb_strtolower';
	 $strtoupper = 'mb_strtoupper';
	 $substr = 'mb_substr';
	 $strpos = 'mb_strpos';
	 $strrpos = 'mb_strrpos';
	 $strlen = 'mb_strlen';
 } else {
 	 $strtolower = 'utf8_strtolower';
	 $strtoupper = 'utf8_strtoupper';
	 $substr = 'utf8_substr';
	 $strpos = 'utf8_strpos';
	 $strrpos = 'utf8_strrpos';
	 $strlen = 'utf8_strlen';
 }

function get_default_theme() {
	$themesDAO = new ThemesDAO();
	
	$rows = $themesDAO->getDefaultTheme();

	if (!is_dir(TR_INCLUDE_PATH . '../themes/' . $rows[0]['dir_name']))
		return 'default';
	else
		return $rows[0]['dir_name'];
}

/**
 * Convert all input to htmlentities output, in UTF-8.
 * @param	string	input to be convert
 * @param	boolean	true if we wish to change all newlines(\r\n) to a <br/> tag, false otherwise.  
 *			ref: http://php.net/manual/en/function.nl2br.php
 * @author	Harris Wong
 * @date	March 12, 2010
 */
function htmlentities_utf8($str, $use_nl2br=true){
	$return = htmlentities($str, ENT_QUOTES, 'UTF-8');
	if ($use_nl2br){
		return nl2br($return);
	} 
	return $return;
}

/**
 * Convert all '&' to '&amp;' from the input
 * @param   string  any string input, mainly URLs.
 * @return  input with & replaced to '&amp;'
 * @author  Harris Wong
 * @date    Oct 7, 2010
 */
function convertAmp($input){
    $input = str_replace('&amp;', '&', $input); //convert everything to '&' first
    return str_replace('&', '&amp;', $input);
}


/**
 * Redirects user to index.php if user is not loggedin
 * @param   None
 * @return  None
 * @author  Alexey Novak
 * @date    Oct 5, 2012
 */
function redirectNotLoggedinUsers() {

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 1) {
        global $msg;
        $msg->addError('LOGIN_REQUIRED');
        header('Location: index.php');
        exit;
    }
}

function query_bit( $bitfield, $bit ) {
	if (!is_int($bitfield)) {
		$bitfield = intval($bitfield);
	}
	if (!is_int($bit)) {
		$bit = intval($bit);
	}
	return ( $bitfield & $bit ) ? true : false;
}
?>
