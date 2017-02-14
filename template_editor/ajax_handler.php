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
require('classes/TemplateCommons.php');
global $_base_path;
debug_to_log(' in handler');
if(isset ($_GET['get'])) {
    if($_GET['get']=='struc_elements') {
        get_structure_elements();
    }elseif($_GET['get']=='base_path') {
        echo $_base_path;
    }
}elseif(isset ($_GET['action'])) {
    $commons=new TemplateCommons('../templates');
    if($_GET['action']=='delete_image') {
        $template=$_GET['temp'];
        $file=$_GET['file'];
        $commons->delete_file("layouts/".$template."/".$template, $file);
    }
}elseif(isset ($_POST['action']) && $_POST['action']=='upload_image') {
    $dataURL = $_POST["image"];
    $template=$_POST['temp'];
   // debug_to_log($_POST);
    $commons=new TemplateCommons('../templates');
    if(strstr($_SERVER['HTTP_REFERER'], "edit_page")){
        $commons->save_DataURL_image($dataURL,"page_templates/".$template,"screenshot.png");
    }else if(strstr($_SERVER['HTTP_REFERER'], "edit_layout")){
        $commons->save_DataURL_image($dataURL,"layouts/".$template,"screenshot-".$template.".png");
    }
}

/**
 * Prints an array of structure elements' names in current language as a JSON string
 * @access  private
 * @author  SupunGS
 */
function get_structure_elements() {
    $elements=array();
    $elements['structure']=_AT('structure');
    $elements['folder']=_AT('folder');
    $elements['page']=_AT('template_page');
    $elements['page_templates']=_AT('page_templates_tag');
    $elements['page_template']=_AT('page_template_tag');
    $elements['tests']=_AT('tests');
    $elements['test']=_AT('test');
    $elements['forum']=_AT('forum');

    echo json_encode($elements);
}

?>
