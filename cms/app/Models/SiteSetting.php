<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'seo' => 'array',
        'socials' => 'array',
        'footer' => 'array',      // {disclaimer:{id,en}, privacy_body:{id,en}, wa_message:{id,en}}
        'analytics' => 'array',   // {plausible_domain, ga4_id}
        'logo_show_name' => 'boolean',
    ];

    /** Social networks shown in the footer, in order. Key => label. */
    public const SOCIALS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'website' => 'Website',
    ];

    public static function current(): self
    {
        return self::first() ?? new self(['brand_name' => 'Griya Balur Bukian Mertasari']);
    }

    /** Editable footer text for a locale, with the built-in text as fallback. */
    public function footerText(string $key, string $locale, string $fallback): string
    {
        $value = trim((string) ($this->footer[$key][$locale] ?? ''));

        return $value !== '' ? $value : $fallback;
    }

    protected static function booted(): void
    {
        static::updated(fn ($m) => ActivityLog::record('updated', $m, $m->getChanges()));
    }
}
