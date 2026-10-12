<?php

namespace App\Filament\Support;

use App\Models\Concerns\Workflow;
use App\Models\PageSection;
use App\Support\Media;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Form and table helpers for bilingual content (ID and EN side by side) and the per-locale workflow.
 */
class Bi
{
    public static function role(): ?string
    {
        return auth()->user()?->role;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /** A bilingual field: ID and EN inputs next to each other, so a missing translation is obvious. */
    public static function text(string $name, string $label, bool $area = false, int $rows = 3): Grid
    {
        $inputs = [];
        foreach (['id' => 'ID', 'en' => 'EN'] as $loc => $tag) {
            $field = $area ? Textarea::make("{$name}.{$loc}")->rows($rows) : TextInput::make("{$name}.{$loc}")->maxLength(255);
            $inputs[] = $field->label("{$label} ({$tag})");
        }

        return Grid::make(['default' => 1, 'md' => 2])->schema($inputs)->columnSpanFull();
    }

    /**
     * Workflow block: one status per locale plus internal review notes.
     * $fields lists the bilingual fields that must be filled before a locale can be approved or published.
     * Null means "look it up from the page section key".
     */
    public static function workflow(?array $fields = null): Section
    {
        return Section::make('Status penerbitan per bahasa')
            ->description('Konten baru berstatus Draft dan BELUM tampil di situs. Isi tampil di suatu bahasa hanya jika statusnya Tayang. Editor hanya dapat memilih Draft atau Content review; reviewer atau admin yang menayangkan.')
            ->schema([
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    self::status('id', $fields),
                    self::status('en', $fields),
                ]),
                Textarea::make('review_notes')->label('Catatan review (internal, tidak tayang)')->rows(2)->columnSpanFull(),
            ])
            ->collapsible()
            ->columnSpanFull();
    }

    private static function status(string $locale, ?array $fields): Select
    {
        return Select::make("status_{$locale}")
            ->label('Status '.strtoupper($locale))
            ->options(self::statuses())
            ->default('draft')
            ->required()
            ->native(false)
            ->disableOptionWhen(fn (string $value): bool => self::role() === 'editor' && ! in_array($value, ['draft', 'content_review'], true))
            ->rule(fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $fields, $locale): void {
                if (! in_array($value, ['approved', 'published'], true)) {
                    return;
                }
                $required = $fields ?? (PageSection::REQUIRED[$get('key')] ?? ['headline']);
                $missing = array_filter($required, fn (string $f) => blank($get("{$f}.{$locale}")));
                if ($missing) {
                    $fail('Terjemahan '.strtoupper($locale).' belum lengkap: '.implode(', ', $missing).'.');
                }
            });
    }

    /** Wraps an upload handler so a bad file shows a message instead of an error page. */
    public static function safeSave(Closure $save): Closure
    {
        return function (TemporaryUploadedFile $file) use ($save): ?string {
            try {
                return $save($file);
            } catch (RuntimeException $e) {
                Notification::make()->danger()->title('File ditolak')->body($e->getMessage())->send();

                return null;
            }
        };
    }

    /** Photo upload: converted to WebP with 480/960px copies, EXIF removed (see App\Support\Media). */
    public static function imageUpload(string $name, string $label, bool $required = false): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->disk('media')
            ->image()
            ->imageEditor()
            ->maxSize(12288)
            ->required($required)
            ->saveUploadedFileUsing(self::safeSave(fn (TemporaryUploadedFile $file): string => Media::fromUpload($file)))
            ->deleteUploadedFileUsing(fn (?string $file) => Media::delete($file))
            ->helperText('JPG, PNG, atau WebP, maksimal 12 MB. Otomatis dikecilkan ke WebP (lebar maks 1920 px) dan data lokasi/EXIF dihapus.')
            ->columnSpanFull();
    }

    /**
     * Row action for reviewers and admins: publish both languages in one click.
     * Each language is tried on its own; incomplete ones are reported and left unchanged.
     */
    public static function publishAction(): Action
    {
        return Action::make('publish')
            ->label('Tayangkan')
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->visible(fn ($record): bool => in_array(self::role(), ['admin', 'reviewer'], true)
                && ($record->status_id !== 'published' || $record->status_en !== 'published'))
            ->requiresConfirmation()
            ->modalHeading('Tayangkan di situs?')
            ->modalDescription('Status ID dan EN diubah menjadi Tayang jika terjemahannya lengkap. Bahasa yang belum lengkap tidak diubah.')
            ->action(function ($record): void {
                $done = [];
                $problems = [];
                foreach (['id', 'en'] as $loc) {
                    try {
                        $record->update(["status_{$loc}" => 'published']);
                        $done[] = strtoupper($loc);
                    } catch (ValidationException $e) {
                        $problems[] = implode(' ', Arr::flatten($e->errors()));
                    }
                }
                $note = Notification::make()->title($done ? 'Tayang: '.implode(' dan ', $done) : 'Belum bisa ditayangkan');
                $problems ? $note->body(implode("\n", $problems))->warning() : $note->success();
                $note->send();
            });
    }

    /** Status options in a fixed order, labelled in Indonesian. */
    public static function statuses(): array
    {
        return [
            'draft' => 'Draft',
            'content_review' => 'Content review',
            'medical_review' => 'Tinjauan medis / regulasi',
            'approved' => 'Disetujui',
            'published' => 'Tayang',
        ];
    }

    /** The two status badges used in every content table. */
    public static function statusColumns(): array
    {
        $color = fn (string $state): string => match ($state) {
            'published' => 'success',
            'approved' => 'info',
            'medical_review', 'content_review' => 'warning',
            default => 'gray',
        };

        return [
            TextColumn::make('status_id')->label('ID')->badge()->color($color)->formatStateUsing(fn (string $state) => self::statuses()[$state] ?? $state),
            TextColumn::make('status_en')->label('EN')->badge()->color($color)->formatStateUsing(fn (string $state) => self::statuses()[$state] ?? $state),
        ];
    }

    /** First non-empty language of a bilingual value, for table cells. */
    public static function preview(mixed $value): string
    {
        $value = is_string($value) ? (json_decode($value, true) ?? []) : (array) $value;
        return mb_strimwidth((string) (($value['en'] ?? '') ?: ($value['id'] ?? '')), 0, 60, '...');
    }
}
