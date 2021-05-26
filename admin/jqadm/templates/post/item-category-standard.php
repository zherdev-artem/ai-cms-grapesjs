<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


$enc = $this->encoder();

$keys = [
	'category.lists.id', 'category.lists.siteid', 'category.lists.type', 'category.lists.refid',
	'category.label', 'category.code', 'category.id'
];


?>
<div id="category" class="item-category tab-pane fade" role="tabpanel" aria-labelledby="category">

	<div class="row">
		<div class="col-xl-6 category-default">
			<div class="box">
				<table class="category-list table table-default"
					data-items="<?= $enc->attr( $this->get( 'categoryData', [] ) ) ?>"
					data-keys="<?= $enc->attr( $keys ) ?>"
					data-siteid="<?= $this->site()->siteid() ?>"
					data-listtype="default">

					<thead>
						<tr>
							<th>
								<span class="help"><?= $enc->html( $this->translate( 'admin', 'Default' ) ) ?></span>
								<div class="form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'Categories the post can be found in by the user on the web site' ) ) ?>
								</div>
							</th>
							<th class="actions">
								<div class="btn act-add fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
									title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>"
									v-on:click="add()">
								</div>
							</th>
						</tr>
					</thead>

					<tbody>

						<tr v-for="(item, idx) in items" v-if="item['category.lists.type'] == listtype" v-bind:key="idx"
							v-bind:class="checkSite(idx) ? 'readonly' : ''">
							<td v-bind:class="item['css'] || ''">
								<input class="item-listtype" type="hidden" v-model="item['category.lists.type']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'default-idx', 'category.lists.type'] ) ) ?>`.replace( 'idx', idx )" />

								<input class="item-listid" type="hidden" v-model="item['category.lists.id']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'default-idx', 'category.lists.id'] ) ) ?>`.replace( 'idx', idx )" />

								<input class="item-label" type="hidden" v-model="item['category.code']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'default-idx', 'category.code'] ) ) ?>`.replace( 'idx', idx )" />

								<input class="item-label" type="hidden" v-model="item['category.label']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'default-idx', 'category.label'] ) ) ?>`.replace( 'idx', idx )" />

								<select is="combo-box" class="form-select item-id"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'default-idx', 'category.id'] ) ) ?>`.replace( 'idx', idx )"
									v-bind:readonly="checkSite(idx) || item['category.lists.id'] != ''"
									v-bind:tabindex="`<?= $enc->js( $this->get( 'tabindex' ) ) ?>`"
									v-bind:label="getLabel(idx)"
									v-bind:required="'required'"
									v-bind:getfcn="getItems"
									v-bind:index="idx"
									v-on:select="update"
									v-model="item['category.id']" >
								</select>
							</td>
							<td class="actions">
								<div v-if="!checkSite(idx)" class="btn act-delete fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
									title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>"
									v-on:click.stop="remove(idx)">
								</div>
							</td>
						</tr>

					</tbody>

				</table>
			</div>
		</div>

		<div class="col-xl-6 category-promotion">

			<div class="box">
				<table class="category-list table table-default"
					data-items="<?= $enc->attr( $this->get( 'categoryData', [] ) ) ?>"
					data-keys="<?= $enc->attr( $keys ) ?>"
					data-siteid="<?= $this->site()->siteid() ?>"
					data-listtype="promotion">

					<thead>
						<tr>
							<th>
								<span class="help"><?= $enc->html( $this->translate( 'admin', 'Promotion' ) ) ?></span>
								<div class="form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'Categories the post will be shown for in the promotional section' ) ) ?>
								</div>
							</th>
							<th class="actions">
								<div class="btn act-add fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
									title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>"
									v-on:click="add()">
								</div>
							</th>
						</tr>
					</thead>

					<tbody>

					<tr v-for="(item, idx) in items" v-if="item['category.lists.type'] == listtype" v-bind:key="idx"
							v-bind:class="checkSite(idx) ? 'readonly' : ''">
							<td v-bind:class="item['css'] || ''">
								<input class="item-listtype" type="hidden" v-model="item['category.lists.type']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'promotion-idx', 'category.lists.type'] ) ) ?>`.replace( 'idx', idx )" />

								<input class="item-listid" type="hidden" v-model="item['category.lists.id']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'promotion-idx', 'category.lists.id'] ) ) ?>`.replace( 'idx', idx )" />

								<input class="item-label" type="hidden" v-model="item['category.code']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'promotion-idx', 'category.code'] ) ) ?>`.replace( 'idx', idx )" />

								<input class="item-label" type="hidden" v-model="item['category.label']"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'promotion-idx', 'category.label'] ) ) ?>`.replace( 'idx', idx )" />

								<select is="combo-box" class="form-select item-id"
									v-bind:name="`<?= $enc->js( $this->formparam( ['category', 'promotion-idx', 'category.id'] ) ) ?>`.replace( 'idx', idx )"
									v-bind:readonly="checkSite(idx) || item['category.lists.id'] != ''"
									v-bind:tabindex="`<?= $enc->js( $this->get( 'tabindex' ) ) ?>`"
									v-bind:label="getLabel(idx)"
									v-bind:required="'required'"
									v-bind:getfcn="getItems"
									v-bind:index="idx"
									v-on:select="update"
									v-model="item['category.id']" >
								</select>
							</td>
							<td class="actions">
								<div v-if="!checkSite(idx)" class="btn act-delete fa" tabindex="<?= $this->get( 'tabindex' ) ?>"
									title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>"
									v-on:click.stop="remove(idx)">
								</div>
							</td>
						</tr>

					</tbody>

				</table>
			</div>
		</div>
	</div>

	<?= $this->get( 'categoryBody' ) ?>

</div>
