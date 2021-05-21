$(function() {
	Aimeos.CmsRef.init();
});

Aimeos.CmsRef = {

	init: function() {

		const self = this;
		const node = document.querySelector('.item-cms .cmsref-list');

		if(node) {
			Aimeos.components['cmsref'] = new Vue({
				'el': node,
				'mixins': [Aimeos.CmsRef.mixins]
			});
		}

		Aimeos.lazy('.item-cms .cmsref-list', function() {
			Aimeos.components['cmsref'] && Aimeos.components['cmsref'].reset();
		});
	},


	mixins: {
		'data': function() {
			return {
				'parentid': null,
				'siteid': '',
				'resource': '',
				'items': [],
				'fields': [],
				'filter': {},
				'offset': 0,
				'limit': 25,
				'total': 0,
				'order': '',
				'types': {},
				'options': [],
				'checked': false,
				'loading': true
			}
		},


		beforeMount: function() {
			try {
				if(!this.$el.dataset) {
					throw 'Missing "data" attributes';
				}
				if(!this.$el.dataset.types) {
					throw 'Missing "data-types" attribute';
				}
				if(!this.$el.dataset.siteid) {
					throw 'Missing "data-siteid" attribute';
				}
				if(!this.$el.dataset.parentid) {
					throw 'Missing "data-parentid" attribute';
				}
				if(!this.$el.dataset.resource) {
					throw 'Missing "data-resource" attribute';
				}

				this.siteid = this.$el.dataset.siteid;
				this.parentid = this.$el.dataset.parentid;
				this.resource = this.$el.dataset.resource;
				this.types = JSON.parse(this.$el.dataset.types);
				this.order = this.prefix + 'position';

				const fieldkey = 'aimeos/jqadm/' + this.resource.replace('/', '') + '/fields';
				this.fields = this.columns(this.$el.dataset.fields || [], fieldkey);
			} catch(e) {
				console.log( '[Aimeos] Init referenced cms list failed: ' + e);
			}
		},


		computed: {
			prefix : function() {
				return this.resource.replace('/', '.') + '.';
			}
		},


		methods: {
			add: function() {
				const obj = {};

				obj[this.prefix + 'id'] = null;
				obj[this.prefix + 'siteid'] = this.siteid;
				obj[this.prefix + 'position'] = 0;
				obj[this.prefix + 'status'] = 1;
				obj[this.prefix + 'type'] = 'default';
				obj[this.prefix + 'config'] = {};
				obj[this.prefix + 'datestart'] = null;
				obj[this.prefix + 'dateend'] = null;
				obj[this.prefix + 'refid'] = null;
				obj['edit'] = true;

				this.items.unshift(obj);
			},


			columns: function(json, key) {
				let list = [];
				try {
					if(window.sessionStorage) {
						list = JSON.parse(window.sessionStorage.getItem(key)) || [];
					}
					if(!list.length) {
						list = JSON.parse(json);
					}
				} catch(e) {
					console.log('[Aimeos] Failed to get list of columns: ' + e);
				}
				return list;
			},


			css: function(key) {
				return this.resource.replace('/', '-') + '-' + key;
			},


			delete: function(resource, id, callback) {

				const self = this;
				self.waiting(true);

				Aimeos.options.done(function(response) {

					if(response.meta && response.meta.resources && response.meta.resources[resource] ) {

						const config = {};

						if(response.meta.prefix && response.meta.prefix) {
							config['params'][response.meta.prefix] = {'id': id};
						} else {
							config['params'] = {'id': id};
						}

						axios.delete(response.meta.resources[resource], config).then(function(response) {
							callback(response.data);
						}).then(function() {
							self.waiting(false);
						});
					}
				});
			},


			edit: function(idx) {
				if(this.siteid === this.items[idx][this.prefix + 'siteid']) {
					this.$set(this.items[idx], 'edit', true);
				}
			},


			find: function(ev, key, op) {
				const value = ev.target ? ev.target.value : ev;
				if(value) {
					const expr = {};
					expr[op || '=='] = {};
					expr[op || '=='][this.prefix + key] = value;
					this.$set(this.filter, this.prefix + key, expr);
				} else {
					this.$delete(this.filter, this.prefix + key);
				}
				this.fetch();
			},


			fetch: function() {
				const self = this;
				const args = {
					'filter': {'&&': []},
					'fields': {},
					'page': {'offset': self.offset, 'limit': self.limit},
					'sort': self.order
				};

				for(let key in self.filter) {
					args['filter']['&&'].push(self.filter[key]);
				}

				if(this.fields.includes(this.prefix + 'refid')) {
					args.fields['cms'] = ['cms.id', 'cms.code', 'cms.label', 'cms.status'];
				}
				args.fields[this.resource] = [self.prefix + 'id', self.prefix + 'siteid', ...self.fields];

				this.get(self.resource, args, function(data) {
					self.total = data.total || 0;
					self.items = data.items || [];
				});
			},


			get: function(resource, args, callback) {

				const self = this;
				self.waiting(true);

				Aimeos.options.done(function(response) {

					if(response.meta && response.meta.resources && response.meta.resources[resource] ) {

						if(args.fields) {
							const include = [];
							for(let key in args.fields) {
								args.fields[key] = args.fields[key].join(',');
								include.push(key);
							}
							args['include'] = include.join(',');
						}

						const config = {
							'paramsSerializer': function(params) {
								return jQuery.param(params); // workaround, Axios and QS fail on [==]
							},
							'params': {}
						};

						if(response.meta.prefix && response.meta.prefix) {
							config['params'][response.meta.prefix] = args;
						} else {
							config['params'] = args;
						}

						axios.get(response.meta.resources[resource], config).then(function(response) {
							const list = [];
							const included = {};

							(response.data.included || []).forEach(function(entry) {
								if(!included[entry.type]) {
									included[entry.type] = {};
								}
								included[entry.type][entry.id] = entry;
							});

							(response.data.data || []).forEach(function(entry) {
								for(let type in (entry.relationships || {})) {
									const relitem = entry.relationships[type]['data'] && entry.relationships[type]['data'][0] || null;
									if(relitem && relitem['id'] && included[type][relitem['id']]) {
										Object.assign(entry['attributes'], included[type][relitem['id']]['attributes'] || {});
									}
								}
								list.push(entry.attributes || {});
							});

							callback({
								total: response.data.meta ? response.data.meta.total || 0 : 0,
								items: list
							});

						}).then(function() {
							self.waiting(false);
						});
					}
				});
			},


			label: function(idx) {
				let str = '';

				if(this.items[idx]) {
					if(this.items[idx][this.prefix + 'refid']) {
						str += this.items[idx][this.prefix + 'refid'];
					}

					if(this.items[idx]['cms.label']) {
						str += ' - ' + this.items[idx]['cms.label'];
					}

					if(this.items[idx]['cms.code']) {
						str += ' (' + this.items[idx]['cms.code'] + ')';
					}
				}

				return str;
			},


			remove: function(idx) {
				const self = this;
				this.checked = false;

				if(idx !== undefined) {
					this.delete(this.resource, this.items[idx][this.prefix + 'id'], () => self.waiting(false));
					return this.items.splice(idx, 1);
				}

				this.items = this.items.filter(function(item) {
					if(item.checked) {
						self.delete(self.resource, item[self.prefix + 'id']);
					}
					return !item.checked;
				});

				this.waiting(false);
			},


			reset: function() {
				const domain = {};
				const parentid = {};

				domain[this.prefix + 'domain'] = 'cms';
				parentid[this.prefix + 'parentid'] = this.parentid;

				Object.assign(this.$data, {filter: {'base': {'&&': [{'==': parentid}, {'==': domain}]}}});
			},


			sort: function(key) {
				this.order = this.order === this.prefix + key ? '-' + this.prefix + key : this.prefix + key;
				this.fetch();
			},


			sortclass: function(key) {
				return this.order === this.prefix + key ? 'sort-desc' : (this.order === '-' + this.prefix + key ? 'sort-asc' : '');
			},


			stringify: function(value) {
				return typeof value === 'object' || typeof value === 'array' ? JSON.stringify(value) : value;
			},


			suggest: function(input, loadfcn) {
				const self = this;
				const args = {
					'filter': {'||': [
						{'==': {'cms.id': input}},
						{'=~': {'cms.code': input}},
						{'=~': {'cms.label': input}}
					]},
					'fields': {'cms': ['cms.id', 'cms.code', 'cms.label']},
					'page': {'offset': 0, 'limit': 25},
					'sort': 'cms.label'
				};

				try {
					loadfcn ? loadfcn(true) : null;

					this.get('cms', args, function(data) {
						self.options = [];
						(data.items || []).forEach(function(entry) {
							self.options.push({
								'id': entry['cms.id'],
								'label': entry['cms.id'] + ' - ' + entry['cms.label'] + ' (' + entry['cms.code'] + ')'
							});
						});
					});
				} finally {
					loadfcn ? loadfcn(false) : null;
				}
			},


			toggle: function(key) {
				key = this.prefix + key;
				const idx = this.fields.indexOf(key);
				idx !== -1 ? this.fields.splice(idx, 1) : this.fields.push(key);

				if(window.sessionStorage) {
					window.sessionStorage.setItem(
						'aimeos/jqadm/' + this.resource.replace('/', '') + '/fields',
						JSON.stringify(this.fields)
					);
				}

				this.fetch();
			},


			value: function(key) {
				const op = Object.keys(this.filter[this.prefix + key] || {}).pop();
				return this.filter[this.prefix + key] && this.filter[this.prefix + key][op][this.prefix + key] || '';
			},


			waiting: function(val) {
				this.loading = val;
			}
		},


		watch: {
			checked: function() {
				for(let item of this.items) {
					this.$set(item, 'checked', this.checked);
				}
			},


			filter: {
				handler: function() {
					this.fetch();
				},
				deep: true
			},


			limit: function() {
				this.fetch();
			},


			offset: function() {
				this.fetch();
			}
		}
	}
};
