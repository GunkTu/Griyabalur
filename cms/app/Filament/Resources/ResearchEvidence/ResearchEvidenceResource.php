<?php

namespace App\Filament\Resources\ResearchEvidence;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\ResearchEvidence\Pages\ManageResearchEvidence;
use App\Filament\Support\Bi;
use App\Models\ResearchEvidence;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * PRD FR-07: every item needs author or institution, year, study type, a source file or link, and its limitations.
 * Only original documents approved by the responsible party are published.
 */
class ResearchEvidenceResource extends BaseResource
{
    protected static ?string $model = ResearchEvidence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'dokumen penelitian';

    protected static ?string $pluralModelLabel = 'Penelitian & bukti';

    private const TYPES = [
        'preclinical' => 'Pra-klinis',
        'clinical' => 'Uji klinis',
        'observational' => 'Observasional',
        'lab' => 'Uji laboratorium',
        'review' => 'Tinjauan pustaka',
        'other' => 'Lainnya',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Judul asli (tidak diterjemahkan)')->required()->maxLength(255)->columnSpanFull(),
            Grid::make(['default' => 1, 'md' => 2])->schema([
                TextInput::make('authors')->label('Penulis')->required()->maxLength(255),
                TextInput::make('institution')->label('Institusi')->maxLength(255),
                TextInput::make('year')->label('Tahun')->numeric()->minValue(1900)->maxValue((int) date('Y'))->required(),
                Select::make('study_type')->label('Jenis studi')->options(self::TYPES)->required()->native(false),
                TextInput::make('url')->label('Tautan sumber / DOI')->url()->maxLength(255),
                FileUpload::make('file')->label('Berkas PDF asli')->disk('media')->directory('research')->visibility('public')
                    ->acceptedFileTypes(['application/pdf'])->maxSize(20480),
            ])->columnSpanFull(),
            Bi::text('summary', 'Ringkasan (tidak boleh lebih kuat dari bukti)', true, 4),
            Bi::text('limitations', 'Keterbatasan studi', true, 4),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Bi::workflow(['summary', 'limitations']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->wrap()->limit(70),
                TextColumn::make('authors')->label('Penulis')->limit(30),
                TextColumn::make('year')->label('Tahun')->sortable(),
                TextColumn::make('study_type')->label('Jenis')->badge()->formatStateUsing(fn ($state) => self::TYPES[$state] ?? $state),
                ...Bi::statusColumns(),
            ])
            ->defaultSort('year', 'desc')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('Belum ada dokumen')
            ->emptyStateDescription('Tambahkan hanya dokumen asli yang telah disahkan penanggung jawab. Selama kosong, situs menampilkan keterangan "sedang diverifikasi".');
    }

    public static function getPages(): array
    {
        return ['index' => ManageResearchEvidence::route('/')];
    }
}
