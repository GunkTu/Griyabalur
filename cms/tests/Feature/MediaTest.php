<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\User;
use App\Support\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    private string $name = 'zz-test-image';

    protected function tearDown(): void
    {
        Media::delete("tests/{$this->name}.webp");
        @rmdir(public_path('media/tests'));
        @unlink(sys_get_temp_dir().'/'.$this->name.'.png');
        parent::tearDown();
    }

    public function test_a_large_upload_becomes_webp_with_smaller_copies(): void
    {
        $this->assertTrue(extension_loaded('gd'), 'PHP gd extension must be enabled');

        $src = sys_get_temp_dir().'/'.$this->name.'.png';
        $img = imagecreatetruecolor(3200, 2000);
        imagefilledrectangle($img, 0, 0, 3200, 2000, imagecolorallocate($img, 25, 59, 50));
        imagepng($img, $src);

        $path = Media::process($src, 'tests', $this->name);

        $this->assertSame("tests/{$this->name}.webp", $path);
        $this->assertSame(1920, getimagesize(public_path("media/{$path}"))[0]);
        $this->assertSame('image/webp', getimagesize(public_path("media/{$path}"))['mime']);
        $this->assertSame(960, getimagesize(public_path("media/tests/{$this->name}-960.webp"))[0]);
        $this->assertSame(480, getimagesize(public_path("media/tests/{$this->name}-480.webp"))[0]);
        $this->assertStringContainsString("{$this->name}-480.webp 480w", Media::srcset($path));
    }

    public function test_small_images_are_not_upscaled(): void
    {
        $src = sys_get_temp_dir().'/'.$this->name.'.png';
        imagepng(imagecreatetruecolor(600, 400), $src);

        $path = Media::process($src, 'tests', $this->name);

        $this->assertSame(600, getimagesize(public_path("media/{$path}"))[0]);
        $this->assertFileDoesNotExist(public_path("media/tests/{$this->name}-960.webp"));
        $this->assertFileExists(public_path("media/tests/{$this->name}-480.webp"));
    }

    public function test_a_file_that_is_not_an_image_is_rejected(): void
    {
        $src = sys_get_temp_dir().'/'.$this->name.'.png';
        file_put_contents($src, 'not an image');

        $this->expectException(\RuntimeException::class);
        Media::process($src, 'tests', $this->name);
    }

    public function test_a_photo_uploaded_in_the_admin_is_stored_as_optimized_webp(): void
    {
        $this->seed(\Database\Seeders\CmsSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test('App\Filament\Resources\GalleryItems\Pages\ManageGalleryItems')
            ->callAction('create', data: [
                'image' => UploadedFile::fake()->image('camera.jpg', 3000, 2000),
                'caption' => ['id' => 'Halaman', 'en' => 'Yard'],
                'alt' => ['id' => 'Halaman depan', 'en' => 'Front yard'],
                'category' => 'exterior',
                'permission_status' => 'not_needed',
                'status_id' => 'published',
                'status_en' => 'published',
            ])
            ->assertHasNoActionErrors();

        $path = GalleryItem::latest('id')->first()->image;
        $this->assertStringStartsWith('uploads/', $path);
        $this->assertStringEndsWith('.webp', $path);
        $this->assertSame(1920, getimagesize(public_path('media/'.$path))[0]);

        $this->get('/en/')->assertSee('/media/'.$path, false)->assertSee('480w', false);
        Media::delete($path);
    }
}
