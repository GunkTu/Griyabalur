<?php

namespace App\Filament\Resources\ResearchEvidence\Pages;

use App\Filament\Resources\ResearchEvidence\ResearchEvidenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResearchEvidence extends ManageRecords
{
    protected static string $resource = ResearchEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth('5xl'),
        ];
    }
}
