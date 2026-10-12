// Static site generator for Griya Balur Bukian Mertasari (bilingual id-ID / en-US)
// Usage: node build.mjs   -> outputs to ./dist
// Design direction: see DESIGN.md
import { mkdirSync, writeFileSync, cpSync, rmSync, readFileSync } from 'node:fs';

// ---------- CONFIG: fill these in (see PRD section 15). Empty = shown as "coming soon" ----------
const CFG = {
  brand: 'Griya Balur Bukian Mertasari',
  siteUrl: 'https://example.com', // production domain
  wa: '', // official WhatsApp number, E.164 without "+", e.g. 6281234567890
  phoneDisplay: '', // e.g. +62 812-3456-7890
  mapsUrl: '', // verified Google Maps URL
  address: '', // official address (not translated)
  hours: '', // service hours
};

// TEST PHOTOS (Wikimedia Commons, Bali). Replace the files in assets/img/ with the real documentation.
const IMG = { hero: 'hero.jpg', about: 'about.jpg', act: 'g2.jpg', contact: 'g5.jpg' };

const T = {
  id: {
    lang: 'id-ID', ogLocale: 'id_ID',
    title: 'Griya Balur Bukian Mertasari | Mengenal BALUR',
    desc: 'Profil, pendekatan, fasilitas, dan dokumentasi kegiatan BALUR di Griya Balur Bukian Mertasari. Informasi edukatif dan terbuka.',
    skip: 'Lewati ke konten utama',
    nav: [['about', 'Tentang Kami'], ['balur', 'BALUR'], ['activities', 'Kegiatan'], ['facilities', 'Fasilitas'], ['gallery', 'Galeri'], ['research', 'Penelitian'], ['contact', 'Kontak']],
    menu: 'Menu', switchLabel: 'Pilih bahasa: Bahasa Indonesia atau English',
    hero: {
      h1: 'Griya Balur Bukian Mertasari',
      sub: 'Mengenal BALUR melalui ruang, aktivitas, dan informasi yang terbuka.',
      c1: 'Lihat fasilitas', c2: 'Tanya admin',
      alt: 'Terasering sawah di Bali pada pagi hari',
    },
    about: {
      h: 'Sebuah griya untuk mengenal *BALUR*.',
      p: ['Griya Balur Bukian Mertasari menyajikan informasi tentang kegiatan BALUR, latar belakang pendekatan biofisika, tempat, dan dokumentasi aktivitas secara terbuka.',
        'Detail layanan dan afiliasi lembaga ditayangkan setelah diverifikasi bersama pengelola.'],
      alt: 'Pavilion bernuansa tradisional Bali dikelilingi taman',
    },
    balur: {
      h: 'Tiga gagasan yang menjadi kerangka pendekatan kami.',
      lead: 'Ini konsep yang diperkenalkan organisasi. Isinya kerangka berpikir, tanpa klaim hasil dan tanpa janji manfaat kesehatan.',
      items: [
        ['Keseimbangan alam', 'Manusia dipandang sebagai bagian dari alam. Kegiatan memakai bahan alami seperti herbal dan kopi lokal.'],
        ['Biofisika', 'Sudut pandang fisika dipakai untuk memahami proses di dalam tubuh, sebagai bahan diskusi dan edukasi.'],
        ['Sains kompleksitas', 'Tubuh dilihat sebagai sistem yang saling terhubung, sehingga informasi disampaikan utuh dan hati-hati.'],
      ],
      note: 'Konsep ini belum merupakan bukti efektivitas medis. Dokumen pendukung yang telah disahkan ada di bagian Penelitian.',
    },
    act: {
      h: 'Kegiatan yang kami *kenalkan*.',
      lead: 'Ringkasan non-preskriptif. Tiap kegiatan ditayangkan setelah melalui tinjauan konten.',
      status: 'Status konten', review: 'menunggu tinjauan',
      items: [
        ['Pembaluran herbal', 'Pembaluran dengan ramuan herbal sebagai bagian dari pengalaman BALUR, didampingi tim kami.'],
        ['Edukasi dan diskusi', 'Penjelasan tentang konsep BALUR, bahan yang digunakan, dan hal yang perlu disiapkan sebelum kegiatan.'],
        ['Kopi Balur', 'Pengenalan Kopi Balur sebagai produk pendukung. Deskripsi produk dipisahkan dari klaim pengobatan.'],
        ['Konsultasi awal', 'Percakapan awal dengan admin untuk menyampaikan kebutuhan dan pertanyaan sebelum berkunjung.'],
      ],
      alt: 'Rempah dan umbi-umbian untuk ramuan herbal',
    },
    visit: {
      h: 'Dari pesan pertama sampai tiba di griya.',
      steps: [['Tanya admin', 'Kirim pesan WhatsApp untuk informasi lokasi dan ketersediaan.'], ['Konsultasi awal', 'Sampaikan kondisi dan pertanyaan Anda sebelum datang.'], ['Penjelasan layanan', 'Tim menjelaskan kegiatan, batasan, dan hal yang perlu diperhatikan.'], ['Kunjungan', 'Datang pada waktu yang telah disepakati.']],
      note: 'Alur umum, akan disesuaikan dengan SOP resmi.',
    },
    fac: {
      h: 'Ruang yang *kami sediakan*.',
      lead: 'Foto di sini contoh. Foto fasilitas asli menyusul setelah dokumentasi tersedia.',
      items: [
        ['Area penerimaan', 'Taman dan kolam di area depan', 'fac1.jpg', 'Taman dengan kolam, pepohonan kelapa, dan bangunan beratap alang-alang'],
        ['Ruang tunggu', 'Relief batu dan bunga kamboja', 'fac3.jpg', 'Relief batu dengan bunga kamboja merah muda'],
        ['Ruang kegiatan', 'Ruang perawatan dengan cahaya alami', 'fac2.jpg', 'Ruang perawatan dengan tempat tidur dan tirai putih'],
        ['Sarana pendukung', 'Hamparan sawah di sekitar lokasi', 'fac5.jpg', 'Sawah dan rumah-rumah kecil di lereng bukit'],
        ['Area luar', 'Terasering di sisi timur', 'fac4.jpg', 'Terasering sawah dikelilingi pohon kelapa'],
      ],
    },
    gal: {
      h: 'Lihat suasana dan *kegiatan* kami.',
      lead: 'Setiap foto ditampilkan dengan izin subjek yang tercatat.',
      cats: { all: 'Semua', exterior: 'Luar ruang', facility: 'Fasilitas', activity: 'Aktivitas', product: 'Produk' },
      empty: 'Belum ada foto pada kategori ini.', close: 'Tutup', prev: 'Sebelumnya', next: 'Berikutnya', open: 'Buka gambar',
      items: [
        ['fac1.jpg', 'exterior', 'Halaman depan', 'Halaman depan dengan kolam dan pepohonan kelapa'],
        ['g1.jpg', 'exterior', 'Sesaji bunga', 'Kelopak bunga di dalam bejana batu'],
        ['fac2.jpg', 'facility', 'Ruang perawatan', 'Ruang perawatan dengan cahaya alami'],
        ['g2.jpg', 'activity', 'Ramuan herbal', 'Rempah dan umbi-umbian untuk ramuan'],
        ['g3.jpg', 'product', 'Biji kopi sangrai', 'Biji kopi sangrai'],
        ['fac3.jpg', 'facility', 'Relief batu', 'Relief batu dengan bunga kamboja'],
        ['g4.jpg', 'activity', 'Bumbu dan rempah', 'Aneka bumbu dan rempah'],
        ['g5.jpg', 'exterior', 'Sawah sekitar lokasi', 'Terasering sawah di Tirta Gangga'],
      ],
    },
    res: {
      h: 'Penelitian dan dokumen pendukung.',
      lead: 'Hanya dokumen asli yang telah disahkan penanggung jawab yang kami tayangkan, lengkap dengan penulis, institusi, tahun, jenis studi, dan keterbatasannya.',
      emptyH: 'Belum ada dokumen yang ditayangkan',
      emptyP: 'Dokumen sedang diverifikasi. Pernyataan pada materi cetak lama tidak ditampilkan sebelum bukti lengkap dan izin penggunaannya tersedia.',
    },
    faq: {
      h: 'Pertanyaan yang sering diajukan.',
      items: [
        ['Di mana lokasinya dan kapan buka?', 'Alamat resmi dan jam layanan tercantum di bagian Kontak. Hubungi admin untuk memastikan ketersediaan sebelum datang.'],
        ['Bagaimana cara berkonsultasi sebelum berkunjung?', 'Hubungi admin melalui WhatsApp. Admin akan menjelaskan alur dan informasi yang perlu disiapkan.'],
        ['Apakah layanan ini menggantikan perawatan medis?', 'Tidak. Informasi di situs ini bersifat edukatif dan bukan pengganti konsultasi dengan tenaga kesehatan.'],
        ['Apakah BALUR menjanjikan kesembuhan?', 'Tidak. Kami tidak memberikan diagnosis maupun janji kesembuhan. Kami memperkenalkan kegiatan dan pendekatan secara terbuka.'],
        ['Apa yang perlu disiapkan sebelum berkunjung?', 'Sampaikan kondisi kesehatan dan pertanyaan Anda kepada admin lebih dulu. Tim akan menjelaskan persiapan yang sesuai.'],
      ],
    },
    contact: {
      h: 'Punya pertanyaan tentang lokasi atau *kunjungan*?',
      p: 'Hubungi admin kami untuk informasi resmi.',
      wa: 'Chat WhatsApp', maps: 'Petunjuk arah', addr: 'Alamat', phone: 'Telepon', hours: 'Jam layanan',
      soon: 'Menyusul, akan diisi pengelola', soonTag: 'menyusul',
      waMsg: 'Halo, saya ingin bertanya tentang Griya Balur Bukian Mertasari.',
      alt: 'Sawah bertingkat di sekitar lokasi',
    },
    footer: {
      disclaimer: 'Informasi pada situs ini bersifat edukatif, bukan diagnosis atau pengganti konsultasi medis.',
      privacy: 'Kebijakan Privasi', rights: 'Hak cipta dilindungi.',
      privacyBody: 'Situs ini tidak meminta atau menyimpan data medis pengunjung. Analitik hanya mencatat peristiwa klik (WhatsApp, peta, galeri, unduhan) tanpa data pribadi.',
    },
    floatWa: 'Tanya admin',
  },
  en: {
    lang: 'en-US', ogLocale: 'en_US',
    title: 'Griya Balur Bukian Mertasari | Explore BALUR',
    desc: 'Our organization, approach, facilities, and documented BALUR activities at Griya Balur Bukian Mertasari. Educational and openly shared information.',
    skip: 'Skip to main content',
    nav: [['about', 'About'], ['balur', 'BALUR'], ['activities', 'Activities'], ['facilities', 'Facilities'], ['gallery', 'Gallery'], ['research', 'Research'], ['contact', 'Contact']],
    menu: 'Menu', switchLabel: 'Choose language: Bahasa Indonesia or English',
    hero: {
      h1: 'Griya Balur Bukian Mertasari',
      sub: 'Explore BALUR through our space, activities, and openly shared information.',
      c1: 'See the facilities', c2: 'Ask our admin',
      alt: 'Rice terraces in Bali at morning light',
    },
    about: {
      h: 'A place to get to know *BALUR*.',
      p: ['Griya Balur Bukian Mertasari shares information about BALUR activities, the biophysics background of the approach, our space, and documentation of our activities.',
        'Service details and institutional affiliations are published once verified with the management.'],
      alt: 'A traditional Balinese pavilion surrounded by garden',
    },
    balur: {
      h: 'Three ideas that frame our approach.',
      lead: 'These are concepts introduced by the organization. They describe a way of thinking, with no claims of results and no promised health benefits.',
      items: [
        ['Balance with nature', 'People are seen as part of nature. Activities use natural materials such as herbs and local coffee.'],
        ['Biophysics', 'Physics serves as a lens for understanding processes in the body, offered for discussion and education.'],
        ['Complexity science', 'The body is viewed as an interconnected system, so information is shared whole and with care.'],
      ],
      note: 'These concepts are not evidence of medical effectiveness. Approved supporting documents are listed under Research.',
    },
    act: {
      h: 'Activities we *introduce*.',
      lead: 'Non-prescriptive summaries. Each activity goes live only after content review.',
      status: 'Content status', review: 'pending review',
      items: [
        ['Herbal balur application', 'A balur application with herbal preparations as part of the BALUR experience, guided by our team.'],
        ['Education and discussion', 'An explanation of the BALUR concept, the materials used, and what to prepare before an activity.'],
        ['Kopi Balur', 'An introduction to Kopi Balur as a supporting product. Product descriptions are kept separate from any treatment claims.'],
        ['Initial consultation', 'A first conversation with our admin to share your needs and questions before visiting.'],
      ],
      alt: 'Spices and roots used for herbal preparations',
    },
    visit: {
      h: 'From the first message to arriving at the griya.',
      steps: [['Ask our admin', 'Message us on WhatsApp for location details and availability.'], ['Initial consultation', 'Share your condition and questions before you arrive.'], ['Service overview', 'Our team explains the activities, limitations, and what to keep in mind.'], ['Visit', 'Come at the time we agreed on.']],
      note: 'A general flow, to be aligned with our official SOP.',
    },
    fac: {
      h: 'The spaces *we offer*.',
      lead: 'The photos here are samples. Original facility photos follow once documentation is ready.',
      items: [
        ['Reception area', 'Garden and pool at the front', 'fac1.jpg', 'Garden with a pool, coconut palms, and thatched buildings'],
        ['Waiting area', 'Stone relief and frangipani', 'fac3.jpg', 'Stone relief with pink frangipani flowers'],
        ['Activity room', 'Treatment room with natural light', 'fac2.jpg', 'Treatment room with a bed and white curtains'],
        ['Supporting amenities', 'Rice fields around the site', 'fac5.jpg', 'Rice fields and small houses on a hillside'],
        ['Outdoor area', 'Terraces on the east side', 'fac4.jpg', 'Rice terraces surrounded by coconut palms'],
      ],
    },
    gal: {
      h: 'A closer look at our spaces and *activities*.',
      lead: 'Every photo is shown with recorded consent from the people in it.',
      cats: { all: 'All', exterior: 'Outdoors', facility: 'Facilities', activity: 'Activities', product: 'Products' },
      empty: 'No photos in this category yet.', close: 'Close', prev: 'Previous', next: 'Next', open: 'Open image',
      items: [
        ['fac1.jpg', 'exterior', 'Front garden', 'Front garden with a pool and coconut palms'],
        ['g1.jpg', 'exterior', 'Flower offering', 'Flower petals in a stone vessel'],
        ['fac2.jpg', 'facility', 'Treatment room', 'Treatment room with natural light'],
        ['g2.jpg', 'activity', 'Herbal preparations', 'Spices and roots for preparations'],
        ['g3.jpg', 'product', 'Roasted coffee beans', 'Roasted coffee beans'],
        ['fac3.jpg', 'facility', 'Stone relief', 'Stone relief with frangipani'],
        ['g4.jpg', 'activity', 'Herbs and spices', 'Assorted herbs and spices'],
        ['g5.jpg', 'exterior', 'Rice terraces nearby', 'Rice terraces at Tirta Gangga'],
      ],
    },
    res: {
      h: 'Research and supporting documents.',
      lead: 'We publish only original documents approved by the responsible party, with authors, institution, year, study type, and limitations.',
      emptyH: 'No documents published yet',
      emptyP: 'Documents are being verified. Statements from older printed material are not shown until complete evidence and permission to use it are available.',
    },
    faq: {
      h: 'Frequently asked questions.',
      items: [
        ['Where are you and when are you open?', 'The official address and hours are listed under Contact. Message our admin to confirm availability before visiting.'],
        ['How can I consult before visiting?', 'Message our admin on WhatsApp. They will explain the process and what to prepare.'],
        ['Does this replace medical care?', 'No. Information on this site is educational and is not a substitute for advice from a healthcare professional.'],
        ['Does BALUR promise a cure?', 'No. We do not provide diagnoses or promise cures. We openly introduce our activities and approach.'],
        ['What should I prepare before visiting?', 'Share your health condition and questions with our admin first. The team will explain suitable preparation.'],
      ],
    },
    contact: {
      h: 'Questions about our location or *visiting* us?',
      p: 'Contact our team for official information.',
      wa: 'Chat on WhatsApp', maps: 'Get directions', addr: 'Address', phone: 'Phone', hours: 'Hours',
      soon: 'Coming soon, to be filled in by the management', soonTag: 'coming soon',
      waMsg: 'Hello, I would like to ask about Griya Balur Bukian Mertasari.',
      alt: 'Terraced rice fields near the site',
    },
    footer: {
      disclaimer: 'Information on this site is educational and is not a diagnosis or a substitute for professional medical advice.',
      privacy: 'Privacy Policy', rights: 'All rights reserved.',
      privacyBody: 'This site does not request or store visitors’ medical data. Analytics only records click events (WhatsApp, maps, gallery, downloads) without personal data.',
    },
    floatWa: 'Ask our admin',
  },
};

