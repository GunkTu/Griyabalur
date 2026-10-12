<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use GdImage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

/**
 * Uploaded photos are re-encoded as WebP (quality 82), capped at 1920px wide, with 480px and 960px
 * copies for responsive srcset. Re-encoding also drops EXIF data such as GPS position.
 * Files live in public/media; paths stored in the database are relative to that folder.
 *
 * Brand files (logo, favicon, share image) use the same folder under brand/.
 */
class Media
{
    public const MAX_WIDTH = 1920;

    public const VARIANTS = [480, 960];

    public const QUALITY = 82;

    public static function fromUpload(TemporaryUploadedFile $file): string
    {
        return self::process($file->getRealPath(), 'uploads', Str::lower(Str::random(20)));
    }

    /** Logo: SVG is kept (after a safety check), raster images become transparent WebP at most 640px wide. */
    public static function fromLogoUpload(TemporaryUploadedFile $file): string
    {
        $name = Str::lower(Str::random(20));

        return self::isSvg($file)
            ? self::storeSvg($file->getRealPath(), $name)
            : self::process($file->getRealPath(), 'brand', $name, 640, []);
    }

    /** Favicon: PNG, ICO, or a safe SVG, stored as uploaded. */
    public static function fromFaviconUpload(TemporaryUploadedFile $file): string
    {
        $name = Str::lower(Str::random(20));
        if (self::isSvg($file)) {
            return self::storeSvg($file->getRealPath(), $name);
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['png', 'ico'], true)) {
            throw new RuntimeException('Favicon must be PNG, ICO, or SVG.');
        }
        $dir = public_path('media/brand');
        is_dir($dir) || mkdir($dir, 0775, true);
        copy($file->getRealPath(), "{$dir}/{$name}.{$ext}");

