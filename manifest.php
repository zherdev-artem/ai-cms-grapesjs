<?php

return [
	'name' => 'ai-cms-grapesjs',
	'depends' => [
		'aimeos-core',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-client-jsonapi',
        'ai-controller-jobs',
		'ai-controller-frontend',
	],
	'include' => [
		'lib/custom/src',
		'client/html/src',
		'client/jsonapi/src',
		'controller/frontend/src',
        'controller/jobs/src',
		'admin/jsonadm/src',
		'admin/jqadm/src',
	],
	'i18n' => [
		'admin' => 'admin/i18n',
		'admin/jsonadm' => 'admin/jsonadm/i18n',
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'controller/frontend' => 'controller/frontend/i18n',
        'controller/jobs' => 'controller/jobs/i18n',
		'mshop' => 'lib/custom/i18n',
	],
	'config' => [
		'config',
	],
	'setup' => [
		'lib/custom/setup',
	],
	'template' => [
		'admin/jqadm/templates' => [
			'admin/jqadm/templates',
		],
		'admin/jsonadm/templates' => [
			'admin/jsonadm/templates',
		],
		'client/html/templates' => [
			'client/html/templates',
		],
		'client/jsonapi/templates' => [
			'client/jsonapi/templates',
        ],
        'controller/jobs' => array(
			'controller/jobs/src',
		),
		'controller/jobs/templates' => array(
			'controller/jobs/templates',
			'client/html/templates',
		),
	],
	'custom' => [
		'admin/jqadm' => [
			'admin/jqadm/manifest.jsb2',
		],
	],
];
