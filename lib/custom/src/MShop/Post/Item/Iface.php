<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Post
 */


namespace Aimeos\MShop\Post\Item;


/**
 * Generic interface for post pages created and saved by post managers.
 *
 * @package MShop
 * @subpackage Post
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the URL of the post page.
	 *
	 * @return string URL of the post page
	 */
	public function getUrl() : string;

	/**
	 * Sets the URL of the post page.
	 *
	 * @param string $value URL of the post page
	 * @return \Aimeos\MShop\Post\Item\Iface Post page for chaining method calls
	 */
	public function setUrl( string $value ) : \Aimeos\MShop\Post\Item\Iface;

	/**
	 * Returns the name of the attribute page.
	 *
	 * @return string Label of the attribute page
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the attribute page.
	 *
	 * @param string $label Type label of the attribute page
	 * @return \Aimeos\MShop\Post\Item\Iface Post page for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Post\Item\Iface;

    public function getCategoryItems() : \Aimeos\Map;
}
