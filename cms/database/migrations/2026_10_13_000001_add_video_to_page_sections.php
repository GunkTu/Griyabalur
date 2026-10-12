<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Profile video of BALUR: one more page section ("video"). Each language has its own video,
 * either a YouTube/Vimeo link or an uploaded file: {"id": "...", "en": "..."}.
 * The section stays hidden on the site in a language until that language has a video.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_sections', function (Blueprint $t) {
            $t->json('video_url')->nullable();
            $t->json('video_file')->nullable();
        });

        // Existing installs get the section row without running the seeder.
        if (! DB::table('page_sections')->where('key', 'video')->exists()) {
            DB::table('page_sections')->insert([
                'key' => 'video',
                'headline' => json_encode([
                    'id' => 'Profil *BALUR* dalam video.',
                    'en' => 'A short *video* profile of BALUR.',
                ], JSON_UNESCAPED_UNICODE),
                'lead' => json_encode([
                    'id' => 'Video profil yang memperkenalkan Griya Balur Bukian Mertasari, kegiatan BALUR, dan suasana tempatnya.',
                    'en' => 'A profile video introducing Griya Balur Bukian Mertasari, BALUR activities, and the place itself.',
                ], JSON_UNESCAPED_UNICODE),
                'status_id' => 'published',
                'status_en' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('page_sections')->where('key', 'video')->delete();
        Schema::table('page_sections', function (Blueprint $t) {
            $t->dropColumn(['video_url', 'video_file']);
        });
    }
};
