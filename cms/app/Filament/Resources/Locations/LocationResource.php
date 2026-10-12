<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Locations\Pages\ManageLocations;
use App\Filament\Support\Bi;
use App\Models\Location;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Official address, WhatsApp, and Maps data (PRD FR-09). Admin only: a wrong number sends visitors to the wrong place. */
class LocationResource extends BaseResource
{
    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Situs';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'lokasi';

    protected static ?string $pluralModelLabel = 'Lokasi & kontak';

    public static function canAccess(): bool
    {
        return Bi::isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama lokasi')->required()->maxLength(160)->columnSpanFull(),
            Textarea::make('address')->label('Alamat resmi (tidak diterjemahkan)')->rows(3)->columnSpanFull(),
            Bi::text('guidance', 'Petunjuk menuju lokasi', true),
            Grid::make(['default' => 1, 'md' => 2])->schema([
                TextInput::make('phone')->label('Telepon (tampilan)')->tel()->maxLength(40),
                TextInput::make('wa_number')->label('Nomor WhatsApp (format E.164 tanpa +, mis. 6281234567890)')
                    ->regex('/^[1-9][0-9]{7,14}$/')->validationMessages(['regex' => 'Gunakan angka saja, diawali kode negara, tanpa + atau spasi.'])->maxLength(15),
                TextInput::make('maps_url')->label('Tautan Google Maps (terverifikasi)')->url()->maxLength(255)->columnSpanFull(),
            ])->columnSpanFull(),
            Bi::text('hours', 'Jam layanan'),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Toggle::make('active')->label('Tampilkan di situs')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Lokasi')->searchable(),
                TextColumn::make('wa_number')->label('WhatsApp')->placeholder('belum diisi'),
                TextColumn::make('maps_url')->label('Maps')->limit(30)->placeholder('belum diisi'),
                IconColumn::make('active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('sort')
            ->recordActions([EditAction::make()->modalWidth('4xl'), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageLocations::route('/')];
    }
}
