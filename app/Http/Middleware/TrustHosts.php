<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TrustHosts extends Middleware
{
    /**
     * Handle the incoming request.
     *
     * Shops routed by domain or subdomain are trusted if the host belongs to an active site.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, $next)
    {
        if ($this->shouldSpecifyTrustedHosts()) {
            Request::setTrustedHosts([]); // validate the host without the patterns of a previous request
            $hosts = array_filter($this->hosts());

            if (!array_filter($hosts, fn($pattern) => preg_match('{' . $pattern . '}i', $request->getHost()))
                && static::shop($request->getHost())
            ) {
                $hosts[] = '^' . preg_quote($request->getHost()) . '$';
            }

            Request::setTrustedHosts($hosts);
        }

        return $next($request);
    }


    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts()
    {
        $hosts = array_filter(array_map('trim', explode(',', (string) config('app.trusted_hosts', ''))));

        return array_merge(
            [$this->allSubdomainsOfApplicationUrl(), '^10\.\d+\.\d+\.\d+$'], // private IPs for Kubernetes probe checks
            array_map(fn($host) => '^' . preg_quote(strtolower($host)) . '$', $hosts)
        );
    }


    /**
     * Returns the domain of the shop routes if shops are routed by domain or subdomain
     *
     * @return string|null Domain like "{site}" or "{site}.example.com", NULL if shops aren't routed by domain
     */
    public static function domain(): ?string
    {
        $routes = (array) config('shop.routes', []);

        return config('app.shop_multishop') ? collect(['default' => $routes['default'] ?? []] + $routes)
            ->map(fn($route) => (string) ($route['domain'] ?? ''))
            ->first(fn($domain) => str_contains($domain, '{site}')) : null;
    }


    /**
     * Tests if the host belongs to an active site if shops are routed by domain or subdomain
     *
     * @param string $host Host name like "myshop.example" or "vendor.example.com"
     * @return bool TRUE if the host matches the shop domain, e.g. "{site}" or "{site}.example.com", of an active site
     */
    public static function shop(string $host): bool
    {
        $host = strtolower($host);

        if (!$domain = static::domain()) {
            return false;
        }

        if (Cache::get($key = 'shop-host:' . $host)) {
            return true;
        }

        // same matching as the router does for the domain of the shop routes
        $pattern = '/^' . str_replace('\\{site\\}', '([A-Za-z0-9\\.\\-]+)', preg_quote($domain, '/')) . '$/i';

        if (preg_match($pattern, $host, $match)
            && DB::table('mshop_locale_site')->where('code', $match[1])->where('status', 1)->exists()
        ) {
            Cache::put($key, true, 300);
            return true;
        }

        return false;
    }
}
