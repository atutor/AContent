<?php
if($this->lastelement == ''){
    $this->lastelement  = "#content h2";
}
if($this->lastelement != ''){
    // Send focus back to the element being edited pressing Save
?>
    <script type="text/javascript">
    $( document ).ready(function() {
        var keyclick = function(thisfunction, thisrule){
            $(thisrule).keypress(
               thisfunction
            ).click(
                thisfunction
            );
        }
        var lastelement_focus = function(event){
              //  event.stopPropagation(); 
                get_selected_style("<?php echo $this->lastelement; ?>");
                setup_toolbar();
                $('#lastelement').val("<?php echo $this->lastelement; ?>");
                $("<?php echo $this->lastelement; ?>").focus();
        }
        lastelement_focus();
    });
    </script>
<?php } ?>

<div id="subnavlistcontainer">
    <div id="sub-navigation"> 
    <span style="width:3em; float:left;margin-left:2em;margin-right:-2em;">
    <a href="template_editor/index.php?tab=layouts"><img src="themes/default/images/previous.png" alt="back"></a>
    </span>
        <ul id="subnavlist">
            <?php 
                echo '<li class="active"><b>'. _AT('edit_template') . '</b></li>';
                echo '<li><a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_meta.php?type=layouts&temp='.$this->template.'">'. _AT('edit_metadata') . '</a></li>';
                echo '<li><a style="font-weight:bold; text-decoration:none;" href="template_editor/delete.php?type=layouts&temp='.$this->template.'">'. _AT('delete') . '</a></li>';
            ?>
        </ul>
    </div>
