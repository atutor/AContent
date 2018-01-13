# DROP TABLE IF EXISTS  `language_text`;
REPLACE INTO `config` (`name`, `value`) VALUES ('test_sql_update','1');

# Integrate template editor from supungs GSoC 2013
REPLACE INTO `privileges` (`privilege_id`, `title_var`, `description`, `create_date`, `link`, `menu_sequence`, `open_to_public`) VALUES (11, 'template_editor', 'Template Editor', NOW(), 'template_editor/index.php', 110, 0);

REPLACE INTO `user_group_privilege` (`user_group_id`, `privilege_id`, `user_requirement`) VALUES (1, 11, 0);

# remove not null for content table fields
ALTER TABLE `content` MODIFY `keywords` text NULL DEFAULT NULL;
ALTER TABLE `content` MODIFY `content_path` text NULL DEFAULT NULL;
ALTER TABLE `content` MODIFY `text` text NULL DEFAULT NULL;
ALTER TABLE `content` MODIFY `head` text NULL DEFAULT NULL;
ALTER TABLE `content` MODIFY `test_message` text NULL DEFAULT NULL;
ALTER TABLE `patches` MODIFY `status` text NULL DEFAULT NULL;
ALTER TABLE `patches` MODIFY `author` text NULL DEFAULT NULL;


