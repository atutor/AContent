<div align="center">
    <table class="etabbed-table" border="0" cellpadding="0" cellspacing="0" width="95%">
        <tbody><tr>
                <td class="editor_tab">
                    <a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_<?php echo $this->template_type.'.php?temp='. $this->template_dir;?>">
                        <?php echo _AT('edit_template'); ?>
                    </a>
                </td>
                <td class="tab-spacer">&nbsp;</td>
                <td class="editor_tab" >
                    <a style="font-weight:bold; text-decoration:none;" href="template_editor/edit_meta.php?type=<?php echo $this->template_type.'&temp='.$this->template_dir; ?>">
                        <?php echo _AT('edit_metadata'); ?>
                    </a></td>
                <td class="tab-spacer">&nbsp;</td>
                <td class="editor_tab_selected" ><?php echo _AT('delete'); ?></td>
                <td>&nbsp;</td>
            </tr>
        </tbody></table>
</div>

<div class="input-form">
    <div class="row">
        <?php echo _AT('confirm_template_delete', $this->template_name); ?>
    </div>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?type=<?php echo $this->template_type; ?>&temp=<?php echo $this->template_dir; ?>">
        <div class="row buttons">
            <input type="submit" name="submit" value="<?php echo _AT('yes'); ?>">
            <input type="submit" name="cancel" value="<?php echo _AT('no'); ?>">
        </div>
    </form>
</div>