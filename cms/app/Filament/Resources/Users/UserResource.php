<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Filament\Support\Bi;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Roles (PRD FR-10): admin manages everything, editor drafts, reviewer approves and publishes. */
class UserResource extends BaseResource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna & peran';

    public static function canAccess(): bool
    {
        return Bi::isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return Bi::isAdmin() && $record->getKey() !== auth()->id(); // nobody removes their own account
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(120),
            TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true)->maxLength(190),
            Select::make('role')->label('Peran')->options([
                'admin' => 'Admin: semua akses, pengguna, lokasi, pengaturan',
                'editor' => 'Editor: menulis dan mengedit, status maksimal Content review',
                'reviewer' => 'Reviewer: menyetujui dan menayangkan konten',
            ])->required()->native(false),
            TextInput::make('password')->label('Kata sandi')->password()->revealable()
                ->required(fn (?User $record) => $record === null)
                ->dehydrated(fn (?string $state) => filled($state))
                ->minLength(12)->helperText('Minimal 12 karakter. Kosongkan saat mengedit jika tidak diganti.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('role')->label('Peran')->badge()->formatStateUsing(fn ($state) => User::ROLES[$state] ?? $state),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageUsers::route('/')];
    }
}
