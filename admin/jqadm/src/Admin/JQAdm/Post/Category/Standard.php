<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Post\Category;

sprintf( 'category' ); // for translation


/**
 * Default implementation of post category JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/** admin/jqadm/post/category/name
	 * Name of the category subpart used by the JQAdm post implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Admin\Jqadm\Post\Category\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the JQAdm class name
	 * @since 2016.04
	 * @category Developer
	 */


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output
	 */
	public function copy() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );

		$view->categoryData = $this->toArray( $view->item, true );
		$view->categoryBody = parent::copy();

		return $this->render( $view );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string|null HTML output
	 */
	public function create() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );
		$siteid = $this->getContext()->getLocale()->getSiteId();
		$data = $view->param( 'category', [] );

		foreach( $view->value( $data, 'category.lists.id', [] ) as $idx => $value ) {
			$data['category.lists.siteid'][$idx] = $siteid;
		}

		$view->categoryData = $data;
		$view->categoryBody = parent::create();

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output
	 */
	public function delete() : ?string
	{
		parent::delete();
		$view = $this->getView();

		$manager = \Aimeos\MShop::create( $this->getContext(), 'category/lists' );

		$search = $manager->filter();
		$expr = array(
			$search->compare( '==', 'category.lists.refid', $view->param( 'id' ) ),
			$search->compare( '==', 'category.lists.domain', 'post' )
		);
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 0x7fffffff );

		$start = 0;

		do
		{
			$search->slice( $start );

			$result = $manager->search( $search );
			$manager->delete( $result->toArray() );

			$count = count( $result );
			$start += $count;
		}
		while( $count >= $search->getLimit() );

		return null;
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output
	 */
	public function get() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );
		$view->categoryData = $this->toArray( $view->item );
		$view->categoryBody = parent::get();

		return $this->render( $view );
	}


	/**
	 * Saves the data
	 *
	 * @return string|null HTML output
	 */
	public function save() : ?string
	{
		$view = $this->getView();

		$manager = \Aimeos\MShop::create( $this->getContext(), 'category/lists' );
		$manager->begin();

		try
		{
			$this->fromArray( $view->item, $view->param( 'category', [] ) );
			$view->categoryBody = parent::save();

			$manager->commit();
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			throw $e;
		}

		return null;
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Admin\JQAdm\Iface
	{
		/** admin/jqadm/post/category/decorators/excludes
		 * Excludes decorators added by the "common" option from the post JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "admin/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/post/category/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "admin/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/post/category/decorators/global
		 * @see admin/jqadm/post/category/decorators/local
		 */

		/** admin/jqadm/post/category/decorators/global
		 * Adds a list of globally available decorators only to the post JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/post/category/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/post/category/decorators/excludes
		 * @see admin/jqadm/post/category/decorators/local
		 */

		/** admin/jqadm/post/category/decorators/local
		 * Adds a list of local decorators only to the post JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Post\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/post/category/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Post\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/post/category/decorators/excludes
		 * @see admin/jqadm/post/category/decorators/global
		 */
		return $this->createSubClient( 'post/category/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames() : array
	{
		/** admin/jqadm/post/category/subparts
		 * List of JQAdm sub-clients rendered within the post category section
		 *
		 * The output of the frontend is composed of the code generated by the JQAdm
		 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
		 * that are responsible for rendering certain sub-parts of the output. The
		 * sub-clients can contain JQAdm clients themselves and therefore a
		 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
		 * the output that is placed inside the container of its parent.
		 *
		 * At first, always the JQAdm code generated by the parent is printed, then
		 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
		 * determines the order of the output of these sub-clients inside the parent
		 * container. If the configured list of clients is
		 *
		 *  array( "subclient1", "subclient2" )
		 *
		 * you can easily change the order of the output by reordering the subparts:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
		 *
		 * You can also remove one or more parts if they shouldn't be rendered:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1" )
		 *
		 * As the clients only generates structural JQAdm, the layout defined via CSS
		 * should support adding, removing or reordering content by a fluid like
		 * design.
		 *
		 * @param array List of sub-client names
		 * @since 2016.01
		 * @category Developer
		 */
		return $this->getContext()->getConfig()->get( 'admin/jqadm/post/category/subparts', [] );
	}


	/**
	 * Returns the category items for the given category list items
	 *
	 * @param \Aimeos\Map $listItems List of items implementing \Aimeos\Common\Item\Lists\Iface
	 * @return \Aimeos\Map List of category IDs as keys and items implementing \Aimeos\Category\Item\Iface
	 */
	protected function getCategoryItems( \Aimeos\Map $listItems ) : \Aimeos\Map
	{
		$ids = $listItems->getParentId()->toArray();
		$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'category.id', $ids ) );

		return $manager->search( $search );
	}


	/**
	 * Returns the category list items for the given post ID
	 *
	 * @param string $prodid Unique post ID
	 * @return \Aimeos\Map Associative list of category list IDs as keys and list items as values
	 */
	protected function getListItems( string $prodid ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'category/lists' );

		$search = $manager->filter()->slice( 0, 0x7fffffff );
		$expr = array(
			$search->compare( '==', 'category.lists.refid', $prodid ),
			$search->compare( '==', 'category.lists.domain', 'post' ),
		);
		$search->setConditions( $search->and( $expr ) );

		return $manager->search( $search );
	}


	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param \Aimeos\MShop\Post\Item\Iface $item Post item object without referenced domain items
	 * @param array $data Data array
	 */
	protected function fromArray( \Aimeos\MShop\Post\Item\Iface $item, array $data )
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'category/lists' );
		$listItems = $this->getListItems( $item->getId() );
		$list = [];

		foreach( $data as $idx => $entry )
		{
			if( isset( $listItems[$entry['category.lists.id']] ) ) {
				$litem = $listItems[$entry['category.lists.id']];
			} else {
				$litem = $manager->create();
			}

			$list[] = $litem->setParentId( $this->getValue( $entry, 'category.id' ) )->setDomain( 'post' )
				->setType( $this->getValue( $entry, 'category.lists.type' ) )->setRefId( $item->getId() );

			unset( $listItems[$litem->getId()] );
		}

		$manager->delete( $listItems->toArray() );
		$manager->save( $list );
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Post\Item\Iface $item Post item object including referenced domain items
	 * @param bool $copy True if items should be copied, false if not
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Post\Item\Iface $item, bool $copy = false ) : array
	{
		$siteId = $this->getContext()->getLocale()->getSiteId();
		$listItems = $this->getListItems( $item->getId() );
		$catItems = $this->getCategoryItems( $listItems );
		$data = [];

		foreach( $listItems as $listItem )
		{
			$catId = $listItem->getParentId();

			if( ( $catItem = $catItems->get( $catId ) ) === null ) {
				continue;
			}

			$list = $listItem->toArray( true ) + $catItem->toArray( true );

			if( $copy === true )
			{
				$list['category.lists.siteid'] = $siteId;
				$list['category.lists.id'] = '';
			}

			$data[] = $list;
		}

		return $data;
	}


	/**
	 * Returns the rendered template including the view data
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with data assigned
	 * @return string HTML output
	 */
	protected function render( \Aimeos\MW\View\Iface $view ) : string
	{
		/** admin/jqadm/post/category/template-item
		 * Relative path to the HTML body template of the category subpart for posts.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jqadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2016.04
		 * @category Developer
		 */
		$tplconf = 'admin/jqadm/post/category/template-item';
		$default = 'post/item-category-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}
