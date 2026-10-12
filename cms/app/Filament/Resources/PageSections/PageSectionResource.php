<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\PageSections\Pages\ManagePageSections;
use App\Filament\Support\Bi;
use App\Models\PageSection;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PageSectionResource extends BaseResource
{
    protected static ?string $model = PageSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'bagian halaman';

    protected static ?string $pluralModelLabel = 'Bagian halaman';

    /** Friendly names shown in the admin (the keys are internal). */
    public const NAMES = [
        'hero' => 'Hero (paling atas)',
        'about' => 'Tentang kami',
        'balur' => 'Memahami BALUR',
        'activities' => 'Kegiatan',
        'visit' => 'Alur kunjungan',
        'facilities' => 'Fasilitas',
        'gallery' => 'Galeri',
        'research' => 'Penelitian',
        'faq' => 'FAQ',
        'contact' => 'Kontak',
    ];

    /** Sections are fixed (they map to the page layout), so they are edited but never created or deleted. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    private static function uses(string $field): \Closure
    {
        return fn (Get $get): bool => in_array($field, PageSection::REQUIRED[$get('key')] ?? [], true);
    }

    private static function hasImage(): \Closure
    {
        return fn (Get $get): bool => in_array($get('key'), ['hero', 'about', 'activities', 'contact'], true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->label('Bagian (internal)')->disabled()->dehydrated(false)->columnSpanFull(),
            Bi::text('headline', 'Judul')->visible(self::uses('headline')),
            Bi::text('lead', 'Teks pengantar', true)->visible(self::uses('lead')),
            Bi::text('body', 'Isi (pisahkan paragraf dengan baris kosong)', true, 6)->visible(self::uses('body')),
            Bi::text('note', 'Catatan', true)->visible(self::uses('note')),
            Bi::text('cta1', 'Tombol utama')->visible(self::uses('cta1')),
            Bi::text('cta2', 'Tombol kedua')->visible(self::uses('cta2')),
            Bi::imageUpload('image', 'Foto')->visible(self::hasImage()),
            Bi::text('image_alt', 'Teks alternatif foto (alt)')->visible(self::hasImage()),
            Bi::workflow(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('media')->label('Foto')->square()->size(48),
                TextColumn::make('key')->label('Bagian')->badge()->formatStateUsing(fn (string $state) => self::NAMES[$state] ?? $state),
                TextColumn::make('headline')->label('Judul / teks')->state(fn ($record) => Bi::preview($record->headline ?: $record->lead))->wrap(),
                ...Bi::statusColumns(),
                TextColumn::make('updated_at')->label('Diubah')->since(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->orderByRaw('CASE "key" '.collect(PageSection::KEYS)->map(fn ($k, $i) => "WHEN '{$k}' THEN {$i}")->implode(' ').' END'))
            ->paginated(false)
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl')]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePageSections::route('/')];
    }
}
