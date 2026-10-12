<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['changes' => 'array', 'created_at' => 'datetime'];

    public static function record(string $action, Model $subject, array $changes = []): void
    {
        unset($changes['updated_at'], $changes['created_at']);

        self::create([
            'user_id' => auth()->id(),
            'subject_type' => class_basename($subject),
            'subject_id' => $subject->getKey(),
            'subject_label' => self::label($subject),
            'action' => $action,
            'changes' => $changes ?: null,
        ]);
    }

    private static function label(Model $m): string
    {
        foreach (['title', 'question', 'caption', 'headline', 'name', 'key'] as $f) {
            $v = $m->{$f} ?? null;
            if (is_array($v)) {
                $v = $v['en'] ?? $v['id'] ?? '';
            }
            if ($v) {
                return mb_strimwidth((string) $v, 0, 80, '...');
            }
        }

        return class_basename($m).' #'.$m->getKey();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
