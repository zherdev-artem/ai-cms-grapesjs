<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

return [
	'post/lists/type' => [
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'text/type' => [
		['domain' => 'post', 'code' => 'name', 'label' => 'Name', 'status' => 1],
		['domain' => 'post', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1],
		['domain' => 'post', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1],
        ['domain' => 'post', 'code' => 'url', 'label' => 'URL segment', 'status' => 1],
        ['domain' => 'post', 'code' => 'short', 'label' => 'Short description', 'status' => 1],
		['domain' => 'post', 'code' => 'content', 'label' => 'Content', 'status' => 1],
	],

	'media/type' => [
		['domain' => 'post', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	]
];
