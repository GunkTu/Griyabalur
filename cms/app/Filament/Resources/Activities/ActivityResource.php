<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ManageActivities;
use App\Filament\Resources\BaseResource;
use App\Filament\Support\Bi;
use App\Models\Activity;
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

class ActivityResource extends BaseResource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'kegiatan';

    protected static ?string $pluralModelLabel = 'Kegiatan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Bi::text('title', 'Nama kegiatan'),
            Bi::text('summary', 'Ringkasan (non-preskriptif, tanpa klaim kesembuhan)', true),
            Bi::text('public_note', 'Keterangan status yang tayang (opsional, mis. Menunggu tinjauan)'),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Bi::workflow(['title', 'summary']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Kegiatan')->state(fn ($record) => Bi::preview($record->title)),
                ...Bi::statusColumns(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageActivities::route('/')];
    }
}
