-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: antrian_ss
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
-- Table structure for table `antrians`
--

DROP TABLE IF EXISTS `antrians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `antrians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `poli_id` bigint unsigned NOT NULL,
  `nomor` int unsigned NOT NULL,
  `no_antrian` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pasien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('waiting','calling','done','skipped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `loket_id` bigint unsigned DEFAULT NULL,
  `called_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `skipped_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `antrians_tanggal_poli_id_nomor_unique` (`tanggal`,`poli_id`,`nomor`),
  KEY `antrians_poli_id_foreign` (`poli_id`),
  KEY `antrians_loket_id_foreign` (`loket_id`),
  CONSTRAINT `antrians_loket_id_foreign` FOREIGN KEY (`loket_id`) REFERENCES `lokets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `antrians_poli_id_foreign` FOREIGN KEY (`poli_id`) REFERENCES `polis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `antrians`
--

LOCK TABLES `antrians` WRITE;
/*!40000 ALTER TABLE `antrians` DISABLE KEYS */;
INSERT INTO `antrians` VALUES (1,'2026-09-18',1,1,'A001','Budi Santoso','done',1,'2026-09-18 03:59:58','2026-09-18 04:03:58',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(2,'2026-09-18',1,2,'A002','Siti Aminah','done',1,'2026-09-18 04:05:58','2026-09-18 04:09:58',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(3,'2026-09-18',1,3,'A003','Joko Prasetyo','calling',1,'2026-09-18 04:17:58',NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(4,'2026-09-18',1,4,'A004',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(5,'2026-09-18',1,5,'A005','Rina Wati','waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(6,'2026-09-18',2,1,'B001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(7,'2026-09-18',3,1,'C001','Dewi Lestari','done',2,'2026-09-18 05:09:58','2026-09-18 05:13:58',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(8,'2026-09-18',3,2,'C002','Rahmawati','calling',2,'2026-09-18 05:22:58',NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(9,'2026-09-18',3,3,'C003',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(10,'2026-09-18',3,4,'C004','Sri Handayani','waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(11,'2026-09-18',4,1,'D001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(12,'2026-09-18',5,1,'E001','Bayu Nugroho','done',NULL,'2026-09-18 06:22:58','2026-09-18 06:26:58',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(13,'2026-09-18',6,1,'F001','Ahmad Fauzi','calling',3,'2026-09-18 06:33:58',NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(14,'2026-09-18',6,2,'F002',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(15,'2026-09-18',6,3,'F003','Maya Putri','waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(16,'2026-09-18',6,4,'F004',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(17,'2026-09-18',7,1,'G001','Hj. Siti Rahmi','done',NULL,'2026-09-18 07:27:58','2026-09-18 07:31:58',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(18,'2026-09-18',7,2,'G002',NULL,'skipped',NULL,'2026-09-18 07:47:58','2026-09-18 07:51:58','2026-09-18 07:47:58','2026-09-18 06:59:58','2026-09-18 06:59:58'),(19,'2026-09-18',8,1,'H001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(20,'2026-09-18',9,1,'I001','Tono Hariyanto','waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(21,'2026-09-18',10,1,'J001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(22,'2026-09-18',11,1,'K001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(23,'2026-09-18',12,1,'L001','Andi Firmansyah','done',4,'2026-09-18 09:14:58','2026-09-18 09:18:58',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(24,'2026-09-18',12,2,'L002',NULL,'calling',4,'2026-09-18 09:28:58',NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(25,'2026-09-18',13,1,'M001',NULL,'waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58'),(26,'2026-09-18',13,2,'M002','Wulan Sari','waiting',NULL,NULL,NULL,NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58');
/*!40000 ALTER TABLE `antrians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `klasters`
--

DROP TABLE IF EXISTS `klasters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `klasters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warna` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0d6efd',
  `urutan` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `klasters`
--

LOCK TABLES `klasters` WRITE;
/*!40000 ALTER TABLE `klasters` DISABLE KEYS */;
INSERT INTO `klasters` VALUES (1,'Klaster 1 (Administrasi)','#0d6efd',1,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(2,'Klaster 2 (KIA, Remaja, Imunisasi)','#d63384',2,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(3,'Klaster 3 (Usia Dewasa dan Lansia)','#198754',3,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(4,'Klaster 4 (Penyakit Menular)','#fd7e14',4,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(5,'Lintas Klaster (Gimul, Apotik, IGD, Laboratorium)','#6f42c1',5,1,'2026-09-18 06:59:56','2026-09-18 06:59:56');
/*!40000 ALTER TABLE `klasters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lokets`
--

DROP TABLE IF EXISTS `lokets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lokets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poli_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lokets_poli_id_foreign` (`poli_id`),
  CONSTRAINT `lokets_poli_id_foreign` FOREIGN KEY (`poli_id`) REFERENCES `polis` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lokets`
--

LOCK TABLES `lokets` WRITE;
/*!40000 ALTER TABLE `lokets` DISABLE KEYS */;
INSERT INTO `lokets` VALUES (1,'Loket 1 - Pendaftaran',1,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(2,'Loket 2 - KIA',3,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(3,'Loket 3 - Umum Dewasa',6,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(4,'Loket 4 - IGD',12,1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(5,'Loket 5 - Laboratorium',13,1,'2026-09-18 06:59:56','2026-09-18 06:59:56');
/*!40000 ALTER TABLE `lokets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_18_100000_create_polis_table',1),(5,'2026_09_18_100001_create_lokets_table',1),(6,'2026_09_18_100002_create_antrians_table',1),(7,'2026_09_18_100003_create_settings_table',1),(8,'2026_09_18_100004_add_loket_id_to_users_table',1),(9,'2026_09_18_133956_create_klasters_table',1),(10,'2026_09_18_133957_add_klaster_id_to_polis_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polis`
--

DROP TABLE IF EXISTS `polis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `klaster_id` bigint unsigned DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warna` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0d6efd',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `polis_prefix_unique` (`prefix`),
  KEY `polis_klaster_id_foreign` (`klaster_id`),
  CONSTRAINT `polis_klaster_id_foreign` FOREIGN KEY (`klaster_id`) REFERENCES `klasters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polis`
--

LOCK TABLES `polis` WRITE;
/*!40000 ALTER TABLE `polis` DISABLE KEYS */;
INSERT INTO `polis` VALUES (1,1,'Pendaftaran','A','#0d6efd','Loket administrasi & pendaftaran pasien',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(2,1,'Administrasi','B','#3d8bfd','Pelayanan administrasi lainnya',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(3,2,'KIA','C','#d63384','Kesehatan Ibu dan Anak',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(4,2,'Remaja','D','#e86aa1','Pelayanan kesehatan remaja',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(5,2,'Imunisasi','E','#f0a0c6','Pelayanan imunisasi',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(6,3,'Umum Dewasa','F','#198754','Pelayanan penyakit umum dewasa',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(7,3,'Lansia','G','#41b57b','Pelayanan kesehatan lansia',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(8,4,'TB & Paru','H','#fd7e14','Pelayanan Tuberkulosis (TB)',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(9,4,'DBD & Demam','I','#fea84e','Pelayanan Demam Berdarah & demam',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(10,5,'Gimul','J','#6f42c1','Gigi dan Mulut',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(11,5,'Apotik','K','#8a63d2','Pengambilan obat',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(12,5,'IGD','L','#a98edc','Instalasi Gawat Darurat',1,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(13,5,'Laboratorium','M','#c2afe8','Pelayanan laboratorium',1,'2026-09-18 06:59:56','2026-09-18 06:59:56');
/*!40000 ALTER TABLE `polis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('50WZ3Q0rCXUV0xm7lKuEouXtILH4Fomn8AVHivi1',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Microsoft Windows 10.0.18363; en-US) PowerShell/7.5.2','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZU9NbExLdlk1YVJ6NnJQUVlMWEg1RFA2d1NEN3lCZ25EeFNKYkJpSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7czo1OiJyb3V0ZSI7czoxMzoiZGlzcGxheS5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1789714806);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'nama_puskesmas','PUSKESMAS KECAMATAN LESUNG BATU','2026-09-18 06:59:56','2026-09-18 06:59:56'),(2,'marquee_text','Selamat Datang di Pelayanan Kesehatan Puskesmas. Mohon menunggu nomor antrian Anda dipanggil sesuai klaster.','2026-09-18 06:59:56','2026-09-18 06:59:56'),(3,'kiosk_open','1','2026-09-18 06:59:56','2026-09-18 06:59:56'),(4,'jam_buka','08:00','2026-09-18 06:59:56','2026-09-18 06:59:56'),(5,'jam_tutup','14:00','2026-09-18 06:59:56','2026-09-18 06:59:56');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','loket') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'loket',
  `loket_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_loket_id_foreign` (`loket_id`),
  CONSTRAINT `users_loket_id_foreign` FOREIGN KEY (`loket_id`) REFERENCES `lokets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@puskesmas.test','admin',NULL,NULL,'$2y$12$zO7zvoAWDj2b32q.OOCmPe4aN.IjKlOVzWX0JJirqJtC5bkRYEfCW',NULL,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(2,'Petugas Loket 1','loket1@puskesmas.test','loket',1,NULL,'$2y$12$Q8FZYzHvJT8QD7jpdOr9vO/fNA/LMFhDm3b7LrDpLYBFhboluYPZG',NULL,'2026-09-18 06:59:56','2026-09-18 06:59:56'),(3,'Petugas Loket 2','loket2@puskesmas.test','loket',2,NULL,'$2y$12$ET3qdqfa/zG2sbVnFWEx0ukHDWQELWdAZ4rBxtg4g/XJMoUUUby7G',NULL,'2026-09-18 06:59:57','2026-09-18 06:59:57'),(4,'Petugas Loket 3','loket3@puskesmas.test','loket',3,NULL,'$2y$12$irHikw6lpDgytcJHOwOoC.Pjj/aDkefIH2VcMEQEZu8pdfXz3q0y.',NULL,'2026-09-18 06:59:57','2026-09-18 06:59:57'),(5,'Petugas Loket 4','loket4@puskesmas.test','loket',4,NULL,'$2y$12$rQu4WKzoQp5XmMTKKQLtoeSXCVB6gM0C4tC6q2oQZbotzxQPCSLwq',NULL,'2026-09-18 06:59:57','2026-09-18 06:59:57'),(6,'Petugas Loket 5','loket5@puskesmas.test','loket',5,NULL,'$2y$12$/uVR1TlbmIZwKgLIpLx2GOer/QMP.j.aqTzUuiIj6Kas047ThPGTG',NULL,'2026-09-18 06:59:58','2026-09-18 06:59:58');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-18 14:08:44
