<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();

$detailTarget = $this->config( 'client/html/post/url/target' );
$detailCntl = $this->config( 'client/html/post/url/controller', 'page' );
$detailAction = $this->config( 'client/html/post/url/action', 'post' );
$detailFilter = array_flip( $this->config( 'client/html/post/url/filter', [] ) );
$detailConfig = $this->config( 'client/html/post/url/config', [] );
$detailConfig['absoluteUri'] = true;

$freq = $enc->xml( $this->get( 'siteFreq', 'daily' ) );

foreach( $this->get( 'siteItems', [] ) as $id => $item )
{
	$texts = [];
	$date = str_replace( ' ', 'T', $item->getTimeModified() ) . date( 'P' );

	foreach( $item->getListItems( 'text', 'default', 'url', false ) as $listItem )
	{
		if( $listItem->isAvailable() && ( $text = $listItem->getRefItem() ) !== null && $text->getStatus() > 0 ) {
			$texts[$text->getLanguageId()] = \Aimeos\MW\Str::slug( $text->getContent() );
		}
	}

	if( empty( $texts ) ) {
		$texts[''] = $item->getLabel();
	}

	foreach( $texts as $lang => $name )
	{
		$params = array_diff_key( ['path' => \Aimeos\MW\Str::slug( $name )], $detailFilter );
		$url = $this->url( $detailTarget, $detailCntl, $detailAction, $params, [], $detailConfig );

		echo '<url><loc>' . $enc->xml( $url ) . '</loc><lastmod>' . $date . '</lastmod><changefreq>' . $freq . "</changefreq></url>\n";
	}
}
