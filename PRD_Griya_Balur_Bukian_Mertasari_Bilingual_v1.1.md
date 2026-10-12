# PRD — Griya Balur Bukian Mertasari | Bilingual Modern One-Page Website

**Versi:** 1.1  |  **Tanggal:** 8 Oktober 2026  |  **Status:** Draft untuk persetujuan klien  
**Basis sumber:** empat gambar brosur BALUR / Kopi Balur (Bahasa Indonesia dan English).

> Status dokumen: perencanaan produk, bukan audit medis, hukum, atau verifikasi klaim kesehatan.

## 01. Ringkasan produk

Membangun website profil dan edukasi publik **Griya Balur Bukian Mertasari**, berfokus pada pengenalan kegiatan BALUR, pendekatan biofisika, fasilitas, serta dokumentasi aktual. Semua materi utama dinavigasi dalam satu halaman panjang (single-page / editorial landing experience) dengan CMS internal untuk mengelola konten dan dokumentasi.

> Keputusan UX: satu halaman untuk pengunjung, namun jalur /admin dan halaman kebijakan privasi dapat berdiri sendiri. Single-page bukan berarti semua materi ilmiah harus dipaksa tampil penuh; ringkasan dapat membuka dokumen bukti/PDF terverifikasi.

| Komponen | Keputusan awal |
| --- | --- |
| Nama proyek dan brand publik | **Griya Balur Bukian Mertasari** — nama resmi yang ditetapkan klien; gunakan identik pada kedua bahasa, logo, header, metadata, dan footer. |
| Jenis | Website corporate profile + edukasi + galeri dokumentasi + lead WhatsApp. |
| Bahasa (MVP) | **Bahasa Indonesia (`id-ID`) + English (United States) (`en-US`) sejak rilis pertama**; pilihan `ID / EN` tersedia di desktop dan mobile. |
| Pengunjung utama | Pengunjung Indonesia dan internasional, calon pengunjung, keluarga pendamping, komunitas serta calon mitra. |
| Tujuan bisnis | Membangun kepercayaan dan transparansi, memperlihatkan tempat/fasilitas, membantu calon pengunjung menghubungi admin. |
| Metrik utama | Klik WhatsApp, klik petunjuk arah, kelengkapan informasi, engagement galeri dan FAQ. |

## 02. Ruang lingkup dan asumsi

### MVP / wajib ada

- Landing page satu halaman dengan navbar anchor, header transparan/blur, footer dan CTA kontak.
- Profil **Griya Balur Bukian Mertasari** dan pengenalan BALUR berdasarkan brosur yang telah ditulis ulang dan diverifikasi.
- Penjelasan pendekatan dan kegiatan dengan terminologi netral serta status verifikasi konten.
- Galeri foto asli lokasi, ruangan, fasilitas, suasana kegiatan, dan dokumentasi edukasi.
- Bagian referensi atau penelitian: hanya dokumen asli yang disahkan pemilik konten/penanggung jawab.
- FAQ, alamat lokasi Malang dan Bali (jika lokasi keduanya masih aktif), WhatsApp dan peta.
- CMS dengan autentikasi, upload media, draft/publish, metadata SEO, dan review konten kesehatan.
- **Dua bahasa lengkap pada MVP (`id-ID` dan `en-US`)**: URL lokal, switcher, copy dan SEO per bahasa, proofreading manual.
- Desain mobile-first, cepat, aman, SEO, dan aksesibel.

### Di luar scope MVP

- Pembayaran online, akun pasien, rekam medis, sistem appointment otomatis, rekomendasi medis personal, chatbot diagnosis, e-commerce produk, portal penelitian interaktif.

Asumsi penting: bisnis mempunyai hak penggunaan foto dan materi brosur, layanan beroperasi legal, serta detail izin operasional akan diserahkan oleh klien. Informasi tersebut belum diverifikasi pada fase PRD.

## 03. Information architecture & urutan halaman

