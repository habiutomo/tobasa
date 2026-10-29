-- =====================================================================
-- EXPORT TERBARU: FITUR LOKET PANGGILAN
-- ---------------------------------------------------------------------
-- Tanggal export : 28 September 2026
-- Sumber         : MySQL 8.0.36, database `tobasa` (localhost)
--
-- Tabel yang diikutkan (6 tabel, urutan sudah aman dari foreign key):
--   1. klasters  - klaster_unit + warna klaster untuk kartu display
--   2. polis     - unit pelayanan (A Pendaftaran ... M Laboratorium)
--   3. lokets    - LOKET PANGGILAN (satu baris = satu loket fisik)
--   4. users     - petugas loket (role, loket_id, all_clusters)
--   5. settings  - setting kiosk + display panggilan
--   6. antrians  - tiket antrian (waiting / calling / done / skipped)
--
-- CARA PAKAI (cPanel / phpMyAdmin):
--   1. Buka phpMyAdmin > pilih database tujuan.
--   2. Tab "SQL" > paste SELURUH isi file ini > Go.
--   3. Selesai. Script meng-DROP tabel lama lalu membuat ulang, jadi
--      data antrian lama ikut terhapus.
--   4. Jalankan: php artisan optimize:clear
--
-- CATATAN PENTING:
--   - Tabel `users` berisi hash password dan `api_token` petugas.
--     Jangan di-import ke server publik tanpa passwordnya diubah dulu.
--   - LoketController hanya memproses loket dengan
--     `is_active` = 1 DAN `poli_id` NOT NULL.
--   - Status tiket yang dirender Loket & Display:
--     'waiting' (antri) dan 'calling' (sedang dipanggil).
-- =====================================================================

-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: tobasa
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `klasters`
--

DROP TABLE IF EXISTS `klasters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `klasters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `warna` varchar(50) NOT NULL DEFAULT '#0d6efd',
  `urutan` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `klasters`
--

