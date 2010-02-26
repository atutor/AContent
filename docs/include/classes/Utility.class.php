<?php
/************************************************************************/
/* Transformable                                                        */
/************************************************************************/
/* Copyright (c) 2009                                                   */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* Utility functions 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('TR_INCLUDE_PATH')) exit;

class Utility {

	/**
	* Return a unique random string based on the given length.
	* The maxium length is 32 hexidecimal
	* @access  public
	* @param   none
	* @return  a random string
	* @author  Cindy Qi Li
	*/
	public static function getRandomStr($length = "")
	{
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr($code, 0, $length);
		else return $code;
	}

	/**
	* This function deletes $dir recrusively without deleting $dir itself.
	* @access  public
	* @param   string $charsets_array	The name of the directory where all files and folders under needs to be deleted
	* @author  Cindy Qi Li
	*/
	public static function clearDir($dir) {
		if(!$opendir = @opendir($dir)) {
			return false;
		}
		
		while(($readdir=readdir($opendir)) !== false) {
			if (($readdir !== '..') && ($readdir !== '.')) {
				$readdir = trim($readdir);
	
				clearstatcache(); /* especially needed for Windows machines: */
	
				if (is_file($dir.'/'.$readdir)) {
					if(!@unlink($dir.'/'.$readdir)) {
						return false;
					}
				} else if (is_dir($dir.'/'.$readdir)) {
					/* calls lib function to clear subdirectories recrusively */
					if(!Utility::clrDir($dir.'/'.$readdir)) {
						return false;
					}
				}
			}
		} /* end while */
	
		@closedir($opendir);
		
		return true;
	}

	/**
	* Enables deletion of directory if not empty
	* @access  public
	* @param   string $dir		the directory to delete
	* @return  boolean			whether the deletion was successful
	* @author  Joel Kronenberg
	*/
	public static function clrDir($dir) {
		if(!$opendir = @opendir($dir)) {
			return false;
		}
		
		while(($readdir=readdir($opendir)) !== false) {
			if (($readdir !== '..') && ($readdir !== '.')) {
				$readdir = trim($readdir);
	
				clearstatcache(); /* especially needed for Windows machines: */
	
				if (is_file($dir.'/'.$readdir)) {
					if(!@unlink($dir.'/'.$readdir)) {
						return false;
					}
				} else if (is_dir($dir.'/'.$readdir)) {
					/* calls itself to clear subdirectories */
					if(!Utility::clrDir($dir.'/'.$readdir)) {
						return false;
					}
				}
			}
		} /* end while */
	
		@closedir($opendir);
		
		if(!@rmdir($dir)) {
			return false;
		}
		return true;
	}

	/* takes the array of valid prefs and assigns them to the current session */
	public static function assign_session_prefs($prefs) {
		unset($_SESSION['prefs']);
		
		if (is_array($prefs)) {
			foreach($prefs as $pref_name => $value) {
				$_SESSION['prefs'][$pref_name] = $value;
			}
		}
	}

	/**
	* Checks if the data exceeded the database predefined length, if so,
	* truncate it.
	* This is used on data that are being inserted into the database.
	* If this function is used for display purposes, you may want to add the '...' 
	*  at the end of the string by setting the $forDisplay=1
	* @param	the mbstring that needed to be checked
	* @param	the byte length of what the input should be in the database.
	* @param	(OPTIONAL)
	*			append '...' at the end of the string.  Should not use this when 
	*			dealing with database.  This should only be set for display purposes.
	* @return	the mbstring safe sql entry
	* @author	Harris Wong
	*/
	public static function validateLength($input, $len, $forDisplay=0){
		global $strlen, $substr;
		$input_bytes_len = strlen($input);
		$input_len = $strlen($input);
	
		//If the input has exceeded the db column limit
		if ($input_bytes_len > $len){
			//calculate where to chop off the string
			$percentage = $input_bytes_len / $input_len;
			//Get the suitable length that should be stored in the db
			$suitable_len = floor($len / $percentage);
	
			if ($forDisplay===1){
				return $substr($input, 0, $suitable_len).'...';
			}
			return $substr($input, 0, $suitable_len);
		}
		//if valid length
		return $input;
	}

