<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Post
 */


namespace Aimeos\MShop\Post\Item;


/**
 * Default post manager implementation.
 *
 * @package MShop
 * @subpackage Post
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Post\Item\Iface
{
	use \Aimeos\MShop\Common\Item\ListsRef\Traits;


	/**
	 * Initializes the post item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'post.', $values );

		$this->initListItems( $listItems, $refItems );
	}

    /**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @return string Specified text type or label of the item
	 */
	public function getName( string $type = 'name' ) : string
	{
		if( ( $item = $this->getRefItems( 'text', $type )->first() ) !== null ) {
			return $item->getContent();
		} else if ( $type === 'url' ) {
            return $this->getUrl();
        }

		return '';
	}

	/**
	 * Returns the URL of the post item.
	 *
	 * @return string URL of the post item
	 */
	public function getUrl() : string
	{
		return $this->get( 'post.url', '' );
	}


	/**
	 * Sets the URL of the post item.
	 *
	 * @param string $value URL of the post item
	 * @return \Aimeos\MShop\Post\Item\Iface Post item for chaining method calls
	 */
	public function setUrl( string $value ) : \Aimeos\MShop\Post\Item\Iface
	{
		return $this->set( 'post.url', \Aimeos\MW\Str::slug( $value ));
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string
	{
		return $this->get( 'post.label', '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Post\Item\Iface Post item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Post\Item\Iface
	{
		return $this->set( 'post.label', (string) $label );
	}


	/**
	 * Returns the status of the post item.
	 *
	 * @return int Status of the post item
	 */
	public function getStatus() : int
	{
		return $this->get( 'post.status', 1 );
	}


	/**
	 * Sets the status of the post item.
	 *
	 * @param int $status true/false for enabled/disabled
	 * @return \Aimeos\MShop\Post\Item\Iface Post item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'post.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'post';
	}

    /**
	 * Returns the category items referencing the post
	 *
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Category\Item\Iface
	 */
	public function getCategoryItems() : \Aimeos\Map
	{
		return map( $this->get( '.category', [] ) );
	}

    /**
	 * Returns the first category item referencing the post
	 */
    public function getCategory() {
        $categories = $this->getCategoryItems();

        if ( $categories->isEmpty() )
            return null;

        return $categories->first();
    }


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Post\Item\Iface Post item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'post.url': $item = $item->setUrl( $value ); break;
				case 'post.label': $item = $item->setLabel( $value ); break;
				case 'post.status': $item = $item->setStatus( (int) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['post.url'] = $this->getUrl();
		$list['post.label'] = $this->getLabel();
		$list['post.status'] = $this->getStatus();

		return $list;
	}
}
