<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Concept;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Location;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\VisitStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Loads the sample content of the static site (database/seeders/content.json) so the public site
 * is complete on first run. Everything is seeded as published; the owner must still review it
 * (PRD section 13: brochure claims are never approved automatically).
 *
 * Run: php artisan db:seed --class=CmsSeeder
 * The first admin comes from ADMIN_EMAIL / ADMIN_PASSWORD in .env. Without a password, a random one is generated and printed once.
 */
class CmsSeeder extends Seeder
{
    private const PUBLISHED = ['status_id' => 'published', 'status_en' => 'published'];

    public function run(): void
    {
        $c = json_decode(file_get_contents(__DIR__.'/content.json'), true);

        SiteSetting::updateOrCreate([], ['brand_name' => 'Griya Balur Bukian Mertasari', 'seo' => $c['seo']]);

        foreach (PageSection::KEYS as $key) {
            PageSection::updateOrCreate(['key' => $key], ($c['sections'][$key] ?? []) + self::PUBLISHED);
        }

        $this->fill(Concept::class, $c['concepts']);
        $this->fill(Activity::class, $c['activities']);
        $this->fill(VisitStep::class, $c['visit_steps']);
        $this->fill(Facility::class, $c['facilities']);
        $this->fill(GalleryItem::class, $c['gallery'], ['permission_status' => 'not_needed']);
        $this->fill(Faq::class, $c['faqs']);

        // Contact details are intentionally empty: the owner fills them in (PRD section 15).
        if (! Location::exists()) {
            Location::create(['name' => 'Griya Balur Bukian Mertasari', 'active' => true, 'sort' => 1]);
        }

        $this->admin();
    }

    private function fill(string $model, array $rows, array $extra = []): void
    {
        if ($model::exists()) {
            return; // never overwrite content that editors may have changed
        }
        foreach ($rows as $i => $row) {
            $model::create($row + $extra + self::PUBLISHED + ['sort' => $i + 1]);
        }
    }

    private function admin(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@griyabalur.test');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $password = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);
        User::create(['name' => 'Admin', 'email' => $email, 'password' => $password, 'role' => 'admin']);

        $this->command?->warn("Admin created: {$email} / {$password}  (change this password after first login)");
    }
}
