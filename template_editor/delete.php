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

if (isset($_current_user) && $_current_user->isAdmin()) {

$_GET['type'] = $addslashes(strip_tags($_GET['type']));
$_GET['temp'] = $addslashes(strip_tags($_GET['temp']));

    if(isset ($_GET['type']) && isset ($_GET['temp'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');
        $type=strip_tags($addslashes($_GET['type']));
        if($_GET['type']=='page') $type='page_template';
        if(isset ($_POST['cancel'])){
            $msg->addFeedback('CANCELLED');
            Header('Location: index.php?tab='.$type);
            exit;
        }

        if(!$commons->template_exists($type, $_GET['temp'])){
            $msg->addError('SELECT_ONE_ITEM');
            Header('Location: index.php?tab='.$type);
            exit;
        }
        if(isset ($_POST['submit'])) {
            $commons->delete_template($type, $_GET['temp']);
            Header('Location: index.php?tab='.$type);
        }
        require(TR_INCLUDE_PATH.'header.inc.php');
        $metadata=$commons->load_metadata($type,$_GET['temp']);
        $savant->assign('template_name', $metadata['template_name']);
        $savant->assign('template_dir', $_GET['temp']);
        $savant->assign('template_type', $_GET['type']);
        $savant->display('template_editor/delete.tmpl.php');
    }else {
        Header('Location: ../index.php?tab='.$type);
    }
    require(TR_INCLUDE_PATH.'footer.inc.php');
}else {
    Header('Location: ../index.php?tab='.$type);
}
?>
