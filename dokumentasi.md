# Dokumentasi Projek: Sistem Presensi Mahasiswa (PresensiApp)

Projek ini adalah aplikasi manajemen absensi mahasiswa berbasis web yang dikembangkan menggunakan **Laravel** dengan pendekatan **Hybrid (Blade + API)**. Aplikasi ini dirancang untuk memudahkan proses absensi menggunakan QR Code dan Kode Unik secara real-time.

---

## 1. Teknologi yang Digunakan

*   **Backend**: Laravel 11 (PHP 8.2+)
*   **Database**: MySQL / MariaDB
*   **Frontend**: 
    *   **Blade Templating**: Untuk struktur halaman.
    *   **Tailwind CSS**: Untuk styling (via Play CDN).
    *   **jQuery & AJAX**: Untuk interaksi data tanpa reload halaman.
    *   **DataTables**: Untuk manajemen tabel yang interaktif.
    *   **SweetAlert2**: Untuk notifikasi dan konfirmasi modern.
*   **Library Khusus**:
    *   **QRCode.js**: Untuk generate QR Code di sisi Dosen.
    *   **Html5-Qrcode**: Untuk scan QR Code menggunakan kamera di sisi Mahasiswa.

---

## 2. Struktur Database (Model & Relasi)

Aplikasi ini memiliki beberapa entitas utama dengan relasi sebagai berikut:

1.  **User**: Menyimpan data login (email, password) dan role (`admin`, `dosen`, `mahasiswa`).
2.  **Jurusan**: Data program studi (Contoh: Teknik Informatika).
3.  **Dosen**: Profil dosen, terhubung ke satu `User` dan satu `Jurusan`.
4.  **Mahasiswa**: Profil mahasiswa, terhubung ke satu `User` dan satu `Jurusan`.
5.  **Mata Kuliah**: Data mata kuliah, terhubung ke satu `Jurusan` dan diampu oleh satu `Dosen`.
6.  **Enrollment**: Tabel penghubung antara `Mahasiswa` dan `Mata Kuliah` (Many-to-Many).
7.  **Sesi Kuliah**: Pertemuan per kelas yang dibuat oleh Dosen (berisi topik, tanggal, dan kode unik).
8.  **Presensi**: Catatan kehadiran mahasiswa pada suatu `Sesi Kuliah`.

---

## 3. Fitur Utama Berdasarkan Role

### A. Admin
*   **Dashboard**: Statistik ringkas jumlah data.
*   **Manajemen Jurusan**: CRUD data program studi.
*   **Manajemen Dosen**: CRUD data dosen (sekaligus membuat akun User).
*   **Manajemen Mahasiswa**: CRUD data mahasiswa (sekaligus membuat akun User).
*   **Manajemen Mata Kuliah**: CRUD data mata kuliah dan penentuan dosen pengampu.
*   **Manajemen Enrollment**: Mendaftarkan mahasiswa ke mata kuliah tertentu secara manual atau otomatis (berdasarkan Jurusan).

### B. Dosen
*   **Manajemen Sesi**: Membuat sesi absensi baru untuk mata kuliah yang diampu.
*   **Generate QR & Kode**: Menampilkan QR Code dan Kode Unik 6 digit untuk mahasiswa.
*   **Monitoring Kehadiran**: Melihat daftar mahasiswa yang terdaftar di kelas dan status kehadirannya secara real-time.
*   **Kontrol Sesi**: Mengaktifkan atau menutup sesi absensi secara manual.

### C. Mahasiswa
*   **Input Absensi**: Mengisi kehadiran dengan mengetik Kode Unik atau melakukan Scan QR.
*   **Sesi Aktif**: Melihat daftar mata kuliah yang sedang membuka absensi secara real-time di dashboard.
*   **Auto-Fill**: Fitur klik pada kode unik di dashboard untuk otomatis mengisi form absensi.
*   **Riwayat**: Melihat catatan kehadiran pribadi pada sesi-sesi sebelumnya.

---

## 4. Alur Kerja Aplikasi (Application Flow)

### Autentikasi
1.  User login melalui halaman login.
2.  Setelah berhasil, API akan mengembalikan data User dan **Token** (disimpan di `localStorage`).
3.  Setiap request ke API akan menyertakan token ini dalam Header Authorization.

### Proses Absensi
1.  **Dosen** membuka sesi perkuliahan di dashboard-nya.
2.  Sistem men-generate `kode_unik` dan `qr_code`.
3.  **Mahasiswa** melihat sesi aktif di dashboard mereka.
4.  Mahasiswa memasukkan kode (atau klik kode tersebut) dan klik "Absen Sekarang".
5.  Sistem memvalidasi:
    *   Apakah sesi masih aktif?
    *   Apakah kode benar?
    *   Apakah mahasiswa terdaftar di mata kuliah tersebut?
    *   Apakah mahasiswa sudah absen sebelumnya?
6.  Jika valid, data disimpan ke tabel `presensi`.

---

## 5. Struktur Folder Penting

*   `app/Models/`: Definisi skema database dan relasi Eloquent.
*   `app/Http/Controllers/Api/`: Logika utama aplikasi (CRUD, validasi, filter).
*   `routes/api.php`: Definisi endpoint API.
*   `routes/web.php`: Definisi route halaman (view).
*   `resources/views/layouts/app.blade.php`: Layout utama, navigasi dinamis, dan setup global AJAX.
*   `resources/views/dashboard/`: Halaman khusus role Dosen dan Mahasiswa.
*   `resources/views/[entitas]/index.blade.php`: Halaman manajemen data (Admin).

---

## 6. Cara Mempelajari Kode Ini

1.  **Pelajari Layout Utama**: Buka `app.blade.php` untuk melihat bagaimana role menentukan menu navigasi dan bagaimana token ditangani.
2.  **Pelajari CRUD Admin**: Buka `jurusan/index.blade.php` sebagai contoh CRUD paling sederhana yang menggunakan AJAX.
3.  **Pelajari Logika API**: Lihat `SesiKuliahController.php` untuk melihat bagaimana filter, include relasi, dan validasi dilakukan di Laravel.
4.  **Pelajari Interaksi Mahasiswa**: Lihat `dashboard/mahasiswa.blade.php` untuk memahami alur absensi dan penggunaan library QR.

---
Dokumentasi ini dibuat untuk membantu pengembangan dan pemeliharaan sistem PresensiApp.
