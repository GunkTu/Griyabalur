<?php

namespace App\Filament\Resources\Facilities;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Facilities\Pages\ManageFacilities;
use App\Filament\Support\Bi;
use App\Models\Facility;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FacilityResource extends BaseResource
{
    protected static ?string $model = Facility::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'fasilitas';

    protected static ?string $pluralModelLabel = 'Fasilitas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Bi::text('title', 'Nama ruang / fasilitas'),
            Bi::text('caption', 'Keterangan singkat'),
            Bi::imageUpload('image', 'Foto (hanya kondisi yang benar-benar ada)'),
            Bi::text('alt', 'Teks alternatif foto (alt)'),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Toggle::make('active')->label('Tampilkan')->default(true),
            Bi::workflow(['title', 'caption', 'alt']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('media')->label('Foto')->square()->size(48),
                TextColumn::make('title')->label('Fasilitas')->state(fn ($record) => Bi::preview($record->title)),
                IconColumn::make('active')->label('Aktif')->boolean(),
                ...Bi::statusColumns(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageFacilities::route('/')];
    }
}
