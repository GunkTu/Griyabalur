<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use Workflow;

    protected $bilingual = ['title', 'caption', 'alt'];

    protected $guarded = [];

    protected $casts = ['title' => 'array', 'caption' => 'array', 'alt' => 'array', 'active' => 'boolean'];
}
