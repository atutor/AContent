<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');
require_once(TR_INCLUDE_PATH.'../home/classes/StructureManager.class.php');
$_custom_head .= '<link rel="stylesheet" href="themes/'.$_SESSION['prefs']['PREF_THEME'].'/template_editor/style.css" type="text/css" />'."\n";
//$_custom_head .= '<script type="text/javascript" src="template_editor/js/structure.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="template_editor/js/jquery.ui.sortable.js"></script>'."\n";
global $_current_user;

// Check if the template DIR is writable
if(!is_writable('../templates')){
    $msg->addWarning('TEMPLATE_DIR_NOT_WRITABLE');
    $temp_unwritable = TRUE;
}else{
    $msg->addFeedback('TEMPLATE_DIR_WRITABLE');
}
//
if (isset($_current_user) && Utility::authenticate($privs[TR_PRIV_TEMPLATE_EDITOR])) {
    // Temporary hack re: mantis 5530
    if(!isset($_POST['template_type'])){
        require(TR_INCLUDE_PATH.'header.inc.php');
    }

    if(isset ($_POST['submit'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');
        $template_folder=$commons->create_template_dir($_POST['template_type'], $_POST['template_name']);
        if($_GET['type']=='page') $type='page_templates';
        $commons->create_template_metadata($_POST['template_type'],$template_folder, $_POST['template_name'], $_POST['template_desc'],
            $_POST['maintainer_name'], $_POST ['maintainer_email'], $_POST ['template_url'], $_POST['template_license'],
            $_POST ['release_version'], $_POST ['release_date'], $_POST ['release_state'], $_POST ['release_notes']);

        if($_POST['template_type']=='structures') {
            $content=$commons->parse_to_XML('<structure version="0.1"></structure>');
            $commons->save_xml($content, 'structures/'.$template_folder, 'content.xml');
            Header('Location: edit_structure.php?temp='.$template_folder);
            exit;
            
        }elseif($_POST['template_type']=='layouts') {
            $commons->save_file('layouts/'.$template_folder, $template_folder.'.css');
            Header('Location: edit_layout.php?temp='.$template_folder);
            exit;
            
        }elseif($_POST['template_type']=='page_templates') {
            $commons->save_file('page_templates/'.$template_folder, $template_folder.'.html');
            Header('Location: edit_page.php?temp='.$template_folder);
            exit;
        }
    }elseif(isset ($_GET['tab'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');

        if($_GET['tab']=="layouts") {
            //$layout_list=$commons->get_template_list("layout");
            $layout_list=$commons->get_template_list("layouts");
            $savant->assign('template_list', $layout_list);
        }elseif($_GET['tab']=="structures") {
            $structure_list=$commons->get_template_list("structures");
            $savant->assign('template_list', $structure_list);
        }elseif($_GET['tab']=="pages") {
            $pgtemp_list=$commons->get_template_list("page_templates");
            $savant->assign('template_list', $pgtemp_list);
        }else {
            //Header('Location: index.php');
            //exit;
        }
        $savant->assign('template_type',$_GET['tab']);
        $savant->display('template_editor/index.tmpl.php');
    }else{
        $savant->display('template_editor/create.tmpl.php');
    }
    require(TR_INCLUDE_PATH.'footer.inc.php');
    exit;
}else{
    Header('Location: ../index.php');
    exit;
}
?>