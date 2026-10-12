<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Media;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private const PAGE = 'App\Filament\Resources\SiteSettings\Pages\ManageSiteSettings';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function save(array $data): void
    {
        Livewire::test(self::PAGE)->callTableAction('edit', SiteSetting::first(), data: $data + ['brand_name' => 'Griya Balur Bukian Mertasari'])->assertHasNoTableActionErrors();
    }

    public function test_footer_text_social_links_and_analytics_reach_the_site(): void
    {
        $this->save([
            'footer' => ['disclaimer' => ['id' => 'Disclaimer baru', 'en' => 'New disclaimer'], 'privacy_body' => ['id' => '', 'en' => ''], 'wa_message' => ['id' => 'Halo Griya', 'en' => 'Hi Griya']],
            'socials' => ['instagram' => 'https://instagram.com/griyabalur'],
            'analytics' => ['plausible_domain' => 'griyabalur.com', 'ga4_id' => 'G-ABC123XYZ9'],
        ]);

        $en = $this->get('/en/')->assertOk()->assertSee('New disclaimer')->assertSee('https://instagram.com/griyabalur', false)->assertSee('data-domain="griyabalur.com"', false)->assertSee('G-ABC123XYZ9', false);
        $this->get('/id/')->assertSee('Disclaimer baru')->assertDontSee('New disclaimer');
        $this->assertStringContainsString('"sameAs"', $en->getContent());
    }

    public function test_an_uploaded_logo_replaces_the_default_mark(): void
    {
        $this->get('/en/')->assertSee('brand__mark', false)->assertDontSee('brand__logo', false);

        $this->save(['logo' => UploadedFile::fake()->image('logo.png', 1600, 600), 'logo_show_name' => false]);

        $logo = SiteSetting::first()->logo;
        $this->assertStringStartsWith('brand/', $logo);
        $this->assertLessThanOrEqual(640, getimagesize(public_path('media/'.$logo))[0]);
        $this->get('/en/')->assertSee('brand__logo', false)->assertDontSee('brand__mark', false)->assertDontSee('brand__name', false);

        Media::delete($logo);
    }

    public function test_a_plain_svg_logo_is_kept_and_a_scripted_one_is_rejected(): void
    {
        $this->assertTrue(Media::svgIsSafe('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="4" fill="#193B32"/></svg>'));
        $this->assertFalse(Media::svgIsSafe('<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'));
        $this->assertFalse(Media::svgIsSafe('<svg xmlns="http://www.w3.org/2000/svg"><rect onload="alert(1)" width="1" height="1"/></svg>'));
        $this->assertFalse(Media::svgIsSafe('<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript:alert(1)"><rect width="1" height="1"/></a></svg>'));
        $this->assertFalse(Media::svgIsSafe('<svg xmlns="http://www.w3.org/2000/svg"><image href="https://evil.example/x.png"/></svg>'));
        $this->assertFalse(Media::svgIsSafe('<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY x "y">]><svg xmlns="http://www.w3.org/2000/svg"/>'));
        $this->assertFalse(Media::svgIsSafe('not xml at all'));
    }

    public function test_the_logo_upload_form_stores_a_safe_svg(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="4"/></svg>';
        $this->save(['logo' => UploadedFile::fake()->createWithContent('logo.svg', $svg)]);

        $logo = SiteSetting::first()->logo;
        $this->assertStringEndsWith('.svg', $logo);
        $this->get('/en/')->assertSee('/media/'.$logo, false);

        Media::delete($logo);
    }

    public function test_the_signed_in_user_can_open_their_profile_page(): void
    {
        $this->get('/admin/profile')->assertOk();
    }

    public function test_the_default_site_still_renders_without_any_settings(): void
    {
        $this->get('/en/')->assertOk()->assertSee('/assets/favicon.svg', false)->assertDontSee('plausible.io', false);
    }
}
