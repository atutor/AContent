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
* File utility functions 
* @access	public
* @author	Cindy Qi Li
*/

if (!defined('TR_INCLUDE_PATH')) exit;

/**
* This function gets used by PclZip when creating a zip archive.
* @access  public
* @return  int				whether or not to include the file
* @author  Joel Kronenberg
*/
function preImportCallBack($p_event, &$p_header) {
	global $IllegalExtentions;

	if ($p_header['folder'] == 1) {
		return 1;
	}
	$p_header['filename']				= preg_replace("/\.\./i", "", $p_header['filename']);
	$path_parts = pathinfo($p_header['filename']);
	$ext = $path_parts['extension'];

	if (in_array($ext, $IllegalExtentions)) {
		return 0;
	}

	return 1;
}

/**
* This function gets used by PclZip when extracting a zip archive.
* @see file_manager/zip.php
* @access  public
* @return  int				whether or not to include the file
* @author  Joel Kronenberg
*/
function preExtractCallBack($p_event, &$p_header) {
	global $translated_file_names;

	if ($p_header['folder'] == 1) {
		return 1;
	}

	if ($translated_file_names[$p_header['index']] == '') {
		return 0;
	}

	if ($translated_file_names[$p_header['index']]) {
		$p_header['filename'] = substr($p_header['filename'], 0, -strlen($p_header['stored_filename']));
		$p_header['filename'] .= $translated_file_names[$p_header['index']];
	}
	return 1;
}

?>
