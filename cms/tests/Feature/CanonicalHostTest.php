<?php

namespace Tests\Feature;

use Tests\TestCase;

class CanonicalHostTest extends TestCase
{
    private function production(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'http://192.168.10.121:8080']);
    }

    public function test_another_host_name_is_sent_to_the_configured_address(): void
    {
        $this->production();

        $this->get('http://localhost/admin/login')->assertStatus(308)->assertRedirect('http://192.168.10.121:8080/admin/login');
        $this->get('http://192.168.10.121/en/')->assertStatus(308)->assertRedirect('http://192.168.10.121:8080/en');
    }

    public function test_the_configured_address_is_served_normally(): void
    {
        $this->production();

        $this->get('http://192.168.10.121:8080/admin/login')->assertOk();
    }

    public function test_local_development_is_never_redirected(): void
    {
        config(['app.url' => 'http://example.test']);

        $this->get('http://localhost/admin/login')->assertOk();
    }
}
