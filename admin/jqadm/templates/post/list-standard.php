<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

$enc = $this->encoder();


$target = $this->config( 'admin/jqadm/url/search/target' );
$controller = $this->config( 'admin/jqadm/url/search/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/search/action', 'search' );
$config = $this->config( 'admin/jqadm/url/search/config', [] );


$newTarget = $this->config( 'admin/jqadm/url/create/target' );
$newCntl = $this->config( 'admin/jqadm/url/create/controller', 'Jqadm' );
$newAction = $this->config( 'admin/jqadm/url/create/action', 'create' );
$newConfig = $this->config( 'admin/jqadm/url/create/config', [] );


$getTarget = $this->config( 'admin/jqadm/url/get/target' );
$getCntl = $this->config( 'admin/jqadm/url/get/controller', 'Jqadm' );
$getAction = $this->config( 'admin/jqadm/url/get/action', 'get' );
$getConfig = $this->config( 'admin/jqadm/url/get/config', [] );


$copyTarget = $this->config( 'admin/jqadm/url/copy/target' );
$copyCntl = $this->config( 'admin/jqadm/url/copy/controller', 'Jqadm' );
$copyAction = $this->config( 'admin/jqadm/url/copy/action', 'copy' );
$copyConfig = $this->config( 'admin/jqadm/url/copy/config', [] );


$delTarget = $this->config( 'admin/jqadm/url/delete/target' );
$delCntl = $this->config( 'admin/jqadm/url/delete/controller', 'Jqadm' );
$delAction = $this->config( 'admin/jqadm/url/delete/action', 'delete' );
$delConfig = $this->config( 'admin/jqadm/url/delete/config', [] );


/** admin/jqadm/post/fields
 * List of post columns that should be displayed in the list view
 *
 * Changes the list of post columns shown by default in the post list view.
 * The columns can be changed by the editor as required within the administraiton
 * interface.
 *
 * The names of the colums are in fact the search keys defined by the managers,
 * e.g. "post.id" for the post ID.
 *
 * @param array List of field names, i.e. search keys
 * @since 2020.10
 * @category Developer
 */
$default = ['post.status', 'post.url', 'post.label'];
$default = $this->config( 'admin/jqadm/post/fields', $default );
$fields = $this->session( 'aimeos/admin/jqadm/post/fields', $default );

$searchParams = $params = $this->get( 'pageParams', [] );
$searchParams['page']['start'] = 0;

$searchAttributes = map( $this->get( 'filterAttributes', [] ) )->filter( function( $item ) {
	return $item->isPublic();
} )->call( 'toArray' )->each( function( &$val ) {
	$val = $this->translate( 'admin/ext', $val['label'] ?? ' ' );
} )->all();

$operators = map( $this->get( 'filterOperators/compare', [] ) )->flip()->map( function( $val, $key ) {
	return $this->translate( 'admin/ext', $key );
} )->all();

$columnList = [
	'post.id' => $this->translate( 'admin', 'ID' ),
	'post.status' => $this->translate( 'admin', 'Status' ),
	'post.url' => $this->translate( 'admin', 'URL' ),
	'post.label' => $this->translate( 'admin', 'Title' ),
    'category.label' => $this->translate( 'admin', 'Catalog' ),
	'post.ctime' => $this->translate( 'admin', 'Created' ),
	'post.mtime' => $this->translate( 'admin', 'Modified' ),
	'post.editor' => $this->translate( 'admin', 'Editor' ),
];

?>
<?php $this->block()->start( 'jqadm_content' ) ?>

<?= $this->partial( $this->config( 'admin/jqadm/partial/navsearch', 'common/partials/navsearch-standard' ) ) ?>
<?= $this->partial( $this->config( 'admin/jqadm/partial/columns', 'common/partials/columns-standard' ) ) ?>

