<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\ResetsPasswords;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
    }
}
