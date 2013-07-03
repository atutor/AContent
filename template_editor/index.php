<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');
require_once(TR_INCLUDE_PATH.'classes/DAO/UsersDAO.class.php');

global $_current_user;

if (isset($_current_user)) {
    require(TR_INCLUDE_PATH.'header.inc.php');

    if(isset ($_GET['create'])) {
    $savant->display('template_editor/create.tmpl.php');

    }elseif(isset ($_POST['submit'])) {
        require('classes/TemplateCommons.php');
        $commons=new TemplateCommons('../templates');
        if(!$commons->template_exists($_POST['template_type'], $_POST['template_name'])) {
            $commons->create_template_metadata($_POST['template_type'], $_POST['template_name'], $_POST['template_desc'],
                $_POST['maintainer_name'], $_POST ['maintainer_email'], $_POST ['template_url'], $_POST['template_license'],
                $_POST ['release_version'], $_POST ['release_date'], $_POST ['release_state'], $_POST ['release_notes']);
        }else {
            echo "Theme named ".$_POST['template_name']. " already exists";
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
}
?>