<div class="list-view"
	data-domain="post"
	data-items="<?= $enc->attr( $this->get( 'items', map() )->call( 'toArray' )->all() ) ?>">

	<nav class="main-navbar">

		<span class="navbar-brand">
			<?= $enc->html( $this->translate( 'admin', 'Posts' ) ) ?>
			<span class="navbar-secondary">(<?= $enc->html( $this->site()->label() ) ?>)</span>
		</span>

		<div class="btn fa act-search" v-on:click="search = true"
			title="<?= $enc->attr( $this->translate( 'admin', 'Show search form' ) ) ?>"
			aria-label="<?= $enc->attr( $this->translate( 'admin', 'Show search form' ) ) ?>">
		</div>
	</nav>

	<nav-search v-bind:show="search" v-on:close="search = false"
		v-bind:url="`<?= $enc->js( $this->link( 'admin/jqadm/url/search', map( $searchParams )->except( 'filter' )->all() ) ) ?>`"
		v-bind:filter="<?= $enc->attr( $this->session( 'aimeos/admin/jqadm/post/filter', [] ) ) ?>"
		v-bind:operators="<?= $enc->attr( $operators ) ?>"
		v-bind:name="`<?= $enc->js( $this->formparam( ['filter', '_key_', '0'] ) ) ?>`"
		v-bind:attributes="<?= $enc->attr( $searchAttributes ) ?>">
	</nav-search>

	<?= $this->partial(
			$this->config( 'admin/jqadm/partial/pagination', 'common/partials/pagination-standard' ),
			['pageParams' => $params, 'pos' => 'top', 'total' => $this->get( 'total' ),
			'page' => $this->session( 'aimeos/admin/jqadm/post/page', [] )]
		);
	?>

	<form ref="form" class="list list-post" method="POST"
		action="<?= $enc->attr( $this->url( $target, $controller, $action, $searchParams, [], $config ) ) ?>"
		data-deleteurl="<?= $enc->attr( $this->url( $delTarget, $delCntl, $delAction, $params, [], $delConfig ) ) ?>">

		<?= $this->csrf()->formfield() ?>

		<column-select tabindex="<?= $this->get( 'tabindex', 1 ) ?>"
			name="<?= $enc->attr( $this->formparam( ['fields', ''] ) ) ?>"
			v-bind:titles="<?= $enc->attr( $columnList ) ?>"
			v-bind:fields="<?= $enc->attr( $fields ) ?>"
			v-bind:show="columns"
			v-on:close="columns = false">
		</column-select>

		<table class="list-items table table-hover table-striped">
			<thead class="list-header">
				<tr>
					<th class="select">
						<a href="#" class="btn act-delete fa" tabindex="1"
							v-on:click.prevent.stop="askDelete()"
							title="<?= $enc->attr( $this->translate( 'admin', 'Delete selected entries' ) ) ?>"
							aria-label="<?= $enc->attr( $this->translate( 'admin', 'Delete' ) ) ?>">
						</a>
					</th>

					<?= $this->partial(
							$this->config( 'admin/jqadm/partial/listhead', 'common/partials/listhead-standard' ),
							['fields' => $fields, 'params' => $params, 'data' => $columnList, 'sort' => $this->session( 'aimeos/admin/jqadm/post/sort' )]
						);
					?>

					<th class="actions">
						<a class="btn fa act-add" tabindex="1"
							href="<?= $enc->attr( $this->url( $newTarget, $newCntl, $newAction, $params, [], $newConfig ) ) ?>"
							title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>"
							aria-label="<?= $enc->attr( $this->translate( 'admin', 'Add' ) ) ?>">
						</a>

						<a class="btn act-columns fa" href="#" tabindex="<?= $this->get( 'tabindex', 1 ) ?>"
							title="<?= $enc->attr( $this->translate( 'admin', 'Columns' ) ) ?>"
							v-on:click.prevent.stop="columns = true">
						</a>
					</th>
				</tr>
			</thead>
			<tbody>

				<?= $this->partial(
					$this->config( 'admin/jqadm/partial/listsearch', 'common/partials/listsearch-standard' ), [
						'fields' => array_merge( $fields, ['select'] ), 'filter' => $this->session( 'aimeos/admin/jqadm/post/filter', [] ),
						'data' => [
							'post.id' => ['op' => '=='],
							'post.status' => ['op' => '==', 'type' => 'select', 'val' => [
								'1' => $this->translate( 'mshop/code', 'status:1' ),
								'0' => $this->translate( 'mshop/code', 'status:0' ),
								'-1' => $this->translate( 'mshop/code', 'status:-1' ),
								'-2' => $this->translate( 'mshop/code', 'status:-2' ),
							]],
							'post.url' => [],
                            'category.label' => [],
							'post.label' => [],
							'post.ctime' => ['op' => '-', 'type' => 'datetime-local'],
							'post.mtime' => ['op' => '-', 'type' => 'datetime-local'],
							'post.editor' => [],
						]
					] );
				?>

				<?php foreach( $this->get( 'items', [] ) as $id => $item ) : ?>
					<?php $url = $enc->attr( $this->url( $getTarget, $getCntl, $getAction, ['id' => $id] + $params, [], $getConfig ) ) ?>
					<tr class="list-item <?= $this->site()->readonly( $item->getSiteId() ) ?>" data-label="<?= $enc->attr( $item->getLabel() ) ?>">
						<td class="select"><input v-on:click="toggle(`<?= $enc->js( $id ) ?>`)" v-bind:checked="items[`<?= $enc->js( $id ) ?>`].checked" class="form-check-input" type="checkbox" tabindex="1" name="<?= $enc->attr( $this->formparam( ['id', ''] ) ) ?>" value="<?= $enc->attr( $item->getId() ) ?>" /></td>
						<?php if( in_array( 'post.id', $fields ) ) : ?>
							<td class="post-id"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getId() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'post.status', $fields ) ) : ?>
							<td class="post-status"><a class="items-field" href="<?= $url ?>"><div class="fa status-<?= $enc->attr( $item->getStatus() ) ?>"></div></a></td>
						<?php endif ?>
						<?php if( in_array( 'post.url', $fields ) ) : ?>
							<td class="post-url"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getUrl() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'post.label', $fields ) ) : ?>
							<td class="post-label"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getLabel() ) ?></a></td>
						<?php endif ?>
                        <?php if( in_array( 'category.label', $fields ) ) : ?>
							<td class="category-label"><a class="items-field" href="<?= $url ?>"><?= $enc->html( ( $cat = $item->getCategory() ) ? $cat->getLabel() : "" ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'post.ctime', $fields ) ) : ?>
							<td class="post-ctime"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getTimeCreated() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'post.mtime', $fields ) ) : ?>
							<td class="post-mtime"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getTimeModified() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'post.editor', $fields ) ) : ?>
							<td class="post-editor"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getEditor() ) ?></a></td>
						<?php endif ?>

						<td class="actions">
							<a class="btn act-copy fa" tabindex="1"
								href="<?= $enc->attr( $this->url( $copyTarget, $copyCntl, $copyAction, ['id' => $id] + $params, [], $copyConfig ) ) ?>"
								title="<?= $enc->attr( $this->translate( 'admin', 'Copy this entry' ) ) ?>"
								aria-label="<?= $enc->attr( $this->translate( 'admin', 'Copy' ) ) ?>">
							</a>
							<?php if( !$this->site()->readonly( $item->getSiteId() ) ) : ?>
								<a class="btn act-delete fa" tabindex="1" href="#"
									v-on:click.prevent.stop="askDelete(`<?= $enc->js( $id ) ?>`)"
									title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>"
									aria-label="<?= $enc->attr( $this->translate( 'admin', 'Delete' ) ) ?>">
								</a>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>

		<?php if( $this->get( 'items', map() )->isEmpty() ) : ?>
			<div class="noitems"><?= $enc->html( sprintf( $this->translate( 'admin', 'No items found' ) ) ) ?></div>
		<?php endif ?>
	</form>

	<?= $this->partial(
			$this->config( 'admin/jqadm/partial/pagination', 'common/partials/pagination-standard' ),
			['pageParams' => $params, 'pos' => 'bottom', 'total' => $this->get( 'total' ),
			'page' => $this->session( 'aimeos/admin/jqadm/post/page', [] )]
		);
	?>

	<confirm-delete v-bind:items="unconfirmed" v-bind:show="dialog"
		v-on:close="confirmDelete(false)" v-on:confirm="confirmDelete(true)">
	</confirm-delete>

</div>
<?php $this->block()->stop() ?>

<?= $this->render( $this->config( 'admin/jqadm/template/page', 'common/page-standard' ) ) ?>
