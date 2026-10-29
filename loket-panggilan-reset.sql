-- =====================================================================
-- RESET FITUR LOKET PANGGILAN (versi DROP IF EXISTS)
-- ---------------------------------------------------------------------
-- Tanggal : 28 September 2026
-- Untuk   : MySQL / MariaDB cPanel (aman di 5.7 sampai 8.0)
--
-- APA YANG DI-RESET (6 tabel, berurutan aman dari foreign key):
--   1. antrians  - tiket antrian HARI INI ikut terhapus ( kolom kosong )
--   2. users     - akun petugas LOKET PANGGILAN ikut terhapus
--   3. lokets    - daftar loket fisik
--   4. polis     - unit pelayanan
--   5. klasters  - klaster + warna
--   6. settings  - setting kiosk & display
--
-- TIDAK disentuh: migrations, cache, cache_locks, jobs,
--                 job_batches, failed_jobs, password_reset_tokens,
--                 sessions  -> tetap jalan di server.
--
-- CARA PAKAI:
--   phpMyAdmin > pilih database > tab "SQL" > paste semua > Go
--   Setelah itu jalankan: php artisan optimize:clear
--
-- PERINGATAN:
--   - JANGAN jalankan di server produksi yang sedang ada antrian
--     berjalan. Data `antrians` dan akun `users` hilang permanen.
--   - Ganti password kedua akun di bawah sebelum dipakai di publik.
--   - Password default kedua akun ini: admin123
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- LANGKAH 1 - Buang tabel lama (urutan anak -> induk)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS antrians;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS lokets;
DROP TABLE IF EXISTS polis;
DROP TABLE IF EXISTS klasters;
DROP TABLE IF EXISTS settings;

