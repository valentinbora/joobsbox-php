var newNodeType = "primary-categ";
var selectedCategNode;
$(function() {
	var tree = $.tree_create();
	tree.init('#categ-wrapper', {
		ui: {
			theme_name: 'apple',
		},
		rules : {
			draggable : "all", 
			dragrules : ["secondary-categ inside root", "secondary-categ inside primary-categ" , "primary-categ after primary-categ", "primary-categ before primary-categ", "primary-categ inside primary-categ"],
			drag_copy : "ctrl",
			renameable: ["primary-categ", "secondary-categ"],
			createable: "all"
		},
		callback: {
			onselect: function(selected, tree) {
				if($(selected).attr("rel") == "root") {
					newNodeType = "primary-categ";
				} else {
					newNodeType = "secondary-categ";
				}
				selectedCategNode = $(selected).attr("rel");
			},
			beforecreate: function(node, parent) {
			return true;
				var type = $(node).attr("rel");
				if(type == "primary-categ" || type == "root") {
					return true;
				} else {
					alert(translate("You can not create more than level 2 categories."));
					return false;
				}
			},
			oncreate: function(node, ref_node, type, tree) {
				$(node).attr("rel", newNodeType);
			},
			beforemove: function(node, ref_node) {
				var type = $(node).parents("li").get(0);
				type = $(type).attr("rel");
				
				if(type == "primary-categ" || type == "root") {
					if($(node).find("ul").length) {
						return false;
					}
				} else if(type == "secondary-categ") {
					return false;
				}
				return true;
			},
			onmove: function(node, ref_node, location) {
				var type = $(node).parents("li").get(0);
				type = $(type).attr("rel");
				
				if(type == "root") {
					$(node).attr("rel", "primary-categ");
				} else {
					if(type == "primary-categ") {
						if($(node).find("ul").length) {
							return false;
						} else {
							$(node).attr("rel", "secondary-categ");					
						}
					} else {
						return false;
					}
				}
				return true;
			}
		},
		selected  : "node_root"
	});
	$('#createNewNode').click(function(){
		tree.create();
	});
	$('#renameNode').click(function(){
		tree.rename();
	});
	$('#deleteNode').click(function(){
		if(selectedCategNode == "root") {
			alert(translate("You surely don't want to delete the root node, do you?"));
		} else {
			if(confirm(translate("Are you sure you want to delete this category and all its subcategories?"))) {
				tree.remove();
			}
		}
	});
	$('#saveConfiguration').click(function(){
		var data = {"categories": []};
		var categories = [];
		traverse($('#node_root'), categories);
		$(categories).each(function(index, el) {
			traverse($(el), categories);
		});
		
		$(categories).each(function(index, el) {
			var category = {};
			category.name = $.trim($(el).children(':first').text());
			category.id   = $(el).attr('id');
			
			if($(el).attr('rel') == "primary-categ") {
				category.parentId = 0;
			} else {
				var parent = $(el).parents("li[rel='primary-categ']").get(0);
				category.parentId   = $(parent).attr('id');
				category.parentName = $.trim($(parent).children(':first').text());
			}
			data.categories.push(category);
		});
		
		$('#saveDialog').dialog({
			bgiframe: true,
			modal: true,
			hide: 'highlight',
			autoOpen: false,	
			buttons: {
				Ok: function() {
					if($(this).dialog('option', 'mustReload')) {
						window.location.reload();
					}
					$(this).dialog('close');
				}
			}
		});
		
		$.post(pluginUrl + 'saveConfiguration', {'data': $.toJSON(data)},
		  function(data){
		    	$('#saveDialog').dialog('option', 'mustReload', data.mustReload).dialog('open');			
		  }, "json");
	});
});

function traverse(root, list) {
	root.find('li').each(function(index, el) {
		var parent = $(el).parents();
		parent = parent[1];
		if(parent == root[0]) {
			list.push(el);
		}
	});
}