</div>
<form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template.SEP.'rand='.rand(); ?>" method="post" name="form" enctype="multipart/form-data">
    <input id="referer" type="hidden" name="referer" value="<?php echo $this->referer; ?>" />
    <input id="lastelement" type="hidden" name="lastelement" value="<?php echo $this->lastelement; ?>" />
    <div class="input-form" style="width: 95%;">
        <div id='layout_topbar' tabindex="0" accesskey="e" aria-label="<?php echo _AT('template_editor_howto'); ?>">
            <label for="selector"><?php echo _AT('selector'); ?>:</label>
            <input id="selector" type="text" size="15" disabled aria-live="assertive">

            <label for="mode_radios" style="margin-left:15px;"><?php echo _AT('edit_mode'); ?></label>
            <span class="bordered" id="mode_radios">
                <input type="radio" name="edit_mode" value="0" id="basic_mode" title="<?php echo _AT('basic'); ?>" checked="checked" accesskey="b">
                <label for="basic_mode"><?php echo _AT('basic'); ?></label>
                <input type="radio" name="edit_mode" value="1" id="adv_mode" title="<?php echo _AT('advanced'); ?>" accesskey="a">
                <label for="adv_mode"><?php echo _AT('advanced'); ?></label>
            </span>
            <input type="submit" name="submit" value="<?php echo _AT('cancel'); ?>" title="<?php echo _AT('cancel'); ?>"accesskey="c"  style="float:right;"/>
            <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" title="<?php echo _AT('save_changes'); ?>" accesskey="s"  style="float:right;"/>
        </div>
        <div id="status"></div>
        <table border="0" cellpadding="4" style="width:100%">
            <tr>
                <td valign="top" height="100%"> 
                    <div id='layout_toolbar'>                        
                        <div id='layout_basictools'>
                            <div id="layout_toolline">
                                <img id="bold" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" alt='<?php echo _AT('bold'); ?>' title='<?php echo _AT('bold'); ?>'>
                                <img id="italic" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" alt='<?php echo _AT('italic'); ?>' title='<?php echo _AT('italic'); ?>'>
                                <img id="underline" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons"  alt='<?php echo _AT('underline'); ?>'title='<?php echo _AT('underline'); ?>'>
                                <img id="align-left" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="left" alt='<?php echo _AT('align_left'); ?>' title='<?php echo _AT('align_left'); ?>'>
                                <img id="align-center" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="center" alt='<?php echo _AT('align_center'); ?>' title='<?php echo _AT('align_center'); ?>'>
                                <img id="align-right" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="right" alt='<?php echo _AT('align_right'); ?>' title='<?php echo _AT('align_right'); ?>'>
                                <img id="align-justify" src="<?php echo $this->base_path;?>images/clr.gif" class="buttons" arg="justify" alt='<?php echo _AT('justify'); ?>' title='<?php echo _AT('justify'); ?>'>
                            </div>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="font-family"><?php echo _AT('font_family'); ?>:</label>
                                        </td>
                                        <td>
                                            <select id="font-family" name="font-family" style="width:100px;">
                                                <option value="courier"><?php echo _AT('font_courier'); ?></option>
                                                <option value="georgia"><?php echo _AT('font_georgia'); ?></option>
                                                <option value="times new roman"><?php echo _AT('font_times_new_roman'); ?></option>
                                                <option value="arial"><?php echo _AT('font_arial'); ?></option>
                                                <option value="helvetica"><?php echo _AT('font_helvetica'); ?></option>
                                                <option value="verdana" selected=""><?php echo _AT('font_verdana'); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <label for="font-size"><?php echo _AT('font_size'); ?>:</label>
                                        </td>
                                        <td>
                                            <input id="font-size" type="text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="font-color"><?php echo _AT('font_color'); ?>:</label>
                                        </td>
                                        <td>
                                            <input id="font-color" type="text" maxlength="6">
                                        </td>
                                    </tr>          
                                    <tr>
                                        <td>
                                            <label for="border-width"><?php echo _AT('border-width'); ?>:</label>
                                        </td>
                                        <td>
                                            <input id="border-width" name="background-color" type="text" size="6" maxlength="4">
                                        </td>
                                        <td>                                <label for="border-style"><?php echo _AT('border-style'); ?>:</label>
                                        </td>
                                        <td>
                                            <select id="border-style" style="width:100px;">
                                                <option value="none">&nbsp;</option>
                                                <option value="solid"><?php echo _AT('border_solid'); ?></option>
                                                <option value="dashed"><?php echo _AT('border_dashed'); ?></option>
                                                <option value="dotted"><?php echo _AT('border_dotted'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="border-color"><?php echo _AT('border-color'); ?>:</label>
                                        </td>
                                        <td>
                                            <input id="border-color" type="text" size="6" maxlength="6">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            ><label for="background-color"><?php echo _AT('background-color'); ?>:</label>
                                        </td>
                                        <td>
                                            <input id="background-color" name="background-color" type="text" size="6" maxlength="6">
                                        </td>
                                        <td>
                                            <label for="background-image"><?php echo _AT('background-image'); ?>:</label>
                                        </td>
                                        <td>
                                            <select id="background-image"  style="width:100px;">
                                                <option value='none'></option>
                                                <?php
                                                foreach ($this->image_list as $image) {
                                                    echo "<option value='".$this->template."/".$image."'>".$image."</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id='layout_exttools'></div>

                        <div class="" >
                            <label for="new_property"><?php echo _AT('property'); ?>:</label>
                            <input id="new_property" type="text" size="18">
                            <label for="new_value"><?php echo _AT('value'); ?>:</label>
                            <input id="new_value" type="text" size="12">
                            <img id="add_rule" src="<?php echo $this->base_path;?>images/clr.gif" alt='<?php echo _AT('add'); ?>' title='<?php echo _AT('add'); ?>' tabindex="0">
                        </div>
                    </div>

                    <textarea  id="css_text" name="css_text" rows="35" cols="60"  style='border:1px solid #cccccc; resize: none;background-color:#ffffff; min-height:400px'> <?php  echo $this->css_code; ?></textarea>
                </td>
                <td valign="top" height="100%" id='css_preview_cell'>
                    <div id='css_preview' style='height:100%; width:400px; min-height:300px; margin:15px;' tabindex='0' accesskey="p">
                        <style id="preview_styles"></style>
                        <div id="content">
                            <h2  title="H2"><?php echo _AT('template_heading'); ?> 2</h2>
                            <h3  title="H3"><?php echo _AT('template_heading'); ?> 3</h3>
                            <h4  title="H4"><?php echo _AT('template_heading'); ?> 4</h4>
                            <p title="Paragraph format"><?php echo _AT('template_sample_text'); ?></p>
                            <ul title="UL">
                                <li title="LI"><?php echo _AT('template_list_item'); ?></li>
                                <li><?php echo _AT('template_list_item'); ?></li>
                            </ul>
                            <ol title="OL">
                                <li title="LI"><?php echo _AT('template_list_item'); ?></li>
                                <li><?php echo _AT('template_list_item'); ?></li>
                            </ol>
                            <table title="table" >
                            <tr><th title="TH"><?php echo _AT('template_table_header'); ?></th><th><?php echo _AT('template_table_header'); ?></th></tr>
                            <tr><td title="TD"><?php echo _AT('template_table_data'); ?></td><td><?php echo _AT('template_table_data'); ?></td></tr>
                            </table>
                            <div id="copy">
                                    
                            </div
                        </div>
                    </div>
                    <div id="css_dumy"></div>
                </td>
            </tr>
        </table>
    </div>
</form>
<div class="input-form" style="width: 95%;">
    <h4 style="margin:-2px 0 5px 0;"><?php echo _AT('associated_images'); ?></h4>

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

            echo "<div class='image_item' file='".$image."'><div class='thumbnail' tabindex='0' title='".$display_name."'>";
            echo "<img src='".$this->base_path."templates/layouts/". $this->template."/".$this->template."/".$image."' alt='".$image."'>";
            echo "</div>";
            echo "<img class='delete_image' src='".$this->base_path."images/x.gif' file='".$image."' alt='"._AT('delete_image')."-".$display_name."' title='"._AT('delete_image')."-".$display_name."' tabindex='0'>";
            echo "<div>".$display_name."</div></div>";
        }
        ?>
    </div>

    <form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template; ?>" method="post" enctype="multipart/form-data">
        <label for="file"><?php echo _AT('file'); ?>:</label>
        <input type="file" name="file" id="file" accesskey="l" title="<?php echo _AT('upload'); ?>">
        <input type="submit" name="upload" value="<?php echo _AT('upload'); ?>">
    </form>
</div>
<div class="input-form" style="width: 95%;">
    <h4 style="margin:2px 0 7px 0;"><?php echo _AT('screenshot'); ?></h4>
    <div tabindex="0" title="<?php echo _AT('screenshot').'  '. _AT('enabled'); ?>">
        <?php
        $img_path=$this->base_path."templates/layouts/". $this->template."/screenshot-".$this->template.".png";
        if(isset($this->screenshot))  echo "<img src='".$img_path."' alt='"._AT('screenshot')."'>";
        ?>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF'].'?temp='.$this->template; ?>" method="post" enctype="multipart/form-data">
        <label for="file"><?php echo _AT('file'); ?>:</label>
        <input type="file" name="file" id="file" accesskey="n" title="<?php echo _AT('upload'); ?>">
        <input type="submit" name="uploadscrn" value="<?php echo _AT('upload'); ?>">
    </form>
</div>
