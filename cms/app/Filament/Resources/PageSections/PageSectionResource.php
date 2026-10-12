<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\PageSections\Pages\ManagePageSections;
use App\Filament\Support\Bi;
use App\Models\PageSection;
use BackedEnum;
use Filament\Actions\EditAction;
use App\Support\Site;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
        'video' => 'Video profil BALUR',
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

    /** Profile video: one per language, as a YouTube/Vimeo link or an uploaded file. An uploaded file wins if both are set. */
    private static function videoFields(): Section
    {
        $link = fn (string $loc) => TextInput::make("video_url.{$loc}")
            ->label('Tautan YouTube / Vimeo ('.strtoupper($loc).')')
            ->placeholder('https://www.youtube.com/watch?v=...')
            ->maxLength(255)
            ->rule(fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                if (filled($value) && ! Site::embed($value)) {
                    $fail('Hanya tautan YouTube atau Vimeo yang diterima.');
                }
            });
        $file = fn (string $loc) => FileUpload::make("video_file.{$loc}")
            ->label('Berkas video ('.strtoupper($loc).')')
            ->disk('media')
            ->directory('video')
            ->visibility('public')
            ->acceptedFileTypes(['video/mp4', 'video/webm'])
            ->maxSize(24576)
            ->helperText('MP4 (H.264) atau WebM, maksimal 24 MB. Untuk video lebih besar, unggah ke YouTube lalu tempel tautannya.');

        return Section::make('Video')
            ->description('Setiap bahasa punya videonya sendiri. Jika kosong di satu bahasa, bagian video disembunyikan di bahasa itu. Bila video yang sama dipakai untuk kedua bahasa, isi keduanya.')
            ->schema([
                Grid::make(['default' => 1, 'md' => 2])->schema([$link('id'), $link('en')]),
                Grid::make(['default' => 1, 'md' => 2])->schema([$file('id'), $file('en')]),
            ])
            ->visible(fn (Get $get): bool => $get('key') === 'video')
            ->columnSpanFull();
    }

    private static function hasImage(): \Closure
    {
        return fn (Get $get): bool => in_array($get('key'), ['hero', 'about', 'video', 'activities', 'contact'], true);
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
            Bi::imageUpload('image', 'Foto (untuk video: gambar sampul, disarankan rasio 16:9)')->visible(self::hasImage()),
            Bi::text('image_alt', 'Teks alternatif foto (alt)')->visible(self::hasImage()),
            self::videoFields(),
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
