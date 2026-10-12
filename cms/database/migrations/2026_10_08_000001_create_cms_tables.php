<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Per-locale editorial workflow columns (PRD section 10): status_id and status_en. */
    private function workflow(Blueprint $t): void
    {
        $t->string('status_id')->default('draft');
        $t->string('status_en')->default('draft');
        $t->text('review_notes')->nullable();
    }

    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('editor'); // admin | editor | reviewer
        });

        Schema::create('site_settings', function (Blueprint $t) {
            $t->id();
            $t->string('brand_name');
            $t->json('seo')->nullable();        // {id:{title,description}, en:{...}}
            $t->json('socials')->nullable();
            $t->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();        // hero, about, balur, ...
            $t->json('headline')->nullable();
            $t->json('lead')->nullable();
            $t->json('body')->nullable();
            $t->json('note')->nullable();
            $t->json('cta1')->nullable();
            $t->json('cta2')->nullable();
            $t->string('image')->nullable();
            $t->json('image_alt')->nullable();
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('concepts', function (Blueprint $t) {
            $t->id();
            $t->json('title');
            $t->json('body');
            $t->unsignedInteger('sort')->default(0);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('activities', function (Blueprint $t) {
            $t->id();
            $t->json('title');
            $t->json('summary');
            $t->json('public_note')->nullable(); // shown publicly, e.g. "pending review"
            $t->unsignedInteger('sort')->default(0);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('visit_steps', function (Blueprint $t) {
            $t->id();
            $t->json('title');
            $t->json('body');
            $t->unsignedInteger('sort')->default(0);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('facilities', function (Blueprint $t) {
            $t->id();
            $t->json('title');
            $t->json('caption');
            $t->json('alt');
            $t->string('image')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $t) {
            $t->id();
            $t->string('image');
            $t->json('caption');
            $t->json('alt');
            $t->string('category')->default('exterior');
            $t->string('permission_status')->default('pending'); // pending | granted | not_needed
            $t->string('credit')->nullable();
            $t->date('captured_at')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('research_evidence', function (Blueprint $t) {
            $t->id();
            $t->string('title');                 // original title, never translated
            $t->json('summary');
            $t->json('limitations');
            $t->string('authors');
            $t->string('institution')->nullable();
            $t->unsignedSmallInteger('year');
            $t->string('study_type');
            $t->string('url')->nullable();
            $t->string('file')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->json('question');
            $t->json('answer');
            $t->unsignedInteger('sort')->default(0);
            $this->workflow($t);
            $t->timestamps();
        });

        Schema::create('locations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->text('address')->nullable();     // official address, not translated
            $t->json('guidance')->nullable();    // how to get there, translated
            $t->string('phone')->nullable();
            $t->string('wa_number')->nullable(); // E.164 without +
            $t->string('maps_url')->nullable();
            $t->json('hours')->nullable();
            $t->boolean('active')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('subject_type');
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->string('subject_label')->nullable();
            $t->string('action');                // created | updated | deleted
            $t->json('changes')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach (['activity_logs', 'locations', 'faqs', 'research_evidence', 'gallery_items', 'facilities', 'visit_steps', 'activities', 'concepts', 'page_sections', 'site_settings'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
