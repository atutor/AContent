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
* DAO for "lang_codes" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('TR_INCLUDE_PATH')) exit;

require_once(TR_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class LangCodesDAO extends DAO {
 
	/**
	* Return all rows
	* @access  public
	* @param   none
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function GetAll()
	{
		$sql = "SELECT * FROM ". TABLE_PREFIX ."lang_codes ORDER BY description";
		
		return $this->execute($sql);
	}
	
	/**
	* Return lang code info of the given 2 letters code
	* @access  public
	* @param   $code : 2 letters code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function GetLangCodeBy2LetterCode($code)
	{
		$sql = "SELECT * FROM ". TABLE_PREFIX ."lang_codes 
					WHERE code_2letters = ?";		
		$values = $code;
		$types = 's';
		return $this->execute($sql,$values, $types);
	}

	/**
	* Return lang code info of the given 3 letters code
	* @access  public
	* @param   $code : 3 letters code
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function GetLangCodeBy3LetterCode($code)
	{

		$sql = "SELECT * FROM ".TABLE_PREFIX."lang_codes 
					WHERE code_3letters = ?";
		$values = $code;
		$types = "s";
		if ($rows = $this->execute($sql, $values, $types))
		{
			return $rows[0];
		}
		else
			return false;
	}
	
    /**
    * Return a name of the language based on its language code
    * @access  public
    * @param   language code 3 or 2 letter one
    * @return  table rows
    * @author  Alexey Novak
    */
    public function getLanguageByCode($code) {

        $sql = 'SELECT * FROM '.TABLE_PREFIX.'lang_codes WHERE code_3letters=? OR code_2letters=?'; 
        $values = array($code, $code);     
        $types="ss";
        if ($rows = $this->execute($sql, $values,$types)) {
            return $rows[0]['description'];
        }
        
        return '';
	}

	/**
	* Return array of all the 2-letter & 3-letter language codes with given direction
	* @access  public
	* @param   $direction : 'rtl' or 'ltr'
	* @return  table rows
	* @author  Cindy Qi Li
	*/
	public function GetLangCodeByDirection($direction)
	{

		$rtn_array = array();
		$sql = "SELECT * FROM ". TABLE_PREFIX ."lang_codes 
					WHERE direction = ?";
		$values = $direction;
		$types = 's';	
		$rows = $this->execute($sql, $values,$types);
		
		if (is_array($rows))
		{
			foreach ($rows as $row)
			{
				array_push($rtn_array, $row['code_3letters']);
				array_push($rtn_array, $row['code_2letters']);
			}
		}
		return $rtn_array;
	}

}
?>