-- ---------------------------------------------------------------------
-- LANGKAH 2 - Buat ulang struktur
-- ---------------------------------------------------------------------
CREATE TABLE klasters (
    id INT NOT NULL AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    warna VARCHAR(50) NOT NULL DEFAULT '#0d6efd',
    urutan INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE polis (
    id INT NOT NULL AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    prefix VARCHAR(10) NOT NULL,
    warna VARCHAR(50) NOT NULL DEFAULT '#0d6efd',
    deskripsi TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    klaster_id INT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY polis_prefix_unique (prefix),
    KEY fk_polis_klasters (klaster_id),
    CONSTRAINT fk_polis_klasters FOREIGN KEY (klaster_id) REFERENCES klasters (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lokets (
    id INT NOT NULL AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    poli_id INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY fk_lokets_polis (poli_id),
    CONSTRAINT fk_lokets_polis FOREIGN KEY (poli_id) REFERENCES polis (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE antrians (
    id INT NOT NULL AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    poli_id INT NOT NULL,
    nomor INT NOT NULL,
    no_antrian VARCHAR(50) NOT NULL,
    nama_pasien VARCHAR(255) NULL,
    status ENUM('waiting', 'calling', 'done', 'skipped') NOT NULL DEFAULT 'waiting',
    loket_id INT NULL,
    called_at DATETIME NULL DEFAULT NULL,
    finished_at DATETIME NULL DEFAULT NULL,
    skipped_at DATETIME NULL DEFAULT NULL,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY antrians_tanggal_poli_id_nomor_unique (tanggal, poli_id, nomor),
    KEY fk_antrians_polis (poli_id),
    KEY fk_antrians_lokets (loket_id),
    CONSTRAINT fk_antrians_polis FOREIGN KEY (poli_id) REFERENCES polis (id) ON DELETE CASCADE,
    CONSTRAINT fk_antrians_lokets FOREIGN KEY (loket_id) REFERENCES lokets (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('admin', 'loket') NOT NULL DEFAULT 'loket',
    email_verified_at DATETIME NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    loket_id INT NULL,
    api_token VARCHAR(255) NULL,
    all_clusters TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email),
    UNIQUE KEY users_api_token_unique (api_token),
    KEY fk_users_lokets (loket_id),
    CONSTRAINT fk_users_lokets FOREIGN KEY (loket_id) REFERENCES lokets (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    id INT NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(255) NOT NULL,
    value TEXT NULL,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY settings_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- LANGKAH 3 - Isi klaster
-- ---------------------------------------------------------------------
-- `urutan` sengaja diisi 1..5 (bukan 0 semua) karena Loket, Display
-- dan Admin mengurutkan klaster dengan `orderBy('urutan')`.
INSERT INTO klasters (id, nama, warna, urutan, is_active, created_at, updated_at) VALUES
(1, 'Klaster 1 (Administrasi)',                    '#0d6efd', 1, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2, 'Klaster 2 (KIA, Remaja, Imunisasi)',          '#d63384', 2, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(3, 'Klaster 3 (Usia Dewasa dan Lansia)',          '#198754', 3, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(4, 'Klaster 4 (Penyakit Menular)',               '#fd7e14', 4, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(5, 'Lintas Klaster (Gimul, Apotik, IGD, Lab)',    '#6f42c1', 5, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP());

-- ---------------------------------------------------------------------
-- LANGKAH 4 - Isi unit pelayanan
-- ---------------------------------------------------------------------
INSERT INTO polis (id, klaster_id, nama, prefix, warna, deskripsi, is_active, created_at, updated_at) VALUES
(1,  1, 'Pendaftaran',   'A', '#0d6efd', 'Loket administrasi & pendaftaran pasien', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2,  1, 'Administrasi',  'B', '#3d8bfd', 'Pelayanan administrasi lainnya',             1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(3,  2, 'KIA',           'C', '#d63384', 'Kesehatan Ibu dan Anak',                     1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(4,  2, 'Remaja',        'D', '#e86aa1', 'Pelayanan kesehatan remaja',                1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(5,  2, 'Imunisasi',     'E', '#f0a0c6', 'Pelayanan imunisasi',                       1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(6,  3, 'Umum Dewasa',   'F', '#198754', 'Pelayanan penyakit umum dewasa',            1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(7,  3, 'Lansia',        'G', '#41b57b', 'Pelayanan kesehatan lansia',                1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(8,  4, 'TB & Paru',     'H', '#fd7e14', 'Pelayanan Tuberkulosis (TB)',                1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(9,  4, 'DBD & Demam',   'I', '#fea84e', 'Pelayanan Demam Berdarah & demam',           1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(10, 5, 'Gimul',         'J', '#6f42c1', 'Gigi dan Mulut',                            1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(11, 5, 'Apotik',        'K', '#8a63d2', 'Pengambilan obat',                          1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(12, 5, 'IGD',           'L', '#a98edc', 'Instalasi Gawat Darurat',                   1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(13, 5, 'Laboratorium',  'M', '#c2afe8', 'Pelayanan laboratorium',                     1, UTC_TIMESTAMP(), UTC_TIMESTAMP());

-- ---------------------------------------------------------------------
-- LANGKAH 5 - Isi LOKET PANGGILAN
-- ---------------------------------------------------------------------
-- PENTING: 1 baris = 1 loket fisik. `poli_id` WAJIB diisi.
-- LoketController hanya memproses loket dengan
-- is_active = 1 DAN poli_id IS NOT NULL.
-- Kalau ada poli aktif tanpa baris di bawah, tiketnya cetak di kiosk
-- tapi tidak pernah muncul di Loket Panggilan / Display.
INSERT INTO lokets (id, nama, poli_id, is_active, created_at, updated_at) VALUES
(1,  'Loket 1  - Pendaftaran',   1,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2,  'Loket 2  - KIA',            3,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(3,  'Loket 3  - Umum Dewasa',    6,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(4,  'Loket 4  - IGD',            12, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(5,  'Loket 5  - Laboratorium',   13, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(6,  'Loket 6  - Administrasi',   2,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(7,  'Loket 7  - Remaja',         4,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(8,  'Loket 8  - Imunisasi',      5,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(9,  'Loket 9  - Lansia',         7,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(10, 'Loket 10 - TB & Paru',      8,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(11, 'Loket 11 - DBD & Demam',    9,  1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(12, 'Loket 12 - Gimul',          10, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(13, 'Loket 13 - Apotik',         11, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP());

-- ---------------------------------------------------------------------
-- LANGKAH 6 - Isi setting kiosk & display
-- ---------------------------------------------------------------------
INSERT INTO settings (`key`, value, created_at, updated_at) VALUES
('nama_puskesmas', 'PUSKESMAS KECAMATAN LESUNG BATU', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('marquee_text',   'Selamat Datang di Pelayanan Kesehatan Puskesmas. Mohon menunggu nomor antrian Anda dipanggil sesuai klaster.', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('kiosk_open',     '1', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('jam_buka',       '08:00', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('jam_tutup',      '14:00', UTC_TIMESTAMP(), UTC_TIMESTAMP());

-- ---------------------------------------------------------------------
-- LANGKAH 7 - Akun petugas
-- ---------------------------------------------------------------------
-- admin  : semua menu, tidak dikunci ke loket mana pun.
-- loket  : all_clusters = 1 -> bisa buka SEMUA loket lewat dropdown
--          "Pilih Loket" di halaman Loket.
-- GANTI PASSWORD sebelum dipakai di server publik.
-- Password kedua akun ini: admin123
INSERT INTO users (id, name, email, role, email_verified_at, password, remember_token, loket_id, all_clusters, api_token, created_at, updated_at) VALUES
(1, 'Administrator', 'admin@puskesmas.test', 'admin', UTC_TIMESTAMP(), '$2y$12$AQ82QBh9HU8.e5a4GrRcu.OYeykxjz2zc89QCv5BwOL06XNTlFVFKSGQjTcUV1s', NULL, NULL, 0, NULL, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2, 'Petugas Loket', 'loket1@puskesmas.test', 'loket', UTC_TIMESTAMP(), '$2y$12$xd1QFpQaaKEM4dQhrTAJNOTTpbimXT2ZpB1Z.8W1h8w7cHBVM/dcG', NULL, 2,    1, NULL, UTC_TIMESTAMP(), UTC_TIMESTAMP());

-- ---------------------------------------------------------------------
-- LANGKAH 8 - Tabel antrian sengaja DIKOSONGKAN
-- ---------------------------------------------------------------------
-- supaya nomor antrian mulai lagi dari A001 / B001 / C001 ... per poli.
-- Kalau mau tetap menyimpan riwayat, JANGAN jalankan script ini --
-- pakai antrian-empty.sql (versi INSERT IGNORE) yang tidak menghapus.

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- LANGKAH 9 - Verifikasi (nilai harapan ditulis di tiap query)
-- ---------------------------------------------------------------------

-- 9a. Haruspnya 5 klasters
SELECT COUNT(*) AS jml_klasters FROM klasters;

-- 9b. Haruspnya 13 unit pelayanan
SELECT COUNT(*) AS jml_polis FROM polis;

-- 9c. Haruspnya 13 loket aktif
SELECT COUNT(*) AS jml_loket_aktif
FROM lokets
WHERE is_active = 1 AND poli_id IS NOT NULL;

-- 9d. Hasilnya harus 0. Kalau tidak, ada poli tanpa loket ->
--     tiketnya akan menggantung selamanya di status 'waiting'.
SELECT p.prefix, p.nama AS poli_tanpa_loket
FROM polis p
WHERE p.is_active = 1
  AND NOT EXISTS (SELECT 1 FROM lokets l WHERE l.poli_id = p.id)
ORDER BY p.prefix;

-- 9e. Daftar loket panggilan
SELECT
    l.id,
    l.nama AS loket,
    p.prefix,
    p.nama  AS poli,
    k.nama   AS klaster,
    k.warna  AS warna
FROM lokets l
LEFT JOIN polis   p ON p.id = l.poli_id
LEFT JOIN klasters k ON k.id = p.klaster_id
ORDER BY l.id;
