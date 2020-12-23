<?php

$routes = [];

if( config( 'app.shop_multishop' ) || config( 'app.shop_registration' ) ) {
	$routes = ['routes' => [
		'admin' => ['prefix' => 'admin', 'middleware' => ['web', 'verified']],
		'jqadm' => ['prefix' => 'admin/{site}/jqadm', 'middleware' => ['web', 'auth', 'verified']],
		'jsonadm' => ['prefix' => 'admin/{site}/jsonadm', 'middleware' => ['web', 'auth', 'verified']],
		'jsonapi' => ['prefix' => 'jsonapi/{site}', 'middleware' => ['web', 'api']],
		'account' => ['prefix' => 'profile/{site}', 'middleware' => ['web', 'auth']],
		'default' => ['prefix' => 'shop/{site}', 'middleware' => ['web']],
		'update' => ['prefix' => '{site}'],
	] ];
}


return $routes + [

	'apc_enabled' => false, // enable for maximum performance if APCu is availalbe
	'apc_prefix' => 'aimeos:', // prefix for caching config and translation in APCu
	'pcntl_max' => 4, // maximum number of parallel command line processes when starting jobs

	'routes' => [
		// Docs: https://aimeos.org/docs/Laravel/Custom_routes
		// Multi-sites: https://aimeos.org/docs/Laravel/Configure_multiple_shops
		// 'admin' => ['prefix' => 'admin', 'middleware' => ['web']],
		// 'jqadm' => ['prefix' => 'admin/{site}/jqadm', 'middleware' => ['web', 'auth']],
		// 'jsonadm' => ['prefix' => 'admin/{site}/jsonadm', 'middleware' => ['web', 'auth']],
		// 'jsonapi' => ['prefix' => 'jsonapi', 'middleware' => ['web', 'api']],
		// 'account' => ['prefix' => 'myaccount', 'middleware' => ['web', 'auth']],
		// 'default' => ['prefix' => 'shop', 'middleware' => ['web']],
		// 'update' => [],
	],


	'page' => [
		// Docs: https://aimeos.org/docs/Laravel/Adapt_pages
		// Hint: catalog/filter is also available as single 'catalog/tree', 'catalog/search', 'catalog/attribute'
		'account-index' => [ 'account/profile','account/review','account/subscription','account/history','account/favorite','account/watch','basket/mini','catalog/session','locale/select' ],
		'basket-index' => [ 'basket/bulk', 'basket/standard','basket/related' ],
		'catalog-count' => [ 'catalog/count' ],
		'catalog-detail' => [ 'basket/mini','catalog/stage','catalog/detail','catalog/session','locale/select' ],
		'catalog-home' => [ 'basket/mini','catalog/home','locale/select' ],
		'catalog-list' => [ 'basket/mini','catalog/filter','catalog/lists','locale/select' ],
		'catalog-stock' => [ 'catalog/stock' ],
		'catalog-suggest' => [ 'catalog/suggest' ],
		'catalog-tree' => [ 'basket/mini','catalog/filter','catalog/stage','catalog/lists','locale/select' ],
		'checkout-confirm' => [ 'checkout/confirm' ],
		'checkout-index' => [ 'checkout/standard' ],
		'checkout-update' => [ 'checkout/update' ],
	],


	/*
	'resource' => [
		'db' => [
			'adapter' => config('database.connections.mysql.driver', 'mysql'),
			'host' => config('database.connections.mysql.host', '127.0.0.1'),
			'port' => config('database.connections.mysql.port', '3306'),
			'socket' => config('database.connections.mysql.unix_socket', ''),
			'database' => config('database.connections.mysql.database', 'forge'),
			'username' => config('database.connections.mysql.username', 'forge'),
			'password' => config('database.connections.mysql.password', ''),
			'stmt' => ["SET SESSION sort_buffer_size=2097144; SET NAMES 'utf8mb4'; SET SESSION sql_mode='ANSI'"],
			'limit' => 3, // maximum number of concurrent database connections
			'defaultTableOptions' => [
					'charset' => config('database.connections.mysql.charset'),
					'collate' => config('database.connections.mysql.collation'),
			],
		],
	],
	*/

	'admin' => [],

	'client' => [
		'html' => [
			'basket' => [
				'cache' => [
					// 'enable' => false, // Disable basket content caching for development
				],
			],
			'common' => [
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
				// 'name' => 'None', // Disable caching for development
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
