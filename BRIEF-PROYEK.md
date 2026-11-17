# BRIEF PROYEK BARU: CBT SIAKANUDA (CI4) — BUILD DARI NOL

*Diterima lengkap dari Beng di main chat, 17 Nov 2026 17.49 WIB (bagian 1–3 diterima 17.48 WIB, bagian 4–5 menyusul 17.49 WIB).*

Halo Mus, berdasarkan hasil rapat final dengan Beng, kita putuskan membangun **CBT SIAKANUDA** (label siswa/PWA: **Ujian SIAKANUDA**) sebagai **aplikasi & repositori baru yang berdiri sendiri** berbasis CodeIgniter 4 — dibangun bersih dari nol (hanya meniru konsep snapshot soal & navigasi ZYACBT, BUKAN porting kode CI3-nya).

---
### 1. REPOSITORI, DUAL-REMOTE GIT, & ISOLASI
- **Repositori baru:** `cbt-siakanuda` (folder lokal baru, terpisah dari `siakanuda`).
- **Dual-Remote Git (wajib di setiap selesai perubahan):**
  - `origin`: GitHub Private (`git@github.com:bangunsetyawan/cbt-siakanuda.git`)
  - `gitea`: Gitea `smknuda2` (`https://git.smknuda.qzz.io/smknuda/cbt-siakanuda.git`)
  *(Jika repo remote GitHub/Gitea belum ada, siapkan repo lokal + commit awal dulu dan beri tahu Beng untuk dibuatkan repo kosongnya, atau buat di Gitea lebih dulu lalu sync ke GitHub).*
- **Isolasi penuh:** Jangan sentuh repo `siakanuda`, branch `master` SIAKANUDA, maupun server produksi `siakanuda2`. Tidak ada fitur WA Bot dan tidak ada sinkronisasi nilai ke rapor.

---
### 2. TARGET SERVER & ARSITEKTUR "2 PINTU, 1 DATABASE"
- **Server target:** `smknuda2` (Docker Compose: Nginx + PHP 8.4-FPM dengan OPcache + MariaDB InnoDB).
- **Kapasitas desain:** 235 siswa mengerjakan bersamaan secara **Pure Offline LAN** (Wi-Fi sekolah tanpa internet agar bandwidth 100 Mbps tidak dimakan trafik latar belakang HP siswa).
- **2 Pintu Akses ke Database MariaDB yang sama:**
  1. **Pintu Offline (Utama di sekolah):** IP LAN `smknuda2` pada **Port 80** (`http://IP_SERVER`) — langsung IP tanpa nama domain agar kebal terhadap *Private DNS* HP Android terbaru.
  2. **Pintu Online (Sekunder — admin/guru dari rumah & siswa sakit/susulan):** Subdomain Cloudflare Tunnel (`cbt.siakanuda.qzz.io`).
- **SYARAT TEKNIS MUTLAK DARI POLA 2 PINTU + OFFLINE:**
  1. **`baseURL` dinamis:** Mengikuti host & skema (`http`/`https`) yang sedang memanggilnya.
  2. **ZERO CDN (100% aset lokal):** Semua CSS, JS, ikon, dan font WAJIB disimpan lokal di project. Dilarang memanggil CDN eksternal (`Google Fonts`, `jsDelivr`, dll.) agar browser HP tidak hang saat Wi-Fi tanpa internet.
  3. **Path media relatif:** Gambar dan audio di dalam soal wajib disimpan sebagai path relatif (tanpa domain/IP), dan gambar otomatis dikompres/resize wajar saat upload agar ringan diakses 235 HP serentak.

---
### 3. KONSEP OPERASIONAL "PROKTOR-CENTRIC" (SIMPEL & CEPAT)
Di sekolah kita, guru menyetorkan soal ke admin/proktor. Maka **buang manajemen hak akses rumit per-guru/per-topik ala ZYACBT**, dan buat alur sepraktis mungkin:
1. **Periode Ujian:** Terorganisir per **Tahun Ajaran + Jenis Ujian** (`PTS`, `PAS`, `Ujian Lainnya`). Semua arsip periode lama tetap tersimpan rapi & bisa dibuka/cetak kapan saja dari rumah maupun sekolah.
2. **Bank Soal & Input Massal Cepat (Fokus Utama UX):**
   - Skema DB sejak awal disiapkan untuk kolom **bobot soal** (default 1) dan fleksibel untuk **PG Kompleks & Menjodohkan**.
   - Untuk rilis awal, jalankan **PG, Esai, dan Isian Singkat** sampai stabil (PG Kompleks & Menjodohkan menyusul).
   - **Quick Paste / Import Massal:** Sediakan fitur copy-paste banyak soal PG maupun Esai dari teks/Word sekaligus (dengan pratinjau sebelum simpan, tanpa harus paham HTML) + import Excel tervalidasi. (Baca file `.docx` langsung via PhpWord menyusul).
