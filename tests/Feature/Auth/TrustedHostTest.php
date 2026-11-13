<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\TrustHosts;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TrustedHostTest extends TestCase
{
    use RefreshDatabase;

    protected bool $multishop = false;
    protected ?string $domain = null;


    public function createApplication()
    {
        $app = require __DIR__.'/../../../bootstrap/app.php';

        // routes are built from the configuration when the application boots
        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            $app['config']->set('app.url', 'https://shop.test');
            $app['config']->set('app.shop_multishop', $this->multishop);
            $app['config']->set('shop.routes.default.domain', $this->domain);
        });

        $app->make(Kernel::class)->bootstrap();

        // trusted hosts are not checked in unit tests by default
        $app->bind(TrustHosts::class, fn($app) => new class($app) extends TrustHosts {
            protected function shouldSpecifyTrustedHosts()
            {
                return true;
            }
        });

        return $app;
    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }


    protected function tearDown(): void
    {
        Request::setTrustedHosts([]);
        Request::setTrustedProxies([], -1);

        parent::tearDown();
    }


    /**
     * Rebuilds the application with multishop and the given domain for the shop routes
     */
    protected function routing(?string $domain = null): void
    {
        $this->multishop = true;
        $this->domain = $domain;

        // roll back the open transaction before the shared in-memory database is reused
        $this->callBeforeApplicationDestroyedCallbacks();
        $this->beforeApplicationDestroyedCallbacks = [];

        $this->refreshApplication();
        $this->refreshDatabase();
        $this->createTables();
    }


    /**
     * Creates the tables and columns from the Aimeos setup
     */
    protected function createTables(): void
    {
        if (!Schema::hasTable('mshop_locale_site')) {
            Schema::create('mshop_locale_site', function (Blueprint $table) {
                $table->id();
                $table->string('siteid');
                $table->string('code');
                $table->smallInteger('status')->default(1);
            });
        }

        if (!Schema::hasColumn('users', 'siteid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('siteid')->default('');
                $table->smallInteger('superuser')->default(0);
            });
        }
    }


    protected function sites(array $sites): void
    {
        foreach ($sites as $siteid => $code) {
            DB::table('mshop_locale_site')->insert(['siteid' => $siteid, 'code' => $code]);
        }
    }


    protected function resetUrl(User $user): string
    {
        $url = null;

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, &$url) {
            $url = $notification->toMail($user)->actionUrl;
            return true;
        });

        return $url;
    }


    public function test_forwarded_host_from_proxy_is_not_used_in_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->withHeaders(['X-Forwarded-Host' => 'evil.example', 'X-Forwarded-Proto' => 'https'])
            ->post('https://shop.test/forgot-password', ['email' => $user->email]);

        $this->assertStringStartsWith('https://shop.test/', $this->resetUrl($user));
    }

    public function test_untrusted_host_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('http://evil.example/forgot-password', ['email' => $user->email]);
        $this->post('http://10.0.0.1.evil.example/forgot-password', ['email' => $user->email]);

        Notification::assertNothingSent();
    }

    public function test_configured_trusted_host_is_accepted(): void
    {
        Notification::fake();

        config(['app.trusted_hosts' => 'www.example.org']);
        $user = User::factory()->create();

        $this->post('http://www.example.org/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_path_routing_uses_site_of_user(): void
    {
        $this->routing();
        Notification::fake();
        $this->sites(['1.2.' => 'vendor', '1.3.' => 'evil']);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('https://shop.test/evil/forgot-password', ['email' => $user->email]);

        $this->assertStringStartsWith('https://shop.test/vendor/reset-password/', $this->resetUrl($user));
    }

    public function test_domain_routing_uses_site_of_user(): void
    {
        $this->routing('{site}');
        Notification::fake();
        $this->sites(['1.' => 'shop.test', '1.2.' => 'victim.example', '1.3.' => 'evil.example']);

        $admin = User::factory()->create(['siteid' => '1.', 'superuser' => 1]);
        $vendor = User::factory()->create(['siteid' => '1.2.']);

        foreach ([$admin, $vendor] as $user) {
            $this->post('http://evil.example/forgot-password', ['email' => $user->email]);
        }

        $this->assertSame('shop.test', parse_url($this->resetUrl($admin), PHP_URL_HOST));
        $this->assertSame('victim.example', parse_url($this->resetUrl($vendor), PHP_URL_HOST));
    }

    public function test_subdomain_routing_uses_site_of_user(): void
    {
        $this->routing('{site}.marketplace.example');
        Notification::fake();
        $this->sites(['1.2.' => 'victim', '1.3.' => 'evil']);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('http://evil.marketplace.example/forgot-password', ['email' => $user->email]);

        $this->assertSame('victim.marketplace.example', parse_url($this->resetUrl($user), PHP_URL_HOST));
    }

    public function test_user_without_active_site_uses_default_site(): void
    {
        $this->routing('{site}');
        Notification::fake();
        config(['shop.mshop.locale.site' => 'shop.test']);
        DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'inactive.example', 'status' => 0]);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('https://shop.test/forgot-password', ['email' => $user->email]);

        $this->assertSame('shop.test', parse_url($this->resetUrl($user), PHP_URL_HOST));
    }

    public function test_domain_routing_rejects_unknown_hosts(): void
    {
        $this->routing('{site}');
        Notification::fake();
        $this->sites(['1.2.' => 'myshop.example']);
        DB::table('mshop_locale_site')->insert(['siteid' => '1.3.', 'code' => 'inactive.example', 'status' => 0]);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('http://unknown.example/forgot-password', ['email' => $user->email]);
        $this->post('http://inactive.example/forgot-password', ['email' => $user->email]);
        Notification::assertNothingSent();

        $this->post('http://myshop.example/forgot-password', ['email' => $user->email]);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_subdomain_routing_rejects_unknown_hosts(): void
    {
        $this->routing('{site}.marketplace.example');
        Notification::fake();
        $this->sites(['1.2.' => 'vendor']);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('http://unknown.marketplace.example/forgot-password', ['email' => $user->email]);
        $this->post('http://vendor.evil.example/forgot-password', ['email' => $user->email]);
        Notification::assertNothingSent();

        $this->post('http://vendor.marketplace.example/forgot-password', ['email' => $user->email]);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_verification_link_uses_site_of_user(): void
    {
        $this->routing('{site}.marketplace.example');
        $this->sites(['1.2.' => 'vendor']);
        $user = User::factory()->unverified()->create(['siteid' => '1.2.']);

        $url = (new VerifyEmail)->toMail($user)->actionUrl;

        $this->assertSame('vendor.marketplace.example', parse_url($url, PHP_URL_HOST));
        $this->assertTrue(URL::hasValidSignature(Request::create($url)));
    }

    public function test_domain_check_with_domains(): void
    {
        $this->routing('{site}');
        $this->sites(['1.2.' => 'myshop.example']);
        DB::table('mshop_locale_site')->insert(['siteid' => '1.3.', 'code' => 'inactive.example', 'status' => 0]);

        $this->get('/domain-check?domain=myshop.example')->assertOk();
        $this->get('/domain-check?domain=MyShop.Example')->assertOk();
        $this->get('/domain-check?domain=www.myshop.example')->assertNotFound();
        $this->get('/domain-check?domain=inactive.example')->assertNotFound();
        $this->get('/domain-check?domain=unknown.example')->assertNotFound();
        $this->get('/domain-check')->assertNotFound();
    }

    public function test_domain_check_with_subdomains(): void
    {
        $this->routing('{site}.marketplace.example');
        $this->sites(['1.2.' => 'vendor']);

        $this->get('/domain-check?domain=vendor.marketplace.example')->assertOk();
        $this->get('/domain-check?domain=unknown.marketplace.example')->assertNotFound();
        $this->get('/domain-check?domain=vendor.evil.example')->assertNotFound();
        $this->get('/domain-check?domain=vendor')->assertNotFound();
    }
}
