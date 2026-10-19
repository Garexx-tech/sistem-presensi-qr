-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: db_sipena
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonnte_devices`
--

DROP TABLE IF EXISTS `fonnte_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fonnte_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama device untuk identifikasi',
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'API Key dari Fonnte',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor WhatsApp yang terhubung',
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Device ID dari Fonnte',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status aktif/nonaktif',
  `priority` int NOT NULL DEFAULT '1' COMMENT 'Priority untuk rotasi (1=highest)',
  `sent_count` int NOT NULL DEFAULT '0' COMMENT 'Total pesan terkirim',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Terakhir digunakan',
  `last_checked_at` timestamp NULL DEFAULT NULL COMMENT 'Terakhir di-check statusnya',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unknown' COMMENT 'connected/disconnected/error/unknown',
  `status_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Detail status/error message',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fonnte_devices_api_key_unique` (`api_key`),
  KEY `fonnte_devices_is_active_priority_index` (`is_active`,`priority`),
  KEY `fonnte_devices_sent_count_index` (`sent_count`),
  KEY `fonnte_devices_last_used_at_index` (`last_used_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonnte_devices`
--

LOCK TABLES `fonnte_devices` WRITE;
/*!40000 ALTER TABLE `fonnte_devices` DISABLE KEYS */;
INSERT INTO `fonnte_devices` VALUES (1,'IM3','dC5U9f7jiRNsayHYgyQq','6285743182936',NULL,1,1,9,'2026-10-12 03:12:48',NULL,'unknown',NULL,'2026-10-11 15:25:21','2026-10-12 03:12:48');
/*!40000 ALTER TABLE `fonnte_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jurusans`
--

DROP TABLE IF EXISTS `jurusans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jurusans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_jurusan` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jurusan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jurusans_kode_jurusan_unique` (`kode_jurusan`),
  KEY `jurusans_kode_jurusan_index` (`kode_jurusan`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jurusans`
--

LOCK TABLES `jurusans` WRITE;
/*!40000 ALTER TABLE `jurusans` DISABLE KEYS */;
INSERT INTO `jurusans` VALUES (3,'IPA','Ilmu Pengetahuan Alam',NULL,'2026-10-19 03:47:05','2026-10-19 03:47:05'),(4,'IPS','Ilmu Pengetahuan Sosial',NULL,'2026-10-19 03:47:21','2026-10-19 03:47:21');
/*!40000 ALTER TABLE `jurusans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jurusan_id` bigint unsigned NOT NULL,
  `nama_kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat` int NOT NULL COMMENT '10, 11, 12',
  `kode_kelas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wali_kelas_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kelas_kode_kelas_unique` (`kode_kelas`),
  KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`),
  KEY `kelas_jurusan_id_index` (`jurusan_id`),
  KEY `kelas_tingkat_index` (`tingkat`),
  KEY `kelas_kode_kelas_index` (`kode_kelas`),
  CONSTRAINT `kelas_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (2,3,'X IPA 1',10,'X-IPA-1',2,'2026-10-19 03:49:28','2026-10-19 03:49:28'),(3,3,'XI IPA 1',11,'XI-IPA-1',NULL,'2026-10-19 03:50:15','2026-10-19 03:50:15'),(4,3,'XII IPA 1',12,'XII-IPA-1',2,'2026-10-19 03:50:44','2026-10-19 03:50:44'),(5,4,'X IPS 1',10,'X-IPS-1',2,'2026-10-19 03:51:10','2026-10-19 03:51:10'),(6,4,'XI-IPS-1',11,'XI-IPS-1',2,'2026-10-19 03:51:43','2026-10-19 03:51:43'),(7,4,'XII IPS 1',12,'XII-IPS-1',2,'2026-10-19 03:53:32','2026-10-19 03:53:32');
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2025_10_18_120614_create_users_table',1),(3,'2025_10_18_120616_create_password_reset_token_table',1),(4,'2025_10_18_134219_create_jurusans_table',1),(5,'2025_10_18_134230_create_kelas_table',1),(6,'2025_10_18_134237_add_foreign_key_to_users_table',1),(7,'2025_10_23_082813_create_presensi_sessions_and_presensis_tables',1),(8,'2025_10_23_082815_create_qr_code_table',1),(9,'2025_10_23_082817_create_presensis_table',1),(10,'2025_11_25_094819_create_settings_table',1),(11,'2025_11_30_204409_create_fonnte_devices_table',1),(12,'2025_11_30_205500.create_school_setting',1);
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presensi_sessions`
--

DROP TABLE IF EXISTS `presensi_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presensi_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kelas_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL COMMENT 'Tanggal presensi',
  `jam_checkin_mulai` time NOT NULL COMMENT 'Jam mulai check-in',
  `jam_checkin_selesai` time NOT NULL COMMENT 'Jam selesai check-in',
  `jam_checkout_mulai` time NOT NULL COMMENT 'Jam mulai check-out',
  `jam_checkout_selesai` time NOT NULL COMMENT 'Jam selesai check-out',
  `latitude_checkin` decimal(10,8) DEFAULT NULL COMMENT 'Latitude titik check-in',
  `longitude_checkin` decimal(11,8) DEFAULT NULL COMMENT 'Longitude titik check-in',
  `radius_checkin` int NOT NULL DEFAULT '200' COMMENT 'Radius check-in dalam meter',
  `latitude_checkout` decimal(10,8) DEFAULT NULL COMMENT 'Latitude titik check-out',
  `longitude_checkout` decimal(11,8) DEFAULT NULL COMMENT 'Longitude titik check-out',
  `radius_checkout` int NOT NULL DEFAULT '200' COMMENT 'Radius check-out dalam meter',
  `status` enum('active','inactive','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Status sesi presensi',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `presensi_sessions_created_by_foreign` (`created_by`),
  KEY `presensi_sessions_kelas_id_tanggal_index` (`kelas_id`,`tanggal`),
  KEY `presensi_sessions_status_index` (`status`),
  KEY `presensi_sessions_tanggal_index` (`tanggal`),
  CONSTRAINT `presensi_sessions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensi_sessions_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presensi_sessions`
--

LOCK TABLES `presensi_sessions` WRITE;
/*!40000 ALTER TABLE `presensi_sessions` DISABLE KEYS */;
INSERT INTO `presensi_sessions` VALUES (3,2,2,'2026-10-19','07:00:00','13:00:00','15:00:00','16:00:00',-7.16771133,112.65496829,200,-7.16771133,112.65496829,200,'active',NULL,'2026-10-19 05:16:35','2026-10-19 05:16:35');
/*!40000 ALTER TABLE `presensi_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presensis`
--

DROP TABLE IF EXISTS `presensis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presensis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `qr_code_id` bigint unsigned DEFAULT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_presensi` date NOT NULL COMMENT 'Tanggal presensi',
  `waktu_checkin` timestamp NULL DEFAULT NULL COMMENT 'Waktu scan QR check-in atau input manual',
  `waktu_checkout` timestamp NULL DEFAULT NULL COMMENT 'Waktu scan QR check-out',
  `status` enum('hadir','izin','sakit','alpha') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `latitude_checkin` decimal(10,8) DEFAULT NULL,
  `longitude_checkin` decimal(11,8) DEFAULT NULL,
  `latitude_checkout` decimal(10,8) DEFAULT NULL,
  `longitude_checkout` decimal(11,8) DEFAULT NULL,
  `is_valid_location_checkin` tinyint(1) NOT NULL DEFAULT '1',
  `is_valid_location_checkout` tinyint(1) NOT NULL DEFAULT '1',
  `keterangan_checkin` text COLLATE utf8mb4_unicode_ci COMMENT 'Keterangan check-in (jarak, accuracy, dll)',
  `keterangan_checkout` text COLLATE utf8mb4_unicode_ci COMMENT 'Keterangan check-out (jarak, accuracy, dll)',
  `metode` enum('qr','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual' COMMENT 'qr = via scan QR code, manual = input manual',
  `bukti_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path file bukti (surat izin/sakit)',
  `notifikasi_checkin_terkirim` tinyint(1) NOT NULL DEFAULT '0',
  `notifikasi_checkout_terkirim` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_siswa_presensi_per_day` (`kelas_id`,`siswa_id`,`tanggal_presensi`),
  KEY `presensis_siswa_id_index` (`siswa_id`),
  KEY `presensis_session_id_index` (`session_id`),
  KEY `presensis_qr_code_id_index` (`qr_code_id`),
  KEY `presensis_kelas_id_index` (`kelas_id`),
  KEY `presensis_status_index` (`status`),
  KEY `presensis_metode_index` (`metode`),
  KEY `presensis_tanggal_presensi_index` (`tanggal_presensi`),
  KEY `presensis_kelas_id_tanggal_presensi_index` (`kelas_id`,`tanggal_presensi`),
  KEY `presensis_siswa_id_tanggal_presensi_index` (`siswa_id`,`tanggal_presensi`),
  CONSTRAINT `presensis_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensis_qr_code_id_foreign` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `presensis_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `presensi_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `presensis_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presensis`
--

LOCK TABLES `presensis` WRITE;
/*!40000 ALTER TABLE `presensis` DISABLE KEYS */;
/*!40000 ALTER TABLE `presensis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_codes`
--

DROP TABLE IF EXISTS `qr_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qr_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `qr_code_checkin` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'QR Code untuk check-in',
  `qr_code_checkout` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'QR Code untuk check-out',
  `status` enum('active','expired','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Status QR Code (terpisah dari status sesi)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete untuk history QR Code',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_codes_qr_code_checkin_unique` (`qr_code_checkin`),
  UNIQUE KEY `qr_codes_qr_code_checkout_unique` (`qr_code_checkout`),
  KEY `qr_codes_session_id_index` (`session_id`),
  KEY `qr_codes_qr_code_checkin_index` (`qr_code_checkin`),
  KEY `qr_codes_qr_code_checkout_index` (`qr_code_checkout`),
  KEY `qr_codes_status_index` (`status`),
  KEY `qr_codes_deleted_at_index` (`deleted_at`),
  CONSTRAINT `qr_codes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `presensi_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_codes`
--

LOCK TABLES `qr_codes` WRITE;
/*!40000 ALTER TABLE `qr_codes` DISABLE KEYS */;
INSERT INTO `qr_codes` VALUES (3,3,'ZDkhrHPuZJk9ecaxg4fSnoNS82a8usRC','VRTw9wXKWupHOHcWbBNmQm87tQhttXGq','active',NULL,'2026-10-19 05:16:35','2026-10-19 05:16:35');
/*!40000 ALTER TABLE `qr_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_settings`
--

DROP TABLE IF EXISTS `school_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_settings`
--

LOCK TABLES `school_settings` WRITE;
/*!40000 ALTER TABLE `school_settings` DISABLE KEYS */;
INSERT INTO `school_settings` VALUES (1,'Nama Sekolah','admin_assets/images/brand/logo/logo_sekolah.png',NULL,NULL,NULL,NULL,'2026-10-11 15:22:56','2026-10-11 15:22:56');
/*!40000 ALTER TABLE `school_settings` ENABLE KEYS */;
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
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_key_index` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'fonnte_enabled','1','boolean','Aktifkan notifikasi WhatsApp (Global)','2026-10-11 15:22:56','2026-10-11 15:25:53'),(2,'fonnte_message_template_checkin','Assalamualaikum Bapak/Ibu\r\n\r\nKami informasikan bahwa:\r\n\r\n*Nama:* {student_name}\r\n*NIS:* {nis}\r\n*Kelas:* {class_name}\r\n*Status:* ✅ MASUK\r\n*Waktu:* {checkin_time}\r\n*Tanggal:* {date}\r\n\r\nTerima kasih.\r\n\r\n_Sistem Presensi Sekolah_','text','Template pesan check-in','2026-10-11 15:22:56','2026-10-11 15:25:53'),(3,'fonnte_message_template_checkout','Assalamualaikum Bapak/Ibu\r\n\r\nKami informasikan bahwa:\r\n\r\n*Nama:* {student_name}\r\n*NIS:* {nis}\r\n*Kelas:* {class_name}\r\n*Status:* ? PULANG\r\n*Check-in:* {checkin_time}\r\n*Check-out:* {checkout_time}\r\n*Tanggal:* {date}\r\n\r\nTerima kasih.\r\n\r\n_Sistem Presensi Sekolah_','text','Template pesan check-out','2026-10-11 15:22:56','2026-10-11 15:25:53');
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
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor Induk Siswa (untuk role siswa)',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint NOT NULL DEFAULT '2' COMMENT '0=guru, 1=admin, 2=siswa',
  `kelas_id` bigint unsigned DEFAULT NULL,
  `parent_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'No. Telepon Orang Tua (untuk siswa)',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nis_unique` (`nis`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`),
  KEY `users_email_index` (`email`),
  KEY `users_nis_index` (`nis`),
  KEY `users_kelas_id_index` (`kelas_id`),
  CONSTRAINT `users_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@admin.com',NULL,NULL,'$2y$10$nNfGhwcQvxpl9O6sV4WHweUj1qo38K53xSuTB7QxOzt83j.YgjoRe',1,NULL,NULL,'active',NULL,'2026-10-11 15:22:56','2026-10-11 15:22:56'),(2,'Guru','guru@guru.com',NULL,NULL,'$2y$10$xUIMHOUGWCiUdReutWKlvO1QR2Qv7BpVOo04H8.8M.4Z0CyiLKPOC',0,NULL,NULL,'active',NULL,'2026-10-11 15:22:56','2026-10-11 15:22:56'),(3,'Siswa',NULL,'233307037',NULL,'$2y$10$oXbVNsvKPi7bmvIfbZc1zu.OGoQ3LeMrQXTVrKqzDRx0/ELtMWUbC',2,2,'082143503962','active',NULL,'2026-10-11 15:22:56','2026-10-19 04:02:37');
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

-- Dump completed on 2026-10-19 12:44:55
