<?php

return [
	'jqadm' => [
		'cms' => [
			'domains' => [
				'text' => 'text',
				'media' => 'media',
			],
			'subparts' => [
				'content' => 'content',
				'seo' => 'seo',
				'media' => 'media',
			],
		],
		'navbar' => [
			45 => [
                '' => 'cms',
                10 => 'cms',
                20 => 'category'
            ],
			70 => [
				45 => 'type/cms/lists'
			]
		],
		'resource' => [
			'cms' => [
				/** admin/jqadm/resource/cms/groups
				 * List of user groups that are allowed to access the CMS panel
				 *
				 * @param array List of user group names
				 * @since 2021.04
				 */
				'groups' => ['admin', 'editor', 'super'],

				/** admin/jqadm/resource/cms/key
				 * Shortcut key to switch to the CMS panel by using the keyboard
				 *
				 * @param string Single character in upper case
				 * @since 2021.04
				 */
				'key' => 'M',
			],
            'category' => [
				/** admin/jqadm/resource/category/groups
				 * List of user groups that are allowed to access the CMS panel
				 *
				 * @param array List of user group names
				 * @since 2021.04
				 */
				'groups' => ['admin', 'editor', 'super'],

				/** admin/jqadm/resource/category/key
				 * Shortcut key to switch to the CMS panel by using the keyboard
				 *
				 * @param string Single character in upper case
				 * @since 2021.04
				 */
				'key' => 'M',
			],
		]
	],
	'jsonadm' => [
        'partials' => [
			'category' => [
				'template-data' => 'partials/category/data-standard',
			],
		],
        'domains' => [
			'category' => 'category'
		],
        'resources' => [
            'category/lists/type' => 'category/lists/type',
        ],
        'resource' => [
            'category' => [
                /** admin/jsonadm/resource/category/groups
                 * List of user groups that are allowed to manage category items
                 *
                 * @param array List of user group names
                 * @since 2017.10
                 */
                'groups' => ['admin', 'editor', 'super'],
                'lists' => [
                    /** admin/jsonadm/resource/category/lists/groups
                     * List of user groups that are allowed to manage category lists items
                     *
                     * @param array List of user group names
                     * @since 2017.10
                     */
                    'groups' => ['admin', 'editor', 'super'],
                    'type' => [
                        /** admin/jsonadm/resource/category/lists/type/groups
                         * List of user groups that are allowed to manage category lists type items
                         *
                         * @param array List of user group names
                         * @since 2017.10
                         */
                        'groups' => ['admin', 'editor', 'super'],
                    ],
                ],
            ]
        ]
	],
];
