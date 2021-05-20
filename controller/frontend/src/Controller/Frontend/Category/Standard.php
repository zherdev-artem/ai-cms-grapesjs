<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Category;


/**
 * Default implementation of the category frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Standard
	extends \Aimeos\Controller\Frontend\Base
	implements Iface, \Aimeos\Controller\Frontend\Common\Iface
{
	private $domains = [];
	private $filter;
	private $manager;
	private $root;


	/**
	 * Common initialization for controller classes
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Common MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->manager = \Aimeos\MShop::create( $context, 'category' );
		$this->filter = $this->manager->filter( true );
		$this->addExpression( $this->filter->getConditions() );
	}


	/**
	 * Clones objects in controller and resets values
	 */
	public function __clone()
	{
		$this->filter = clone $this->filter;
	}


	/**
	 * Adds generic condition for filtering attributes
	 *
	 * @param string $operator Comparison operator, e.g. "==", "!=", "<", "<=", ">=", ">", "=~", "~="
	 * @param string $key Search key defined by the category manager, e.g. "category.status"
	 * @param array|string $value Value or list of values to compare to
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 * @since 2019.04
	 */
	public function compare( string $operator, string $key, $value ) : Iface
	{
		$this->addExpression( $this->filter->compare( $operator, $key, $value ) );
		return $this;
	}


	/**
	 * Returns the category for the given category code
	 *
	 * @param string $code Unique category code
	 * @return \Aimeos\MShop\Category\Item\Iface Category item including the referenced domains items
	 * @since 2019.04
	 */
	public function find( string $code ) : \Aimeos\MShop\Category\Item\Iface
	{
		return $this->manager->find( $code, $this->domains, null, null, true );
	}


	/**
	 * Creates a search function string for the given name and parameters
	 *
	 * @param string $name Name of the search function without parenthesis, e.g. "category:has"
	 * @param array $params List of parameters for the search function with numeric keys starting at 0
	 * @return string Search function string that can be used in compare()
	 */
	public function function( string $name, array $params ) : string
	{
		return $this->filter->make( $name, $params );
	}


	/**
	 * Returns the category for the given category ID
	 *
	 * @param string $id Unique category ID
	 * @return \Aimeos\MShop\Category\Item\Iface Category item including the referenced domains items
	 * @since 2019.04
	 */
	public function get( string $id ) : \Aimeos\MShop\Category\Item\Iface
	{
		return $this->manager->get( $id, $this->domains, true );
	}


	/**
	 * Returns the list of categories up to the root node including the node given by its ID
	 *
	 * @param string $id Current category ID
	 * @return \Aimeos\MShop\Category\Item\Iface[] Associative list of categories
	 * @since 2017.03
	 */
	public function getPath( string $id )
	{
		$list = $this->manager->getPath( $id, $this->domains );

		if( $list->isAvailable()->search( false ) ) {
			throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Category is not available' ) );
		}

		if( $this->root )
		{
			foreach( $list as $key => $item )
			{
				if( $key == $this->root ) {
					break;
				}
				unset( $list[$key] );
			}
		}

		return $list;
	}


	/**
	 * Returns the categories filtered by the previously assigned conditions
	 *
	 * @param int $level Tree level constant, e.g. ONE, LIST or TREE
	 * @return \Aimeos\MShop\Category\Item\Iface Category tree
	 * @since 2019.04
	 */
	public function getTree( int $level = Iface::TREE ) : \Aimeos\MShop\Category\Item\Iface
	{
		$this->filter->setConditions( $this->filter->and( $this->getConditions() ) );
		return $this->manager->getTree( $this->root, $this->domains, $level, $this->filter );
	}


	/**
	 * Parses the given array and adds the conditions to the list of conditions
	 *
	 * @param array $conditions List of conditions, e.g. ['>' => ['category.status' => 0]]
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 * @since 2019.04
	 */
	public function parse( array $conditions ) : Iface
	{
		if( ( $cond = $this->filter->parse( $conditions ) ) !== null ) {
			$this->addExpression( $cond );
		}

		return $this;
	}


	/**
	 * Sets the category ID of node that is used as root node
	 *
	 * @param string|null $id Category ID
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 * @since 2019.04
	 */
	public function root( string $id = null ) : Iface
	{
		$this->root = ( $id ? $id : null );
		return $this;
	}


	/**
	 * Returns the categories filtered by the previously assigned conditions
	 *
	 * @param int &$total Parameter where the total number of found categories will be stored in
	 * @return \Aimeos\Map Ordered list of category items implementing \Aimeos\MShop\Category\Item\Iface
	 * @since 2019.10
	 */
	public function search( int &$total = null ) : \Aimeos\Map
	{
		$this->filter->setConditions( $this->filter->and( $this->getConditions() ) );
		$this->filter->setSortations( $this->getSortations() );

		return $this->manager->search( $this->filter, $this->domains, $total );
	}


	/**
	 * Sets the start value and the number of returned products for slicing the list of found products
	 *
	 * @param int $start Start value of the first product in the list
	 * @param int $limit Number of returned products
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 * @since 2019.10
	 */
	public function slice( int $start, int $limit ) : Iface
	{
		$maxsize = $this->getContext()->config()->get( 'controller/frontend/common/max-size', 250 );
		$this->filter->slice( $start, min( $limit, $maxsize ) );
		return $this;
	}


	/**
	 * Sets the sorting of the result list
	 *
	 * @param string|null $key Search key for sorting of the result list and null for no sorting
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 * @since 2019.10
	 */
	public function sort( ?string $key = null ) : Iface
	{
		$list = $this->splitKeys( $key );

		foreach( $list as $sortkey )
		{
			$direction = ( $sortkey[0] === '-' ? '-' : '+' );
			$this->addExpression( $this->filter->sort( $direction, ltrim( $sortkey, '+-' ) ) );
		}

		return $this;
	}


	/**
	 * Sets the referenced domains that will be fetched too when retrieving items
	 *
	 * @param array $domains Domain names of the referenced items that should be fetched too
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 * @since 2019.04
	 */
	public function uses( array $domains ) : Iface
	{
		$this->domains = $domains;
		return $this;
	}


	/**
	 * Limits categories returned to only visible ones depending on the given category IDs
	 *
	 * @param array $catIds List of category IDs
	 * @return \Aimeos\Controller\Frontend\Category\Iface Category controller for fluent interface
	 */
	public function visible( array $catIds ) : Iface
	{
		if( empty( $catIds ) ) {
			return $this;
		}

		$config = $this->getContext()->getConfig();

		$expr = [
			$this->filter->compare( '==', 'category.parentid', $catIds ),
			$this->filter->compare( '==', 'category.id', $catIds )
		];

		/** controller/frontend/category/levels-always
		 * The number of levels in the category tree that should be always displayed
		 *
		 * Usually, only the root node and the first level of the category
		 * tree is shown in the frontend. Only if the user clicks on a
		 * node in the first level, the page reloads and the sub-nodes of
		 * the chosen category are rendered as well.
		 *
		 * Using this configuration option you can enforce the given number
		 * of levels to be always displayed. The root node uses level 0, the
		 * categories below level 1 and so on.
		 *
		 * In most cases you can set this value via the administration interface
		 * of the shop application. In that case you often can configure the
		 * levels individually for each category filter.
		 *
		 * Note: This setting was available between 2014.03 and 2019.04 as
		 * client/html/category/filter/tree/levels-always
		 *
		 * @param integer Number of tree levels
		 * @since 2019.04
		 * @category User
		 * @category Developer
		 * @see controller/frontend/category/levels-only
		 */
		if( ( $levels = $config->get( 'controller/frontend/category/levels-always' ) ) != null ) {
			$expr[] = $this->filter->compare( '<=', 'category.level', $levels );
		}

		/** controller/frontend/category/levels-only
		 * No more than this number of levels in the category tree should be displayed
		 *
		 * If the user clicks on a category node, the page reloads and the
		 * sub-nodes of the chosen category are rendered as well.
		 * Using this configuration option you can enforce that no more than
		 * the given number of levels will be displayed at all. The root
		 * node uses level 0, the categories below level 1 and so on.
		 *
		 * In most cases you can set this value via the administration interface
		 * of the shop application. In that case you often can configure the
		 * levels individually for each category filter.
		 *
		 * Note: This setting was available between 2014.03 and 2019.04 as
		 * client/html/category/filter/tree/levels-only
		 *
		 * @param integer Number of tree levels
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/frontend/category/levels-always
		 */
		if( ( $levels = $config->get( 'controller/frontend/category/levels-only' ) ) != null ) {
			$this->addExpression( $this->filter->compare( '<=', 'category.level', $levels ) );
		}

		$this->addExpression( $this->filter->or( $expr ) );
		return $this;
	}
}
