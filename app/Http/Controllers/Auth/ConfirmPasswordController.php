<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\ConfirmsPasswords;


class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
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

        $this->middleware('auth');
    }
}