	/**
	* check if value in the given attribute is a valid language code
	* return true if valid, otherwise, return false
	*/
	public static function isValidLangCode($code)
	{
		require_once(TR_INCLUDE_PATH.'classes/DAO/LangCodesDAO.class.php');
		$langCodesDAO = new LangCodesDAO();

		if (strlen($code) == 2) 
		{
			$rows = $langCodesDAO->GetLangCodeBy2LetterCode($code);
		}
		else if (strlen($code) == 3)
		{
			$rows = $langCodesDAO->GetLangCodeBy3LetterCode($code);
		}
		else 
		{
			return false;
		}

		return (is_array($rows));
	}

	/**
	 * Return a valid 3-character language code
	 * 1. if input is a valid 3-character language code, return itself;
	 * 2. if input is a valid 2-character language code, return according 3-character language code
	 * 3. if input is an invalid language code, return default language code
	 */
	public static function get3LetterLangCode($code)
	{
		require_once(TR_INCLUDE_PATH.'classes/DAO/LangCodesDAO.class.php');
		$langCodesDAO = new LangCodesDAO();
		
		if (!Utility::isValidLangCode($code))
			return $_config['default_language'];
		else
		{
			if (strlen($code) == 3) return $code;
			
			if (strlen($code) == 2) 
			{
				$rows = $langCodesDAO->GetLangCodeBy2LetterCode($code);
				return $rows[0]['code_3letters'];
			}
			
		}
	}
	
	/**
	 * Find out whether the current theme is a mobile theme
	 * @access public
	 * @param  none
	 * @return true if the current theme is a mobile theme; otherwise, false.
	 */
	public static function isMobileTheme() {
		return ($_SESSION['prefs']['PREF_THEME'] == 'mobile');
	}
	
	/**
	* This function authenticate user privilege
	* @access  public
	* @param   privilege constants
	* @author  Cindy Qi Li
	*/
	public static function authenticate($privilegeToValidate) {
		global $_current_user, $_course_id, $msg;
		
		$authenticated = true; // default
		/* make sure the user is the author of the current course */
		if ($privilegeToValidate == TR_PRIV_ISAUTHOR_OF_CURRENT_COURSE && 
		    (!isset($_current_user) || !$_current_user->isAuthor($_course_id) || !isset($_course_id)))
		{
			$authenticated = false;
		}

		if ($privilegeToValidate == TR_PRIV_ISAUTHOR &&
		    (!isset($_current_user) || !$_current_user->isAuthor()))
		{
			$authenticated = false;
		}
		
		if (!$authenticated)
		{
			$msg->addError('NO_PRIV');
			include(TR_INCLUDE_PATH.'header.inc.php');
			$msg->printAll(); 
			include(TR_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	}
	
	/**
	 * This function highlights the given keywords in the text
	 * and perserves the case of the keywords in the text
	 * @access public
	 * @param  $text string, 
	 *         $keywords    an array of keywords
	 * @author Cindy Qi Li
	 */
	public static function highlightKeywords($text, $keywords)
	{
		if (!is_array($keywords)) return $text;
		
		foreach($keywords as $keyword)
		{	 
			// skip "OR"
			if ($keyword == 'OR') continue;
			
			$textLen = strlen($keyword);
	
			$textArray = array();
			$pos					= 0;
			$count			 = 0;
	
			while($pos !== FALSE) {
					$pos = stripos($text,$keyword,$pos);
					if($pos !== FALSE) {
							$textArray[$count]['kwic'] = substr($text,$pos,$textLen);
							$textArray[$count++]['pos']	= $pos;
							$pos++;
					}
			}
	
			for($i=count($textArray)-1;$i>=0;$i--) {
					$replace = '<strong class="highlight">'.$textArray[$i]['kwic'].'</strong>';
					$text = substr_replace($text,$replace,$textArray[$i]['pos'],$textLen);
			}
		}
		return $text;
 	}
 	
	/**
	 * This function removes all the items that are NULL or only spaces from the given array
	 * @access public
	 * @param  $in_array    array
	 * @return if successful, return the array without the NULL or all-space items
	 *         if the in_array is not an array or failed, return $in_array itself 
	 * @author Cindy Qi Li
	 */
	public static function removeEmptyItemsFromArray($in_array)
	{
		if (!is_array($in_array)) return $in_array;
		
		foreach ($in_array as $key => $value)
			if (is_null($value) || trim($value) == '') unset($in_array[$key]);
		
		return array_values($in_array);
	}
}
?>