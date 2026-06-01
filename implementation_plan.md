# Rencana Implementasi (Final)

## Perubahan Utama

### Alur Baru yang Diinginkan:
1. **Admin** menentukan jadwal tetap per mata kuliah: hari, jam mulai/selesai, gedung, lantai, ruangan
2. **Dosen** saat buat sesi → pilih jadwal yang sudah ditentukan admin → gedung/lantai/ruangan otomatis terisi
3. **Validasi**: tidak boleh ada bentrok ruangan di waktu yang sama
4. **Sidebar** menggantikan navbar untuk semua role

---

## File yang Akan Dibuat/Diubah

### [NEW] Migration: create_jadwal_mata_kuliah_table
Tabel baru `jadwal_mata_kuliah`:
- id, mata_kuliah_id (FK), hari (enum), jam_mulai, jam_selesai, gedung, lantai, ruangan, timestamps

### [NEW] Model: JadwalMataKuliah.php
### [NEW] Controller: JadwalMataKuliahController.php (CRUD API)
### [MODIFY] routes/api.php — tambah route jadwal-mata-kuliah
### [MODIFY] app/Models/MataKuliah.php — tambah relasi hasMany JadwalMataKuliah
### [MODIFY] resources/views/mata_kuliah/index.blade.php — Admin kelola jadwal per matkul (modal baru)
### [MODIFY] resources/views/dashboard/dosen.blade.php — pilih jadwal → auto-fill lokasi, tampilkan error validasi
### [MODIFY] resources/views/layouts/app.blade.php — Navbar → Sidebar responsif

---

## Verification Plan
- Jalankan migration
- Admin: buka mata kuliah → tambah jadwal → cek tabel jadwal
- Dosen: buat sesi → pilih jadwal → cek auto-fill → submit bentrok → cek error tampil
- Cek sidebar desktop & mobile
