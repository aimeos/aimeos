<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The headers that should be used to detect proxies.
     *
     * X-Forwarded-Host is not trusted because load balancers often pass it
     * through from the client unchanged and it's used to generate URLs.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;


    /**
     * Get the trusted proxies.
     *
     * @return array|string|null
     */
    protected function proxies()
    {
        return static::$alwaysTrustProxies ?: config('app.trusted_proxies');
    }
}
