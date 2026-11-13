<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Http\Request;
use Tests\TestCase;

class MultiRouteTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        // routes are built from the configuration when the application boots
        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            $app['config']->set('app.shop_multiroute', true);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function test_multi_route_matches_nested_paths()
    {
        $request = Request::create('/category/item', 'GET');
        $route = $this->app['router']->getRoutes()->match($request);

        $this->assertSame('category/item', $route->parameter('path'));
    }
}
