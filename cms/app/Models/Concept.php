<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class Concept extends Model
{
    use Workflow;

    protected $bilingual = ['title', 'body'];

    protected $guarded = [];

    protected $casts = ['title' => 'array', 'body' => 'array'];
}
