<div class="lang {{ $cls }}" role="group" aria-label="{{ __('site.switch_label') }}" data-active="{{ $L }}"><span class="lang__thumb" aria-hidden="true"></span>
    <a href="/id/" data-lang="id" lang="id" hreflang="id-ID" @if ($L === 'id') aria-current="true" @endif aria-label="Bahasa Indonesia">ID</a>
    <a href="/en/" data-lang="en" lang="en" hreflang="en-US" @if ($L === 'en') aria-current="true" @endif aria-label="English">EN</a></div>
