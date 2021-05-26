<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/cms/post/url/target' );
$cntl = $this->config( 'client/html/cms/post/url/controller', 'page' );
$action = $this->config( 'client/html/cms/post/url/action', 'post' );
$config = $this->config( 'client/html/cms/post/url/config', [] );


/** client/html/cms/post/metatags
 * Adds the title, meta and link tags to the HTML header
 *
 * By default, each instance of the cms list component adds some HTML meta
 * tags to the post head section, like post title, meta keywords and description
 * as well as some link tags to support browser navigation. If several instances
 * are placed on one post, this leads to adding several title and meta tags used
 * by search engine. This setting enables you to suppress these tags in the post
 * header and maybe add your own to the post manually.
 *
 * @param boolean True to display the meta tags, false to hide it
 * @since 2021.01
 * @category Developer
 * @category User
 * @see client/html/cms/lists/metatags
 */
?>
<?php if( (bool) $this->config( 'client/html/cms/post/metatags', true ) === true ) : ?>
	<?php if( isset( $this->postCmsItem ) ) : ?>
		<title><?= $enc->html( strip_tags( $this->postCmsItem->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

		<link rel="canonical" href="<?= $enc->attr( $this->url( $target, $cntl, $action, ['f_name' => $this->param('f_name'), 'path' => $this->postCmsItem->getUrl()], $config + ['absoluteUri' => true] ) ); ?>" />

		<meta property="og:type" content="article" />
		<meta property="og:title" content="<?= $enc->attr( $this->postCmsItem->getName() ); ?>" />
		<meta property="og:url" content="<?= $enc->attr( $this->url( $target, $cntl, $action, ['f_name' => $this->param('f_name'), 'path' => $this->postCmsItem->getUrl()], $config + ['absoluteUri' => true] ) ); ?>" />

		<?php foreach( $this->postCmsItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
			<meta property="og:image" content="<?= $enc->attr( $this->content( $mediaItem->getUrl() ) ) ?>" />
		<?php endforeach ?>

		<?php foreach( $this->postCmsItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
			<meta property="og:description" content="<?= $enc->attr( $textItem->getContent() ) ?>" />
			<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
		<?php endforeach ?>

		<?php foreach( $this->postCmsItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
			<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
		<?php endforeach; ?>

		<meta name="twitter:card" content="summary_large_image" />

	<?php else : ?>

		<title><?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

	<?php endif; ?>

	<meta name="application-name" content="Aimeos" />

<?php endif; ?>

<?= $this->get( 'postHeader' ); ?>
