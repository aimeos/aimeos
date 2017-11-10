<?php

return [

	'apc_enabled' => false,
	'apc_prefix' => 'aimeos:',

	'routes' => [
		// 'login' => ['middleware' => ['web']],
		// 'jqadm' => ['prefix' => 'admin/{site}/jqadm', 'middleware' => ['web', 'auth']],
		// 'extadm' => ['prefix' => 'admin/{site}/extadm', 'middleware' => ['web', 'auth']],
		// 'jsonadm' => ['prefix' => 'admin/{site}/jsonadm', 'middleware' => ['web', 'auth']],
		// 'jsonapi' => ['prefix' => 'jsonapi', 'middleware' => ['web', 'api']],
		// 'account' => ['middleware' => ['web', 'auth']],
		// 'default' => ['middleware' => ['web']],
		// 'update' => [],
	],

	'page' => [
		'account-index' => array( 'account/profile','account/history','account/favorite','account/watch','basket/mini','catalog/session','locale/select' ),
		'catalog-detail' => array( 'basket/mini','catalog/stage','catalog/detail','catalog/session','locale/select' ),
		'catalog-list' => array( 'basket/mini','catalog/filter','catalog/stage','catalog/lists','locale/select' ),
		// 'basket-index' => array( 'basket/standard','basket/related' ),
		// 'catalog-count' => [ 'catalog/count' ],
		// 'catalog-stock' => [ 'catalog/stock' ],
		// 'catalog-suggest' => [ 'catalog/suggest' ],
		// 'checkout-confirm' => [ 'checkout/confirm' ],
		// 'checkout-index' => [ 'checkout/standard' ],
		// 'checkout-update' => [ 'checkout/update' ],
	],

	/*
	'resource' => [
		'db' => [
			'adapter' => env('DB_CONNECTION', 'mysql'),
			'host' => env('DB_HOST', 'localhost'),
			'port' => env('DB_PORT', ''),
			'socket' => '',
			'database' => env('DB_DATABASE', 'laravel'),
			'username' => env('DB_USERNAME', 'root'),
			'password' => env('DB_PASSWORD', ''),
			'stmt' => ["SET SESSION sort_buffer_size=2097144; SET NAMES 'utf8'; SET SESSION sql_mode='ANSI'"],
		],
	],
	*/

	'admin' => [],

	'client' => [
		'html' => [
			'basket' => [
				'cache' => [
					// 'enable' => false, // Disable basket content caching
				],
			],
			'common' => [
				'content' => [
					// 'baseurl' => '/',
				],
				'template' => [
					// 'baseurl' => 'packages/aimeos/shop/themes/elegance',
				],
			],
			'catalog' => [
				'selection' => [
					'type' => [
						'color' => 'radio',
						'length' => 'radio',
						'width' => 'radio',
					],
				],
			],
		],
	],

	'controller' => [
	],

	'i18n' => [
	],

	'madmin' => [
		'cache' => [
			'manager' => [
				// 'name' => 'None', // Disable caching
			],
		],
		'log' => [
			'manager' => [
				'standard' => [
					// 'loglevel' => 7, // Enable debug logging into madmin_log table
				],
			],
		],
	],

	'mshop' => [
	],


	'command' => [
	],

	'frontend' => [
	],

	'backend' => [
	],

];
