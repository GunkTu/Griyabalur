<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Concept;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Location;
use App\Models\PageSection;
use App\Models\ResearchEvidence;
use App\Models\SiteSetting;
use App\Models\VisitStep;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Public one-page site, one URL per language (/id/ and /en/), server-rendered from the CMS.
 * A locale only shows content that is published in that locale; nothing falls back to the other language.
 */
class SiteController extends Controller
{
    public const LOCALES = ['id', 'en'];

    /** Default language is English unless the visitor saved a choice (kept in localStorage by the language switch). */
    public function root()
    {
        return response()->view('site.root');
    }

    public function show(string $locale)
    {
        abort_unless(in_array($locale, self::LOCALES, true), 404);
        app()->setLocale($locale);

        $sections = PageSection::publishedIn($locale)->get()->keyBy('key');
        $concepts = Concept::publishedIn($locale)->orderBy('sort')->get();
        $activities = Activity::publishedIn($locale)->orderBy('sort')->get();
        $steps = VisitStep::publishedIn($locale)->orderBy('sort')->get();
        $facilities = Facility::publishedIn($locale)->where('active', true)->orderBy('sort')->get();
        $gallery = GalleryItem::publishedIn($locale)->orderBy('sort')->get();
        $research = ResearchEvidence::publishedIn($locale)->orderByDesc('year')->get();
        $faqs = Faq::publishedIn($locale)->orderBy('sort')->get();
        $place = Location::where('active', true)->orderBy('sort')->first();

        // A section appears only when its text is published in this locale and it has something to show.
        $visible = [
            'about' => $sections->has('about'),
            'balur' => $sections->has('balur') && $concepts->isNotEmpty(),
            // The profile video shows only in a language that has its own video (uploaded file or YouTube/Vimeo link).
            'video' => $sections->has('video') && (filled($sections['video']->video_file[$locale] ?? null) || Site::embed($sections['video']->video_url[$locale] ?? null) !== null),
            'activities' => $sections->has('activities') && $activities->isNotEmpty(),
            'visit' => $sections->has('visit') && $steps->isNotEmpty(),
            'facilities' => $sections->has('facilities') && $facilities->isNotEmpty(),
            'gallery' => $sections->has('gallery') && $gallery->isNotEmpty(),
            'research' => $sections->has('research'),
            'faq' => $sections->has('faq') && $faqs->isNotEmpty(),
            'contact' => $sections->has('contact'),
        ];

        return response()->view('site.page', [
            'L' => $locale,
            'settings' => SiteSetting::current(),
            'sections' => $sections,
            'concepts' => $concepts,
            'activities' => $activities,
            'steps' => $steps,
            'facilities' => $facilities,
            'gallery' => $gallery,
            'research' => $research,
            'faqs' => $faqs,
            'place' => $place,
            'visible' => $visible,
        ]);
    }

    public function sitemap(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";
        foreach (self::LOCALES as $l) {
            $xml .= '<url><loc>'.url("/{$l}/").'</loc>';
            $xml .= '<xhtml:link rel="alternate" hreflang="id-ID" href="'.url('/id/').'"/><xhtml:link rel="alternate" hreflang="en-US" href="'.url('/en/').'"/></url>'."\n";
        }

        return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(Request $request): Response
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".url('/sitemap.xml')."\n", 200, ['Content-Type' => 'text/plain']);
    }
}
