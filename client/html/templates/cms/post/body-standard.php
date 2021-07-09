<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

/* Available data:
 * - postCmsItem : Cms post item incl. referenced items
 */


$enc = $this->encoder();


?>
<section class="aimeos cms-post" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ); ?>">

	<?php if( isset( $this->postErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->postErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if( isset( $this->postCmsItem ) ) : ?>
		<?php foreach( $this->postCmsItem->getRefItems( 'text', 'content' ) as $textItem ) : ?>
			<?= $textItem->getContent() ?>
		<?php endforeach ?>
	<?php endif; ?>

</section>
