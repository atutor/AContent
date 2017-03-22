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

//if (isset($_current_user) && $_current_user->isAdmin()) {
if (isset($_current_user) && Utility::authenticate($privs[TR_PRIV_TEMPLATE_EDITOR])) {

$_GET['type'] = $addslashes(strip_tags($_GET['type']));
$_GET['temp'] = $addslashes(strip_tags($_GET['temp']));

    if(isset ($_GET['type']) && isset ($_GET['temp'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');
        $type=strip_tags($addslashes($_GET['type']));
        
        
        if($_GET['type']=='pages') $type='page_templates';
       
        if(isset ($_POST['cancel'])){
            $msg->addFeedback('CANCELLED');
            if($_GET['type']=='page_templates') $type='page';
            if($_GET['type']=='page_templates') $app_type='page';
            if($_GET['type']=='structures') $type='structure';
            if($_GET['type']=='structures') $app_type='structure';
            if($_GET['type']=='layouts') $type='layouts';
            if($_GET['type']=='layouts') $app_type='layout';
            header('Location: edit_'.$app_type.'.php?type='.$type.SEP.'temp='.$_GET['temp'] );
            exit;
        }

        if(isset ($_POST['submit'])) {
            if($_GET['type']=='page_templates') $type='page_templates';
            if($_GET['type']=='page_templates') $app_type='pages';
             if($_GET['type']=='structures') $type='structures';
             if($_GET['type']=='structures') $app_type='structures';
             if($_GET['type']=='layouts') $type='layouts';
             if($_GET['type']=='layouts') $app_type='layouts';

            if($commons->delete_template($type, $_GET['temp']) == true){
                $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
                header('Location: index.php?tab='.$app_type);
                exit;
            }  else{
                $msg->addError('DIR_NOT_DELETED');
                $app_type = rtrim($app_type, "s");
                header('Location: edit_'.$app_type.'.php?type='.$type.SEP.'temp='.$_GET['temp'] );
                exit;
            }
 
        }
        
        if(!$commons->template_exists($type, $_GET['temp'])){
            if($_GET['type']=='page_templates') $type='pages';
             if($_GET['type']=='structures') $type='structures';
             if($_GET['type']=='layout') $type='layouts';
             if($_GET['type']=='layouts') $type='layouts';
            $msg->addError('SELECT_ONE_ITEM');
            header('Location: index.php?tab='.$type);
            exit;
        }
        require(TR_INCLUDE_PATH.'header.inc.php');
        $metadata=$commons->load_metadata($type,$_GET['temp']);
        $savant->assign('template_name', $metadata['template_name']);
        $savant->assign('template_dir', $_GET['temp']);
        if($_GET['type'] =='page_templates'){
            $_GET['type'] = 'pages';
        }
        $savant->assign('template_type', $_GET['type']);
        $savant->display('template_editor/delete.tmpl.php');
    }else {
        header('Location: ../index.php?tab='.$type);
        exit;
    }
    require(TR_INCLUDE_PATH.'footer.inc.php');
}else {
    header('Location: ../index.php?tab='.$type);
    exit;
}
?>
