<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


/** admin/jqadm/post/item/meta/config/suggest
 * List of suggested configuration keys in post meta panel
 *
 * Item references can store arbitrary key value pairs. This setting gives
 * editors a hint which config keys are available and are used in the templates.
 *
 * @param string List of suggested config keys
 * @since 2020.01
 * @category Developer
 */


$enc = $this->encoder();


?>
<div id="meta" class="item-post-meta tab-pane fade" role="tablist" aria-labelledby="meta">

	<div id="item-text-group"
		data-translate="<?= $enc->attr( $this->config( 'admin/jqadm/api/translate', [] ) ) ?>"
		data-items="<?= $enc->attr( $this->get( 'metaData', [] ) ) ?>"
		data-siteid="<?= $this->site()->siteid() ?>"
		data-domain="post" >

		<div class="group-list" role="tablist" aria-multiselectable="true">
			<div is="draggable" v-model="items" group="text" handle=".act-move">
				<div v-for="(item, idx) in items" v-bind:key="idx" class="group-item card">

					<div v-bind:id="'item-text-group-item-' + idx" v-bind:class="item['_show'] ? 'show' : 'collapsed'"
						v-bind:data-target="'#item-text-group-data-' + idx" data-bs-toggle="collapse" role="tab" class="card-header header"
						v-bind:aria-controls="'item-text-group-data-' + idx" aria-expanded="false" v-on:click="toggle('_show', idx)">
						<div class="card-tools-start">
							<div class="btn btn-card-header act-show fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
								title="<?= $enc->attr( $this->translate( 'admin', 'Show/hide this entry' ) ) ?>">
							</div>
						</div>
						<span class="item-label header-label" v-bind:class="{disabled: !active(idx)}">{{ label(idx) }}</span>
						<div class="card-tools-end">
							<div class="dropdown">
								<a v-bind:id="'translate-menu-' + idx" class="btn btn-card-header act-translate fa dropdown-toggle" href="#"
									tabindex="<?= $this->get( 'tabindex' ) ?>" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
									title="<?= $enc->attr( $this->translate( 'admin', 'Translate text' ) ) ?>">
 								</a>
								<div class="dropdown-menu dropdown-menu-end" v-bind:aria-labelledby="'translate-menu-' + idx">
									<?php foreach( ['de', 'en', 'es', 'fr', 'it', 'nl', 'pl', 'pt', 'ru'] as $lang ) : ?>
										<a class="dropdown-item" href="#" v-on:click="translate(idx, `<?= strtoupper( $lang ) ?>`)"><?= $enc->html( $this->translate( 'language', $lang ) ) ?></a>
									<?php endforeach ?>
								</div>
							</div>
							<div v-if="item['post.lists.siteid'] == siteid && !item['_nosort']"
								class="btn btn-card-header act-move fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
								title="<?= $enc->attr( $this->translate( 'admin', 'Move this entry up/down' ) ) ?>">
							</div>
							<div v-if="item['post.lists.siteid'] == siteid"
								class="btn btn-card-header act-delete fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
								title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>"
								v-on:click.stop="remove(idx)">
							</div>
						</div>
					</div>

					<div v-bind:id="'item-text-group-data-' + idx" v-bind:class="item['_show'] ? 'show' : 'collapsed'"
						v-bind:aria-labelledby="'item-text-group-item-' + idx" role="tabpanel" class="card-block collapse row">

						<input type="hidden" v-model="item['text.id']"
							v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'text.id' ) ) ) ?>`.replace('_idx_', idx)" />

						<div class="col-xl-6">

							<div class="form-group row mandatory">
								<div class="col-sm-12">
									<textarea is="html-editor" class="form-control item-content" required="required"
										v-bind:key="idx"
										v-bind:id="'cke-' + idx"
										v-bind:config="Aimeos.ckeditor"
										v-bind:value="item['text.content']"
										v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'text.content' ) ) ) ?>`.replace('_idx_', idx)"
										v-bind:readonly="item['text.siteid'] != siteid"
										v-bind:tabindex="`<?= $enc->js( $this->get( 'tabindex' ) ) ?>`"
										v-model="item['text.content']"
									></textarea>
								</div>
							</div>

						</div>

						<div class="col-xl-6">

							<div class="form-group row mandatory">
								<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Status' ) ) ?></label>
								<div class="col-sm-8">
									<select class="form-select item-status" required="required" tabindex="<?= $this->get( 'tabindex' ) ?>"
										v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'text.status' ) ) ) ?>`.replace('_idx_', idx)"
										v-bind:readonly="item['text.siteid'] != siteid"
										v-model="item['text.status']" >
										<option value=""><?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?></option>
										<option value="1" v-bind:selected="item['text.status'] == 1" >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:1' ) ) ?>
										</option>
										<option value="0" v-bind:selected="item['text.status'] == 0" >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:0' ) ) ?>
										</option>
										<option value="-1" v-bind:selected="item['text.status'] == -1" >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:-1' ) ) ?>
										</option>
										<option value="-2" v-bind:selected="item['text.status'] == -2" >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:-2' ) ) ?>
										</option>
									</select>
								</div>
							</div>

							<?php if( !( $languages = $this->get( 'pageLangItems', map() ) )->count() !== 1 ) : ?>
								<div class="form-group row mandatory">
									<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Language' ) ) ?></label>
									<div class="col-sm-8">
										<select is="select-component" required class="form-select item-languageid" tabindex="<?= $enc->attr( $this->get( 'tabindex' ) ) ?>"
											v-bind:items="<?= $enc->attr( $languages->col( 'locale.language.label', 'locale.language.id' )->toArray() ) ?>"
											v-bind:name="`<?= $enc->js( $this->formparam( ['meta', '_idx_', 'text.languageid'] ) ) ?>`.replace('_idx_', idx)"
											v-bind:text="`<?= $enc->js( $this->translate( 'admin', 'Please select' ) ) ?>`"
											v-bind:all="`<?= $enc->js( $this->translate( 'admin', 'All' ) ) ?>`"
											v-bind:readonly="item['text.siteid'] != siteid"
											v-model="item['text.languageid']" >
										</select>
									</div>
									<div class="col-sm-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Language of the entered text' ) ) ?>
									</div>
								</div>
							<?php else : ?>
								<input class="text-langid" type="hidden"
									v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'text.languageid' ) ) ) ?>`.replace('_idx_', idx)"
									value="<?= $enc->attr( $languages->getCode()->first() ) ?>" />
							<?php endif ?>

							<?php if( ( $metaTypes = $this->get( 'metaTypes', map() ) )->count() !== 1 ) : ?>
								<div class="form-group row mandatory">
									<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Type' ) ) ?></label>
									<div class="col-sm-8">
										<select is="select-component" required class="form-select item-type" tabindex="<?= $enc->attr( $this->get( 'tabindex' ) ) ?>"
											v-bind:items="<?= $enc->attr( $metaTypes->col( 'text.type.label', 'text.type.code' )->toArray() ) ?>"
											v-bind:name="`<?= $enc->js( $this->formparam( ['meta', '_idx_', 'text.type'] ) ) ?>`.replace('_idx_', idx)"
											v-bind:text="`<?= $enc->js( $this->translate( 'admin', 'Please select' ) ) ?>`"
											v-bind:readonly="item['text.siteid'] != siteid"
											v-model="item['text.type']" >
										</select>
									</div>
									<div class="col-sm-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Types for additional texts like per one lb/kg or per month' ) ) ?>
									</div>
								</div>
							<?php else : ?>
								<input class="item-type" type="hidden"
									v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'text.type' ) ) ) ?>`.replace('_idx_', idx)"
									value="<?= $enc->attr( $metaTypes->getCode()->first() ) ?>" />
							<?php endif ?>

							<div class="form-group row optional">
									<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Label' ) ) ?></label>
								<div class="col-sm-8">
									<input class="form-control item-label" type="text" tabindex="<?= $this->get( 'tabindex' ) ?>"
										v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'text.label' ) ) ) ?>`.replace('_idx_', idx)"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'Label' ) ) ?>"
										v-bind:readonly="item['text.siteid'] != siteid"
										v-model="item['text.label']" />
								</div>
								<div class="col-sm-12 form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'Description of the text content if it\'s in a foreign language' ) ) ?>
								</div>
							</div>

						</div>


						<div v-on:click="toggle('_ext', idx)" class="col-xl-12 advanced" v-bind:class="{'collapsed': !item['_ext']}">
							<div class="card-tools-start">
								<div class="btn act-show fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
									title="<?= $enc->attr( $this->translate( 'admin', 'Show/hide advanced data' ) ) ?>">
								</div>
							</div>
							<span class="header-label"><?= $enc->html( $this->translate( 'admin', 'Advanced' ) ) ?></span>
						</div>

						<div v-show="item['_ext']" class="col-xl-6 secondary">

							<?php if( !( $listTypes = $this->get( 'textListTypes', map() ) )->count() !== 1 ) : ?>
								<div class="form-group row mandatory">
									<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'List type' ) ) ?></label>
									<div class="col-sm-8">
										<select is="select-component" required class="form-select listitem-type" tabindex="<?= $enc->attr( $this->get( 'tabindex' ) ) ?>"
											v-bind:items="<?= $enc->attr( $listTypes->col( 'post.lists.type.label', 'post.lists.type.code' )->toArray() ) ?>"
											v-bind:name="`<?= $enc->js( $this->formparam( ['meta', '_idx_', 'post.lists.type'] ) ) ?>`.replace('_idx_', idx)"
											v-bind:text="`<?= $enc->js( $this->translate( 'admin', 'Please select' ) ) ?>`"
											v-bind:readonly="item['post.lists.siteid'] != siteid"
											v-model="item['post.lists.type']" >
										</select>
									</div>
									<div class="col-sm-12 form-text text-muted help-text">
										<?= $enc->html( $this->translate( 'admin', 'Second level type for grouping items' ) ) ?>
									</div>
								</div>
							<?php else : ?>
								<input class="listitem-type" type="hidden"
									v-bind:name="`<?= $enc->js( $this->formparam( ['meta', '_idx_', 'post.lists.type'] ) ) ?>`.replace('_idx_', idx)"
									value="<?= $enc->attr( $listTypes->getCode()->first() ) ?>" />
							<?php endif ?>

							<div class="form-group row optional">
								<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Start date' ) ) ?></label>
								<div class="col-sm-8">
									<input is="flat-pickr" class="form-control listitem-datestart" type="datetime-local" tabindex="<?= $this->get( 'tabindex' ) ?>"
										v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'post.lists.datestart' ) ) ) ?>`.replace('_idx_', idx)"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ) ?>"
										v-bind:disabled="item['post.lists.siteid'] != siteid"
										v-bind:config="Aimeos.flatpickr.datetime"
										v-model="item['post.lists.datestart']" />
								</div>
								<div class="col-sm-12 form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'The item is only shown on the web site after that date and time' ) ) ?>
								</div>
							</div>
							<div class="form-group row optional">
								<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'End date' ) ) ?></label>
								<div class="col-sm-8">
									<input is="flat-pickr" class="form-control listitem-dateend" type="datetime-local" tabindex="<?= $this->get( 'tabindex' ) ?>"
										v-bind:name="`<?= $enc->js( $this->formparam( array( 'meta', '_idx_', 'post.lists.dateend' ) ) ) ?>`.replace('_idx_', idx)"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ) ?>"
										v-bind:disabled="item['post.lists.siteid'] != siteid"
										v-bind:config="Aimeos.flatpickr.datetime"
										v-model="item['post.lists.dateend']" />
								</div>
								<div class="col-sm-12 form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'The item is only shown on the web site until that date and time' ) ) ?>
								</div>
							</div>
						</div>

						<div v-show="item['_ext']" class="col-xl-6 secondary" v-bind:class="{readonly: item['post.lists.siteid'] != siteid}">
							<config-table v-bind:tabindex="`<?= $enc->js( $this->get( 'tabindex' ) ) ?>`"
								v-bind:keys="<?= $enc->attr( $this->config( 'admin/jqadm/post/item/meta/config/suggest', [] ) ) ?>"
								v-bind:name="`<?= $enc->js( $this->formparam( ['meta', '_idx_', 'config', '_pos_', '_key_'] ) ) ?>`"
								v-bind:index="idx" v-bind:readonly="item['post.lists.siteid'] != siteid"
								v-bind:items="item['config']" v-on:update:config="item['config'] = $event">
							</config-table>
						</div>

						<?= $this->get( 'metaBody' ) ?>

					</div>
				</div>
			</div>

			<div slot="footer" class="card-tools-more">
				<div class="btn btn-primary btn-card-more act-add fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
					title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>"
					v-on:click="add()" >
				</div>
			</div>
		</div>
	</div>
</div>
