# RANCANGAN FASE 1 — CBT SIAKANUDA (CI4)

**Proyek:** `cbt-siakanuda` — aplikasi ujian baru, berdiri sendiri (CI4 4.7.6, PHP 8.4, MariaDB).
**Server target:** smknuda2 (Docker Compose: Nginx + PHP 8.4-FPM + OPcache + MariaDB InnoDB).
**Dokumen ini:** rancangan final struktur folder & tabel DB (Fase 1). Disusun 17 Nov 2026.

---

## 1. STRUKTUR FOLDER

```
cbt-siakanuda/
├── app/
│   ├── Config/
│   │   ├── App.php            # baseURL dinamis (ikut host & skema pemanggil)
│   │   ├── Database.php       # koneksi MariaDB (env)
│   │   ├── Filters.php        # filter auth (admin/siswa), CSRF
│   │   ├── Routes.php         # rute: / (siswa), /admin, /api
│   │   └── ...                # config bawaan CI4 lainnya
│   ├── Controllers/
│   │   ├── Auth.php           # login siswa (NIS + PIN) & login admin/proktor
│   │   ├── Siswa.php          # dashboard siswa, daftar tes
│   │   ├── Ujian.php          # kerjakan tes (1 soal/layar, simpan jawaban, timer)
│   │   ├── Admin/
│   │   │   ├── Dashboard.php
│   │   │   ├── Periode.php    # periode ujian (tahun ajaran + jenis)
│   │   │   ├── BankSoal.php   # bank soal + quick paste + import Excel
│   │   │   ├── JadwalTes.php  # jadwal tes (token opsional)
│   │   │   ├── Monitor.php    # 1 layar monitor proktor (Fase 3)
│   │   │   ├── Koreksi.php    # antrean koreksi esai (Fase 4)
│   │   │   ├── Laporan.php    # rekap + export Excel + analisis butir (Fase 4)
│   │   │   └── Peserta.php    # siswa/kelas/guru + sinkron SIAKANUDA
│   │   └── Api/
│   │       ├── Sso.php        # terima token SSO HMAC-SHA256 (Fase 4)
│   │       └── Sinkron.php    # sinkron satu arah data SIAKANUDA (Fase 2/4)
│   ├── Models/                # satu model per tabel (lihat §2)
│   ├── Database/
│   │   ├── Migrations/        # migrasi semua tabel (§2), urut tanggal
│   │   └── Seeds/
│   │       └── PenggunaSeeder.php  # akun admin awal (password wajib diganti)
│   ├── Filters/
│   │   ├── AuthAdmin.php      # wajib login admin/proktor
│   │   └── AuthSiswa.php      # wajib login siswa
│   ├── Libraries/
│   │   ├── MediaSoal.php      # upload + kompres/resize gambar soal
│   │   └── QuickPaste.php     # parser paste massal soal (Fase 2)
│   ├── Helpers/
│   │   └── cbt_helper.php     # format nilai, status, dsb.
│   └── Views/
│       ├── layouts/
│       │   ├── siswa.php      # layout HP-first siswa (zero-CDN)
│       │   └── admin.php      # layout proktor/admin
│       ├── auth/              # login siswa & admin
│       ├── siswa/             # dashboard + kerjakan tes
│       └── admin/             # periode, bank soal, jadwal, monitor, laporan
├── public/
│   ├── index.php              # front controller CI4
│   ├── assets/
│   │   ├── css/               # semua CSS lokal (tanpa CDN)
│   │   ├── js/                # semua JS lokal (tanpa CDN)
│   │   ├── fonts/             # font lokal (tanpa Google Fonts)
│   │   └── img/               # logo & ikon lokal
│   └── uploads/
│       └── soal/              # gambar/audio soal (path relatif)
├── docker/
│   ├── nginx/
│   │   └── default.conf       # Nginx untuk PHP-FPM (port 80)
│   ├── php/
│   │   └── Dockerfile         # PHP 8.4-FPM + OPcache + ext (intl, mbstring, mysqli)
│   └── docker-compose.yml     # nginx + php + mariadb (lihat §3)
├── writable/                  # cache, log, session (CI4)
├── .env.example               # contoh env (tanpa kredensial asli)
├── .gitignore
├── composer.json
└── README.md
```

**Prinsip yang dipegang:**
- **Zero-CDN:** semua CSS/JS/font/ikon di `public/assets/` — tidak ada panggilan ke CDN mana pun.
- **baseURL dinamis:** `App.php` membaca host & skema dari request yang masuk (HTTP di LAN, HTTPS lewat Cloudflare Tunnel) — bukan nilai tetap di `.env`.
- **Media relatif:** gambar/audio soal disimpan sebagai path relatif (`uploads/soal/...`), tanpa domain/IP.
- **Isolasi:** tidak ada kode yang menyentuh repo `siakanuda` atau server `siakanuda2`.

