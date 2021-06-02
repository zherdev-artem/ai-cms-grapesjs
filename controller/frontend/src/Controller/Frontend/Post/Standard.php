<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Post;


/**
 * Default implementation of the post frontend controller
 *
 * @package Controller
 * @subpackage Frontend
 */
class Standard
	extends \Aimeos\Controller\Frontend\Base
	implements Iface, \Aimeos\Controller\Frontend\Common\Iface
{
	private $conditions = [];
	private $domains = [];
	private $filter;
	private $manager;


	/**
	 * Common initialization for controller classes
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Common MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->manager = \Aimeos\MShop::create( $context, 'postindex' );
		$this->filter = $this->manager->filter( true );
		$this->conditions[] = $this->filter->getConditions();
	}


	/**
	 * Clones objects in controller and resets values
	 */
	public function __clone()
	{
		$this->filter = clone $this->filter;
	}


	/**
	 * Adds generic condition for filtering
	 *
	 * @param string $operator Comparison operator, e.g. "==", "!=", "<", "<=", ">=", ">", "=~", "~="
	 * @param string $key Search key defined by the post manager, e.g. "post.status"
	 * @param array|string $value Value or list of values to compare to
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function compare( string $operator, string $key, $value ) : Iface
	{
		$this->conditions[] = $this->filter->compare( $operator, $key, $value );
		return $this;
	}


	/**
	 * Returns the post for the given post code
	 *
	 * @param string $code Unique post code
	 * @return \Aimeos\MShop\Post\Item\Iface Post item including the referenced domains items
	 * @since 2021.04
	 */
	public function find( string $code ) : \Aimeos\MShop\Post\Item\Iface
	{
		return $this->manager->find( $code, $this->domains, 'post', null, true );
	}


	/**
	 * Creates a search function string for the given name and parameters
	 *
	 * @param string $name Name of the search function without parenthesis, e.g. "post:has"
	 * @param array $params List of parameters for the search function with numeric keys starting at 0
	 * @return string Search function string that can be used in compare()
	 */
	public function function( string $name, array $params ) : string
	{
		return $this->filter->make( $name, $params );
	}


	/**
	 * Returns the post for the given post ID
	 *
	 * @param string $id Unique post ID
	 * @return \Aimeos\MShop\Post\Item\Iface Post item including the referenced domains items
	 * @since 2021.04
	 */
	public function get( string $id ) : \Aimeos\MShop\Post\Item\Iface
	{
		return $this->manager->get( $id, $this->domains, true );
	}

    /**
	 * Adds input string for full text search
	 *
	 * @param string|null $text User input for full text search
	 * @return \Aimeos\Controller\Frontend\Product\Iface Product controller for fluent interface
	 * @since 2021.04
	 */
	public function text( string $text = null ) : Iface
	{
		if( !empty( $text ) )
		{
			$langid = $this->getContext()->getLocale()->getLanguageId();
			$func = $this->filter->make( 'index.text:relevance', [$langid, $text] );
			$sortfunc = $this->filter->make( 'sort:index.text:relevance', [$langid, $text] );

			$this->addExpression( $this->filter->compare( '>', $func, 0 ) );
			$this->addExpression( $this->filter->sort( '-', $sortfunc ) );
		}

		return $this;
	}


	/**
	 * Adds a filter to return only items containing a reference to the given ID
	 *
	 * @param string $domain Domain name of the referenced item, e.g. "product"
	 * @param string|null $type Type code of the reference, e.g. "default" or null for all types
	 * @param string|null $refId ID of the referenced item of the given domain or null for all references
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function has( string $domain, string $type = null, string $refId = null ) : Iface
	{
		$params = [$domain];
		!$type ?: $params[] = $type;
		!$refId ?: $params[] = $refId;

		$func = $this->filter->make( 'post:has', $params );
		$this->conditions[] = $this->filter->compare( '!=', $func, null );
		return $this;
	}


	/**
	 * Parses the given array and adds the conditions to the list of conditions
	 *
	 * @param array $conditions List of conditions, e.g. ['=~' => ['post.label' => 'test']]
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function parse( array $conditions ) : Iface
	{
		if( ( $cond = $this->filter->parse( $conditions ) ) !== null ) {
			$this->conditions[] = $cond;
		}

		return $this;
	}


	/**
	 * Returns the posts filtered by the previously assigned conditions
	 *
	 * @param int &$total Parameter where the total number of found posts will be stored in
	 * @return \Aimeos\Map Ordered list of post items implementing \Aimeos\MShop\Post\Item\Iface
	 * @since 2021.04
	 */
	public function search( int &$total = null ) : \Aimeos\Map
	{
        $this->filter->setSortations( $this->getSortations() );
		$this->filter->setConditions( $this->filter->and( $this->getConditions() ) );
		return $this->manager->search( $this->filter, $this->domains, $total );
	}


	/**
	 * Sets the start value and the number of returned post items for slicing the list of found post items
	 *
	 * @param int $start Start value of the first post item in the list
	 * @param int $limit Number of returned post items
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function slice( int $start, int $limit ) : Iface
	{
		$this->filter->slice( $start, $limit );
		return $this;
	}


	/**
	 * Sets the sorting of the result list
	 *
	 * @param string|null $key Sorting key of the result list like "post.label", null for no sorting
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function sort( string $key = null ) : Iface
	{
		$sort = [];
		$list = ( $key ? explode( ',', $key ) : [] );

		foreach( $list as $sortkey )
		{
			$direction = ( $sortkey[0] === '-' ? '-' : '+' );
			$sort[] = $this->filter->sort( $direction, ltrim( $sortkey, '+-' ) );
		}

		$this->filter->setSortations( $sort );
		return $this;
	}


	/**
	 * Sets the referenced domains that will be fetched too when retrieving items
	 *
	 * @param array $domains Domain names of the referenced items that should be fetched too
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function uses( array $domains ) : Iface
	{
		$this->domains = $domains;
		return $this;
	}
}
