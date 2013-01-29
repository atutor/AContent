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
	* at the end of the string by setting the $forDisplay=1
	* @param	the mbstring that needed to be checked
	* @param	the byte length of what the input should be in the database.
	* @param	(OPTIONAL) 1 or 0, default value is 0 
	*			when 1, append '...' at the end of the string.
	*           when 0, only truncate string, do not append '...'  
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
	*          $printMsg: true or false. 
	*                     When it's true, the function prints "NO_PRIV" error msg if the user does not have $privilegeToValidate
	*                     When it's fales, the function returns true if the user has $privilegeToValidate, or false if the user has no $privilegeToValidate
	* @return  If the caller is oauth, echo error msg.
	*          Otherwise, the return is based on the value of $printMsg, @see @param $printMsg
	* @author  Cindy Qi Li
	*/
	public static function authenticate($privilegeToValidate, $printMsg=true) {
		global $_current_user, $_course_id, $msg, $oauth_import;
		
		// Add isAdmin() to allow all privileges for admins
		if ($privilegeToValidate == '' || $privilegeToValidate == 0 || ($_current_user && $_current_user->isAdmin())) return true;
		
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
		
		if ($privilegeToValidate == TR_PRIV_IN_A_COURSE &&
		    (!isset($_course_id) || $_course_id == 0))
		{
			$authenticated = false;
		}
		
		if (!$authenticated)
		{
			if ($oauth_import)
			{
				echo "error=".urlencode('User has no author privilege');
				exit;
			}
			else if ($printMsg)
			{
				$msg->addError('NO_PRIV');
				include(TR_INCLUDE_PATH.'header.inc.php');
				$msg->printAll(); 
				include(TR_INCLUDE_PATH.'footer.inc.php');
				exit;
			}
			else {
				return false;
			}
		}
		return true;
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
		// remove empty and "OR" element from the keywords array
		$keywords = array_diff($keywords, array('OR', '', NULL));
		
		// strip html tags from input text
		$text = strip_tags($text);
		
		$i = 0;
		$highlight_start_tag = '<strong class="highlight">';
		$highlight_end_tag = '</strong>';
		
		$strlen_highlight_start_tag = strlen($highlight_start_tag);
		$strlen_highlight_end_tag = strlen($highlight_end_tag);
		
		// Read the text character one by one and highlight from the reading point
		// This is to avoid the highlight on highlight html tags.
		while ($i < strlen($text))
		{
			foreach ($keywords as $keyword)
			{
				if (strtolower(substr($text, $i, strlen($keyword))) == strtolower($keyword))
				{
					$text = substr($text, 0, $i).$highlight_start_tag.substr($text, $i, strlen($keyword)).$highlight_end_tag.substr($text, $i+strlen($keyword));
					$i += strlen($keyword) + $strlen_highlight_start_tag + $strlen_highlight_end_tag;
					continue 2;
				}
			}
			$i++;
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
	
	/**
	 * This funciton replace the reserved constants with the real values
	 * @access public
	 * @param $str
	 * @return a replaced string
	 * @author Cindy Qi Li
	 */
	public static function replaceConstants($str)
	{
		global $_course_id, $_content_id;
		
		return str_replace(array('{COURSE_ID}', '{CONTENT_ID}'), array($_course_id, $_content_id), $str);
	}

	/**
	 * This funciton returns a pair of (URL, URL parameters). 
	 * For example, if input "tests/index.php?a=1&b=2", returns array ("tests/index.php", '?a=1&b=2"');
	 * @access public
	 * @param $str
	 * @return array of (URL, URL parameters)
	 * @author Cindy Qi Li
	 */
	public static function separateURLAndParam($str)
	{
		$pos = strpos($str, '?');
		
		if (!$pos) return array($str, '');
		else return array(substr($str, 0, $pos), substr($str, $pos));
	}
	
	/**
	 * This funciton returns a pair of (URL, URL parameters) of the PHP_SELF url. 
	 * For example, if http referer is "http://localhost/achecker/home/index.php?a=1&b=2", 
	 * the function returns array ("http://localhost/achecker/home/index.php?a=1&b=2&", 'a=1&b=2"');
	 * @access public
	 * @param none
	 * @return array of (URL, URL parameters)
	 * @author Cindy Qi Li
	 */
	public static function getRefererURLAndParams() {
		$caller_url_parts = explode('/', $_SERVER['PHP_SELF']); 
		$caller_script = $caller_url_parts[count($caller_url_parts)-1];
		
		if (count($_GET) > 0)
		{
			foreach ($_GET as $param => $value)
			{
				if ($param == 'action' || $param == 'cid') 
					continue;
				else
					$url_param .= $param.'='.urlencode($value).'&';
			}
		}
		
		$caller_url = $caller_script. '?'.(isset($url_param) ? $url_param : '');
		$url_param = substr($url_param, 0, -1);
		
		return array($caller_url, $url_param);
	}
	
	/**
	 * This funciton returns a path to the file_name in the images folder of the current session's theme
	 * @access public
	 * @param File Name with its extenstion. For example: my_own_course.gif
	 * @return path to the file in the images folder of the current session's theme
	 * @author Alexey Novak
	 */
	public static function getThemeImagePath($file_name) {
        $theme = $_SESSION['prefs']['PREF_THEME'];
        if (!isset($theme) || $theme == "" || 
            !isset($file_name) || $file_name == "") {
            return '';
        }
        
        return sprintf('themes/%s/images/%s', $theme, $file_name);
	}
}
?>