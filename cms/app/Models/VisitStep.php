<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class VisitStep extends Model
{
    use Workflow;

    protected $table = 'visit_steps';

    protected $bilingual = ['title', 'body'];

    protected $guarded = [];

    protected $casts = ['title' => 'array', 'body' => 'array'];
}
