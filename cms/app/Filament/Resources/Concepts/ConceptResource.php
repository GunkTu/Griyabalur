<?php

namespace App\Filament\Resources\Concepts;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Concepts\Pages\ManageConcepts;
use App\Filament\Support\Bi;
use App\Models\Concept;
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

class ConceptResource extends BaseResource
{
    protected static ?string $model = Concept::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'konsep BALUR';

    protected static ?string $pluralModelLabel = 'Konsep BALUR';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Bi::text('title', 'Nama konsep'),
            Bi::text('body', 'Penjelasan', true),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Bi::workflow(['title', 'body']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Konsep')->state(fn ($record) => Bi::preview($record->title)),
                ...Bi::statusColumns(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageConcepts::route('/')];
    }
}
