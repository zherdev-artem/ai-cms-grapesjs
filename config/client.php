<?php

return [
	'html' => [
		'cms' => [
			'page' => [
				'template-error' => 'cms/page/body-error'
            ],
            'post' => [
                'url' => [
                    'target' => 'aimeos_post'
                ]
            ]
		]
	],
	'jsonapi' => [
        'resources' => [
            'category' => 'category',
            'post' => 'post'
        ]
	],
];