---

## 2. TABEL DATABASE (17 tabel, MariaDB InnoDB, utf8mb4)

### 2.1 Master — Tahun ajaran, periode, kelas, mapel
| Tabel | Kolom penting | Keterangan |
|---|---|---|
| `tahun_ajaran` | id, nama (cth. `2026/2027`), aktif (tinyint), created_at, updated_at | Satu tahun ajaran aktif dalam satu waktu. |
| `periode` | id, tahun_ajaran_id (FK), jenis (enum `PTS`,`PAS`,`Lainnya`), nama, tgl_mulai, tgl_selesai, aktif, created_at, updated_at | Arsip periode lama tetap tersimpan & bisa dibuka/cetak. |
| `kelas` | id, nama (cth. `XI TKJ`), tingkat (X/XI/XII), created_at, updated_at | Kelas peserta ujian. |
| `mapel` | id, kode, nama, created_at, updated_at | Mata pelajaran bank soal. |

### 2.2 Pengguna — admin/proktor, guru, siswa
| Tabel | Kolom penting | Keterangan |
|---|---|---|
| `pengguna` | id, username, nama, password_hash (bcrypt), peran (enum `admin`,`proktor`), aktif, created_at, updated_at | Login admin/proktor. Akun awal dibuat via seeder; password wajib diganti. |
| `guru` | id, nama, nip (nullable), created_at, updated_at | Guru penyetor soal (nama saja, tanpa hak akses rumit). |
| `siswa` | id, nis (unik), nama, kelas_id (FK), pin_hash (bcrypt, untuk login offline), aktif, created_at, updated_at | Login siswa pakai NIS + PIN. Data hasil sinkron dari SIAKANUDA. |

### 2.3 Bank soal — bank, soal (fleksibel JSON)
| Tabel | Kolom penting | Keterangan |
|---|---|---|
| `bank_soal` | id, periode_id (FK), mapel_id (FK), nama, guru_id (FK, penyetor), created_at, updated_at | Satu bank soal = satu mapel dalam satu periode. |
| `soal` | id, bank_soal_id (FK), tipe (enum `pg`,`esai`,`isian`,`pg_kompleks`,`menjodohkan`), pertanyaan (HTML, disanitasi), bobot (decimal, default 1), konten_json (JSON — isi tergantung tipe), media_gambar (path relatif, nullable), media_audio (path relatif, nullable), aktif, created_at, updated_at | **Skema fleksibel sejak awal.** `konten_json` menampung isi per tipe (lihat bawah). Bobot sudah ada dari awal. |

**Isi `konten_json` per tipe soal:**
- `pg`: `{"opsi": ["A...", "B...", "C...", "D..."], "kunci": 1}` (kunci = indeks opsi benar)
- `esai`: `{"rubrik": "..."}` (kunci dinilai manual oleh proktor)
- `isian`: `{"kunci": "jawaban benar"}` (cocokkan teks, toleransi huruf besar/kecil)
- `pg_kompleks`: `{"opsi": [...], "kunci": [0, 2]}` (kunci = daftar indeks benar; rilis menyusul)
- `menjodohkan`: `{"pasangan": [{"kiri": "...", "kanan": "..."}, ...]}` (rilis menyusul)

### 2.4 Jadwal tes & peserta
| Tabel | Kolom penting | Keterangan |
|---|---|---|
| `jadwal_tes` | id, periode_id (FK), bank_soal_id (FK), nama, tanggal, jam_mulai, jam_selesai, durasi_menit, token (varchar, nullable), token_aktif (tinyint, default 0), status (enum `draft`,`aktif`,`selesai`), created_at, updated_at | Token opsional, default MATI. |
| `jadwal_tes_kelas` | id, jadwal_tes_id (FK), kelas_id (FK) | Pivot: kelas yang ikut satu jadwal tes. |

### 2.5 Mesin ujian — sesi, snapshot, jawaban, log
| Tabel | Kolom penting | Keterangan |
|---|---|---|
| `sesi_ujian` | id, jadwal_tes_id (FK), siswa_id (FK), waktu_mulai, waktu_selesai (nullable), status (enum `berlangsung`,`selesai`,`dipaksa_selesai`), nilai_akhir (decimal, nullable), created_at, updated_at | Satu baris per siswa per jadwal tes. |
| `snapshot_soal` | id, sesi_ujian_id (FK), soal_id (FK), urutan, pertanyaan_snapshot (HTML), konten_snapshot (JSON), bobot_snapshot (decimal) | **Snapshot soal saat siswa mulai tes.** Editan bank soal setelah itu tidak mengubah soal yang sedang dikerjakan. |
| `jawaban_siswa` | id, sesi_ujian_id (FK), snapshot_soal_id (FK), jawaban (text/JSON), benar (tinyint, nullable), skor (decimal, nullable), dikoreksi (tinyint, default 0), dikoreksi_oleh (FK pengguna, nullable), dikoreksi_at (nullable), created_at, updated_at | Jawaban tersimpan tiap klik (aman mati lampu). `benar`/`skor` diisi otomatis untuk PG/isian; esai menunggu koreksi manual. |
| `log_pindah_tab` | id, sesi_ujian_id (FK), siswa_id (FK), waktu, keterangan | Catatan peringatan pindah tab (peringatan + log saja, tanpa auto-submit). |

