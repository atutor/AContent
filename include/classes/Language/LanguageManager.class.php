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

require_once(dirname(__FILE__) . '/Language.class.php');

//define('TR_LANG_STATUS_EMPTY',       0);
//define('TR_LANG_STATUS_INCOMPLETE',  1);
//define('TR_LANG_STATUS_COMPLETE',    2);
//define('TR_LANG_STATUS_PUBLISHED',   3);

/**
* LanguageManager
* Class for managing available languages as Language Objects.
* @access	public
* @author	Joel Kronenberg
* @see		Language.class.php
* @package	Language
*/
class LanguageManager {

	/**
	* This array stores references to all the Language Objects
	* that are available in this installation.
	* @access private
	* @var array
	*/
	var $allLanguages;
	
	/**
	* This array stores references to the Language Objects
	* that are available in this installation.
	* @access private
	* @var array
	*/
	var $availableLanguages;

	/**
	* The number of languages that are available. Does not include
	* character set variations.
	* @access private
	* @var integer
	*/
	var $numEnabledLanguages;

	/**
	* Constructor.
	* 
	* Initializes availableLanguages and numLanguages.
	*/
	function LanguageManager() {
		require_once(TR_INCLUDE_PATH. 'classes/DAO/LanguagesDAO.class.php');
		$languagesDAO = new LanguagesDAO();
		
		// initialize available lanuguages. Available languages are the ones with status "enabled"
		$rows = $languagesDAO->getAllEnabled();
		
		// if there's no enabled language, set to default language and default charset
		if (!is_array($rows))
		{
			$rows = array($languagesDAO->getByLangCodeAndCharset(DEFAULT_LANGUAGE_CODE, DEFAULT_CHARSET));
		}
		foreach ($rows as $i => $row) {
			$this->availableLanguages[$row['language_code']][$row['charset']] = new Language($row);
		}
		$this->numEnabledLanguages = count($this->availableLanguages);

			// initialize available lanuguages. Available languages are the ones with status "enabled"
		$rows = $languagesDAO->getAll();
		
		foreach ($rows as $i => $row) {
			$this->allLanguages[$row['language_code']][$row['charset']] = new Language($row);
		}
		$this->numLanguages = count($this->allLanguages);
	}


	/**
	* Returns a valid Language Object based on the given language $code and optional
	* $charset, FALSE if it can't be found.
	* @access	public
	* @param	string $code		The language code of the language to return.
	* @param	string $charset		Optionally, the character set of the language to find.
	* @return	boolean|Language	Returns FALSE if the requested language code and
	*								character set cannot be found. Returns a Language Object for the
	*								specified language code and character set.
	* @see		getMyLanguage()
	*/
	function getLanguage($code, $charset = '') {
		if (!$charset) {
			if (isset($this->allLanguages[$code])) {
				if (is_array($this->allLanguages[$code]))
					foreach ($this->allLanguages[$code] as $language){
						return $language;
					}
//				return current($this->allLanguages[$code]);
			} else {
				return FALSE;
			}
		}

		foreach ($this->allLanguages[$code] as $language) {
			if ($language->getCharacterSet() == $charset) {
				return $language;
			}
		}
		return FALSE;
	}

	/**
	* Tries to detect the user's current language preference/setting from (in order):
	* _GET, _POST, _SESSION, HTTP_ATCEPT_LANGUAGE, HTTP_USER_AGENT. If no match can be made
	* then it tries to detect a default setting (defined in config.inc.php) or a fallback
	* setting, false if all else fails.
	* @access	public
	* @return	boolean|Language	Returns a Language Object matching the user's current session.
	*								Returns FALSE if a valid Language Object cannot be found
	*								to match the request
	* @see		getLanguage()
	*/
	function getMyLanguage() {
		global $db, $_config; 

		if (isset($_GET) && !empty($_GET['lang']) && isset($this->availableLanguages[$_GET['lang']])) {
			$language = $this->getLanguage($_GET['lang']);

			if ($language) {
				return $language;
			}
		} 
		
		if (isset($_POST) && !empty($_POST['lang']) && isset($this->availableLanguages[$_POST['lang']])) {
			$language = $this->getLanguage($_POST['lang']);

			if ($language) {
				return $language;
			}
		}

		if (isset($_SESSION) && isset($_SESSION['lang']) && !empty($_SESSION['lang']) && isset($this->availableLanguages[$_SESSION['lang']])) {
			$language = $this->getLanguage($_SESSION['lang']);

			if ($language) {
				return $language;
			}
		}

		// Didn't catch any valid lang : we use the default settings
		if (isset($_config['default_language'])) $default_lang = $_config['default_language'];
		else $default_lang = DEFAULT_LANGUAGE_CODE;
		
		if (isset($this->availableLanguages[$default_lang])) {
			$language = $this->getLanguage($default_lang, DEFAULT_CHARSET);

			if ($language) {
				return $language;
			}
		}

		if (!empty($_SERVER['HTTP_ATCEPT_LANGUAGE'])) {
			// Language is not defined yet :
			// try to find out user's language by checking its HTTP_ATCEPT_LANGUAGE
			$accepted    = explode(',', $_SERVER['HTTP_ATCEPT_LANGUAGE']);
			$acceptedCnt = count($accepted);
			reset($accepted);
			for ($i = 0; $i < $acceptedCnt; $i++) {
				foreach ($this->availableLanguages as $codes) {
					foreach ($codes as $language) {
						if ($language->isMatchHttpAcceptLanguage($accepted[$i])) {
							return $language;
						}
					}
				}
			}
		}
		
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			// Language is not defined yet :
			// try to find out user's language by checking its HTTP_USER_AGENT
			foreach ($this->availableLanguages as $codes) {
				foreach ($codes as $language) {
					if ($language->isMatchHttpUserAgent($_SERVER['HTTP_USER_AGENT'])) {
						return $language;
					}
				}
			}
		}

