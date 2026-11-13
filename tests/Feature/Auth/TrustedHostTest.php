<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\TrustHosts;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustedHostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://shop.test', 'app.shop_registration' => false]);

        // trusted hosts are not checked in unit tests by default
        $this->app->bind(TrustHosts::class, fn($app) => new class($app) extends TrustHosts {
            protected function shouldSpecifyTrustedHosts()
            {
                return !\App\Support\SiteDomain::enabled();
            }
        });

        // tables/columns created by the Aimeos setup
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

    protected function domainRouting(string $domain = '{site}'): void
    {
        config([
            'app.shop_multishop' => true,
            'shop.routes.default' => ['domain' => $domain, 'prefix' => 'shop'],
        ]);
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

    protected function tearDown(): void
    {
        Request::setTrustedHosts([]);
        Request::setTrustedProxies([], -1);

        parent::tearDown();
    }

    public function test_forwarded_host_from_proxy_is_not_used_in_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->withHeaders(['X-Forwarded-Host' => 'evil.example', 'X-Forwarded-Proto' => 'https'])
            ->post('https://shop.test/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $url = $notification->toMail($user)->actionUrl;

            $this->assertStringStartsWith('https://shop.test/', $url);
            return true;
        });
    }

    public function test_untrusted_host_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('http://evil.example/forgot-password', ['email' => $user->email]);

        Notification::assertNothingSent();
    }

    public function test_host_suffix_of_private_ip_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create();

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

    public function test_site_domain_is_accepted(): void
    {
        Notification::fake();

        $this->domainRouting();
        \DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'myshop.example']);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('http://myshop.example/forgot-password', ['email' => $user->email]);

        $this->assertStringStartsWith('https://myshop.example/', $this->resetUrl($user));
    }

    public function test_site_domain_requires_domain_routing(): void
    {
        Notification::fake();

        \DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'myshop.example']);
        $user = User::factory()->create();

        $this->post('http://myshop.example/forgot-password', ['email' => $user->email]);

        Notification::assertNothingSent();
    }

    public function test_registered_domain_cannot_steal_tokens_of_other_users(): void
    {
        Notification::fake();

        $this->domainRouting();
        config(['app.shop_registration' => true]);
        \DB::table('mshop_locale_site')->insert([
            ['siteid' => '1.', 'code' => 'default'],
            ['siteid' => '1.2.', 'code' => 'victim.example'],
            ['siteid' => '1.3.', 'code' => 'evil.example'],
        ]);

        $admin = User::factory()->create(['siteid' => '1.', 'superuser' => 1]);
        $customer = User::factory()->create(['siteid' => '1.']);
        $vendor = User::factory()->create(['siteid' => '1.2.']);

        foreach ([$admin, $customer, $vendor] as $user) {
            $this->post('http://evil.example/forgot-password', ['email' => $user->email]);
        }

        $this->assertStringStartsWith('https://shop.test/', $this->resetUrl($admin));
        $this->assertStringStartsWith('https://shop.test/', $this->resetUrl($customer));
        $this->assertStringStartsWith('https://victim.example/', $this->resetUrl($vendor));
    }

    public function test_verification_link_uses_site_domain(): void
    {
        $this->domainRouting();
        \DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'myshop.example']);
        $user = User::factory()->unverified()->create(['siteid' => '1.2.']);

        $url = (new \Illuminate\Auth\Notifications\VerifyEmail)->toMail($user)->actionUrl;

        $this->assertSame('myshop.example', parse_url($url, PHP_URL_HOST));
        $this->assertTrue(\Illuminate\Support\Facades\URL::hasValidSignature(\Illuminate\Http\Request::create($url)));
        $this->assertNotSame('myshop.example', parse_url(url('/'), PHP_URL_HOST));
    }

    public function test_subdomain_site_is_accepted(): void
    {
        Notification::fake();

        $this->domainRouting('{site}.marketplace.example');
        \DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'vendor']);
        $user = User::factory()->create(['siteid' => '1.2.']);

        $this->post('http://vendor.marketplace.example/forgot-password', ['email' => $user->email]);

        $this->assertStringStartsWith('https://vendor.marketplace.example/', $this->resetUrl($user));
    }

    public function test_subdomain_links_use_users_site(): void
    {
        Notification::fake();

        $this->domainRouting('{site}.marketplace.example');
        \DB::table('mshop_locale_site')->insert([
            ['siteid' => '1.', 'code' => 'default'],
            ['siteid' => '1.2.', 'code' => 'victim'],
            ['siteid' => '1.3.', 'code' => 'evil'],
        ]);

        $customer = User::factory()->create(['siteid' => '1.']);
        $vendor = User::factory()->create(['siteid' => '1.2.']);

        foreach ([$customer, $vendor] as $user) {
            $this->post('http://evil.marketplace.example/forgot-password', ['email' => $user->email]);
        }

        $this->assertStringStartsWith('https://shop.test/', $this->resetUrl($customer));
        $this->assertStringStartsWith('https://victim.marketplace.example/', $this->resetUrl($vendor));
    }

    public function test_domain_check_accepts_active_sites_only(): void
    {
        $this->domainRouting();
        \DB::table('mshop_locale_site')->insert([
            ['siteid' => '1.', 'code' => 'default', 'status' => 1],
            ['siteid' => '1.2.', 'code' => 'myshop.example', 'status' => 1],
            ['siteid' => '1.3.', 'code' => 'inactive.example', 'status' => 0],
            ['siteid' => '1.4.', 'code' => 'nodomain', 'status' => 1],
        ]);

        $this->get('/domain-check?domain=myshop.example')->assertOk();
        $this->get('/domain-check?domain=MyShop.Example')->assertOk();
        $this->get('/domain-check?domain=inactive.example')->assertNotFound();
        $this->get('/domain-check?domain=nodomain')->assertNotFound();
        $this->get('/domain-check?domain=default')->assertNotFound();
        $this->get('/domain-check?domain=unknown.example')->assertNotFound();
        $this->get('/domain-check')->assertNotFound();
    }

    public function test_domain_check_with_subdomains(): void
    {
        $this->domainRouting('{site}.marketplace.example');
        \DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'vendor']);

        $this->get('/domain-check?domain=vendor.marketplace.example')->assertOk();
        $this->get('/domain-check?domain=unknown.marketplace.example')->assertNotFound();
        $this->get('/domain-check?domain=vendor.evil.example')->assertNotFound();
        $this->get('/domain-check?domain=vendor')->assertNotFound();
        $this->get('/domain-check?domain=.marketplace.example')->assertNotFound();
    }

    public function test_domain_check_without_domain_routing(): void
    {
        \DB::table('mshop_locale_site')->insert(['siteid' => '1.2.', 'code' => 'myshop.example']);

        $this->get('https://shop.test/domain-check?domain=myshop.example')->assertNotFound();
    }
}
