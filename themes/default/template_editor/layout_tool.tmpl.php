<form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template; ?>" method="post" name="form" enctype="multipart/form-data">
    <div align="center">
        <table class="etabbed-table" border="0" cellpadding="0" cellspacing="0" width="95%">
            <tbody><tr>
                    <td class="editor_tab_selected" ><?php echo _AT('edit_template'); ?></td>
                    <td class="tab-spacer">&nbsp;</td>
                    <td class="editor_tab">
                        <a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_meta.php?type=layout&temp=<?php echo $this->template; ?>">
                            <?php echo _AT('edit_metadata'); ?>
                        </a>
                    </td>
                    <td class="tab-spacer">&nbsp;</td>
                    <td class="editor_tab" >
                        <a style="font-weight:bold; text-decoration:none;" href="template_editor/delete.php?type=layout&temp=<?php echo $this->template; ?>">
                            <?php echo _AT('delete'); ?>
                        </a>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody></table>
    </div>
    <div class="input-form" style="width: 95%;">
        <div id='layout_toolbar'>
            <div style="height:30px; padding:5px 8px 1px 5px;">
                <div style="float:left;">
                    <img id="bold" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" alt='<?php echo _AT('bold'); ?>' title='<?php echo _AT('bold'); ?>'>
                    <img id="italic" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" alt='<?php echo _AT('italic'); ?>' title='<?php echo _AT('italic'); ?>'>
                    <img id="underline" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons"  alt='<?php echo _AT('underline'); ?>'title='<?php echo _AT('underline'); ?>'>
                    <img id="align-left" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="left" alt='<?php echo _AT('align_left'); ?>' title='<?php echo _AT('align_left'); ?>'>
                    <img id="align-center" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="center" alt='<?php echo _AT('align_center'); ?>' title='<?php echo _AT('align_center'); ?>'>
                    <img id="align-right" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="right" alt='<?php echo _AT('align_right'); ?>' title='<?php echo _AT('align_right'); ?>'>
                    <img id="align-justify" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="justify" alt='<?php echo _AT('justify'); ?>' title='<?php echo _AT('justify'); ?>'>
                </div>
                <div style="float:left; margin:0 15px 0 20px;">
                    <label for="font-family"><?php echo _AT('font_family'); ?>:</label>
                    <select id="font-family" name="font-family">
                        <option value="courier">Courier</option>
                        <option value="georgia">Georgia</option>
                        <option value="times new roman">Times New Roman</option>
                        <option value="arial">Arial</option>
                        <option value="helvetica">Helvetica</option>
                        <option value="verdana" selected="">Verdana</option>
                    </select>
                    <label for="font-size"><?php echo _AT('font_size'); ?>:</label>
                    <input id="font-size" type="text" size="5">
                    <label for="font-color"><?php echo _AT('font_color'); ?>:</label>
                    <input id="font-color" type="text" size="6" maxlength="6">
                </div>
                <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?>" accesskey="s"  style="float:right;"/>
            </div>
            
            <div style="float:left; margin:0 15px 0 0;">
                <label for="background-color"><?php echo _AT('background-color'); ?>:</label>
                <input id="background-color" name="background-color" type="text" size="6" maxlength="6">
                <label for="background-image"><?php echo _AT('background-image'); ?>:</label>
                <select id="background-image"  style="width:100px;">
                    <?php
                            foreach ($this->image_list as $image) {
                                echo "<option value='".$this->template."/".$image."'>".$image."</option>";
                            }
                    ?>                    
                </select>
            </div>

            <div style="float:left; margin:0 15px 0 20px;">
                <label for="border-width"><?php echo _AT('border-width'); ?>:</label>
                <input id="border-width" name="background-color" type="text" size="3" maxlength="3">
                <label for="border-style"><?php echo _AT('border-style'); ?>:</label>
                <select id="border-style">
                    <option value="solid">Solid</option>
                    <option value="dashed">Dashed</option>
                    <option value="dotted">Dotted</option>
                </select>
                <label for="border-color"><?php echo _AT('border-color'); ?>:</label>
                <input id="border-color" type="text" size="6" maxlength="6">
            </div>
        </div>
        <div id="status"></div>
        <table border="0" cellpadding="4" style="width:100%">
            <tr>
                <td valign="top" height="100%"> <textarea  id="css_text" name="css_text" rows="35" cols="60"  style='border:1px solid #cccccc; resize: none;background-color:#ffffff; min-height:400px'> <?php  echo $this->css_code; ?></textarea></td>
                <td valign="top" height="100%">
                    <div id='css_preview' style='height:100%; width:400px; min-height:300px; margin:15px;'>
                        <style id="preview_styles"></style>
                        <div id="content"><h3>Heading 3</h3><h2>Heading 2</h2>Some text content<ul><li>List Item</li><li>List Item</li></ul></div>
                    </div>
                    <div id="css_dumy"></div>
                </td>
            </tr>
        </table>
    </div>
</form>

<div class="input-form" style="width: 95%;">
    <form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template; ?>" method="post" enctype="multipart/form-data">
        <label for="file"><?php echo _AT('file'); ?>:</label>
        <input type="file" name="file" id="file">
        <input type="submit" name="upload" value="<?php echo _AT('upload'); ?>">
    </form>
    <div class="confirm" id="image_confirm">
        <?php echo _AT('confirm_image_delete', "%s"); ?>
        <input class="btn_delete" type="submit" name="delete" value="<?php echo _AT('yes'); ?>">
        <input class="btn_delete" type="submit" name="cancel" value="<?php echo _AT('no'); ?>">
    </div>
    <div style="margin:5px 5px 0 0;"><?php
        foreach ($this->image_list as $image) {
            $display_name;
            if(strlen( $image)<17) $display_name=$image;
            else $display_name=substr($image, 0, 6)."...". substr($image, strlen( $image)-7, 7);

            echo "<div class='image_item' file='".$image."'><div class='thumbnail'>";
            echo "<img src='".$this->base_path."templates/layout/". $this->template."/".$this->template."/".$image."'>";
            echo "</div>";
            echo "<img class='delete_image' src='".$this->base_path."images/x.gif' file='".$image."' alt='"._AT('delete_image')."' title='"._AT('delete_image')."'>";
            echo "<div>".$display_name."</div></div>";
        }
        ?>
    </div>
</div>