| Urut | Section publik | Tujuan & isi utama | Interaksi |
| --- | --- | --- | --- |
| 01 | Hero immersive | Brand Griya Balur Bukian Mertasari, proposisi nilai netral, foto/video pendek fasilitas. | CTA About BALUR / Facilities / WhatsApp, terlokalisasi menurut bahasa |
| 02 | Tentang kami | Profil Griya Balur Bukian Mertasari, latar belakang, pendekatan, dan hubungan LPPRB hanya bila diverifikasi. | Scroll reveal ringan |
| 03 | Memahami BALUR | Gambaran konsep biofisika dan aktivitas komplementer; hindari klaim manfaat definitif. | 3–4 editorial info cards |
| 04 | Rangkaian kegiatan | Kegiatan yang benar-benar dijalankan; ringkasan non-preskriptif, status keamanan, kebutuhan konsultasi. | Accordion / detail ringkas |
| 05 | Pengalaman kunjungan | Alur informasi: tanya admin, konsultasi/skrining, penjelasan layanan, kunjungan; disesuaikan SOP. | Timeline 4 langkah |
| 06 | Ruang & fasilitas | Foto tempat, ruang tunggu, ruangan kegiatan, sarana, kenyamanan, aksesibilitas. | Bento image grid |
| 07 | Dokumentasi kegiatan | Galeri suasana terapi, edukasi, tim/aktivitas; izin subjek wajib. | Filter kategori + lightbox |
| 08 | Penelitian & transparansi | Daftar publikasi/bukti asli, jenis studi, penulis, tahun, lingkup, batasan. | Baca ringkasan / unduh dokumen |
| 09 | FAQ | Lokasi, jam, cara konsultasi, kesiapan kunjungan, perbedaan layanan, disclaimer. | Accessible accordion |
| 10 | Lokasi & kontak | Alamat resmi Griya Balur Bukian Mertasari sesuai verifikasi terbaru; lokasi Malang/Bali dari brosur bukan otomatis cabang brand. | Map link / chat CTA |
| 11 | Footer | Identitas legal, privacy, disclaimer, kontak sosial, copyright. | Tautan penting |

> Satu halaman tidak boleh terasa seperti brosur yang dipindahkan mentah-mentah: pecah paragraf panjang menjadi cerita visual, subjudul yang jelas, foto nyata, dan konten ringkas yang dapat diperluas.

## 04. Content strategy: pemetaan materi dari brosur

| Materi asal | Penempatan | Strategi redaksi / status |
| --- | --- | --- |
| LPPRB: Lembaga Penelitian Peluruhan Radikal Bebas | Tentang Kami + footer | Gunakan nama organisasi sesuai badan hukum terverifikasi; hindari menciptakan profil atau akreditasi. |
| Nama layanan: Griya Balur Bukian Mertasari / Terapi Komplementer BALUR | Hero + Tentang Kami + Memahami BALUR | **Griya Balur Bukian Mertasari** wajib menjadi brand utama. BALUR sebagai nama pendekatan/aktivitas; LPPRB hanya sebagai afiliasi jika telah dikonfirmasi. |
| Narasi “keseimbangan alam, biofisika, sains kompleksitas” | Filosofi pendekatan | Dinyatakan sebagai kerangka/konsep yang digunakan organisasi; bukan otomatis bukti efektivitas medis. |
| Pembaluran herbal, kopi Balur, dan fasilitas | Rangkaian kegiatan + dokumentasi | Boleh diperkenalkan sebagai kegiatan/produk sesuai fakta; tidak memuat instruksi medis mandiri. |
| Asap tembakau / “rokok sehat”, enema kopi, telur mentah | Tidak masuk copy promosi MVP | Tahan sampai review risiko klinis, hukum, perizinan, dan klaim publik. |
| “Pre-klinis aman”, “teruji klinis”, “antiradikal”, antiinflamasi, E. coli | Bagian bukti setelah review | Setiap pernyataan perlu laporan metode, institusi, sampel, jenis studi, tanggal, hasil, batasan dan izin penggunaan. |
| Alamat, telepon, situs smartbio.org | Lokasi & footer | Data pada brosur hanya referensi lama; klien harus memastikan alamat/nomor resmi **Griya Balur Bukian Mertasari** yang ditampilkan. |
| Brosur bahasa Inggris | **Multilingual MVP (EN-US)** | Referensi awal saja; perlu penyuntingan native-style US English, pengecekan istilah, fakta, dan review klaim identik dengan ID. |

### 05. Copywriting awal (siap untuk ditinjau)

| Slot | Usulan copy |
| --- | --- |
| Headline hero | **Griya Balur Bukian Mertasari** — mengenal kegiatan BALUR melalui informasi, fasilitas, dan dokumentasi yang terbuka. |
| Subheadline hero | Jelajahi profil kami, pendekatan yang diperkenalkan, fasilitas, serta suasana kegiatan melalui foto dan informasi yang dapat ditelusuri. |
| CTA 1 | Kenali BALUR |
| CTA 2 | Jelajahi Fasilitas |
| CTA 3 | Hubungi Admin |
| Tentang kami | Griya Balur Bukian Mertasari menyajikan informasi tentang kegiatan BALUR, latar belakang pendekatan biofisika, tempat, dan dokumentasi aktivitas secara terbuka. Detail layanan dan afiliasi lembaga ditayangkan setelah diverifikasi klien. |
| Judul galeri | Lihat ruang, suasana, dan kegiatan kami. |
| Judul transparansi | Informasi penelitian dan dokumen pendukung. |
| Microcopy kontak | Punya pertanyaan mengenai lokasi, ketersediaan layanan, atau persiapan kunjungan? Silakan hubungi admin untuk informasi resmi. |
| Catatan kesehatan | Informasi di website bersifat edukatif, bukan diagnosis, janji penyembuhan, atau pengganti konsultasi tenaga kesehatan. |