3. **Jadwal Tes Simpel:** Pilih Periode → Pilih Mapel/Bank Soal → Centang Kelas → Set Jam Mulai–Selesai & Durasi. **Token bersifat opsional (default MATI)**. Batas IP/ruang tidak perlu dipakai dulu.
4. **1 Layar Monitor Ujian Proktor:** Pantau status peserta real-time dalam satu halaman dengan tombol aksi cepat: **Reset Login**, **Lanjutkan/Buka Kembali Tes**, **Paksa Selesai**, dan **Tambah Waktu Massal** (antisipasi jika listrik padam lalu menyala lagi lewat Auto Power-On).
5. **Koreksi & Laporan:** Antrean koreksi Esai (termasuk esai kosong + riwayat koreksi), Rekap Nilai + Export Excel, dan Analisis Butir Soal yang akurat (terkunci ke `soal_id`, bukan urutan acak).

---
### 4. ALUR SISWA, KEAMANAN, & JEMBATAN KE SIAKANUDA
- **UI/UX Siswa:** Adaptasi gaya tampilan ringan & mobile-first ala SIAKANUDA (1 soal per layar, tombol nomor 1–N warna status, timer server-side dengan detik, simpan jawaban langsung saat klik tanpa polling berat tiap detik, aman terhadap mati lampu).
- **Deteksi pindah tab:** Mode **peringatan + catat log** saja (jangan auto-submit).
- **Login Offline + SSO SIAKANUDA:**
  - **Offline (wajib mandiri):** Siswa langsung buka `http://IP_SMKNUDA2` dan login pakai **NIS + Password/PIN** (hasil sinkron dari SIAKANUDA sebelum ujian).
  - **Online / Terhubung SIAKANUDA:** Siapkan endpoint terima token **SSO (HMAC-SHA256)** dan skrip/endpoint sinkronisasi satu arah untuk data **Siswa, Kelas, Guru, dan Tahun Pelajaran** dari SIAKANUDA (integrasi ke SIAKANUDA-nya dipasang belakangan setelah CBT matang).
- **Keamanan standar CI4:** Password ter-hash (`bcrypt`), CSRF aktif, Query Builder anti-SQLi, validasi kepemilikan sesi di tiap endpoint jawaban, serta sanitasi ketat upload gambar/audio & HTML soal.

---
### 5. KERJAKAN BERTAHAP (LAPOR SETIAP SELESAI FASE)
- **Fase 1 — Fondasi:** Setup repo `cbt-siakanuda`, kerangka CI4 terbaru, Docker Compose (`nginx + php 8.4 + mariadb`) untuk `smknuda2`, migrasi DB lengkap, dan layout dasar Zero-CDN + `baseURL` dinamis.
- **Fase 2 — Master & Bank Soal:** Periode Ujian, Peserta/Kelas (+ alat sinkron data SIAKANUDA), serta Bank Soal + Quick Paste Massal + Import Excel.
- **Fase 3 — Mesin Ujian & Monitor:** Buat Jadwal Tes (token opsional), Snapshot soal siswa, Halaman Kerjakan HP-first (auto-save + peringatan pindah tab), dan 1 Layar Monitor Proktor.
- **Fase 4 — Koreksi, Laporan, & SSO:** Koreksi Esai, Rekap Excel, Analisis Butir Soal, serta pemasangan tombol SSO di SIAKANUDA (setelah minta persetujuan Beng).

*Instruksi Beng: mulai dari **Fase 1** dulu — siapkan rancangan final struktur folder & tabel DB, lalu inisialisasi project CI4 + Docker-nya. Konfirmasi ke Beng jika butuh dibuatkan repo kosong di GitHub/Gitea terlebih dahulu.*
