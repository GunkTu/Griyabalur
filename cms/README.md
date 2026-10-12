# Griya Balur Bukian Mertasari: website + admin panel

Laravel 12 + Filament 4. The public site is server-rendered at `/id/` and `/en/` (root `/` goes to `/en/` unless the visitor chose Indonesian). The admin panel is at `/admin`.

## Run locally

```
cd cms
php artisan serve          # http://127.0.0.1:8000  (/en/, /id/, /admin)
php artisan test           # 39 tests: both languages, workflow rules, roles, admin forms, photo optimizing, brand settings, profile video
```

First setup on a new machine: `composer install`, copy `.env.example` to `.env`, `php artisan key:generate`, then
`php artisan migrate --seed --seeder=CmsSeeder` and `php artisan filament:assets`.
The seeder prints the first admin login once. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before seeding to choose your own.
`php artisan migrate:fresh --seed --seeder=CmsSeeder` wipes everything and starts over.

## What is managed where

| Admin menu | Public section |
| --- | --- |
| Bagian halaman | Hero text, headings, intro texts, section photos, and the BALUR profile video (row "Video profil BALUR") |
| Konsep BALUR, Kegiatan, Langkah kunjungan, Fasilitas, Galeri, Penelitian & bukti, FAQ | The matching sections |
| Lokasi & kontak (admin) | Address, WhatsApp, Maps, hours, floating WhatsApp button |
| Pengaturan situs (admin) | Brand name, logo, favicon, link-preview image, SEO per language, footer and privacy text, WhatsApp opening message, social links, analytics |
| Pengguna & peran, Log aktivitas (admin) | Accounts and audit trail |

Navigation labels, buttons, and fixed interface text live in `lang/id/site.php` and `lang/en/site.php`.
Uploaded files go to `public/media/` (no `storage:link` needed).
Photos are converted to WebP (max 1920 px wide) with 480 px and 960 px copies, and EXIF data such as GPS is removed. This needs the PHP `gd` extension (`extension=gd` in php.ini). `php artisan media:optimize` converts photos that are already in the database.

## Profile video

In **Bagian halaman → Video profil BALUR** each language gets its own video, as a YouTube/Vimeo link or an uploaded MP4/WebM (up to 24 MB; larger videos should go to YouTube). An uploaded file wins if both are set. The cover image (16:9 recommended) is the section photo. The section is hidden in a language until that language has a video, and nothing loads from YouTube or Vimeo before the visitor presses play. The summary box under the video (field "Catatan") is optional text for people who cannot watch it.
Uploaded videos go to `public/media/video/` and are not part of git, so back that folder up with the rest of `public/media/`.

## Rules built in

- New content starts as Draft and does not show on the site. A reviewer or admin publishes it with the "Tayangkan" row button (or by setting the status to Tayang).
- Each content item has a status per language: Draft, Content review, Medical/regulatory review, Approved, Published.
- A language can only be approved or published when every required field in that language is filled. Nothing falls back to the other language; a section with no published text in a language is hidden there.
- Editors can only set Draft or Content review. Editing the text of an approved or published language sends it back to Content review. Reviewers and admins can approve and publish. Only admins delete.
- A gallery photo can only be published once its permission is recorded.
- Every create, update, and delete is written to the activity log.

## Before going live

- Fill in Lokasi & kontak (address, WhatsApp, Maps, hours). Until then the buttons show "coming soon".
- Replace the sample photos (Wikimedia Commons, see `public/media/CREDITS.txt`) and review all seeded text with the owner. Seeded content is marked Published only so the site works on first run.
- `.env`: `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, MySQL/MariaDB credentials, then `php artisan optimize`.
- Not included yet: two-factor login, scheduled backups.
