<?php

use Illuminate\Support\Facades\Route;

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
        return redirect(airoute('aimeos_home', ['locale' => app()->getLocale()]));
    });
}


Route::group($locale ?? [], function() {

    Route::group(['prefix' => 'auth'], function() {
        require __DIR__.'/auth.php';
    });

    Route::match(['GET', 'POST'], '{path?}', '\Aimeos\Shop\Controller\PageController@indexAction')
        ->name('aimeos_page')->where( 'path', '.*' );

});