        return "brand/{$name}.{$ext}";
    }

    /** Link-preview image: JPEG, 1200px wide (WhatsApp and other chat apps do not reliably read WebP). */
    public static function fromShareUpload(TemporaryUploadedFile $file): string
    {
        $img = self::load($file->getRealPath());
        $flat = imagecreatetruecolor(imagesx($img), imagesy($img));
        imagefill($flat, 0, 0, imagecolorallocate($flat, 255, 255, 255));
        imagecopy($flat, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
        if (imagesx($flat) > 1200) {
            $flat = imagescale($flat, 1200, -1, IMG_BICUBIC);
        }
        $dir = public_path('media/brand');
        is_dir($dir) || mkdir($dir, 0775, true);
        $name = Str::lower(Str::random(20));
        imagejpeg($flat, "{$dir}/{$name}.jpg", 85);

        return "brand/{$name}.jpg";
    }

    /** Converts one source image. Returns the main file path relative to public/media. */
    public static function process(string $source, string $dir, string $name, int $maxWidth = self::MAX_WIDTH, ?array $variants = null): string
    {
        $variants ??= self::VARIANTS;
        $img = self::load($source);
        $folder = public_path('media'.($dir !== '' ? '/'.$dir : ''));
        if (! is_dir($folder)) {
            mkdir($folder, 0775, true);
        }
        $prefix = $dir !== '' ? $dir.'/' : '';

        self::write($img, $maxWidth, "{$folder}/{$name}.webp");
        $width = imagesx($img);
        foreach ($variants as $w) {
            if ($width > $w) { // never upscale
                self::write($img, $w, "{$folder}/{$name}-{$w}.webp");
            }
        }

        return "{$prefix}{$name}.webp";
    }

    private static function load(string $source): GdImage
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            throw new RuntimeException('Ekstensi PHP "gd" belum aktif di server ini. Aktifkan extension=gd di php.ini lalu restart server (php artisan serve), atau minta penyedia hosting mengaktifkannya.');
        }

        $data = @file_get_contents($source);
        $img = $data === false ? false : @imagecreatefromstring($data);
        if (! $img) {
            throw new RuntimeException('The file is not a readable image.');
        }

        // Apply the camera orientation, because it is lost when the metadata is dropped.
        if (function_exists('exif_read_data') && str_starts_with($data, "\xFF\xD8")) {
            $orientation = @exif_read_data($source)['Orientation'] ?? 1;
            $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
            if ($angle) {
                $img = imagerotate($img, $angle, 0) ?: $img;
            }
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        return $img;
    }

    private static function write(GdImage $img, int $maxWidth, string $path): void
    {
        if (imagesx($img) > $maxWidth) {
            $img = imagescale($img, $maxWidth, -1, IMG_BICUBIC);
            imagealphablending($img, false);
            imagesavealpha($img, true); // keep transparency of logos
        }
        imagewebp($img, $path, self::QUALITY);
    }

    // ---- SVG ----

    private static function isSvg(TemporaryUploadedFile $file): bool
    {
        return strtolower($file->getClientOriginalExtension()) === 'svg' || str_contains((string) $file->getMimeType(), 'svg');
    }

    private static function storeSvg(string $source, string $name): string
    {
        $svg = (string) file_get_contents($source);
        if (! self::svgIsSafe($svg)) {
            throw new RuntimeException('This SVG contains scripts or external references and was rejected. Export a plain SVG and try again.');
        }
        $dir = public_path('media/brand');
        is_dir($dir) || mkdir($dir, 0775, true);
        file_put_contents("{$dir}/{$name}.svg", $svg);

        return "brand/{$name}.svg";
    }

    /**
     * An SVG can carry scripts and is served from our own origin, so anything that could run code
     * or pull in outside content is rejected rather than cleaned.
     */
    public static function svgIsSafe(string $svg): bool
    {
        if ($svg === '' || stripos($svg, '<!ENTITY') !== false || stripos($svg, '<!DOCTYPE') !== false && stripos($svg, '[') !== false) {
            return false;
        }
        $doc = new DOMDocument;
        $prev = libxml_use_internal_errors(true);
        $ok = $doc->loadXML($svg, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        if (! $ok || ! $doc->documentElement || strtolower($doc->documentElement->localName) !== 'svg') {
            return false;
        }

        foreach ($doc->getElementsByTagName('*') as $el) {
            /** @var DOMElement $el */
            if (in_array(strtolower($el->localName), ['script', 'foreignobject', 'iframe', 'object', 'embed', 'audio', 'video', 'animate', 'set'], true)) {
                return false;
            }
            foreach ($el->attributes as $attr) {
                $n = strtolower($attr->nodeName);
                $v = strtolower(trim($attr->nodeValue));
                if (str_starts_with($n, 'on')) {
                    return false;
                }
                if (in_array($n, ['href', 'xlink:href', 'src'], true) && ! str_starts_with($v, '#') && ! preg_match('#^data:image/(png|jpeg|webp);base64,#', $v)) {
                    return false;
                }
                if (str_contains($v, 'javascript:') || str_contains($v, 'url(http') || str_contains($v, '@import')) {
                    return false;
                }
            }
        }

        return true;
    }

    // ---- lookup and cleanup ----

    /** srcset for an image path, using only the variants that exist. */
    public static function srcset(?string $path): ?string
    {
        if (! $path || ! str_ends_with($path, '.webp')) {
            return null;
        }
        $base = substr($path, 0, -5);
        $set = [];
        foreach (self::VARIANTS as $w) {
            if (is_file(public_path("media/{$base}-{$w}.webp"))) {
                $set[] = '/media/'.$base."-{$w}.webp {$w}w";
            }
        }
        $size = @getimagesize(public_path('media/'.$path));
        if ($set && $size) {
            $set[] = '/media/'.$path.' '.$size[0].'w';
        }

        return $set ? implode(', ', $set) : null;
    }

    /** Removes the main file and its variants (used when a photo is replaced or deleted in the admin). */
    public static function delete(?string $path): void
    {
        if (! $path || str_contains($path, '..')) {
            return;
        }
        $files = [public_path('media/'.$path)];
        if (str_ends_with($path, '.webp')) {
            $base = substr($path, 0, -5);
            foreach (self::VARIANTS as $w) {
                $files[] = public_path("media/{$base}-{$w}.webp");
            }
        }
        foreach ($files as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
    }
}
