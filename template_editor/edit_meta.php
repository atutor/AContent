<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

global $_current_user;

if (isset($_current_user) && $_current_user->isAdmin()) {
    require(TR_INCLUDE_PATH.'header.inc.php');
    require('classes/TemplateCommons.php');
    $commons=new TemplateCommons('../templates');

    if(isset($_GET['temp']) && isset($_GET['type'])) {
        $type=$_GET['type'];
        if($_GET['type']=='page') $type='page_template';
        
        if(!$commons->template_exists($type, $_GET['temp']) || isset ($_POST['cancel'])) {
            Header('Location: index.php');
            exit;
        }
        if(isset($_POST['submit'])) {
            $type=$_GET['type'];
            if($_GET['type']=='page') $type='page_template';

            if($commons->template_exists($type, $_GET['temp'])) {
                $commons->create_template_metadata($type,$_GET['temp'], $_POST['template_name'], $_POST['template_desc'],
                    $_POST['maintainer_name'], $_POST ['maintainer_email'], $_POST ['template_url'], $_POST['template_license'],
                    $_POST ['release_version'], $_POST ['release_date'], $_POST ['release_state'], $_POST ['release_notes']);
                Header('Location: index.php');
            }

        }else {
            $metadata=$commons->load_metadata($type,$_GET['temp']);
            $savant->assign('metadata', $metadata);
            $savant->assign('template_dir', $_GET['temp']);
            $savant->assign('template_type', $_GET['type']);
            $savant->display('template_editor/edit_meta.tmpl.php');
        }
        require(TR_INCLUDE_PATH.'footer.inc.php');
    }else {
        Header('Location: ../index.php');
    }
}else {
    Header('Location: ../index.php');
}

?>
