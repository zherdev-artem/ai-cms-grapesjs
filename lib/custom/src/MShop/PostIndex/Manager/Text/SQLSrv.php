<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage PostIndex
 */


namespace Aimeos\MShop\PostIndex\Manager\Text;


/**
 * SQL Server based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage PostIndex
 */
class SQLSrv
	extends \Aimeos\MShop\PostIndex\Manager\Text\Standard
{
	private $searchConfig = array(
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
	);


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/post/index/manager/sitemode', $level );

		if( $context->getConfig()->get( 'mshop/post/index/manager/text/sqlsrv/fulltext', false ) )
		{
			$search = ':site AND mpostindte."langid" = $1 AND (
				SELECT mpostindte_ft.RANK
				FROM CONTAINSTABLE("mshop_index_text", "content", $2) AS mpostindte_ft
				WHERE mpostindte."id" = mpostindte_ft."KEY"
			)';
			$sort = 'mpostindte_ft.RANK';

			$func = $this->getFunctionRelevance();
		}
		else
		{
			$search = ':site AND mpostindte."langid" = $1 AND CHARINDEX( $2, content )';
			$sort = '-CHARINDEX( $2, content )';

			$func = function( $source, array $params ) {

				if( isset( $params[1] ) ) {
					$params[1] = mb_strtolower( $params[1] );
				}

				return $params;
			};
		}

		$expr = $this->getSiteString( 'mpostindte."siteid"', $level );

		$this->searchConfig['index.text:relevance']['internalcode'] = str_replace( ':site', $expr, $search );
		$this->searchConfig['sort:index.text:relevance']['internalcode'] = $sort;
		$this->searchConfig['index.text:relevance']['function'] = $func;
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attriubte items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}


	/**
	 * Returns the search function for searching by relevance
	 *
	 * @return \Closure Relevance search function
	 */
	protected function getFunctionRelevance()
	{
		return function( $source, array $params ) {

			if( isset( $params[1] ) )
			{
				$strings = [];
				$regex = '/(\&|\||\!|\-|\+|\>|\<|\(|\)|\~|\*|\:|\"|\'|\@|\\| )+/';
				$search = trim( mb_strtolower( preg_replace( $regex, ' ', $params[1] ) ), "' \t\n\r\0\x0B" );

				foreach( explode( ' ', $search ) as $part )
				{
					if( strlen( $part ) > 2 ) {
						$strings[] = '"' . $part . '*"';
					}
				}

				$params[1] = '\'' . join( ' | ', $strings ) . '\'';
			}

			return $params;
		};
	}
}