## 06. Arah UI/UX & visual identity

### Konsep desain: Natural Science, Refined & Human

Arah visual menggabungkan ketenangan fasilitas kesehatan dengan estetika laboratorium modern: warna botanical, dokumentasi foto nyata, bidang kaca tipis, tipografi editorial, ruang kosong lebar, motion lembut. Targetnya premium dan kredibel, bukan efek neon atau sci-fi yang membuat klaim riset tampak berlebihan.

| Token | Nilai | Penggunaan |
| --- | --- | --- |
| Deep Forest | #193B32 | Headings, navbar gelap, kontras utama. |
| Sage / Olive | #84946D | Aksen botanical, shape/diagram ringan. |
| Warm Copper | #B46D38 | CTA sekunder, accent, icon highlight. |
| Porcelain | #F7F5EF | Background utama yang hangat. |
| Charcoal | #20302D | Body text. |
| Glass White | rgba(255,255,255,.65) | Kartu kaca di atas gambar/gradient berkontras jelas. |
| Surface Border | rgba(255,255,255,.55) | Border 1px, blur 14–18px, subtle shadow. |

### Komponen visual wajib

- Hero: foto lokasi yang meyakinkan, overlay gelap selektif agar headline tetap terbaca; 1 headline, 1 subheadline, 2 CTA.
- Glassmorphism: hanya di navbar, hero badge, kartu highlight, floating CTA dan lokasi. Hindari seluruh paragraf panjang berada di kaca transparan.
- Bento gallery: komposisi foto editorial, campuran portrait & landscape, label per kategori dan alt text.
- Section transitions: pattern garis biofisika/partikel abstrak yang tipis; tidak memakai visual “radikal bebas hancur” sebagai fakta ilmiah.
- Card program: ikon sederhana, headline ringkas, deskripsi 2–3 kalimat, dan label “pelajari lebih lanjut”.
- Microinteractions: hover 150–250 ms, scroll reveal sekali, gambar zoom subtle; patuhi prefers-reduced-motion.
- Mobile: menu sheet, CTA WhatsApp tidak menutup informasi penting, galeri satu kolom, tap area ≥44x44 px.

Tipografi: Plus Jakarta Sans (headline) dan Inter (isi) atau font setara. Body desktop/mobile minimum 16px, line-height 1.55–1.7. Hero desktop 56–72px, mobile 34–42px; seluruh teks harus menjaga kontras.

## 07. Layout blueprint & pengalaman perangkat

| Viewport | Hero + konten | Galeri + navigasi |
| --- | --- | --- |
| Desktop ≥1200 | Hero split 55/45 atau full-bleed; navbar sticky; max content width 1200px; section padding 96–128px. | Bento 12-col; 3–4 card visual; FAQ dua kolom jika cukup ruang. |
| Tablet 768–1199 | Hero stack/split sesuai foto; section padding 64–88px. | Gallery 2 columns; nav dipadatkan. |
| Mobile ≤767 | Hero single column; headline jelas; CTAs vertikal; section padding 56–72px. | Grid 1–2 col; lightbox swipe; sticky CTA hanya bila tidak mengganggu. |

### Sketsa susunan desktop

Sticky glass navbar (termasuk switcher ID / EN) → Hero visual dan CTA → About → Konsep BALUR (3 kartu) → Aktivitas → Alur kunjungan → Bento fasilitas → Galeri dokumentasi → Penelitian/bukti → FAQ → Peta dan WhatsApp → Footer.

## 08. Spesifikasi galeri & kebutuhan dokumentasi

