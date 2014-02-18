(function($){

	$(document).ready(function() {
		var tree = {};
		
		var initIconPicker = function() {
			$('.icon-select').iconPicker({
				onSelect: function(item, iconHtml, iconClass) {
					var id = item.parentsUntil('li').parent().attr('id').substring('5');
					tree.treeBuilder('updateItem', {'icon': iconClass}, id);
				}
			});
		};

		tree = $('#nested-tree').treeBuilder({
			ajaxUrl		: ajaxUrl,
			primaryKey	: 'cat_id',
			loaded		: function() {
				initIconPicker();
			},
			updated		: function() {
				initIconPicker();
			}
		});

		// cloud9 editor for yaml
		var ace_editor = ace.edit("buld_editor");
		var textarea = document.getElementById('add_list');

		textarea.style.display = 'none';
		ace_editor.setTheme("ace/theme/monokai");
		ace_editor.getSession().setMode("ace/mode/yaml");
		ace_editor.getSession().setValue(textarea.value);
		ace_editor.getSession().on('change', function(){
			textarea.value = ace_editor.getSession().getValue();
		});
	});
})(jQuery);