const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
// "*word*" becomes italic emphasis in the same typeface (identity: one italic word per major heading)
const hl = (s) => esc(s).replace(/\*(.+?)\*/g, '<em>$1</em>');
const plain = (s) => s.replace(/\*/g, '');

// Phosphor Light icons (assets/icons/*.svg), inlined so they inherit currentColor
const icon = (name) => readFileSync(`assets/icons/${name}.svg`, 'utf8').replace('<svg ', '<svg aria-hidden="true" focusable="false" ');
// Read JPEG dimensions so every <img> reserves its space (no layout shift, lazy masonry works)
const sizes = {};
function jpegSize(file) {
  if (sizes[file]) return sizes[file];
  const b = readFileSync(`assets/img/${file}`);
  let i = 2;
  while (i < b.length) {
    if (b[i] !== 0xff) { i++; continue; }
    const m = b[i + 1];
    if (m >= 0xc0 && m <= 0xcf && m !== 0xc4 && m !== 0xc8 && m !== 0xcc) return (sizes[file] = { h: b.readUInt16BE(i + 5), w: b.readUInt16BE(i + 7) });
    i += 2 + b.readUInt16BE(i + 2);
  }
  return (sizes[file] = { w: 4, h: 3 });
}
const img = (src, alt, extra = '') => { const { w, h } = jpegSize(src); return `<img src="/assets/img/${src}" alt="${esc(alt)}" width="${w}" height="${h}" loading="lazy" decoding="async" ${extra}>`; };

