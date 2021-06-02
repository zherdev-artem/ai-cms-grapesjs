<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all platform specific tables
 */
class TablesCreatePostPlatform extends TablesCreateMShop
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMAdmin', 'TablesCreatePostIndex'];
	}


	/**
	 * Removes old columns and sequences
	 */
	public function clean()
	{
	}


	/**
	 * Creates the platform specific schema
	 */
	public function migrate()
	{
		$this->msg( 'Creating platform specific post idex schema', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$this->setupPlatform( 'db-post', 'mysql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'post-index-mysql.sql' );
		$this->setupPlatform( 'db-post', 'pgsql', realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'post-index-pgsql.sql' );
    }


	/**
	 * Creates all required tables if they doesn't exist
	 */
	protected function setupPlatform( $rname, $adapter, $filepath )
	{
		$schema = $this->getSchema( $rname );

		if( $adapter !== $schema->getName() ) {
			return;
		}

		$this->setup( array( $rname => $filepath ) );
	}
}