| Kategori | Konten foto / video | Catatan produksi |
| --- | --- | --- |
| Eksterior | Bangunan, papan nama, pintu masuk, akses lokasi. | Hero dan thumbnail Maps/Location. |
| Interior | Reception, ruang tunggu, koridor, pencahayaan, kebersihan. | Tampilkan kondisi yang benar-benar ada. |
| Fasilitas | Ruang terapi, peralatan, tempat duduk/tidur, wastafel, toilet, parkir jika ada. | Jangan mengeklaim fasilitas yang belum terdokumentasi. |
| Aktivitas | Suasana kegiatan, pengarahan, persiapan, edukasi. | Izin tertulis/terdokumentasi orang di foto; hindari prosedur sensitif. |
| Tim & organisasi | Foto staf, penanggung jawab, kegiatan penelitian bila relevan. | Nama/jabatan/sertifikasi hanya jika valid. |
| Produk / material | Kopi Balur dan material pendukung, jika izin visual memadai. | Pisahkan deskripsi produk dari klaim pengobatan. |

Target minimum aset awal: hero 1 foto utama + 1 cadangan; fasilitas 8–12 foto; suasana kegiatan 8–12 foto; eksterior/lokasi 4 foto; tim/edukasi 3–6 foto. Jumlah adalah target produksi, bukan klaim ketersediaan saat ini.

- Optimasi media: sumber resolusi tinggi untuk arsip; output WebP/AVIF berukuran responsif; metadata EXIF lokasi pasien dihapus.
- Setiap asset mempunyai judul, kategori, alt text, urutan, kredit fotografer, status izin, dan tanggal ambil.
- Jika video hero digunakan, maksimal 6–12 detik loop tanpa suara; poster image disediakan; jangan autoplay berat pada mobile.

## 09. Functional requirements / acceptance criteria

| ID | Kebutuhan | Acceptance criteria (AC) |
| --- | --- | --- |
| FR-01 | Anchor navigation | Klik menu scroll halus ke section, URL fragment relevan, sticky nav tidak menutupi heading. |
| FR-02 | Responsive layout | Tidak ada horizontal scrolling pada viewport 360–1440px; menu dan CTA usable. |
| FR-03 | Kartu glassmorphism | Kartu tetap terbaca saat background beragam; dukung fallback tanpa backdrop-filter. |
| FR-04 | Galeri media | CMS upload foto; filter kategori; lightbox; keyboard Escape / close; alt text wajib. |
| FR-05 | Katalog fasilitas | Admin dapat membuat/edit/arsip fasilitas, unggah 1–N foto, dan atur urutan. |
| FR-06 | Konten kegiatan | Konten kegiatan memiliki title, summary, image dan content-review status sebelum publish. |
| FR-07 | Bukti / penelitian | Setiap item wajib author/institusi, tahun, type of evidence, source file/link, disclaimer/limitations. |
| FR-08 | FAQ | Admin membuat FAQ; heading/button keyboard accessible; hanya satu atau banyak item terbuka. |
| FR-09 | Lokasi & WhatsApp | Klik nomor menggunakan wa.me format E.164; CTA deep link; tiap lokasi memiliki Maps URL terverifikasi. |
| FR-10 | Admin roles | Admin/Editor/Reviewer; izin publish konten berisiko khusus pada Reviewer/Admin. |
| FR-11 | Preview + versioning | Perubahan dibuat draft, dapat di-preview dan rollback content revision saat tersedia. |
| FR-12 | Analytics privacy-aware | Event wa_click, maps_click, gallery_open, research_download; tanpa menyimpan data medis. |
| FR-13 | SEO | Metadata dan sitemap **per bahasa**, canonical per-URL, pasangan hreflang `id-ID`/`en-US`, OG locale, semantik H1–H3, rich snippet organisasi bila valid. |
| FR-14 | Legal & consent | Privacy + disclaimer, admin akses aman, tidak ada foto identitas pasien tanpa izin. |
| FR-15 | Error states | Loading skeleton, error upload, empty gallery, fallback image dan state CTA gagal ditangani. |
| FR-16 | Language switcher | Tombol ID / EN di desktop dan mobile; mengganti locale tanpa kehilangan konteks section; tidak mengubah brand dan data kontak. |
| FR-17 | Localized routes | URL publik ` /id/ ` dan ` /en/ ` dengan anchor sama; root `/` diarahkan ke default `/id/`, kecuali preferensi pengguna tersimpan. |
| FR-18 | Translation completeness | Publish EN diblokir jika headline, body, CTA, FAQ, SEO, alt text wajib, disclaimer belum diterjemahkan/disetujui; tidak menampilkan teks campur otomatis. |
| FR-19 | Locale persistence | Pilihan bahasa manual lebih tinggi prioritasnya daripada preferensi browser; pilihan bertahan ketika user menavigasi/refresh. |
| FR-20 | Language parity QA | Seluruh section, lokasi, galeri, FAQ, kontak, dokumen bukti, kebijakan privasi, navigasi, dan error state diuji pada ID/EN. |

## 10. CMS & rancangan data

