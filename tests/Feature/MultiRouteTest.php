<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class MultiRouteTest extends TestCase
{
    protected function createApplication()
    {
        $processValue = getenv('SHOP_MULTIROUTE');
        $hasEnvValue = array_key_exists('SHOP_MULTIROUTE', $_ENV);
        $envValue = $_ENV['SHOP_MULTIROUTE'] ?? null;
        $hasServerValue = array_key_exists('SHOP_MULTIROUTE', $_SERVER);
        $serverValue = $_SERVER['SHOP_MULTIROUTE'] ?? null;

        putenv('SHOP_MULTIROUTE=true');
        $_ENV['SHOP_MULTIROUTE'] = 'true';
        $_SERVER['SHOP_MULTIROUTE'] = 'true';

        try {
            return parent::createApplication();
        } finally {
            $processValue === false ? putenv('SHOP_MULTIROUTE') : putenv('SHOP_MULTIROUTE=' . $processValue);

            if ($hasEnvValue) {
                $_ENV['SHOP_MULTIROUTE'] = $envValue;
            } else {
                unset($_ENV['SHOP_MULTIROUTE']);
            }

            if ($hasServerValue) {
                $_SERVER['SHOP_MULTIROUTE'] = $serverValue;
            } else {
                unset($_SERVER['SHOP_MULTIROUTE']);
            }
        }
    }

    public function test_multi_route_matches_nested_paths()
    {
        $request = Request::create('/category/item', 'GET');
        $route = $this->app['router']->getRoutes()->match($request);

        $this->assertSame('category/item', $route->parameter('path'));
    }
}
