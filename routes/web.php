<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if( env( 'SHOP_MULTILOCALE' ) )
{
    $locale = ['prefix' => '{locale}', 'where' => ['locale' => '[a-zA-Z]{2}(\_[a-zA-Z]{2})?']];

    Route::get('/', function () {
        return redirect(app()->getLocale());
    });
}


Route::group($locale ?? [], function() {

    Auth::routes(['verify' => true]);

    Route::get('/', '\Aimeos\Shop\Controller\CatalogController@homeAction')->name('aimeos_home');

    Route::get('{path?}', '\Aimeos\Shop\Controller\PageController@indexAction')
        ->name('aimeos_page')->where( 'path', '.*' );

});
