<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use Workflow;

    /** Section keys in page order. */
    public const KEYS = ['hero', 'about', 'balur', 'video', 'activities', 'visit', 'facilities', 'gallery', 'research', 'faq', 'contact'];

    /** Bilingual fields each section needs before a locale can be approved or published. */
    public const REQUIRED = [
        'hero' => ['lead', 'cta1', 'cta2'],
        'about' => ['headline', 'body'],
        'balur' => ['headline', 'lead', 'note'],
        'video' => ['headline', 'lead'],
        'activities' => ['headline', 'lead'],
        'visit' => ['headline', 'note'],
        'facilities' => ['headline', 'lead'],
        'gallery' => ['headline', 'lead'],
        'research' => ['headline', 'lead'],
        'faq' => ['headline'],
        'contact' => ['headline', 'lead'],
    ];

    protected $guarded = [];

    protected $casts = [
        'headline' => 'array', 'lead' => 'array', 'body' => 'array', 'note' => 'array',
        'cta1' => 'array', 'cta2' => 'array', 'image_alt' => 'array',
        'video_url' => 'array', 'video_file' => 'array',
    ];

    public function bilingualFields(): array
    {
        return self::REQUIRED[$this->key] ?? ['headline'];
    }
}
