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

if (!defined('TR_INCLUDE_PATH')) { exit; }
require_once(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/ContentDAO.class.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/PrivilegesDAO.class.php');

global $savant;

$contentDAO		= new ContentDAO();
$privilegesDAO	= new PrivilegesDAO();
$output = '';

?>

<?php

######################################
#	Variables declarations / definitions
######################################

global $_course_id, $_content_id;

$_course_id		= $course_id = (isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : $_course_id);
$_content_id	= $cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : $_content_id; /* content id of an optional chapter */

// paths settings

$mod_path					= array();
$mod_path['dnd_themod']		= realpath(TR_BASE_HREF			. 'dnd_themod').'/';
$mod_path['dnd_themod_int']	= realpath(TR_INCLUDE_PATH		. '../dnd_themod').'/';
$mod_path['dnd_themod_sys']	= $mod_path['dnd_themod_int']	. 'system/';
$mod_path['models_dir']		= $mod_path['dnd_themod']		. 'models/';
$mod_path['models_dir_int']	= $mod_path['dnd_themod_int']	. 'models/';

// includo immediatamente il file "applicaTema" così che possa ereditare variabili e costanti definite dal sistema
include_once($mod_path['dnd_themod_sys'].'Models.class.php');

// istanzio la classe Themes (che chiama il costruttore) 
$mod		= new Models($mod_path);

// includo le classi necessarie
//require_once($mod_path['dnd_themod_int'].'system/applicaTema.inc.php');

$user_priv	= $privilegesDAO->getUserPrivileges($_SESSION['user_id']);
$is_author	= $user_priv[1]['is_author'];

// prendo la lista dei temi disponibili validi
$listaModelli	= $mod->getListaModelli();

// chiamo la funzione che crea il modulo grafico di selezione del tema
$resArray		= $mod->createUI($listaModelli);

// array contenente il contenuto corrente (testo, eader, bit che indica che l'header è incluso)
//$content	= getContent($contentDAO, $cid);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// percorso del modulo
$dnd_themod		= TR_BASE_HREF.'dnd_themod/';
$dnd_themod_int	= TR_INCLUDE_PATH.'../dnd_themod/';

// percorso contenente la lista dei temi
$model_dir		= $dnd_themod.'models/';
$model_dir_int	= $dnd_themod_int.'models/';

// directory e i file system da escludere dalla lista dei temi presenti
$except	= array('.', '..', '.DS_Store', 'desktop.ini', 'Thumbs.db');

// id contenuto
$cid	= $this->cid;
// se non presente, prendo il _cid (id del contenuto in fase di modifica)
if($cid == '' AND isset($_GET['_cid']) AND $_GET['_cid'] != '')
	$cid = htmlentities($_GET['_cid']);


######################################
#	SCRIPT JQUERY DEL MODULO
######################################
include $mod_path['dnd_themod_sys'].'Models.js';

######################################
#	RESTITUISCO L'OUTPUT
######################################

// restituisco l'output
$output		= $resArray;

$savant->assign('title', _AT('models'));

$savant->assign('dropdown_contents', $output);
//$savant->assign('default_status', "hide");

$savant->display('include/box.tmpl.php');
/*
echo '<div style="position:absolute; background:white"></pre>';
	var_dump($_SESSION);
echo '</pre></div>';
*/
?>