| Modul | Field utama | Aksi role |
| --- | --- | --- |
| SiteSettings | brand_name (tetap di ID/EN), tagline_{id,en}, kontak, WA, logo, seo_{id,en}, socials | Admin |
| PageSections | section_key, headline_{id,en}, body_{id,en}, CTA_{id,en}, media_id, sort_order, active | Editor draft; Reviewer publish |
| Activities | title_{id,en}, slug_{id,en}, summary_{id,en}, category, media, status, review_notes | Editor draft; Reviewer publish |
| Facilities | title_{id,en}, description_{id,en}, location_id, image_ids, sort_order, active | Editor edit/publish sesuai kebijakan |
| GalleryMedia | file, alt_{id,en}, caption_{id,en}, category, location, permission_status, captured_at | Editor upload; Admin release |
| ResearchEvidence | judul asli, ringkasan_{id,en}, limitations_{id,en}, authors, year, study_type, URL/file, approval | Reviewer/penanggung jawab approve |
| FAQ | question_{id,en}, answer_{id,en}, sort_order, active | Editor |
| Locations | name, address (alamat resmi tidak diterjemahkan), guidance_{id,en}, phone, wa_number, maps_url, hours, active | Admin |
| AuditLog | editor_id, object, action, locale, timestamp, revision | Admin read |

Workflow editorial **untuk masing-masing bahasa**: DRAFT → CONTENT REVIEW → MEDICAL/REGULATORY REVIEW (jika ada klaim kesehatan) → APPROVED → PUBLISHED. Materi lama di brosur tidak otomatis diberi status approved.

## 11. Arsitektur teknologi (rekomendasi)

Rekomendasi default: Laravel + Blade + Tailwind CSS + Alpine.js untuk single-page yang cepat, CMS Filament, database MySQL/MariaDB, gambar di object storage atau disk server yang dibackup. Versi Laravel dan Filament difinalkan setelah meninjau versi PHP server dan kompatibilitas dependensi.

| Layer | Rancangan | Alasan |
| --- | --- | --- |
| Frontend | Blade + Tailwind + Alpine, Vite | Kinerja baik, sedikit JavaScript, tidak perlu SPA kompleks. |
| Animasi | CSS + IntersectionObserver; library motion opsional | Motion hanya jika ada nilai UX; budget JS kecil. |
| Backend / CMS | Laravel + Filament (verifikasi versi kompatibel) | Dashboard editor, media, role, konten terstruktur. |
| Database | MySQL / MariaDB | Umum pada shared/VPS; backup dan migration mudah. |
| Media | Resize server-side, WebP/AVIF, CDN opsional | Mengurangi bandwidth dan LCP. |
| Hosting | HTTPS + VPS/managed PHP hosting dengan queue bila diperlukan | Deploy dan maintenance menyesuaikan kapasitas klien. |
| Monitoring | Uptime + error logging + backup alerts | Operasional lebih terkontrol. |

Alternatif bila klien tidak memerlukan CMS: Astro statis + konten Markdown dapat lebih ringan. Tetapi karena dokumentasi foto perlu diperbarui berkala, CMS Laravel lebih sesuai kebutuhan operasional yang diasumsikan.

## 12. Non-functional requirements

| Aspek | Target penerimaan |
| --- | --- |
| Performance | Core Web Vitals p75 mobile: LCP ≤2.5s, INP ≤200ms, CLS ≤0.1; pengukuran setelah deploy memakai perangkat nyata. |
| Media budget | Hero image lazy? tidak: preload secukupnya; gambar bawah fold lazy-load; seluruh media memakai srcset/sizes. |
| Accessibility | Target WCAG 2.2 AA: kontras teks ≥4.5:1 pada teks normal, keyboard operable, reduced motion, alt text. |
| Responsiveness | 360, 390, 768, 1024, 1280, 1440 dan landscape diuji; no overlap/glass unreadable. |
| Security | TLS, CSRF, validation, sanitasi input, secure auth, rate-limit login, MFA admin opsional-kuat, audit log. |
| Privacy | Minimasi formulir; tidak menerima/upload data klinis pasien dalam MVP. Persetujuan foto dan kebijakan privasi. |
| Availability | Target operasional 99.5% per bulan sebagai sasaran, bukan jaminan tanpa SLA hosting. |
| SEO & locale | Konten server-rendered, canonical per locale, `hreflang` timbal balik, XML sitemap `/id/` dan `/en/`, title/description/OG locale unik. |
| Backup | DB harian dan media sesuai SLA, retensi 14–30 hari sesuai kebijakan, uji restore sebelum launch. |
| Browser | Chrome/Edge/Firefox terbaru, Safari desktop dan iPhone, Android Chrome. |

