<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use Workflow;

    protected $bilingual = ['title', 'summary'];

    protected $guarded = [];

    protected $casts = ['title' => 'array', 'summary' => 'array', 'public_note' => 'array'];
}
