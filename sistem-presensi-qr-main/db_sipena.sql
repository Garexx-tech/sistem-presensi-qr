-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 11, 2026 at 01:31 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sipena`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint UNSIGNED NOT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fonnte_devices`
--

CREATE TABLE `fonnte_devices` (
  `id` bigint UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurusans`
--

CREATE TABLE `jurusans` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_jurusan` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jurusan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jurusans`
--

INSERT INTO `jurusans` (`id`, `kode_jurusan`, `nama_jurusan`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'CC', 'Cloud Computing', 'broandy', '2026-10-05 06:32:21', '2026-10-05 06:32:21');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint UNSIGNED NOT NULL,
  `jurusan_id` bigint UNSIGNED NOT NULL,
  `nama_kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat` int NOT NULL COMMENT '10, 11, 12',
  `kode_kelas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wali_kelas_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `jurusan_id`, `nama_kelas`, `tingkat`, `kode_kelas`, `wali_kelas_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'B-pg', 10, 'TI-1', 2, '2026-10-05 06:33:09', '2026-10-05 06:33:09');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_10_18_120614_create_users_table', 1),
(3, '2025_10_18_120616_create_password_reset_token_table', 1),
(4, '2025_10_18_134219_create_jurusans_table', 1),
(5, '2025_10_18_134230_create_kelas_table', 1),
(6, '2025_10_18_134237_add_foreign_key_to_users_table', 1),
(7, '2025_10_23_082813_create_presensi_sessions_and_presensis_tables', 1),
(8, '2025_10_23_082815_create_qr_code_table', 1),
(9, '2025_10_23_082817_create_presensis_table', 1),
(10, '2025_11_25_094819_create_settings_table', 1),
(11, '2025_11_30_204409_create_fonnte_devices_table', 1),
(12, '2025_11_30_205500.create_school_setting', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensis`
--

CREATE TABLE `presensis` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED DEFAULT NULL,
  `qr_code_id` bigint UNSIGNED DEFAULT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `siswa_id` bigint UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi_sessions`
--

CREATE TABLE `presensi_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `presensi_sessions`
--

INSERT INTO `presensi_sessions` (`id`, `kelas_id`, `created_by`, `tanggal`, `jam_checkin_mulai`, `jam_checkin_selesai`, `jam_checkout_mulai`, `jam_checkout_selesai`, `latitude_checkin`, `longitude_checkin`, `radius_checkin`, `latitude_checkout`, `longitude_checkout`, `radius_checkout`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-10-05', '07:00:00', '08:00:00', '15:00:00', '16:00:00', '-7.16832100', '112.65414750', 200, '-7.16832100', '112.65414750', 200, 'active', NULL, '2026-10-05 06:33:51', '2026-10-05 06:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `qr_codes`
--

CREATE TABLE `qr_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` bigint UNSIGNED NOT NULL,
  `qr_code_checkin` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'QR Code untuk check-in',
  `qr_code_checkout` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'QR Code untuk check-out',
  `status` enum('active','expired','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Status QR Code (terpisah dari status sesi)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete untuk history QR Code',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `qr_codes`
--

INSERT INTO `qr_codes` (`id`, `session_id`, `qr_code_checkin`, `qr_code_checkout`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'C00wpa8SH2InD05JKrcAcmKcbt9zq8G2', 'zy0lUP2bDpGYaXmWmx8jL0Ld8E90QzfD', 'active', NULL, '2026-10-05 06:33:51', '2026-10-05 06:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `school_settings`
--

CREATE TABLE `school_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_settings`
--

INSERT INTO `school_settings` (`id`, `school_name`, `logo_path`, `address`, `phone`, `email`, `website`, `created_at`, `updated_at`) VALUES
(1, 'Nama Sekolah', 'admin_assets/images/brand/logo/logo_sekolah.png', NULL, NULL, NULL, NULL, '2026-10-05 06:23:18', '2026-10-05 06:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'fonnte_enabled', '0', 'boolean', 'Aktifkan notifikasi WhatsApp (Global)', '2026-10-05 06:23:18', '2026-10-05 06:23:18'),
(2, 'fonnte_message_template_checkin', 'Assalamualaikum Bapak/Ibu\n\nKami informasikan bahwa:\n\n*Nama:* {student_name}\n*NIS:* {nis}\n*Kelas:* {class_name}\n*Status:* ✅ MASUK\n*Waktu:* {checkin_time}\n*Tanggal:* {date}\n\nTerima kasih.\n\n_Sistem Presensi Sekolah_', 'text', 'Template pesan check-in', '2026-10-05 06:23:18', '2026-10-05 06:23:18'),
(3, 'fonnte_message_template_checkout', 'Assalamualaikum Bapak/Ibu\n\nKami informasikan bahwa:\n\n*Nama:* {student_name}\n*NIS:* {nis}\n*Kelas:* {class_name}\n*Status:* 🏠 PULANG\n*Check-in:* {checkin_time}\n*Check-out:* {checkout_time}\n*Tanggal:* {date}\n\nTerima kasih.\n\n_Sistem Presensi Sekolah_', 'text', 'Template pesan check-out', '2026-10-05 06:23:18', '2026-10-05 06:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor Induk Siswa (untuk role siswa)',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint NOT NULL DEFAULT '2' COMMENT '0=guru, 1=admin, 2=siswa',
  `kelas_id` bigint UNSIGNED DEFAULT NULL,
  `parent_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'No. Telepon Orang Tua (untuk siswa)',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `nis`, `email_verified_at`, `password`, `role`, `kelas_id`, `parent_phone`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@admin.com', NULL, NULL, '$2y$10$nEZjkQF.GeVGvNgY42TOB.spHyD00Vu4Ct03BLE6o60u.aUmpW8CC', 1, NULL, NULL, 'active', NULL, '2026-10-05 06:23:18', '2026-10-05 06:23:18'),
(2, 'Guru', 'guru@guru.com', NULL, NULL, '$2y$10$kc0Kzjs9m/yGYo7/cs593.eID6Y9TnzGPtHs5GHoOZ0tTjbPXiZHC', 0, NULL, NULL, 'active', NULL, '2026-10-05 06:23:18', '2026-10-05 06:23:18'),
(3, 'Siswa', NULL, '233307037', NULL, '$2y$10$bQq3XfIg6j5eArh.9bLOq.JcxxeUWWCuVWCDFU79VeXqmn8guNmgW', 2, NULL, NULL, 'active', NULL, '2026-10-05 06:23:18', '2026-10-05 06:23:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fonnte_devices`
--
ALTER TABLE `fonnte_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fonnte_devices_api_key_unique` (`api_key`),
  ADD KEY `fonnte_devices_is_active_priority_index` (`is_active`,`priority`),
  ADD KEY `fonnte_devices_sent_count_index` (`sent_count`),
  ADD KEY `fonnte_devices_last_used_at_index` (`last_used_at`);

--
-- Indexes for table `jurusans`
--
ALTER TABLE `jurusans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jurusans_kode_jurusan_unique` (`kode_jurusan`),
  ADD KEY `jurusans_kode_jurusan_index` (`kode_jurusan`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kelas_kode_kelas_unique` (`kode_kelas`),
  ADD KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`),
  ADD KEY `kelas_jurusan_id_index` (`jurusan_id`),
  ADD KEY `kelas_tingkat_index` (`tingkat`),
  ADD KEY `kelas_kode_kelas_index` (`kode_kelas`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `presensis`
--
ALTER TABLE `presensis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_siswa_presensi_per_day` (`kelas_id`,`siswa_id`,`tanggal_presensi`),
  ADD KEY `presensis_siswa_id_index` (`siswa_id`),
  ADD KEY `presensis_session_id_index` (`session_id`),
  ADD KEY `presensis_qr_code_id_index` (`qr_code_id`),
  ADD KEY `presensis_kelas_id_index` (`kelas_id`),
  ADD KEY `presensis_status_index` (`status`),
  ADD KEY `presensis_metode_index` (`metode`),
  ADD KEY `presensis_tanggal_presensi_index` (`tanggal_presensi`),
  ADD KEY `presensis_kelas_id_tanggal_presensi_index` (`kelas_id`,`tanggal_presensi`),
  ADD KEY `presensis_siswa_id_tanggal_presensi_index` (`siswa_id`,`tanggal_presensi`);

--
-- Indexes for table `presensi_sessions`
--
ALTER TABLE `presensi_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `presensi_sessions_created_by_foreign` (`created_by`),
  ADD KEY `presensi_sessions_kelas_id_tanggal_index` (`kelas_id`,`tanggal`),
  ADD KEY `presensi_sessions_status_index` (`status`),
  ADD KEY `presensi_sessions_tanggal_index` (`tanggal`);

--
-- Indexes for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_codes_qr_code_checkin_unique` (`qr_code_checkin`),
  ADD UNIQUE KEY `qr_codes_qr_code_checkout_unique` (`qr_code_checkout`),
  ADD KEY `qr_codes_session_id_index` (`session_id`),
  ADD KEY `qr_codes_qr_code_checkin_index` (`qr_code_checkin`),
  ADD KEY `qr_codes_qr_code_checkout_index` (`qr_code_checkout`),
  ADD KEY `qr_codes_status_index` (`status`),
  ADD KEY `qr_codes_deleted_at_index` (`deleted_at`);

--
-- Indexes for table `school_settings`
--
ALTER TABLE `school_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_key_index` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nis_unique` (`nis`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_status_index` (`status`),
  ADD KEY `users_email_index` (`email`),
  ADD KEY `users_nis_index` (`nis`),
  ADD KEY `users_kelas_id_index` (`kelas_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fonnte_devices`
--
ALTER TABLE `fonnte_devices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurusans`
--
ALTER TABLE `jurusans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensis`
--
ALTER TABLE `presensis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi_sessions`
--
ALTER TABLE `presensi_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `qr_codes`
--
ALTER TABLE `qr_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `school_settings`
--
ALTER TABLE `school_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `presensis`
--
ALTER TABLE `presensis`
  ADD CONSTRAINT `presensis_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensis_qr_code_id_foreign` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `presensis_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `presensi_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `presensis_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_sessions`
--
ALTER TABLE `presensi_sessions`
  ADD CONSTRAINT `presensi_sessions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_sessions_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD CONSTRAINT `qr_codes_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `presensi_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
