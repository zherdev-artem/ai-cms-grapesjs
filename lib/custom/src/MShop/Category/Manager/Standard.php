<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Category
 */


namespace Aimeos\MShop\Category\Manager;


/**
 * Category manager with methods for managing categories products, text, media.
 *
 * @package MShop
 * @subpackage Category
 */
class Standard extends Base
	implements \Aimeos\MShop\Category\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'id' => array(
			'code' => 'category.id',
			'internalcode' => 'mcmscat."id"',
			'label' => 'ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'category.siteid' => array(
			'code' => 'category.siteid',
			'internalcode' => 'mcmscat."siteid"',
			'label' => 'Site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'parentid' => array(
			'code' => 'category.parentid',
			'internalcode' => 'mcmscat."parentid"',
			'label' => 'Parent ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'level' => array(
			'code' => 'category.level',
			'internalcode' => 'mcmscat."level"',
			'label' => 'Tree level',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'left' => array(
			'code' => 'category.left',
			'internalcode' => 'mcmscat."nleft"',
			'label' => 'Left value',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'right' => array(
			'code' => 'category.right',
			'internalcode' => 'mcmscat."nright"',
			'label' => 'Right value',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'label' => array(
			'code' => 'category.label',
			'internalcode' => 'mcmscat."label"',
			'label' => 'Label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'code' => array(
			'code' => 'category.code',
			'internalcode' => 'mcmscat."code"',
			'label' => 'Code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'status' => array(
			'code' => 'category.status',
			'internalcode' => 'mcmscat."status"',
			'label' => 'Status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'category.url' => array(
			'code' => 'category.url',
			'internalcode' => 'mcmscat."url"',
			'label' => 'URL segment',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'category.target' => array(
			'code' => 'category.target',
			'internalcode' => 'mcmscat."target"',
			'label' => 'URL target',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'category.config' => array(
			'code' => 'category.config',
			'internalcode' => 'mcmscat."config"',
			'label' => 'Config',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'category.ctime' => array(
			'label' => 'Create date/time',
			'code' => 'category.ctime',
			'internalcode' => 'mcmscat."ctime"',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'category.mtime' => array(
			'label' => 'Modify date/time',
			'code' => 'category.mtime',
			'internalcode' => 'mcmscat."mtime"',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'category.editor' => array(
			'code' => 'category.editor',
			'internalcode' => 'mcmscat."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'category:has' => array(
			'code' => 'category:has()',
			'internalcode' => ':site AND :key AND mcmscatli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_category_list" AS mcmscatli ON ( mcmscatli."parentid" = mcmscat."id" )'],
			'label' => 'Category has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
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
		parent::__construct( $context, $this->searchConfig );
		$this->setResourceName( 'db-category' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/category/manager/sitemode', $level );


		$this->searchConfig['category:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->getSiteString( 'mcmscatli."siteid"', $level );
			$keystr = $this->toExpression( 'mcmscatli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Category\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$search = $this->getObject()->filter();

		foreach( $config->get( 'mshop/category/manager/submanagers', ['lists'] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/category/manager/cleanup/mysql
			 * Deletes the categories for the given site from the database
			 *
			 * @see mshop/category/manager/cleanup/ansi
			 */

			/** mshop/category/manager/cleanup/ansi
			 * Deletes the categories for the given site from the database
			 *
			 * Removes the records matched by the given site ID from the category
			 * database.
			 *
			 * The ":siteid" placeholder is replaced by the name and value of the
			 * site ID column and the given ID or list of IDs.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for removing the records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/category/manager/delete/ansi
			 * @see mshop/category/manager/insert/ansi
			 * @see mshop/category/manager/update/ansi
			 * @see mshop/category/manager/newid/ansi
			 * @see mshop/category/manager/search/ansi
			 * @see mshop/category/manager/count/ansi
			 */
			$path = 'mshop/category/manager/cleanup';
			$sql = $this->getSqlConfig( $path );

			$types = array( 'siteid' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$translations = array( 'siteid' => '"siteid"' );

			$search->setConditions( $search->compare( '==', 'siteid', $siteids ) );
			$sql = str_replace( ':siteid', $search->getConditionSource( $types, $translations ), $sql );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, 0, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, 0x7FFFFFFF, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Category\Item\Iface New category item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
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
			return $this->filterBase( 'category' );
		}

		return parent::filter();
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|array|string $items List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Category\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( is_map( $items ) ) { $items = $items->toArray(); }
		if( !is_array( $items ) ) { $items = [$items]; }
		if( empty( $items ) ) { return $this; }

		$this->begin();
		$this->lock();

		try
		{
			$siteid = $this->getContext()->getLocale()->getSiteId();

			foreach( $items as $item ) {
				$this->createTreeManager( $siteid )->deleteNode( (string) $item );
			}

			$this->unlock();
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->unlock();
			$this->rollback();
			throw $e;
		}

		return $this->deleteRefItems( $items );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool $default True to add default criteria
	 * @return \Aimeos\MShop\Category\Item\Iface Category item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'category.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the item specified by its ID.
	 *
	 * @param string $id Unique ID of the category item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Category\Item\Iface Category item of the given ID
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'category.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/category/manager/submanagers';
		return $this->getResourceTypeBase( 'category', $path, array( 'lists' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/category/manager/submanagers
		 * List of manager names that can be instantiated by the category manager
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
		$path = 'mshop/category/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Category\Item\Iface $item Item which should be inserted
	 * @param string|null $parentId ID of the parent item where the item should be inserted into
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Category\Item\Iface $item Updated item including the generated ID
	 */
	public function insert( \Aimeos\MShop\Category\Item\Iface $item, string $parentId = null,
		string $refId = null ) : \Aimeos\MShop\Category\Item\Iface
	{
		$this->begin();
		$this->lock();

		try
		{
			$node = $item->getNode();
			$siteid = $this->getContext()->getLocale()->getSiteId();

			$this->createTreeManager( $siteid )->insertNode( $node, $parentId, $refId );
			$this->updateUsage( $node->getId(), $item, true );
			$this->unlock();
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->unlock();
			$this->rollback();
			throw $e;
		}

		$item = $this->saveListItems( $item, 'category' );
		return $this->saveChildren( $item );
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param string $id ID of the item that should be moved
	 * @param string|null $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param string|null $newParentId ID of the new parent item where the item should be moved to
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Category\Manager\Iface Manager object for chaining method calls
	 */
	public function move( string $id, string $oldParentId = null, string $newParentId = null,
		string $refId = null ) : \Aimeos\MShop\Category\Manager\Iface
	{
		$this->begin();
		$this->lock();

		try
		{
			$item = $this->getObject()->get( $id );
			$siteid = $this->getContext()->getLocale()->getSiteId();

			$this->createTreeManager( $siteid )->moveNode( $id, $oldParentId, $newParentId, $refId );
			$this->updateUsage( $id, $item );
			$this->unlock();
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->unlock();
			$this->rollback();
			throw $e;
		}

		return $this;
	}


	/**
	 * Updates an item object.
	 *
	 * @param \Aimeos\MShop\Category\Item\Iface $item Item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Category\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Category\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Category\Item\Iface
	{
		if( !$item->isModified() )
		{
			$item = $this->saveListItems( $item, 'category', $fetch );
			return $this->saveChildren( $item );
		}

		$node = $item->getNode();
		$siteid = $this->getContext()->getLocale()->getSiteId();

		$this->createTreeManager( $siteid )->saveNode( $node );
		$this->updateUsage( $node->getId(), $item );

		$item = $this->saveListItems( $item, 'category', $fetch );
		return $this->saveChildren( $item );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Category\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$nodeMap = $siteMap = [];
		$context = $this->getContext();

		$dbname = $this->getResourceName();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'category' );

			/** mshop/category/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole category domain if items from parent sites are inherited,
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
			 * @since 2018.01
			 * @see mshop/locale/manager/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;
			$level = $context->getConfig()->get( 'mshop/category/manager/sitemode', $level );

			/** mshop/category/manager/search-item/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/category/manager/search-item/ansi
			 */

			/** mshop/category/manager/search-item/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the category
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
			 * @see mshop/category/manager/delete/ansi
			 * @see mshop/category/manager/get/ansi
			 * @see mshop/category/manager/insert/ansi
			 * @see mshop/category/manager/update/ansi
			 * @see mshop/category/manager/newid/ansi
			 * @see mshop/category/manager/search/ansi
			 * @see mshop/category/manager/count/ansi
			 * @see mshop/category/manager/move-left/ansi
			 * @see mshop/category/manager/move-right/ansi
			 * @see mshop/category/manager/update-parentid/ansi
			 */
			$cfgPathSearch = 'mshop/category/manager/search-item';

			/** mshop/category/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/category/manager/count/ansi
			 */

			/** mshop/category/manager/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the category
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
			 * @see mshop/category/manager/delete/ansi
			 * @see mshop/category/manager/get/ansi
			 * @see mshop/category/manager/insert/ansi
			 * @see mshop/category/manager/update/ansi
			 * @see mshop/category/manager/newid/ansi
			 * @see mshop/category/manager/search/ansi
			 * @see mshop/category/manager/search-item/ansi
			 * @see mshop/category/manager/move-left/ansi
			 * @see mshop/category/manager/move-right/ansi
			 * @see mshop/category/manager/update-parentid/ansi
			 */
			$cfgPathCount = 'mshop/category/manager/count';

			if( $search->getSortations() === [] ) {
				$search->setSortations( [$search->sort( '+', 'category.left' )] );
			}

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null ) {
				$siteMap[(string) $row['siteid']][(string) $row['id']] = new \Aimeos\MW\Tree\Node\Standard( $row );
			}

			$sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

			foreach( $sitePath as $siteId )
			{
				if( isset( $siteMap[$siteId] ) && !empty( $siteMap[$siteId] ) )
				{
					$nodeMap = $siteMap[$siteId];
					break;
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this->buildItems( $nodeMap, $ref, 'category' );
	}


	/**
	 * Returns a list of items starting with the given category that are in the path to the root node
	 *
	 * @param string $id ID of item to get the path for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\Map Associative list of category items implementing \Aimeos\MShop\Category\Item\Iface with IDs as keys
	 */
	public function getPath( string $id, array $ref = [] ) : \Aimeos\Map
	{
		$sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

		foreach( $sitePath as $siteId )
		{
			try {
				$path = $this->createTreeManager( $siteId )->getPath( $id );
			} catch( \Exception $e ) {
				continue;
			}

			if( !empty( $path ) )
			{
				$itemMap = [];

				foreach( $path as $node ) {
					$itemMap[$node->getId()] = $node;
				}

				return $this->buildItems( $itemMap, $ref, 'category' );
			}
		}

		throw new \Aimeos\MShop\Category\Exception( sprintf( 'Category path for ID "%1$s" not found', $id ) );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param string[] List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MShop\Category\Item\Iface Category item, maybe with subnodes
	 */
	public function getTree( string $id = null, array $ref = [], int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE,
		\Aimeos\MW\Criteria\Iface $criteria = null ) : \Aimeos\MShop\Category\Item\Iface
	{
		$sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

		foreach( $sitePath as $siteId )
		{
			try {
				$node = $this->createTreeManager( $siteId )->getNode( $id, $level, $criteria );
			} catch( \Aimeos\MW\Tree\Exception $e ) {
				continue;
			}

			$listItems = $listItemMap = $refIdMap = [];
			$nodeMap = $this->getNodeMap( $node );

			if( count( $ref ) > 0 ) {
				$listItems = $this->getListItems( array_keys( $nodeMap ), $ref, 'category' );
			}

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->getRefItems( $refIdMap, $ref );
			$nodeid = $node->getId();

			$listItems = [];
			if( array_key_exists( $nodeid, $listItemMap ) ) {
				$listItems = $listItemMap[$nodeid];
			}

			$refItems = [];
			if( array_key_exists( $nodeid, $refItemMap ) ) {
				$refItems = $refItemMap[$nodeid];
			}

			if( $item = $this->applyFilter( $this->createItemBase( [], $listItems, $refItems, [], $node ) ) )
			{
				$this->createTree( $node, $item, $listItemMap, $refItemMap );
				return $item;
			}
		}

		throw new \Aimeos\MShop\Category\Exception( sprintf( 'No category node for ID "%1$s"', $id ) );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $manager Name of the sub manager type
	 * @param string|null $name Name of the implementation, will be from configuration (or Default)
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'category', $manager, $name );
	}


	/**
	 * Saves the children of the given node
	 *
	 * @param \Aimeos\MShop\Category\Item\Iface $item Category item object incl. child items
	 * @return \Aimeos\MShop\Category\Item\Iface Category item with saved child items
	 */
	protected function saveChildren( \Aimeos\MShop\Category\Item\Iface $item ) : \Aimeos\MShop\Category\Item\Iface
	{
		$rmIds = [];
		foreach( $item->getChildrenDeleted() as $child ) {
			$rmIds[] = $child->getId();
		}

		$this->delete( $rmIds );

		foreach( $item->getChildren() as $child )
		{
			if( $child->getId() !== null )
			{
				$this->save( $child );

				if( $child->getParentId() !== $item->getParentId() ) {
					$this->move( $child->getId(), $item->getParentId(), $child->getParentId() );
				}
			}
			else
			{
				$this->insert( $child, $item->getId() );
			}
		}

		return $item;
	}


	/**
	 * Locks the category table against modifications from other connections
	 *
	 * @return \Aimeos\MShop\Category\Manager\Iface Manager object for chaining method calls
	 */
	protected function lock() : \Aimeos\MShop\Category\Manager\Iface
	{
		/** mshop/category/manager/lock/mysql
		 * SQL statement for locking the category table
		 *
		 * @see mshop/category/manager/lock/ansi
		 */

		/** mshop/category/manager/lock/ansi
		 * SQL statement for locking the category table
		 *
		 * Updating the nested set of categories in the category table requires locking
		 * the whole table to avoid data corruption. This statement will be followed by
		 * insert or update statements and closed by an unlock statement.
		 *
		 * @param string Lock SQL statement
		 * @since 2019.04
		 * @category Developer
		 */
		$path = 'mshop/category/manager/lock';

		if( ( $sql = $this->getSqlConfig( $path ) ) !== $path )
		{
			$dbname = $this->getResourceName();
			$dbm = $this->getContext()->getDatabaseManager();

			$conn = $dbm->acquire( $dbname );
			$conn->create( $sql )->execute()->finish();
			$dbm->release( $conn, $dbname );
		}

		return $this;
	}


	/**
	 * Unlocks the category table for modifications from other connections
	 *
	 * @return \Aimeos\MShop\Category\Manager\Iface Manager object for chaining method calls
	 */
	protected function unlock() : \Aimeos\MShop\Category\Manager\Iface
	{
		/** mshop/category/manager/unlock/mysql
		 * SQL statement for unlocking the category table
		 *
		 * @see mshop/category/manager/unlock/ansi
		 */

		/** mshop/category/manager/unlock/ansi
		 * SQL statement for unlocking the category table
		 *
		 * Updating the nested set of categories in the category table requires locking
		 * the whole table to avoid data corruption. This statement will be executed
		 * after the table is locked and insert or update statements have been sent to
		 * the database.
		 *
		 * @param string Lock SQL statement
		 * @since 2019.04
		 * @category Developer
		 */
		 $path = 'mshop/category/manager/unlock';

		if( ( $sql = $this->getSqlConfig( $path ) ) !== $path )
		{
			$dbname = $this->getResourceName();
			$dbm = $this->getContext()->getDatabaseManager();

			$conn = $dbm->acquire( $dbname );
			$conn->create( $sql )->execute()->finish();
			$dbm->release( $conn, $dbname );
		}

		return $this;
	}


	/**
	 * Updates the usage information of a node.
	 *
	 * @param string $id Id of the record
	 * @param \Aimeos\MShop\Category\Item\Iface $item Category item
	 * @param bool $case True if the record shoud be added or false for an update
	 * @return \Aimeos\MShop\Category\Manager\Iface Manager object for chaining method calls
	 */
	private function updateUsage( string $id, \Aimeos\MShop\Category\Item\Iface $item,
		bool $case = false ) : \Aimeos\MShop\Category\Manager\Iface
	{
		$date = date( 'Y-m-d H:i:s' );
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$siteid = $context->getLocale()->getSiteId();
			$columns = $this->getObject()->getSaveAttributes();

			if( $case !== true )
			{
				/** mshop/category/manager/update-usage/mysql
				 * Updates the config, editor and mtime value of an updated record
				 *
				 * @see mshop/category/manager/update-usage/ansi
				 */

				/** mshop/category/manager/update-usage/ansi
				 * Updates the config, editor and mtime value of an updated record
				 *
				 * Each record contains some usage information like when it was
				 * created, last modified and by whom. These information are part
				 * of the category items and the generic tree manager doesn't care
				 * about this information. Thus, they are updated after the tree
				 * manager saved the basic record information.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the category item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the method using this statement,
				 * so the correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/category/manager/delete/ansi
				 * @see mshop/category/manager/get/ansi
				 * @see mshop/category/manager/insert/ansi
				 * @see mshop/category/manager/newid/ansi
				 * @see mshop/category/manager/search/ansi
				 * @see mshop/category/manager/search-item/ansi
				 * @see mshop/category/manager/count/ansi
				 * @see mshop/category/manager/move-left/ansi
				 * @see mshop/category/manager/move-right/ansi
				 * @see mshop/category/manager/update-parentid/ansi
				 * @see mshop/category/manager/insert-usage/ansi
				 */
				$path = 'mshop/category/manager/update-usage';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}
			else
			{
				/** mshop/category/manager/insert-usage/mysql
				 * Updates the config, editor, ctime and mtime value of an inserted record
				 *
				 * @see mshop/category/manager/insert-usage/ansi
				 */

				/** mshop/category/manager/insert-usage/ansi
				 * Updates the config, editor, ctime and mtime value of an inserted record
				 *
				 * Each record contains some usage information like when it was
				 * created, last modified and by whom. These information are part
				 * of the category items and the generic tree manager doesn't care
				 * about this information. Thus, they are updated after the tree
				 * manager inserted the basic record information.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the category item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the method using this statement,
				 * so the correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/category/manager/delete/ansi
				 * @see mshop/category/manager/get/ansi
				 * @see mshop/category/manager/insert/ansi
				 * @see mshop/category/manager/newid/ansi
				 * @see mshop/category/manager/search/ansi
				 * @see mshop/category/manager/search-item/ansi
				 * @see mshop/category/manager/count/ansi
				 * @see mshop/category/manager/move-left/ansi
				 * @see mshop/category/manager/move-right/ansi
				 * @see mshop/category/manager/update-parentid/ansi
				 * @see mshop/category/manager/update-usage/ansi
				 */
				$path = 'mshop/category/manager/insert-usage';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getUrl() );
			$stmt->bind( $idx++, json_encode( $item->getConfig() ) );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $item->getTarget() );

			if( $case !== true )
			{
				$stmt->bind( $idx++, $siteid );
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}
			else
			{
				$stmt->bind( $idx++, $date ); // ctime
				$stmt->bind( $idx++, $siteid );
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this;
	}
}
