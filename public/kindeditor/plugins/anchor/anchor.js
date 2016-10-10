/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

KindEditor.plugin('anchor', function(K) {
	var self = this, name = 'anchor', lang = self.lang(name + '.');
	self.plugin.anchor = {
		edit : function() {
			var html = ['<div style="padding:20px;">',
					'<div class="ke-dialog-row">',
					'<label for="keName">' + lang.name + '</label>',
					'<input class="ke-input-text" type="text" id="keName" name="name" value="" style="width:100px;" />',
					'</div>',
					'</div>'].join('');
			var dialog = self.createDialog({
				name : name,
				width : 300,
				title : self.lang(name),
				body : html,
				yesBtn : {
					name : self.lang('yes'),
					click : function(e) {
						self.insertHtml('<a name="' + nameBox.val() + '">').hideDialog().focus();
					}
				}
			});
			var div = dialog.div,
				nameBox = K('input[name="name"]', div);
			var img = self.plugin.getSelectedAnchor();
			if (img) {
				nameBox.val(unescape(img.attr('data-ke-name')));
			}
			nameBox[0].focus();
			nameBox[0].select();
		},
		'delete' : function() {
			self.plugin.getSelectedAnchor().remove();
		}
	};
	self.clickToolbar(name, self.plugin.anchor.edit);
});