### 2.6 Jembatan & pengaturan
| Tabel | Kolom penting | Keterangan |
|---|---|---|
| `sso_token` | id, token_hash, siswa_id (FK), dibuat_at, kedaluwarsa_at, dipakai (tinyint, default 0) | Token SSO sekali pakai (HMAC-SHA256) dari SIAKANUDA. Dipasang Fase 4. |
| `pengaturan` | id, kunci (unik), nilai, keterangan, updated_at | Nama aplikasi, nama sekolah, dsb. |

**Catatan desain:**
- Semua tabel pakai InnoDB (transaksi + aman mati mendadak) dan `utf8mb4` (karakter Indonesia/Arab/Jepang aman).
- Foreign key aktif agar data tidak yatim (hapus bank soal tidak bisa kalau masih dipakai jadwal, dst.).
- `jawaban_siswa.jawaban` disimpan tiap klik — jadi mati lampu di tengah ujian tidak menghilangkan jawaban yang sudah diklik.
- Analisis butir soal dihitung dari `snapshot_soal.soal_id` (terkunci ke soal), bukan dari urutan acak — sesuai brief.
- Tidak ada tabel WA Bot dan tidak ada kolom nilai rapor — sesuai isolasi.

---

## 3. DOCKER COMPOSE (smknuda2)

Tiga service, satu jaringan internal `cbt-net`:
- **`nginx`** — image `nginx:alpine`, port host `80` (pintu offline LAN) — melayani `public/` dan meneruskan PHP ke `php:9000`.
- **`php`** — build dari `docker/php/Dockerfile` (PHP 8.4-FPM + OPcache + `intl`, `mbstring`, `mysqli`, `gd`), volume kode aplikasi, `TZ=Asia/Jakarta`.
- **`mariadb`** — image `mariadb:11`, database `cbt_siakanuda`, user khusus aplikasi (bukan root), volume data persisten, `innodb_flush_log_at_trx_commit=1` (aman mati mendadak), batas memori ~512 MB–1 GB.

Pintu online (`cbt.siakanuda.qzz.io`) dipasang belakangan lewat Cloudflare Tunnel yang sudah ada (tunnel `siakanuda`), mengarah ke port 80 container nginx ini — tanpa mengubah compose.

Kredensial database hanya ada di file `.env` (tidak masuk git).

---

## 4. YANG DIBANGUN DI FASE 1 (checklist)

- [x] Repo lokal `cbt-siakanuda` + kerangka CI4 4.7.6 (via Composer)
- [x] Rancangan struktur folder & tabel DB (dokumen ini)
- [ ] `docker-compose.yml` + Dockerfile PHP 8.4 + konfigurasi Nginx
- [ ] `.env.example` + `.gitignore` + README
- [ ] `App.php` baseURL dinamis + `Database.php` MariaDB (env)
- [ ] 17 migrasi DB (satu file per tabel, urut)
- [ ] Seeder akun awal & akun test semua role (keputusan Beng 17 Nov 2026 17.59 WIB — menggantikan rencana env CBT_ADMIN_PASSWORD): `admin`/`admin` (peran admin), `proktor`/`proktor` (peran proktor), siswa test NIS `0001` / PIN `1234` (kelas `X TKJ`). Akun-akun ini HANYA untuk awal/test; Beng yang menonaktifkan sendiri begitu sudah fix online. README wajib memuat peringatan ini.
- [ ] Layout dasar `siswa.php` & `admin.php` (zero-CDN, aset lokal) + halaman awal
- [ ] Uji jalan: `docker compose up` di smknuda2 (atau setempat) + migrasi jalan + halaman awal tampil
- [ ] Commit awal + push ke Gitea (setelah repo dibuat) & GitHub (setelah Beng buat repo kosong)

**Status repo remote (17 Nov 2026):** GitHub `bangunsetyawan/cbt-siakanuda` BELUM ada; Gitea `smknuda/cbt-siakanuda` BELUM ada (404). Perlu dibuatkan repo kosongnya oleh Beng (GitHub) — Gitea bisa dibuatkan Mus via API.
