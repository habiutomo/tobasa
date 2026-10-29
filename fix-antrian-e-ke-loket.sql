-- =====================================================================
-- FIX: Tiket antrian Poli E (Imunisasi) masuk ke Loket Panggilan & Display
-- ---------------------------------------------------------------------
-- PENYEBAS: `antrians.loket_id` hanya diisi saat petugas menekan "Panggil"
--           (LoketController::panggil). Tiket E yang menggantung karena
--           Poli E sebelumnya tidak punya loket => `loket_id` NULL, sehingga
--           tidak pernah muncul di Display maupun sebagai nomor dipanggil.
--
-- CARA PAKAI: buka phpMyAdmin > pilih database > tab SQL > paste > Go
-- Script aman dijalankan berulang kali (idempotent).
-- Setelah selesai, jalankan: php artisan optimize:clear
-- =====================================================================


-- ---------------------------------------------------------------------
-- LANGKAH 1 - Cari poli prefix E dan loket yang melayani poli itu
-- ---------------------------------------------------------------------
SET @poli_id := (SELECT `id` FROM `polis` WHERE `prefix` = 'E' LIMIT 1);
SET @id_loket := (
    SELECT `id` FROM `lokets`
    WHERE `poli_id` = @poli_id AND `is_active` = 1
    ORDER BY `id` LIMIT 1
);

SELECT p.`id` AS poli_id, p.`prefix`, p.`nama` AS poli,
       @id_loket AS loket_id, l.`nama` AS loket
FROM `polis` p
LEFT JOIN `lokets` l ON l.`id` = @id_loket
WHERE p.`id` = @poli_id;


-- ---------------------------------------------------------------------
-- LANGKAH 2 - Pastikan Poli E punya loket (kalau belum ada, dibuatkan)
-- ---------------------------------------------------------------------
-- Nomor loket dilanjutkan dari MAX(id) agar tidak bentrok dengan
-- "Loket 1 - Pendaftaran" s.d. "Loket 13 - Apotik".
INSERT INTO `lokets` (`nama`, `poli_id`, `is_active`, `created_at`, `updated_at`)
SELECT CONCAT('Loket ', (SELECT COALESCE(MAX(`id`), 0) FROM `lokets`) + 1, ' - ', p.`nama`),
       p.`id`, 1, NOW(), NOW()
FROM `polis` p
WHERE p.`id` = @poli_id
  AND NOT EXISTS (SELECT 1 FROM `lokets` l WHERE l.`poli_id` = p.`id`);

-- Baca ulang id loket hasil LANGKAH 2
SET @id_loket := (
    SELECT `id` FROM `lokets`
    WHERE `poli_id` = @poli_id AND `is_active` = 1
    ORDER BY `id` LIMIT 1
);

SELECT @id_loket AS loket_id, `nama` AS loket, `poli_id`, `is_active`
FROM `lokets` WHERE `id` = @id_loket;


-- ---------------------------------------------------------------------
-- LANGKAH 3 - Ikat tiket E yang menggantung ke loket E
-- ---------------------------------------------------------------------
-- Hanya status 'waiting' dan 'calling' yang dirender di Loket & Display.
-- 'done' dan 'skipped' sengaja tidak disentuh (riwayat).
UPDATE `antrians`
SET `loket_id`  = @id_loket,
    `updated_at` = NOW()
WHERE `poli_id`  = @poli_id
  AND `tanggal`  = CURDATE()
  AND `status`  IN ('waiting', 'calling')
  AND (`loket_id` IS NULL OR `loket_id` <> @id_loket);


-- ---------------------------------------------------------------------
-- LANGKAH 4 - Verifikasi
-- ---------------------------------------------------------------------
-- 4a. Tiket E hari ini
SELECT a.`id`, a.`no_antrian`, a.`nama_pasien`, a.`status`,
       a.`loket_id`, l.`nama` AS loket, a.`called_at`
FROM `antrians` a
LEFT JOIN `lokets` l ON l.`id` = a.`loket_id`
WHERE a.`poli_id` = @poli_id
  AND a.`tanggal` = CURDATE()
ORDER BY a.`nomor`;

-- 4b. Harus kosong. Kalau ada, ulangi LANGKAH 2.
SELECT p.`prefix`, p.`nama` AS poli_tanpa_loket
FROM `polis` p
WHERE p.`is_active` = 1
  AND NOT EXISTS (SELECT 1 FROM `lokets` l WHERE l.`poli_id` = p.`id`);

-- 4c. Tiket E menggantung dari tanggal sebelumnya (tidak dirender hari ini)
SELECT `tanggal`, `no_antrian`, `status`, `loket_id`
FROM `antrians`
WHERE `poli_id` = @poli_id
  AND `status` IN ('waiting', 'calling')
  AND `tanggal` < CURDATE()
ORDER BY `tanggal`, `nomor`;


-- ---------------------------------------------------------------------
-- LANGKAH 5 - Pastikan petugas bisa membuka loket E
-- ---------------------------------------------------------------------
-- Petugas dengan `all_clusters` = 1 sudah bisa berpindah loket sendiri
-- lewat dropdown "Pilih Loket" di halaman Loket, jadi baris di bawah
-- tidak perlu dijalankan untuk user tersebut.
--
-- SET `all_clusters` = 1 agar petugas bisa pindah loket:
-- UPDATE `users` SET `all_clusters` = 1, `updated_at` = NOW()
-- WHERE `role` = 'loket';
--
-- ATAU kunci petugas ke loket E:
-- UPDATE `users` SET `loket_id` = @id_loket, `updated_at` = NOW()
-- WHERE `role` = 'loket' AND `id` = 2;   -- ganti 2 dengan id petugas E


-- ---------------------------------------------------------------------
-- LANGKAH 6 - OPSIONAL: panggil 1 tiket E agar langsung tampil di Display
-- ---------------------------------------------------------------------
-- Display menampilkan nomor "calling" kalau ada; kalau tidak, baru
-- menampilkan satu nomor "waiting" berikutnya. Baris di bawah memaksa
-- tiket E pertama hari ini jadi yang sedang dipanggil.
-- Hapus tanda `--` hanya bila memang mau langsung memanggil.
--
-- UPDATE `antrians`
-- SET `status` = 'calling',
--     `loket_id` = @id_loket,
--     `called_at` = NOW(),
--     `updated_at` = NOW()
-- WHERE `id` = (
--     SELECT `x`.`id` FROM (
--         SELECT `id` FROM `antrians`
--         WHERE `poli_id` = @poli_id
--           AND `tanggal` = CURDATE()
--           AND `status` = 'waiting'
--         ORDER BY `nomor` LIMIT 1
--     ) AS `x`
-- );
