-- =====================================================================
-- FIX: Tiket Administrasi (prefix B) tidak muncul di Loket & Display
-- ---------------------------------------------------------------------
-- PENYEBAS: tabel `lokets` tidak memiliki baris yang menunjuk ke poli
--           "Administrasi" (prefix B). Kiosk tetap bisa mencetak tiket B,
--           tetapi tidak ada loket tujuan sehingga tidak tampil di display
--           dan tidak bisa dipanggil petugas.
--
-- CARA PAKAI: buka phpMyAdmin > pilih database > tab SQL > paste > Go
-- Script ini aman dijalankan berulang kali (idempotent).
-- Setelah selesai, jalankan: php artisan optimize:clear
-- =====================================================================

-- ---------------------------------------------------------------------
-- LANGKAH 1 - Cek kondisi poli Administrasi (prefix B)
-- ---------------------------------------------------------------------
SELECT `id`, `nama`, `prefix`, `is_active`
FROM `polis`
WHERE `prefix` = 'B';

-- Kalau hasil di atas kosong, jangan lanjut ke langkah 2, tapi pastikan
-- poli Administrasi sudah dibuat (lewat menu Admin -> Layanan / Poli).


-- ---------------------------------------------------------------------
-- LANGKAH 2 - Buat loket untuk poli Administrasi
-- ---------------------------------------------------------------------
-- Hanya dieksekusi kalau poli prefix B ada, aktif, dan belum punya loket.
INSERT INTO `lokets` (`nama`, `poli_id`, `is_active`, `created_at`, `updated_at`)
SELECT 'Loket 6 - Administrasi', p.`id`, 1, NOW(), NOW()
FROM `polis` p
WHERE p.`prefix` = 'B'
  AND p.`is_active` = 1
  AND NOT EXISTS (
      SELECT 1
      FROM `lokets` l
      WHERE l.`poli_id` = p.`id`
  );


-- ---------------------------------------------------------------------
-- LANGKAH 3 - Verifikasi hasil
-- ---------------------------------------------------------------------
SELECT
    l.`id`,
    l.`nama`  AS loket,
    l.`poli_id`,
    p.`nama`   AS poli,
    p.`prefix`,
    l.`is_active`
FROM `lokets` l
LEFT JOIN `polis` p ON p.`id` = l.`poli_id`
ORDER BY l.`id`;


-- ---------------------------------------------------------------------
-- LANGKAH 4 (opsional) - Cek tiket B hari ini
-- ---------------------------------------------------------------------
-- Hanya status 'waiting' dan 'calling' yang dirender di Loket & Display.
SELECT `no_antrian`, `nama_pasien`, `status`, `poli_id`, `loket_id`, `called_at`
FROM `antrians`
WHERE `no_antrian` LIKE 'B%'
  AND `tanggal` = CURDATE()
ORDER BY `nomor` DESC;


-- ---------------------------------------------------------------------
-- LANGKAH 5 (opsional) - Assign petugas loket ke loket baru
-- ---------------------------------------------------------------------
-- Ganti 'nama@email.com' dengan user petugas yang akan.handle loket ini.
-- Kalau user sudah punya akses semua klaster (all_clusters = 1),
-- baris ini tidak perlu dijalankan.
--
-- UPDATE `users`
-- SET `loket_id` = (
--         SELECT l.`id` FROM `lokets` l
--         JOIN `polis` p ON p.`id` = l.`poli_id`
--         WHERE p.`prefix` = 'B' LIMIT 1
--     ),
--     `updated_at` = NOW()
-- WHERE `email` = 'nama@email.com'
--   AND `role` = 'loket';
