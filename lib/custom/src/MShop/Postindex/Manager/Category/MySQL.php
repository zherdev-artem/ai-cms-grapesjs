<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Postindex
 */


namespace Aimeos\MShop\Postindex\Manager\Category;


/**
 * MySQL based index category for searching in post tables.
 *
 * @package MShop
 * @subpackage Postindex
 */
class MySQL
	extends \Aimeos\MShop\Postindex\Manager\Category\Standard
{
	private $searchConfig = array(
		'index.category.id' => array(
			'code' => 'index.category.id',
			'internalcode' => 'mpostindca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_post_index_category" AS mpostindca USE INDEX ("idx_msindca_s_ca_lt_po", "unq_msindca_p_s_cid_lt_po") ON mpostindca."postid" = mpro."id"' ),
			'label' => 'Post index category ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attriubte items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}
}