		// else pick one at random:
		reset($this->availableLanguages);
		$uknown_language = current($this->availableLanguages);
		if ($unknown_language) {
			return FALSE;
		}
		
		return current($uknown_language);
	}

	function getAvailableLanguages() {
		return $this->availableLanguages;
	}

	// public
	function printDropdown($current_language, $name, $id) {
		echo '<select name="'.$name.'" id="'.$id.'">';

		foreach ($this->availableLanguages as $codes) {
			$language = current($codes);
			if ($language->getStatus() == TR_STATUS_ENABLED) {
				echo '<option value="'.$language->getCode().'"';
				if ($language->getCode() == $current_language) {
					echo ' selected="selected"';
				}
				echo '>'.$language->getNativeName().'</option>';
			}
		}
		echo '</select>';
	}

	// public
	function printList($current_language, $name, $id, $url) {

		$delim = false;
		
		foreach ($this->availableLanguages as $codes) {
			$language = current($codes);

			if ($language->getStatus() == TR_STATUS_ENABLED) {

				if ($delim){
					echo ' | ';
				}

				if ($language->getCode() == $current_language) {
					echo '<strong>'.$language->getNativeName().'</strong>';
				} else {
					echo '<a href="'.$url.'lang='.$language->getCode().'">'.$language->getNativeName().'</a> ';
				}

				$delim = true;
			}
		}
	}

	// public
	function getNumEnabledLanguages() {
		return $this->numEnabledLanguages;
	}

	function getNumLanguages() {
		return $this->numLanguages;
	}
	
	// public
	// checks whether or not the language exists
	function exists($code) {
		return isset($this->allLanguages[$code]);
	}

	// public
	// import language pack from specified file
	function import($filename) {
		require_once(TR_INCLUDE_PATH . 'lib/pclzip.lib.php');
		require_once(TR_INCLUDE_PATH . 'classes/Language/LanguageParser.class.php');
		require_once(TR_INCLUDE_PATH . 'classes/DAO/LanguagesDAO.class.php');
		
		global $languageManager, $msg;

		$import_path = TR_CONTENT_DIR . 'import/';

		$archive = new PclZip($filename);
		if ($archive->extract(PCLZIP_OPT_PATH,	$import_path) == 0) {
			exit('Error : ' . $archive->errorInfo(true));
		}

		$language_xml = @file_get_contents($import_path.'language.xml');

		$languageParser = new LanguageParser();
		$languageParser->parse($language_xml);
		$languageEditor = $languageParser->getLanguageEditor(0);

		$lang_code = $languageEditor->getCode();
		if ($languageManager->exists($lang_code)) {
			$msg->addError('LANG_EXISTS');
		}

		if (!$msg->containsErrors()) {
			$languageEditor->import($import_path . 'language_text.sql');
			$languagesDAO = new LanguagesDAO();
			$languagesDAO->UpdateField($lang_code, "status", TR_STATUS_ENABLED);
			$msg->addFeedback('IMPORT_LANG_SUCCESS');
			
			$version_in_pack = $languageEditor->getTransformableVersion();
			if ($version_in_pack != VERSION) 
			{
					$msg->addFEEDBACK(array('LANG_MISMATCH_VERSION', $version_in_pack, VERSION));
			}

		}

		// remove the files:
		@unlink($import_path . 'language.xml');
		@unlink($import_path . 'language_text.sql');
		@unlink($import_path . 'readme.txt');
		@unlink($filename);
	}

	// public
	// imports LIVE language from the AContent  language database


/* 	function liveImport($language_code) {
		global $db;

        // UPDATE FOR MYSQLI
		$tmp_lang_db = mysql_connect(TR_LANG_DB_HOST, TR_LANG_DB_USER, TR_LANG_DB_PASS);
		// set database connection using utf8
		mysql_query("SET NAMES 'utf8'", $tmp_lang_db);
		
		if (!$tmp_lang_db) {

			echo 'Unable to connect to db.';
			exit;
		}
		if (!mysql_select_db('dev_ATansformable_langs', $tmp_lang_db)) {
			echo 'DB connection established, but database "dev_ATansformable_langs" cannot be selected.';
			exit;
		}

		$sql = "SELECT * FROM languages_SVN WHERE language_code='$language_code'";
		$result = mysql_query($sql, $tmp_lang_db);

		if ($row = mysql_fetch_assoc($result)) {
			$row['reg_exp'] = addslashes($row['reg_exp']);
			$row['native_name'] = addslashes($row['native_name']);
			$row['english_name'] = addslashes($row['english_name']);

			$sql = "REPLACE INTO ".TABLE_PREFIX."languages VALUES ('{$row['language_code']}', '{$row['charset']}', '{$row['reg_exp']}', '{$row['native_name']}', '{$row['english_name']}', 3)";
			$result = mysql_query($sql, $db);

			$sql = "SELECT * FROM language_text_SVN WHERE language_code='$language_code'";
			$result = mysql_query($sql, $tmp_lang_db);

			$sql = "REPLACE INTO ".TABLE_PREFIX."language_text VALUES ";
			while ($row = mysql_fetch_assoc($result)) {
				$row['text'] = addslashes($row['text']);
				$row['context'] = addslashes($row['context']);
				$sql .= "('{$row['language_code']}', '{$row['variable']}', '{$row['term']}', '{$row['text']}', '{$row['revised_date']}', '{$row['context']}'),";
			}
			$sql = substr($sql, 0, -1);
			mysql_query($sql, $db);
		}
	}
	*/
}


?>