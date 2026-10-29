-- =====================================================================
-- FIX: Tiket tidak muncul di Loket Panggilan & Display
-- ---------------------------------------------------------------------
-- PENYEBAS: tabel `lokets` hanya punya satu baris per loket fisik, dan
--           setiap baris menunjuk ke SATU poli (`lokets.poli_id`).
--           Poli yang aktif tapi belum punya baris loket tidak punya
--           "loket tujuan" sama sekali:
--             - Kiosk tetap bisa mencetak tiketnya (mis. E001).
--             - Tapi tidak muncul di daftar tunggu Loket Panggilan
--               (LoketController::data + panggil memfilter
--                `where poli_id = loket->poli_id`).
--             - Dan tidak tampil di Display (DisplayController::data).
--           Akibatnya tiket menggantung berstatus 'waiting' selamanya.
--
-- CARA PAKAI: buka phpMyAdmin > pilih database > tab SQL > paste > Go
-- Script aman dijalankan berulang kali (idempotent).
-- Setelah selesai, jalankan: php artisan optimize:clear
-- =====================================================================


-- ---------------------------------------------------------------------
-- LANGKAH 1 - Daftar poli aktif yang BELUM punya loket
-- ---------------------------------------------------------------------
SELECT p.`id`, p.`prefix`, p.`nama` AS poli, p.`klaster_id`
FROM `polis` p
WHERE p.`is_active` = 1
  AND NOT EXISTS (
      SELECT 1 FROM `lokets` l WHERE l.`poli_id` = p.`id`
  )
ORDER BY p.`prefix`;


-- ---------------------------------------------------------------------
-- LANGKAH 2 - Buat loket untuk setiap poli yang belum punya
-- ---------------------------------------------------------------------
-- Nomor loket dilanjutkan dari id loket terakhir yang ada, sehingga
-- tidak bentrok dengan "Loket 1 - Pendaftaran" s.d. "Loket 6 - Administrasi".
DROP PROCEDURE IF EXISTS `fix_loket_tanpa_loket`;
DELIMITER $$
CREATE PROCEDURE `fix_loket_tanpa_loket`()
BEGIN
    DECLARE v_urut INT;
    DECLARE v_poli_id INT;
    DECLARE v_poli_nama VARCHAR(100);
    DECLARE v_selesai INT DEFAULT 0;

    DECLARE cur CURSOR FOR
        SELECT p.`id`, p.`nama`
        FROM `polis` p
        WHERE p.`is_active` = 1
          AND NOT EXISTS (
              SELECT 1 FROM `lokets` l WHERE l.`poli_id` = p.`id`
          )
        ORDER BY p.`prefix`;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_selesai = 1;

    SET v_urut = (SELECT COALESCE(MAX(`id`), 0) FROM `lokets`);

    OPEN cur;
    perulangan: LOOP
        FETCH cur INTO v_poli_id, v_poli_nama;
        IF v_selesai = 1 THEN
            LEAVE perulangan;
        END IF;

        SET v_urut = v_urut + 1;

        INSERT INTO `lokets` (`nama`, `poli_id`, `is_active`, `created_at`, `updated_at`)
        VALUES (CONCAT('Loket ', v_urut, ' - ', v_poli_nama), v_poli_id, 1, NOW(), NOW());
    END LOOP;
    CLOSE cur;
END$$
DELIMITER ;

CALL `fix_loket_tanpa_loket`();
DROP PROCEDURE IF EXISTS `fix_loket_tanpa_loket`;


-- ---------------------------------------------------------------------
-- LANGKAH 3 - Verifikasi: semua poli aktif harus punya tepat 1 loket
-- ---------------------------------------------------------------------
SELECT
    l.`id`,
    l.`nama`  AS loket,
    p.`prefix`,
    p.`nama`   AS poli,
    l.`is_active`
FROM `lokets` l
LEFT JOIN `polis` p ON p.`id` = l.`poli_id`
ORDER BY l.`id`;

-- Hasilnya harus kosong. Kalau tidak kosong, ulangi LANGKAH 2.
SELECT p.`prefix`, p.`nama` AS poli_tanpa_loket
FROM `polis` p
WHERE p.`is_active` = 1
  AND NOT EXISTS (
      SELECT 1 FROM `lokets` l WHERE l.`poli_id` = p.`id`
  )
ORDER BY p.`prefix`;


-- ---------------------------------------------------------------------
-- LANGKAH 4 - Tiket menggantung dari poli yang tadinya tanpa loket
-- ---------------------------------------------------------------------
-- Hanya 'waiting'/'calling' yang dirender di Loket & Display.
SELECT `no_antrian`, `nama_pasien`, `status`, `poli_id`, `loket_id`, `called_at`
FROM `antrians`
WHERE `tanggal` = CURDATE()
  AND `status` IN ('waiting', 'calling')
ORDER BY `no_antrian`;


-- ---------------------------------------------------------------------
-- LANGKAH 5 - Pastikan petugas bisa membuka loket barunya
-- ---------------------------------------------------------------------
-- Petugas dengan `all_clusters` = 1 sudah bisa berpindah loket sendiri
-- lewat dropdown "Pilih Loket" di halaman Loket, jadi baris di bawah
-- tidak perlu dijalankan untuk user tersebut.
--
-- SET `all_clusters` = 1 agar petugas bisa pindah loket:
-- UPDATE `users` SET `all_clusters` = 1, `updated_at` = NOW()
-- WHERE `role` = 'loket';
--
-- ATAU kunci petugas ke satu loket:
-- UPDATE `users` u
-- JOIN `lokets` l   ON l.`id` = u.`loket_id`
-- JOIN `polis`  p   ON p.`id` = l.`poli_id`
-- SET u.`updated_at` = NOW()
-- WHERE p.`prefix` = 'E' AND u.`role` = 'loket';