LOCK TABLES `klasters` WRITE;
/*!40000 ALTER TABLE `klasters` DISABLE KEYS */;
INSERT INTO `klasters` VALUES (1,'Klaster 1 (Administrasi)','#0d6efd',0,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(2,'Klaster 2 (KIA, Remaja, Imunisasi)','#d63384',0,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(3,'Klaster 3 (Usia Dewasa dan Lansia)','#198754',0,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(4,'Klaster 4 (Penyakit Menular)','#fd7e14',0,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(5,'Lintas Klaster (Gimul, Apotik, IGD, Laboratorium)','#6f42c1',0,1,'2026-09-25 16:16:33','2026-09-25 16:16:33');
/*!40000 ALTER TABLE `klasters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polis`
--

DROP TABLE IF EXISTS `polis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `warna` varchar(50) NOT NULL DEFAULT '#0d6efd',
  `deskripsi` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `klaster_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `polis_prefix_unique` (`prefix`),
  KEY `fk_polis_klasters` (`klaster_id`),
  CONSTRAINT `fk_polis_klasters` FOREIGN KEY (`klaster_id`) REFERENCES `klasters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polis`
--

LOCK TABLES `polis` WRITE;
/*!40000 ALTER TABLE `polis` DISABLE KEYS */;
INSERT INTO `polis` VALUES (1,'Pendaftaran','A','#0d6efd','Loket administrasi & pendaftaran pasien',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',1),(2,'Administrasi','B','#3d8bfd','Pelayanan administrasi lainnya',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',1),(3,'KIA','C','#d63384','Kesehatan Ibu dan Anak',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',2),(4,'Remaja','D','#e86aa1','Pelayanan kesehatan remaja',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',2),(5,'Imunisasi','E','#f0a0c6','Pelayanan imunisasi',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',2),(6,'Umum Dewasa','F','#198754','Pelayanan penyakit umum dewasa',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',3),(7,'Lansia','G','#41b57b','Pelayanan kesehatan lansia',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',3),(8,'TB & Paru','H','#fd7e14','Pelayanan Tuberkulosis (TB)',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',4),(9,'DBD & Demam','I','#fea84e','Pelayanan Demam Berdarah & demam',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',4),(10,'Gimul','J','#6f42c1','Gigi dan Mulut',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',5),(11,'Apotik','K','#8a63d2','Pengambilan obat',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',5),(12,'IGD','L','#a98edc','Instalasi Gawat Darurat',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',5),(13,'Laboratorium','M','#c2afe8','Pelayanan laboratorium',1,'2026-09-25 16:16:33','2026-09-25 16:16:33',5);
/*!40000 ALTER TABLE `polis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lokets`
--

DROP TABLE IF EXISTS `lokets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lokets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `poli_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_lokets_polis` (`poli_id`),
  CONSTRAINT `fk_lokets_polis` FOREIGN KEY (`poli_id`) REFERENCES `polis` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lokets`
--

LOCK TABLES `lokets` WRITE;
/*!40000 ALTER TABLE `lokets` DISABLE KEYS */;
INSERT INTO `lokets` VALUES (1,'Loket 1 - Pendaftaran',1,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(2,'Loket 2 - KIA',3,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(3,'Loket 3 - Umum Dewasa',6,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(4,'Loket 4 - IGD',12,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(5,'Loket 5 - Laboratorium',13,1,'2026-09-25 16:16:33','2026-09-25 16:16:33'),(6,'Loket 6 - Administrasi',2,1,'2026-09-26 10:02:52','2026-09-26 10:02:52'),(7,'Loket 7 - Remaja',4,1,'2026-09-28 10:12:33','2026-09-28 10:12:33'),(8,'Loket 8 - Imunisasi',5,1,'2026-09-28 10:12:33','2026-09-28 10:12:33'),(9,'Loket 9 - Lansia',7,1,'2026-09-28 10:12:33','2026-09-28 10:12:33'),(10,'Loket 10 - TB & Paru',8,1,'2026-09-28 10:12:33','2026-09-28 10:12:33'),(11,'Loket 11 - DBD & Demam',9,1,'2026-09-28 10:12:33','2026-09-28 10:12:33'),(12,'Loket 12 - Gimul',10,1,'2026-09-28 10:12:33','2026-09-28 10:12:33'),(13,'Loket 13 - Apotik',11,1,'2026-09-28 10:12:33','2026-09-28 10:12:33');
/*!40000 ALTER TABLE `lokets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','loket') NOT NULL DEFAULT 'loket',
  `email_verified_at` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `loket_id` int DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `all_clusters` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`),
  KEY `fk_users_lokets` (`loket_id`),
  CONSTRAINT `fk_users_lokets` FOREIGN KEY (`loket_id`) REFERENCES `lokets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@puskesmas.test','admin','2026-09-25 16:16:33','$2y$12$AQ82QBh9HU8.e5a4GrRcu.OYeykxjz2zc89QCv5BwOL06XNTlFVFKSGQjTcUV1s','6Cj3477NGZ','2026-09-25 16:16:34','2026-09-25 16:16:34',NULL,NULL,0),(2,'Petugas Loket','loket1@puskesmas.test','loket','2026-09-25 16:16:34','$2y$12$xd1QFpQaaKEM4dQhrTAJNOTTpbimXT2ZpB1Z.8W1h8w7cHBVM/dcG',NULL,'2026-09-25 16:16:34','2026-09-28 10:13:48',2,NULL,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'nama_puskesmas','PUSKESMAS KECAMATAN LESUNG BATU','2026-09-25 16:16:33','2026-09-25 16:16:33'),(2,'marquee_text','Selamat Datang di Pelayanan Kesehatan Puskesmas. Mohon menunggu nomor antrian Anda dipanggil sesuai klaster.','2026-09-25 16:16:33','2026-09-25 16:16:33'),(3,'kiosk_open','1','2026-09-25 16:16:33','2026-09-25 16:16:33'),(4,'jam_buka','08:00','2026-09-25 16:16:33','2026-09-25 16:16:33'),(5,'jam_tutup','14:00','2026-09-25 16:16:33','2026-09-25 16:16:33');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `antrians`
--

DROP TABLE IF EXISTS `antrians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `antrians` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `poli_id` int NOT NULL,
  `nomor` int NOT NULL,
  `no_antrian` varchar(50) NOT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `status` enum('waiting','calling','done','skipped') NOT NULL DEFAULT 'waiting',
  `loket_id` int DEFAULT NULL,
  `called_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `skipped_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `antrians_tanggal_poli_id_nomor_unique` (`tanggal`,`poli_id`,`nomor`),
  KEY `fk_antrians_polis` (`poli_id`),
  KEY `fk_antrians_lokets` (`loket_id`),
  CONSTRAINT `fk_antrians_lokets` FOREIGN KEY (`loket_id`) REFERENCES `lokets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_antrians_polis` FOREIGN KEY (`poli_id`) REFERENCES `polis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `antrians`
--

LOCK TABLES `antrians` WRITE;
/*!40000 ALTER TABLE `antrians` DISABLE KEYS */;
INSERT INTO `antrians` VALUES (1,'2026-09-25',3,1,'C001',NULL,'calling',2,'2026-09-25 16:33:09',NULL,NULL,'2026-09-25 16:31:07','2026-09-25 16:33:09'),(4,'2026-09-26',2,1,'B001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-26 10:08:05','2026-09-26 10:08:05'),(5,'2026-09-26',2,2,'B002',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-26 10:17:22','2026-09-26 10:17:22'),(6,'2026-09-26',3,1,'C-001','Siti Aminah','skipped',2,'2026-09-26 10:17:42','2026-09-26 10:17:42','2026-09-26 10:17:42','2026-09-26 10:17:36','2026-09-26 10:17:42'),(7,'2026-09-26',3,2,'C-002','Budi Santoso','done',2,'2026-09-26 10:17:43','2026-09-26 10:17:43',NULL,'2026-09-26 10:17:36','2026-09-26 10:17:43'),(8,'2026-09-26',3,3,'C-003','<img src=x onerror=alert(1)>Ani','waiting',NULL,NULL,NULL,NULL,'2026-09-26 10:17:36','2026-09-26 10:17:45'),(9,'2026-09-26',3,4,'C-004','Dewi Lestari','calling',2,'2026-09-26 10:17:44',NULL,NULL,'2026-09-26 10:17:36','2026-09-26 10:17:44'),(10,'2026-09-26',3,5,'C-005','Rina Marlina','waiting',NULL,NULL,NULL,NULL,'2026-09-26 10:17:36','2026-09-26 10:17:36'),(11,'2026-09-26',2,3,'B003',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-26 10:18:09','2026-09-26 10:18:09');
/*!40000 ALTER TABLE `antrians` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
