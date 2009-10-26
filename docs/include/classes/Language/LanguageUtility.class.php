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
* Utility functions for language 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('AF_INCLUDE_PATH')) exit;

class LanguageUtility {

	/**
	* return language code from given AFrame language code
	* @access  public
	* @param   $code
	* @return  language code
	* @author  Cindy Qi Li
	*/
	public static function getParentCode($code = '') {
		if (!$code && isset($this)) {
			$code = $this->code;
		}
		$peices = explode(AF_LANGUAGE_LOCALE_SEP, $code, 2);
		return $peices[0];
	}

	/**
	* return charset from given AFrame language code
	* @access  public
	* @param   $code
	* @return  charset
	* @author  Cindy Qi Li
	*/
	public static function getLocale($code = '') {
		if (!$code && isset($this)) {
			$code = $this->code;
		}
		$peices = explode(AF_LANGUAGE_LOCALE_SEP, $code, 2);
		return $peices[1];
	}
}
?>