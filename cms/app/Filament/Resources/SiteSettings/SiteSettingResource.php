<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\SiteSettings\Pages\ManageSiteSettings;
use App\Filament\Support\Bi;
use App\Models\SiteSetting;
use App\Support\Media;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

/** Everything about the site that is not page content: brand, logo, favicon, SEO, footer text, social links, analytics. */
class SiteSettingResource extends BaseResource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Situs';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'pengaturan situs';

    protected static ?string $pluralModelLabel = 'Pengaturan situs';

    public static function canAccess(): bool
    {
        return Bi::isAdmin();
    }

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

    private static function brandFile(string $name, string $label, array $types, \Closure $save, string $help): FileUpload
    {
        return FileUpload::make($name)->label($label)->disk('media')->acceptedFileTypes($types)->maxSize(2048)
            ->saveUploadedFileUsing(Bi::safeSave($save))
            ->deleteUploadedFileUsing(fn (?string $file) => Media::delete($file))
            ->helperText($help);
    }

    public static function form(Schema $schema): Schema
    {
        $seo = fn (string $loc, string $name) => Section::make("SEO {$name}")->columnSpanFull()->collapsible()->schema([
            TextInput::make("seo.{$loc}.title")->label('Judul halaman (title)')->maxLength(70)->helperText('Maksimal sekitar 60 karakter.'),
            Textarea::make("seo.{$loc}.description")->label('Deskripsi (meta description)')->rows(3)->maxLength(170)->helperText('Maksimal sekitar 160 karakter.'),
        ]);

        return $schema->components([
            Section::make('Identitas & logo')->columnSpanFull()->schema([
                TextInput::make('brand_name')->label('Nama brand (sama di semua bahasa)')->required()->maxLength(120)->columnSpanFull(),
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    self::brandFile('logo', 'Logo', ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'],
                        fn (TemporaryUploadedFile $f) => Media::fromLogoUpload($f),
                        'PNG transparan, WebP, atau SVG, maks 2 MB. Tampil di navbar. SVG dengan skrip akan ditolak.'),
                    self::brandFile('favicon', 'Favicon (ikon tab browser)', ['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'],
                        fn (TemporaryUploadedFile $f) => Media::fromFaviconUpload($f),
                        'PNG persegi (mis. 256x256), ICO, atau SVG, maks 2 MB.'),
                    self::brandFile('share_image', 'Gambar pratinjau tautan (WhatsApp, media sosial)', ['image/png', 'image/jpeg', 'image/webp'],
                        fn (TemporaryUploadedFile $f) => Media::fromShareUpload($f),
                        'Disarankan 1200x630 px. Jika kosong, foto hero yang dipakai.'),
                    Toggle::make('logo_show_name')->label('Tampilkan nama brand di samping logo')->default(true)
                        ->helperText('Matikan jika logo Anda sudah memuat tulisan nama.')->inline(false),
                ])->columnSpanFull(),
            ]),
            $seo('id', 'Bahasa Indonesia (id-ID)'),
            $seo('en', 'English (en-US)'),
            Section::make('Teks footer & pesan WhatsApp')->columnSpanFull()->collapsible()->collapsed()
                ->description('Kosongkan untuk memakai teks bawaan. Disclaimer kesehatan sebaiknya tidak dihapus tanpa persetujuan penanggung jawab.')
                ->schema([
                    Bi::text('footer.disclaimer', 'Disclaimer footer', true),
                    Bi::text('footer.privacy_body', 'Isi Kebijakan Privasi singkat', true, 4),
                    Bi::text('footer.wa_message', 'Pesan awal WhatsApp (terisi otomatis saat pengunjung membuka chat)', true, 2),
                ]),
            Section::make('Media sosial')->columnSpanFull()->collapsible()->collapsed()
                ->description('Hanya yang diisi yang tampil di footer.')
                ->schema([Grid::make(['default' => 1, 'md' => 2])->schema(array_map(
                    fn (string $key, string $label) => TextInput::make("socials.{$key}")->label($label)->url()->maxLength(255)->placeholder('https://'),
                    array_keys(SiteSetting::SOCIALS), SiteSetting::SOCIALS
                ))->columnSpanFull()]),
            Section::make('Analitik')->columnSpanFull()->collapsible()->collapsed()
                ->description('Hanya klik WhatsApp, peta, galeri, FAQ, unduhan, dan ganti bahasa yang dicatat, tanpa data pribadi atau medis.')
                ->schema([Grid::make(['default' => 1, 'md' => 2])->schema([
                    TextInput::make('analytics.plausible_domain')->label('Domain Plausible (tanpa cookie, disarankan)')->placeholder('griyabalur.com')
                        ->regex('/^[a-z0-9.-]+\.[a-z]{2,}$/i')->maxLength(120),
                    TextInput::make('analytics.ga4_id')->label('Google Analytics 4 (opsional)')->placeholder('G-XXXXXXXXXX')
                        ->regex('/^G-[A-Z0-9]{6,12}$/')->maxLength(20)
                        ->helperText('GA4 memakai cookie, sehingga situs perlu pemberitahuan cookie sesuai aturan yang berlaku.'),
                ])->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')->disk('media')->label('Logo')->height(36),
                TextColumn::make('brand_name')->label('Brand'),
                TextColumn::make('seo.id.title')->label('Judul SEO (ID)')->limit(40),
                TextColumn::make('seo.en.title')->label('Judul SEO (EN)')->limit(40),
            ])
            ->paginated(false)
            ->recordActions([EditAction::make()->modalWidth('5xl')]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSiteSettings::route('/')];
    }
}
