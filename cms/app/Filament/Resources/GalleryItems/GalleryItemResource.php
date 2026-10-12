<?php

namespace App\Filament\Resources\GalleryItems;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\GalleryItems\Pages\ManageGalleryItems;
use App\Filament\Support\Bi;
use App\Models\GalleryItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class GalleryItemResource extends BaseResource
{
    protected static ?string $model = GalleryItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'foto galeri';

    protected static ?string $pluralModelLabel = 'Galeri';

    private const CATEGORIES = [
        'exterior' => 'Eksterior / luar ruang',
        'interior' => 'Interior',
        'facility' => 'Fasilitas',
        'activity' => 'Aktivitas',
        'team' => 'Tim & organisasi',
        'product' => 'Produk / material',
    ];

    private const PERMISSION = [
        'pending' => 'Belum dicatat',
        'granted' => 'Izin tercatat',
        'not_needed' => 'Tidak diperlukan (tanpa orang)',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Bi::imageUpload('image', 'Foto', true),
            Bi::text('caption', 'Keterangan'),
            Bi::text('alt', 'Teks alternatif (alt), wajib'),
            Select::make('category')->label('Kategori')->options(self::CATEGORIES)->required()->native(false),
            Select::make('permission_status')->label('Izin subjek foto')->options(self::PERMISSION)->default('pending')->required()->native(false)
                ->helperText('Foto berisi orang hanya boleh tayang jika izin sudah tercatat.'),
            TextInput::make('credit')->label('Kredit fotografer')->maxLength(120),
            DatePicker::make('captured_at')->label('Tanggal ambil'),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Bi::workflow(['caption', 'alt']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('media')->label('Foto')->square()->size(56),
                TextColumn::make('caption')->label('Keterangan')->state(fn ($record) => Bi::preview($record->caption)),
                TextColumn::make('category')->label('Kategori')->badge()->formatStateUsing(fn ($state) => self::CATEGORIES[$state] ?? $state),
                TextColumn::make('permission_status')->label('Izin')->badge()
                    ->color(fn ($state) => $state === 'pending' ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => self::PERMISSION[$state] ?? $state),
                ...Bi::statusColumns(),
            ])
            ->filters([SelectFilter::make('category')->label('Kategori')->options(self::CATEGORIES)])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageGalleryItems::route('/')];
    }
}
