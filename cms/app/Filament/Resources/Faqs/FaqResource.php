<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Filament\Support\Bi;
use App\Models\Faq;
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

class FaqResource extends BaseResource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 8;

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Bi::text('question', 'Pertanyaan'),
            Bi::text('answer', 'Jawaban', true),
            TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            Bi::workflow(['question', 'answer']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->label('Pertanyaan')->state(fn ($record) => Bi::preview($record->question))->wrap(),
                ...Bi::statusColumns(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->recordActions([Bi::publishAction(), EditAction::make()->modalWidth('5xl'), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageFaqs::route('/')];
    }
}
