<?php

namespace App\Providers;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;


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


        // for multi-locale/site setups
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(function($notifiable, $token) {
            return url(airoute('password.reset', [
                'email' => $notifiable->getEmailForPasswordReset(),
                'token' => $token,
            ], false));
        });


        // for multi-locale/site setups
        \Illuminate\Auth\Notifications\VerifyEmail::$createUrlCallback = function($notifiable) {
            $time = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));
            $params = [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ];

            if( config( 'app.shop_multilocale' ) ) {
                $params['locale'] = Request::route( 'locale', Request::input( 'locale', app()->getLocale() ) );
            }

            if( config( 'app.shop_multishop' ) || config( 'app.shop_registration' ) ) {
                $params['site'] = Request::route( 'site', Request::input( 'site', config( 'shop.mshop.locale.site', 'default' ) ) );
            }

            return URL::temporarySignedRoute('verification.verify', $time, $params);
        };


        // Aimeos admin check for backend
        \Illuminate\Support\Facades\Gate::define('admin', function($user, $class, $roles) {
            if( isset( $user->superuser ) && $user->superuser ) {
                return true;
            }
            return app( '\Aimeos\Shop\Base\Support' )->checkUserGroup( $user, $roles );
        });


        // Aimeos context for icon and logo in all Blade templates
        View::composer('*', function ( $view ) {
            try {
                $view->with( 'aimeossite', app( 'aimeos.context' )->get()->locale()->getSiteItem() );
            } catch( \Exception $e ) {
                $view->with( 'aimeossite', \Aimeos\MShop::create( app( 'aimeos.context' )->get( false ), 'locale/site' )->create() );
            }
        });


        // resolve CMS pages sharing same route as categories and products
        \Aimeos\Shop\Controller\ResolveController::register( 'cms', function( $context, $path ) {
            return $this->cms( $context, $path );
        });
    }
}
