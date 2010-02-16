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

if (!defined('TR_INCLUDE_PATH')) { exit; }

function write_config_file($filename, $comments) {
	global $config_template;

	$tokens = array('{USER}',
					'{PASSWORD}',
					'{HOST}',
					'{PORT}',
					'{DBNAME}',
					'{TABLE_PREFIX}',
					'{CONTENT_DIR}',
					'{GENERATED_COMMENTS}',
				);

		$values = array(urldecode($_POST['step2']['db_login']),
					addslashes(urldecode($_POST['step2']['db_password'])),
					$_POST['step2']['db_host'],
					$_POST['step2']['db_port'],
					$_POST['step2']['db_name'],
					$_POST['step2']['tb_prefix'],
					addslashes(urldecode($_POST['step4']['content_dir'])),
					$comments,
				);

	$config_template = str_replace($tokens, $values, $config_template);

	if (!$handle = @fopen($filename, 'wb')) {
         return false;
    }
	@ftruncate($handle,0);
    if (!@fwrite($handle, $config_template, strlen($config_template))) {
		return false;
    }
        
    @fclose($handle);
	return true;
}

$config_template = "<"."?php 
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
{GENERATED_COMMENTS}
/************************************************************************/
/************************************************************************/
/* the database user name                                               */
define('DB_USER',                      '{USER}');

/* the database password                                                */
define('DB_PASSWORD',                  '{PASSWORD}');

/* the database host                                                    */
define('DB_HOST',                      '{HOST}');

/* the database tcp/ip port                                             */
define('DB_PORT',                      '{PORT}');

/* the database name                                                    */
define('DB_NAME',                      '{DBNAME}');

/* The prefix to add to table names to avoid conflicts with existing    */
/* tables. Default: TR_                                                 */
define('TABLE_PREFIX',                 '{TABLE_PREFIX}');

/* Where the content files are located.  This includes all file       */
/* manager and imported files.  If security is a concern, it is         */
/* recommended that the content directory be moved outside of the web	*/
/* accessible area.														*/
define('TR_CONTENT_DIR', '{CONTENT_DIR}');

/* DO NOT ALTER THIS LAST LINE                                          */
define('TR_INSTALL', TRUE);

?".">";

?>