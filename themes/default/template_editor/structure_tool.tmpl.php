        <style>
            li{

                margin:1px 0 0 1px;
                background:#ffd;
            }
            .items{
                border: 1px dashed #eed;
                width:150px; height:20px;
                margin:2px 0 0 2px;
            }
            .items:hover{
                border: 1px dashed #e00;
            }
        </style>
<div id='toolbar' style='width:80%; height:100; border: 1px solid #123; margin:5px;'>
    <button class="btn" id="toxml"  ><<<</button>
    <button class="btn" id="totree" >>>>></button>
    <label id="lbl_node_name">Name:</label>
    <input id="node_name" type="text" size="15" maxlength="25" value="">|
    <button class="btn_history" id="btn_undo" >undo</button>
    <button class="btn_history" id="btn_redo" >redo</button>
        <button class="btn_delete" id="btn_delete" >X</button>
    <div style='width:100%;'> Insert
        <button class="btn_insert" id="insert_folder"  >Folder</button>
        <button class="btn_insert" id="insert_page" >Page</button>
        <button class="btn_insert" id="insert_page_templates" >Page Templates</button>
        <button class="btn_insert" id="insert_page_template" >Page Template</button>
        <button class="btn_insert" id="insert_tests" >Tests</button>
        <button class="btn_insert" id="insert_test" >Test</button>
    </div>
</div>

<textarea id="tarea" rows="35" cols="60"  style='float:left;'>
<?php
    echo $this->xml_script;
    
?>
</textarea>

<div id='preview' style='float:left; width:500px; min-height:300px; border: 1px solid #123; margin:5px;'>
</div>