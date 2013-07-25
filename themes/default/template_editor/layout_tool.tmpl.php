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
        <div id='struct_toolbar'>
        </div>

    <div id="status"></div>
    <table border="0" cellpadding="4" style="width:100%">
        <tr>
            <td valign="top" height="100%"> <textarea  id="css_text" name="css_text" rows="35" cols="60"  style='border:1px solid #cccccc; resize: none;background-color:#ffffff; min-height:400px'> <?php  echo $this->css_code; ?></textarea></td>
            <td valign="top" height="100%"><div id='css_preview' style='height:100%; width:400px; min-height:300px; margin:2px;'></div></td>
        </tr>
    </table>
</div>
</form>

