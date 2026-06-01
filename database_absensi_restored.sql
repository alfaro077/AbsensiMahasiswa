-- Database SQL Dump for AbsensiMahasiswa
-- Reconstructed from Laravel Models

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dosen','mahasiswa') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Administrator', 'admin@example.com', '$2y$12$lRlze4GmXV4/LtK7Im96c.twEhYGLhDsJepHFQDH0M/x6I6NBnaOq', 'admin'),
(2, 'Budi Susanto, M.Kom', 'budi@dosen.example.com', '$2y$12$lRlze4GmXV4/LtK7Im96c.twEhYGLhDsJepHFQDH0M/x6I6NBnaOq', 'dosen'),
(3, 'Siti Aminah, Ph.D.', 'siti@dosen.example.com', '$2y$12$lRlze4GmXV4/LtK7Im96c.twEhYGLhDsJepHFQDH0M/x6I6NBnaOq', 'dosen'),
(4, 'Andi Wijaya', 'andi@mhs.example.com', '$2y$12$lRlze4GmXV4/LtK7Im96c.twEhYGLhDsJepHFQDH0M/x6I6NBnaOq', 'mahasiswa'),
(5, 'Rina Melati', 'rina@mhs.example.com', '$2y$12$lRlze4GmXV4/LtK7Im96c.twEhYGLhDsJepHFQDH0M/x6I6NBnaOq', 'mahasiswa');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--
CREATE TABLE `jurusan` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jurusan_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jurusan` (`id`, `kode`, `nama`) VALUES
(1, 'TI', 'Teknik Informatika'),
(2, 'SI', 'Sistem Informasi'),
(3, 'TE', 'Teknik Elektro'),
(4, 'MI', 'Manajemen Informatika');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `jurusan_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(20) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dosen_nip_unique` (`nip`),
  KEY `dosen_user_id_foreign` (`user_id`),
  KEY `dosen_jurusan_id_foreign` (`jurusan_id`),
  CONSTRAINT `dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dosen_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `user_id`, `jurusan_id`, `nip`, `jabatan`) VALUES
(1, 2, 1, '198001012010011001', 'Lektor'),
(2, 3, 2, '198505052015042002', 'Asisten Ahli');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `jurusan_id` bigint(20) UNSIGNED NOT NULL,
  `nim` varchar(20) NOT NULL,
  `angkatan` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mahasiswa_nim_unique` (`nim`),
  KEY `mahasiswa_user_id_foreign` (`user_id`),
  KEY `mahasiswa_jurusan_id_foreign` (`jurusan_id`),
  CONSTRAINT `mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mahasiswa_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `user_id`, `jurusan_id`, `nim`, `angkatan`) VALUES
