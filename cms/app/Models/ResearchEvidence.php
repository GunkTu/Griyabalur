<?php

namespace App\Models;

use App\Models\Concerns\Workflow;
use Illuminate\Database\Eloquent\Model;

class ResearchEvidence extends Model
{
    use Workflow;

    protected $table = 'research_evidence';

    protected $bilingual = ['summary', 'limitations'];

    protected $guarded = [];

    protected $casts = ['summary' => 'array', 'limitations' => 'array'];
}
