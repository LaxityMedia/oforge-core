// CMS Elements Controller Module
// will be initialized on load of elements/index.twig
var cmsElementsControllerModule = (function() {
	// status variable to check if foreign object was dragged to jsTree
	var isForeignDND = false;

	// variable that holds the selected parent for content type dragged to jsTree
	var contentTypeParentForeignDND = false;

	// bind functions to window resize event
	$(window).resize(function() {
		cecm.resizeContentEditor();
	});

	// drag 'n drop event listeners for jsTree foreign objects
	$(document).bind("dnd_start.vakata", function(event, data) {
		console.log("jsTree - Start dnd");
		console.log("Data:");
		console.log(jsonStringify(data.data.jstree));
		console.log(jsonStringify(data.data.obj));
		console.log(jsonStringify(data.data.nodes));
		console.log("------------------");
	})
	.bind("dnd_stop.vakata", function(event, data) {
		console.log("jsTree - Stop dnd");
		console.log("Data:");
		console.log(jsonStringify(data.data.jstree));
		console.log(jsonStringify(data.data.obj));
		console.log(jsonStringify(data.data.nodes));
		console.log("this was a foreign operation: " + isForeignDND);
		console.log("------------------");

		var dndData = data.data;

		if (isForeignDND && contentTypeParentForeignDND) {
			if (data && data.data && data.data.nodes &&  data.data.nodes.length > 0) {
				var node = data.data.nodes[0];
		
				if (node && node.data_ct_id) {
					$('#cms_edit_element_id').val(node.data_ct_id);
					$('#cms_edit_element_parent_id').val(contentTypeParentForeignDND);
					$('#cms_edit_element_action').val('dnd');
					$('#cms_element_jstree_form').submit();
				}
			}
		}
	});

	// make foreign objects draggable to jsTree
	$('.jstree_draggable').on('mousedown', function (event) {
		$(this).wrap( "<div id='jstree-drag-element'></div>" );
		var dragHelper = '<div id="jstree-dnd" class="jstree-default"><i class="jstree-icon jstree-er"></i>' + $('#jstree-drag-element').html() + '<ins class="jstree-copy" style="display:none;">+</ins></div>';
		$(this).unwrap();

		return $.vakata.dnd.start(
			event,
			{
				'jstree' : true,
				'obj' 	 : $(this),
				'nodes'  : [{
					'icon'		 : 'jstree-file',
					'text'		 : 'New Content Element',
					'data_ct_id' : $(this).attr('data-ct-id')
				}]
			},
			dragHelper
		);
	});

	// jsTree callback functions
	$('#cms_elements_controller_jstree').on('loaded.jstree', function (event, data) {
		$('#cms_elements_controller_jstree').jstree('open_all');
	});

	$('#cms_elements_controller_jstree').on('select_node.jstree', function (event, data) {
		// switch element on element select
		$('#cms_element_jstree_selected_element').val($('#cms_elements_controller_jstree').jstree('get_selected'));
		$('#cms_element_selected_element').val('');
		$('#cms_element_editor_form').submit();
	});
	
	// called after creating the node in jsTree. afterwards "rename_node.jstree"-callback
	// will be called when user finished editing the node's name
	$('#cms_elements_controller_jstree').on('create_node.jstree', function (event, data) {
		var node = data.node;
		var parent = data.parent;
		var position = data.position;
	
		$('#cms_edit_element_parent_id').val(node.parent);
		$('#cms_edit_element_action').val('create');
	});
	
	// called after user finished editing the node's name
	$('#cms_elements_controller_jstree').on('rename_node.jstree', function (event, data) {
		var node = data.node;
		var text = data.text;
		var old = data.old;
		
		if ($('#cms_edit_element_action').val() != 'create') {
			$('#cms_edit_element_id').val(node.id);
			$('#cms_edit_element_action').val('rename');
		}
	
		$('#cms_edit_element_description').val(node.text);
		$('#cms_element_jstree_form').submit();
	});
	
	// called after deleting a jsTree node
	$('#cms_elements_controller_jstree').on('delete_node.jstree', function (event, data) {
		var node = data.node;
		var parent = data.parent;
	
		$('#cms_edit_element_id').val(node.id);
		$('#cms_edit_element_action').val('delete');
		$('#cms_element_jstree_form').submit();
	});
	
	// on click create new root element
	$('#cms-element-editor-create-new-root-element').click(
		function() {
			var tree = $('#cms_elements_controller_jstree').jstree(true);
			var node = tree.get_node("#");
	
			if (node.id === "#" || node.id.startsWith("_parent")) {
				node = tree.create_node(node, {"type":"folder"});
				tree.edit(node);
			} else {
				alert("New folders can only be created as root folders or as sub-folders in user created folders!");
			}
	
		}
	);

	// drag 'n drop event listener for jsTree's internal node objects
	$('#cms_elements_controller_jstree').on('move_node.jstree', function (event, data) {
		console.log("move_node - new position: " + data.position);
		console.log("move_node - old position: " + data.old_position);
		console.log("node: " + jsonStringify(data.node));
		console.log("parent: " + jsonStringify(data.parent));
		/*
		console.log("position: " + data.position);
		console.log("old_parent: " + data.old_parent);
		console.log("old_position: " + data.old_position);
		console.log("is_multi: " + data.is_multi);
		console.log("old_instance: " + data.old_instance);
		console.log("new_instance: " + data.new_instance);
		*/

		var node = data.node;
		var parent = data.parent;

		if (isForeignDND === false && node && node.id) {
			if (node.id.startsWith("_parent#")) {
				if (parent && (parent === ("#") || parent.startsWith("_parent#"))) {
					$('#cms_edit_element_id').val(node.id);
					$('#cms_edit_element_parent_id').val(parent);
					$('#cms_edit_element_action').val('move');
					$('#cms_element_jstree_form').submit();
				}
			} else if (node.id.startsWith("_element#")) {
				if (parent) {
					$('#cms_edit_element_id').val(node.id);
					$('#cms_edit_element_parent_id').val(parent);
					$('#cms_edit_element_action').val('move');
					$('#cms_element_jstree_form').submit();
				}
			}
		}
	});

	if (typeof cms_elements_controller_jstree_config !== typeof undefined && cms_elements_controller_jstree_config) {
		// create jstree configs
		var jsTreeConfig = {
            "core" : {
                "multiple"       : false,
                "animation"      : 0,
                "check_callback" : function (op, node, par, pos, more) {
					console.log("check_callback - op: " + op + " | node: " + node.id + " | parent: " + par.id);
					console.log("-----------------------");

					// by default assume that this is not a foreign dnd operation
					isForeignDND = false;
					contentTypeParentForeignDND = false;

					// check if this is a context menu operation and if yes permit it
					if (op === "create_node" || op === "rename_node" || op === "delete_node" || op === "edit") {
						return true;
					}

					// check if this is a foreign dnd operation and if yes allow it
					if (op === "move_node" && !node && more.dnd === true && more.is_foreign === true) {
						if (par && par.id && !par.id.startsWith("_element#")) {
							isForeignDND = true;
							contentTypeParentForeignDND = par.id;
							return true;
						} else {
							return false;
						}
					}

					// check if this is a dnd operation of a user created content parent folder
					// if yes only permit it if it is dragged to another user created content parent folder
					if (op === "move_node" && node && node.id && (node.id.startsWith("_parent#"))) {
						if (par && par.id && (par.id === "#" || par.id.startsWith("_parent#"))) {
							return true;
						} else {
							return false;
						}
					// check if this is a dnd operation of an existing content element and if yes allow it:
					// if content elements are dragged to any default content type folder they will always
					// automatically sorted into their default folder by setting their parent to NULL
					} else if (op === "move_node" && node && node.id && (node.id.startsWith("_element#"))) {
						if (par && par.id && !par.id.startsWith("_element#")) {
							return true;
						} else {
							return false;
						}
					// deny all other jsTree operations
					} else {
						return false;
					}
				},
                "force_text"     : true,
                "themes"         : {"stripes" : false},
                "data"           : cms_elements_controller_jstree_config
			},
			"plugins" : ["contextmenu", "dnd", "html_data"],
			"contextmenu" : {
				"select_node" : false,
				"show_at_node" : true,
				"items" : function (node) {
					var items = {
						"createItem": {
							"label": "Create",
							"action": function (obj) {
								var tree = $('#cms_elements_controller_jstree').jstree(true);
								var node = tree.get_node(obj.reference);
		
								if (node.id === "#" || node.id.startsWith("_parent#")) {
									node = tree.create_node(node, {"type":"folder"});
									tree.edit(node);
								} else {
									alert("New folders can only be created as root folders or as sub-folders in user created folders!");
								}
							}
						},
						"renameItem": {
							"label": "Rename",
							"action": function (obj) {
								var tree = $('#cms_elements_controller_jstree').jstree(true);
								var node = tree.get_node(obj.reference);
		
								if (node.id.startsWith("_parent#")) {
									tree.edit(node);
								} else {
									alert("Only user created folders can be renamed!");
								}
							}
						},
						"deleteItem": {
							"label": "Delete",
							"action": function (obj) {
								var tree = $('#cms_elements_controller_jstree').jstree(true);
								var node = tree.get_node(obj.reference);
								
								if (node.id.startsWith("_parent#")) {
									tree.delete_node(node);
								} else {
									alert("Only user created folders can be deleted!");
								}
							}
						}
					}
		
					if (node.id && node.id.startsWith("_parent#")) {
						return items;
					}
		
					return false;
				}
			}
		};

		// create jsTree object
		$('#cms_elements_controller_jstree').jstree(jsTreeConfig);
	}

	console.log("-= CMS Elements Controller Module has been initialized! =-");

	return {
		// adopt cms content editor containers to parents height on window resize event
		resizeContentEditor : function () {
			if (typeof $('#element_editor_container_wrapper') !== typeof undefined && typeof $('#element_editor_container_wrapper').position() !== typeof undefined) {
				var calculatedHeight = window.innerHeight - $('#element_editor_container_wrapper').position().top - $('.main-footer').outerHeight(true) - $('.content').css('padding-top').replace('px', '') - $('.content').css('padding-bottom').replace('px', '');
			
				$('#element_editor_container_wrapper').height(calculatedHeight);
				
				$('#cms_element_jstree_container').height(calculatedHeight);
				$('#cms_element_editor_container').height(calculatedHeight);
				$('#cms_content_type_list_container').height(calculatedHeight);
				
				if (typeof $('#cms_content_type_editor_wrapper') !== typeof undefined && typeof $('#cms_content_type_editor_wrapper').position() !== typeof undefined) {
					$('#cms_content_type_editor_wrapper').height(calculatedHeight - $('#cms_content_type_editor_wrapper').position().top);
				}
			}
		}
	}
});

// check if the Oforge namespace exists
if (typeof Oforge !== 'undefined') {
    // if it exists, it should have the register function, so register your module
    // the properties "name", "selector" and "init" are required
    // name: the name of your module
    // selector: the html selector to search for. If it is found, the module can be initialized
    // init: the function to initialize the module. This function gets called automatically from the module-loader.js
    // when the DOMContentLoaded event is triggered.
    Oforge.register({
        name: 'cmsElementsControllerModule',
        selector: '#element_editor_container_wrapper',
        init: function () {
			window.cecm = cmsElementsControllerModule();
			cecm.resizeContentEditor();
        }
    });
} else {
    console.warn("Oforge is not defined. Module cannot be registered.");
}