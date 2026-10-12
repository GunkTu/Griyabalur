@php
    use App\Models\SiteSetting;
    use App\Support\Media;
    use App\Support\Site;

    $ico = fn (string $n) => Site::icon($n);
    $brand = $settings->brand_name;
    $seo = $settings->seo[$L] ?? [];
    $title = ($seo['title'] ?? '') ?: __('site.title');
    $desc = ($seo['description'] ?? '') ?: __('site.desc');
    $other = $L === 'id' ? 'en' : 'id';
    $sec = fn (string $k) => $sections->get($k);
    $text = fn (string $k, string $f) => $sections->get($k)?->tr($f, $L) ?? '';
    $navItems = collect(__('site.nav'))->filter(fn ($n) => $visible[$n[0]] ?? true)->values();

    $wa = $place?->wa_number;
    $waHref = $wa ? 'https://wa.me/'.$wa.'?text='.rawurlencode($settings->footerText('wa_message', $L, __('site.contact.wa_msg'))) : null;
    $mapsUrl = $place?->maps_url;
    $hero = $sec('hero');
    $heroImg = $hero?->image ?: 'hero.jpg';
    $logo = $settings->logo;
    $socials = collect(SiteSetting::SOCIALS)->filter(fn ($label, $key) => ! empty($settings->socials[$key] ?? null));
    $ld = ['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $brand, 'url' => url("/{$L}/"), 'inLanguage' => __('site.locale.lang')];
    if ($logo) { $ld['logo'] = url(Site::media($logo)); }
    if ($socials->isNotEmpty()) { $ld['sameAs'] = $socials->map(fn ($l, $k) => $settings->socials[$k])->values()->all(); }
    $plausible = $settings->analytics['plausible_domain'] ?? null;
    $ga4 = $settings->analytics['ga4_id'] ?? null;
    $socialIcons = ['instagram' => 'instagram-logo', 'facebook' => 'facebook-logo', 'youtube' => 'youtube-logo', 'tiktok' => 'tiktok-logo', 'website' => 'globe'];
    $cats = $gallery->pluck('category')->unique()->values();
    $soon = '<span class="todo">'.e(__('site.contact.soon')).'</span>';
@endphp
<!doctype html>
<html lang="{{ __('site.locale.lang') }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ url("/{$L}/") }}">
<link rel="alternate" hreflang="id-ID" href="{{ url('/id/') }}">
<link rel="alternate" hreflang="en-US" href="{{ url('/en/') }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/en/') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $brand }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ url("/{$L}/") }}">
<meta property="og:image" content="{{ url(Site::media($settings->share_image ?: $heroImg)) }}">
<meta property="og:locale" content="{{ __('site.locale.og') }}">
<meta property="og:locale:alternate" content="{{ __('site.locale.og') === 'en_US' ? 'id_ID' : 'en_US' }}">
<meta name="theme-color" content="#193B32">
<link rel="icon" href="{{ $settings->favicon ? Site::media($settings->favicon) : '/assets/favicon.svg' }}">
<link rel="preload" as="image" href="{{ Site::media($heroImg) }}" fetchpriority="high">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500;1,600&display=swap">
<link rel="stylesheet" href="/assets/style.css?v={{ filemtime(public_path('assets/style.css')) }}">
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@if ($plausible)<script defer data-domain="{{ $plausible }}" src="https://plausible.io/js/script.js"></script>@endif
@if ($ga4)<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $ga4 }}',{anonymize_ip:true});</script>@endif
</head>
<body data-locale="{{ $L }}">
<a class="skip" href="#main">{{ __('site.skip') }}</a>

<span class="nav-sentinel" aria-hidden="true"></span>
<header class="nav" id="top">
  <div class="nav__in">
    <a class="brand" href="#main" aria-label="{{ $brand }}">@if ($logo)<img class="brand__logo" src="{{ Site::media($logo) }}" alt="{{ $settings->logo_show_name ? '' : $brand }}" decoding="async">@else<span class="brand__mark">{!! $ico('leaf') !!}</span>@endif
      @if (! $logo || $settings->logo_show_name)<span class="brand__name">{{ $brand }}</span>@endif</a>
    <nav class="nav__links" aria-label="Main">@foreach ($navItems as [$id, $label])<a href="#{{ $id }}">{{ $label }}</a>@endforeach</nav>
    <div class="nav__tools">@include('site.partials.switcher', ['cls' => 'lang--desk'])<button class="burger" aria-expanded="false" aria-controls="sheet" aria-label="{{ __('site.menu') }}"><span></span><span></span></button></div>
  </div>
