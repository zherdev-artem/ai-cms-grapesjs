<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage PostIndex
 */


namespace Aimeos\MShop\PostIndex\Manager\Category;


/**
 * Submanager for category.
 *
 * @package MShop
 * @subpackage PostIndex
 */
class Standard
	extends \Aimeos\MShop\PostIndex\Manager\DBBase
	implements \Aimeos\MShop\PostIndex\Manager\Category\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'index.category.id' => array(
			'code' => 'index.category.id',
			'internalcode' => 'mpostindca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_post_index_category" AS mpostindca ON mpostindca."postid" = mpost."id"' ),
			'label' => 'Post index category ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'index.category:position' => array(
			'code' => 'index.category:position()',
			'internalcode' => ':site :catid :listtype mpostindca."pos"',
			'label' => 'Post position in category, parameter([<list type code>,[<category IDs>]])',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'sort:index.category:position' => array(
			'code' => 'sort:index.category:position()',
			'internalcode' => 'mpostindca."pos"',
			'label' => 'Sort post position in category, parameter([<list type code>,[<category IDs>]])',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
        ),
        'index.category:codes' => array(
			'code' => 'index.category:codes()',
			'internalcode' => '( SELECT mpost_codes."id" FROM mshop_product AS mpost_codes
				WHERE mpost."id" = mpost_codes."id" AND (
					SELECT COUNT(DISTINCT mindat_codes."code")
					FROM "mshop_post_index_category" AS mindat_codes
					WHERE mpost."id" = mindat_codes."postid" AND :site
					AND mindat_codes."code" IN ( $1 ) ) > 0
				)',
			'label' => 'Number of product categorys, parameter(<category Codes>)',
			'type' => 'null',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_NULL,
			'public' => false,
		),
	);

	private $subManagers;


	/**
	 * Initializes the manager instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/post/index/manager/sitemode', $level );

		$this->searchConfig['index.category:position']['function'] = function( &$source, array $params ) use ( $level ) {

			$source = str_replace( ':listtype', isset( $params[0] ) ? 'mpostindca."listtype" = $1 AND' : '', $source );
			$source = str_replace( ':catid', isset( $params[1] ) ? 'mpostindca."catid" IN ( $2 ) AND' : '', $source );
			$source = str_replace( ':site', $this->getSiteString( 'mpostindca."siteid"', $level ) . ' AND', $source );

			return $params;
		};

        $name = 'index.category:codes';
		$expr = $this->getSiteString( 'mindat_codes."siteid"', $level );
		$this->searchConfig[$name]['internalcode'] = str_replace( ':site', $expr, $this->searchConfig[$name]['internalcode'] );
	}


	/**
	 * Counts the number posts that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate posts for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of ID values as key and the number of counted posts as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		return $this->aggregateBase( $search, $key, 'mshop/post/index/manager/aggregate', [], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		parent::clear( $siteids );

		return $this->clearBase( $siteids, 'mshop/post/index/manager/category/delete' );
	}


	/**
	 * Removes all entries not touched after the given timestamp in the index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface Manager object for chaining method calls
	 */
	public function cleanup( string $timestamp ) : \Aimeos\MShop\PostIndex\Manager\Iface
	{
		/** mshop/post/index/manager/category/cleanup/mysql
		 * Deletes the index category records that haven't been touched
		 *
		 * @see mshop/post/index/manager/category/cleanup/ansi
		 */

		/** mshop/post/index/manager/category/cleanup/ansi
		 * Deletes the index category records that haven't been touched
		 *
		 * During the rebuild process of the post index, the entries of all
		 * active posts will be removed and readded. Thus, no stale data for
		 * these posts will remain in the database.
		 *
		 * All posts that have been disabled since the last rebuild will be
		 * still part of the index. The cleanup statement removes all records
		 * that belong to posts that haven't been touched during the index
		 * rebuild because these are the disabled ones.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting the outdated index records
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/post/index/manager/category/count/ansi
		 * @see mshop/post/index/manager/category/delete/ansi
		 * @see mshop/post/index/manager/category/insert/ansi
		 * @see mshop/post/index/manager/category/search/ansi
		 */
		return $this->cleanupBase( $timestamp, 'mshop/post/index/manager/category/cleanup' );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\Map|array|string $items List of item objects or IDs of the items
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/post/index/manager/category/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/post/index/manager/category/delete/ansi
		 */

		/** mshop/post/index/manager/category/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the index database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting index category records
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/post/index/manager/category/count/ansi
		 * @see mshop/post/index/manager/category/cleanup/ansi
		 * @see mshop/post/index/manager/category/insert/ansi
		 * @see mshop/post/index/manager/category/search/ansi
		 */
		return $this->deleteItemsBase( $items, 'mshop/post/index/manager/category/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/post/index/manager/category/submanagers';

		return $this->getResourceTypeBase( 'postindex/category', $path, [], $withsub );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		/** mshop/post/index/manager/category/submanagers
		 * List of manager names that can be instantiated by the index attribute manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'mshop/post/index/manager/category/submanagers';

		$list += $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );

		return $list;
	}


	/**
	 * Returns a new manager for post extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/post/index/manager/category/name
		 * Class name of the used index category manager implementation
		 *
		 * Each default index category manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\PostIndex\Manager\Category\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\PostIndex\Manager\Category\Mycategory
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/post/index/manager/category/name = Mycategory
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCategory"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/post/index/manager/category/decorators/excludes
		 * Excludes decorators added by the "common" option from the index category manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the index category manager.
		 *
		 *  mshop/post/index/manager/category/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the index category manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/post/index/manager/category/decorators/global
		 * @see mshop/post/index/manager/category/decorators/local
		 */

		/** mshop/post/index/manager/category/decorators/global
		 * Adds a list of globally available decorators only to the index category manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the index category
		 * manager.
		 *
		 *  mshop/post/index/manager/category/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the index
		 * category manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/post/index/manager/category/decorators/excludes
		 * @see mshop/post/index/manager/category/decorators/local
		 */

		/** mshop/post/index/manager/category/decorators/local
		 * Adds a list of local decorators only to the index category manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\PostIndex\Manager\Category\Decorator\*") around the index
		 * category manager.
		 *
		 *  mshop/post/index/manager/category/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\PostIndex\Manager\Category\Decorator\Decorator2" only to the
		 * index category manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/post/index/manager/category/decorators/excludes
		 * @see mshop/post/index/manager/category/decorators/global
		 */

		return $this->getSubManagerBase( 'postindex', 'category/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 *
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface Manager object for chaining method calls
	 */
	public function optimize() : \Aimeos\MShop\PostIndex\Manager\Iface
	{
		/** mshop/post/index/manager/category/optimize/mysql
		 * Optimizes the stored category data for retrieving the records faster
		 *
		 * @see mshop/post/index/manager/category/optimize/ansi
		 */

		/** mshop/post/index/manager/category/optimize/ansi
		 * Optimizes the stored category data for retrieving the records faster
		 *
		 * The SQL statement should reorganize the data in the DBMS storage to
		 * optimize access to the records of the table or tables. Some DBMS
		 * offer specialized statements to optimize indexes and records. This
		 * statement doesn't return any records.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for optimizing the stored category data
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/post/index/manager/category/count/ansi
		 * @see mshop/post/index/manager/category/search/ansi
		 * @see mshop/post/index/manager/category/aggregate/ansi
		 */
		return $this->optimizeBase( 'mshop/post/index/manager/category/optimize' );
	}


	/**
	 * Rebuilds the index category for searching posts or specified list of posts.
	 * This can be a long lasting operation.
	 *
	 * @param \Aimeos\MShop\Post\Item\Iface[] $items Associative list of post IDs as keys and items as values
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface Manager object for chaining method calls
	 */
	public function rebuild( iterable $items = [] ) : \Aimeos\MShop\PostIndex\Manager\Iface
	{
		if( map( $items )->isEmpty() ) { return $this; }

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Post\Item\Iface::class, $items );

		$date = date( 'Y-m-d H:i:s' );
		$context = $this->getContext();
		$siteid = $context->getLocale()->getSiteId();
		$listItems = $this->getListItems( $items );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/post/index/manager/category/insert/mysql
			 * Inserts a new category record into the post index database
			 *
			 * @see mshop/post/index/manager/category/insert/ansi
			 */

			/** mshop/post/index/manager/category/insert/ansi
			 * Inserts a new category record into the post index database
			 *
			 * During the post index rebuild, categories related to a
			 * post will be stored in the index for this post. All
			 * records are deleted before the new ones are inserted.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the order item to the statement before they are
			 * sent to the database server. The number of question marks must
			 * be the same as the number of columns listed in the INSERT
			 * statement. The order of the columns must correspond to the
			 * order in the rebuild() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/post/index/manager/category/cleanup/ansi
			 * @see mshop/post/index/manager/category/delete/ansi
			 * @see mshop/post/index/manager/category/search/ansi
			 * @see mshop/post/index/manager/category/count/ansi
			 */
			$stmt = $this->getCachedStatement( $conn, 'mshop/post/index/manager/category/insert' );

			foreach( $items as $id => $item )
			{
                $categoryCode = \Aimeos\Map::from($item->get('.category'))->first()->getCode() ?? '';

				if( !$listItems->has( $id ) ) { continue; }

				foreach( (array) $listItems[$id] as $listItem )
				{
					$stmt->bind( 1, $listItem->getRefId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, $listItem->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, $listItem->getType() );
					$stmt->bind( 4, $listItem->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 5, $date ); //mtime
					$stmt->bind( 6, $siteid );
                    $stmt->bind( 7, $categoryCode );

					try {
						$stmt->execute()->finish();
					} catch( \Aimeos\MW\DB\Exception $e ) { ; } // Ignore duplicates
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		foreach( $this->getSubManagers() as $submanager ) {
			$submanager->rebuild( $items );
		}

		return $this;
	}


	/**
	 * Removes the posts from the post index.
	 *
	 * @param array|string $ids Post ID or list of IDs
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface Manager object for chaining method calls
	 */
	public function remove( $ids ) : \Aimeos\MShop\PostIndex\Manager\Iface
	{
		parent::remove( $ids )->delete( $ids );
		return $this;
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Post\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		/** mshop/post/index/manager/category/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/post/index/manager/category/search/ansi
		 */

		/** mshop/post/index/manager/category/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the post index
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the SELECT statement can retrieve all records
		 * from the current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * If the records that are retrieved should be ordered by one or more
		 * columns, the generated string of column / sort direction pairs
		 * replaces the ":order" placeholder. In case no ordering is required,
		 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
		 * markers is removed to speed up retrieving the records. Columns of
		 * sub-managers can also be used for ordering the result set but then
		 * no index can be used.
		 *
		 * The number of returned records can be limited and can start at any
		 * number between the begining and the end of the result set. For that
		 * the ":size" and ":start" placeholders are replaced by the
		 * corresponding values from the criteria object. The default values
		 * are 0 for the start and 100 for the size value.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for searching items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/post/index/manager/category/count/ansi
		 * @see mshop/post/index/manager/category/optimize/ansi
		 * @see mshop/post/index/manager/category/aggregate/ansi
		 */
		$cfgPathSearch = 'mshop/post/index/manager/category/search';

		/** mshop/post/index/manager/category/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/post/index/manager/category/count/ansi
		 */

		/** mshop/post/index/manager/category/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the post index
		 * database. The records must be from one of the sites that are
		 * configured via the context item. If the current site is part of
		 * a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * Both, the strings for ":joins" and for ":cond" are the same as for
		 * the "search" SQL statement.
		 *
		 * Contrary to the "search" statement, it doesn't return any records
		 * but instead the number of records that have been found. As counting
		 * thousands of records can be a long running task, the maximum number
		 * of counted records is limited for performance reasons.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for counting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/post/index/manager/category/search/ansi
		 * @see mshop/post/index/manager/category/optimize/ansi
		 * @see mshop/post/index/manager/category/aggregate/ansi
		 */
		$cfgPathCount = 'mshop/post/index/manager/category/count';

		return $this->searchItemsPostIndexBase( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Returns the list items referencing the given posts
	 *
	 * @param \Aimeos\MShop\Post\Item\Iface[] $items List of post items
	 * @return \Aimeos\Map Associative list of post IDs as keys and lists of list items as values
	 */
	protected function getListItems( iterable $items ) : \Aimeos\Map
	{
		$listItems = [];
		$listManager = \Aimeos\MShop::create( $this->getContext(), 'category/lists' );

		$search = $listManager->filter( true )->slice( 0, 0x7fffffff )->add( [
			'category.lists.refid' => map( $items )->keys()->toArray(),
			'category.lists.domain' => 'post'
		] );

		foreach( $listManager->search( $search ) as $listItem ) {
			$listItems[$listItem->getRefId()][] = $listItem;
		}

		return map( $listItems );
	}


	/**
	 * Returns the list of sub-managers available for the index category manager.
	 *
	 * @return \Aimeos\MShop\PostIndex\Manager\Iface[] Associative list of the sub-domain as key and the manager object as value
	 */
	protected function getSubManagers() : array
	{
		if( $this->subManagers === null )
		{
			$this->subManagers = [];
			$config = $this->getContext()->getConfig();

			/** mshop/post/index/manager/category/submanagers
			 * A list of sub-manager names used for indexing associated items to categories
			 *
			 * All items referenced by a post (e.g. texts, prices, media,
			 * etc.) are added to the post index via specialized index
			 * managers. You can add the name of new sub-managers to add more
			 * data to the index or remove existing ones if you don't want to
			 * index that data at all.
			 *
			 * This option configures the sub-managers that cares about
			 * indexing data associated to post categories.
			 *
			 * @param string List of index sub-manager names
			 * @since 2014.09
			 * @category User
			 * @category Developer
			 * @see mshop/post/index/manager/submanagers
			 */
			foreach( $config->get( 'mshop/post/index/manager/category/submanagers', [] ) as $domain )
			{
				$name = $config->get( 'mshop/post/index/manager/category/' . $domain . '/name' );
				$this->subManagers[$domain] = $this->getObject()->getSubManager( $domain, $name );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}
