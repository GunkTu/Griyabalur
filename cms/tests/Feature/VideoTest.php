<?php

namespace Tests\Feature;

use App\Models\PageSection;
use App\Models\User;
use App\Support\Media;
use App\Support\Site;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    private const PAGE = 'App\Filament\Resources\PageSections\Pages\ManagePageSections';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
    }

    private function section(): PageSection
    {
        return PageSection::where('key', 'video')->firstOrFail();
    }

    private function saveVideo(array $data): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(self::PAGE)->callTableAction('edit', $this->section(), data: $data)->assertHasNoTableActionErrors();
    }

    public function test_the_video_section_is_hidden_until_a_video_is_added(): void
    {
        $this->get('/en/')->assertOk()->assertDontSee('id="video"', false);
        $this->get('/id/')->assertOk()->assertDontSee('id="video"', false);
    }

    public function test_each_language_shows_only_its_own_video(): void
    {
        $this->section()->update(['video_url' => ['id' => '', 'en' => 'https://youtu.be/dQw4w9WgXcQ']]);

        $this->get('/en/')->assertSee('id="video"', false)->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false)->assertSee('A short', false);
        $this->get('/id/')->assertDontSee('id="video"', false)->assertDontSee('youtube-nocookie', false);
    }

    public function test_the_player_is_not_loaded_before_the_click(): void
    {
        $this->section()->update(['video_url' => ['id' => 'https://vimeo.com/76979871', 'en' => 'https://vimeo.com/76979871']]);

        $html = $this->get('/id/')->getContent();
        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringContainsString('data-embed="https://player.vimeo.com/video/76979871?autoplay=1&amp;dnt=1"', $html);
    }

    public function test_an_uploaded_file_is_played_directly_and_wins_over_a_link(): void
    {
        $this->section()->update(['video_url' => ['id' => 'https://youtu.be/dQw4w9WgXcQ', 'en' => 'https://youtu.be/dQw4w9WgXcQ']]);

        $this->saveVideo(['video_file' => ['id' => UploadedFile::fake()->create('clip.mp4', 8, 'video/mp4'), 'en' => null]]);

        $file = $this->section()->video_file['id'];
        $this->assertStringStartsWith('video/', $file);
        $this->assertFileExists(public_path('media/'.$file));

        $id = $this->get('/id/')->assertSee('<video', false)->assertSee('/media/'.$file, false)->assertSee('video/mp4', false);
        $this->assertStringNotContainsString('data-embed', $id->getContent());
        $this->get('/en/')->assertSee('data-embed', false)->assertDontSee('<video', false); // English has only the link

        Media::delete($file);
    }

    public function test_only_youtube_and_vimeo_links_are_accepted_in_the_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(self::PAGE)
            ->callTableAction('edit', $this->section(), data: ['video_url' => ['id' => '', 'en' => 'https://evil.example/watch?v=dQw4w9WgXcQ']])
            ->assertHasTableActionErrors();

        $this->assertNull($this->section()->fresh()->video_url['en'] ?? null);
    }

    public function test_the_link_parser_accepts_known_forms_and_rejects_look_alikes(): void
    {
        foreach (['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'https://youtu.be/dQw4w9WgXcQ?t=5', 'https://www.youtube.com/shorts/dQw4w9WgXcQ', 'https://vimeo.com/76979871'] as $ok) {
            $this->assertNotNull(Site::embed($ok), $ok);
        }
        foreach (['https://youtube.com.evil.example/watch?v=dQw4w9WgXcQ', 'javascript:alert(1)', 'https://example.com/x.mp4', 'not a url', ''] as $bad) {
            $this->assertNull(Site::embed($bad), $bad);
        }
    }

    public function test_a_video_that_is_not_published_in_a_language_stays_hidden_there(): void
    {
        $this->section()->update(['video_url' => ['id' => 'https://youtu.be/dQw4w9WgXcQ', 'en' => 'https://youtu.be/dQw4w9WgXcQ'], 'status_en' => 'draft']);

        $this->get('/id/')->assertSee('id="video"', false);
        $this->get('/en/')->assertDontSee('id="video"', false);
    }
}
