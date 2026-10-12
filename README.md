# Griya Balur Bukian Mertasari — website

Situs statis bilingual (`/id/` id-ID, `/en/` en-US), server-rendered, tanpa dependensi.

```
node build.mjs          # hasil di ./dist
npx serve dist          # preview lokal (atau server statis apa pun)
```

- Konten & konfigurasi: `build.mjs` (objek `CFG` dan `T`)
- Gaya: `assets/style.css`, interaksi: `assets/app.js`

## Wajib diisi sebelum rilis (lihat PRD §15)
`CFG` di `build.mjs`: `siteUrl`, `wa` (E.164), `phoneDisplay`, `mapsUrl`, `address`, `hours`.
Foto asli menggantikan placeholder (hero, about, fasilitas, galeri). Seksi Penelitian sengaja kosong
sampai dokumen disahkan; klaim brosur (asap tembakau, enema kopi, telur mentah, "teruji klinis") tidak dimuat sesuai PRD §13.

## Belum termasuk
CMS (Laravel + Filament, role, workflow review, audit log) — PRD merekomendasikannya sebagai fase lanjutan.
