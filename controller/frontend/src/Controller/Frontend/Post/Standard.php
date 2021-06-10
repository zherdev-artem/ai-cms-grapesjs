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
	 * Returns the product for the given product URL name
	 *
	 * @param string $code Post category code
	 * @return \Aimeos\MShop\Post\Item\Iface Post item including the referenced domains items
	 * @since 2021.04
	 */
    public function codes( $code, $domain = 'category' )
    {
        $code = (array) $code;

		foreach( $code as $key => $entry )
		{
			if( is_array( $entry ) && ( $codes = $this->validateCodes( $entry ) ) !== [] )
			{
				$func = $this->filter->make( "index.$domain:codes", [$codes] );
				$this->addExpression( $this->filter->compare( '!=', $func, null ) );
				unset( $code[$key] );
			}
		}

		if( ( $codes = $this->validateCodes( $code ) ) !== [] )
		{
			$func = $this->filter->make( "index.$domain:codes", [$codes] );
			$this->addExpression( $this->filter->compare( '!=', $func, null ) );
		}

		return $this;
    }

    /**
	 * Returns the post for the given post URL name
	 *
	 * @param string $name Post URL name
	 * @return \Aimeos\MShop\Post\Item\Iface Post item including the referenced domains items
	 * @since 2021.04
	 */
	public function resolve( string $name ) : \Aimeos\MShop\Post\Item\Iface
	{
		$search = $this->manager->filter( true )->slice( 0, 1 )->add( ['index.text:url()' => $name] );

		if( ( $item = $this->manager->search( $search, $this->domains )->first() ) === null )
		{
			$msg = $this->getContext()->getI18n()->dt( 'controller/frontend', 'Unable to find post "%1$s"' );
			throw new \Aimeos\Controller\Frontend\Post\Exception( sprintf( $msg, $name ) );
		}

		return $item;
	}

    /**
	 * Adds category IDs for filtering
	 *
	 * @param array|string $catIds Catalog ID or list of IDs
	 * @param string $listtype List type of the posts referenced by the categories
	 * @param int $level Constant from \Aimeos\MW\Tree\Manager\Base if posts in subcategories are matched too
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function category( $catIds, string $listtype = 'default', int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE ) : Iface
	{
		if( !empty( $catIds ) && ( $ids = $this->validateIds( (array) $catIds ) ) !== [] )
		{
			if( $level != \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE )
			{
				$list = map();
				$cntl = \Aimeos\Controller\Frontend::create( $this->getContext(), 'category' );

				foreach( $ids as $catId ) {
					$list->union( $cntl->root( $catId )->getTree( $level )->toList() );
				}

				$ids = $this->validateIds( $list->keys()->toArray() );
			}

			$func = $this->filter->make( 'index.category:position', [$listtype, $ids] );

			$this->addExpression( $this->filter->compare( '==', 'index.category.id', $ids ) );
			$this->addExpression( $this->filter->compare( '>=', $func, 0 ) );

			$func = $this->filter->make( 'sort:index.category:position', [$listtype, $ids] );
			$this->addExpression( $this->filter->sort( '+', $func ) );
			$this->addExpression( $this->filter->sort( '+', 'post.id' ) ); // prevent flaky order if posts have same position
		}

		return $this;
	}

    /**
	 * Adds input string for full text search
	 *
	 * @param string|null $text User input for full text search
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
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
	 * @param string $domain Domain name of the referenced item, e.g. "post"
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
		$list = ( $key ? explode( ',', $key ) : [] );

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
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function uses( array $domains ) : Iface
	{
		$this->domains = $domains;
		return $this;
	}

    /**
	 * Validates the given IDs as integers
	 *
	 * @param array $ids List of IDs to validate
	 * @return array List of validated IDs
	 */
	protected function validateIds( array $ids ) : array
	{
		$list = [];

		foreach( $ids as $id )
		{
			if( is_array( $id ) ) {
				$list[] = $this->validateIds( $id );
			} elseif( $id != '' && preg_match( '/^[A-Za-z0-9\-\_]+$/', $id ) === 1 ) {
				$list[] = (string) $id;
			}
		}

		return $list;
	}

    /**
	 * Validates the given codes as integers
	 *
	 * @param array $ids List of codes to validate
	 * @return array List of validated codes
	 */
	protected function validateCodes( array $codes ) : array
	{
		$list = [];

		foreach( $codes as $code )
		{
			if( is_array( $code ) ) {
				$list[] = $this->valcodeateCodes( $code );
			} elseif( $code != '' && preg_match( '/^[A-Za-z0-9\-\_]+$/', $code ) === 1 ) {
				$list[] = (string) $code;
			}
		}

		return $list;
	}
}
