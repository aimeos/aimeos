<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\AuthenticatesUsers;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if( config( 'app.shop_registration' ) ) {
            $this->redirectTo = '/admin';
        } else {
            if( $current = Route::current() )
            {
                $params = [
                    'site' => Route::current()->parameter( 'site', 'default' ),
                    'locale' => Route::current()->parameter( 'locale', app()->getLocale()  ),
                    'currency' => Route::current()->parameter( 'currency', 'EUR' )
                ];
            }

            $this->redirectTo = route( 'aimeos_shop_account', $params ?? [] );
        }

        $this->middleware('guest')->except('logout');
    }
}
