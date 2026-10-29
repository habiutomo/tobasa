SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS antrians;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS lokets;
DROP TABLE IF EXISTS polis;
DROP TABLE IF EXISTS klasters;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS migrations;

CREATE TABLE migrations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache (
    `key` VARCHAR(191) NOT NULL,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`),
    KEY cache_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
    `key` VARCHAR(191) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`),
    KEY cache_locks_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    queue VARCHAR(191) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_batches (
    id VARCHAR(191) NOT NULL,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NOT NULL DEFAULT 0,
    created_at INT NOT NULL,
    finished_at INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid VARCHAR(255) NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY failed_jobs_uuid_unique (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE klasters (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    warna VARCHAR(20) NOT NULL DEFAULT '#0d6efd',
    urutan INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE polis (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    klaster_id BIGINT UNSIGNED NULL,
    nama VARCHAR(255) NOT NULL,
    prefix VARCHAR(191) NOT NULL,
    warna VARCHAR(20) NOT NULL DEFAULT '#0d6efd',
    deskripsi TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY polis_prefix_unique (prefix),
    KEY polis_klaster_id_foreign (klaster_id),
    CONSTRAINT polis_klaster_id_foreign FOREIGN KEY (klaster_id) REFERENCES klasters (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lokets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    poli_id BIGINT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY lokets_poli_id_foreign (poli_id),
    CONSTRAINT lokets_poli_id_foreign FOREIGN KEY (poli_id) REFERENCES polis (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE antrians (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    poli_id BIGINT UNSIGNED NOT NULL,
    nomor INT UNSIGNED NOT NULL,
    no_antrian VARCHAR(10) NOT NULL,
    nama_pasien VARCHAR(255) NULL,
    status ENUM('waiting', 'calling', 'done', 'skipped') NOT NULL DEFAULT 'waiting',
    loket_id BIGINT UNSIGNED NULL,
    called_at TIMESTAMP NULL DEFAULT NULL,
    finished_at TIMESTAMP NULL DEFAULT NULL,
    skipped_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY antrians_tanggal_poli_id_nomor_unique (tanggal, poli_id, nomor),
    KEY antrians_poli_id_foreign (poli_id),
    KEY antrians_loket_id_foreign (loket_id),
    CONSTRAINT antrians_poli_id_foreign FOREIGN KEY (poli_id) REFERENCES polis (id) ON DELETE CASCADE,
    CONSTRAINT antrians_loket_id_foreign FOREIGN KEY (loket_id) REFERENCES lokets (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(191) NOT NULL,
    role ENUM('admin', 'loket') NOT NULL DEFAULT 'loket',
    loket_id BIGINT UNSIGNED NULL,
    all_clusters TINYINT(1) NOT NULL DEFAULT 0,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    api_token VARCHAR(191) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email),
    UNIQUE KEY users_api_token_unique (api_token),
    KEY users_loket_id_foreign (loket_id),
    CONSTRAINT users_loket_id_foreign FOREIGN KEY (loket_id) REFERENCES lokets (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
    email VARCHAR(191) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id VARCHAR(191) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    PRIMARY KEY (id),
    KEY sessions_user_id_index (user_id),
    KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(191) NOT NULL,
    value TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY settings_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO klasters (id, nama, warna, urutan, is_active, created_at, updated_at) VALUES
(1, 'Klaster 1 (Administrasi)', '#0d6efd', 1, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2, 'Klaster 2 (KIA, Remaja, Imunisasi)', '#d63384', 2, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(3, 'Klaster 3 (Usia Dewasa dan Lansia)', '#198754', 3, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(4, 'Klaster 4 (Penyakit Menular)', '#fd7e14', 4, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(5, 'Lintas Klaster (Gimul, Apotik, IGD, Laboratorium)', '#6f42c1', 5, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP());

INSERT INTO polis (id, klaster_id, nama, prefix, warna, deskripsi, is_active, created_at, updated_at) VALUES
(1, 1, 'Pendaftaran', 'A', '#0d6efd', 'Loket administrasi & pendaftaran pasien', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2, 1, 'Administrasi', 'B', '#3d8bfd', 'Pelayanan administrasi lainnya', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(3, 2, 'KIA', 'C', '#d63384', 'Kesehatan Ibu dan Anak', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(4, 2, 'Remaja', 'D', '#e86aa1', 'Pelayanan kesehatan remaja', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(5, 2, 'Imunisasi', 'E', '#f0a0c6', 'Pelayanan imunisasi', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(6, 3, 'Umum Dewasa', 'F', '#198754', 'Pelayanan penyakit umum dewasa', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(7, 3, 'Lansia', 'G', '#41b57b', 'Pelayanan kesehatan lansia', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(8, 4, 'TB & Paru', 'H', '#fd7e14', 'Pelayanan Tuberkulosis (TB)', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(9, 4, 'DBD & Demam', 'I', '#fea84e', 'Pelayanan Demam Berdarah & demam', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(10, 5, 'Gimul', 'J', '#6f42c1', 'Gigi dan Mulut', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(11, 5, 'Apotik', 'K', '#8a63d2', 'Pengambilan obat', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(12, 5, 'IGD', 'L', '#a98edc', 'Instalasi Gawat Darurat', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(13, 5, 'Laboratorium', 'M', '#c2afe8', 'Pelayanan laboratorium', 1, UTC_TIMESTAMP(), UTC_TIMESTAMP());

INSERT INTO lokets (id, nama, poli_id, is_active, created_at, updated_at) VALUES
(1, 'Loket 1 - Pendaftaran', 1, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(6, 'Loket 6 - Administrasi', 2, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2, 'Loket 2 - KIA', 3, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(3, 'Loket 3 - Umum Dewasa', 6, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(4, 'Loket 4 - IGD', 12, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(5, 'Loket 5 - Laboratorium', 13, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP());

INSERT INTO settings (`key`, value, created_at, updated_at) VALUES
('nama_puskesmas', 'PUSKESMAS KECAMATAN LESUNG BATU', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('marquee_text', 'Selamat Datang di Pelayanan Kesehatan Puskesmas. Mohon menunggu nomor antrian Anda dipanggil sesuai klaster.', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('kiosk_open', '1', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('jam_buka', '08:00', UTC_TIMESTAMP(), UTC_TIMESTAMP()),
('jam_tutup', '14:00', UTC_TIMESTAMP(), UTC_TIMESTAMP());

INSERT INTO users (id, name, email, role, loket_id, all_clusters, email_verified_at, password, remember_token, api_token, created_at, updated_at) VALUES
(1, 'Administrator', 'admin@puskesmas.test', 'admin', NULL, 0, UTC_TIMESTAMP(), '$2y$10$ARuBhnG7xcboE1EXoSmPjuTi56ZQkj9HD1xKA5XLHkgN6/rdsSAmO', NULL, NULL, UTC_TIMESTAMP(), UTC_TIMESTAMP()),
(2, 'Petugas Loket', 'loket1@puskesmas.test', 'loket', NULL, 1, UTC_TIMESTAMP(), '$2y$10$ARuBhnG7xcboE1EXoSmPjuTi56ZQkj9HD1xKA5XLHkgN6/rdsSAmO', NULL, NULL, UTC_TIMESTAMP(), UTC_TIMESTAMP());

INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_09_18_100000_create_polis_table', 1),
('2026_09_18_100001_create_lokets_table', 1),
('2026_09_18_100002_create_antrians_table', 1),
('2026_09_18_100003_create_settings_table', 1),
('2026_09_18_100004_add_loket_id_to_users_table', 1),
('2026_09_18_133956_create_klasters_table', 1),
 ('2026_09_18_133957_add_klaster_id_to_polis_table', 1),
 ('2026_09_25_000001_add_api_token_to_users_table', 1),
 ('2026_09_25_000002_add_all_clusters_to_users_table', 1);

SET FOREIGN_KEY_CHECKS = 1;
