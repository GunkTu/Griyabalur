@php
    use App\Support\Site;

    $vs = $sections->get('video');
    $vFile = $vs->video_file[$L] ?? null;
    $embed = $vFile ? null : Site::embed($vs->video_url[$L] ?? null);
    $poster = $vs->image;
    $vTitle = Site::plain($vs->tr('headline', $L));
@endphp
<section class="sec sec--video" id="video" aria-labelledby="video-h">
  <div class="wrap">
    <div class="head reveal">
      <h2 id="video-h" class="statement statement--s">{{ Site::hl($vs->tr('headline', $L)) }}</h2>
      <p class="lead">{{ $vs->tr('lead', $L) }}</p>
    </div>
    <div class="video reveal">
      <div class="video__frame">
        @if ($vFile)
          <video class="video__el" controls playsinline preload="none" @if ($poster) poster="{{ Site::media($poster) }}" @endif aria-label="{{ $vTitle }}">
            <source src="{{ Site::media($vFile) }}" type="{{ Site::videoType($vFile) }}">
            {{ __('site.video.unsupported') }}
          </video>
        @else
          {{ Site::contour(9, 4) }}
          @if ($poster){{ Site::img($poster, $vs->tr('image_alt', $L), 'lazy', 'class="video__poster"', '(min-width: 1240px) 1200px, 100vw') }}@endif
          <button type="button" class="video__play" data-embed="{{ $embed['src'] }}" data-title="{{ $vTitle }}" aria-label="{{ __('site.video.play') }}: {{ $vTitle }}">
            <span class="video__btn">{!! Site::icon('play') !!}</span>
          </button>
        @endif
      </div>
      @if ($vs->tr('note', $L))
        <details class="video__summary"><summary>{{ __('site.video.summary') }}</summary><p>{{ $vs->tr('note', $L) }}</p></details>
      @endif
    </div>
  </div>
</section>
