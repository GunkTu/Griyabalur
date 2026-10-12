<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ManageActivityLogs;
use App\Filament\Resources\BaseResource;
use App\Filament\Support\Bi;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Audit log (PRD section 10): who changed what, and when. Read only. */
class ActivityLogResource extends BaseResource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'catatan aktivitas';

    protected static ?string $pluralModelLabel = 'Log aktivitas';

    public static function canAccess(): bool
    {
        return Bi::isAdmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('user.name')->label('Pengguna')->placeholder('sistem'),
                TextColumn::make('action')->label('Aksi')->badge()
                    ->color(fn ($state) => match ($state) { 'created' => 'success', 'deleted' => 'danger', default => 'gray' })
                    ->formatStateUsing(fn ($state) => ['created' => 'dibuat', 'updated' => 'diubah', 'deleted' => 'dihapus'][$state] ?? $state),
                TextColumn::make('subject_type')->label('Jenis'),
                TextColumn::make('subject_label')->label('Objek')->wrap()->searchable(),
            ])
            ->filters([
                SelectFilter::make('action')->label('Aksi')->options(['created' => 'dibuat', 'updated' => 'diubah', 'deleted' => 'dihapus']),
                SelectFilter::make('subject_type')->label('Jenis')->options(fn () => ActivityLog::query()->distinct()->pluck('subject_type', 'subject_type')->all()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ManageActivityLogs::route('/')];
    }
}
