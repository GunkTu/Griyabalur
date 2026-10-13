<?php

namespace Tests\Feature;

use App\Models\PageSection;
use App\Models\User;
use App\Support\Media;
use App\Support\Site;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    private $cover = null;

    private const PAGE = 'App\Filament\Resources\PageSections\Pages\ManagePageSections';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        // tests never reach YouTube or Vimeo: every request gets $this->cover, or a 404 when none is set
        Http::fake(fn () => $this->cover ?? Http::response('', 404));
        $this->cleanThumbnails();
    }

    protected function tearDown(): void
    {
        $this->cleanThumbnails();
        parent::tearDown();
    }

    private function cleanThumbnails(): void
    {
        Media::delete('video/thumbs/youtube-dQw4w9WgXcQ.webp');
        @unlink(storage_path('framework/cache/thumb-miss-youtube-dQw4w9WgXcQ'));
    }

    private function fakeCover(): string
    {
        $img = imagecreatetruecolor(1280, 720);
        for ($y = 0; $y < 720; $y += 6) {
            imagefilledrectangle($img, 0, $y, 1280, $y + 5, imagecolorallocate($img, ($y * 7) % 255, ($y * 3) % 255, 90));
        }
        ob_start();
        imagejpeg($img, null, 90);

        return ob_get_clean();
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

    public function test_a_youtube_link_gets_its_cover_image_fetched_once_and_kept_on_our_server(): void
    {
        $this->cover = Http::response($this->fakeCover(), 200, ['Content-Type' => 'image/jpeg']);
        $this->section()->update(['video_url' => ['id' => 'https://youtu.be/dQw4w9WgXcQ', 'en' => 'https://youtu.be/dQw4w9WgXcQ']]);

        $html = $this->get('/en/')->assertSee('class="video__poster"', false)->getContent();
        $this->assertStringContainsString('/media/video/thumbs/youtube-dQw4w9WgXcQ.webp', $html);
        $this->assertStringNotContainsString('ytimg.com', $html); // visitors never contact YouTube before pressing play
        $this->assertSame(1280, getimagesize(public_path('media/video/thumbs/youtube-dQw4w9WgXcQ.webp'))[0]);

        $this->get('/id/')->assertSee('video__poster', false);
        Http::assertSentCount(1); // fetched once, then served from our own folder
    }

    public function test_when_the_cover_cannot_be_fetched_the_page_still_works_and_does_not_keep_retrying(): void
    {
        $this->section()->update(['video_url' => ['id' => 'https://youtu.be/dQw4w9WgXcQ', 'en' => 'https://youtu.be/dQw4w9WgXcQ']]);

        $this->get('/en/')->assertOk()->assertSee('video__play', false)->assertDontSee('video__poster', false);
        $this->get('/id/')->assertOk()->assertSee('video__play', false);

        Http::assertSentCount(2); // maxres and hq on the first try, nothing on the next visits
    }

    public function test_an_uploaded_cover_wins_over_the_fetched_one(): void
    {
        $this->cover = Http::response($this->fakeCover(), 200, ['Content-Type' => 'image/jpeg']);
        $this->section()->update(['video_url' => ['id' => 'https://youtu.be/dQw4w9WgXcQ', 'en' => 'https://youtu.be/dQw4w9WgXcQ'], 'image' => 'hero.webp', 'image_alt' => ['id' => 'Sampul', 'en' => 'Cover']]);

        $this->get('/en/')->assertSee('/media/hero.webp', false)->assertDontSee('thumbs/youtube', false);
        Http::assertNothingSent();
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
