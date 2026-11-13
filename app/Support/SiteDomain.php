<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;


/**
 * Shop domains based on site codes, e.g. route option "domain" => "{site}"
 * (code is the domain) or "domain" => "{site}.example.com" (code is the subdomain)
 */
class SiteDomain
{
    /**
     * Returns the domain template of the shop routes
     *
     * @return string|null Domain template containing "{site}" or NULL if shops aren't routed by domain
     */
    public static function template() : ?string
    {
        if( !config( 'app.shop_multishop' ) ) {
            return null;
        }

        $routes = (array) config( 'shop.routes', [] );

        foreach( array_merge( ['default' => $routes['default'] ?? []], $routes ) as $route )
        {
            if( str_contains( $domain = strtolower( (string) ( $route['domain'] ?? '' ) ), '{site}' ) ) {
                return $domain;
            }
        }

        return null;
    }


    /**
     * Tests if shops are routed by domain
     *
     * @return bool TRUE if domain routing is used, FALSE if not
     */
    public static function enabled() : bool
    {
        return static::template() !== null;
    }


    /**
     * Returns the host name of the site
     *
     * @param string $code Site code
     * @return string|null Host name or NULL if the site has no own domain
     */
    public static function host( string $code ) : ?string
    {
        $code = strtolower( $code );

        if( !( $template = static::template() ) || $code === strtolower( config( 'shop.mshop.locale.site', 'default' ) )
            || !preg_match( '/^[a-z0-9-]+(\.[a-z0-9-]+)*$/', $code )
            || $template === '{site}' && !str_contains( $code, '.' ) // code must be a domain if it's not a subdomain
        ) {
            return null;
        }

        return str_replace( '{site}', $code, $template );
    }


    /**
     * Tests if the host belongs to an active site
     *
     * @param string $host Host name
     * @return bool TRUE if a site uses the host as domain, FALSE if not
     */
    public static function exists( string $host ) : bool
    {
        if( !( $template = static::template() ) ) {
            return false;
        }

        [$prefix, $suffix] = explode( '{site}', $template, 2 );
        $host = strtolower( $host );

        if( !str_starts_with( $host, $prefix ) || !str_ends_with( $host, $suffix ) ) {
            return false;
        }

        $code = substr( $host, strlen( $prefix ), strlen( $host ) - strlen( $prefix ) - strlen( $suffix ) );

        return static::host( $code ) === $host
            && DB::table( 'mshop_locale_site' )->where( 'code', $code )->where( 'status', 1 )->exists();
    }


    /**
     * Returns the root URL for links sent to the user
     *
     * Never derived from the request host because it can be forged and then
     * sent tokens to the attacker.
     *
     * @param object $user User the link is sent to
     * @return string Root URL without trailing slash
     */
    public static function rootUrl( $user ) : string
    {
        $root = rtrim( (string) config( 'app.url' ), '/' );

        if( !static::enabled() || !empty( $user->superuser ) || empty( $user->siteid ) ) {
            return $root;
        }

        $code = DB::table( 'mshop_locale_site' )->where( 'siteid', $user->siteid )->where( 'status', 1 )->value( 'code' );

        if( !$code || !( $host = static::host( $code ) ) ) {
            return $root;
        }

        return ( parse_url( $root, PHP_URL_SCHEME ) ?: 'https' ) . '://' . $host;
    }
}
