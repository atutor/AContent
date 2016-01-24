<div id="error">
    <h4><?php echo _AT('the_follow_errors_occurred'); ?></h4>
    <ul>
        <li><?php echo _AT('empty_fields_error'); ?></li>
    </ul>
</div>

<?php
$type=$this->metadata['template_type'];
if($this->metadata['template_type']=='page_templates') $type='page_templates';
?>
<div id="subnavlistcontainer">
    <div id="sub-navigation">
    <span style="width:3em; float:left;margin-left:2em;margin-right:-2em;">
    <a href="template_editor/index.php?tab=<?php echo $type; ?>"><img src="themes/default/images/previous.png" alt="back"></a>
    </span>
        <ul id="subnavlist">
            <?php 
                // hack to remove the s from layouts etc. for
                // referencing by respective edit_layout.php etc.

                if($type == "page_templates"){
                    $app_type = "page";
                }else if($type == "layouts")  { 
                    $app_type = "layout"; 
                }else if($type == "structures")  { 
                    $app_type = "structure"; 
                }
                echo '<li><a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_'. $app_type.'.php?type='.$type.SEP.'temp='. $this->template_dir.'"><strong>'. _AT('edit_template') . '</strong></a></li>';
                echo '<li class="active"><strong>'. _AT('edit_metadata') . '</strong></li>';
                echo '<li><a style="font-weight:bold; text-decoration:none;" href="template_editor/delete.php?type='.$type.SEP.'temp='.urlencode($this->template_dir).'">'. _AT('delete') . '</a></li>';
            ?>
        </ul>
    </div>
</div>
<!-- <div align="center">
    <table class="etabbed-table" border="0" cellpadding="0" cellspacing="0" width="95%">
        <tbody><tr>
                <td class="editor_tab">
                    <a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_<?php echo $type.'.php?temp='. $this->template_dir;?>">
                        <?php echo _AT('edit_template'); ?>
                    </a>
                </td>
                <td class="tab-spacer">&nbsp;</td>
                <td class="editor_tab_selected" ><?php echo _AT('edit_metadata'); ?></td>
                <td class="tab-spacer">&nbsp;</td>
                <td class="editor_tab" >
                    <a style="font-weight:bold; text-decoration:none;" href="template_editor/delete.php?type=<?php echo $type.'&temp='. $this->template_dir;?>">
                        <?php echo _AT('delete'); ?>
                    </a>
                </td>
                <td>&nbsp;</td>
            </tr>
        </tbody></table>
</div>
-->
<div class="input-form">
    <fieldset class="group_form"><legend></legend>
        <form method="post" name="form" action="<?php echo $_SERVER['PHP_SELF']?>?type=<?php echo $this->template_type; ?>&temp=<?php echo $this->template_dir; ?>" method="post">
            <input id="template_name" name="template_name" type="hidden"  value="<?php echo $this->metadata['template_name']; ?>" />

            <dl class="form_layout">
                <dt><label for="template_desc"><?php echo _AT('template_description'); ?></label></dt>
                <dd><textarea name="template_desc" id="template_desc" maxlength="100"  rows="4" ><?php echo $this->metadata['template_desc']; ?></textarea></dd>

                <dt><span class="required" title="Required Field">*</span><label for="maintainer_name"><?php echo _AT('maintainer_name'); ?></label></dt>
                <dd><input name="maintainer_name" id="maintainer_name" size="30" type="text" value="<?php echo $this->metadata['maintainer_name']; ?>"/></dd>

                <dt><label for="maintainer_email"><?php echo _AT('maintainer_email'); ?></label></dt>
                <dd><input name="maintainer_email" id="maintainer_email" size="30" type="text" value="<?php echo $this->metadata['maintainer_email']; ?>"/></dd>

                <dt><label for="template_url"><?php echo _AT('template_url'); ?></label></dt>
                <dd><input name="template_url" size="30" type="text" value="<?php echo $this->metadata['template_url']; ?>"/></dd>

                <dt><label for="template_license"><?php echo _AT('template_license'); ?></label></dt>
                <dd><input name="template_license" size="30" type="text" value="<?php echo $this->metadata['template_license']; ?>"/></dd>

                <dt><span class="required" title="Required Field">*</span><label for="release_version"><?php echo _AT('release_version'); ?></label></dt>
                <dd><input name="release_version" id="release_version" size="10" type="text" value="<?php echo $this->metadata['release_version']; ?>"/></dd>

                <dt><label for="release_date"><?php echo _AT('release_date'); ?></label></dt>
                <dd><input name="release_date" size="10" type="text" value="<?php echo $this->metadata['release_date']; ?>"/></dd>

                <dt><span class="required" title="Required Field">*</span><label for="release_state"><?php echo _AT('release_state'); ?></label></dt>
                <dd><input name="release_state" id="release_state" size="10" type="text" value="<?php echo $this->metadata['release_state']; ?>"/></dd>

                <dt><label for="release_notes"><?php echo _AT('release_note'); ?></label></dt>
                <dd><input id="release_notes" name="release_notes" size="30" type="text" value="<?php echo $this->metadata['release_notes']; ?>"/></dd>
            </dl>

            <p class="submit_buttons">
                <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" id="submit_btn" />
                <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"   />
            </p>
        </form>
    </fieldset>
</div>

<script type="text/javascript">
    $('#error').hide();
    $("#submit_btn").click(function() {
        $('#error').hide();
        var error=false;
        if($("input#template_name").val()=="") error=true;
        if($("input#template_type").val()=="") error=true;
        if($("input#maintainer_name").val()=="") error=true;
        if($("input#release_version").val()=="") error=true;
        if($("input#release_state").val()=="") error=true;

        if(error) $('#error').show();
        return !error;
    });
</script>