(1, 4, 1, '20230001', 2023),
(2, 5, 2, '20230002', 2023);

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jurusan_id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `dosen_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mata_kuliah_kode_unique` (`kode`),
  KEY `mata_kuliah_dosen_id_foreign` (`dosen_id`),
  KEY `mata_kuliah_jurusan_id_foreign` (`jurusan_id`),
  CONSTRAINT `mata_kuliah_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mata_kuliah_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`id`, `jurusan_id`, `kode`, `nama`, `sks`, `semester`, `dosen_id`) VALUES
(1, 1, 'TI101', 'Algoritma dan Pemrograman I', 3, 1, 1),
(2, 1, 'TI102', 'Matematika Diskrit', 3, 1, 1),
(3, 1, 'TI103', 'Sistem Digital', 3, 1, 1),
(4, 1, 'TI104', 'Struktur Data', 3, 2, 1),
(5, 1, 'TI105', 'Basis Data', 4, 2, 1),
(6, 1, 'TI106', 'Pemrograman Web', 3, 3, 1),
(7, 1, 'TI107', 'Rekayasa Perangkat Lunak', 3, 3, 1),
(8, 1, 'TI108', 'Jaringan Komputer', 3, 4, 1),
(9, 1, 'TI109', 'Sistem Operasi', 3, 4, 1),
(10, 1, 'TI110', 'Kecerdasan Buatan', 3, 5, 1),

(11, 2, 'SI101', 'Pengantar Sistem Informasi', 3, 1, 2),
(12, 2, 'SI102', 'Algoritma dan Logika', 3, 1, 2),
(13, 2, 'SI103', 'Matematika Bisnis', 3, 1, 2),
(14, 2, 'SI104', 'Sistem Basis Data', 4, 2, 2),
(15, 2, 'SI105', 'Analisis Proses Bisnis', 3, 2, 2),
(16, 2, 'SI106', 'Desain UI/UX', 3, 3, 2),
(17, 2, 'SI107', 'E-Business', 3, 3, 2),
(18, 2, 'SI108', 'Analisis dan Perancangan SI', 3, 4, 2),
(19, 2, 'SI109', 'Manajemen Proyek SI', 3, 4, 2),
(20, 2, 'SI110', 'Audit Sistem Informasi', 3, 5, 2),

(21, 3, 'TE101', 'Fisika Dasar', 3, 1, 1),
(22, 3, 'TE102', 'Kalkulus I', 3, 1, 1),
(23, 3, 'TE103', 'Rangkaian Elektrik I', 3, 1, 1),
(24, 3, 'TE104', 'Rangkaian Elektrik II', 3, 2, 1),
(25, 3, 'TE105', 'Dasar Elektronika', 3, 2, 1),
(26, 3, 'TE106', 'Sinyal dan Sistem', 3, 3, 1),
(27, 3, 'TE107', 'Elektromagnetika', 3, 3, 1),
(28, 3, 'TE108', 'Sistem Kontrol', 3, 4, 1),
(29, 3, 'TE109', 'Mikrokontroler', 3, 4, 1),
(30, 3, 'TE110', 'Pengolahan Sinyal Digital', 3, 5, 1),

(31, 4, 'MI101', 'Pengantar Manajemen', 3, 1, 2),
(32, 4, 'MI102', 'Pengantar Teknologi Informasi', 3, 1, 2),
(33, 4, 'MI103', 'Praktikum Aplikasi Perkantoran', 3, 1, 2),
(34, 4, 'MI104', 'Basis Data Terapan', 4, 2, 2),
(35, 4, 'MI105', 'Pemrograman Visual', 3, 2, 2),
(36, 4, 'MI106', 'Jaringan Komputer Dasar', 3, 3, 2),
(37, 4, 'MI107', 'Pengembangan Aplikasi Web', 3, 3, 2),
(38, 4, 'MI108', 'Sistem Informasi Manajemen', 3, 4, 2),
(39, 4, 'MI109', 'E-Commerce', 3, 4, 2),
(40, 4, 'MI110', 'Etika Profesi', 3, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` bigint(20) UNSIGNED NOT NULL,
  `mata_kuliah_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `enrollment_mahasiswa_id_foreign` (`mahasiswa_id`),
  KEY `enrollment_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  CONSTRAINT `enrollment_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollment_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sesi_kuliah`
--

CREATE TABLE `sesi_kuliah` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mata_kuliah_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `topik` text DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `kode_unik` varchar(50) DEFAULT NULL,
  `kode_expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `sesi_kuliah_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  CONSTRAINT `sesi_kuliah_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sesi_id` bigint(20) UNSIGNED NOT NULL,
  `mahasiswa_id` bigint(20) UNSIGNED NOT NULL,
  `waktu_absen` datetime NOT NULL,
  `metode` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `presensi_sesi_id_foreign` (`sesi_id`),
  KEY `presensi_mahasiswa_id_foreign` (`mahasiswa_id`),
  CONSTRAINT `presensi_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensi_sesi_id_foreign` FOREIGN KEY (`sesi_id`) REFERENCES `sesi_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
