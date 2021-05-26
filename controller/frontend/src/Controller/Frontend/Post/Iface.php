<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Post;


/**
 * Interface for post frontend controllers
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Iface
{
	/**
	 * Adds generic condition for filtering
	 *
	 * @param string $operator Comparison operator, e.g. "==", "!=", "<", "<=", ">=", ">", "=~", "~="
	 * @param string $key Search key defined by the post manager, e.g. "post.status"
	 * @param array|string $value Value or list of values to compare to
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function compare( string $operator, string $key, $value ) : Iface;

	/**
	 * Returns the post for the given post code
	 *
	 * @param string $code Unique post code
	 * @param string[] $domains Domain names of items that are associated with the posts and that should be fetched too
	 * @return \Aimeos\MShop\Post\Item\Iface Post item including the referenced domains items
	 * @since 2021.04
	 */
	public function find( string $code ) : \Aimeos\MShop\Post\Item\Iface;

	/**
	 * Creates a search function string for the given name and parameters
	 *
	 * @param string $name Name of the search function without parenthesis, e.g. "post:has"
	 * @param array $params List of parameters for the search function with numeric keys starting at 0
	 * @return string Search function string that can be used in compare()
	 */
	public function function( string $name, array $params ) : string;

	/**
	 * Returns the post for the given post ID
	 *
	 * @param string $id Unique post ID
	 * @param string[] $domains Domain names of items that are associated with the posts and that should be fetched too
	 * @return \Aimeos\MShop\Post\Item\Iface Post item including the referenced domains items
	 * @since 2021.04
	 */
	public function get( string $id ) : \Aimeos\MShop\Post\Item\Iface;

	/**
	 * Adds a filter to return only items containing a reference to the given ID
	 *
	 * @param string $domain Domain name of the referenced item, e.g. "attribute"
	 * @param string|null $type Type code of the reference, e.g. "variant" or null for all types
	 * @param string|null $refId ID of the referenced item of the given domain or null for all references
	 * @return \Aimeos\Controller\Frontend\Product\Iface Product controller for fluent interface
	 * @since 2019.10
	 */
	public function has( string $domain, string $type = null, string $refId = null ) : Iface;

	/**
	 * Parses the given array and adds the conditions to the list of conditions
	 *
	 * @param array $conditions List of conditions, e.g. ['>' => ['post.dateback' => '2000-01-01 00:00:00']]
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function parse( array $conditions ) : Iface;

	/**
	 * Returns the posts filtered by the previously assigned conditions
	 *
	 * @param int &$total Parameter where the total number of found posts will be stored in
	 * @return \Aimeos\Map Ordered list of items implementing \Aimeos\MShop\Post\Item\Iface
	 * @since 2021.04
	 */
	public function search( int &$total = null ) : \Aimeos\Map;

	/**
	 * Sets the start value and the number of returned post items for slicing the list of found post items
	 *
	 * @param int $start Start value of the first post item in the list
	 * @param int $limit Number of returned post items
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function slice( int $start, int $limit ) : Iface;

	/**
	 * Sets the sorting of the result list
	 *
	 * @param string|null $key Sorting key of the result list like "post.label", null for no sorting
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function sort( string $key = null ) : Iface;

	/**
	 * Sets the referenced domains that will be fetched too when retrieving items
	 *
	 * @param array $domains Domain names of the referenced items that should be fetched too
	 * @return \Aimeos\Controller\Frontend\Post\Iface Post controller for fluent interface
	 * @since 2021.04
	 */
	public function uses( array $domains ) : Iface;
}