</header>
<div class="sheet" id="sheet" hidden>
  <nav aria-label="Mobile">@foreach ($navItems as $i => [$id, $label])<a href="#{{ $id }}" style="--i:{{ $i }}">{{ $label }}</a>@endforeach</nav>
  @include('site.partials.switcher', ['cls' => 'lang--mob'])
</div>

<main id="main">
<section class="hero" aria-labelledby="h1">
  <img class="hero__img" src="{{ Site::media($heroImg) }}" @if ($heroSet = Media::srcset($heroImg)) srcset="{{ $heroSet }}" sizes="100vw" @endif alt="{{ $hero?->tr('image_alt', $L) }}" fetchpriority="high" decoding="async">
  <div class="hero__scrim"></div>
  <div class="wrap hero__in">
    <h1 id="h1">{{ $brand }}</h1>
    @if ($text('hero', 'lead'))<p class="hero__sub">{{ $text('hero', 'lead') }}</p>@endif
    <div class="cta">
      @if ($visible['facilities'] && $text('hero', 'cta1'))<a class="btn btn--primary" href="#facilities">{{ $text('hero', 'cta1') }}</a>@endif
      @if ($visible['contact'] && $text('hero', 'cta2'))<a class="btn btn--onphoto" href="#contact">{{ $text('hero', 'cta2') }}</a>@endif
    </div>
  </div>
</section>

@if ($visible['about'])
<section class="sec sec--about" id="about" aria-labelledby="about-h">
  <div class="wrap">
    <h2 id="about-h" class="statement reveal">{{ Site::hl($text('about', 'headline')) }}</h2>
    <div class="about reveal">
      <div class="about__txt">@foreach (Site::paragraphs($text('about', 'body')) as $p)<p>{{ $p }}</p>@endforeach</div>
      @if ($sec('about')->image)<figure class="photo about__ph">{{ Site::img($sec('about')->image, $sec('about')->tr('image_alt', $L), 'lazy', '', '(min-width: 1024px) 55vw, 100vw') }}</figure>@endif
    </div>
  </div>
</section>
@endif

@if ($visible['balur'])
<section class="sec sec--tint" id="balur" aria-labelledby="balur-h">
  <div class="wrap">
    <div class="head reveal"><h2 id="balur-h">{{ $text('balur', 'headline') }}</h2><p class="lead">{{ $text('balur', 'lead') }}</p></div>
    <ol class="index">
      @foreach ($concepts as $c)<li class="index__row reveal"><h3>{{ $c->tr('title', $L) }}</h3><p>{{ $c->tr('body', $L) }}</p></li>@endforeach
    </ol>
    <p class="note reveal">{{ $text('balur', 'note') }}</p>
  </div>
</section>
@endif

@if ($visible['video'])
@include('site.partials.video')
@endif

@if ($visible['activities'])
<section class="sec" id="activities" aria-labelledby="act-h">
  <div class="wrap actwrap">
    @if ($sec('activities')->image)<figure class="photo photo--act reveal">{{ Site::img($sec('activities')->image, $sec('activities')->tr('image_alt', $L), 'lazy', '', '(min-width: 1024px) 40vw, 100vw') }}</figure>@endif
    <div class="reveal">
      <h2 id="act-h">{{ Site::hl($text('activities', 'headline')) }}</h2>
      <p class="lead">{{ $text('activities', 'lead') }}</p>
      <div class="acc">
        @foreach ($activities as $i => $a)
          <details @if ($i === 0) open @endif><summary>{{ $a->tr('title', $L) }}</summary><div><p>{{ $a->tr('summary', $L) }}</p>@if ($a->tr('public_note', $L))<p class="status">{{ __('site.activities.status') }}: {{ $a->tr('public_note', $L) }}</p>@endif</div></details>
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

@if ($visible['visit'])
<section class="sec sec--panel" id="visit" aria-labelledby="visit-h">
  <div class="panel">
    {{ Site::contour(14, 2) }}
    <div class="wrap panel__in">
      <div class="reveal"><h2 id="visit-h">{{ $text('visit', 'headline') }}</h2><p class="panel__note">{{ $text('visit', 'note') }}</p></div>
      <dl class="flow reveal">
        @foreach ($steps as $s)<div><dt>{{ $s->tr('title', $L) }}</dt><dd>{{ $s->tr('body', $L) }}</dd></div>@endforeach
      </dl>
    </div>
  </div>
</section>
@endif

