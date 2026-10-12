<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities;
use App\Filament\Resources\Concepts;
use App\Filament\Resources\Facilities;
use App\Filament\Resources\Faqs;
use App\Filament\Resources\GalleryItems;
use App\Filament\Resources\Locations;
use App\Filament\Resources\PageSections;
use App\Filament\Resources\SiteSettings;
use App\Filament\Resources\Users;
use App\Filament\Resources\VisitSteps;
use App\Models\Activity;
use App\Models\Concept;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Location;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\VisitStep;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_both_languages_render_with_their_own_text(): void
    {
        $this->get('/en/')->assertOk()->assertSee('lang="en-US"', false)->assertSee('See the facilities')->assertDontSee('Lihat fasilitas');
        $this->get('/id/')->assertOk()->assertSee('lang="id-ID"', false)->assertSee('Lihat fasilitas')->assertDontSee('See the facilities');
        $this->get('/fr/')->assertNotFound();
    }

    public function test_page_has_hreflang_pairs_and_no_em_dash(): void
    {
        foreach (['en', 'id'] as $l) {
            $html = $this->get("/{$l}/")->assertOk()->getContent();
            $this->assertStringContainsString('hreflang="id-ID"', $html);
            $this->assertStringContainsString('hreflang="en-US"', $html);
            $this->assertStringNotContainsString('—', $html);
        }
    }

    public function test_content_not_published_in_a_locale_is_hidden_there_only(): void
    {
        $faq = Faq::first();
        $question = $faq->question['en'];
        $faq->update(['status_en' => 'draft']);

        $this->get('/en/')->assertDontSee($question, false);
        $this->get('/id/')->assertSee($faq->question['id'], false);
    }

    public function test_publishing_is_blocked_when_the_translation_is_incomplete(): void
    {
        $faq = Faq::first();
        $faq->update(['status_en' => 'draft', 'answer' => ['id' => 'Jawaban', 'en' => '']]);

        $this->expectException(ValidationException::class);
        $faq->update(['status_en' => 'published']);
    }

    public function test_a_photo_without_recorded_permission_cannot_be_published(): void
    {
        $photo = GalleryItem::first();
        $photo->update(['permission_status' => 'pending', 'status_id' => 'draft']);

        $this->expectException(ValidationException::class);
        $photo->update(['status_id' => 'published']);
    }

    public function test_an_editor_cannot_approve_or_publish(): void
    {
        $this->actingAs($this->user('editor'));
        $faq = Faq::first();
        $faq->update(['status_en' => 'draft']);

        $this->expectException(ValidationException::class);
        $faq->update(['status_en' => 'published']);
    }

    public function test_an_editor_changing_published_text_sends_it_back_to_review(): void
    {
        $this->actingAs($this->user('editor'));
        $faq = Faq::first();
        $faq->update(['answer' => ['id' => $faq->answer['id'], 'en' => 'Changed text']]);

        $faq->refresh();
        $this->assertSame('content_review', $faq->status_en);
        $this->assertSame('published', $faq->status_id);
    }

    public function test_changes_are_written_to_the_audit_log(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin);
        Faq::first()->update(['sort' => 99]);

        $this->assertDatabaseHas('activity_logs', ['user_id' => $admin->id, 'subject_type' => 'Faq', 'action' => 'updated']);
    }

    public function test_admin_pages_follow_roles(): void
    {
        $pages = ['page-sections', 'concepts', 'activities', 'visit-steps', 'facilities', 'gallery-items', 'research-evidence', 'faqs'];
        $adminOnly = ['locations', 'site-settings', 'users', 'activity-logs'];

        $this->get('/admin/faqs')->assertRedirect('/admin/login');

        $this->actingAs($this->user('admin'));
        foreach ([...$pages, ...$adminOnly] as $p) {
            $this->get("/admin/{$p}")->assertOk();
        }
        $this->get('/admin')->assertOk();

        foreach (['editor', 'reviewer'] as $role) {
            $this->actingAs($this->user($role));
            foreach ($pages as $p) {
                $this->get("/admin/{$p}")->assertOk();
            }
            foreach ($adminOnly as $p) {
                $this->get("/admin/{$p}")->assertForbidden();
            }
        }
    }


    public function test_every_form_modal_opens_for_create_and_edit(): void
    {
        $this->actingAs($this->user('admin'));

        $resources = [
            [PageSections\Pages\ManagePageSections::class, PageSection::class, false],
            [Concepts\Pages\ManageConcepts::class, Concept::class, true],
            [Activities\Pages\ManageActivities::class, Activity::class, true],
            [VisitSteps\Pages\ManageVisitSteps::class, VisitStep::class, true],
            [Facilities\Pages\ManageFacilities::class, Facility::class, true],
            [GalleryItems\Pages\ManageGalleryItems::class, GalleryItem::class, true],
            [Faqs\Pages\ManageFaqs::class, Faq::class, true],
            [Locations\Pages\ManageLocations::class, Location::class, true],
            [SiteSettings\Pages\ManageSiteSettings::class, SiteSetting::class, false],
            [Users\Pages\ManageUsers::class, User::class, true],
        ];

        foreach ($resources as [$page, $model, $canCreate]) {
            $test = Livewire::test($page)->assertSuccessful();
            if ($canCreate) {
                $test->mountAction('create')->assertSuccessful();
            }
            Livewire::test($page)->mountTableAction('edit', $model::first())->assertSuccessful();
        }
    }

    public function test_research_evidence_can_be_created_and_then_shows_on_the_site(): void
    {
        $this->actingAs($this->user('admin'));
        $page = \App\Filament\Resources\ResearchEvidence\Pages\ManageResearchEvidence::class;

        Livewire::test($page)->mountAction('create')->assertSuccessful();

        Livewire::test($page)
            ->callAction('create', data: [
                'title' => 'Original Study Title',
                'authors' => 'A. Writer',
                'institution' => 'Example University',
                'year' => 2024,
                'study_type' => 'lab',
                'summary' => ['id' => 'Ringkasan', 'en' => 'Summary'],
                'limitations' => ['id' => 'Keterbatasan', 'en' => 'Limitations text'],
                'status_id' => 'published',
                'status_en' => 'published',
            ])
            ->assertHasNoActionErrors();

        $this->get('/en/')->assertSee('Original Study Title')->assertSee('Limitations text')->assertDontSee('No documents published yet');
        $this->get('/id/')->assertSee('Original Study Title')->assertSee('Keterbatasan');
    }

    public function test_an_admin_can_edit_a_faq_through_the_form(): void
    {
        $this->actingAs($this->user('admin'));
        $faq = Faq::first();

        Livewire::test('App\\Filament\\Resources\\Faqs\\Pages\\ManageFaqs')
            ->callTableAction('edit', $faq, data: [
                'question' => ['id' => 'Tanya baru', 'en' => 'New question'],
                'status_id' => 'published',
                'status_en' => 'published',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertSame('New question', $faq->refresh()->question['en']);
    }
}
