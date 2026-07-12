# Dokumentasi Teknis Alur Kode — AbsensiMahasiswa

> **📘 Dokumentasi ini berisi penjelasan teknis alur kode** untuk pengembang.
> Jika Anda mencari dokumentasi untuk presentasi / tugas, buka **`ALUR_PROJEK.md`**.

## 📋 Daftar Isi

1. [Arsitektur Aplikasi](#1-arsitektur-aplikasi)
2. [Alur Autentikasi](#2-alur-autentikasi)
3. [Alur Request / Response API](#3-alur-request--response-api)
4. [Alur CRUD (Frontend + Backend)](#4-alur-crud-frontend--backend)
5. [Alur Presensi (Absensi)](#5-alur-presensi-absensi)
6. [Alur Jadwal Kuliah](#6-alur-jadwal-kuliah)
7. [Alur Notifikasi Telegram](#7-alur-notifikasi-telegram)
8. [Entity Relationship Diagram](#8-entity-relationship-diagram)
9. [Role-Based Access Control](#9-role-based-access-control)
10. [Developer Guide](#10-developer-guide)

---

## 1. Arsitektur Aplikasi

### Tech Stack

| Layer | Teknologi |
|---|---|
| **Framework** | Laravel 13.x (PHP 8.3+) |
| **Database** | SQLite (default dev) / MySQL (production) |
| **Auth** | Laravel Sanctum (Token-based API) |
| **Frontend** | Blade + Tailwind CSS 4 + jQuery 3.7.1 |
| **Table UI** | DataTables 1.13.6 |
| **Notification** | jQuery AJAX + SweetAlert2 |
| **Notifikasi** | Telegram Bot API |
| **Build** | Vite (for CSS/JS minimal) |

### Struktur Folder

```
app/
├── Console/
│   └── Commands/
│       └── SendScheduleNotification.php    # Notifikasi jadwal harian via Telegram
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php                  # Base controller (abstract)
│   │   └── Api/
│   │       ├── AuthController.php          # Login / Register
│   │       ├── MahasiswaController.php     # CRUD mahasiswa
│   │       ├── DosenController.php         # CRUD dosen
│   │       ├── MataKuliahController.php    # CRUD mata kuliah
│   │       ├── KelasParalelController.php  # CRUD kelas paralel
│   │       ├── JadwalMataKuliahController.php # CRUD jadwal + bentrok detection
│   │       ├── SesiKuliahController.php    # CRUD sesi kuliah (per pertemuan)
│   │       ├── PresensiController.php      # Absensi + laporan
│   │       ├── EnrollmentController.php    # Pendaftaran MK mahasiswa
│   │       ├── GedungController.php        # CRUD gedung
│   │       ├── RuanganController.php       # CRUD ruangan
│   │       ├── JurusanController.php       # CRUD jurusan
│   │       ├── UserController.php          # CRUD user
│   │       ├── ProfileController.php       # Profile user
│   │       └── TelegramBotController.php   # Bot Telegram
│   └── Requests/                           # 9 FormRequest classes
├── Models/                                  # 12 Eloquent models
├── Services/
│   └── TelegramService.php                 # Wrapper API Telegram
├── Traits/
│   ├── ApiResponse.php                     # Standard JSON response helpers
│   └── Filterable.php                      # Filter, search, sort, paginate
routes/
├── api.php                                 # 30+ API endpoints
├── web.php                                 # 15+ web routes (Blade views)
└── console.php                             # 5 Artisan commands
resources/views/
├── layouts/app.blade.php                   # Layout utama (navbar, sidebar)
├── auth/login.blade.php                    # Halaman login
├── dashboard/dosen.blade.php               # Dashboard dosen
├── dashboard/mahasiswa.blade.php           # Dashboard mahasiswa
├── mahasiswa/index.blade.php               # CRUD mahasiswa
├── dosen/index.blade.php                   # CRUD dosen
├── jadwal/index.blade.php                  # CRUD jadwal kuliah
├── profile/index.blade.php                 # Profil + link Telegram
├── telegram-test/index.blade.php           # Testing Telegram
└── ... berbagai CRUD views lainnya
```

---

## 2. Alur Autentikasi

### Flow Diagram

```
[Browser]                          [Server]
    |                                 |
    |-- POST /api/login ------------>|  AuthController@login
    |   {email, password}            |     |
    |                                 |     ├── Validasi input
    |                                 |     ├── Cari user by email
    |                                 |     ├── Hash::check(password)
    |                                 |     ├── Buat Sanctum token
    |                                 |     └── Response JSON:
    |                                 |         {user, access_token}
    |<-- 200 {user, token} ----------|
    |                                 |
    |-- Simpan ke localStorage -------|  [Frontend]
    |   token, user JSON              |
    |                                 |
    |-- GET /mahasiswa -------------->|  [Dengan header:
    |   Authorization: Bearer {token} |   Authorization: Bearer]
    |                                 |  UserController@index
    |<-- 200 {data: [...]} ----------|
```

### Endpoint

| Method | Endpoint | Controller | Deskripsi |
|---|---|---|---|
| `POST` | `/api/register` | `AuthController@register` | Register mahasiswa/dosen baru |
| `POST` | `/api/login` | `AuthController@login` | Login, return token + user |

### Register Flow (di dalam database transaction)

```
POST /api/register {nama, email, password, role, nim, jurusan_id, ...}
    │
    ├── role = 'mahasiswa'?
    │   ├── Buat User (nama, email, password hashed, role)
    │   ├── Buat Mahasiswa (user_id, nim, jurusan_id, angkatan)
    │   └── Load relasi mahasiswa
    │
    ├── role = 'dosen'?
    │   ├── Buat User
    │   ├── Buat Dosen (user_id, nip, jabatan, jurusan_id)
    │   └── Load relasi dosen
    │
    └── Generate Sanctum token
    └── Return {user, access_token}
```

### Login Flow

```
POST /api/login {email, password}
    │
    ├── Validasi: email required|email, password required
    ├── Cari User::with('mahasiswa', 'dosen')->where('email', ...)
    ├── Hash::check($password, $user->password)
    │   ├── Gagal → 401 "Email atau password salah"
    │   └── Berhasil → Generate Sanctum token
    └── Response: {user, access_token, token_type}
```

### Token di Frontend

Setelah login berhasil, token disimpan di `localStorage`:

```javascript
// auth/login.blade.js
localStorage.setItem('token', response.access_token);
localStorage.setItem('user', JSON.stringify(response.user));
```

Semua AJAX request selanjutnya menyertakan header:

```javascript
// layouts/app.blade.js
$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    }
});
```

---

## 3. Alur Request / Response API

### Flow Umum

```
[Browser] --> GET /api/mahasiswa?search=Andi&sort_by=nama&per_page=10
                    │
                    ▼
              routes/api.php
              Route::apiResource('mahasiswa', MahasiswaController::class)
                    │
                    ▼
              Middleware: auth:sanctum
              (cek token di header Authorization)
                    │
                    ▼
              MahasiswaController@index(Request $request)
                    │
                    ├── $query = Mahasiswa::query()
                    ├── $query->with('user', 'jurusan')
                    │
                    ├── $this->applyFilters(
                    │       query: $query,
                    │       request: $request,
                    │       filterableFields: ['jurusan_id', 'angkatan'],
                    │       searchableFields: ['nim'],
                    │       sortableFields: ['id', 'nim', 'angkatan'],
                    │   )
                    │   │
                    │   ├── Exact match filter: ?jurusan_id=1
                    │   ├── LIKE search: ?search=Andi → WHERE nim LIKE '%Andi%'
                    │   ├── Sort: ?sort_by=nama&sort_dir=asc
                    │   └── Paginate: ?per_page=10&page=1
                    │
                    └── return $this->success($result, '...')
                              │
                              ▼
                    JSON Response:
                    {
                        "success": true,
                        "message": "...",
                        "data": [...],
                        "meta": {
                            "current_page": 1,
                            "last_page": 5,
                            "per_page": 10,
                            "total": 50,
                            "from": 1,
                            "to": 10
                        },
                        "links": {
                            "first": "...",
                            "last": "...",
                            "prev": null,
                            "next": "..."
                        }
                    }
```

### ApiResponse Trait

Semua controller menggunakan trait `ApiResponse` yang menyediakan 5 method helper:

```php
trait ApiResponse {
    success($data, $message, $code = 200)
    created($data, $message)           // 201
    error($message, $code, $errors)    // 4xx/5xx
    notFound($message)                 // 404
    validationError($errors, $message) // 422
}
```

### Filterable Trait

Trait `Filterable` digunakan oleh sebagian besar controller untuk menangani:

```
?search=keyword    → LIKE search pada field yang dikonfigurasi
?sort_by=field     → Sorting (whitelist validation)
?sort_dir=asc|desc → Arah sorting
?per_page=15       → Pagination size (clamped 1-1000)
?page=1            → Halaman
?field=value       → Exact match filter
?include=rel       → Eager load relasi
```

---

## 4. Alur CRUD (Frontend + Backend)

### Pattern Umum (Contoh: Mahasiswa)

Setiap halaman CRUD di proyek ini mengikuti pattern yang sama:

```
┌─────────────────────────────────────────────┐
│  Mahasiswa                                   │
│  ┌─────────────────────────────────────────┐ │
│  │ [Tambah Data] (hanya untuk admin)       │ │
│  ├─────────────────────────────────────────┤ │
│  │ DataTable:                               │ │
│  │ ┌──────┬──────┬────────┬────────┬─────┐ │ │
│  │ │ NIM  │ Nama │Jurusan │Angkatan│Aksi │ │ │
│  │ ├──────┼──────┼────────┼────────┼─────┤ │ │
│  │ │20001 │ Andi │ TI     │ 2023   │E|H  │ │ │
│  │ │20002 │ Budi │ TI     │ 2023   │E|H  │ │ │
│  │ └──────┴──────┴────────┴────────┴─────┘ │ │
│  └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘

┌───────── Modal Form ─────────┐
│  Tambah / Edit Mahasiswa     │
│  ┌─────────────────────────┐ │
│  │ Nama: [_____________]  │ │
│  │ Email: [_____________] │ │
│  │ NIM:  [_____________]  │ │
│  │ Jurusan: [▼ select ]  │ │
│  │ Angkatan: [________]   │ │
│  ├─────────────────────────┤ │
│  │ [Batal]    [Simpan]    │ │
│  └─────────────────────────┘ │
└─────────────────────────────┘
```

### Alur Create

```
1. Admin klik "Tambah Data"
    → openModal('add')
    → Reset form, kosongkan semua field
    → Load options (jurusan, dll) via AJAX GET

2. Admin isi form, klik "Simpan"
    → Form submit handler
    → AJAX POST /api/mahasiswa {nama, email, nim, ...}
    → MahasiswaController@store:
        a. Validasi via MahasiswaRequest
        b. DB transaction: create user + create mahasiswa
        c. Return 201 + data mahasiswa
    → Success: close modal, reload DataTable, notifikasi

3. Error validasi:
    → 422: tampilkan error di form
    → Lainnya: SweetAlert error
```

### Alur Edit

```
1. Admin klik "Edit" pada baris tertentu
    → openModal('edit', id)
    → AJAX GET /api/mahasiswa/{id}?include=user
    → Isi form dengan data dari response
    → Load options yang sudah di-cache

2. Admin ubah data, klik "Simpan"
    → AJAX PUT /api/mahasiswa/{id} {nama, email, ...}
    → MahasiswaController@update:
        a. Validasi
        b. Update user + mahasiswa
        c. Return 200
    → Success: reload table
```

### Alur Delete

```
1. Admin klik "Hapus"
    → SweetAlert confirmation:
      "Apakah Anda yakin ingin menghapus?"
      [Batal] [Ya, Hapus!]

2. Konfirmasi "Ya"
    → AJAX DELETE /api/mahasiswa/{id}
    → MahasiswaController@destroy:
        a. Cari mahasiswa by id
        b. Delete (cascade ke user)
        c. Return 200
    → Success: reload table, notifikasi "Terhapus"
```

---

## 5. Alur Presensi (Absensi)

### Flow Diagram

```
┌──────────┐     ┌──────────┐     ┌──────────┐
│  Dosen   │     │ SesiKuliah│    │Mahasiswa │
│  (login) │────>│ (create) │<────│ (login)  │
└──────────┘     └────┬─────┘     └──────────┘
                      │               │
                      ▼               ▼
               ┌────────────┐  ┌──────────┐
               │ QR Code    │  │ Kode Unik│
               │ + Kode     │  │ (input)  │
               └─────┬──────┘  └────┬─────┘
                     │              │
                     ▼              ▼
               ┌────────────────────────┐
               │     Presensi (store)   │
               │  POST /api/presensi    │
               │  {sesi_id, metode,     │
               │   kode_unik atau qr}   │
               └───────────┬────────────┘
                           │
                           ▼
               ┌────────────────────────┐
               │ Validasi:              │
               │ 1. Sesi aktif?         │
               │ 2. Waktu sesuai?       │
               │ 3. Sudah absen?        │
               │ 4. Mahasiswa terdaftar?│
               └───────────┬────────────┘
                           │
               ┌───────────┴────────────┐
               │     Success / Error     │
               └────────────────────────┘
```

### Alur Lengkap

#### A. Dosen Membuat Sesi Kuliah

```
POST /api/sesi-kuliah
{
    mata_kuliah_id: 1,
    tanggal: "2026-07-12",
    jam_mulai: "08:00",
    jam_selesai: "10:00",
    topik: "Pertemuan 1 - Pengantar",
    gedung: "Gedung A",
    lantai: "2",
    ruangan: "201"
}

Response:
{
    sesi: { ... },
    qr_code: "data:image/png;base64,...",  // QR dari JSON encode sesi
    kode_unik: "AB12XY",                    // 6 karakter random
    kode_expires_at: "2026-07-12 10:00:00"  // sesuai jam_selesai
}
```

**Proses di backend** (`SesiKuliahController@store`):
1. Validasi input (tanggal, jam, dll)
2. Generate `qr_code` — QR dari JSON data sesi
3. Generate `kode_unik` — 6 karakter alfanumerik random
4. Set `is_active = true`, `kode_expires_at = jam_selesai`
5. Cek bentrok jadwal dosen (metode `cekBentrok`)
6. Simpan, return data + QR

#### B. Mahasiswa Melakukan Absensi

Mahasiswa bisa absen lewat 3 metode:

| Metode | Input | Endpoint |
|---|---|---|
| **QR Code** | Scan QR (via html5-qrcode) | `POST /api/presensi` |
| **Kode Unik** | Input 6 karakter | `POST /api/presensi` |
| **Manual** (dosen) | Pilih mahasiswa | `POST /api/presensi` |

```
POST /api/presensi
{
    sesi_id: 1,
    metode: "kode_unik|qr|manual",
    kode_unik: "AB12XY"       // hanya jika metode = kode_unik
    // atau qr_data: "..."     // hanya jika metode = qr
}
```

**Proses di backend** (`PresensiController@store`):

```
1. Cari sesi kuliah by sesi_id
    │
2. Cek: sesi->is_active === true?
    ├── Tidak → 422 "Sesi tidak aktif"
    └── Ya → lanjut
    │
3. Cek: waktu_absen dalam range [jam_mulai, jam_selesai]?
    ├── Tidak → 422 "Di luar waktu absen"
    └── Ya → lanjut
    │
4. Cek metode:
    ├── kode_unik: kode_unik === sesi->kode_unik?
    │   └── Tidak → 422 "Kode unik salah"
    ├── qr: validasi data QR (parsing)
    └── manual: (tidak ada validasi khusus, untuk dosen)
    │
5. Cek: sudah absen sebelumnya?
    ├── Ya → 422 "Sudah melakukan absen"
    └── Tidak → lanjut
    │
6. Cek: mahasiswa terdaftar di MK ini?
    ├── Tidak → 422 "Tidak terdaftar di mata kuliah ini"
    └── Ya → lanjut
    │
7. Simpan presensi
    ↓
Response 201: {presensi}
```

#### C. Laporan Presensi

```
GET /api/laporan/presensi?sesi_id=1

Response:
{
    "data": {
        "sesi": { ... },
        "summary": {
            "total_mahasiswa": 40,
            "hadir": 35,
            "izin": 2,
            "sakit": 1,
            "alpha": 2,
            "persentase_hadir": "87.50%"
        },
        "details": [
            { mahasiswa: "Andi", status: "hadir", waktu: "08:05:30" },
            { mahasiswa: "Budi", status: "alpha", waktu: null },
            ...
        ]
    }
}
```

---

## 6. Alur Jadwal Kuliah

### Struktur Data

```
MataKuliah (1) ──> KelasParalel (many) ──> JadwalMataKuliah (many)
                       │                          │
                       │                          ├── hari (enum)
                       │                          ├── jam_mulai
                       │                          ├── jam_selesai
                       │                          ├── gedung_id ──> Gedung
                       │                          └── ruangan_id ──> Ruangan
                       │
                       └── dosen_id ──> Dosen
```

### Alur Create Jadwal

```
Halaman: /jadwal
    │
    ├── DataTable: menampilkan semua jadwal
    │
    └── Modal "Tambah Jadwal":
        │
        ├── Select Mata Kuliah (dari GET /api/mata-kuliah)
        │   └── On change → load Kelas Paralel
        │
        ├── Select Kelas Paralel (dari GET /api/kelas-paralel?mata_kuliah_id=X)
        │
        ├── Select Hari (Senin-Minggu)
        ├── Input Jam Mulai & Jam Selesai (time)
        ├── Select Gedung (dari GET /api/gedung)
        │   └── On change → load Ruangan
        └── Select Ruangan (dari GET /api/ruangan?gedung_id=X)

Submit → POST /api/jadwal-mata-kuliah
{
    kelas_paralel_id: 1,
    hari: "Senin",
    jam_mulai: "08:00",
    jam_selesai: "10:00",
    ruangan_id: 2
}
```

**Proses di backend** (`JadwalMataKuliahController@store`):

```
1. Validasi input
    │
2. Derive mata_kuliah_id dari kelas_paralel_id
   (KelasParalel::find()->mata_kuliah_id)
    │
3. Derive gedung_id dari ruangan_id
   (Ruangan::find()->gedung_id)
    │
4. Cek Bentrok (private method cekBentrok):
    │
    ├── a. Cek ruangan:
    │       Same ruangan + same hari + time overlap?
    │       → "Ruangan sudah digunakan"
    │
    ├── b. Cek dosen:
    │       Same dosen (dari MK atau kelasParalel) + same hari + overlap?
    │       → "Dosen sudah memiliki jadwal"
    │
    └── c. Cek kelas paralel:
    │       Same kelas_paralel_id + same hari + overlap?
    │       → "Kelas paralel sudah memiliki jadwal"
    │
5. Jika ada bentrok → return 422 dengan daftar error
   Jika tidak → create, return 201
```

### "Sudah Terjadwal" Label

Saat menambah jadwal:

- **Dropdown Mata Kuliah**: jika semua kelas paralel dari MK sudah punya jadwal, tampilkan `"MK Nama (Sudah Terjadwal)"` dan **disable**
- **Dropdown Kelas Paralel**: jika kelas sudah punya jadwal, tampilkan `"Kelas A (Sudah Terjadwal)"` dan **disable**

Logika backend ada di model accessor:

```php
// MataKuliah@getSudahTerjadwalAttribute
total_kelas == terjadwal_kelas  // semua kelas sudah terjadwal

// KelasParalel@getSudahTerjadwalAttribute
jadwal_count > 0  // sudah punya jadwal
```

---

## 7. Alur Notifikasi Telegram

### Arsitektur

```
┌─────────────┐       ┌──────────────────┐       ┌─────────────┐
│  Aplikasi   │       │  TelegramService  │       │  Telegram   │
│  (Laravel)  │──────>│  (app/Services/)  │──────>│  Bot API    │
│             │       │                   │       │             │
│  Command    │       │  sendMessage()    │       │  @bot       │
│  Controller │       │  setWebhook()     │       │             │
│  Webhook    │       │  getUpdates()     │       │  ┌───────┐  │
└─────────────┘       └──────────────────┘       │  │ Dosen │  │
                                                  │  ├───────┤  │
                                                  │  │Mhs    │  │
                                                  │  └───────┘  │
                                                  └─────────────┘
```

### Alur Link Akun

```
[User di Browser]                    [Telegram Bot]              [Server]
      │                                    │                        │
      │── Klik "Hubungkan Telegram" -------│                        │
      │   (di halaman Profile)             │                        │
      │                                    │                        │
      │── POST /api/telegram/link ──────────────────────────────────>│
      │                                    │                        │
      │<── Response: {link} ──────────────│                        │
      │   "https://t.me/bot?start=UUID"   │                        │
      │                                    │                        │
      │── Klik link ──────────────────────>│                        │
      │                                    │── /start UUID          │
      │                                    │                        │
      │                                    │── POST /api/telegram/  │
      │                                    │    webhook ───────────>│
      │                                    │   {message.text:       │
      │                                    │    "/start UUID",      │
      │                                    │    chat.id: 123456}    │
      │                                    │                        │
      │                                    │<── "Akun berhasil     │
      │                                    │     terhubung!" ──────│
      │                                    │                        │
      │<── Profile: "Terhubung" ──────────│                        │
```

### Alur Notifikasi Jadwal Harian

```
[Server]                          [Telegram API]          [User]
    │                                    │                  │
    │  Setiap hari pukul 06:00 WIB       │                  │
    │  (schedule:send-notification)       │                  │
    │                                    │                  │
    ├── Cari jadwal hari ini             │                  │
    │   (JadwalMataKuliah::where('hari',  │                  │
    │    $hariIndonesia)->get())          │                  │
    │                                    │                  │
    ├── Untuk setiap jadwal:             │                  │
    │   │                                │                  │
    │   ├── Cari dosen dari MK           │                  │
    │   │   (mataKuliah.dosen.user)      │                  │
    │   │                                │                  │
    │   ├── Kirim ke dosen:              │                  │
    │   │   "Selamat pagi, Bapak/Ibu!    │                  │
    │   │    📋 Jadwal Mengajar Hari Ini  │                  │
    │   │    📚 Algoritma (TI101)        │                  │
    │   │    🕐 08:00 - 10:00            │                  │
    │   │    🏢 Gedung A - Lt.2 - 201    │                  │
    │   │    👥 40 mahasiswa"            │                  │
    │   │                                │                  │
    │   │── POST sendMessage ───────────>│── Notif ───────>│ Dosen
    │   │                                │                  │
    │   ├── Cari mahasiswa dari kelas    │                  │
    │   │   (kelasParalel.mahasiswa.user)│                  │
    │   │                                │                  │
    │   └── Kirim ke tiap mahasiswa:     │                  │
    │       "Selamat pagi! ☀️             │                  │
    │        📋 Jadwal Kuliah Hari Ini    │                  │
    │        📚 Algoritma - Kelas A      │                  │
    │        🕐 08:00 - 10:00            │                  │
    │        🏢 Gedung A - Lt.2 - 201    │                  │
    │        👨‍🏫 Pak Budi"               │                  │
    │                                    │                  │
    │       │── POST sendMessage ───────>│── Notif ───────>│ Mhs
    │                                    │                  │
    └── Selesai: "Terkirim: 42, Gagal: 0"                 │
```

### Testing Tool (Halaman Web)

Tersedia di `/telegram-test` (hanya admin):

| Fitur | Deskripsi |
|---|---|
| **Status Bot** | Cek token valid, bot username, webhook |
| **User Terhubung** | Tabel user dengan Telegram + tombol "Kirim Pesan" |
| **Kirim Pesan Kustom** | Pilih user + tulis pesan sendiri |
| **Kirim Simulasi Jadwal** | Kirim format notifikasi jadwal hari ini ke user tertentu |

### Artisan Commands

| Command | Fungsi |
|---|---|
| `php artisan schedule:send-notification` | Kirim notifikasi jadwal hari ini |
| `php artisan schedule:test-notification` | Test token + kirim ke admin |
| `php artisan schedule:test-notification --startup` | Kirim notifikasi "Server Aktif" |
| `php artisan telegram:set-webhook {url?}` | Set webhook bot |
| `php artisan telegram:poll` | Poll update (alternatif webhook utk dev lokal) |

### Dev Server Starter

```powershell
.\dev.ps1
```

Script ini akan:
1. Kirim notifikasi startup ke admin via Telegram ✅
2. Jalankan `schedule:work` di background ✅
3. Jalankan `php artisan serve` di terminal utama ✅

---

## 8. Entity Relationship Diagram

### Relasi Antar Model (Text ERD)

```
┌─────────┐        ┌───────────┐
│  User   │        │  Jurusan  │
├─────────┤        ├───────────┤
│ id      │        │ id        │
│ nama    │        │ kode      │
│ email   │        │ nama      │
│ password│        └───────────┘
│ role    │              │
│ tel_chat│              │
│ tel_tok │              │
└────┬────┘              │
     │                   │
     │ 1                 │ 1
     │                   │
   ┌─┴──────┐          ┌┴───────────┐        ┌───────────────┐
   │ Dosen  │          │ Mahasiswa  │        │  MataKuliah   │
   ├────────┤          ├────────────┤        ├───────────────┤
   │ id     │          │ id         │        │ id            │
   │user_id │◄────────►│ user_id    │        │ jurusan_id ───┤
   │jurusan │───┐      │ jurusan_id─┤        │ kode          │
   │ nip    │   │      │ nim        │        │ nama          │
   │jabatan │   │      │ angkatan   │        │ sks           │
   └────────┘   │      └─────┬──────┘        │ semester      │
                │            │               │ dosen_id ─────┤
                │            │               └────┬──────────┘
                │            │                    │
                │            │ ┌──────────────────┤
                │            │ │  ┌───────────────┘
                ▼            ▼ ▼  ▼
           ┌──────────────────────────┐
           │      Enrollment          │
           ├──────────────────────────┤
           │ id                      │
           │ mahasiswa_id            │
           │ mata_kuliah_id          │
           │ kelas_paralel_id ───────┤
           │ tahun_ajaran            │
           └──────────────────────────┘

┌─────────────┐    ┌──────────────┐    ┌───────────┐
│ KelasParalel│    │JadwalMataKul.│    │  Gedung   │
├─────────────┤    ├──────────────┤    ├───────────┤
│ id          │    │ id           │    │ id        │
│mata_kuliah─►│    │mata_kuliah_id│    │ kode      │
│ nama_kelas  │    │kelas_paral_id│    │ nama      │
│ dosen_id    │    │ hari         │    │ lokasi    │
│thn_ajaran   │    │ jam_mulai    │    └─────┬─────┘
└──────┬──────┘    │ jam_selesai  │          │
       │           │ gedung_id ───┼──────────┤
       └──────────►│ ruangan_id ──┼──────┐   │
                   └──────────────┘      │   │
                                         ▼   ▼
                                   ┌──────────┐
                                   │ Ruangan  │
                                   ├──────────┤
                                   │ id       │
                                   │gedung_id─┤
                                   │ nama     │
                                   │ lantai   │
                                   │kapasitas │
                                   └──────────┘

┌─────────────┐       ┌──────────────┐
│ SesiKuliah  │       │  Presensi    │
├─────────────┤       ├──────────────┤
│ id          │       │ id           │
│mata_kuliah─►│       │ sesi_id ─────┤
│ tanggal     │       │ mahasiswa_id │
│ jam_mulai   │       │ waktu_absen  │
│ jam_selesai │       │ metode       │
│ topik       │       │ status       │
│ qr_code     │       │ keterangan   │
│ kode_unik   │       └──────────────┘
│ is_active   │
└─────────────┘
```

---

## 9. Role-Based Access Control

### Tiga Role

| Role | Redirect Setelah Login | Akses |
|---|---|---|
| **admin** | `/mahasiswa` | Semua fitur CRUD |
| **dosen** | `/dashboard-dosen` | Lihat jadwal sendiri, kelola sesi, presensi |
| **mahasiswa** | `/dashboard-mahasiswa` | Lihat jadwal sendiri, absensi |

### Implementasi

#### Client-Side (di layout)

```javascript
// Sidebar navigation di-render berdasarkan role
const links = {
    admin: [
        'Mahasiswa', 'Dosen', 'Jurusan', 'Gedung', 'Ruangan',
        'Jadwal Kuliah', 'Mata Kuliah', 'Laporan', 'Telegram Test', 'Profil Saya'
    ],
    dosen: ['Dashboard', 'Jadwal Kuliah', 'Mata Kuliah', 'Profil Saya'],
    mahasiswa: ['Beranda', 'Jadwal Kuliah', 'Mata Kuliah', 'Profil Saya']
};
```

Tombol Add/Edit/Delete hanya muncul untuk admin:
```javascript
if (JSON.parse(localStorage.getItem('user'))?.role === 'admin') {
    $('#btn-add-data').removeClass('hidden');
}
```

Kolom Aksi di DataTable hanya visible untuk admin:
```javascript
{ visible: user.role === 'admin', ... }
```

#### Server-Side (di API Controllers)

```php
// Contoh: role check di controller
if ($request->user()->role !== 'admin') {
    return $this->error('Anda tidak memiliki izin', 403);
}

// Contoh: auto-filter data
if ($user->role === 'dosen') {
    $query->where('dosen_id', $user->dosen->id);
} elseif ($user->role === 'mahasiswa') {
    $query->whereHas('mahasiswa', function ($q) use ($user) {
        $q->where('mahasiswa.id', $user->mahasiswa->id);
    });
}
```

---

## 10. Developer Guide

### Persyaratan

- PHP 8.3+
- Composer
- Node.js (untuk build frontend)
- SQLite (default) atau MySQL

### Instalasi

```bash
# 1. Clone & masuk direktori
cd AbsensiMahasiswa

# 2. Install dependencies PHP
composer install

# 3. Copy .env
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Jalankan migrasi + seeder
php artisan migrate --seed

# 6. Install Node dependencies (opsional)
npm install
npm run build
```

### Menjalankan (Cara 1 — Manual)

```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Scheduler (untuk notifikasi otomatis)
php artisan schedule:work
```

### Menjalankan (Cara 2 — Dev Starter)

```powershell
.\dev.ps1
```
Satu perintah → notifikasi startup + scheduler background + serve.

### Menjalankan (Cara 3 — Laragon)

1. Buka Laragon → `Start All`
2. Buka terminal Laragon (`Menu > Terminal`)
3. Jalankan scheduler:
   ```bash
   cd C:/laragon/www/AbsensiMahasiswa
   php artisan schedule:work
   ```
4. Buka `http://absensimahasiswa.test` (atau sesuai hostname)

### Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| Dosen | `dosen1@example.com` | `password` |
| Dosen | `dosen2@example.com` | `password` |
| Mahasiswa | `andi@example.com` | `password` |

### Telegram Setup

```bash
# 1. Dapatkan token dari @BotFather di Telegram
# 2. Isi .env:
TELEGRAM_BOT_TOKEN=8925925484:AAHRUsVAsL...
TELEGRAM_BOT_USERNAME=absensi_notif_bot
TELEGRAM_ADMIN_CHAT_ID=5604047850

# 3. Set webhook (production)
php artisan telegram:set-webhook

# 4. Test koneksi
php artisan schedule:test-notification

# 5. Kirim notifikasi jadwal hari ini
php artisan schedule:send-notification
```

### Testing Telegram (Web)

Buka `/telegram-test` setelah login sebagai admin:
- Cek status bot
- Lihat user yang terhubung
- Kirim pesan kustom
- Kirim simulasi jadwal

### Struktur Database

```bash
# Melihat semua tabel
php artisan db:show

# Melihat schema tabel tertentu
php artisan db:table --name=jadwal_mata_kuliah
```

---

*Dokumentasi ini diperbarui: Juli 2026*
