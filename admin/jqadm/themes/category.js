/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


/**
 * Attention:
 *
 * Updating tree.jquery.js requires removing or overwriting these lines from
 * NodeElement.prototype.select() and NodeElement.prototype.deselect():
 *
 * var $span = this.getSpan();
 * $span.attr("tabindex", 0);
 * $span.focus();
 */



/**
 * Load categories and create category tree
 */
 Aimeos.options.done(function(result) {

	if(!result || !result.meta || !result.meta.resources || !result.meta.resources.category || $(".aimeos .item-category").length === 0) {
		return;
	}

	if(result.meta.prefix) {
		Aimeos.Category.prefix = result.meta.prefix;
	}

	var params = {};
	var rootId = $(".aimeos .item-category").data("rootid");

	if(rootId) {
		if(result.meta.prefix) {
			params[result.meta.prefix] = {id: rootId, include: "category"};
		} else {
			params = {id: rootId, include: "category"};
		}
	}

	$.ajax(result.meta.resources.category, {
		"data": params,
		"dataType": "json"
	}).done(function(result) {

		if(!result || !result.data || !result.meta) {
			throw {"msg": "No valid data in response", "result": result};
		}

		if(result.meta.csrf) {
			Aimeos.Category.csrf = result.meta.csrf;
		}

		var root = Aimeos.Category.createTree(Aimeos.Category.transformNodes(result));

		root.bind("tree.click", Aimeos.Category.onClick);
		root.bind("tree.move", Aimeos.Category.onMove);
	});
});



