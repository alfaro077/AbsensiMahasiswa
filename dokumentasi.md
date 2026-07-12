# Dokumentasi — Sistem Presensi Mahasiswa

Sistem informasi presensi mahasiswa berbasis web dengan fitur penjadwalan kuliah, absensi via QR Code / Kode Unik, serta notifikasi otomatis via Telegram.

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Fitur Aplikasi](#2-fitur-aplikasi)
3. [Aktor Pengguna](#3-aktor-pengguna)
4. [Alur Bisnis](#4-alur-bisnis)
5. [Struktur Database](#5-struktur-database)
6. [Tech Stack](#6-tech-stack)
7. [Instalasi & Menjalankan](#7-instalasi--menjalankan)
8. [Struktur File](#8-struktur-file)

---

## 1. Gambaran Umum

Aplikasi ini menangani seluruh siklus perkuliahan mulai dari:

```
Manajemen Data Master
    │
    ▼
Penjadwalan Kuliah (dengan deteksi bentrok)
    │
    ▼
Pelaksanaan Sesi Kuliah (generate QR & Kode Unik)
    │
    ▼
Presensi Mahasiswa (scan QR / input kode)
    │
    ▼
Rekap & Laporan Kehadiran
    │
    ▼
Notifikasi Jadwal Otomatis via Telegram
```

Tiga peran pengguna: **Admin** (mengelola semua data), **Dosen** (membuka sesi & merekap presensi), **Mahasiswa** (melakukan absensi).

---

## 2. Fitur Aplikasi

### Admin
- CRUD Mahasiswa (dengan pembuatan akun otomatis)
- CRUD Dosen (dengan pembuatan akun otomatis)
- CRUD Jurusan, Gedung, Ruangan
- CRUD Mata Kuliah & Kelas Paralel
- CRUD Jadwal Kuliah dengan **deteksi bentrok** (ruangan, dosen, kelas)
- Label **"Sudah Terjadwal"** pada dropdown (disable otomatis)
- Laporan Presensi semua mata kuliah
- Testing notifikasi Telegram

### Dosen
- Dashboard berisi jadwal & sesi aktif
- Membuka sesi kuliah baru (generate QR Code + Kode Unik)
- Melihat daftar kehadiran mahasiswa real-time
- Menyetujui / menolak presensi manual
- Menerima notifikasi jadwal harian via Telegram

### Mahasiswa
- Beranda dengan jadwal hari ini & sesi aktif
- Absensi via **scan QR Code** (kamera)
- Absensi via **input Kode Unik** (6 karakter)
- Histori kehadiran
- Menerima notifikasi jadwal harian via Telegram

---

## 3. Aktor Pengguna

| Role | Hak Akses | Redirect setelah login |
|---|---|---|
| **admin** | Full CRUD semua data, laporan, telegram test | `/mahasiswa` |
| **dosen** | Lihat jadwal sendiri, kelola sesi, rekap presensi | `/dashboard-dosen` |
| **mahasiswa** | Lihat jadwal sendiri, absensi, histori | `/dashboard-mahasiswa` |

Role dicek di dua tempat:
- **Client-side**: sidebar navigasi & tombol aksi disembunyikan berdasarkan role dari localStorage
- **Server-side**: controller memfilter data dan membatasi akses berdasarkan `$request->user()->role`

---

## 4. Alur Bisnis

### 4.1 Alur Master Data

```
Admin login → Kelola Jurusan → Kelola Gedung & Ruangan
           → Kelola Dosen (buat akun) → Kelola Mahasiswa (buat akun)
           → Kelola Mata Kuliah (assign dosen)
           → Kelola Kelas Paralel (A/B/C per MK)
```

### 4.2 Alur Penjadwalan

```
Admin buka halaman /jadwal → klik "Tambah Jadwal"

1. Pilih Mata Kuliah
   → cascading: load Kelas Paralel yang tersedia
2. Pilih Kelas Paralel
3. Pilih Hari, Jam Mulai, Jam Selesai
4. Pilih Gedung
   → cascading: load Ruangan di gedung tersebut
5. Pilih Ruangan

Sistem cek bentrok:
- Ruangan sudah dipakai?  → ERROR 422
- Dosen sudah mengajar?   → ERROR 422
- Kelas sudah ada jadwal? → ERROR 422

Tidak bentrok → Jadwal tersimpan
```

**Catatan:** Mata kuliah atau kelas paralel yang sudah memiliki jadwal akan muncul dengan label "(Sudah Terjadwal)" dan otomatis di-disable.

### 4.3 Alur Presensi

**Sisi Dosen:**
```
Dashboard → Buka Sesi Kuliah
  → Pilih Mata Kuliah
  → Set Tanggal & Jam
  → Generate QR Code + Kode Unik (6 karakter)
  → Tampilkan QR di layar / bacakan kode
  → Pantau kehadiran real-time
```

**Sisi Mahasiswa:**
```
Beranda / Jadwal
  → Lihat sesi aktif
  → Scan QR (buka kamera) atau input Kode Unik
  → Sistem validasi:
     - Sesi aktif?
     - Dalam rentang jam?
     - Belum absen sebelumnya?
     - Terdaftar di MK ini?
  → Berhasil: "Absensi tercatat"
```

**Metode Absensi:**

| Metode | Input | Cocok untuk |
|---|---|---|
| QR Code | Scan kamera via html5-qrcode | Tatap muka di kelas |
| Kode Unik | 6 karakter alfanumerik | Jika QR tidak terbaca |
| Manual (dosen) | Pilih nama mahasiswa | Kondisi khusus |

### 4.4 Alur Notifikasi Telegram

**Link Akun:**
```
User buka Profile → klik "Hubungkan Telegram"
  → Sistem generate link: https://t.me/bot?start=UUID
  → User klik link → buka Telegram → tekan Start
  → Bot kirim /start UUID ke webhook server
  → Server verifikasi UUID → simpan chat_id user
  → "✅ Akun berhasil terhubung!"
```

**Notifikasi Jadwal Harian (otomatis pukul 06:00 WIB):**
```
Sistem cek jadwal hari ini
  → Untuk setiap jadwal:
     → Kirim ke Dosen: info jadwal mengajar
     → Kirim ke Mahasiswa: info jadwal kuliah
  → Format HTML dengan emoji, bold, separator
```

**Command:**
| Perintah | Fungsi |
|---|---|
| `php artisan schedule:send-notification` | Kirim notifikasi jadwal hari ini |
| `php artisan schedule:test-notification` | Test token & kirim ke admin |
| `php artisan telegram:set-webhook` | Set webhook bot |
| `php artisan telegram:poll` | Poll update (alternatif webhook) |

---

## 5. Struktur Database

### 5.1 Model & Relasi

```
User (1) ──── (0..1) Dosen
User (1) ──── (0..1) Mahasiswa

Jurusan (1) ──── (banyak) Dosen, Mahasiswa, MataKuliah
Dosen (1) ──── (banyak) MataKuliah, KelasParalel

MataKuliah (1) ──── (banyak) KelasParalel, SesiKuliah, JadwalMataKuliah
KelasParalel (1) ──── (banyak) JadwalMataKuliah, Enrollment

Mahasiswa (banyak) ──── (banyak) MataKuliah (via Enrollment)
Mahasiswa (banyak) ──── (banyak) KelasParalel (via Enrollment)
Mahasiswa (1) ──── (banyak) Presensi

Gedung (1) ──── (banyak) Ruangan, JadwalMataKuliah
Ruangan (1) ──── (banyak) JadwalMataKuliah
SesiKuliah (1) ──── (banyak) Presensi
```

### 5.2 Tabel

| Tabel | Isi |
|---|---|
| `users` | Akun login (nama, email, password, role, telegram_chat_id, telegram_token) |
| `mahasiswa` | Data mahasiswa (nim, jurusan_id, angkatan) |
| `dosen` | Data dosen (nip, jabatan, jurusan_id) |
| `jurusan` | Program studi (kode, nama) |
| `mata_kuliah` | Mata kuliah (kode, nama, sks, semester, dosen_id, jurusan_id) |
| `kelas_paralel` | Kelas A/B/C (mata_kuliah_id, nama_kelas, dosen_id, tahun_ajaran) |
| `gedung` | Gedung (kode, nama, lokasi) |
| `ruangan` | Ruangan (gedung_id, nama, lantai, kapasitas) |
| `jadwal_mata_kuliah` | Jadwal tetap (mata_kuliah_id, kelas_paralel_id, hari, jam, gedung_id, ruangan_id) |
| `enrollment` | Pendaftaran MK (mahasiswa_id, mata_kuliah_id, kelas_paralel_id, tahun_ajaran) |
| `sesi_kuliah` | Sesi pertemuan (mata_kuliah_id, tanggal, jam, qr_code, kode_unik, is_active) |
| `presensi` | Kehadiran (sesi_id, mahasiswa_id, waktu_absen, metode, status) |

---

## 6. Tech Stack

| Bagian | Teknologi |
|---|---|
| Backend | PHP 8.3+, Laravel 13 |
| Frontend | Blade, Tailwind CSS 4 (CDN), jQuery 3.7.1 |
| Tabel | DataTables 1.13.6 |
| Auth API | Laravel Sanctum (token) |
| Database | SQLite (dev) / MySQL (production) |
| Notifikasi | Telegram Bot API |
| QR Code | qrcode.js + html5-qrcode |
| Build Tool | Vite |

---

## 7. Instalasi & Menjalankan

### Persyaratan
- PHP 8.3+, Composer, Node.js (opsional)

### Instalasi

```bash
git clone <repo-url>
cd AbsensiMahasiswa
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### Menjalankan

**Manual (2 terminal):**
```bash
# Terminal 1
php artisan serve

# Terminal 2 (untuk notifikasi otomatis)
php artisan schedule:work
```

**Via dev.ps1 (1 perintah):**
```powershell
.\dev.ps1
```
→ Notifikasi startup Telegram + scheduler background + serve.

### Setup Telegram (opsional)

Edit `.env`:
```
TELEGRAM_BOT_TOKEN=token_dari_botfather
TELEGRAM_BOT_USERNAME=username_bot
TELEGRAM_ADMIN_CHAT_ID=id_chat_admin
```

### Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Dosen | dosen1@example.com | password |
| Mahasiswa | andi@example.com | password |

---

## 8. Struktur File

```
app/
├── Console/Commands/SendScheduleNotification.php
├── Http/
│   ├── Controllers/Api/   (15 controllers)
│   └── Requests/           (9 form requests)
├── Models/                 (12 models)
├── Services/TelegramService.php
└── Traits/
    ├── ApiResponse.php
    └── Filterable.php

config/
├── services.php            (telegram config)

database/
├── migrations/             (22 migrations)
└── seeders/

resources/views/
├── auth/login.blade.php
├── dashboard/ (dosen, mahasiswa)
├── layouts/app.blade.php
├── mahasiswa/index.blade.php
├── dosen/index.blade.php
├── jadwal/index.blade.php
├── laporan/index.blade.php
├── profile/index.blade.php
├── telegram-test/index.blade.php
└── ... (gedung, jurusan, ruangan, mata_kuliah)

routes/
├── api.php                 (30+ API endpoints)
├── web.php                 (15+ web routes)
└── console.php             (5 Artisan commands)

dev.ps1                     (dev server starter)
```

---

*Dokumentasi ini diperbarui: Juli 2026*
