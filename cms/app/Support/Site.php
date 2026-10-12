<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/** View helpers for the public site. */
class Site
{
    /** Inline Phosphor Light icon (public/assets/icons/*.svg) so it inherits currentColor. */
    public static function icon(string $name): HtmlString
    {
        $file = public_path("assets/icons/{$name}.svg");
        $svg = is_file($file) ? file_get_contents($file) : '';

        return new HtmlString(str_replace('<svg ', '<svg aria-hidden="true" focusable="false" ', $svg));
    }

    /** "*word*" becomes italic emphasis in the same typeface (one italic word per major heading). */
    public static function hl(string $text): HtmlString
    {
        return new HtmlString(preg_replace('/\*(.+?)\*/', '<em>$1</em>', e($text)));
    }

    public static function plain(string $text): string
    {
        return str_replace('*', '', $text);
    }

    /** Paragraphs separated by blank lines. */
    public static function paragraphs(string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $text))));
    }

    public static function media(?string $path): ?string
    {
        return $path ? '/media/'.ltrim($path, '/') : null;
    }

    /** Pixel size of an uploaded image, so the browser can reserve space (no layout shift). */
    public static function size(?string $path): array
    {
        static $cache = [];
        if (! $path) {
            return [4, 3];
        }

        return $cache[$path] ??= (function () use ($path) {
            $file = public_path('media/'.ltrim($path, '/'));
            $info = is_file($file) ? @getimagesize($file) : false;

            return $info ? [$info[0], $info[1]] : [4, 3];
        })();
    }

    public static function img(?string $path, string $alt, string $loading = 'lazy', string $extra = '', string $sizes = '100vw'): HtmlString
    {
        if (! $path) {
            return new HtmlString('');
        }
        [$w, $h] = self::size($path);
        $srcset = Media::srcset($path);

        return new HtmlString(sprintf(
            '<img src="%s"%s alt="%s" width="%d" height="%d" loading="%s" decoding="async" %s>',
            e(self::media($path)),
            $srcset ? ' srcset="'.e($srcset).'" sizes="'.e($sizes).'"' : '',
            e($alt), $w, $h, $loading, $extra
        ));
    }

    /**
     * Turns a YouTube or Vimeo link into an embed address, or null for anything else.
     * YouTube uses the privacy-enhanced domain and Vimeo asks not to track; the player only loads after a click.
     *
     * @return array{provider: string, id: string, src: string}|null
     */
    public static function embed(?string $url): ?array
    {
        $parts = parse_url(trim((string) $url));
        if (! $parts || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true) || empty($parts['host'])) {
            return null;
        }
        $host = preg_replace('/^(www\.|m\.)/', '', strtolower($parts['host']));
        $path = $parts['path'] ?? '';

        $youtubeId = null;
        if ($host === 'youtu.be') {
            $youtubeId = substr(ltrim($path, '/'), 0, 11);
        } elseif (in_array($host, ['youtube.com', 'music.youtube.com', 'youtube-nocookie.com'], true)) {
            if ($path === '/watch') {
                parse_str($parts['query'] ?? '', $q);
                $youtubeId = (string) ($q['v'] ?? '');
            } elseif (preg_match('#^/(embed|shorts|live)/([\w-]{11})#', $path, $m)) {
                $youtubeId = $m[2];
            }
        }
        if ($youtubeId !== null && preg_match('/^[\w-]{11}$/', $youtubeId)) {
            return ['provider' => 'youtube', 'id' => $youtubeId, 'src' => "https://www.youtube-nocookie.com/embed/{$youtubeId}?autoplay=1&rel=0&modestbranding=1"];
        }

        if (in_array($host, ['vimeo.com', 'player.vimeo.com'], true) && preg_match('#/(\d{6,12})(?:/|$)#', $path, $m)) {
            return ['provider' => 'vimeo', 'id' => $m[1], 'src' => "https://player.vimeo.com/video/{$m[1]}?autoplay=1&dnt=1"];
        }

        return null;
    }

    /** MIME type for an uploaded video file. */
    public static function videoType(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'webm' ? 'video/webm' : 'video/mp4';
    }

    /** Identity motif: terrace contour lines. Deterministic, so pages are stable between requests. */
    public static function contour(int $lines, int $seed): HtmlString
    {
        $w = 1200;
        $h = 420;
        $d = '';
        for ($i = 0; $i < $lines; $i++) {
            $y0 = 30 + ($i * ($h - 60)) / ($lines - 1);
            $a1 = 16 + (($i * 7 + $seed) % 5) * 3;
            $a2 = 6 + (($i * 3 + $seed) % 4) * 2;
            $p1 = $seed + $i * 0.45;
            $p2 = $seed * 1.7 + $i * 0.9;
            $path = '';
            for ($x = 0; $x <= $w; $x += 12) {
                $y = $y0 + $a1 * sin($x / 150 + $p1) + $a2 * sin($x / 57 + $p2);
                $path .= ($x === 0 ? 'M' : 'L').$x.' '.number_format($y, 1, '.', '');
            }
            $d .= '<path d="'.$path.'"/>';
        }

        return new HtmlString('<svg class="contour" viewBox="0 0 '.$w.' '.$h.'" preserveAspectRatio="none" aria-hidden="true" focusable="false">'.$d.'</svg>');
    }
}
