Aimeos.Post = {
    init : function() {
		this.components();
		this.dataset();
	},

	components : function() {

		Aimeos.components['category/default'] = new Vue({
			'el': '.item-category .category-default .category-list',
			'data': {
				'items': $(".item-category .category-default .category-list").data("items"),
				'keys': $(".item-category .category-default .category-list").data("keys"),
				'listtype': $(".item-category .category-default .category-list").data("listtype"),
				'siteid': $(".item-category .category-default .category-list").data("siteid")
			},
			'mixins': [Aimeos.Post.Category.mixins.bind(this)()]
		});

		Aimeos.components['category/promotion'] = new Vue({
			'el': '.item-category .category-promotion .category-list',
			'data': {
				'items': $(".item-category .category-promotion .category-list").data("items"),
				'keys': $(".item-category .category-promotion .category-list").data("keys"),
				'listtype': $(".item-category .category-promotion .category-list").data("listtype"),
				'siteid': $(".item-category .category-promotion .category-list").data("siteid")
			},
			'mixins': [Aimeos.Post.Category.mixins.bind(this)()]
		});

	},

    dataset : function() {

		$(".item-basic .item-set").on("change", function() {
			var config = $("option:selected", this).data("config");

			for(var name in config) {
				if(Aimeos.components[name]) {
					for(var key in config[name]) {
						if(Aimeos.components[name]) {
							Aimeos.components[name].add(config[name][key]);
						}
					}
				}
			}
		});
	}
};

Aimeos.Post.Category = {

	mixins : function() {
		return {
			methods: {
				checkSite : function(idx) {
					return this.items[idx]['category.lists.siteid'] && this.items[idx]['category.lists.siteid'] != this.siteid;
				},


				add : function(data) {

					var idx = (this.items || []).length;
					this.$set(this.items, idx, {});

					for(var key in this.keys) {
						key = this.keys[key]; this.$set(this.items[idx], key, data && data[key] || '');
					}

					this.$set(this.items[idx], 'category.lists.siteid', this.siteid);
					this.$set(this.items[idx], 'category.lists.type', this.listtype);
				},


				remove : function(idx) {
					this.items.splice(idx, 1);
				},


				getItems : function() {

					return function(request, response, element) {

						var labelFcn = function(attr) {
							return attr['category.label'] + ' (' + attr['category.code'] + ')';
						}

						Aimeos.getOptions(request, response, element, 'category', 'category.label', 'category.label', null, labelFcn);
					}
				},


				getLabel : function(idx) {

					var label = this.items[idx]['category.label'];

					if(this.items[idx]['category.code']) {
						label += ' (' + this.items[idx]['category.code'] + ')';
					}

					return label;
				},


				update : function(ev) {

					this.$set(this.items[ev.index], 'category.lists.id', '');
					this.$set(this.items[ev.index], 'category.lists.type', this.listtype);
					this.$set(this.items[ev.index], 'category.lists.siteid', this.siteid);
					this.$set(this.items[ev.index], 'category.lists.refid', '');
					this.$set(this.items[ev.index], 'category.label', ev.label);
					this.$set(this.items[ev.index], 'category.id', ev.value);
					this.$set(this.items[ev.index], 'category.code', '');

					var ids = [];

					for(idx in this.items) {

						if(this.items[idx]['category.lists.type'] != this.listtype) {
							continue;
						}

						this.items[idx]['css'] = '';

						if(ids.indexOf(this.items[idx]['category.id']) !== -1) {
							this.items[idx]['css'] = 'is-invalid';
						}

						ids.push(this.items[idx]['category.id']);
					}
				}
			}
		};
	}
};

$(function() {
	Aimeos.Post.init();
});
