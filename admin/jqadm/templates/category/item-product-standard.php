<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


/** admin/jqadm/category/product/fields
 * List of category list and product columns that should be displayed in the category product view
 *
 * Changes the list of category list and product columns shown by default in the
 * category product view. The columns can be changed by the editor as required
 * within the administraiton interface.
 *
 * The names of the colums are in fact the search keys defined by the managers,
 * e.g. "category.lists.status" for the status value.
 *
 * @param array List of field names, i.e. search keys
 * @since 2017.10
 * @category Developer
 */
$fields = ['category.lists.status', 'category.lists.type', 'category.lists.position', 'category.lists.refid'];
$fields = $this->config( 'admin/jqadm/category/product/fields', $fields );


?>
<div id="product" class="item-product tab-pane fade box" role="tabpanel" aria-labelledby="product">
	<?= $this->partial( $this->config( 'admin/jqadm/partial/productref', 'common/partials/productref-standard' ), [
		'types' => $this->get( 'productListTypes', map() )->col( 'category.lists.type.label', 'category.lists.type.code' )->toArray(),
		'siteid' => $this->site()->siteid(),
		'parentid' => $this->param( 'id' ),
		'resource' => 'category/lists',
		'fields' => $fields,
	] ) ?>
</div>
<?= $this->get( 'productBody' ) ?>
