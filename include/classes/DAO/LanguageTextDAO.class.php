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
* DAO for "language_text" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class LanguageTextDAO extends DAO {

	/**
	* Create a new entry
	* @access  public
	* @param   $language_code : language code
	*          $variable: '_msgs', '_template', '_check', '_guideline', '_test'
	*          $term
	*          $text
	*          $context
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function Create($language_code, $variable, $term, $text, $context)
	{

		$sql = "INSERT INTO ".TABLE_PREFIX."language_text
		        (`language_code`, `variable`, `term`, `text`, `revised_date`, `context`)
		        VALUES
		        (?, ?, ?, ?, now(), ?)";
		$values = array($language_code,$variable, $term, $text,$context);
		$types = "sssss";
		return $this->execute($sql,$values,$types);
	}

	/**
	* Insert new record if not exists, replace the existing one if already exists. 
	* Record is identified by primary key: $language_code, variable, $term
	* @access  public
	* @param   $language_code : language code
	*          $variable: '_msgs', '_template', '_check', '_guideline', '_test'
	*          $term
	*          $text
	*          $context
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function Replace($language_code, $variable, $term, $text, $context)
	{
		$sql = "REPLACE INTO ".TABLE_PREFIX."language_text
		        (`language_code`, `variable`, `term`, `text`, `revised_date`, `context`)
		        VALUES
		        (?, ?, ?, ?, now(), ?)";	
		$values = array($language_code,$variable, $term, $text,$context);
		$types = "sssss";	        
		return $this->execute($sql,$values,$types);
	}
	
	/**
	* Delete a record by $variable and $term
	* @access  public
	* @param   $language_code : language code
	*          $variable: '_msgs', '_template', '_check', '_guideline', '_test'
	*          $term
	* @return  true / false
	* @author  Cindy Qi Li
	*/
	function DeleteByVarAndTerm($variable, $term)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."language_text
		        WHERE `variable` = ?
		          AND `term` = ?)";		
	    $values = array($variable, $term);
	    $types="ss";    
		return $this->execute($sql,$values,$types);
	}
	
	/**
	* Return message text of given term and language
	* @access  public
	* @param   term : language term
	*          lang : language code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getMsgByTermAndLang($term, $lang)
	{		
		$sql	= "SELECT * FROM ".TABLE_PREFIX."language_text  WHERE term=?  AND variable=? AND language_code=? ORDER BY variable";
		$tag = "_msgs";
		$values = array($term, $tag,  $lang);
		$types="sss";

	    return $this->execute($sql, $values, $types);

  }

	/**
	* Return text of given term and language
	* @access  public
	* @param   term : language term
	*          lang : language code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getByTermAndLang($term, $lang)
	{

 		$sql	= 'SELECT * FROM '.TABLE_PREFIX.'language_text 
						WHERE term=? 
						AND language_code=? 
						ORDER BY variable';   
		$values = array($term, $lang); 
		$types="ss";   
	    return $this->execute($sql,$values,$types);
  	}

	/**
	* Return rows of handbook rows by matching given text and language
	* @access  public
	* @param   term : language term
	*          lang : language code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getHelpByMatchingText($text, $lang)
	{

		$sql	= "SELECT * FROM ".TABLE_PREFIX."language_text 
						WHERE term like 'TR_HELP_%'
						AND lower(cast(text as char)) like '%?%' 
						AND language_code=? 
						ORDER BY variable";
		$values = array(strtolower($text), $lang);
		$types="ss";
	    return $this->execute($sql,$values,$lang);
  	}

  	/**
	* Return all template info of given language
	* @access  public
	* @param   lang : language code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAllByLang($lang)
	{

		$sql = "SELECT * FROM ".TABLE_PREFIX."language_text 
						WHERE language_code=?
						ORDER BY variable, term ASC";
		$values = $lang;
		$types = "s";
		return $this->execute($sql,$values,$types);
	}

  	/**
	* Return all template info of given language
	* @access  public
	* @param   lang : language code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	function getAllTemplateByLang($lang)
	{

        $sql = "SELECT * FROM ".TABLE_PREFIX."language_text 
						WHERE language_code= 'en' 
						AND variable= '_template'  
						ORDER BY variable ASC";
		$template_var = "_template";
		$values = array($lang,$template_var);
		$types="ss";

    	return $this->execute($sql);
	}

	/**
	* Update text based on given primary key
	* @access  public
	* @param   $languageCode : language_text.language_code
	*          $variable : language_text.variable
	*          $term : language_text.term
	*          $text : text to update into language_text.text
	* @return  true : if successful
	*          false: if unsuccessful
	* @author  Cindy Qi Li
	*/
	function setText($languageCode, $variable, $term, $text)
	{

		$sql = "UPDATE ".TABLE_PREFIX."language_text 
		           SET text= ?,
		               revised_date = now()
		         WHERE language_code = ? 
		           AND variable=? 
		           AND term = ?";
		$values=array($text, $_SESSION['lang'], $variable, $term);
		$types="ssss";
        return $this->execute($sql,$values,$types);
  }
}
?>