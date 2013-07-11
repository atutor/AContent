<?php
define('TR_INCLUDE_PATH', '../include/');
require(TR_INCLUDE_PATH.'vitals.inc.php');

if(isset ($_GET['get'])){
    if($_GET['get']=='struc_elements'){
        get_structure_elements();
    }
}


function get_structure_elements(){
    $elements=array();
    $elements['structure']=_AT('structure');
    $elements['folder']=_AT('folder');
    $elements['page']=_AT('template_page');
    $elements['page_templates']=_AT('page_templates_tag');
    $elements['page_template']=_AT('page_template_tag');
    $elements['tests']=_AT('tests');
    $elements['test']=_AT('test');

    echo json_encode($elements);
}

?>
