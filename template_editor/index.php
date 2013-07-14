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

    if(isset ($_GET['create'])) {
    $savant->display('template_editor/create.tmpl.php');

    }elseif(isset ($_POST['submit'])) {
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
        }
    }else {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');
        $layout_list=$commons->get_template_list("layout");
        $structure_list=$commons->get_template_list("structure");
        $pgtemp_list=$commons->get_template_list("page_template");
        $savant->assign('layout_list', $layout_list);
        $savant->assign('structure_list', $structure_list);
        $savant->assign('pgtemp_list', $pgtemp_list);
        $savant->display('template_editor/index.tmpl.php');
    }
    require(TR_INCLUDE_PATH.'footer.inc.php');
    exit;
}else{
    Header('Location: ../index.php');
}
?>