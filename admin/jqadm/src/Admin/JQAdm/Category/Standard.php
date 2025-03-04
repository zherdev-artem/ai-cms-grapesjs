<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Category;

sprintf( 'goods' ); // for translation
sprintf( 'category' ); // for translation


/**
 * Default implementation of category JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/**
	 * Adds the required data used in the template
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface View object with assigned parameters
	 */
	public function addData( \Aimeos\MW\View\Iface $view ) : \Aimeos\MW\View\Iface
	{
		$view->itemSubparts = $this->getSubClientNames();
		return $view;
	}


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output
	 */
	public function copy() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );

		try
		{
			if( ( $id = $view->param( 'id' ) ) === null ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Required parameter "%1$s" is missing', 'id' ) );
			}

			$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );
			$view->item = $manager->get( $id, $this->getDomains() );

			$view->itemData = $this->toArray( $view->item, true );
			$view->itemRootId = $this->getRootId();
			$view->itemBody = parent::copy();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'copy' );
		}

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

		try
		{
			$data = $view->param( 'item', [] );

			if( !isset( $view->item ) ) {
				$view->item = \Aimeos\MShop::create( $this->getContext(), 'category' )->create();
			}

			$data['category.siteid'] = $view->item->getSiteId();
			$data['category.parentid'] = $view->item->getParentId() ?: $view->param( 'parentid', $view->param( 'item/category.parentid' ) );

			$view->itemData = array_replace_recursive( $this->toArray( $view->item ), $data );
			$view->itemRootId = $this->getRootId();
			$view->itemBody = parent::create();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'create' );
		}

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output
	 */
	public function delete() : ?string
	{
		$view = $this->getView();

		$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );
		$manager->begin();

		try
		{
			if( ( $ids = $view->param( 'id' ) ) === null ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Required parameter "%1$s" is missing', 'id' ) );
			}

			$search = $manager->filter()->slice( 0, count( (array) $ids ) );
			$search->setConditions( $search->compare( '==', 'category.id', $ids ) );
			$items = $manager->search( $search, $this->getDomains() );

			foreach( $items as $item )
			{
				$view->item = $item;
				parent::delete();
			}

			$manager->delete( $items->toArray() );
			$manager->commit();

			return $this->redirect( 'category', 'search', null, 'delete' );
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			$this->report( $e, 'delete' );
		}

		return $this->search();
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output
	 */
	public function get() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );

		try
		{
			if( ( $id = $view->param( 'id' ) ) === null ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Required parameter "%1$s" is missing', 'id' ) );
			}

			$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );

			$view->item = $manager->get( $id, $this->getDomains() );
			$view->itemData = $this->toArray( $view->item );
			$view->itemRootId = $this->getRootId();
			$view->itemBody = parent::get();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'get' );
		}

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

		$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );
		$manager->begin();

		try
		{
			$item = $this->fromArray( $view->param( 'item', [] ) );
			$view->item = $item->getId() ? $item : $manager->save( $item );
			$view->itemBody = parent::save();

			$manager->save( clone $view->item );
			$manager->commit();

			$action = $view->param( 'next' );
			$id = ( $action === 'create' ? $view->item->getParentId() : $view->item->getId() );

			return $this->redirect( 'category', $action, $id, 'save' );
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			$this->report( $e, 'save' );
		}

		return $this->create();
	}


	/**
	 * Returns the category root node
	 *
	 * @return string|null HTML output
	 */
	public function search() : ?string
	{
		$view = $this->getView();

		try
		{
			$view->item = \Aimeos\MShop::create( $this->getContext(), 'category' )->create();
			$view->itemRootId = $this->getRootId();
			$view->itemBody = parent::search();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'search' );
		}

		return $this->render( $view );
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
		/** admin/jqadm/category/decorators/excludes
		 * Excludes decorators added by the "common" option from the category JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/category/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "client/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/category/decorators/global
		 * @see admin/jqadm/category/decorators/local
		 */

		/** admin/jqadm/category/decorators/global
		 * Adds a list of globally available decorators only to the category JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/category/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/category/decorators/excludes
		 * @see admin/jqadm/category/decorators/local
		 */

		/** admin/jqadm/category/decorators/local
		 * Adds a list of local decorators only to the category JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Category\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/category/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Category\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2016.01
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/category/decorators/excludes
		 * @see admin/jqadm/category/decorators/global
		 */
		return $this->createSubClient( 'category/' . $type, $name );
	}


	/**
	 * Returns the domain names whose items should be fetched too
	 *
	 * @return string[] List of domain names
	 */
	protected function getDomains() : array
	{
		/** admin/jqadm/category/domains
		 * List of domain items that should be fetched along with the category
		 *
		 * If you need to display additional content, you can configure your own
		 * list of domains (attribute, media, price, category, text, etc. are
		 * domains) whose items are fetched from the storage.
		 *
		 * @param array List of domain names
		 * @since 2016.01
		 * @category Developer
		 */
		return $this->getContext()->getConfig()->get( 'admin/jqadm/category/domains', [] );
	}


	/**
	 * Returns the IDs of the root category
	 *
	 * @return string|null ID of the root category
	 */
	protected function getRootId() : ?string
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );

		try {
			return $manager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE )->getId();
		} catch( \Exception $e ) {
			return null;
		}
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames() : array
	{
		/** admin/jqadm/category/subparts
		 * List of JQAdm sub-clients rendered within the category section
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
		return $this->getContext()->getConfig()->get( 'admin/jqadm/category/subparts', [] );
	}


	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param array $data Data array
	 * @return \Aimeos\MShop\Category\Item\Iface New category item object
	 */
	protected function fromArray( array $data ) : \Aimeos\MShop\Category\Item\Iface
	{
		$conf = [];

		foreach( (array) $this->getValue( $data, 'config', [] ) as $idx => $entry )
		{
			if( ( $key = trim( $entry['key'] ?? '' ) ) !== '' ) {
				$conf[$key] = trim( $entry['val'] ?? '' );
			}
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'category' );

		if( isset( $data['category.id'] ) && $data['category.id'] != '' ) {
			$item = $manager->get( $data['category.id'], $this->getDomains() );
		} else {
			$item = $manager->create();
		}

		$item = $item->fromArray( $data, true )->setConfig( $conf );

		if( $item->getId() == null ) {
			return $manager->insert( $item, $data['category.parentid'] ?: null );
		}

		return $item;
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Category\Item\Iface $item Category item object
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Category\Item\Iface $item, bool $copy = false ) : array
	{
		$data = $item->toArray( true );
		$data['config'] = [];

		if( $copy === true )
		{
			$data['category.id'] = '';
			$data['category.siteid'] = $item->getSiteId();
			$data['category.code'] = $data['category.code'] . '_' . substr( md5( microtime( true ) ), -5 );
		}

		foreach( $item->getConfig() as $key => $value ) {
			$data['config'][] = ['key' => $key, 'val' => $value];
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
		/** admin/jqadm/category/template-item
		 * Relative path to the HTML body template for the category item.
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
		$tplconf = 'admin/jqadm/category/template-item';
		$default = 'category/item-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}
