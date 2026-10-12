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

    public function test_local_development_keeps_using_the_request_host(): void
    {
        config(['app.url' => 'https://example.com']);
        (new AppServiceProvider($this->app))->useConfiguredUrl();

        $this->get('http://localhost:8000/admin')->assertRedirect('http://localhost:8000/admin/login');
    }
}
