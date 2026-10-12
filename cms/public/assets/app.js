(function () {
  var d = document, root = d.documentElement;
  root.classList.add('js');
  var $ = function (s, c) { return (c || d).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  var locale = d.body.dataset.locale;

  // Privacy-aware analytics hook: events carry only name + locale (no personal/medical data).
  function track(name, extra) {
    var p = Object.assign({ locale: locale }, extra || {});
    if (window.gtag) window.gtag('event', name, p);
    if (window.plausible) window.plausible(name, { props: p });
    d.dispatchEvent(new CustomEvent('gb:event', { detail: { name: name, params: p } }));
  }

  // Language switcher: the thumb slides to the chosen language, then the page changes
  // (a cross-document view transition fades the two pages). The choice is saved and the section is kept.
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  $$('.lang a').forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      var to = a.dataset.lang;
      if (to === locale) return;
      try { localStorage.setItem('gb_lang', to); } catch (_) {}
      track('language_switch', { to: to });
      $$('.lang').forEach(function (box) { box.dataset.active = to; });
      var url = a.getAttribute('href') + (location.hash || '');
      if (reduce) location.href = url; else setTimeout(function () { location.href = url; }, 260);
    });
  });

  // Header: floating pill at the top of the page, full-width bar once scrolled (watches a sentinel, no scroll listener)
  var nav = $('.nav'), sentinel = $('.nav-sentinel');
  if ('IntersectionObserver' in window && sentinel) {
    new IntersectionObserver(function (es) {
      nav.classList.toggle('is-stuck', !es[0].isIntersecting);
    }).observe(sentinel);
  }

  // Logo + name: back to the hero at the top, and drop any #section from the URL
  $('.brand').addEventListener('click', function (e) {
    e.preventDefault();
    if (!sheet.hidden) setMenu(false);
    window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
    if (location.hash) history.replaceState(null, '', location.pathname + location.search);
  });

  // Mobile menu
  var burger = $('.burger'), sheet = $('#sheet');
  function setMenu(open) {
    burger.setAttribute('aria-expanded', open);
    sheet.hidden = !open;
    d.body.style.overflow = open ? 'hidden' : '';
  }
  burger.addEventListener('click', function () { setMenu(sheet.hidden); });
  $$('#sheet nav a').forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });
  d.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sheet.hidden) { setMenu(false); burger.focus(); } });

  // Scroll reveal (once)
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12 });
    $$('.reveal').forEach(function (el) { io.observe(el); });
  } else { $$('.reveal').forEach(function (el) { el.classList.add('in'); }); }

  // Click events
  $$('[data-evt]').forEach(function (a) { a.addEventListener('click', function () { track(a.dataset.evt); }); });
  $$('details[data-faq]').forEach(function (dt) { dt.addEventListener('toggle', function () { if (dt.open) track('faq_open'); }); });

  // Privacy toggle
  var pl = $('#privacy-link'), pv = $('#privacy');
  pl.addEventListener('click', function (e) { e.preventDefault(); pv.hidden = !pv.hidden; if (!pv.hidden) pv.scrollIntoView({ behavior: 'smooth' }); });

  // Profile video: a YouTube/Vimeo player is created only after the click, so nothing third-party loads before then
  $('.video__play').forEach(function (b) {
    b.addEventListener('click', function () {
      var frame = b.parentNode, f = d.createElement('iframe');
      f.src = b.dataset.embed;
      f.title = b.dataset.title || '';
      f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
      f.allowFullscreen = true;
      f.referrerPolicy = 'strict-origin-when-cross-origin';
      $('.video__poster, .contour', frame).forEach(function (n) { n.remove(); });
      frame.replaceChild(f, b);
      f.focus();
      track('video_play');
    });
  });
  $('video.video__el').forEach(function (v) { v.addEventListener('play', function () { track('video_play'); }, { once: true }); });

  // Facilities tab viewer (arrow keys, Home, End)
  var tabs = $$('.fac__tab'), fimgs = $$('.fac__img'), panel = $('#fac-view');
  function selectTab(i, focus) {
    tabs.forEach(function (t, j) {
      t.setAttribute('aria-selected', j === i);
      t.tabIndex = j === i ? 0 : -1;
      fimgs[j].classList.toggle('on', j === i);
    });
    panel.setAttribute('aria-labelledby', tabs[i].id);
    if (focus) tabs[i].focus();
  }
  tabs.forEach(function (t, i) {
    t.addEventListener('click', function () { selectTab(i); track('facility_view'); });
    t.addEventListener('keydown', function (e) {
      var n = tabs.length, k = e.key, to = null;
      if (k === 'ArrowDown' || k === 'ArrowRight') to = (i + 1) % n;
      else if (k === 'ArrowUp' || k === 'ArrowLeft') to = (i - 1 + n) % n;
      else if (k === 'Home') to = 0;
      else if (k === 'End') to = n - 1;
      if (to !== null) { e.preventDefault(); selectTab(to, true); }
    });
  });

  // Gallery filter
  var items = $$('.gal__i'), empty = $('.empty');
  $$('.filters button').forEach(function (b) {
    b.addEventListener('click', function () {
      $$('.filters button').forEach(function (x) { x.setAttribute('aria-pressed', x === b); });
      var f = b.dataset.filter, n = 0;
      items.forEach(function (it) { var show = f === 'all' || it.dataset.cat === f; it.hidden = !show; if (show) n++; });
      empty.hidden = n > 0;
    });
  });

  // Lightbox
  var lb = $('#lb'), cap = $('#lb-cap'), lbImg = $('#lb-img'), cur = 0, last;
  var btns = items.map(function (f) { return $('button', f); });
  function visible() { return items.filter(function (i) { return !i.hidden; }).map(function (f) { return $('button', f); }); }
  function show(i) {
    var v = visible(); cur = (i + v.length) % v.length;
    lbImg.src = v[cur].dataset.src; lbImg.alt = v[cur].dataset.alt; cap.textContent = v[cur].dataset.cap;
  }
  function openLb(b) { last = b; lb.hidden = false; show(visible().indexOf(b)); $('.lb__x').focus(); d.body.style.overflow = 'hidden'; track('gallery_open'); }
  function closeLb() { lb.hidden = true; d.body.style.overflow = ''; if (last) last.focus(); }
  btns.forEach(function (b) { b.addEventListener('click', function () { openLb(b); }); });
  $('.lb__x').addEventListener('click', closeLb);
  $('.lb__p').addEventListener('click', function () { show(cur - 1); });
  $('.lb__n').addEventListener('click', function () { show(cur + 1); });
  lb.addEventListener('click', function (e) { if (e.target === lb) closeLb(); });
  d.addEventListener('keydown', function (e) {
    if (lb.hidden) return;
    if (e.key === 'Escape') closeLb();
    if (e.key === 'ArrowLeft') show(cur - 1);
    if (e.key === 'ArrowRight') show(cur + 1);
  });
  var sx = null;
  lb.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
  lb.addEventListener('touchend', function (e) {
    if (sx === null) return; var dx = e.changedTouches[0].clientX - sx;
    if (Math.abs(dx) > 50) show(cur + (dx < 0 ? 1 : -1)); sx = null;
  });
})();
