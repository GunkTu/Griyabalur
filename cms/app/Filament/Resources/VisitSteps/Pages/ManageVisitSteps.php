<?php

namespace App\Filament\Resources\VisitSteps\Pages;

use App\Filament\Resources\VisitSteps\VisitStepResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageVisitSteps extends ManageRecords
{
    protected static string $resource = VisitStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth('5xl'),
        ];
    }
}
