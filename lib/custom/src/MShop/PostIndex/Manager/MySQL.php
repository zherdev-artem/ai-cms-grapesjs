<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage PostIndex
 */


namespace Aimeos\MShop\PostIndex\Manager;


/**
 * MySQL index index manager for searching in product tables.
 *
 * @package MShop
 * @subpackage PostIndex
 */
class MySQL
	extends \Aimeos\MShop\PostIndex\Manager\Standard
	implements \Aimeos\MShop\PostIndex\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $subManagers;


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'postindex', $manager, $name ?: 'MySQL' );
	}


	/**
	 * Returns the list of sub-managers available for the index attribute manager.
	 *
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface[] Associative list of the sub-domain as key and the manager object as value
	 */
	protected function getSubManagers() : array
	{
		if( $this->subManagers === null )
		{
			$this->subManagers = [];
			$config = $this->getContext()->getConfig();

			foreach( $config->get( 'mshop/post/index/manager/submanagers', [] ) as $domain )
			{
				$name = $config->get( 'mshop/post/index/manager/' . $domain . '/name' );
				$this->subManagers[$domain] = $this->getObject()->getSubManager( $domain, $name ?: 'MySQL' );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}
