        <style>
            li{
                margin:4px 0 4px 1px;               
            }
            .items{
                border: 1px dashed #eed;
                width:150px; height:20px;
                margin:2px 0 0 2px;text-decoration:none;
            }
            .items:hover{
                border: 1px dashed #909080;
            }
            .node_icons{
                margin-left:-25px;
            }
            #toolbar img:hover{
                background:#b2bbd0;
                border: 1px solid #0a246a;
            }
            #toolbar img{
                padding:2px;
                border: 1px solid #f0f0ee;
            }
            #toolbar img[active=false]{
                background:#f0f0ee;
                opacity:0.3;
                border: 1px solid #f0f0ee;
            }
            #toolbar button:disabled{
                background:#f0f0ee;
                border: 1px solid #f0f0ee;
                color:#a5a4a4;
            }
        </style>
<div id='toolbar' style='width:80%; height:100; background:#f0f0ee; border: 1px solid #123; margin:5px; padding:3px;'>
    <div style='float:left; margin:0 10px 1px 10px;'>
        <img class="btn_history" id="btn_undo" src="<?php echo $this->image_path."/undo.png"; ?>">
        <img class="btn_history" id="btn_redo" src="<?php echo $this->image_path."/redo.png"; ?>">
        <img class="btn_insert" id="insert_folder" src="<?php echo $this->image_path."/add_sub_folder.gif"; ?>">
        <img class="btn_insert" id="insert_page" src="<?php echo $this->image_path."/add_sub_page.gif"; ?>">
        <img class="btn_insert" id="insert_page_templates" src="<?php echo $this->image_path."/tree/tree_page_templates.gif"; ?>">
        <img class="btn_insert" id="insert_page_template"  src="<?php echo $this->image_path."/tree/tree_page_template.gif"; ?>">
        <img class="btn_insert" id="insert_tests" src="<?php echo $this->image_path."/tree/tree_tests.gif"; ?>">
        <img class="btn_insert" id="insert_test" src="<?php echo $this->image_path."/tree/tree_test.gif"; ?>">
    </div>

    <div style='float:left; margin:-1px 15px 1px 15px;'>
        <label id="lbl_node_name" for="node_name">Name:</label>
        <input id="node_name" type="text" size="15" maxlength="25" value="" accesskey='n'>
        <label id="lbl_node_min" for="node_min">Min:</label>
        <input id="node_min" type="text" size="3" maxlength="3" value="" accesskey='o'>
        <label id="lbl_node_max" for="node_min">Max:</label>
        <input id="node_max" type="text" size="3" maxlength="3" value="" accesskey='p'>
    </div>
    <img class="btn_delete"  accesskey='d' value="Delete" id="btn_delete" src="<?php echo $this->image_path."/x.gif"; ?>">
    <img class="btn_move"  accesskey='u' value="Up" id="btn_up" src="<?php echo $this->image_path."/move_up.png"; ?>">
    <img class="btn_move"   value="Down" id="btn_down" src="<?php echo $this->image_path."/move_down.png"; ?>">

</div>

<textarea  id="tarea" rows="35" cols="60"  style='float:left;'>
<?php
    echo $this->xml_script;   
?>
</textarea>

<div id='preview' style='float:left; width:400px; min-height:300px; border: 1px solid #123; margin:5px;'>
</div>