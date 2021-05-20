<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Category
 */


namespace Aimeos\MShop\Category\Item;


/**
 * Generic interface for category items.
 *
 * @package MShop
 * @subpackage Category
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Tree\Iface
{
	/**
	 * Returns the URL segment for the category item.
	 *
	 * @return string URL segment of the category item
	 */
	public function getUrl() : string;

	/**
	 * Sets a new URL segment for the category.
	 *
	 * @param string|null $url New URL segment of the category item
	 * @return \Aimeos\MShop\Category\Item\Iface Category item for chaining method calls
	 */
	public function setUrl( ?string $url ) : \Aimeos\MShop\Category\Item\Iface;

	/**
	 * Returns the URL target specific for that category
	 *
	 * @return string URL target specific for that category
	 */
	public function getTarget() : string;

	/**
	 * Sets a new URL target specific for that category
	 *
	 * @param string $value New URL target specific for that category
	 * @return \Aimeos\MShop\Category\Item\Iface Category item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Category\Item\Iface;
}
