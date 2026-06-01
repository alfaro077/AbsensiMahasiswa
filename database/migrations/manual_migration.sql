-- =============================================
-- Manual Migration: Create Gedung Table
-- Run this if 'php artisan migrate' fails
-- =============================================

CREATE TABLE IF NOT EXISTS gedung (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    kode varchar(20) NOT NULL,
    
ama varchar(100) NOT NULL,
    lokasi varchar(255) DEFAULT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY gedung_kode_unique (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Alter Jadwal Mata Kuliah: Add gedung_id
-- =============================================

ALTER TABLE jadwal_mata_kuliah 
    DROP COLUMN gedung,
    ADD COLUMN gedung_id bigint unsigned NOT NULL AFTER jam_selesai,
    ADD CONSTRAINT jadwal_mata_kuliah_gedung_id_foreign 
        FOREIGN KEY (gedung_id) REFERENCES gedung (id) ON DELETE CASCADE;