## 13. Klaim kesehatan, keselamatan, dan review legal

> Gate publikasi wajib: jangan mengubah klaim brosur menjadi bukti ilmiah. Pastikan izin usaha dan status hukum fasilitas/produk, tinjau semua konten layanan bersama penanggung jawab klinis/regulasi, lalu terbitkan hanya redaksi yang disetujui dan dapat dipertanggungjawabkan.

| Materi yang perlu keputusan | Risiko | Keputusan PRD |
| --- | --- | --- |
| “Aman dikonsumsi”, “teruji klinis”, “meningkatkan kualitas hidup”, “menghambat E. coli” | Overclaim tanpa detail uji dan konteks populasi/produk | HOLD. Minta laporan lengkap, bukti perizinan & review klaim. |
| “Mengeluarkan logam berat dan radikal bebas” / “grounding menarik radikal” | Mekanisme/hasil klinis tidak boleh diasumsikan dari istilah teknis | Tulis sebagai “konsep yang diperkenalkan organisasi” hanya bila tidak menyesatkan; jangan janjikan efek. |
| Terapi asap tembakau atau “rokok sehat” | Risiko paparan asap tembakau, tidak boleh disebut aman | EXCLUDE copy promosi; review keselamatan terpisah. |
| Enema kopi sebagai pengobatan/detoksifikasi | Bukti manfaat tidak memadai dan terdapat laporan efek merugikan | EXCLUDE panduan, klaim dan CTA prosedur; perlu nasihat medis dan hukum. |
| Campuran kopi dengan telur mentah | Risiko keamanan pangan dan kontaminasi | Jangan tampilkan resep/anjuran konsumsi di website. |
| Testimoni kesembuhan dan before-after | Dapat menyesatkan & memerlukan aturan khusus iklan layanan kesehatan | Tidak menjadi fitur MVP; dokumentasi harus fokus fasilitas/kegiatan. |

Catatan regulasi Indonesia: klasifikasi layanan maupun produk menentukan rezim iklan yang relevan. Permenkes 1787/Menkes/Per/XII/2010 memuat kewajiban publikasi pelayanan kesehatan yang akurat dan berbasis bukti. Jika Kopi Balur dipasarkan sebagai obat tradisional/suplemen, ketentuan BPOM tentang materi iklan, izin edar, dan persetujuan perlu diperiksa; apabila sebagai pangan olahan, peraturan klaim pangan berlaku sesuai kategorinya. Ini bukan penetapan klasifikasi produk.

## 14. KPI, analytics, dan pengujian sebelum rilis

| Metrik | Definisi | Target awal (hipotesis, revisi setelah baseline) |
| --- | --- | --- |
| WhatsApp CTR | wa_click / unique website sessions, dipisah menurut `locale` | ≥3–5% bulan awal |
| Location intent | maps_click / sessions | Dipantau per lokasi |
| Gallery engagement | gallery_open / sessions | ≥20% |
| FAQ discoverability | faq_open / sessions | Monitor pertanyaan teratas untuk perbaikan copy |
| CWV | Persentil ke-75 data real-user | Lulus target LCP/INP/CLS |
| Content quality | % blok wajib yang approved | 100% sebelum publikasi |
| Media rights | % dokumentasi dengan izin/asal-usul tercatat | 100% sebelum publikasi |

- UAT desktop & mobile: navigasi, CTA, WhatsApp, Maps, carousel/lightbox, semua link, gambar dan PDF, FAQ.
- Content QA: proofread **ID dan US English** seluruh copy/CTA/SEO/alt/disclaimer; periksa status bukti, nomor telepon/alamat, hak gambar, informasi legal.
- Technical QA: Lighthouse, screenreader basic, keyboard path, reduced motion, broken links, **locale routes + hreflang + switcher**, SEO, cache, backup restore.
- Security QA: role authorization, upload validation, CSRF, brute force limits, HTTPS, env secrets tidak terekspos.

## 15. Roadmap, deliverable dan keputusan yang masih terbuka

| Fase | Output | Estimasi indikatif |
| --- | --- | --- |
| 0 — Discovery / compliance | Kunci brand Griya Balur Bukian Mertasari, lokasi aktif, izin, bukti uji, foto, scope konten. | 3–5 hari kerja |
| 1 — UX & art direction | Sitemap, wireframe desktop/mobile, design tokens, UI kit, prototipe hero dan galeri. | 4–7 hari kerja |
| 2 — Implementation | Frontend responsif, CMS, gallery, FAQ, lokasi, **i18n ID/EN**, locale switcher, metadata bilingual, analytics. | 10–18 hari kerja |
| 3 — Content & QA | Foto, **copywriting dan proofreading dua bahasa**, konten approved, uji browser/performance/security/i18n. | 5–9 hari kerja |
| 4 — Launch | Domain/SSL, deploy, backup, dokumentasi admin dan handover. | 1–3 hari kerja |

