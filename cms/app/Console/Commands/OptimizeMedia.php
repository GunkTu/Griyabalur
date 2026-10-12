<?php

namespace App\Console\Commands;

use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\PageSection;
use App\Support\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeMedia extends Command
{
    protected $signature = 'media:optimize {--prune : delete the original JPG/PNG files after converting}';

    protected $description = 'Convert photos already in the database (for example the seeded samples) to WebP with 480/960px copies';

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The PHP gd extension is not enabled (php.ini: extension=gd).');

            return self::FAILURE;
        }

        $done = 0;
        foreach ([PageSection::class, Facility::class, GalleryItem::class] as $model) {
            foreach ($model::query()->whereNotNull('image')->get() as $row) {
                $path = $row->image;
                $abs = public_path('media/'.$path);
                if (str_ends_with($path, '.webp') || ! is_file($abs)) {
                    continue;
                }

                $dir = dirname($path) === '.' ? '' : dirname($path);
                $new = Media::process($abs, $dir, pathinfo($path, PATHINFO_FILENAME));

                // Update without model events: this is a file format change, not an editorial change.
                DB::table($row->getTable())->where('id', $row->id)->update(['image' => $new]);

                if ($this->option('prune') && ! $this->stillUsed($path)) {
                    @unlink($abs);
                }
                $this->line("{$path} -> {$new}");
                $done++;
            }
        }

        $this->info("Converted {$done} photo(s).");

        return self::SUCCESS;
    }

    private function stillUsed(string $path): bool
    {
        return PageSection::where('image', $path)->exists() || Facility::where('image', $path)->exists() || GalleryItem::where('image', $path)->exists();
    }
}
