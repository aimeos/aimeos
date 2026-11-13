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

Route::get('/ready', function() {
    return 'OK';
});

// for reverse proxies issuing TLS certificates on demand (e.g. Caddy "ask")
Route::get('/domain-check', function(\Illuminate\Http\Request $request) {
    return \App\Http\Middleware\TrustHosts::shop((string) $request->query('domain')) ? 'OK' : abort(404);
});

$params = [];
$conf = ['prefix' => '', 'where' => []];

if( config( 'app.shop_multilocale' ) )
{
    $conf['prefix'] .= '{locale}';
    $conf['where']['locale'] = '[a-z]{2}(\_[A-Z]{2})?';
    $params = ['locale' => app()->getLocale()];
}

if( config( 'app.shop_multishop' ) )
{
    // shops routed by domain or subdomain, e.g. "{site}" or "{site}.example.com"
    if( str_contains( $domain = (string) config( 'shop.routes.default.domain' ), '{site}' ) ) {
        $conf['domain'] = $domain;
    } else {
        $conf['prefix'] .= '/{site}';
    }

    $conf['where']['site'] = '[A-Za-z0-9\.\-]+';
}

if( $conf['prefix'] )
{
    Route::get('/', function() use ($params) {
        return redirect(airoute('aimeos_home', $params));
    });
}

Route::group($conf ?? [], function() {
    require __DIR__.'/auth.php';
});

if( config( 'app.shop_multiroute' ) )
{
    Route::group( $conf + ['middleware' => ['web']], function() {
        Route::match( ['GET', 'POST'], '/{path?}', array(
            'as' => 'aimeos_resolve',
            'uses' => 'Aimeos\Shop\Controller\ResolveController@indexAction'
        ) )->where( ['locale' => '[a-z]{2}(\_[A-Z]{2})?', 'site' => '[A-Za-z0-9\.\-]+', 'path' => '.*'] );
    });
}