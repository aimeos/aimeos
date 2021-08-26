<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TrustHosts extends Middleware
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, $next)
    {
        if ($this->shouldSpecifyTrustedHosts()) {
            Request::setTrustedHosts(array_filter($this->trusted($request)));
        }

        return $next($request);
    }


    public function hosts()
    {
    }


    /**
     * Get the host patterns that should be trusted.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function trusted(Request $request)
    {
        if($domain = $request->route()->getDomain()) {
            $domain = DB::table( 'mshop_locale_site' )->where('code', $domain)->first();
        }

        return [$domain, $this->allSubdomainsOfApplicationUrl()];
    }
}