Estimasi total kasar **5–8 minggu** bergantung pada kesiapan foto, peninjauan materi kesehatan, dan iterasi klien. Bukan komitmen biaya atau jadwal sebelum discovery.

### Keputusan klien yang diperlukan

- **Brand publik sudah diputuskan: Griya Balur Bukian Mertasari.** Konfirmasi logo resmi, ejaan visual, tagline, dan apakah LPPRB afiliasi legal.
- Organisasi secara legal tergolong lembaga penelitian, fasilitas layanan kesehatan, layanan wellness, atau bentuk usaha lain?
- Alamat dan nomor Malang/Bali mana yang masih digunakan, jam layanan, Google Maps, dan nomor WA resmi?
- Apakah Kopi Balur akan ditampilkan sebagai produk tersendiri? Tersedia nomor izin edar dan persetujuan klaim?
- Dokumen ilmiah apa yang dapat diterbitkan (judul, penulis, institusi, DOI, PDF dan ruang lingkup)?
- Apakah klien sudah punya foto/video terbaru, izin dokumentasi orang, logo vector, dan brand guide?
- **Bilingual ID + US English sudah diputuskan untuk MVP.** Tentukan reviewer/penerjemah yang menyetujui kedua versi, khususnya istilah medis dan klaim.
- Apakah tim klien memerlukan pelatihan CMS dan siapa yang menjadi penanggung jawab approval konten?

## 16. Spesifikasi internasionalisasi (i18n) & lokalisasi (l10n) — WAJIB MVP

### Identitas dan perilaku bahasa

- **Brand tidak diterjemahkan:** `Griya Balur Bukian Mertasari` tampil identik dalam ID maupun EN; nama pendekatan `BALUR` juga tidak perlu diterjemahkan sebagai istilah brand. Afiliasi LPPRB hanya tampil setelah verifikasi.
- **Dua locale terpisah:** Bahasa Indonesia `id-ID`, English (United States) `en-US`. Label switcher singkat `ID | EN` serta nama bahasa lengkap pada atribut aksesibilitas.
- **Default dan rute:** root `/` mengarahkan ke `/id/` secara default; alternatif `/en/` bisa dibuka langsung. Pilihan manual disimpan, diutamakan dibanding `Accept-Language`. Pengalihan otomatis browser hanyalah opsi UX dan tidak boleh memaksa pengunjung terus-menerus.
- **Satu halaman per locale:** struktur section/anchor sama (contoh `/id/#fasilitas` → `/en/#facilities` bila identifier dipetakan, atau gunakan ID anchor stabil seperti `#facilities` pada kedua locale). Switcher menjaga konteks section dan scroll.
- **Bukan terjemahan instan:** seluruh konten publik ditulis/edit ulang untuk pembaca masing-masing bahasa; EN menggunakan tata bahasa dan gaya US English profesional. Tidak ada Google Translate widget otomatis sebagai fitur utama.
- **Data yang tidak diterjemahkan:** brand, alamat resmi, nomor telepon, peta/tautan, nama orang, nama institusi, DOI dan judul asli publikasi; penjelasan menuju lokasi serta ringkasan penelitian boleh diterjemahkan.
- **Review konten:** bahasa EN memiliki proses persetujuan sendiri. Jika salah satu konten kritis belum lengkap, blok publikasi locale tersebut atau sembunyikan section secara eksplisit setelah review (jangan campur teks ID/EN diam-diam).

### Rancangan teknis SEO + CMS bilingual