/*
## Language for template editor
REPLACE INTO `AC_language_text` VALUES ('en', '_template', 'template_editor', 'Template Editor', '2013-07-13 08:46:35', ''),
('en', '_template', 'create_template', 'Create Template', '2013-07-13 08:46:35', ''),
('en', '_template', 'pages', 'Pages', '2013-07-13 08:46:35', ''),
('en', '_template', 'edit_structure', 'Edit Structure', '2013-07-13 08:46:35', ''),
('en', '_template', 'edit_layout', 'Edit Theme', '2013-07-13 08:46:35', ''),
('en', '_template', 'edit_page', 'Edit Page', '2013-07-13 08:46:35', ''),
('en', '_template', 'edit_template', 'Edit Template', '2013-07-13 08:49:45', ''),
('en', '_template', 'edit_metadata', 'Edit Metadata', '2013-07-13 08:49:45', ''),
('en', '_template', 'min', 'Min', '2013-07-13 08:49:45', ''),
('en', '_template', 'max', 'Max', '2013-07-13 08:52:00', ''),
('en', '_template', 'undo', 'Undo', '2013-07-13 08:52:00', ''),
('en', '_template', 'redo', 'Redo', '2013-07-13 08:52:00', ''),
('en', '_template', 'add_page_templates', 'Add Page Templates', '2013-07-13 08:53:00', ''),
('en', '_template', 'add_page_template', 'Add Page Template', '2013-07-13 08:53:00', ''),
('en', '_template', 'add_tests', 'Add Tests', '2013-07-13 08:55:00', ''),
('en', '_template', 'add_test', 'Add Test$AB(', '2013-07-13 08:55:00', ''),
('en', '_template', 'add_forum', 'Add Forum', '2013-07-13 08:55:00', ''),
('en', '_template', 'move_up', 'Move Up', '2013-07-13 08:57:00', ''),
('en', '_template', 'move_down', 'Move Down', '2013-07-13 08:57:00', ''),
('en', '_template', 'page_templates_tag', 'Page Templates', '2013-07-13 08:59:00', ''),
('en', '_template', 'page_template_tag', 'Page Template', '2013-07-13 08:59:00', ''),
('en', '_template', 'test', 'Test', '2013-07-13 08:59:00', ''),
('en', '_template', 'forum', 'Forum', '2013-07-13 08:59:00', ''),
('en', '_template', 'invalid_xml', 'There is a syntax error in the xml code.', '2013-07-13 09:01:00', ''),
('en', '_template', 'template_name', 'Template Name', '2013-07-13 09:06:00', ''),
('en', '_template', 'template_type', 'Template Type', '2013-07-13 09:06:00', ''),
('en', '_template', 'template_description', 'Template Description', '2013-07-13 09:06:00', ''),
('en', '_template', 'template_license', 'Template License', '2013-07-13 09:06:00', ''),
('en', '_template', 'template_url', 'Template URL', '2013-07-13 09:08:00', ''),
('en', '_template', 'maintainer_name', 'Maintainer Name', '2013-07-13 09:08:00', ''),
('en', '_template', 'maintainer_email', 'Maintainer Email', '2013-07-13 09:08:00', ''),
('en', '_template', 'release_version', 'Release Version', '2013-07-13 09:09:00', ''),
('en', '_template', 'release_date', 'Release Date', '2013-07-13 09:09:00', ''),
('en', '_template', 'release_state', 'Release State', '2013-07-13 09:09:00', ''),
('en', '_template', 'release_note', 'Release Note', '2013-07-13 09:09:00', ''),
('en', '_template', 'empty_fields_error', 'One or more required fields are empty.', '2013-07-13 09:09:00', ''),
('en', '_template', 'confirm_template_delete', 'Are you sure you want to delete the template <strong>%s</strong>?', '2013-07-13 09:15:00', ''),
('en', '_template', 'delete_template', 'Delete Template', '2013-07-13 08:49:45', ''),
('en', '_template', 'confirm_template_delete', 'Are you sure you want to delete the template <strong>%s</strong>?', '2013-07-13 09:15:00', ''),
('en', '_template', 'bold', 'Bold', '2013-08-10 09:40:00', ''),
('en', '_template', 'italic', 'Italic', '2013-08-10 09:40:00', ''),
('en', '_template', 'underline', 'Underline', '2013-08-10 09:40:00', ''),
('en', '_template', 'align_left', 'Align Left', '2013-08-10 09:40:00', ''),
('en', '_template', 'align_right', 'Align Right', '2013-08-10 09:40:00', ''),
('en', '_template', 'align_center', 'Align Center', '2013-08-10 09:40:00', ''),
('en', '_template', 'justify', 'Justify', '2013-08-10 09:40:00', ''),
('en', '_template', 'font_family', 'Font Family', '2013-08-10 09:40:00', ''),
('en', '_template', 'font_size', 'Font Size', '2013-08-10 09:40:00', ''),
('en', '_template', 'font_color', 'Font Color', '2013-08-10 09:40:00', ''),
('en', '_template', 'background-color', 'Background Color', '2013-08-10 09:40:00', ''),
('en', '_template', 'background-image', 'Background Image', '2013-08-10 09:40:00', ''),
('en', '_template', 'border-width', 'Border Width', '2013-08-10 09:40:00', ''),
('en', '_template', 'border-style', 'Border Style', '2013-08-10 09:40:00', ''),
('en', '_template', 'border-color', 'Border Color', '2013-08-10 09:40:00', ''),
('en', '_template', 'confirm_image_delete', 'Are you sure you want to delete <strong>%s</strong>?', '2013-08-10 09:40:00', ''),
('en', '_template', 'selector', 'Selector', '2013-08-19 09:40:00', ''),
('en', '_template', 'edit_mode ', 'Edit Mode', '2013-08-19 09:40:00', ''),
('en', '_template', 'basic', 'Basic', '2013-08-19 09:40:00', ''),
('en', '_template', 'advanced', 'Advanced', '2013-08-19 09:40:00', ''),
('en', '_template', 'property', 'Property', '2013-08-19 09:40:00', ''),
('en', '_template', 'value', 'Value', '2013-08-19 09:40:00', ''),
('en', '_template', 'associated_images ', 'Associated Images ', '2013-08-19 09:40:00', ''),
('en', '_template', 'screenshot', 'Screenshot', '2013-08-19 09:40:00', ''),
('en', '_template', 'delete_image', 'Delete Image', '2013-08-19 09:40:00', ''),
('en', '_template', 'ordered_list', 'Ordered List', '2013-09-08 09:40:00', ''),
('en', '_template', 'image', 'Image', '2013-09-08 09:40:00', ''),
('en', '_template', 'table', 'Table', '2013-09-08 09:40:00', ''),
('en', '_template', 'box', 'Box', '2013-09-08 09:40:00', ''),
('en', '_template', 'format', 'Format', '2013-09-08 09:40:00', ''),
('en', '_template', 'rows', 'Rows', '2013-09-08 09:40:00', ''),
('en', '_template', 'columns', 'Columns', '2013-09-08 09:40:00', ''),
('en', '_template', 'auto_generate', 'Auto Generate', '2013-09-08 09:40:00', ''),
('en', '_template', 'number_of_rows', 'Rows', '2013-09-08 09:40:00', ''),
('en', '_template', 'number_of_columns', 'Columns', '2013-09-08 09:40:00', ''),
('en', '_template', 'link', 'Link', '2013-09-08 09:40:00', ''),
('en', '_template', 'paragraph', 'Paragraph', '2013-09-08 09:40:00', ''),
('en', '_msgs', 'TR_HELP_TEMPLATE_EDITOR', '
<h2>Template Editor</h2>
<p>The Template Editor allows administrators to create, edit and manage AContent templates. Templates are managed through the four tabs described below:</p>
<ul>
<li><strong>Create Template</strong>: All new templates are started here, then advance to one of the template editors described below. </li>
<li><strong>Themes</strong>: review and customize existing theme templates and styles.</li>
<li><strong>Structures</strong>: review and modify existing content structures.</li>
<li><strong>Pages</strong>: review and tailor layouts for presenting page content.</li>
</ul>
<h3>Creating a New Template</h3>
<p>To create a new template, select the Create Template tab, then enter at least the required information to setup the properties for your new template. The required fields are decribed below:</p>
<ul>
<li><strong>Template Name</strong>:  A descriptive name for the theme, which appears with its thumbnail. Keep it short. </li>
<li><strong>Template Type</strong>:   Select  either Theme, Page, or Structure</li>
<li><strong>Maintainer Name</strong>:  Who is maintaining the theme?</li>
<li><strong>Release Version</strong>:   Set this value to a number greater than or equal to 1.0 to make the template available to use while authoring content. Set to 0.9 or less to keep the template hidden while it is being developed.</li>
<li><strong>Release State</strong>: Words like Dev, Beta, Stable, Mature to describe the development state.</li>
</ul>
<p>Click the Create button to save the properties for the new template and move on to the respective template editor to create the rest of the template. See the handbook page while using each editor for more about creating and maintaining templates.</p>
', '2013-09-18 09:40:00', ''),
('en', '_msgs', 'TR_HELP_STRUCTURE_EDITOR', '
<h2>Structure Template Editor</h2>
<p>Use the Structure Template Editor to create and modify standard content units, made up of things like a syllabus, outline, references, content, and a quiz, for instance. </p>
<p>The Structure Template Editor has a toolbar across the top, a text window to the left where the template XML file is generated, and the interactive treeview to the right. Review the default structures that come with AContent, to understand how content units are assembled. And, see the toolbar buttons described below.</p> 

<h3>Toolbar</h3>
<p>As you click and select nodes in the treeview,  the toolbar buttons will change according to the selected node.</p>
<ul>
<li><img src=\"../images/add_sub_folder.gif\" alt=\"\" /> Inserts a sub folder under the currently selected node. Only available for structure or folder nodes.</li>
<li><img src=\"../images/add_sub_page.gif\" alt=\"\" /> Inserts a page under the currently selected node. Only available for structure and folder nodes.</li>
<li><img src=\"../images/tree/tree_page_templates.gif\" alt=\"\" /> Inserts a pagetemplates node under the currently selected page. </li>
<li><img src=\"../images/tree/tree_page_template.gif\" alt=\"\" /> Inserts a page template node under the currently selected pagetemplates node.</li>
<li><img src=\"../images/tree/tree_tests.gif\" alt=\"\" /> Inserts a tests node under the currently selected page.</li>
<li><img src=\"../images/tree/tree_test.gif\" alt=\"\" /> Inserts a test node under the currently tests node.</li>
<li><img src=\"../images/tree/tree_forum.gif\" alt=\"\" /> Inserts a forum under the currently selected page.</li>
<li>Use the Name field to set the name of the currently selected node. Names can be set for structure, folder, page, test, page template, and forum nodes.</li>
<li>Use the Min and Max fields to set the minimum and maximum numbers for the currently selected node. Only available for structure, folder, page, test, page template and forum nodes.</li>
<li><img src=\"../images/x.gif\" alt=\"\" /> Delete the currently selected node. Deleting the top structure element will delete the whole template.</li>
<li><img src=\"../images/move_up.png\" alt=\"\" /><img src=\"../images/move_down.png\" alt=\"\" /> Move the currently selected node up or down .</li>
</ul>
<p>You can easily reorder treeview nodes by dragging and dropping them within a suitable parent node. For example, you can only move a Test node under a Page node. If you know what you are doing, you can also modify the XML code in the text window and the treeview will auto update to reflect your changes.</p>
', '2013-09-18 09:40:00', ''),
('en', '_msgs', 'TR_HELP_LAYOUT_EDITOR', "
<h2>Theme Template Editor</h2>
<p>The Theme Template Editor allows you create and customize templates that control the look and feel of a page, a lesson, or a course. On the left is the editor, either Basic, editing through the context sensitive form, or Advanced, editing the CSS directly.</p>
<p>To the right is the preview panel to display a live update of the template. In basic mode you can select the elements in the preview panel on the right, to have the CSS properties of the selected element displayed in the editor panel on the left. You can set values for most basic CSS properties such as text alignment, font, background and border. More complex CSS may not be supported.</p>

<p>If there is a style you can not create with the editor, you may edit the CSS directly to add your own custom styles.</p>
<p>The <strong>Associated Images</strong> panels below the editor is where you upload and manage the images associated with the theme. See how associated images are referenced by viewing the CSS of other theme templates.</p>
<p>The <strong>Screenshot</strong> area allows you to either automatically generate a thumbnail, or upload one to represent the theme.</p>
", '2013-09-18 09:40:00', ''),
('en', '_msgs', 'TR_HELP_PAGE_TEMPLATE_EDITOR', '
<h2>Page Template Editor</h2>
<p>The Page Template Editor allows you to create and customize units that layout the content of a page. Page templates can be as simple or complex as you like. One page template can be used as a full page of content, or several page templates can be strung together to create a page.</p>

<p>There are two modes: Basic and Advanced. </p>
<p>In <strong>Basic mode</strong> you have a WYSIWYG editor you can use to insert some basic page elements such as paragraphs, lists, boxes, tables, and images using the buttons in the toolbar. You can also modify text alignment, change text format, and font etc. </p>
<p>In <strong>Advanced mode</strong> you can edit the HTML code directly. View the HTML of other page templates for examples of the element included in a page template. </p>
<p>At the bottom is the Screenshot panel where you can quickly generate a screenshot automatically, or upload one you have custom created. </p>
', '2013-09-18 09:40:00', '');
*/