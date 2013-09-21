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

global $_current_user;

if (isset($_current_user) && $_current_user->isAdmin()) {
    require(TR_INCLUDE_PATH.'header.inc.php');

    if(isset ($_POST['submit'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');
        $template_folder=$commons->create_template_dir($_POST['template_type'], $_POST['template_name']);

        $commons->create_template_metadata($_POST['template_type'],$template_folder, $_POST['template_name'], $_POST['template_desc'],
            $_POST['maintainer_name'], $_POST ['maintainer_email'], $_POST ['template_url'], $_POST['template_license'],
            $_POST ['release_version'], $_POST ['release_date'], $_POST ['release_state'], $_POST ['release_notes']);

        if($_POST['template_type']=='structure') {
            $content=$commons->parse_to_XML('<structure version="0.1"></structure>');
            $commons->save_xml($content, 'structures/'.$template_folder, 'content.xml');
            Header('Location: edit_structure.php?temp='.$template_folder);
            
        }elseif($_POST['template_type']=='layout') {
            $commons->save_file('layout/'.$template_folder, $template_folder.'.css');
            Header('Location: edit_layout.php?temp='.$template_folder);
        }elseif($_POST['template_type']=='page_template') {
            $commons->save_file('page_template/'.$template_folder, $template_folder.'.html');
            Header('Location: edit_page.php?temp='.$template_folder);
        }
    }elseif(isset ($_GET['tab'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');

        if($_GET['tab']=="layout") {
            $layout_list=$commons->get_template_list("layout");
            $savant->assign('template_list', $layout_list);
        }elseif($_GET['tab']=="structures") {
            $structure_list=$commons->get_template_list("structure");
            $savant->assign('template_list', $structure_list);
        }elseif($_GET['tab']=="pages") {
            $pgtemp_list=$commons->get_template_list("page_template");
            $savant->assign('template_list', $pgtemp_list);
        }else {
            Header('Location: index.php');
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
}
?>