<div id="error">
     <h4>The following errors occurred:</h4>
    <ul>
        <li>Some required field(s) are empty</li>
    </ul>
</div>
<div class="input-form">
    <fieldset class="group_form"><legend></legend>
        <form method="post" name="form" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <dl class="form_layout">
                <dt><span class="required" title="Required Field">*</span><label for="template_name">Template Name</label>:</dt>
                <dd><input id="template_name" name="template_name" type="text" size="30" maxlength="50" value="" /></dd>
                <dt><span class="required" title="Required Field">*</span><label for="template_type">Template Type</label></dt>
                <dd>
                    <select name="template_type" id="template_type">
                        <option value="layout">Layout</option>
                        <option value="page_template">Page Template</option>
                        <option value="structure">Structure</option>
                    </select>
                </dd>
                <dt><label for="template_desc">Template Description</label></dt>
                <dd><textarea name="template_desc" id="template_desc" maxlength="100"  rows="4" ></textarea></dd>
                <dt><span class="required" title="Required Field">*</span><label for="maintainer_name">Maintainer Name</label></dt>
                <dd><input name="maintainer_name" id="maintainer_name" size="30" type="text"/></dd>
                <dt><label for="maintainer_email">Maintainer Email</label></dt>
                <dd><input name="maintainer_email" id="maintainer_email" size="30" type="text"/></dd>
                <dt><label for="template_url">Template URL</label></dt>
                <dd><input name="template_url" size="30" type="text"/></dd>
                <dt><label for="template_license">Template License</label></dt>
                <dd><input name="template_license" size="30" type="text"/></dd>
                <dt><span class="required" title="Required Field">*</span><label for="release_version">Release Version</label></dt>
                <dd><input name="release_version" id="release_version" size="10" type="text"/></dd>
                <dt><label for="release_date">Release Date</label></dt>
                <dd><input name="release_date" size="10" type="text"/></dd>
                <dt><span class="required" title="Required Field">*</span><label for="release_state">Release State</label></dt>
                <dd><input name="release_state" id="release_state" size="10" type="text"/></dd>
                <dt><label for="release_note">Release Note</label></dt>
                <dd><input name="release_note" size="30" type="text"/></dd>
            </dl>

            <p class="submit_buttons">
                <input type="submit" name="submit" value="Create" class="submit" id="submit_btn" />
                <input type="submit" name="cancel" value=" Cancel "  class="submit" />
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