@if ($visible['facilities'])
<section class="sec" id="facilities" aria-labelledby="fac-h">
  <div class="wrap">
    <div class="head reveal"><h2 id="fac-h">{{ Site::hl($text('facilities', 'headline')) }}</h2><p class="lead">{{ $text('facilities', 'lead') }}</p></div>
    <div class="fac reveal">
      <div class="fac__list" role="tablist" aria-orientation="vertical">
        @foreach ($facilities as $i => $f)<button type="button" role="tab" class="fac__tab" id="fac-tab-{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="fac-view" tabindex="{{ $i === 0 ? 0 : -1 }}"><strong>{{ $f->tr('title', $L) }}</strong><span>{{ $f->tr('caption', $L) }}</span></button>@endforeach
      </div>
      <figure class="fac__view photo" id="fac-view" role="tabpanel" aria-labelledby="fac-tab-0">
        @foreach ($facilities as $i => $f){{ Site::img($f->image, $f->tr('alt', $L), $i === 0 ? 'eager' : 'lazy', 'class="fac__img'.($i === 0 ? ' on' : '').'"', '(min-width: 1024px) 58vw, 100vw') }}@endforeach
      </figure>
    </div>
  </div>
</section>
@endif

@if ($visible['gallery'])
<section class="sec sec--tint" id="gallery" aria-labelledby="gal-h">
  <div class="wrap">
    <div class="head reveal"><h2 id="gal-h">{{ Site::hl($text('gallery', 'headline')) }}</h2><p class="lead">{{ $text('gallery', 'lead') }}</p></div>
    <div class="filters" role="group" aria-label="{{ Site::plain($text('gallery', 'headline')) }}">
      <button type="button" data-filter="all" aria-pressed="true">{{ __('site.gallery.cats.all') }}</button>
      @foreach ($cats as $c)<button type="button" data-filter="{{ $c }}" aria-pressed="false">{{ __('site.gallery.cats.'.$c) }}</button>@endforeach
    </div>
    <div class="gal">
      @foreach ($gallery as $g)
        <figure class="gal__i" data-cat="{{ $g->category }}"><button type="button" data-src="{{ Site::media($g->image) }}" data-cap="{{ $g->tr('caption', $L) }}" data-alt="{{ $g->tr('alt', $L) }}" aria-label="{{ __('site.gallery.open') }}: {{ $g->tr('caption', $L) }}">{{ Site::img($g->image, $g->tr('alt', $L), 'lazy', '', '(min-width: 1024px) 33vw, 50vw') }}</button><figcaption>{{ $g->tr('caption', $L) }}</figcaption></figure>
      @endforeach
    </div>
    <p class="empty" hidden>{{ __('site.gallery.empty') }}</p>
  </div>
</section>
@endif

@if ($visible['research'])
<section class="sec sec--plain" id="research" aria-labelledby="res-h">
  <div class="wrap resgrid">
    <div class="reveal"><h2 id="res-h">{{ $text('research', 'headline') }}</h2><p class="lead">{{ $text('research', 'lead') }}</p></div>
    @if ($research->isEmpty())
      <div class="empty-note reveal"><h3>{{ __('site.research.empty_title') }}</h3><p>{{ __('site.research.empty_text') }}</p></div>
    @else
      <ul class="evidence-list reveal">
        @foreach ($research as $r)
          <li>
            <h3>{{ $r->title }}</h3>
            <p class="evidence-list__meta">{{ $r->authors }}@if ($r->institution), {{ $r->institution }}@endif, {{ $r->year }}</p>
            <p>{{ $r->tr('summary', $L) }}</p>
            <p class="evidence-list__lim"><strong>{{ __('site.research.limitations') }}:</strong> {{ $r->tr('limitations', $L) }}</p>
            @if ($r->file || $r->url)<a class="evidence-list__link" href="{{ $r->file ? Site::media($r->file) : $r->url }}" target="_blank" rel="noopener" data-evt="research_download">{{ __('site.research.open') }}</a>@endif
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</section>
@endif

@if ($visible['faq'])
<section class="sec sec--tint" id="faq" aria-labelledby="faq-h">
  <div class="wrap faqwrap">
    <div class="head reveal"><h2 id="faq-h">{{ $text('faq', 'headline') }}</h2></div>
    <div class="acc acc--plain reveal">
      @foreach ($faqs as $q)<details data-faq><summary>{{ $q->tr('question', $L) }}</summary><div><p>{{ $q->tr('answer', $L) }}</p></div></details>@endforeach
    </div>
  </div>
</section>
@endif

