<?php

namespace App\Filament\Resources\VisitSteps;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\VisitSteps\Pages\ManageVisitSteps;
use App\Filament\Support\Bi;
use App\Models\VisitStep;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VisitStepResource extends BaseResource
{
    protected static ?string $model = VisitStep::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'langkah kunjungan';

    protected static ?string $pluralModelLabel = 'Langkah kunjungan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Bi::text('title', 'Langkah'),
            Bi::text('body', 'Penjelasan', true),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Bi::workflow(['title', 'body']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Langkah')->state(fn ($record) => Bi::preview($record->title)),
                ...Bi::statusColumns(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageVisitSteps::route('/')];
    }
}
