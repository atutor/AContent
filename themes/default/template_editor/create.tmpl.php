<div id="error">
    <h4><?php echo _AT('the_follow_errors_occurred'); ?></h4>
    <ul>
        <li><?php echo _AT('empty_fields_error'); ?></li>
    </ul>
</div>

<div id="subnavlistcontainer">
    <div id="sub-navigation">
        <ul id="subnavlist">
            <li class="active"><b><?php echo _AT('create_template'); ?></b></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>?tab=layout"><?php echo _AT('layout'); ?></a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>?tab=structures"><?php echo _AT('structures'); ?></a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF'] ?>?tab=pages"><?php echo _AT('pages'); ?></a></li>
        </ul>
    </div>
</div>
<div class="input-form">
    <fieldset class="group_form"><legend></legend>
        <form method="post" name="form" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <dl class="form_layout">
                <dt><span class="required" title="Required Field">*</span><label for="template_name"><?php echo _AT('template_name'); ?></label>:</dt>
                <dd><input id="template_name" name="template_name" type="text" size="30" maxlength="50" value="" /></dd>
                
                <dt><span class="required" title="Required Field">*</span><label for="template_type"><?php echo _AT('template_type'); ?></label></dt>
                <dd>
                    <select name="template_type" id="template_type">
                        <option value="layout"><?php echo _AT('layout'); ?></option>
                        <option value="page_template"><?php echo _AT('template_page'); ?></option>
                        <option value="structure"><?php echo _AT('structure'); ?></option>
                    </select>
                </dd>

                <dt><label for="template_desc"><?php echo _AT('template_description'); ?></label></dt>
                <dd><textarea name="template_desc" id="template_desc" maxlength="100"  rows="4" ></textarea></dd>

                <dt><span class="required" title="Required Field">*</span><label for="maintainer_name"><?php echo _AT('maintainer_name'); ?></label></dt>
                <dd><input name="maintainer_name" id="maintainer_name" size="30" type="text"/></dd>

                <dt><label for="maintainer_email"><?php echo _AT('maintainer_email'); ?></label></dt>
                <dd><input name="maintainer_email" id="maintainer_email" size="30" type="text"/></dd>

                <dt><label for="template_url"><?php echo _AT('template_url'); ?></label></dt>
                <dd><input name="template_url" size="30" type="text"/></dd>

                <dt><label for="template_license"><?php echo _AT('template_license'); ?></label></dt>
                <dd><input name="template_license" size="30" type="text"/></dd>

                <dt><span class="required" title="Required Field">*</span><label for="release_version"><?php echo _AT('release_version'); ?></label></dt>
                <dd><input name="release_version" id="release_version" size="10" type="text"/></dd>

                <dt><label for="release_date"><?php echo _AT('release_date'); ?></label></dt>
                <dd><input name="release_date" size="10" type="text"/></dd>

                <dt><span class="required" title="Required Field">*</span><label for="release_state"><?php echo _AT('release_state'); ?></label></dt>
                <dd><input name="release_state" id="release_state" size="10" type="text"/></dd>

                <dt><label for="release_note"><?php echo _AT('release_note'); ?></label></dt>
                <dd><input name="release_note" size="30" type="text"/></dd>
            </dl>

            <p class="submit_buttons">
                <input type="submit" name="submit" value="<?php echo _AT('create'); ?>" class="submit" id="submit_btn" />
                <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  class="submit" />
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

