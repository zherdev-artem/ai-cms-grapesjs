<?php

return [
	'cms' => include(__DIR__ . '/mshop/cms.php'),
    'post' => include(__DIR__ . '/mshop/post.php'),
    'category' => include(__DIR__ . '/mshop/category.php'),
    'post' => [
        'index' => include(__DIR__ . '/mshop/post-index.php')
    ],
	'locale' => [
		'manager' => [
			'site' => [
				'cleanup' => [
					'shop' => [
						'domains' => [
							'cms' => 'cms'
						]
					]
				]
			]
		]
	]
];
