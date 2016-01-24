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

    global $_current_user, $msg;
    $type=htmlspecialchars($_GET['type'], ENT_QUOTES, 'UTF-8');
    $temp=htmlspecialchars($_GET['temp'], ENT_QUOTES, 'UTF-8');
    if(isset($_POST['cancel'])) {
        $msg->addFeedback('CANCELLED');
        header('Location:edit_'.$type.'.php?type='.$type.SEP.'temp='.$temp);
        exit;
    }
if (isset($_current_user) && $_current_user->isAdmin()) {
    require('classes/TemplateCommons.php');
    $commons=new TemplateCommons('../templates');

    if(isset($temp) && isset($type)) {
        if($type=='page') $type='page_templates';
        
        if(!$commons->template_exists($type, $temp) || isset ($_POST['cancel'])) {
            header('Location: edit_layout.php?temp='.$temp);
            exit;
        }
        if(isset($_POST['submit'])) {
            //$type=htmlspecialchars($_GET['type']);
            //$temp=htmlspecialchars($_GET['temp']);
            if($type=='page') $type='page_templates';
            //$_GET['temp'] = htmlspecialchars($_GET['temp'], ENT_QUOTES, 'UTF-8');
            $_POST['template_name'] = htmlspecialchars($_POST['template_name'], ENT_QUOTES, 'UTF-8');
            $_POST['template_desc'] = htmlspecialchars($_POST['template_desc'], ENT_QUOTES, 'UTF-8');
            $_POST['maintainer_name'] = htmlspecialchars($_POST['maintainer_name'], ENT_QUOTES, 'UTF-8'); 
            $_POST ['maintainer_email'] = htmlspecialchars($_POST['maintainer_email'], ENT_QUOTES, 'UTF-8'); 
            $_POST ['template_url'] = htmlspecialchars($_POST['template_url'], ENT_QUOTES, 'UTF-8'); 
            $_POST['template_license'] = htmlspecialchars($_POST['template_license'], ENT_QUOTES, 'UTF-8');
            $_POST ['release_version'] = htmlspecialchars($_POST['release_version'], ENT_QUOTES, 'UTF-8'); 
            $_POST ['release_date'] = htmlspecialchars($_POST['release_date'], ENT_QUOTES, 'UTF-8'); 
            $_POST ['release_state'] = htmlspecialchars($_POST['release_state'], ENT_QUOTES, 'UTF-8'); 
            $_POST ['release_notes'] = htmlspecialchars($_POST['release_notes'], ENT_QUOTES, 'UTF-8');
            if($commons->template_exists($type, $temp)) {
                $commons->create_template_metadata($type,
                    $temp, 
                    $_POST['template_name'], 
                    $_POST['template_desc'],
                    $_POST['maintainer_name'], 
                    $_POST ['maintainer_email'], 
                    $_POST ['template_url'], 
                    $_POST['template_license'],
                    $_POST ['release_version'], 
                    $_POST ['release_date'], 
                    $_POST ['release_state'], 
                    $_POST ['release_notes']);
                    header('Location:?temp='.$temp);
                    exit;
            }

        }
        //else {
        require(TR_INCLUDE_PATH.'header.inc.php');
            $metadata=$commons->load_metadata($type,$temp);
            $savant->assign('metadata', $metadata);
            $savant->assign('template_dir', $temp);
            $savant->assign('template_type', $type);
            $savant->display('template_editor/edit_meta.tmpl.php');
        //}
        require(TR_INCLUDE_PATH.'footer.inc.php');
        exit;
        
    }else {
        $msg->addFeedback('FAILED');
        header('Location:edit_layout.php?temp='.$temp);
        exit;
        //Header('Location: ../index.php');
    }
}else {
        $msg->addFeedback('FAILED2');
        header('Location:edit_layout.php?temp='.$temp);
        exit;
    //Header('Location: ../index.php');
}

?>