// Identity motif: terrace contour lines (deterministic, no randomness so builds are stable)
function contour(lines, seed) {
  const W = 1200, H = 420, step = 12;
  let d = '';
  for (let i = 0; i < lines; i++) {
    const y0 = 30 + (i * (H - 60)) / (lines - 1);
    const a1 = 16 + ((i * 7 + seed) % 5) * 3, a2 = 6 + ((i * 3 + seed) % 4) * 2;
    const p1 = seed + i * 0.45, p2 = seed * 1.7 + i * 0.9;
    let path = '';
    for (let x = 0; x <= W; x += step) {
      const y = y0 + a1 * Math.sin(x / 150 + p1) + a2 * Math.sin(x / 57 + p2);
      path += `${x === 0 ? 'M' : 'L'}${x} ${y.toFixed(1)}`;
    }
    d += `<path d="${path}"/>`;
  }
  return `<svg class="contour" viewBox="0 0 ${W} ${H}" preserveAspectRatio="none" aria-hidden="true" focusable="false">${d}</svg>`;
}

function page(loc) {
  const t = T[loc];
  const other = loc === 'id' ? 'en' : 'id';
  const waHref = CFG.wa ? `https://wa.me/${CFG.wa}?text=${encodeURIComponent(t.contact.waMsg)}` : '';
  const url = (l) => `${CFG.siteUrl}/${l}/`;
  const soon = `<span class="todo">${esc(t.contact.soon)}</span>`;
  const field = (v) => (v ? esc(v) : soon);

  const switcher = (cls) => `<div class="lang ${cls}" role="group" aria-label="${esc(t.switchLabel)}" data-active="${loc}"><span class="lang__thumb" aria-hidden="true"></span>
    <a href="/id/" data-lang="id" lang="id" hreflang="id-ID" ${loc === 'id' ? 'aria-current="true"' : ''} aria-label="Bahasa Indonesia">ID</a>
    <a href="/en/" data-lang="en" lang="en" hreflang="en-US" ${loc === 'en' ? 'aria-current="true"' : ''} aria-label="English">EN</a></div>`;
  const navLinks = t.nav.map(([id, l]) => `<a href="#${id}">${esc(l)}</a>`).join('');
  const ld = { '@context': 'https://schema.org', '@type': 'Organization', name: CFG.brand, url: url(loc), inLanguage: t.lang };

  const waBtn = waHref
    ? `<a class="btn btn--primary" href="${waHref}" target="_blank" rel="noopener" data-evt="wa_click">${icon('whatsapp-logo')}${esc(t.contact.wa)}<span class="btn__ext">${icon('arrow-up-right')}</span></a>`
    : `<span class="btn btn--primary is-soon" aria-disabled="true">${icon('whatsapp-logo')}${esc(t.contact.wa)} <small>(${esc(t.contact.soonTag)})</small></span>`;
  const mapsBtn = CFG.mapsUrl
    ? `<a class="btn btn--outline" href="${CFG.mapsUrl}" target="_blank" rel="noopener" data-evt="maps_click">${icon('map-pin')}${esc(t.contact.maps)}<span class="btn__ext">${icon('arrow-up-right')}</span></a>`
    : `<span class="btn btn--outline is-soon" aria-disabled="true">${icon('map-pin')}${esc(t.contact.maps)} <small>(${esc(t.contact.soonTag)})</small></span>`;

  return `<!doctype html>
<html lang="${t.lang}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${esc(t.title)}</title>
<meta name="description" content="${esc(t.desc)}">
<link rel="canonical" href="${url(loc)}">
<link rel="alternate" hreflang="id-ID" href="${url('id')}">
<link rel="alternate" hreflang="en-US" href="${url('en')}">
<link rel="alternate" hreflang="x-default" href="${url('en')}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="${esc(CFG.brand)}">
<meta property="og:title" content="${esc(t.title)}">
<meta property="og:description" content="${esc(t.desc)}">
<meta property="og:url" content="${url(loc)}">
<meta property="og:image" content="${CFG.siteUrl}/assets/img/${IMG.hero}">
<meta property="og:locale" content="${t.ogLocale}">
<meta property="og:locale:alternate" content="${T[other].ogLocale}">
<meta name="theme-color" content="#193B32">
<link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
<link rel="preload" as="image" href="/assets/img/${IMG.hero}" fetchpriority="high">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500;1,600&display=swap">
<link rel="stylesheet" href="/assets/style.css">
<script type="application/ld+json">${JSON.stringify(ld)}</script>
</head>
<body data-locale="${loc}">
<a class="skip" href="#main">${esc(t.skip)}</a>

<span class="nav-sentinel" aria-hidden="true"></span>
<header class="nav" id="top">
  <div class="nav__in">
    <a class="brand" href="#main" aria-label="${esc(CFG.brand)}"><span class="brand__mark">${icon('leaf')}</span><span class="brand__name">${esc(CFG.brand)}</span></a>
    <nav class="nav__links" aria-label="Main">${navLinks}</nav>
    <div class="nav__tools">${switcher('lang--desk')}<button class="burger" aria-expanded="false" aria-controls="sheet" aria-label="${esc(t.menu)}"><span></span><span></span></button></div>
  </div>
</header>
<div class="sheet" id="sheet" hidden>
  <nav aria-label="Mobile">${t.nav.map(([id, l], i) => `<a href="#${id}" style="--i:${i}">${esc(l)}</a>`).join('')}</nav>
  ${switcher('lang--mob')}
</div>

<main id="main">
<section class="hero" aria-labelledby="h1">
  <img class="hero__img" src="/assets/img/${IMG.hero}" alt="${esc(t.hero.alt)}" fetchpriority="high" decoding="async">
  <div class="hero__scrim"></div>
  <div class="wrap hero__in">
    <h1 id="h1">${esc(t.hero.h1)}</h1>
    <p class="hero__sub">${esc(t.hero.sub)}</p>
    <div class="cta">
      <a class="btn btn--primary" href="#facilities">${esc(t.hero.c1)}</a>
      <a class="btn btn--onphoto" href="#contact">${esc(t.hero.c2)}</a>
    </div>
  </div>
</section>

<section class="sec sec--about" id="about" aria-labelledby="about-h">
  <div class="wrap">
    <h2 id="about-h" class="statement reveal">${hl(t.about.h)}</h2>
    <div class="about reveal">
      <div class="about__txt">${t.about.p.map((x) => `<p>${esc(x)}</p>`).join('')}</div>
      <figure class="photo about__ph">${img(IMG.about, t.about.alt)}</figure>
    </div>
  </div>
</section>

<section class="sec sec--tint" id="balur" aria-labelledby="balur-h">
  <div class="wrap">
    <div class="head reveal"><h2 id="balur-h">${esc(t.balur.h)}</h2><p class="lead">${esc(t.balur.lead)}</p></div>
    <ol class="index">
      ${t.balur.items.map(([h, p]) => `<li class="index__row reveal"><h3>${esc(h)}</h3><p>${esc(p)}</p></li>`).join('')}
    </ol>
    <p class="note reveal">${esc(t.balur.note)}</p>
  </div>
</section>

<section class="sec" id="activities" aria-labelledby="act-h">
  <div class="wrap actwrap">
    <figure class="photo photo--act reveal">${img(IMG.act, t.act.alt)}</figure>
    <div class="reveal">
      <h2 id="act-h">${hl(t.act.h)}</h2>
      <p class="lead">${esc(t.act.lead)}</p>
      <div class="acc">
        ${t.act.items.map(([h, p], i) => `<details${i === 0 ? ' open' : ''}><summary>${esc(h)}</summary><div><p>${esc(p)}</p><p class="status">${esc(t.act.status)}: ${esc(t.act.review)}</p></div></details>`).join('')}
      </div>
    </div>
  </div>
</section>

<section class="sec sec--panel" id="visit" aria-labelledby="visit-h">
  <div class="panel">
    ${contour(14, 2)}
    <div class="wrap panel__in">
      <div class="reveal"><h2 id="visit-h">${esc(t.visit.h)}</h2><p class="panel__note">${esc(t.visit.note)}</p></div>
      <dl class="flow reveal">
        ${t.visit.steps.map(([h, p]) => `<div><dt>${esc(h)}</dt><dd>${esc(p)}</dd></div>`).join('')}
      </dl>
    </div>
  </div>
</section>

<section class="sec" id="facilities" aria-labelledby="fac-h">
  <div class="wrap">
    <div class="head reveal"><h2 id="fac-h">${hl(t.fac.h)}</h2><p class="lead">${esc(t.fac.lead)}</p></div>
    <div class="fac reveal">
      <div class="fac__list" role="tablist" aria-orientation="vertical">
        ${t.fac.items.map(([name, cap], i) => `<button type="button" role="tab" class="fac__tab" id="fac-tab-${i}" aria-selected="${i === 0}" aria-controls="fac-view" tabindex="${i === 0 ? 0 : -1}"><strong>${esc(name)}</strong><span>${esc(cap)}</span></button>`).join('')}
      </div>
      <figure class="fac__view photo" id="fac-view" role="tabpanel" aria-labelledby="fac-tab-0">
        ${t.fac.items.map(([name, cap, file, alt], i) => `<img class="fac__img${i === 0 ? ' on' : ''}" src="/assets/img/${file}" alt="${esc(alt)}" width="${jpegSize(file).w}" height="${jpegSize(file).h}" loading="${i === 0 ? 'eager' : 'lazy'}" decoding="async">`).join('')}
      </figure>
    </div>
  </div>
</section>

<section class="sec sec--tint" id="gallery" aria-labelledby="gal-h">
  <div class="wrap">
    <div class="head reveal"><h2 id="gal-h">${hl(t.gal.h)}</h2><p class="lead">${esc(t.gal.lead)}</p></div>
    <div class="filters" role="group" aria-label="${esc(plain(t.gal.h))}">
      ${Object.entries(t.gal.cats).map(([k, v], i) => `<button type="button" data-filter="${k}" aria-pressed="${i === 0}">${esc(v)}</button>`).join('')}
    </div>
    <div class="gal">
      ${t.gal.items.map(([file, cat, cap, alt]) => `<figure class="gal__i" data-cat="${cat}"><button type="button" data-src="/assets/img/${file}" data-cap="${esc(cap)}" data-alt="${esc(alt)}" aria-label="${esc(t.gal.open)}: ${esc(cap)}">${img(file, alt)}</button><figcaption>${esc(cap)}</figcaption></figure>`).join('')}
    </div>
    <p class="empty" hidden>${esc(t.gal.empty)}</p>
  </div>
</section>

<section class="sec sec--plain" id="research" aria-labelledby="res-h">
  <div class="wrap resgrid">
    <div class="reveal"><h2 id="res-h">${esc(t.res.h)}</h2><p class="lead">${esc(t.res.lead)}</p></div>
    <div class="empty-note reveal"><h3>${esc(t.res.emptyH)}</h3><p>${esc(t.res.emptyP)}</p></div>
  </div>
</section>

<section class="sec sec--tint" id="faq" aria-labelledby="faq-h">
  <div class="wrap faqwrap">
    <div class="head reveal"><h2 id="faq-h">${esc(t.faq.h)}</h2></div>
    <div class="acc acc--plain reveal">
      ${t.faq.items.map(([q, a]) => `<details data-faq><summary>${esc(q)}</summary><div><p>${esc(a)}</p></div></details>`).join('')}
    </div>
  </div>
</section>

<section class="sec sec--contact" id="contact" aria-labelledby="con-h">
  ${contour(10, 5)}
  <div class="wrap">
    <div class="reveal">
      <h2 id="con-h" class="statement statement--s">${hl(t.contact.h)}</h2>
      <p class="lead">${esc(t.contact.p)}</p>
      <div class="cta">${waBtn}${mapsBtn}</div>
    </div>
    <dl class="info reveal">
      <div><dt>${esc(t.contact.addr)}</dt><dd>${field(CFG.address)}</dd></div>
      <div><dt>${esc(t.contact.phone)}</dt><dd>${CFG.phoneDisplay && CFG.wa ? `<a href="tel:+${CFG.wa}">${esc(CFG.phoneDisplay)}</a>` : soon}</dd></div>
      <div><dt>${esc(t.contact.hours)}</dt><dd>${field(CFG.hours)}</dd></div>
    </dl>
    <figure class="photo photo--band reveal">${img(IMG.contact, t.contact.alt)}</figure>
  </div>
</section>
</main>

<footer class="foot">
  ${contour(8, 9)}
  <div class="wrap foot__in">
    <div class="foot__top">
      <strong class="foot__brand">${esc(CFG.brand)}</strong>
      <p class="foot__note">${esc(t.footer.disclaimer)}</p>
    </div>
    <div class="foot__bottom">
      <nav class="foot__nav" aria-label="Footer">${navLinks}</nav>
      <div class="foot__tools">
        ${switcher('lang--foot')}
        <a class="foot__privacy" href="#privacy" id="privacy-link">${esc(t.footer.privacy)}</a>
      </div>
      <small class="foot__copy">&copy; ${new Date().getFullYear()} ${esc(CFG.brand)}. ${esc(t.footer.rights)}</small>
    </div>
  </div>
  <div class="wrap privacy" id="privacy" hidden><h3>${esc(t.footer.privacy)}</h3><p>${esc(t.footer.privacyBody)}</p></div>
</footer>

${waHref ? `<a class="float" href="${waHref}" target="_blank" rel="noopener" data-evt="wa_click" aria-label="${esc(t.floatWa)}">${icon('whatsapp-logo')}<span>${esc(t.floatWa)}</span></a>` : ''}

<div class="lb" id="lb" hidden role="dialog" aria-modal="true" aria-label="${esc(plain(t.gal.h))}">
  <button class="lb__x" aria-label="${esc(t.gal.close)}">&times;</button>
  <button class="lb__p" aria-label="${esc(t.gal.prev)}">&lsaquo;</button>
  <figure class="lb__c"><img id="lb-img" src="" alt=""><figcaption id="lb-cap"></figcaption></figure>
  <button class="lb__n" aria-label="${esc(t.gal.next)}">&rsaquo;</button>
</div>
<script src="/assets/app.js" defer></script>
</body>
</html>
`;
}

