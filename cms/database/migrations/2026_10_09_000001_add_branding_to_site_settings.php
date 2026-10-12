<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $t) {
            $t->string('logo')->nullable();
            $t->boolean('logo_show_name')->default(true);   // keep the brand name next to the logo
            $t->string('favicon')->nullable();
            $t->string('share_image')->nullable();           // default link-preview image
            $t->json('footer')->nullable();                  // {id:{disclaimer,privacy_body,wa_message}, en:{...}}
            $t->json('analytics')->nullable();               // {plausible_domain, ga4_id}
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $t) {
            $t->dropColumn(['logo', 'logo_show_name', 'favicon', 'share_image', 'footer', 'analytics']);
        });
    }
};
