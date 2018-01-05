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

/**
* DAO for "config" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class LanguagesDAO extends DAO {

	/**
	* Insert table languages
	* @access  public
	* @param   $langCode, $charset, $regExp, $nativeName, $englishName, $status
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function Create($langCode, $charset, $regExp, $nativeName, $englishName, $status)
	{
		global $languageManager, $msg, $addslashes;
		//$langCode = $addslashes($langCode);
		//$charset = $addslashes($charset);
		//$regExp = $addslashes($regExp);
		//$englishName = $addslashes($englishName);
		//$status = intval($status);
	
		
		// check if the required fields are filled
		if (!$this->ValidateFields($langCode, $charset, $nativeName, $englishName)) return false;
		
		// check if the language already exists
		if ($languageManager->exists($langCode)) $msg->addError('LANG_EXISTS');
		
		if (!$msg->containsErrors())
		{
			$sql = "INSERT INTO ".TABLE_PREFIX."languages (language_code, charset, reg_exp, native_name, english_name, status)  VALUES (?,?, ?,?,?,?)";
		    $values=array($langCode, $charset, $regExp, $nativeName, $englishName, $status);	
		    $types= "sssssi";
			return $this->execute($sql,$values,$types);
		}
	}

	/**
	* Update a row
	* @access  public
	* @param   $langCode: required
	*          $charset: required
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function Update($langCode, $charset, $regExp, $nativeName, $englishName, $status)
	{
		// check if the required fields are filled
		if (!$this->ValidateFields($langCode, $charset, $nativeName, $englishName)) return false;
		
		$sql = "UPDATE ".TABLE_PREFIX."languages 
		           SET reg_exp=?,
		               native_name =?,
		               english_name =?,
		               status = ?
		         WHERE language_code = ?
		           AND charset = ?";
		$values = array($regExp,$nativeName,$englishName,$status,$langCode,$charset);
		$types="sssiss";         
		return $this->execute($sql,$values,$types);
	}

	/**
	* Update the given field with the given value by language_code and charset
	* @access  public
	* @param   $langCode
	*          $fieldName
	*          $fieldValue
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function UpdateField($langCode, $fieldName, $fieldValue)
	{
		
		// check if the required fields are filled
		if ($fieldValue == '') return false;

		$sql = "UPDATE ".TABLE_PREFIX."languages 
		           SET ".$fieldName."=?
		         WHERE language_code = ?";
		         
		$values = array($fieldValue,$langCode);
		$types = "ss";
		return $this->execute($sql,$values,$types);
	}

	/**
	* Delete a row
	* @access  public
	* @param   $langCode
	*          $charset
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function Delete($langCode)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."languages 
		         WHERE language_code = ?";
	    $values = $langCode;
	    $types = "s";
	    
		if (!$this->execute($sql,$values,$types)) return false;

		$sql = "DELETE FROM ".TABLE_PREFIX."language_text 
	             WHERE language_code = ?";	
	    $values = $langCode;
	    $types = "s";	
		return $this->execute($sql,$values,$types);
	}

	/**
	* Return all languages
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAll()
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."languages 
	             ORDER BY native_name";
	
	    return $this->execute($sql);
	}

	/**
	* Return all enabled languages
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAllEnabled()
	{
	    $sql = "SELECT * FROM ".TABLE_PREFIX."languages 
	             WHERE status = ".TR_STATUS_ENABLED."
	             ORDER BY native_name";
	    return $this->execute($sql);
	}

	/**
	* Return language with given language code
	* @access  public
	* @param   $langCode
	*          $charset
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getByLangCodeAndCharset($langCode, $charset)
	{
        $sql="SELECT * FROM ".TABLE_PREFIX."languages 
	             WHERE language_code = ?
	               AND charset=?
	             ORDER BY native_name";
	    $values=array($langCode, $charset);
	    $types="ss";    
		if ($rows = $this->execute($sql, $values, $types))
		{
			return $rows[0];
		}
	}

	/**
	* Return all languages except the ones with language code in the given string 
	* @access  public
	* @param   $langCode : one language codes, for example: en
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAllExceptLangCode($langCode)
	{

		if (trim($langCode) == '')
			return $this->getAll();
		else
		{

	    	$sql = "SELECT * FROM ".TABLE_PREFIX."languages
					 WHERE language_code <> ?
			         ORDER BY native_name";
			$values = $langCode;    
			$types = "s";     			         
		    return $this->execute($sql,$values,$types);
		}
	}
	
	/**
	* Return all languages except the ones with language code in the given string 
	* @access  public
	* @param   $langCode : one language codes, for example: en
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function ValidateFields($langCode, $charset, $nativeName, $englishName)
	{
		global $msg;
		
		$missing_fields = array();

		if ($langCode == '') {
			$missing_fields[] = _AT('lang_code');
		}
		if ($charset == '') {
			$missing_fields[] = _AT('charset');
		}
		if ($nativeName == '') {
			$missing_fields[] = _AT('name_in_language');
		}
		if ($englishName == '') {
			$missing_fields[] = _AT('name_in_english');
		}

		if ($missing_fields) {
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
			return false;
		}
		return true;
	}
}
?>