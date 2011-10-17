/**
 * $Id: $
 *
 * @author Laurel A. Williams
 * @copyright Copyright © 2008, ATutor, All rights reserved.
 */


(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('insert_tag');

	tinymce.create('tinymce.plugins.Insert_tagPlugin', {
		/**
		 * Creates control instances based in the incoming name. This method is
		 * normally not needed since the addButton method of the tinymce.Editor
		 * class is a more easy way of adding buttons but you sometimes need to
		 * create more complex controls like listboxes, split buttons etc then
		 * this method can be used to create those.
		 * 
		 * @param {String}
		 *            n Name of the control to create.
		 * @param {tinymce.ControlManager}
		 *            cm Control manager to use to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no
		 *         control was created.
		 */
		createControl: function(n, cm) {
			var editor = tinyMCE.activeEditor;
			switch (n) {
				case 'insert_tag':
					var pluginImgURL = tinymce.baseURL + '/plugins/insert_tag/img/';
					var c = cm.createMenuButton('insert_tag', {
						title : 'insert_tag.desc',
						image : pluginImgURL + 'insert_tag.png',
						cmd : 'mceInsert_tag'
					});
					
					c.onRenderMenu.add(function(c, m) {
						m.add({
							//image: pluginImgURL + 'insert_tag.png',
							title : 'insert_tag.term',
							onclick : function() {
								editor.execCommand('mceInsertContent', false, '[?][/?]');
							}
						});
	
						m.add({
							title : 'insert_tag.code', 
							onclick : function() {
								editor.execCommand('mceInsertContent', false, '[code][/code]');
							}
						});
						
						m.add({
							title : 'insert_tag.media', 
							onclick : function() {
								editor.execCommand('mceInsertContent', false, '[media|640|480]http://[/media]');
							}
						});
					});
		 
					// Return the new menu button instance
					return c;
				}
				return null;
		},	
		
		
		
		/**
		 * Returns information about the plugin as a name/value array. The
		 * current keys are longname, author, authorurl, infourl and version.
		 * 
		 * @return {Object} Name/value array containing information about the
		 *         plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Insert tag plugin',
				author : 'ATutor',
				authorurl : 'http://www.atutor.ca',
				infourl : 'http://www.atutor.ca',
				version : "0.1alpha"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('insert_tag', tinymce.plugins.Insert_tagPlugin);
})();