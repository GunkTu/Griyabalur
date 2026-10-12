<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use Workflow;

    public const CATEGORIES = ['exterior', 'interior', 'facility', 'activity', 'team', 'product'];

    protected $bilingual = ['caption', 'alt'];

    /** Media rights (PRD FR-04, section 14): a photo goes live only when permission is on record. */
    public function publishBlockers(): array
    {
        return $this->permission_status === 'pending' ? ['photo permission is not recorded'] : [];
    }

    protected $guarded = [];

    protected $casts = ['caption' => 'array', 'alt' => 'array', 'captured_at' => 'date'];
}
