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
require_once(TR_INCLUDE_PATH . 'classes/DAO/LanguageTextDAO.class.php');
require_once(TR_INCLUDE_PATH . '../home/classes/ContentUtility.class.php');

/**********************************************************************************/
/* Output functions found in this file, in order:
/*
/*	- AC(term)
/*
/**********************************************************************************/

/**
* Converts language code to actual language message, caches them according to page url
* @access	public
* @param	args				unlimited number of arguments allowed but first arg MUST be name of the language variable/term
*								i.e		$args[0] = the term to the format string $_template[term]
*										$args[1..x] = optional arguments to the formatting string 
* @return	string|array		full resulting message
* @see		$db			        in include/vitals.inc.php
* @see		cache()				in include/phpCache/phpCache.inc.php
* @see		cache_variable()	in include/phpCache/phpCache.inc.php
* @author	Joel Kronenberg
*/
function _AT() {
	global $_cache_template, $lang_et, $_rel_url;
	static $_template;

	$args = func_get_args();
	
	if ($args[0] == "") return "";
	
	$languageTextDAO = new LanguageTextDAO();
	
	// a feedback msg
	if (!is_array($args[0])) {
		/**
		 * Added functionality for translating language code String (TR_ERROR|TR_INFOS|TR_WARNING|TR_FEEDBACK).*
		 * to its text and returning the result. No caching needed.
		 * @author Jacek Materna
		 */

		// Check for specific language prefix, extendible as needed
		// 0002767:  a substring+in_array test should be faster than a preg_match test.
		// replaced the preg_match with a test of the substring.
		$sub_arg = substr($args[0], 0, 7); // 7 is the shortest type of msg (TR_INFO)
		if (in_array($sub_arg, array('TR_ERRO','TR_INFO','TR_WARN','TR_FEED','TR_CONF'))) {
			global $_base_path, $addslashes;

			$args[0] = $addslashes($args[0]);
					
			/* get $_msgs_new from the DB */
			$rows = $languageTextDAO->getMsgByTermAndLang($args[0], $_SESSION['lang']);
			$msgs = '';
					
			if (is_array($rows)) 
			{
				$row = $rows[0];
				// do not cache key as a digit (no contstant(), use string)
				$msgs = str_replace('SITE_URL/', $_base_path, $row['text']);
				if (defined('TR_DEVEL') && TR_DEVEL) {
					$msgs .= ' <small><small>('. $args[0] .')</small></small>';
				}
			}

			return $msgs;
		}
	}
	
	// a template variable
	if (!isset($_template)) {
		$url_parts = parse_url(TR_BASE_HREF);
		$name = substr($_SERVER['PHP_SELF'], strlen($url_parts['path'])-1);

		if ( !($lang_et = cache(120, 'lang', $_SESSION['lang'].'_'.$name)) ) {
			/* get $_template from the DB */
			$rows = $languageTextDAO->getAllTemplateByLang($_SESSION['lang']);
			
			if (is_array($rows))
			{
				foreach ($rows as $id => $row) 
				{
					//Do not overwrite the variable that existed in the cache_template already.
					//The edited terms (_c_template) will always be at the top of the resultset
					//0003279
					if (isset($_cache_template[$row['term']])){
						continue;
					}
	
					// saves us from doing an ORDER BY
					if ($row['language_code'] == $_SESSION['lang']) {
						$_cache_template[$row['term']] = stripslashes($row['text']);
					} else if (!isset($_cache_template[$row['term']])) {
						$_cache_template[$row['term']] = stripslashes($row['text']);
					}
				}
			}
		
			cache_variable('_cache_template');
			endcache(true, false);
		}
		$_template = $_cache_template;
	}

	$num_args = func_num_args();
	if (is_array($args[0])) {
		$args = $args[0];
		$num_args = count($args);
	}
	$format	  = array_shift($args);

	if (isset($_template[$format]) && count($args) > 0) {
		$outString	= vsprintf($_template[$format], $args);
		$str = ob_get_contents();
	} else {
		$outString = '';
	}

	if ($outString === false) {
		return ('[Error parsing language. Variable: <code>'.$format.'</code>. Language: <code>'.$_SESSION['lang'].'</code> ]');
	}

	if (empty($outString)) {

		$rows = $languageTextDAO->getByTermAndLang($format, $_SESSION['lang']);
		if (is_array($rows))
		{
			$row = $rows[0];
			$_template[$row['term']] = stripslashes($row['text']);
			$outString = $_template[$row['term']];
		}

		if (empty($outString)) {
			return ('[ '.$format.' ]');
		}
	}

	return $outString;
}

