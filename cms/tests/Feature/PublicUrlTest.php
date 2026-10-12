<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PublicUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        URL::forceRootUrl(null);
        URL::forceScheme(null);
        parent::tearDown();
    }

    private function productionAt(string $url): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => $url]);
        (new AppServiceProvider($this->app))->useConfiguredUrl();
    }

    public function test_links_use_the_public_address_even_when_the_host_header_is_localhost(): void
    {
        $this->productionAt('https://pv-griyabalurmertasari.thegt.my.id');

        $this->get('http://localhost/admin')->assertRedirect('https://pv-griyabalurmertasari.thegt.my.id/admin/login');
    }

    public function test_the_language_page_still_renders_behind_a_proxy_with_a_foreign_host(): void
    {
        $this->seed(CmsSeeder::class);
        $this->productionAt('https://pv-griyabalurmertasari.thegt.my.id');

        $this->get('http://192.168.10.121:8080/en/', ['X-Forwarded-Proto' => 'https'])->assertOk()->assertSee('See the facilities');
    }


    public function test_a_tunnel_that_calls_the_origin_as_localhost_still_logs_in_to_the_public_address(): void
    {
        $this->productionAt('https://pv-griyabalurmertasari.thegt.my.id');

        // what Cloudflare's tunnel sends: Host localhost:8080, the public name only in X-Forwarded-Host
        $headers = ['X-Forwarded-Host' => 'pv-griyabalurmertasari.thegt.my.id', 'X-Forwarded-Proto' => 'https'];
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])->get('http://localhost:8080/admin', $headers)
            ->assertRedirect('https://pv-griyabalurmertasari.thegt.my.id/admin/login');

        // the page the visitor wanted is remembered under the public address, not under localhost:8080
        $this->assertSame('https://pv-griyabalurmertasari.thegt.my.id/admin', session('url.intended'));
    }

    public function test_forwarded_host_is_ignored_when_it_does_not_come_from_the_local_tunnel(): void
    {
        $this->productionAt('https://pv-griyabalurmertasari.thegt.my.id');

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->get('http://192.168.10.121:8080/admin', ['X-Forwarded-Host' => 'evil.example', 'X-Forwarded-Proto' => 'https'])
            ->assertRedirect('https://pv-griyabalurmertasari.thegt.my.id/admin/login');

        $this->assertSame('http://192.168.10.121:8080/admin', session('url.intended'));
    }

    public function test_local_development_keeps_using_the_request_host(): void
    {
        config(['app.url' => 'https://example.com']);
        (new AppServiceProvider($this->app))->useConfiguredUrl();

        $this->get('http://localhost:8000/admin')->assertRedirect('http://localhost:8000/admin/login');
    }
}