@if ($visible['contact'])
<section class="sec sec--contact" id="contact" aria-labelledby="con-h">
  {{ Site::contour(10, 5) }}
  <div class="wrap">
    <div class="reveal">
      <h2 id="con-h" class="statement statement--s">{{ Site::hl($text('contact', 'headline')) }}</h2>
      <p class="lead">{{ $text('contact', 'lead') }}</p>
      <div class="cta">
        @if ($waHref)
          <a class="btn btn--primary" href="{{ $waHref }}" target="_blank" rel="noopener" data-evt="wa_click">{!! $ico('whatsapp-logo') !!}{{ __('site.contact.wa') }}<span class="btn__ext">{!! $ico('arrow-up-right') !!}</span></a>
        @else
          <span class="btn btn--primary is-soon" aria-disabled="true">{!! $ico('whatsapp-logo') !!}{{ __('site.contact.wa') }} <small>({{ __('site.contact.soon_tag') }})</small></span>
        @endif
        @if ($mapsUrl)
          <a class="btn btn--outline" href="{{ $mapsUrl }}" target="_blank" rel="noopener" data-evt="maps_click">{!! $ico('map-pin') !!}{{ __('site.contact.maps') }}<span class="btn__ext">{!! $ico('arrow-up-right') !!}</span></a>
        @else
          <span class="btn btn--outline is-soon" aria-disabled="true">{!! $ico('map-pin') !!}{{ __('site.contact.maps') }} <small>({{ __('site.contact.soon_tag') }})</small></span>
        @endif
      </div>
    </div>
    <dl class="info reveal">
      <div><dt>{{ __('site.contact.address') }}</dt><dd>{!! $place?->address ? nl2br(e($place->address)) : $soon !!}@if ($place?->guidance && ($place->guidance[$L] ?? ''))<br><span class="info__hint">{{ $place->guidance[$L] }}</span>@endif</dd></div>
      <div><dt>{{ __('site.contact.phone') }}</dt><dd>@if ($place?->phone && $wa)<a href="tel:+{{ $wa }}">{{ $place->phone }}</a>@elseif ($place?->phone){{ $place->phone }}@else{!! $soon !!}@endif</dd></div>
      <div><dt>{{ __('site.contact.hours') }}</dt><dd>@if ($place?->hours && ($place->hours[$L] ?? '')){{ $place->hours[$L] }}@else{!! $soon !!}@endif</dd></div>
    </dl>
    @if ($sec('contact')->image)<figure class="photo photo--band reveal">{{ Site::img($sec('contact')->image, $sec('contact')->tr('image_alt', $L), 'lazy', '', '(min-width: 1240px) 1200px, 100vw') }}</figure>@endif
  </div>
</section>
@endif
</main>

<footer class="foot">
  {{ Site::contour(8, 9) }}
  <div class="wrap foot__in">
    <div class="foot__top">
      <strong class="foot__brand">{{ $brand }}</strong>
      <p class="foot__note">{{ $settings->footerText('disclaimer', $L, __('site.footer.disclaimer')) }}</p>
    </div>
    <div class="foot__bottom">
      <nav class="foot__nav" aria-label="Footer">@foreach ($navItems as [$id, $label])<a href="#{{ $id }}">{{ $label }}</a>@endforeach</nav>
      @if ($socials->isNotEmpty())
      <ul class="foot__social" aria-label="Social media">
        @foreach ($socials as $key => $label)<li><a href="{{ $settings->socials[$key] }}" target="_blank" rel="noopener" aria-label="{{ $label }}">{!! $ico($socialIcons[$key]) !!}<span>{{ $label }}</span></a></li>@endforeach
      </ul>
      @endif
      <div class="foot__tools">
        @include('site.partials.switcher', ['cls' => 'lang--foot'])
        <a class="foot__privacy" href="#privacy" id="privacy-link">{{ __('site.footer.privacy') }}</a>
      </div>
      <small class="foot__copy">&copy; {{ date('Y') }} {{ $brand }}. {{ __('site.footer.rights') }}</small>
    </div>
  </div>
  <div class="wrap privacy" id="privacy" hidden><h3>{{ __('site.footer.privacy') }}</h3><p>{{ $settings->footerText('privacy_body', $L, __('site.footer.privacy_body')) }}</p></div>
</footer>

@if ($waHref)
<a class="float" href="{{ $waHref }}" target="_blank" rel="noopener" data-evt="wa_click" aria-label="{{ __('site.float_wa') }}">{!! $ico('whatsapp-logo') !!}<span>{{ __('site.float_wa') }}</span></a>
@endif

<div class="lb" id="lb" hidden role="dialog" aria-modal="true" aria-label="{{ Site::plain($text('gallery', 'headline')) }}">
  <button class="lb__x" aria-label="{{ __('site.gallery.close') }}">&times;</button>
  <button class="lb__p" aria-label="{{ __('site.gallery.prev') }}">&lsaquo;</button>
  <figure class="lb__c"><img id="lb-img" src="" alt=""><figcaption id="lb-cap"></figcaption></figure>
  <button class="lb__n" aria-label="{{ __('site.gallery.next') }}">&rsaquo;</button>
</div>
<script src="/assets/app.js?v={{ filemtime(public_path('assets/app.js')) }}" defer></script>
</body>
</html>