/* 
	The following options were added as language dependant:
	%D: A textual representation of a week, three letters Mon through Sun
	%F: A full textual representation of a month, such as January or March January through December
	%l (lowercase 'L'): A full textual representation of the day of the week Sunday through Saturday
	%M: A short textual representation of a month, three letters Jan through Dec

	Support for the following maybe added later:
	?? %S: English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j
	?? %a: Lowercase Ante meridiem and Post meridiem am or pm 
	?? %A: Uppercase Ante meridiem and Post meridiem AM or PM 

	valid formTR_types:
	TR_DATE_MYSQL_DATETIME:		YYYY-MM-DD HH:MM:SS
	TR_DATE_MYSQL_TIMESTAMP_14:	YYYYMMDDHHMMSS
	TR_DATE_UNIX_TIMESTAMP:		seconds since epoch
	TR_DATE_INDEX_VALUE:		0-x, index into a date array
*/
function AT_date($format='%Y-%M-%d', $timestamp = '', $format_type=TR_DATE_MYSQL_DATETIME) {	
	static $day_name_ext, $day_name_con, $month_name_ext, $month_name_con;
	global $_config;

	if (!isset($day_name_ext)) {
		$day_name_ext = array(	'date_sunday', 
								'date_monday', 
								'date_tuesday', 
								'date_wednesday', 
								'date_thursday', 
								'date_friday',
								'date_saturday');

		$day_name_con = array(	'date_sun', 
								'date_mon', 
								'date_tue', 
								'date_wed',
								'date_thu', 
								'date_fri', 
								'date_sat');

		$month_name_ext = array('date_january', 
								'date_february', 
								'date_march', 
								'date_april', 
								'date_may',
								'date_june', 
								'date_july', 
								'date_august', 
								'date_september', 
								'date_october', 
								'date_november',
								'date_december');

		$month_name_con = array('date_jan', 
								'date_feb', 
								'date_mar', 
								'date_apr', 
								'date_may_short',
								'date_jun', 
								'date_jul', 
								'date_aug', 
								'date_sep', 
								'date_oct', 
								'date_nov',
								'date_dec');
	}

	if ($format_type == TR_DATE_INDEX_VALUE) {
		// apply timezone offset
		apply_timezone($timestamp);
	
		if ($format == '%D') {
			return _AT($day_name_con[$timestamp-1]);
		} else if ($format == '%l') {
			return _AT($day_name_ext[$timestamp-1]);
		} else if ($format == '%F') {
			return _AT($month_name_ext[$timestamp-1]);
		} else if ($format == '%M') {
			return _AT($month_name_con[$timestamp-1]);
		}
	}

	if ($timestamp == '') {
		$timestamp = time();
		$format_type = TR_DATE_UNIX_TIMESTAMP;
	}

	/* convert the date to a Unix timestamp before we do anything with it */
	if ($format_type == TR_DATE_MYSQL_DATETIME) {
		$year	= substr($timestamp,0,4);
		$month	= substr($timestamp,5,2);
		$day	= substr($timestamp,8,2);
		$hour	= substr($timestamp,11,2);
		$min	= substr($timestamp,14,2);
		$sec	= substr($timestamp,17,2);
		$timestamp	= mktime($hour, $min, $sec, $month, $day, $year);

	} else if ($format_type == TR_DATE_MYSQL_TIMESTAMP_14) {
	    $year		= substr($timestamp,0,4);
	    $month		= substr($timestamp,4,2);
	    $day		= substr($timestamp,6,2);
		$hour		= substr($timestamp,8,2);
	    $minute		= substr($timestamp,10,2);
	    $second		= substr($timestamp,12,2);
	    $timestamp	= mktime($hour, $minute, $second, $month, $day, $year);  
	}

	// apply timezone offset
	apply_timezone($timestamp);

	/* pull out all the %X items from $format */
	$first_token = strpos($format, '%');
	if ($first_token === false) {
		/* no tokens found */
		return $timestamp;
	} else {
		$tokened_format = substr($format, $first_token);
	}
	$tokens = explode('%', $tokened_format);
	array_shift($tokens);
	$num_tokens = count($tokens);

	$output = $format;
	
	for ($i=0; $i<$num_tokens; $i++) {
		$tokens[$i] = substr($tokens[$i],0,1);

		if ($tokens[$i] == 'D') {
			$output = str_replace('%D', _AT($day_name_con[date('w', $timestamp)]),$output);
		
		} else if ($tokens[$i] == 'l') {
			$output = str_replace('%l', _AT($day_name_ext[date('w', $timestamp)]),$output);
		
		} else if ($tokens[$i] == 'F') {
			$output = str_replace('%F', _AT($month_name_ext[date('n', $timestamp)-1]),$output);		
		
		} else if ($tokens[$i] == 'M') {
			$output = str_replace('%M', _AT($month_name_con[date('n', $timestamp)-1]),$output);

		} else {
			/* this token doesn't need translating */
			$value = date($tokens[$i], $timestamp);
			if ($value != $tokens[$i]) {
				$output = str_replace('%'.$tokens[$i], $value, $output);
			} /* else: this token isn't valid. so don't replace it. Eg. try %q */
		}
	}

	return $output;
}

