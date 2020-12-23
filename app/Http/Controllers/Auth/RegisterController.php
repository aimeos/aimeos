<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if( config( 'app.shop_registration' ) ) {
            $this->redirectTo = '/admin';
        }

        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validate = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if( config( 'app.shop_registration' ) ) {
            $validate['code'] = ['required', 'string', 'max:255', 'unique:mshop_locale_site', 'regex:/^[a-z0-9\-]+(\.[a-z0-9\-]+)?$/i'];
        } else {
            $validate['name'] = ['required', 'string', 'max:255'];
        }

        return Validator::make($data, $validate);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $context = app( 'aimeos.context' )->get();
        $manager = \Aimeos\MShop::create( $context, 'locale/site' );
        $root = $manager->find( 'default' );
        $siteId = $root->getSiteId();

        if( config( 'app.shop_registration' ) )
        {
            $item = $manager->create()->setCode( $data['code'] )->setLabel( $data['code'] );
            $siteId = $manager->insertItem( $item, $root->getId() )->getSiteId();

            $paths = app( 'aimeos' )->get()->getSetupPaths( 'default' );
            $config = $context->getConfig()->set( 'setup/site', $data['code'] );
            $dbconf = $this->getDbConfig( $config );
            $dbm = $context->getDatabaseManager();

            $setup = new \Aimeos\MW\Setup\Manager\Multiple( $dbm, $dbconf, $paths, $context );
            ob_start();
            $setup->migrate();
            ob_end_clean();
        }

        $user = User::create([
            'name' => $data['code'] ?? $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'siteid' => $siteId,
        ]);

        if( config( 'app.shop_registration' ) )
        {
            $group = config( 'app.shop_permission', 'admin' );
            $context->setLocale( \Aimeos\MShop::create( $context, 'locale' )->bootstrap( $data['code'] ) );
            $groupId = \Aimeos\MShop::create( $context, 'customer/group' )->find( $group )->getId();

            $manager = \Aimeos\MShop::create( $context, 'customer/lists' );
            $item = $manager->create()->setParentId( $user->id )->setDomain( 'customer/group' )
                ->setType( 'default' )->setRefId( $groupId );
            $manager->saveItem( $item );
        }

        return $user;
    }


	/**
	 * Returns the database configuration from the config object.
	 *
	 * @param \Aimeos\MW\Config\Iface $conf Config object
	 * @return array Multi-dimensional associative list of database configuration parameters
	 */
	protected function getDbConfig( \Aimeos\MW\Config\Iface $conf ) : array
	{
		$dbconfig = $conf->get( 'resource', array() );

		foreach( $dbconfig as $rname => $dbconf )
		{
			if( strncmp( $rname, 'db', 2 ) !== 0 ) {
				unset( $dbconfig[$rname] );
			} else {
				$conf->set( 'resource/' . $rname . '/limit', 5 );
			}
		}

		return $dbconfig;
	}
}
