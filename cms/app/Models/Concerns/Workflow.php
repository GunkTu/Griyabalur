<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Validation\ValidationException;

/**
 * Per-locale editorial workflow (PRD section 10):
 * DRAFT -> CONTENT REVIEW -> MEDICAL/REGULATORY REVIEW -> APPROVED -> PUBLISHED, tracked separately for id and en.
 * Models using this trait list their required bilingual fields in $bilingual.
 */
trait Workflow
{
    public const LOCALES = ['id', 'en'];

    public const STATUSES = [
        'draft' => 'Draft',
        'content_review' => 'Content review',
        'medical_review' => 'Medical / regulatory review',
        'approved' => 'Approved',
        'published' => 'Published',
    ];

    public static function bootWorkflow(): void
    {
        static::saving(fn ($m) => $m->guardWorkflow());
        static::created(fn ($m) => ActivityLog::record('created', $m));
        static::updated(fn ($m) => ActivityLog::record('updated', $m, $m->getChanges()));
        static::deleted(fn ($m) => ActivityLog::record('deleted', $m));
    }

    public function bilingualFields(): array
    {
        return $this->bilingual ?? [];
    }

    public function scopePublishedIn($query, string $locale)
    {
        return $query->where("status_{$locale}", 'published');
    }

    /** Text of a bilingual field in one locale. Never falls back to the other language (no mixed text). */
    public function tr(string $field, string $locale): string
    {
        $value = $this->{$field};

        return is_array($value) ? trim((string) ($value[$locale] ?? '')) : '';
    }

    /** Required bilingual fields that are still empty for a locale. */
    public function missingFor(string $locale): array
    {
        return array_values(array_filter($this->bilingualFields(), fn ($f) => $this->tr($f, $locale) === ''));
    }

    /** Completeness per locale, 0 to 1 (used by the dashboard). */
    public function completeness(string $locale): float
    {
        $fields = $this->bilingualFields();

        return $fields === [] ? 1.0 : 1 - count($this->missingFor($locale)) / count($fields);
    }

    protected function guardWorkflow(): void
    {
        $user = auth()->user();

        foreach (self::LOCALES as $locale) {
            $col = "status_{$locale}";

            // An editor who changes the text of an approved or published locale sends it back to review.
            if ($user?->role === 'editor' && $this->exists && ! $this->isDirty($col) && in_array($this->{$col}, ['approved', 'published'], true)) {
                foreach ($this->bilingualFields() as $f) {
                    $old = (array) json_decode($this->getRawOriginal($f) ?? '[]', true);
                    if (trim((string) ($old[$locale] ?? '')) !== $this->tr($f, $locale)) {
                        $this->{$col} = 'content_review';
                        break;
                    }
                }
            }

            if (! $this->isDirty($col)) {
                continue;
            }

            if ($user?->role === 'editor' && ! in_array($this->{$col}, ['draft', 'content_review'], true)) {
                throw ValidationException::withMessages([$col => 'Editors can only set Draft or Content review.']);
            }

            if ($this->{$col} === 'published' && method_exists($this, 'publishBlockers') && ($blockers = $this->publishBlockers())) {
                throw ValidationException::withMessages([$col => 'Cannot publish: '.implode('; ', $blockers).'.']);
            }

            if (in_array($this->{$col}, ['approved', 'published'], true) && ($missing = $this->missingFor($locale))) {
                throw ValidationException::withMessages([$col => 'Cannot approve or publish '.strtoupper($locale).': '.implode(', ', $missing).' still empty.']);
            }
        }
    }
}