Aimeos.Category = {

	csrf : null,
	element : null,
	prefix : null,
	instance: null,


	init : function() {

		this.askDelete();
		this.confirmDelete();

		this.setupAdd();
		this.setupSearch();

		const node = document.querySelector(".item-category .tree-toolbar");
		if(node) {
			this.instance = new Vue({el: node, mixins: [this.mixins]});
		}
	},


	mixins : {
		data() {
			return {
				dialog: false,
				selected: null,
				unconfirmed: {},
			}
		},

		methods: {
			create() {
				const root = $(".tree-content");
				let node = root.tree("getSelectedNode");

				if(!node) {
					node = root.tree("getNodeByHtmlElement", $(".jqtree-tree > .jqtree-folder", root));
				}

				window.location = $(".aimeos .item-category").data("createurl").replace("_ID_", (node ? node.id : ''));
			},

			confirm: function(val) {
				if(val && this.selected) {
					Aimeos.Category.deleteNode(this.selected.id, this.selected.parentid || null);
				}

				this.selected = null;
				this.dialog = false;
			},

			filter(val) {
				$('.aimeos .category-tree .tree-content .jqtree_common[role="treeitem"]').each(function(idx, node) {
					const regex = new RegExp(val, 'i');
					const jqnode = $(node);

					if(regex.test(jqnode.html())) {
						jqnode.parents("li.jqtree_common").show();
						jqnode.show();
					} else {
						jqnode.hide();
					}
				});
			},

			remove() {
				const panel = $(".tree-content");
				let node = panel.tree("getSelectedNode");

				if(!node) {
					node = panel.tree("getNodeByHtmlElement", $(".jqtree-tree > .jqtree-folder", panel));
				}

				this.selected = {id: node.id, parentid: node.parent.id};
				this.unconfirmed = {1: node.name || '***'};
				this.dialog = true;
			}
		}
	},


	createTree : function(root) {

		const rtl = document.documentElement && document.documentElement.getAttribute('dir') === 'rtl' ? true : false;

		var tree = $(".aimeos .item-category .tree-content").tree({
			"data": [root],
			"dragAndDrop": true,
			"closedIcon": " ",
			"openedIcon": " ",
			"saveState": true,
			"slide": false,
			"rtl": rtl,
			"dataFilter": function(result) {
				var list = [];

				for(var i in result.included) {
					if(result.included[i].type !== 'category') {
						continue;
					}
					list.push({
						id: result.included[i].id,
						name: result.included[i].attributes['category.label'],
						load_on_demand: result.included[i].attributes['category.hasChildren'],
						children: []
					});
				}

				return list;
			},
			"dataUrl": function(node) {

				var params = {};

				if(Aimeos.Category.prefix) {
					params[Aimeos.Category.prefix] = {'include': 'category'};
				} else {
					params = {'include': 'category'};
				}

				var result = {
					'url': $(".aimeos .item-tree").data("jsonurl"),
					'data': params,
					'method': 'GET'
				}

				if(node) {
					var name = $(".aimeos .item-tree").data("idname");
					result['data'][name] = node.id;
				}

				return result;
			},
			"onCanMoveTo": function(node, target, position) {
				if(target === tree.tree('getTree').children[0] && position !== 'inside') {
					return false;
				}
				return true;
			},
			"onCreateLi": function(node, li, isselected) {
				$(".jqtree-toggler", li).attr("tabindex", 1);
				$(".jqtree-title", li).attr("tabindex", 1);
			}
		});

		return tree;
	},


	onClick : function(event) {
		window.location = $(".aimeos .item-category").data("geturl").replace("_ID_", event.node.id);
	},


	onMove : function(event) {
		event.preventDefault();

		Aimeos.options.done(function(result) {

			if(!result || !result.meta || !result.meta.resources || !result.meta.resources.category) {
				throw {"msg": "No valid data in response", "result": result};
			}

			var params = {};
			var url = result.meta.resources.category;

			if(result.meta.prefix) {
				params[result.meta.prefix] = {id: event.move_info.moved_node.id};
			} else {
				params = {id: event.move_info.moved_node.id};
			}

			if(Aimeos.Category.csrf) {
				params[Aimeos.Category.csrf.name] = Aimeos.Category.csrf.value;
			}

			var targetid = event.move_info.target_node.id;
			var entry = {
				attributes: {},
				id: event.move_info.moved_node.id,
				parentid: event.move_info.previous_parent.id,
				targetid: targetid
			};

			if(event.move_info.position === 'inside') {
				var children = event.move_info.target_node.children;
				entry.refid = children && children[0] && children[0].id || null;
			}
			else if(event.move_info.position === 'before') {
				entry.refid = targetid;
			}
			else if(event.move_info.position === 'after') {
				var children = event.move_info.target_node.parent.children;
				entry.targetid = event.move_info.target_node.parent.id;

				for(var i = 0; i < children.length; i++) {
					if(children[i].id === targetid && i+1 < children.length) {
						entry.refid = children[i+1].id;
						break;
					}
				}
			}

			$.ajax(url + (url.indexOf('?') !== -1 ? '&' : '?') + jQuery.param(params), {
				"dataType": "json",
				"method": "PATCH",
				"data": JSON.stringify({"data": entry})
			}).done(function(result) {
				event.move_info.do_move();

				if(result.meta.csrf) {
					Aimeos.Category.csrf = result.meta.csrf;
				}
			});
		});
	},


	transformNodes : function(result) {

		root = {
			id: result.data.id,
			name: result.data.attributes && result.data.attributes['category.label'] || '',
			children: []
		};

		if(result.included && result.included.length > 0) {

			var getChildren = function(list, parentId) {
				var result = [];

				for(var i in list) {
					if(list[i].attributes['category.parentid'] == parentId) {
						result.push({
							id: list[i].id,
							name: list[i].attributes['category.label'],
							load_on_demand: list[i].attributes['category.hasChildren'],
							children: getChildren(list, list[i].id)
						});
					}
				}

				return result;
			};

			root.children = getChildren(result.included, result.data.id);
		}

		return root;
	},


	askDelete : function() {
		var self = this;

		$(".aimeos .item-category").on("click", ".tree-toolbar .act-delete", function(ev) {

			self.element = $(".tree-content", ev.delegateTarget).tree("getSelectedNode");

			var dialog = $("#confirm-delete");
			var list = $(".modal-body ul.items", dialog);
			var item = $('<li>').text(self.element.name);

			list.html("").append(item);
			dialog.modal("show", $(this));
			$(".modal", dialog).addClass('show');

			return false;
		});
	},


	confirmDelete : function() {
		var self = this;

		$("#confirm-delete").on("click", ".btn-danger", function(e) {
			if(self.element) {
				self.deleteNode(self.element, self.element.parent || null);
			}
		});
	},


	deleteNode : function(nodeid, parentid) {

		Aimeos.options.done(function(result) {

			if(!result || !result.meta || !result.meta.resources || !result.meta.resources.category) {
				throw {"msg": "No valid data in response", "result": result};
			}

			var params = {};
			var url = result.meta.resources.category;

			if(result.meta.prefix) {
				params[result.meta.prefix] = {id: nodeid};
			} else {
				params = {id: nodeid};
			}

			if(Aimeos.Category.csrf) {
				params[Aimeos.Category.csrf.name] = Aimeos.Category.csrf.value;
			}

			$.ajax(url + (url.indexOf('?') !== -1 ? '&' : '?') + jQuery.param(params), {
				"dataType": "json",
				"method": "DELETE"
			}).done(function(result) {

				if(result.meta.csrf) {
					Aimeos.Category.csrf = result.meta.csrf;
				}

				if(!result.errors) {
					window.location = $(".aimeos .item-category").data("createurl").replace("_ID_", parentid || '');
				}
			});
		});
	},


	setupAdd : function() {

		$(".aimeos .item-category").on("click", ".tree-toolbar .act-add", function(ev) {

			var root = $(".tree-content", ev.delegateTarget);
			var node = root.tree("getSelectedNode");

			if(!node) {
				node = root.tree("getNodeByHtmlElement", $(".jqtree-tree > .jqtree-folder", root));
			}

			window.location = $(ev.delegateTarget).data("createurl").replace("_ID_", (node ? node.id : ''));
		});
	},


	setupSearch : function() {

		$(".aimeos .category-tree .tree-toolbar").on("input", ".search-input", function() {
			var name = $(this).val();

			$('.aimeos .category-tree .tree-content .jqtree_common[role="treeitem"]').each(function(idx, node) {
				var regex = new RegExp(name, 'i');
				var node = $(node);

				if(regex.test(node.html())) {
					node.parents("li.jqtree_common").show();
					node.show();
				} else {
					node.hide();
				}
			});
		});
	}
};



$(function() {

	Aimeos.Category.init();
});
