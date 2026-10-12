<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Concept;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\PageSection;
use App\Models\ResearchEvidence;
use App\Models\VisitStep;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Editorial dashboard (PRD section 16): translation completeness and publication state per locale.
 */
class ContentCompleteness extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Kelengkapan konten per bahasa';

    protected function getStats(): array
    {
        $models = [PageSection::class, Concept::class, Activity::class, VisitStep::class, Facility::class, GalleryItem::class, ResearchEvidence::class, Faq::class];
        $stats = [];

        foreach (['id' => 'Bahasa Indonesia', 'en' => 'English'] as $loc => $name) {
            $total = $complete = $published = $inReview = 0;
            foreach ($models as $model) {
                foreach ($model::all() as $row) {
                    $total++;
                    $complete += $row->missingFor($loc) === [] ? 1 : 0;
                    $published += $row->{"status_{$loc}"} === 'published' ? 1 : 0;
                    $inReview += in_array($row->{"status_{$loc}"}, ['content_review', 'medical_review'], true) ? 1 : 0;
                }
            }
            $pct = $total ? round($complete / $total * 100) : 0;
            $stats[] = Stat::make($name, "{$pct}% lengkap")
                ->description("{$complete} dari {$total} konten terisi, {$published} tayang, {$inReview} menunggu review")
                ->color($pct === 100 ? 'success' : 'warning');
        }

        return $stats;
    }
}
