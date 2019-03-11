// jsTree callback functions
$('#cms_page_controller_jstree').on('loaded.jstree', function (event, data) {
	$('#cms_page_controller_jstree').jstree('open_all');
});

$('#cms_page_controller_jstree').on('select_node.jstree', function (event, data) {
	// switch page on page select
	$('#cms_page_jstree_selected_page').val($('#cms_page_controller_jstree').jstree('get_selected'));
	$('#cms_page_selected_element').val('');
	$('#cms_page_builder_form').submit();
});

$('#cms_page_controller_jstree').on('create_node.jstree', function (event, data) {
	var node = data.node;
	var parent = data.parent;
	var position = data.position;
	
	console.log("#!!! A new node has been created:");
	console.log("Page Id: " + node.id);
	console.log("Page Parent: " + node.parent);
	console.log("Page Name: " + node.text);
	console.log("-");
	console.log("New node was added to parent: " + parent);
	console.log("New node was added at position: " + position);
	console.log("---");
});

$('#cms_page_controller_jstree').on('rename_node.jstree', function (event, data) {
	var node = data.node;
	var text = data.text;
	var old = data.old;
	
	console.log("#!!! A node has been renamed:");
	console.log("Page Id: " + node.id);
	console.log("Page Parent: " + node.parent);
	console.log("Page Name: " + node.text);
	console.log("-");
	console.log("Node was renamed to: " + text);
	console.log("Node was renamed from: " + old);
	console.log("---");
});

$('#cms_page_controller_jstree').on('delete_node.jstree', function (event, data) {
	var node = data.node;
	var parent = data.parent;
	
	console.log("#!!! A node has been deleted:");
	console.log("Page Id: " + node.id);
	console.log("Page Parent: " + node.parent);
	console.log("Page Name: " + node.text);
	console.log("-");
	console.log("Node was deleted from parent: " + parent);
	console.log("---");
});



// mark and select selectable elements in page builder
$('[data-pb-id]').each(
	function() {
		var selectedElement = '^(' + $(this).attr('data-pb-se') + '\-)';
		var regularExpression = new RegExp(selectedElement);
		
		if (
			$(this).attr('data-pb-id') != $(this).attr('data-pb-se')
			&& $(this).attr('data-pb-id').startsWith($(this).attr('data-pb-se'))
			&& $(this).attr('data-pb-id').replace(regularExpression, '').indexOf('-') === -1
		) {
			// mark selectable elements in page builder on mouse hover
			$(this).hover(
				function() {
					$(this).addClass("cms-page-builder-selected-element");
				},
				function() {
					$(this).removeClass("cms-page-builder-selected-element");
				}		
			);
					
			// select element in page builder on mouse click
			$(this).click(
				function() {
					$('#cms_page_selected_element').val($(this).attr('data-pb-id'));
					$('#cms_page_builder_form').submit();
				}
			);
		}
	}
);

//on click create new root page
$('#cms-page-builder-create-new-root-page').click(
	function() {
	    var tree = $('#cms_page_controller_jstree').jstree(true);
	    var node = tree.get_node("#");
    	
	    node = tree.create_node(node, {"type":"folder"});
	    tree.edit(node);
	    
    	console.log("Creating page:");
    	console.log("Page Id: " + node.id);
    	console.log("Page Parent: " + node.parent);
    	console.log("Page Name: " + node.text);
    	console.log("---");
	}
);

//on edit cancel button event
$('#cms-page-builder-cancel').click(
	function() {
		var lastElementIdPosition = $(this).attr('data-pb-se').lastIndexOf('-');
		var newSelectedElementId = '';
		if (lastElementIdPosition > 0) {
			newSelectedElementId = $(this).attr('data-pb-se').substring(0, lastElementIdPosition);
		}
		$('#cms_page_selected_element').val(newSelectedElementId);
		$('#cms_page_builder_form').submit();
	}
);

//on edit submit button event
$('#cms-page-builder-submit').click(
	function() {
		var lastElementIdPosition = $(this).attr('data-pb-se').lastIndexOf('-');
		var newSelectedElementId = '';
		if (lastElementIdPosition > 0) {
			newSelectedElementId = $(this).attr('data-pb-se').substring(0, lastElementIdPosition);
		}
		$('#cms_page_selected_action').val('submit');
		$('#cms_page_builder_form').submit();
	}
);

//adopt cms content builder containers to parents height on window resize event
function resizePageBuilder() {
	var calculatedHeight = $('.main-footer').position().top - $('#page_builder_container_wrapper').position().top;
	
	$('#cms_page_jstree_container').height(calculatedHeight);
	$('#page_builder_container').height(calculatedHeight);
	$('#cms_content_type_list_container').height(calculatedHeight);
}

// bind functions to window resize event
$(window).resize(function() {
    resizePageBuilder();
});

// bind functions to document load event
$(document).ready(function() {
	// create jstree configs
	var jsTreeCoreConfig   = cms_page_controller_jstree_config;
	var jsTreeCustomConfig = {
		"plugins" : ["contextmenu"],
		"contextmenu" : {
		    "select_node" : false,
		    "show_at_node" : true,
		    "items" : {
		        "createItem": {
		            "label": "Create",
		            "action": function (obj) {
		        	    var tree = $('#cms_page_controller_jstree').jstree(true);
		        	    var node = tree.get_node(obj.reference);
		            	
		        	    node = tree.create_node(node, {"type":"folder"});
		        	    tree.edit(node);
		        	    
		            	console.log("Creating page:");
		            	console.log("Page Id: " + node.id);
		            	console.log("Page Parent: " + node.parent);
		            	console.log("Page Name: " + node.text);
		            	console.log("---");
		            }
		        },
		        "renameItem": {
		            "label": "Rename",
		            "action": function (obj) {
		        	    var tree = $('#cms_page_controller_jstree').jstree(true);
		        	    var node = tree.get_node(obj.reference);
		        	    
		        	    tree.edit(node);
		        	    
		            	console.log("Renaming page:");
		            	console.log("Page Id: " + node.id);
		            	console.log("Page Parent: " + node.parent);
		            	console.log("Page Name: " + node.text);
		            	console.log("---");
		            }
		        },
		        "deleteItem": {
		            "label": "Delete",
		            "action": function (obj) {
		        	    var tree = $('#cms_page_controller_jstree').jstree(true);
		        	    var node = tree.get_node(obj.reference);
		        	    
		        	    tree.delete_node(node);
		        	    
		            	console.log("Deleting page:");
		            	console.log("Page Id: " + node.id);
		            	console.log("Page Parent: " + node.parent);
		            	console.log("Page Name: " + node.text);
		            	console.log("---");
		            }
		        }
		    }
		}
	};
	
	// merge jstree configs
	var jsTreeConfig = Object.assign(jsTreeCoreConfig, jsTreeCustomConfig);
	
	// create jstree object
    $('#cms_page_controller_jstree').jstree(jsTreeConfig);
    
    $('#cms_page_builder_language_selector').change(
    	function() {
    		$('#cms_page_selected_language').val($('#cms_page_builder_language_selector option:selected').val());
    		$('#cms_page_builder_form').submit();
    	}
    );
    
    // TODO: move to own function that is triggered after document loaded by RichText-PageBuilderForm.twig
    if ($('#cms_page_builder_form').length && $('#cms_page_richtext_editor').length) {
    	$('#cms_page_builder_form').submit(
	        function() {
	            $('#cms_page_richtext_text').val($('#cms_page_richtext_editor').val());
	        }
		);
		$('#cms_page_richtext_editor').wysihtml5();
    }
    
    resizePageBuilder();
});
