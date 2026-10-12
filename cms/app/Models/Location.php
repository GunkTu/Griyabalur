<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    protected $casts = ['guidance' => 'array', 'hours' => 'array', 'active' => 'boolean'];

    protected static function booted(): void
    {
        static::created(fn ($m) => ActivityLog::record('created', $m));
        static::updated(fn ($m) => ActivityLog::record('updated', $m, $m->getChanges()));
        static::deleted(fn ($m) => ActivityLog::record('deleted', $m));
    }
}