rmSync('dist', { recursive: true, force: true });
for (const l of ['id', 'en']) {
  mkdirSync(`dist/${l}`, { recursive: true });
  writeFileSync(`dist/${l}/index.html`, page(l));
}
cpSync('assets', 'dist/assets', { recursive: true });

// Root: default to /en/ unless a saved preference exists (manual choice beats browser language)
writeFileSync('dist/index.html', `<!doctype html><html lang="en-US"><head><meta charset="utf-8"><title>${CFG.brand}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="canonical" href="${CFG.siteUrl}/en/">
<link rel="alternate" hreflang="id-ID" href="${CFG.siteUrl}/id/"><link rel="alternate" hreflang="en-US" href="${CFG.siteUrl}/en/"><link rel="alternate" hreflang="x-default" href="${CFG.siteUrl}/en/">
<meta http-equiv="refresh" content="0; url=/en/">
<script>try{var l=localStorage.getItem('gb_lang');location.replace('/'+(l==='id'?'id':'en')+'/'+location.hash)}catch(e){}</script>
</head><body><a href="/en/">English</a> | <a href="/id/">Bahasa Indonesia</a></body></html>`);

writeFileSync('dist/sitemap.xml', `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
${['id', 'en'].map((l) => `<url><loc>${CFG.siteUrl}/${l}/</loc><xhtml:link rel="alternate" hreflang="id-ID" href="${CFG.siteUrl}/id/"/><xhtml:link rel="alternate" hreflang="en-US" href="${CFG.siteUrl}/en/"/></url>`).join('\n')}
</urlset>`);
writeFileSync('dist/robots.txt', `User-agent: *\nAllow: /\nSitemap: ${CFG.siteUrl}/sitemap.xml\n`);
console.log('Built dist/ (id, en)');
