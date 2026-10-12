<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Workflow;

    protected $bilingual = ['question', 'answer'];

    protected $guarded = [];

    protected $casts = ['question' => 'array', 'answer' => 'array'];
}