| Area | Implementasi wajib | Penerimaan |
| --- | --- | --- |
| URL dan cache | `/id/`, `/en/`; server-rendered; cache bervariasi menurut locale | 200 OK, metadata dan isi sesuai locale; tidak ada duplikasi cache silang bahasa |
| SEO lokal | `html lang=id-ID/en-US`, title, meta description, OG locale, canonical self, `hreflang=id-ID/en-US` timbal balik, sitemap kedua URL | Penguji SEO memverifikasi pasangan bahasa dan tidak menemukan canonical salah |
| Konten berpasangan | Field `title`, `summary`, `body`, `CTA`, `FAQ`, `alt`, `caption`, `SEO` masing-masing memiliki ID dan EN | Editorial dashboard menampilkan completeness per locale |
| Navigasi + kontak | Navbar, filter galeri, pesan kosong/error, CTA WA, contact intro, FAQ, legal disclaimer diterjemahkan | Tidak ada string Indonesia terselip di interface EN |
| Gallery dan media | Foto asli dipakai bersama; caption dan alt text berbeda per locale jika diperlukan | SEO/a11y memperoleh deskripsi visual yang bermakna pada kedua bahasa |
| Bukti ilmiah | Judul PDF asli tetap; kartu ringkasan, klasifikasi studi dan keterbatasan diterjemahkan melalui review | Kedua locale tidak membuat klaim baru/lebih kuat daripada bukti yang sama |
| Analytics | `locale` untuk event `language_switch`, `wa_click`, `maps_click`, `gallery_open`, `research_download` | Dashboard bisa melihat perbandingan trafik/konversi per locale |

### Contoh copy pembuka bilingual (draft editorial, belum persetujuan klien)

| Elemen | Bahasa Indonesia (`id-ID`) | English (United States) (`en-US`) |
| --- | --- | --- |
| Nama situs | Griya Balur Bukian Mertasari | Griya Balur Bukian Mertasari |
| Hero tagline | Mengenal BALUR melalui ruang, aktivitas, dan informasi yang terbuka. | Explore BALUR through our space, activities, and openly shared information. |
| Hero deskripsi | Temukan profil kami, pendekatan yang diperkenalkan, fasilitas, serta dokumentasi kegiatan. | Learn about our organization, our approach, our facilities, and documented activities. |
| CTA primer | Jelajahi Fasilitas | Explore Our Facilities |
| CTA sekunder | Hubungi Kami | Contact Us |
| Navigasi | Tentang Kami · BALUR · Kegiatan · Fasilitas · Galeri · Penelitian · Kontak | About · BALUR · Activities · Facilities · Gallery · Research · Contact |
| Galeri | Lihat suasana dan kegiatan kami. | A closer look at our spaces and activities. |
| Kontak | Punya pertanyaan tentang lokasi atau kunjungan? Hubungi admin kami. | Have questions about our location or visiting us? Contact our team. |
| Disclaimer | Informasi pada situs ini bersifat edukatif, bukan diagnosis atau pengganti konsultasi medis. | Information on this site is educational and is not a diagnosis or a substitute for professional medical advice. |

### Skenario UAT bilingual sebelum go-live

1. Membuka `/id/` dan `/en/` menampilkan nama brand yang sama dan seluruh interface dalam locale yang benar.
2. Menekan `EN` dari halaman ID tidak mengubah section yang sedang dibaca; pada reload, pilihan bahasa bertahan.
3. Switcher dapat diakses keyboard dan pembaca layar; tersedia di navbar desktop serta menu mobile.
4. Semua tombol, galeri, FAQ, error state, disclaimer, privacy notice, dan pesan WhatsApp yang disusun otomatis (jika ada) memakai bahasa aktif.
5. Metadata title/description, canonical, Open Graph locale, `hreflang`, sitemap, dan HTML `lang` lulus inspeksi pada kedua URL.
6. Konten kesehatan dan ringkasan studi di kedua locale memiliki approval terpisah dan tidak memperkuat klaim tanpa bukti.
7. Missing translation memunculkan warning pada CMS, tidak otomatis menampilkan teks campur atau mengirim halaman parsial untuk diindeks.

## Lampiran A — Komposisi wireframe konten

Hero (foto unggulan + overlay) → Intro institusi (foto exterior + copy) → Kata kunci 3 pilar (card kaca selektif) → Program/aktivitas (cards) → Visitor journey (timeline) → Fasilitas (bento) → Dokumentasi kegiatan (filter) → Penelitian & bukti (cards transparansi) → FAQ → Lokasi/WA (contact panel) → Footer.

## Lampiran B — Referensi verifikasi klaim & regulasi

Sumber eksternal berikut digunakan hanya sebagai masukan batasan komunikasi kesehatan dan kepatuhan konten; bukan bukti bahwa layanan BALUR telah diuji atau memperoleh izin.

WHO — Tobacco and nicotine (2026): https://www.who.int/en/news-room/fact-sheets/detail/tobacco

NCCIH/NIH — Detoxes and Cleanses: https://www.nccih.nih.gov/health/detoxes-cleanses | Son et al. (2020), coffee enema systematic review: https://pubmed.ncbi.nlm.nih.gov/32899046/

JDIH Kemenkes — Permenkes 1787/2010: https://jdih.kemkes.go.id/documents/peraturan-menteri-kesehatan-nomor-1787menkesperxii2010