/**********************************************************************************************************/
	/**
	* 	Transforms text based on formatting preferences.  Original $input is also changed (passed by reference).
	*	Can be called as:
	*	1) $output = AT_print($input, $name);
	*	   echo $output;
	*
	*	2) echo AT_print($input, $name); // prefered method
	*
	* @access	public
	* @param	string $input			text being transformed
	* @param	string $name			the unique name of this field (convension: table_name.field_name)
	* @param	boolean $runtime_html	forcefully disables html formatting for $input (only used by fields that 
	*									have the 'formatting' option
	* @return	string					transformed $input
	* @see		TR_FORMAT constants		in include/lib/constants.inc.php
	* @see		query_bit()				in include/vitals.inc.php
	* @author	Joel Kronenberg
	*/
	function AT_print($input, $name, $runtime_html = true) {
		global $_field_formatting;

		if (!isset($_field_formatting[$name])) {
			/* field not set, check if there's a global setting */
			$parts = explode('.', $name);
			
			/* check if wildcard is set: */
			if (isset($_field_formatting[$parts[0].'.*'])) {
				$name = $parts[0].'.*';
			} else {
				/* field not set, and there's no global setting */
				/* same as TR_FORMTR_NONE */
				return $input;
			}
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_QUOTES)) {
			$input = str_replace('"', '&quot;', $input);
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_CONTENT_DIR)) {
			$input = str_replace('CONTENT_DIR/', '', $input);
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_HTML) && $runtime_html) {
			/* what special things do we have to do if this is HTML ? remove unwanted HTML? validate? */
		} else {
			$input = str_replace('<', '&lt;', $input);
			$input = nl2br($input);
		}

		/* this has to be here, only because TR_FORMTR_HTML is the only check that has an else-block */
		if ($_field_formatting[$name] === TR_FORMTR_NONE) {
			return $input;
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_EMOTICONS)) {
			$input = smile_replace($input);
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_ATCODES)) {
			$input = trim(ContentUtility::myCodes(' ' . $input . ' '));
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_LINKS)) {
			$input = trim(ContentUtility::makeClickable(' ' . $input . ' '));
		}

		if (query_bit($_field_formatting[$name], TR_FORMTR_IMAGES)) {
			$input = trim(ContentUtility::imageReplace(' ' . $input . ' '));
		}

	
		return $input;
	}

/********************************************************************************************/
// Global variables for emoticons
 
global $smile_pics;
global $smile_codes;
if (!isset($smile_pics)) {
	$smile_pics[0] = $_base_path.'images/forum/smile.gif';
	$smile_pics[1] = $_base_path.'images/forum/wink.gif';
	$smile_pics[2] = $_base_path.'images/forum/frown.gif';
	$smile_pics[3] = $_base_path.'images/forum/ohwell.gif';
	$smile_pics[4] = $_base_path.'images/forum/tongue.gif';
	$smile_pics[5] = $_base_path.'images/forum/51.gif';
	$smile_pics[6] = $_base_path.'images/forum/52.gif';
	$smile_pics[7] = $_base_path.'images/forum/54.gif';
	$smile_pics[8] = $_base_path.'images/forum/27.gif';
	$smile_pics[9] = $_base_path.'images/forum/19.gif';
	$smile_pics[10] = $_base_path.'images/forum/3.gif';
	$smile_pics[11] = $_base_path.'images/forum/56.gif';
}

if (!isset($smile_codes)) {
	$smile_codes[0] = ':)';
	$smile_codes[1] = ';)';
	$smile_codes[2] = ':(';
	$smile_codes[3] = '::ohwell::';
	$smile_codes[4] = ':P';
	$smile_codes[5] = '::evil::';
	$smile_codes[6] = '::angry::';
	$smile_codes[7] = '::lol::';
	$smile_codes[8] = '::crazy::';
	$smile_codes[9] = '::tired::';
	$smile_codes[10] = '::confused::';
	$smile_codes[11] = '::muah::';
}

