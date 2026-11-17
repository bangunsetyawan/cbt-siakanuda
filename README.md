# CBT SIAKANUDA

Aplikasi ujian berbasis komputer (Computer Based-Test) SMK NU Darussalam — dibangun dari nol dengan CodeIgniter 4. Label siswa/PWA: **Ujian SIAKANUDA**.

> **REPO INI PUBLIK.** Jangan pernah menulis password, kunci/API key, atau alamat IP internal di repo ini. Semua contoh memakai placeholder — isi nilai asli hanya di file `.env` (tidak masuk git).

## Arsitektur singkat

- Docker Compose di smknuda2: Nginx (port 80) + PHP 8.4-FPM + MariaDB 11.
- Dua pintu ke database yang sama: LAN offline (`http://IP_SERVER`, port 80) dan online via Cloudflare Tunnel (`cbt.siakanuda.qzz.io`).
- Zero-CDN: semua CSS/JS/font lokal di `public/assets/`. `baseURL` mengikuti host & skema request yang masuk.
- Isolasi penuh: tidak menyentuh repo `siakanuda`, server produksi `siakanuda2`, dan tidak ada fitur WA Bot maupun sinkron nilai ke rapor.

## Menjalankan (di smknuda2)

1. Salin `.env.example` menjadi `.env`, lalu isi password database yang kuat:
   - `DB_PASSWORD` dan `DB_ROOT_PASSWORD` (database).
   - `database.default.password` di `.env` otomatis mengikuti `DB_PASSWORD` — isi sekali di `DB_PASSWORD` saja.
2. Jalankan container (dari root proyek):
   `docker compose --env-file .env -f docker/docker-compose.yml up -d --build`
3. Jalankan migrasi database:
   `docker compose --env-file .env -f docker/docker-compose.yml exec php php spark migrate`
4. Buat akun awal & akun test (admin, proktor, siswa test):
   `docker compose --env-file .env -f docker/docker-compose.yml exec php php spark db:seed PenggunaSeeder`
5. Buka `http://IP_SERVER` dari HP/laptop di Wi-Fi sekolah.

Catatan: folder `writable/` harus bisa ditulis PHP — jalankan `chmod -R 775 writable` (bila masih error, `chmod -R 777 writable`).

## Akun awal (test)

Akun-akun ini HANYA untuk pengembangan & uji coba awal. **Wajib dinonaktifkan/dihapus sebelum aplikasi dibuka online.**

| Akun | Login | Password/PIN |
|---|---|---|
| Admin | username `admin` | `admin` |
| Proktor | username `proktor` | `proktor` |
| Siswa test | NIS `0001` | PIN `1234` |

> Peringatan tegas: akun di atas memakai password default yang mudah ditebak. Nonaktifkan atau hapus ketiganya sebelum go-live — jangan biarkan akun test aktif di server yang bisa diakses dari internet.

## Perintah yang berguna

- `php spark migrate --help` — cek perintah migrasi.
- `php spark migrate` — jalankan semua migrasi (butuh database jalan).
- `php spark db:seed PenggunaSeeder` — seeder akun awal & akun test (admin, proktor, siswa test).
- `php spark routes` — daftar rute aplikasi.

## Tahapan

- **Fase 1 (ini):** fondasi — repo, Docker, migrasi 17 tabel, layout zero-CDN, halaman awal.
- **Fase 2:** master & bank soal (periode, peserta/kelas, bank soal + quick paste + import Excel).
- **Fase 3:** mesin ujian & monitor (jadwal tes, snapshot soal, halaman kerjakan, monitor proktor).
- **Fase 4:** koreksi, laporan & SSO (koreksi esai, rekap Excel, analisis butir soal, SSO SIAKANUDA).
