<?php
/************************************************************************/
/* AFrame                                                               */
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

if (!defined('AF_INCLUDE_PATH')) exit;

class Utility {

	/**
	* return a unique random string based on the given length
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
}
?>