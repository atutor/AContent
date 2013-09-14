<form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template; ?>" method="post" name="form" enctype="multipart/form-data">
    <div align="center">
        <table class="etabbed-table" border="0" cellpadding="0" cellspacing="0" width="95%">
            <tbody><tr>
                    <td class="editor_tab_selected" ><?php echo _AT('edit_template'); ?></td>
                    <td class="tab-spacer">&nbsp;</td>
                    <td class="editor_tab">
                        <a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_meta.php?type=page_template&temp=<?php echo $this->template; ?>">
                            <?php echo _AT('edit_metadata'); ?>
                        </a>
                    </td>
                    <td class="tab-spacer">&nbsp;</td>
                    <td class="editor_tab" >
                        <a style="font-weight:bold; text-decoration:none;" href="template_editor/delete.php?type=page_template&temp=<?php echo $this->template; ?>">
                            <?php echo _AT('delete'); ?>
                        </a>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody></table>
    </div>
    <div class="input-form" style="width: 95%;">
        <div id='page_topbar'>
            <label for="mode_radios" style="margin-left:15px;"><?php echo _AT('edit_mode'); ?></label>
            <span class="bordered" id="mode_radios">
                <input type="radio" name="edit_mode" value="0" id="basic_mode" title="<?php echo _AT('basic'); ?>" checked="checked" accesskey="b">
                <label for="basic_mode"><?php echo _AT('basic'); ?></label>
                <input type="radio" name="edit_mode" value="1" id="adv_mode" title="<?php echo _AT('advanced'); ?>" accesskey="a">
                <label for="adv_mode"><?php echo _AT('advanced'); ?></label>
            </span>
            <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?>" accesskey="s"  style="float:right;"/>
        </div>

        <table border="0" cellpadding="4" style="width:100%">
            <tr>
                <td valign="top" height="100%" >
                    <div id='page_toolbar'>
                        <div class="page_toolline">
                            <div style='float:left;'>
                                <img id="bold" src="<?php echo $this->base_path;?>images/clr.gif" arg="b" class="buttons wrap" alt='<?php echo _AT('bold'); ?>' title='<?php echo _AT('bold'); ?>'>
                                <img id="italic" src="<?php echo $this->base_path;?>images/clr.gif" arg="i" class="buttons  wrap" alt='<?php echo _AT('italic'); ?>' title='<?php echo _AT('italic'); ?>'>
                                <img id="underline" src="<?php echo $this->base_path;?>images/clr.gif" arg="u" class="buttons  wrap"  alt='<?php echo _AT('underline'); ?>'title='<?php echo _AT('underline'); ?>'>
                                <img id="align-left" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons attrib" arg="left" alt='<?php echo _AT('align_left'); ?>' title='<?php echo _AT('align_left'); ?>'>
                                <img id="align-center" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons  attrib" arg="center" alt='<?php echo _AT('align_center'); ?>' title='<?php echo _AT('align_center'); ?>'>
                                <img id="align-right" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons  attrib" arg="right" alt='<?php echo _AT('align_right'); ?>' title='<?php echo _AT('align_right'); ?>'>
                                <img id="align-justify" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons  attrib" arg="justify" alt='<?php echo _AT('justify'); ?>' title='<?php echo _AT('justify'); ?>'>
                            </div>
                            <div style='float:left; margin:0 10px 1px 20px;'>
                                <img id="insert-paragraph" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons  insert" alt='<?php echo _AT('paragraph'); ?>' title='<?php echo _AT('paragraph'); ?>'>
                                <img id="insert-ulist" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons insert" arg="ul" alt='<?php echo _AT('unordered_list'); ?>' title='<?php echo _AT('unordered_list'); ?>'>
                                <img id="insert-olist" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons insert" arg="ol" alt='<?php echo _AT('ordered_list'); ?>' title='<?php echo _AT('ordered_list'); ?>'>
                                <img id="insert-image" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons insert" alt='<?php echo _AT('image'); ?>' title='<?php echo _AT('image'); ?>'>
                                <img id="insert-table" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" alt='<?php echo _AT('table'); ?>' title='<?php echo _AT('table'); ?>'>
                                <img id="insert-link" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons  insert" alt='<?php echo _AT('link'); ?>' title='<?php echo _AT('link'); ?>'>
                            </div>
                        </div>
                        <div class="layout_toolline">
                            <select id="format" name="format" style="width:130px;">
                                <option value="null"><?php echo _AT('format'); ?></option>
                                <option value="p">P</option>
                                <option value="h2">H2</option>
                                <option value="h3">H3</option>
                                <option value="h4">H4</option>
                            </select>
                            <select id="font-family" name="font-family" style="width:130px;">
                                <option value="null" selected><?php echo _AT('font_family'); ?></option>
                                <option value="georgia">Georgia</option>
                                <option value="times new roman">Times New Roman</option>
                                <option value="arial">Arial</option>
                                <option value="verdana">Verdana</option>
                            </select>
                            <select id="font-size" name="font-size" style="width:100px;">
                                <option value="null" selected><?php echo _AT('font_size'); ?></option>
                                <option value="8px">8</option>
                                <option value="10px">10</option>
                                <option value="12px">12</option>
                                <option value="14px">14</option>
                                <option value="16px">16</option>
                                <option value="18px">18</option>
                            </select>
                        </div>
                        <div id="table_settings" class="layout_toolline">
                            <label for="num_rows"><?php echo _AT('number_of_rows'); ?>:</label>
                            <input id="num_rows" type="text" size="5" style="width:30px;" value="1">
                            <label for="num_cols"><?php echo _AT('number_of_columns'); ?>:</label>
                            <input id="num_cols" type="text" size="5" style="width:30px;" value="1">
                            <img id="add_table" class="buttons insert" src="<?php echo $this->base_path;?>images/clr.gif" alt='<?php echo _AT('insert'); ?>' title='<?php echo _AT('insert'); ?>'>
                        </div>
                    </div>
                    <textarea  id="page_text" name="page_text" rows="35" cols="60"  style='border:1px solid #cccccc; resize: none;background-color:#ffffff; min-height:400px; '><?php  echo $this->html_code; ?></textarea>
                    <div id='page_preview' contenteditable="true" style='height:100%; min-width:400px; min-height:300px; margin:15px;'>

                    </div>
                </td>
            </tr>
        </table>
    </div>
</form>

<div class="input-form" style="width: 95%;">
    <h4 style="margin:2px 0 7px 0;"><?php echo _AT('screenshot'); ?></h4>
    <div>
        <?php
        $img_path=$this->base_path."templates/page_template/". $this->template."/screenshot.png";
        if(isset($this->screenshot))  echo "<img src='".$img_path."' alt='"._AT('screenshot')."'>";
        ?>
        <canvas width=200 height=200 id="screenshot_canvas"></canvas>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template; ?>" method="post" enctype="multipart/form-data">
        <label for="file"><?php echo _AT('file'); ?>:</label>
        <input type="file" name="file" id="file" accesskey="n" title="<?php echo _AT('upload'); ?>">
        <input type="submit" name="uploadscrn" value="<?php echo _AT('upload'); ?>">
        <input id="generate_scrn" type="submit" value="<?php echo _AT('auto_generate'); ?>">
    </form>
</div>