/**
* Replaces smile-code text into smilie image.
* @access	public
* @param	string $text		smile text to be transformed
* @return	string				transformed $text
* @see		$smile_pics			in include/lib/output.inc.php (above)
* @see		$smile_codes		in include/lib/output.inc.php (above)
* @author	Joel Kronenberg
*/
function smile_replace($text) {
	global $smile_pics;
	global $smile_codes;
	static $smiles;

	$smiles[0] = '<img src="'.$smile_pics[0].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_smile').'" />';
	$smiles[1] = '<img src="'.$smile_pics[1].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_wink').'" />';
	$smiles[2] = '<img src="'.$smile_pics[2].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_frown').'" />';
	$smiles[3]= '<img src="'.$smile_pics[3].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_oh_well').'" />';
	$smiles[4]= '<img src="'.$smile_pics[4].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_tongue').'" />';
	$smiles[5]= '<img src="'.$smile_pics[5].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_evil').'" />';
	$smiles[6]= '<img src="'.$smile_pics[6].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_angry').'" />';
	$smiles[7]= '<img src="'.$smile_pics[7].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_lol').'" />';
	$smiles[8]= '<img src="'.$smile_pics[8].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_crazy').'" />';
	$smiles[9]= '<img src="'.$smile_pics[9].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_tired').'" />';
	$smiles[10]= '<img src="'.$smile_pics[10].'" border="0" height="17" width="19" align="bottom" alt="'._AT('smile_confused').'" />';
	$smiles[11]= '<img src="'.$smile_pics[11].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_muah').'" />';

	$text = str_replace($smile_codes[0],$smiles[0],$text);
	$text = str_replace($smile_codes[1],$smiles[1],$text);
	$text = str_replace($smile_codes[2],$smiles[2],$text);
	$text = str_replace($smile_codes[3],$smiles[3],$text);
	$text = str_replace($smile_codes[4],$smiles[4],$text);
	$text = str_replace($smile_codes[5],$smiles[5],$text);
	$text = str_replace($smile_codes[6],$smiles[6],$text);
	$text = str_replace($smile_codes[7],$smiles[7],$text);
	$text = str_replace($smile_codes[8],$smiles[8],$text);
	$text = str_replace($smile_codes[9],$smiles[9],$text);
	$text = str_replace($smile_codes[10],$smiles[10],$text);
	$text = str_replace($smile_codes[11],$smiles[11],$text);

	return $text;
}

function html_get_list($array) {
	$list = '';
	foreach ($array as $value) {
		$list .= '<li>'.$value.'</li>';
	}
	return $list;
}

/**
 * print_paginator
 *
 * print out list of page links
 */
function print_paginator($current_page, $num_rows, $request_args, $rows_per_page = 50, $window = 5, $skippager='0') {
	$num_pages = ceil($num_rows / $rows_per_page);
	$request_args = '?'.$request_args;

	if ($num_pages == 1) return;
	if ($num_rows) {
		echo '<div><a href="'.$_SERVER['PHP_SELF'].'#skippager'.$skippager.'" class="hide_focus">'._AT('skip_pager').'</a></div>';
		echo '<div class="paging">';
	    echo '<ul>';
		
		$i=max($current_page-$window - max($window-$num_pages+$current_page,0), 1);

	    if ($current_page > 1)
			echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.($current_page-1).'">'._AT('prev').'</a>&nbsp;&nbsp;&nbsp;</li>';
    
		if ($i > 1) {
			echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p=1">1</a></li>';
			if ($i > 2) {
		        echo '<li>&hellip;</li>';
			}
		}

		for ($i; $i<= min($current_page+$window -min($current_page-$window,0),$num_pages); $i++) {
			if ($current_page == $i) {
				echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.$i.'" class="current"><em>'.$current_page.'</em></a></li>';
			} else {
				echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.$i.'">'.$i.'</a></li>';
			}
		}
        if ($i <= $num_pages) {
			if ($i < $num_pages) {
		        echo '<li>&hellip;</li>';
	        }
			echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.$num_pages.'">'.$num_pages.'</a></li>';
		}
		
		if ($current_page < $num_pages)
			echo '<li>&nbsp;&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.($current_page+1).'">'._AT('next').'</a></li>';
		
		echo '</ul>';
		echo '</div><a name="skippager'.$skippager.'"></a>';
	}
}

/**
* apply_timezone
* converts a unix timestamp into another UNIX timestamp with timezone offset added up.
* Adds the user's timezone offset, then converts back to a MYSQL timestamp
* Available both as a system config option, and a user preference, if both are set
* they are added together
* @param   date	 MYSQL timestamp.
* @return  date  MYSQL timestamp plus user's and/or system's timezone offset.
* @author  Greg Gay  .
*/
function apply_timezone($timestamp){
	global $_config;

	if($_config['time_zone']){
		$timestamp = ($timestamp + ($_config['time_zone']*3600));
	}

	if(isset($_SESSION['prefs']['PREF_TIMEZONE'])){
		$timestamp = ($timestamp + ($_SESSION['prefs']['PREF_TIMEZONE']*3600));
	}

	return $timestamp;
}
?>
