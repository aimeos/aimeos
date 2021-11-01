<?php

namespace App\Providers;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Password::defaults(function () {
            $rule = Password::min( 8 );
            return $this->app->isProduction() ? $rule->mixedCase()->uncompromised() : $rule;
        });

        View::composer('*', function ( $view ) {
            $view->with( 'aimeossite', app( 'aimeos.context' )->get()->getLocale()->getSiteItem() );
        });
    }
}
