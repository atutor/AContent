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

	$path_parts = pathinfo($p_header['filename']);
	$ext = $path_parts['extension'];

	if (in_array($ext, $IllegalExtentions)) {
		return 0;
	}

	return 1;
}

?>