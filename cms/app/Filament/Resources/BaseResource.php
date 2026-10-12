<?php

namespace App\Filament\Resources;

use App\Filament\Support\Bi;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared access rules (PRD FR-10): admin, editor, and reviewer can work on content; only admins delete.
 */
abstract class BaseResource extends Resource
{
    public static function canDelete(Model $record): bool
    {
        return Bi::isAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return Bi::isAdmin();
    }
}
