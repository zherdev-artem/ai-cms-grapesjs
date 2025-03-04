<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Post
 */


namespace Aimeos\MShop\Post\Manager;


/**
 * Default post manager implementation
 *
 * @package MShop
 * @subpackage Post
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Post\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;


	private $searchConfig = array(
		'post.id' => array(
			'code' => 'post.id',
			'internalcode' => 'mpost."id"',
			'label' => 'ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'post.siteid' => array(
			'code' => 'post.siteid',
			'internalcode' => 'mpost."siteid"',
			'label' => 'Site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'post.url' => array(
			'code' => 'post.url',
			'internalcode' => 'mpost."url"',
			'label' => 'Type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'post.label' => array(
			'code' => 'post.label',
			'internalcode' => 'mpost."label"',
			'label' => 'Label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'post.status' => array(
			'code' => 'post.status',
			'internalcode' => 'mpost."status"',
			'label' => 'Status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'post.ctime' => array(
			'code' => 'post.ctime',
			'internalcode' => 'mpost."ctime"',
			'label' => 'create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'post.mtime' => array(
			'code' => 'post.mtime',
			'internalcode' => 'mpost."mtime"',
			'label' => 'modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'post.editor' => array(
			'code' => 'post.editor',
			'internalcode' => 'mpost."editor"',
			'label' => 'editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'post:has' => array(
			'code' => 'post:has()',
			'internalcode' => ':site AND :key AND mpostli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_post_list" AS mpostli ON ( mpostli."parentid" = mpost."id" )'],
			'label' => 'Post has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->setResourceName( 'db-post' );
		$this->languageId = $context->getLocale()->getLanguageId();

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;
		$level = $context->getConfig()->get( 'mshop/post/manager/sitemode', $level );


		$this->searchConfig['post:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->getSiteString( 'mpostli."siteid"', $level );
			$keystr = $this->toExpression( 'mpostli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Post\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/post/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, ['lists'] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/post/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Post\Item\Iface New post item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['post.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Updates or adds a post item object.
	 * This method doesn't update the type string that belongs to the type ID
	 *
	 * @param \Aimeos\MShop\Post\Item\Iface $item Post item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Post\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Post\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Post\Item\Iface
	{
		if( !$item->isModified() ) {
			return $this->saveListItems( $item, 'post', $fetch );
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null )
			{
				/** mshop/post/manager/insert/mysql
				 * Inserts a new post record into the database table
				 *
				 * @see mshop/post/manager/insert/ansi
				 */

				/** mshop/post/manager/insert/ansi
				 * Inserts a new post record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the post item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the save() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2020.10
				 * @category Developer
				 * @see mshop/post/manager/update/ansi
				 * @see mshop/post/manager/newid/ansi
				 * @see mshop/post/manager/delete/ansi
				 * @see mshop/post/manager/search/ansi
				 * @see mshop/post/manager/count/ansi
				 */
				$path = 'mshop/post/manager/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/post/manager/update/mysql
				 * Updates an existing post record in the database
				 *
				 * @see mshop/post/manager/update/ansi
				 */

				/** mshop/post/manager/update/ansi
				 * Updates an existing post record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the post item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the save() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2020.10
				 * @category Developer
				 * @see mshop/post/manager/insert/ansi
				 * @see mshop/post/manager/newid/ansi
				 * @see mshop/post/manager/delete/ansi
				 * @see mshop/post/manager/search/ansi
				 * @see mshop/post/manager/count/ansi
				 */
				$path = 'mshop/post/manager/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getUrl() );
			$stmt->bind( $idx++, $item->getLabel() );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null )
			{
				/** mshop/post/manager/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/post/manager/newid/ansi
				 */

				/** mshop/post/manager/newid/ansi
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * As soon as a new record is inserted into the database table,
				 * the database server generates a new and unique identifier for
				 * that record. This ID can be used for retrieving, updating and
				 * deleting that specific record from the table again.
				 *
				 * For MySQL:
				 *  SELECT LAST_INSERT_ID()
				 * For PostgreSQL:
				 *  SELECT currval('seq_mpost_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mpost_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2020.10
				 * @category Developer
				 * @see mshop/post/manager/insert/ansi
				 * @see mshop/post/manager/update/ansi
				 * @see mshop/post/manager/delete/ansi
				 * @see mshop/post/manager/search/ansi
				 * @see mshop/post/manager/count/ansi
				 */
				$path = 'mshop/post/manager/newid';
				$id = $this->newId( $conn, $path );
			}

			$item->setId( $id );

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this->saveListItems( $item, 'post', $fetch );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Post\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/post/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/post/manager/delete/ansi
		 */

		/** mshop/post/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the post database.
		 * The records must be from the site that is configured via the
		 * conpost item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2020.10
		 * @category Developer
		 * @see mshop/post/manager/insert/ansi
		 * @see mshop/post/manager/update/ansi
		 * @see mshop/post/manager/newid/ansi
		 * @see mshop/post/manager/search/ansi
		 * @see mshop/post/manager/count/ansi
		 */
		$path = 'mshop/post/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path )->deleteRefItems( $itemIds );
	}


	/**
	 * Returns the item specified by its URL
	 *
	 * @param string $code URL of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'post.url' => $code ), $ref, $default );
	}


	/**
	 * Returns the post item object specified by the given ID.
	 *
	 * @param string $id Id of the post item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Post\Item\Iface Returns the post item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'post.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/post/manager/submanagers';
		return $this->getResourceTypeBase( 'post', $path, ['lists'], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/post/manager/submanagers
		 * List of manager names that can be instantiated by the post manager
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
		 * @since 2020.10
		 * @category Developer
		 */
		$path = 'mshop/post/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Searches for all post items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Post\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'post' );

			/** mshop/post/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole post domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2020.10
			 * @see mshop/locale/manager/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;
			$level = $context->getConfig()->get( 'mshop/post/manager/sitemode', $level );

			/** mshop/post/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/post/manager/search/ansi
			 */

			/** mshop/post/manager/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the post
			 * database. The records must be from one of the sites that are
			 * configured via the conpost item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL conpost. This is done by
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
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/post/manager/insert/ansi
			 * @see mshop/post/manager/update/ansi
			 * @see mshop/post/manager/newid/ansi
			 * @see mshop/post/manager/delete/ansi
			 * @see mshop/post/manager/count/ansi
			 */
			$cfgPathSearch = 'mshop/post/manager/search';

			/** mshop/post/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/post/manager/count/ansi
			 */

			/** mshop/post/manager/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the post
			 * database. The records must be from one of the sites that are
			 * configured via the conpost item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
			 * current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL conpost. This is done by
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
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/post/manager/insert/ansi
			 * @see mshop/post/manager/update/ansi
			 * @see mshop/post/manager/newid/ansi
			 * @see mshop/post/manager/delete/ansi
			 * @see mshop/post/manager/search/ansi
			 */
			$cfgPathCount = 'mshop/post/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null ) {
				$map[$row['post.id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

        if( isset( $ref['category'] ) || in_array( 'category', $ref, true ) )
		{
			$domains = isset( $ref['category'] ) && is_array( $ref['category'] ) ? $ref['category'] : [];

			foreach( $this->getDomainRefItems( array_keys( $map ), 'category', $domains ) as $postId => $list ) {
				$map[$postId]['.category'] = $list;
			}
		}

		return $this->buildItems( $map, $ref, 'post' );
	}


	/**
	 * Returns a new manager for post extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'post', $manager, $name );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool $default Add default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\MW\Criteria\Iface Returns the filter object
	 */
	public function filter( bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
	{
		if( $default === true ) {
			return $this->filterBase( 'post' );
		}

		return parent::filter();
	}


	/**
	 * Creates a new post item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface $refItems List of referenced items
	 * @return \Aimeos\MShop\Post\Item\Iface New post item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return new \Aimeos\MShop\Post\Item\Standard( $values, $listItems, $refItems );
	}

    /**
	 * Returns the associative list of domain items referencing the post
	 *
	 * @param array $ids List of post IDs
	 * @param string $domain Domain name, e.g. "catalog" or "supplier"
	 * @param array $ref List of referenced items that should be fetched too
	 * @return array Associative list of post IDs as keys and list of domain items as values
	 */
	protected function getDomainRefItems( array $ids, string $domain, array $ref ) : array
	{
		$keys = $map = $result = [];
		$context = $this->getContext();

		foreach( $ids as $id ) {
			$keys[] = 'post|default|' . $id;
		}


		$manager = \Aimeos\MShop::create( $context, $domain . '/lists' );

		$search = $manager->filter( true )->slice( 0, 0x7fffffff );
		$search->setConditions( $search->and( [
			$search->compare( '==', $domain . '.lists.key', $keys ),
			$search->getConditions(),
		] ) );

		foreach( $manager->search( $search ) as $listItem ) {
			$map[$listItem->getParentId()][] = $listItem->getRefId();
		}

		$manager = \Aimeos\MShop::create( $context, $domain );

		$search = $manager->filter( true )->slice( 0, 0x7fffffff );
		$search->setConditions( $search->and( [
			$search->compare( '==', $domain . '.id', array_keys( $map ) ),
			$search->getConditions(),
		] ) );

		$items = $manager->search( $search, $ref );


		foreach( $map as $parentId => $list )
		{
			if( isset( $items[$parentId] ) )
			{
				foreach( $list as $сhildId ) {
					$result[$сhildId][$parentId] = $items[$parentId];
				}
			}
		}

		return $result;
	}
}
