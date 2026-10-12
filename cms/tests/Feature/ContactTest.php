<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\ResearchEvidence;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_saved_in_the_admin_shows_on_both_pages(): void
    {
        $this->seed(CmsSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test('App\Filament\Resources\Locations\Pages\ManageLocations')
            ->callTableAction('edit', Location::first(), data: [
                'name' => 'Griya Balur',
                'address' => 'Jl. Contoh No. 1, Malang',
                'phone' => '+62 812 0000 0000',
                'wa_number' => '628120000000',
                'maps_url' => 'https://maps.example.com/x',
                'hours' => ['id' => 'Setiap hari 09.00-17.00', 'en' => 'Daily 9am-5pm'],
                'guidance' => ['id' => 'Masuk dari gerbang utama', 'en' => 'Enter by the main gate'],
                'active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->get('/en/')->assertSee('Jl. Contoh No. 1, Malang')->assertSee('Daily 9am-5pm')->assertSee('https://wa.me/628120000000', false)->assertSee('https://maps.example.com/x', false);
        $this->get('/id/')->assertSee('Jl. Contoh No. 1, Malang')->assertSee('Setiap hari 09.00-17.00');
    }

    public function test_admin_tables_show_readable_titles_not_commas(): void
    {
        $this->seed(CmsSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get('/admin/page-sections')->assertSee('Tentang kami')->assertSee('A place to get to know *BALUR*.', false)->assertSee('Hero (paling atas)');
        $this->get('/admin/faqs')->assertSee('Where are you and when are you open?')->assertSee('Tayang');
        $this->get('/admin/concepts')->assertSee('Balance with nature');
    }

    public function test_new_research_stays_hidden_until_it_is_published(): void
    {
        $this->seed(CmsSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'reviewer']));
        $page = 'App\Filament\Resources\ResearchEvidence\Pages\ManageResearchEvidence';

        Livewire::test($page)->callAction('create', data: [
            'title' => 'Draft Journal', 'authors' => 'B. Author', 'year' => 2023, 'study_type' => 'lab',
            'summary' => ['id' => 'Ringkasan', 'en' => 'Summary'],
            'limitations' => ['id' => 'Batas', 'en' => 'Limits'],
            'status_id' => 'draft', 'status_en' => 'draft',
        ])->assertHasNoActionErrors();

        $this->get('/en/')->assertDontSee('Draft Journal');

        Livewire::test($page)->callTableAction('publish', ResearchEvidence::first());

        $this->get('/en/')->assertSee('Draft Journal');
        $this->get('/id/')->assertSee('Draft Journal');
    }
}
