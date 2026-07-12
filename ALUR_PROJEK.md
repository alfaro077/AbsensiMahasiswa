# Dokumentasi Projek — Sistem Presensi Mahasiswa

> **📗 Dokumentasi ini berisi alur bisnis & fitur** untuk presentasi / tugas.
> Jika Anda mencari dokumentasi teknis alur kode, buka **`ALUR_KODE.md`**.

**Diajukan untuk:** Tugas / Presentasi Mata Kuliah  
**Nama Projek:** Sistem Presensi Mahasiswa Berbasis Web dengan Notifikasi Telegram  
**Teknologi:** Laravel 13, Tailwind CSS, MySQL, Telegram Bot API

---

## 📚 Daftar Isi

1. [Latar Belakang](#1-latar-belakang)
2. [Tujuan Projek](#2-tujuan-projek)
3. [Aktor & Role Pengguna](#3-aktor--role-pengguna)
4. [Fitur-Fitur Aplikasi](#4-fitur-fitur-aplikasi)
5. [Alur Bisnis (Business Flow)](#5-alur-bisnis-business-flow)
6. [Halaman Aplikasi](#6-halaman-aplikasi)
7. [Tech Stack](#7-tech-stack)
8. [Struktur Database](#8-struktur-database)
9. [Cara Menjalankan](#9-cara-menjalankan)
10. [Akun Default untuk Demo](#10-akun-default-untuk-demo)

---

## 1. Latar Belakang

Sistem presensi di perguruan tinggi masih banyak yang dilakukan secara manual (tanda tangan di kertas) atau menggunakan aplikasi terpisah yang tidak terintegrasi. Hal ini menyebabkan beberapa masalah:

- **Rekap presensi** memakan waktu dan rawan kesalahan
- **Notifikasi jadwal** tidak otomatis — dosen dan mahasiswa harus mengecek jadwal manual
- **Tidak ada histori** yang tersimpan rapi dan mudah diakses

Projek ini hadir sebagai solusi **sistem presensi terintegrasi** berbasis web yang menangani seluruh alur dari penjadwalan, presensi, hingga notifikasi otomatis via Telegram.

---

## 2. Tujuan Projek

| No | Tujuan |
|---|---|
| 1 | Memudahkan admin mengelola data master (mahasiswa, dosen, jurusan, mata kuliah) |
| 2 | Memudahkan penjadwalan kuliah dengan deteksi bentrok otomatis |
| 3 | Memudahkan dosen membuka sesi kuliah dan memantau kehadiran |
| 4 | Memudahkan mahasiswa melakukan absensi via QR Code atau Kode Unik |
| 5 | Menyediakan laporan presensi secara real-time |
| 6 | Mengirim notifikasi jadwal otomatis ke Telegram dosen dan mahasiswa |

---

## 3. Aktor & Role Pengguna

Sistem memiliki **3 role** pengguna:

```
┌─────────────────────────────────────────────────────────┐
│                    SISTEM PRESENSI                      │
│                                                         │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐         │
│   │  ADMIN   │    │  DOSEN   │    │ MAHASISWA│         │
│   └────┬─────┘    └────┬─────┘    └────┬─────┘         │
│        │               │               │               │
│        ▼               ▼               ▼               │
│   ┌────────────┐ ┌────────────┐ ┌────────────────┐    │
│   │ Full akses │ │ Kelola     │ │ Lihat jadwal   │    │
│   │ CRUD semua │ │ sesi kuliah│ │ Absensi via QR │    │
│   │ data master│ │ Rekap absen│ │ / Kode Unik    │    │
│   │ Laporan    │ │           │ │ Histori absen  │    │
│   └────────────┘ └────────────┘ └────────────────┘    │
└─────────────────────────────────────────────────────────┘
```

### 3.1 Admin

- Mengelola data **Mahasiswa** (CRUD + buat akun)
- Mengelola data **Dosen** (CRUD + buat akun)
- Mengelola **Jurusan**, **Gedung**, **Ruangan**
- Mengelola **Mata Kuliah** dan **Kelas Paralel**
- Mengatur **Jadwal Kuliah** dengan deteksi bentrok
- Melihat **Laporan Presensi** seluruh mata kuliah
- **Testing notifikasi Telegram**

### 3.2 Dosen

- Melihat **Dashboard** berisi sesi aktif dan jadwal
- **Membuka Sesi Kuliah** baru (generate QR Code & Kode Unik)
- Melihat daftar **mahasiswa yang hadir**
- **Menyetujui / Menolak** presensi mahasiswa (manual)
- Menerima **notifikasi jadwal** otomatis di Telegram

### 3.3 Mahasiswa

- Melihat **jadwal kuliah** hari ini
- Melakukan **absensi** via scan QR Code atau input Kode Unik
- Melihat **histori kehadiran**
- Menerima **notifikasi jadwal** otomatis di Telegram

---

## 4. Fitur-Fitur Aplikasi

### 4.1 Fitur Umum

| Fitur | Admin | Dosen | Mahasiswa |
|---|---|---|---|
| Login / Logout | ✅ | ✅ | ✅ |
| Edit Profil | ✅ | ✅ | ✅ |
| Lihat Jadwal Kuliah | ✅ | ✅ | ✅ |
| Hubungkan Telegram | ✅ | ✅ | ✅ |

### 4.2 Fitur Khusus Admin

| Fitur | Keterangan |
|---|---|
| **CRUD Mahasiswa** | Tambah/Edit/Hapus data mahasiswa + akun login |
| **CRUD Dosen** | Tambah/Edit/Hapus data dosen + akun login |
| **CRUD Jurusan** | Kelola jurusan (TI, SI, dll) |
| **CRUD Gedung & Ruangan** | Kelola lokasi perkuliahan |
| **CRUD Mata Kuliah** | Kelola mata kuliah per jurusan |
| **CRUD Kelas Paralel** | Kelola kelas A/B/C per mata kuliah |
| **CRUD Jadwal** | Atur jadwal dengan deteksi bentrok ruangan/dosen/kelas |
| **Laporan Presensi** | Rekap kehadiran semua mata kuliah |
| **Telegram Testing** | Kirim notifikasi uji coba ke user |

### 4.3 Fitur Khusus Dosen

| Fitur | Keterangan |
|---|---|
| **Dashboard** | Lihat sesi aktif, jadwal hari ini |
| **Buka Sesi Kuliah** | Buat sesi baru, generate QR Code & Kode Unik |
| **Rekap Presensi** | Lihat mahasiswa hadir/tidak, setujui/tolak |
| **Filter Mata Kuliah** | Hanya melihat mata kuliah yang diajar |

### 4.4 Fitur Khusus Mahasiswa

| Fitur | Keterangan |
|---|---|
| **Beranda** | Lihat jadwal hari ini, sesi aktif |
| **Absensi QR Code** | Scan QR dari dosen untuk absen |
| **Absensi Kode Unik** | Input 6 karakter kode unik untuk absen |
| **Histori Absensi** | Riwayat kehadiran semua mata kuliah |

---

## 5. Alur Bisnis (Business Flow)

### 5.1 Alur Master Data

```
Admin Login
    │
    ├──► Kelola Jurusan (TI, SI, dll)
    ├──► Kelola Gedung & Ruangan
    ├──► Kelola Dosen (buat akun + assign jurusan)
    ├──► Kelola Mahasiswa (buat akun + assign jurusan)
    ├──► Kelola Mata Kuliah (assign ke dosen & jurusan)
    └──► Kelola Kelas Paralel (A/B per mata kuliah)
```

### 5.2 Alur Penjadwalan

```
Admin → Buka Halaman Jadwal
    │
    ├── Klik "Tambah Jadwal"
    │
    ├── Pilih Mata Kuliah (dropup)
    │   └── Otomatis: load Kelas Paralel yang tersedia
    │
    ├── Pilih Kelas Paralel
    ├── Pilih Hari, Jam Mulai, Jam Selesai
    ├── Pilih Gedung
    │   └── Otomatis: load Ruangan di gedung tersebut
    └── Pilih Ruangan
         │
         ▼
    Sistem cek bentrok:
    ├── Ruangan sudah dipakai?     → ERROR
    ├── Dosen sudah mengajar?      → ERROR
    └── Kelas sudah ada jadwal?    → ERROR
         │
         ✓ Tidak bentrok → Jadwal tersimpan
```

### 5.3 Alur Presensi (Absensi)

```
DOSEN                                    MAHASISWA
─────                                    ─────────
Login                                    Login
  │                                        │
  ▼                                        ▼
Dashboard / Buka Sesi                   Beranda / Jadwal
  │                                        │
  ├── Pilih Mata Kuliah                    │
  ├── Pilih Tanggal & Jam                   │
  ├── Generate QR + Kode Unik             │
  └── Tampilkan QR di layar               │
       │                                   │
       │  👨‍🏫 Dosen tampilkan QR           │
       │  🔢 Dosen bacakan Kode Unik       │
       │                                   │
       ▼                                   ▼
  ┌─────────────────────────────────────┐
  │                                     │
  │    QR SCAN / INPUT KODE UNIK        │
  │                                     │
  │    Sistem validasi:                  │
  │    ├── Sesi aktif?                  │
  │    ├── Dalam jam kuliah?            │
  │    ├── Sudah absen? (cegah ganda)   │
  │    └── Mahasiswa terdaftar?         │
  │                                     │
  │    ✓ BERHASIL → "Absensi tercatat"  │
  └─────────────────────────────────────┘
       │
       ▼
DOSEN lihat rekap real-time
    ├── Total hadir
    ├── Total alpha
    └── Detail per mahasiswa
```

### 5.4 Alur Notifikasi Telegram

```
SETIAP PAGI PUKUL 06:00 WIB (otomatis)
    │
    ├── Sistem cek jadwal hari ini
    │
    ├── Kirim notifikasi ke DOSEN:
    │   "Selamat pagi, Bapak/Ibu Dosen!
    │    📋 Jadwal Mengajar Hari Ini
    │    📚 Algoritma (TI101) - Kelas A
    │    🕐 08:00 - 10:00 WIB
    │    🏢 Gedung A - Lt.2 - Ruang 201
    │    👥 40 mahasiswa terdaftar"
    │
    └── Kirim notifikasi ke MAHASISWA:
        "Selamat pagi!
         📋 Jadwal Kuliah Hari Ini
         📚 Algoritma - Kelas A
         🕐 08:00 - 10:00 WIB
         🏢 Gedung A - Lt.2 - Ruang 201
         👨‍🏫 Dosen: Budi Susanto"
```

### 5.5 Alur Link Akun Telegram

```
User (di aplikasi)
    │
    ├── Buka halaman Profile
    ├── Klik "Hubungkan Telegram"
    │
    ▼
Sistem generate link:
    https://t.me/absensi_notif_bot?start=UUID
    │
    ▼
User klik link → terbuka Telegram → tekan "Start"
    │
    ▼
Bot menerima perintah /start UUID
    │
    ▼
Sistem verifikasi token → simpan chat_id
    │
    ▼
"✅ Akun Anda berhasil terhubung!"
    │
    ▼
Notifikasi jadwal otomatis masuk setiap pagi ✅
```

---

## 6. Halaman Aplikasi

### 6.1 Halaman Login (`/login`)

Halaman awal aplikasi. User memasukkan email dan password untuk login.

**Fungsi:**
- Form login dengan email & password
- Validasi client & server side
- Redirect sesuai role setelah login

### 6.2 Dashboard & Menu Sidebar

Setelah login, tampilan menyesuaikan role:

| Role | Link Sidebar |
|---|---|
| **Admin** | Mahasiswa, Dosen, Jurusan, Gedung, Ruangan, Jadwal Kuliah, Mata Kuliah, Laporan, Telegram Test, Profil Saya |
| **Dosen** | Dashboard, Jadwal Kuliah, Mata Kuliah, Profil Saya |
| **Mahasiswa** | Beranda, Jadwal Kuliah, Mata Kuliah, Profil Saya |

### 6.3 CRUD Mahasiswa (`/mahasiswa`)

**Fungsi:**
- DataTable: NIM, Nama, Jurusan, Angkatan, Aksi (Edit/Hapus)
- Modal form: Nama, Email, Password, NIM, Jurusan (pilihan), Angkatan
- Filter & pencarian real-time

### 6.4 CRUD Dosen (`/dosen`)

**Fungsi:**
- DataTable: NIP, Nama, Jurusan, Jabatan
- Modal form: Nama, Email, Password, NIP, Jabatan, Jurusan

### 6.5 CRUD Jadwal Kuliah (`/jadwal`)

**Fungsi:**
- DataTable: Mata Kuliah, Kelas, Hari, Waktu, Gedung, Lantai, Ruangan
- Modal form dengan cascading dropdown:
  - Pilih Mata Kuliah → load Kelas Paralel
  - Pilih Gedung → load Ruangan
- **Deteksi bentrok otomatis** (ruangan, dosen, kelas)
- **Label "Sudah Terjadwal"** pada option yang sudah punya jadwal (disable)

### 6.6 Dashboard Dosen (`/dashboard-dosen`)

**Fungsi:**
- Info sesi aktif saat ini
- Tabel sesi kuliah (CRUD) — buat sesi baru, generate QR
- Tombol "Buka Sesi" → generate QR Code + Kode Unik
- Modal detail presensi (lihat mahasiswa hadir, setujui/tolak)

### 6.7 Beranda Mahasiswa (`/dashboard-mahasiswa`)

**Fungsi:**
- Jam real-time
- Daftar sesi aktif untuk absensi
- Tombol "Absen QR" (buka kamera via html5-qrcode)
- Input Kode Unik untuk absen manual
- Riwayat absensi (DataTable)

### 6.8 Laporan Presensi (`/laporan`)

**Fungsi:**
- Pilih Mata Kuliah
- Pilih Kelas Paralel
- Pilih Sesi
- Tabel rekap: Nama Mahasiswa, Status (Hadir/Izin/Sakit/Alpha), Waktu Absen
- Ringkasan: Total Mahasiswa, Hadir, Izin, Sakit, Alpha, Persentase

### 6.9 Halaman Profile (`/profile`)

**Fungsi:**
- Info profil pengguna
- Edit nama, email, password
- **Hubungkan Telegram** — generate link untuk menghubungkan bot Telegram
- Status koneksi Telegram (terhubung / belum)

### 6.10 Telegram Testing (`/telegram-test`)

**Fungsi (khusus admin):**
- Status bot (token valid, bot username, webhook)
- Daftar user yang sudah terhubung Telegram
- Kirim pesan kustom ke user tertentu
- Kirim simulasi notifikasi jadwal ke user tertentu

---

## 7. Tech Stack

| Layer | Teknologi | Kegunaan |
|---|---|---|
| **Backend** | PHP 8.3+ | Bahasa pemrograman utama |
| **Framework** | Laravel 13 | MVC framework, routing, ORM, auth |
| **Frontend** | Tailwind CSS 4 | Styling UI modern & responsif |
| **Frontend JS** | jQuery 3.7.1 | Manipulasi DOM & AJAX |
| **Tabel** | DataTables 1.13.6 | Sorting, searching, pagination |
| **Auth API** | Laravel Sanctum | Token-based authentication |
| **Database** | MySQL / SQLite | Penyimpanan data |
| **Notifikasi** | Telegram Bot API | Push notification jadwal |
| **QR Code** | qrcode.js + html5-qrcode | Generate & scan QR Code |

### Arsitektur Umum

```
┌─────────────────────────────────────────────────┐
│                   BROWSER                        │
│  (Blade + Tailwind + jQuery + DataTables)        │
└──────────────────────┬──────────────────────────┘
                       │ AJAX (JSON)
                       │ Authorization: Bearer
                       ▼
┌─────────────────────────────────────────────────┐
│           LARAVEL BACKEND (API)                  │
│                                                   │
│  routes/api.php ──► Controllers ──► Models        │
│                         │              │          │
│                    Traits:           Database     │
│                    Filterable        (MySQL)      │
│                    ApiResponse                    │
│                         │                        │
│               ┌─────────┴─────────┐              │
│               │ TelegramService   │              │
│               │ SendSchedule...   │              │
│               └───────────────────┘              │
└──────────────────────┬──────────────────────────┘
                       │ HTTPS
                       ▼
┌─────────────────────────────────────────────────┐
│              TELEGRAM BOT API                    │
│  Mengirim notifikasi ke Dosen & Mahasiswa        │
└─────────────────────────────────────────────────┘
```

---

## 8. Struktur Database

### 8.1 Entity Relationship (Ringkasan)

```
User (1) ──── (0..1) Dosen
User (1) ──── (0..1) Mahasiswa

Jurusan (1) ──── (banyak) Dosen
Jurusan (1) ──── (banyak) Mahasiswa
Jurusan (1) ──── (banyak) MataKuliah

Dosen (1) ──── (banyak) MataKuliah

MataKuliah (1) ──── (banyak) KelasParalel
MataKuliah (1) ──── (banyak) SesiKuliah
MataKuliah (1) ──── (banyak) JadwalMataKuliah

KelasParalel (1) ──── (banyak) JadwalMataKuliah
KelasParalel (1) ──── (banyak) Enrollment

Mahasiswa (banyak) ──── (banyak) MataKuliah   (via Enrollment)
Mahasiswa (banyak) ──── (banyak) KelasParalel  (via Enrollment)
Mahasiswa (1) ──── (banyak) Presensi

Gedung (1) ──── (banyak) Ruangan
Gedung (1) ──── (banyak) JadwalMataKuliah
Ruangan (1) ──── (banyak) JadwalMataKuliah

SesiKuliah (1) ──── (banyak) Presensi
```

### 8.2 Daftar Tabel

| No | Tabel | Fungsi |
|---|---|---|
| 1 | `users` | Akun login (nama, email, password, role, telegram fields) |
| 2 | `mahasiswa` | Data mahasiswa (nim, jurusan, angkatan) |
| 3 | `dosen` | Data dosen (nip, jabatan, jurusan) |
| 4 | `jurusan` | Program studi (kode, nama) |
| 5 | `mata_kuliah` | Mata kuliah (kode, nama, sks, semester, dosen) |
| 6 | `kelas_paralel` | Kelas A/B untuk setiap MK (nama_kelas, dosen, tahun) |
| 7 | `gedung` | Gedung perkuliahan (kode, nama, lokasi) |
| 8 | `ruangan` | Ruangan di dalam gedung (nama, lantai, kapasitas) |
| 9 | `jadwal_mata_kuliah` | Jadwal tetap (hari, jam, ruangan, kelas) |
| 10 | `enrollment` | Pendaftaran mahasiswa ke MK & kelas |
| 11 | `sesi_kuliah` | Sesi pertemuan (tanggal, jam, QR, kode unik) |
| 12 | `presensi` | Data kehadiran (status, waktu, metode) |

---

## 9. Cara Menjalankan

### 9.1 Persiapan

```bash
# 1. Clone projek
git clone https://github.com/username/AbsensiMahasiswa.git
cd AbsensiMahasiswa

# 2. Install PHP dependencies
composer install

# 3. Copy environment
cp .env.example .env

# 4. Generate key
php artisan key:generate

# 5. Migrasi & seed database
php artisan migrate --seed

# 6. Install JS dependencies (opsional, untuk production build)
npm install
npm run build
```

### 9.2 Menjalankan (Development)

**Cara 1 — Manual (2 terminal):**
```bash
# Terminal 1:
php artisan serve

# Terminal 2:
php artisan schedule:work    # untuk notifikasi otomatis
```

**Cara 2 — Dev Starter (1 perintah):**
```powershell
.\dev.ps1
```
Fitur: notifikasi startup Telegram + scheduler background + serve.

### 9.3 Setup Telegram (Opsional)

```bash
# 1. Isi .env:
TELEGRAM_BOT_TOKEN=token_dari_botfather
TELEGRAM_BOT_USERNAME=username_bot
TELEGRAM_ADMIN_CHAT_ID=id_chat_admin

# 2. Test koneksi
php artisan schedule:test-notification

# 3. Kirim notifikasi jadwal
php artisan schedule:send-notification
```

### 9.4 Akses Aplikasi

```
http://localhost:8000
```

---

## 10. Akun Default untuk Demo

Setelah menjalankan `php artisan migrate --seed`, akun berikut tersedia:

| Role | Nama | Email | Password |
|---|---|---|---|
| **Admin** | Administrator | `admin@example.com` | `password` |
| **Dosen** | Budi Susanto, M.Kom | `dosen1@example.com` | `password` |
| **Dosen** | Siti Aminah, Ph.D. | `dosen2@example.com` | `password` |
| **Mahasiswa** | Andi Wijaya | `andi@example.com` | `password` |
| **Mahasiswa** | Bambang Heru | `bambang@example.com` | `password` |
| **Mahasiswa** | Cici Paramida | `cici@example.com` | `password` |
| **Mahasiswa** | Dedi Pratama | `dedi@example.com` | `password` |
| **Mahasiswa** | Eka Safitri | `eka@example.com` | `password` |

### Data Seeder Lainnya

| Data | Jumlah |
|---|---|
| Jurusan | 1 (TI - Teknik Informatika) |
| Mata Kuliah | 5 (Algoritma, MatDis, Sistem Digital, Struktur Data, Basis Data) |
| Kelas Paralel | 10 (2 per MK: A dan B) |
| Gedung | 1 (Gedung A) |
| Ruangan | 4 (101, 102, 201, 202) |
| Jadwal | 8 entries (berbagai hari & jam) |

---

*Dokumentasi ini disusun untuk keperluan presentasi projek.*  
*Sistem Presensi Mahasiswa — 2026*
