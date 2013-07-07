        <style>
            li{
                margin:4px 0 4px 1px;
                background:#ffd;
            }
            .items{
                border: 1px dashed #eed;
                width:150px; height:20px;
                margin:2px 0 0 2px;text-decoration:none;
            }
            .items:hover{
                border: 1px dashed #e00;
            }
            .node_icons{
                margin-left:-25px;
            }
            #toolbar button{
                background:#f0f0ee;
                border: 1px solid #f0f0ee;
                font-weight:bold;
                font-size:12px;
            }
            #toolbar button:hover{
                background:#b2bbd0;
                border: 1px solid #0a246a;
            }
            #toolbar button:disabled{
                background:#f0f0ee;
                border: 1px solid #f0f0ee;
                color:#a5a4a4;
            }
        </style>
<div id='toolbar' style='width:80%; height:100; background:#f0f0ee; border: 1px solid #123; margin:5px; padding:3px;'>
    <button class="btn" id="toxml"  ><<<</button>
    <button class="btn" id="totree" >>>>></button>
    <label id="lbl_node_name" for="node_name">Name:</label>
    <input id="node_name" type="text" size="15" maxlength="25" value="" accesskey='n'>
    <label id="lbl_node_min" for="node_min">Min:</label>
    <input id="node_min" type="text" size="3" maxlength="3" value="" accesskey='o'>
    <label id="lbl_node_max" for="node_min">Max:</label>
    <input id="node_max" type="text" size="3" maxlength="3" value="" accesskey='p'>
    <button class="btn_history" id="btn_undo">undo</button>
    <button class="btn_history" id="btn_redo">redo</button>
    <button class="btn_delete"  accesskey='d' value="Delete" id="btn_delete" >X</button>

    <button class="btn_move"  accesskey='d' value="Up" id="btn_up" >up</button>
    <button class="btn_move"  accesskey='d' value="Down" id="btn_down" >Dwn</button>

    <div style='width:100%;'> Insert
        <button class="btn_insert" id="insert_folder"  >Folder</button>
        <button class="btn_insert" id="insert_page" >Page</button>
        <button class="btn_insert" id="insert_page_templates" >Page Templates</button>
        <button class="btn_insert" id="insert_page_template" >Page Template</button>
        <button class="btn_insert" id="insert_tests" >Tests</button>
        <button class="btn_insert" id="insert_test" >Test</button>
    </div>
</div>

<textarea  id="tarea" rows="35" cols="60"  style='float:left;'>
<?php
    echo $this->xml_script;   
?>
</textarea>

<div id='preview' style='float:left; width:500px; min-height:300px; border: 1px solid #123; margin:5px;'>
</div>