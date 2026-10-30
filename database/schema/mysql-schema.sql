/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `aktual_better`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `aktual_better` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_std_salinitas_viskositas` bigint(20) unsigned NOT NULL,
  `id_persiapan_bahan_better` bigint(20) unsigned NOT NULL,
  `aktual_vis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_sal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_air` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aktual_better_uuid_unique` (`uuid`),
  KEY `aktual_better_id_std_salinitas_viskositas_foreign` (`id_std_salinitas_viskositas`),
  KEY `aktual_better_id_persiapan_bahan_better_foreign` (`id_persiapan_bahan_better`),
  CONSTRAINT `aktual_better_id_persiapan_bahan_better_foreign` FOREIGN KEY (`id_persiapan_bahan_better`) REFERENCES `persiapan_bahan_better` (`id`),
  CONSTRAINT `aktual_better_id_std_salinitas_viskositas_foreign` FOREIGN KEY (`id_std_salinitas_viskositas`) REFERENCES `std_salinitas_viskositas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aktual_suhu_adonan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `aktual_suhu_adonan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `id_persiapan_bahan_forming` bigint(20) unsigned DEFAULT NULL,
  `id_persiapan_bahan_non_forming` bigint(20) unsigned DEFAULT NULL,
  `id_persiapan_cold_mixing` bigint(20) unsigned DEFAULT NULL,
  `id_suhu_adonan` bigint(20) unsigned NOT NULL,
  `aktual_suhu_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_suhu_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_suhu_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_suhu_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_suhu_5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_aktual_suhu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aktual_suhu_adonan_uuid_unique` (`uuid`),
  KEY `aktual_suhu_adonan_id_persiapan_cold_mixing_foreign` (`id_persiapan_bahan_forming`),
  KEY `aktual_suhu_adonan_id_suhu_adonan_foreign` (`id_suhu_adonan`),
  KEY `aktual_suhu_owner_idx` (`owner_type`,`owner_id`),
  CONSTRAINT `aktual_suhu_adonan_id_suhu_adonan_foreign` FOREIGN KEY (`id_suhu_adonan`) REFERENCES `suhu_adonan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `area_proses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `area_proses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `area_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kebersihan_ruangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kondisi_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suhu_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kebersihan_karyawan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_suhu_ruang` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ketidaksesuaian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_proses_uuid_unique` (`uuid`),
  KEY `area_proses_id_plan_foreign` (`id_plan`),
  KEY `area_proses_area_id_foreign` (`area_id`),
  KEY `area_proses_user_id_id_plan_area_id_index` (`user_id`,`id_plan`,`area_id`),
  KEY `area_proses_tanggal_index` (`tanggal`),
  KEY `area_proses_shift_id_index` (`shift_id`),
  KEY `area_proses_group_uuid_index` (`group_uuid`),
  KEY `area_proses_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `area_proses_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `area_proses_spv_approved_by_foreign` (`spv_approved_by`),
  CONSTRAINT `area_proses_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `input_area` (`id`) ON DELETE CASCADE,
  CONSTRAINT `area_proses_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `area_proses_produksi_approved_by_foreign` FOREIGN KEY (`produksi_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `area_proses_qc_approved_by_foreign` FOREIGN KEY (`qc_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `area_proses_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `data_shift` (`id`) ON DELETE SET NULL,
  CONSTRAINT `area_proses_spv_approved_by_foreign` FOREIGN KEY (`spv_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `area_proses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bagian_mesin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bagian_mesin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_mesin` bigint(20) unsigned NOT NULL,
  `nama_bagian` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bagian_mesin_uuid_unique` (`uuid`),
  KEY `bagian_mesin_id_mesin_foreign` (`id_mesin`),
  CONSTRAINT `bagian_mesin_id_mesin_foreign` FOREIGN KEY (`id_mesin`) REFERENCES `input_mesin_peralatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_baku_rice_bites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_baku_rice_bites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nomor_formula` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_bahan_baku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_baku_rice_bites_uuid_unique` (`uuid`),
  KEY `bahan_baku_rice_bites_id_plan_foreign` (`id_plan`),
  KEY `bahan_baku_rice_bites_user_id_foreign` (`user_id`),
  KEY `bahan_baku_rice_bites_id_produk_id_plan_index` (`id_produk`,`id_plan`),
  CONSTRAINT `bahan_baku_rice_bites_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_baku_rice_bites_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_baku_rice_bites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_baku_roasting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_baku_roasting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_roasting_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `kode_produksi_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `standart_suhu_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_baku_roasting_uuid_unique` (`uuid`),
  KEY `bahan_baku_roasting_id_plan_foreign` (`id_plan`),
  KEY `bahan_baku_roasting_user_id_foreign` (`user_id`),
  KEY `bahan_baku_roasting_shift_id_foreign` (`shift_id`),
  KEY `bahan_baku_roasting_id_produk_foreign` (`id_produk`),
  CONSTRAINT `bahan_baku_roasting_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_baku_roasting_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_baku_roasting_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `data_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_baku_roasting_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_baku_roasting_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_baku_roasting_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahan_baku_roasting_id` bigint(20) unsigned NOT NULL,
  `bahan_baku_roasting_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_baku_roasting_logs_uuid_unique` (`uuid`),
  KEY `bbr_logs_bbr_id_foreign` (`bahan_baku_roasting_id`),
  KEY `bbr_logs_bbr_uuid_index` (`bahan_baku_roasting_uuid`),
  KEY `bbr_logs_user_id_index` (`user_id`),
  KEY `bbr_logs_created_at_index` (`created_at`),
  CONSTRAINT `bbr_logs_bbr_id_foreign` FOREIGN KEY (`bahan_baku_roasting_id`) REFERENCES `bahan_baku_roasting` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bbr_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_baku_tumbling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_baku_tumbling` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_bahan_nonforming` bigint(20) unsigned DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bahan_baku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi_bahan_baku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan bahan baku tumbling',
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suhu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi_daging` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salinity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `viskositas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gramase` decimal(10,2) DEFAULT NULL,
  `hasil_pencampuran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_bahan_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`manual_bahan_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_baku_tumbling_uuid_unique` (`uuid`),
  KEY `bahan_baku_tumbling_id_plan_foreign` (`id_plan`),
  KEY `bahan_baku_tumbling_user_id_foreign` (`user_id`),
  KEY `bahan_baku_tumbling_id_produk_foreign` (`id_produk`),
  KEY `bahan_baku_tumbling_id_bahan_nonforming_foreign` (`id_bahan_nonforming`),
  CONSTRAINT `bahan_baku_tumbling_id_bahan_nonforming_foreign` FOREIGN KEY (`id_bahan_nonforming`) REFERENCES `no_formula_non_forming` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_baku_tumbling_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_baku_tumbling_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bahan_baku_tumbling_id` bigint(20) unsigned NOT NULL,
  `bahan_baku_tumbling_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_baku_tumbling_logs_uuid_unique` (`uuid`),
  KEY `bahan_baku_tumbling_logs_bahan_baku_tumbling_id_foreign` (`bahan_baku_tumbling_id`),
  KEY `bahan_baku_tumbling_logs_bahan_baku_tumbling_uuid_index` (`bahan_baku_tumbling_uuid`),
  KEY `bahan_baku_tumbling_logs_user_id_index` (`user_id`),
  KEY `bahan_baku_tumbling_logs_created_at_index` (`created_at`),
  CONSTRAINT `bahan_baku_tumbling_logs_bahan_baku_tumbling_id_foreign` FOREIGN KEY (`bahan_baku_tumbling_id`) REFERENCES `bahan_baku_tumbling` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_baku_tumbling_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_emulsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_emulsi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_emulsi_id` bigint(20) unsigned NOT NULL,
  `total_pemakaian_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nama_emulsi_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_emulsi_uuid_unique` (`uuid`),
  KEY `bahan_emulsi_nomor_emulsi_id_foreign` (`nomor_emulsi_id`),
  KEY `bahan_emulsi_total_pemakaian_id_foreign` (`total_pemakaian_id`),
  KEY `bahan_emulsi_id_produk_foreign` (`id_produk`),
  KEY `bahan_emulsi_nama_emulsi_id_foreign` (`nama_emulsi_id`),
  KEY `bahan_emulsi_id_plan_foreign` (`id_plan`),
  KEY `bahan_emulsi_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_formula` bigint(20) unsigned NOT NULL,
  `nama_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_forming_uuid_unique` (`uuid`),
  KEY `bahan_forming_id_plan_foreign` (`id_plan`),
  KEY `bahan_forming_id_produk_foreign` (`id_produk`),
  KEY `bahan_forming_id_formula_foreign` (`id_formula`),
  KEY `bahan_forming_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_formula_fla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_formula_fla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_nama_formula_fla` bigint(20) unsigned NOT NULL,
  `id_nomor_step_formula_fla` bigint(20) unsigned DEFAULT NULL,
  `bahan_formula_fla` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_formula_fla` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_formula_fla_uuid_unique` (`uuid`),
  KEY `bahan_formula_fla_id_plan_foreign` (`id_plan`),
  KEY `bahan_formula_fla_user_id_foreign` (`user_id`),
  KEY `bahan_formula_fla_id_nama_formula_fla_foreign` (`id_nama_formula_fla`),
  KEY `bahan_formula_fla_id_nomor_step_formula_fla_foreign` (`id_nomor_step_formula_fla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bahan_rm_non_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_rm_non_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_no_formula_non_forming` bigint(20) unsigned NOT NULL,
  `nama_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_rm` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bahan_rm_non_forming_uuid_unique` (`uuid`),
  KEY `bahan_rm_non_forming_id_no_formula_non_forming_foreign` (`id_no_formula_non_forming`),
  KEY `bahan_rm_non_forming_user_id_foreign` (`user_id`),
  KEY `bahan_rm_non_forming_id_plan_foreign` (`id_plan`),
  CONSTRAINT `bahan_rm_non_forming_id_no_formula_non_forming_foreign` FOREIGN KEY (`id_no_formula_non_forming`) REFERENCES `no_formula_non_forming` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_rm_non_forming_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bahan_rm_non_forming_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `barang_mudah_pecah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_mudah_pecah` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan barang mudah pecah',
  `items_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items_json`)),
  `id_nama_barang` bigint(20) unsigned DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `id_area` bigint(20) unsigned DEFAULT NULL,
  `id_sub_area` bigint(20) unsigned DEFAULT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT 0,
  `nama_barang_manual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_karyawan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temuan_ketidaksesuaian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barang_mudah_pecah_uuid_unique` (`uuid`),
  KEY `barang_mudah_pecah_uuid_index` (`uuid`),
  KEY `barang_mudah_pecah_id_plan_index` (`id_plan`),
  KEY `barang_mudah_pecah_user_id_index` (`user_id`),
  KEY `barang_mudah_pecah_shift_id_index` (`shift_id`),
  KEY `barang_mudah_pecah_tanggal_index` (`tanggal`),
  KEY `barang_mudah_pecah_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `barang_mudah_pecah_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `barang_mudah_pecah_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `barang_mudah_pecah_id_area_foreign` (`id_area`),
  KEY `barang_mudah_pecah_id_sub_area_foreign` (`id_sub_area`),
  CONSTRAINT `barang_mudah_pecah_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `input_area` (`id`) ON DELETE SET NULL,
  CONSTRAINT `barang_mudah_pecah_id_sub_area_foreign` FOREIGN KEY (`id_sub_area`) REFERENCES `sub_area` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `barang_mudah_pecah_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_mudah_pecah_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_mudah_pecah_id` bigint(20) unsigned NOT NULL,
  `barang_mudah_pecah_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barang_mudah_pecah_logs_uuid_unique` (`uuid`),
  KEY `brg_mdh_pch_logs_brg_mdh_pch_id_idx` (`barang_mudah_pecah_id`),
  KEY `brg_mdh_pch_logs_user_id_idx` (`user_id`),
  KEY `brg_mdh_pch_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `berat_produk_bag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `berat_produk_bag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `id_pengemasan_produk` bigint(20) unsigned NOT NULL,
  `id_pengemasan_plastik` bigint(20) unsigned DEFAULT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `id_data_bag` bigint(20) unsigned NOT NULL,
  `line` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `berat_aktual` text DEFAULT NULL,
  `rata_rata_berat` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `berat_produk_bag_uuid_unique` (`uuid`),
  KEY `berat_produk_bag_id_plan_foreign` (`id_plan`),
  KEY `berat_produk_bag_id_shift_foreign` (`id_shift`),
  KEY `berat_produk_bag_id_data_bag_foreign` (`id_data_bag`),
  KEY `berat_produk_bag_id_pengemasan_plastik_foreign` (`id_pengemasan_plastik`),
  KEY `berat_produk_bag_id_pengemasan_produk_foreign` (`id_pengemasan_produk`),
  KEY `berat_produk_bag_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `berat_produk_bag_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `berat_produk_bag_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_produk_bag_id` bigint(20) unsigned NOT NULL,
  `berat_produk_bag_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `berat_produk_bag_logs_uuid_unique` (`uuid`),
  KEY `berat_produk_bag_logs_berat_produk_bag_id_index` (`berat_produk_bag_id`),
  KEY `berat_produk_bag_logs_berat_produk_bag_uuid_index` (`berat_produk_bag_uuid`),
  KEY `berat_produk_bag_logs_user_id_index` (`user_id`),
  KEY `berat_produk_bag_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `berat_produk_box`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `berat_produk_box` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pengemasan_plastik` bigint(20) unsigned DEFAULT NULL,
  `id_pengemasan_produk` bigint(20) unsigned DEFAULT NULL,
  `id_berat_produk_bag` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `id_data_box` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `berat_aktual` text DEFAULT NULL,
  `rata_rata_berat` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `berat_produk_box_uuid_unique` (`uuid`),
  KEY `berat_produk_box_id_plan_foreign` (`id_plan`),
  KEY `berat_produk_box_id_shift_foreign` (`id_shift`),
  KEY `berat_produk_box_id_data_box_foreign` (`id_data_box`),
  KEY `berat_produk_box_id_pengemasan_plastik_foreign` (`id_pengemasan_plastik`),
  KEY `berat_produk_box_id_pengemasan_produk_foreign` (`id_pengemasan_produk`),
  KEY `berat_produk_box_id_berat_produk_bag_foreign` (`id_berat_produk_bag`),
  KEY `berat_produk_box_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `berat_produk_box_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `berat_produk_box_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_produk_box_id` bigint(20) unsigned NOT NULL,
  `berat_produk_box_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `berat_produk_box_logs_uuid_unique` (`uuid`),
  KEY `berat_produk_box_logs_berat_produk_box_id_index` (`berat_produk_box_id`),
  KEY `berat_produk_box_logs_berat_produk_box_uuid_index` (`berat_produk_box_uuid`),
  KEY `berat_produk_box_logs_user_id_index` (`user_id`),
  KEY `berat_produk_box_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chillroom_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chillroom_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `penerimaan_chillroom_id` bigint(20) unsigned NOT NULL,
  `penerimaan_chillroom_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chillroom_logs_uuid_unique` (`uuid`),
  KEY `chillroom_logs_user_id_foreign` (`user_id`),
  KEY `chillroom_logs_penerimaan_chillroom_id_created_at_index` (`penerimaan_chillroom_id`,`created_at`),
  KEY `chillroom_logs_penerimaan_chillroom_uuid_index` (`penerimaan_chillroom_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_bag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_bag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `std_bag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_bag_uuid_unique` (`uuid`),
  KEY `data_bag_id_produk_foreign` (`id_produk`),
  KEY `data_bag_id_plan_foreign` (`id_plan`),
  KEY `data_bag_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_barang` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_barang_uuid_unique` (`uuid`),
  KEY `data_barang_user_id_foreign` (`user_id`),
  KEY `data_barang_id_plan_user_id_index` (`id_plan`,`user_id`),
  KEY `data_barang_uuid_index` (`uuid`),
  KEY `data_barang_id_area_foreign` (`id_area`),
  KEY `data_barang_id_plan_id_area_index` (`id_plan`,`id_area`),
  CONSTRAINT `data_barang_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `input_area` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_box`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_box` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `std_box` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_isi_pack_karton` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_box_uuid_unique` (`uuid`),
  KEY `data_box_id_produk_foreign` (`id_produk`),
  KEY `data_box_id_plan_foreign` (`id_plan`),
  KEY `data_box_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_defect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_defect` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `nama_produsen` varchar(255) DEFAULT NULL,
  `detail_defect` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail_defect`)),
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_defect_uuid_unique` (`uuid`),
  KEY `data_defect_id_plan_index` (`id_plan`),
  KEY `data_defect_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_rm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_rm` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_rm_uuid_unique` (`uuid`),
  KEY `data_rm_id_plan_index` (`id_plan`),
  KEY `data_rm_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_seasoning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_seasoning` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_seasoning` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_seasoning_uuid_unique` (`uuid`),
  KEY `data_seasoning_id_plan_index` (`id_plan`),
  KEY `data_seasoning_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_shift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_shift` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_shift_uuid_unique` (`uuid`),
  KEY `data_shift_id_plan_foreign` (`id_plan`),
  KEY `data_shift_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_thermo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_thermo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_thermo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_thermo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_thermo_uuid_unique` (`uuid`),
  UNIQUE KEY `data_thermo_kode_thermo_unique` (`kode_thermo`),
  KEY `data_thermo_id_plan_index` (`id_plan`),
  KEY `data_thermo_user_id_index` (`user_id`),
  KEY `data_thermo_kode_thermo_index` (`kode_thermo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_timbangan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_timbangan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_timbangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_timbangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_timbangan_uuid_unique` (`uuid`),
  UNIQUE KEY `data_timbangan_kode_timbangan_unique` (`kode_timbangan`),
  KEY `data_timbangan_id_plan_index` (`id_plan`),
  KEY `data_timbangan_user_id_index` (`user_id`),
  KEY `data_timbangan_kode_timbangan_index` (`kode_timbangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_tumbling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_tumbling` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `drum_on` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drum_off` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drum_speed` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_waktu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tekanan_vakum` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `drum_on_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drum_off_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drum_speed_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_waktu_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tekanan_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_tumbling_uuid_unique` (`uuid`),
  KEY `data_tumbling_id_plan_foreign` (`id_plan`),
  KEY `data_tumbling_id_produk_foreign` (`id_produk`),
  KEY `data_tumbling_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dokumentasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dokumentasi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_pengemasan_karton` bigint(20) unsigned DEFAULT NULL,
  `id_berat_produk_box` bigint(20) unsigned DEFAULT NULL,
  `id_berat_produk_bag` bigint(20) unsigned DEFAULT NULL,
  `id_pengemasan_plastik` bigint(20) unsigned DEFAULT NULL,
  `id_pengemasan_produk` bigint(20) unsigned DEFAULT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `foto_kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_kode_produksi_sekunder` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `label_polyroll` varchar(255) DEFAULT NULL,
  `label_supplier` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dokumentasi_uuid_unique` (`uuid`),
  KEY `dokumentasi_id_plan_foreign` (`id_plan`),
  KEY `dokumentasi_id_shift_foreign` (`id_shift`),
  KEY `dokumentasi_user_id_foreign` (`user_id`),
  KEY `dokumentasi_id_pengemasan_karton_foreign` (`id_pengemasan_karton`),
  KEY `dokumentasi_id_berat_produk_box_foreign` (`id_berat_produk_box`),
  KEY `dokumentasi_id_berat_produk_bag_foreign` (`id_berat_produk_bag`),
  KEY `dokumentasi_id_pengemasan_plastik_foreign` (`id_pengemasan_plastik`),
  KEY `dokumentasi_id_pengemasan_produk_foreign` (`id_pengemasan_produk`),
  KEY `dokumentasi_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `dokumentasi_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `dokumentasi_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dokumentasi_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dokumentasi_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dokumentasi_id` bigint(20) unsigned NOT NULL,
  `dokumentasi_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dokumentasi_logs_uuid_unique` (`uuid`),
  KEY `dokumentasi_logs_dokumentasi_id_index` (`dokumentasi_id`),
  KEY `dokumentasi_logs_dokumentasi_uuid_index` (`dokumentasi_uuid`),
  KEY `dokumentasi_logs_user_id_index` (`user_id`),
  KEY `dokumentasi_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frayer_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `frayer_2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `frayer_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses frayer 2',
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer_2` bigint(20) unsigned DEFAULT NULL,
  `id_waktu_penggorengan_2` bigint(20) unsigned DEFAULT NULL,
  `aktual_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpm_minyak` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ffa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frayer_2_uuid_unique` (`uuid`),
  KEY `frayer_2_id_plan_foreign` (`id_plan`),
  KEY `frayer_2_user_id_foreign` (`user_id`),
  KEY `frayer_2_id_produk_foreign` (`id_produk`),
  KEY `frayer_2_id_suhu_frayer_2_foreign` (`id_suhu_frayer_2`),
  KEY `frayer_2_id_waktu_penggorengan_2_foreign` (`id_waktu_penggorengan_2`),
  KEY `frayer_2_frayer_uuid_index` (`frayer_uuid`),
  KEY `frayer_2_penggorengan_uuid_index` (`penggorengan_uuid`),
  KEY `frayer_2_predust_uuid_index` (`predust_uuid`),
  KEY `frayer_2_battering_uuid_index` (`battering_uuid`),
  KEY `frayer_2_breader_uuid_index` (`breader_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frayer_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `frayer_3` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer` bigint(20) unsigned NOT NULL,
  `id_waktu_penggorengan` bigint(20) unsigned NOT NULL,
  `aktual_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpm_minyak` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses frayer 3',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frayer_3_uuid_unique` (`uuid`),
  KEY `frayer_3_id_produk_foreign` (`id_produk`),
  KEY `frayer_3_id_plan_foreign` (`id_plan`),
  KEY `frayer_3_user_id_foreign` (`user_id`),
  KEY `frayer_3_id_suhu_frayer_foreign` (`id_suhu_frayer`),
  KEY `frayer_3_id_waktu_penggorengan_foreign` (`id_waktu_penggorengan`),
  KEY `frayer_3_penggorengan_uuid_index` (`penggorengan_uuid`),
  KEY `frayer_3_predust_uuid_index` (`predust_uuid`),
  KEY `frayer_3_battering_uuid_index` (`battering_uuid`),
  KEY `frayer_3_breader_uuid_index` (`breader_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frayer_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `frayer_4` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer` bigint(20) unsigned NOT NULL,
  `id_waktu_penggorengan` bigint(20) unsigned NOT NULL,
  `aktual_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpm_minyak` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses frayer 4',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frayer_4_uuid_unique` (`uuid`),
  KEY `frayer_4_id_produk_foreign` (`id_produk`),
  KEY `frayer_4_id_plan_foreign` (`id_plan`),
  KEY `frayer_4_user_id_foreign` (`user_id`),
  KEY `frayer_4_id_suhu_frayer_foreign` (`id_suhu_frayer`),
  KEY `frayer_4_id_waktu_penggorengan_foreign` (`id_waktu_penggorengan`),
  KEY `frayer_4_penggorengan_uuid_index` (`penggorengan_uuid`),
  KEY `frayer_4_predust_uuid_index` (`predust_uuid`),
  KEY `frayer_4_battering_uuid_index` (`battering_uuid`),
  KEY `frayer_4_breader_uuid_index` (`breader_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frayer_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `frayer_5` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer` bigint(20) unsigned NOT NULL,
  `id_waktu_penggorengan` bigint(20) unsigned NOT NULL,
  `aktual_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpm_minyak` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses frayer 5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frayer_5_uuid_unique` (`uuid`),
  KEY `frayer_5_id_plan_foreign` (`id_plan`),
  KEY `frayer_5_user_id_foreign` (`user_id`),
  KEY `frayer_5_id_suhu_frayer_foreign` (`id_suhu_frayer`),
  KEY `frayer_5_id_waktu_penggorengan_foreign` (`id_waktu_penggorengan`),
  KEY `frayer_5_id_produk_id_plan_index` (`id_produk`,`id_plan`),
  KEY `frayer_5_tanggal_index` (`tanggal`),
  KEY `frayer_5_penggorengan_uuid_index` (`penggorengan_uuid`),
  KEY `frayer_5_predust_uuid_index` (`predust_uuid`),
  KEY `frayer_5_battering_uuid_index` (`battering_uuid`),
  KEY `frayer_5_breader_uuid_index` (`breader_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gmp_karyawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gmp_karyawan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses gmp karyawan',
  `entries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`entries`)),
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_karyawan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temuan_ketidaksesuaian` enum('sesuai','perlengkapan','kuku','perhiasan','luka','tidak_ada_temuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifikasi` enum('ok','tidak_ok') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `koreksi_lanjutan` enum('ok','tidak_ok') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_qc_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv_at` timestamp NULL DEFAULT NULL,
  `approved_by_qc_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_produksi_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_spv_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gmp_karyawan_uuid_unique` (`uuid`),
  KEY `gmp_karyawan_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `gmp_karyawan_user_id_index` (`user_id`),
  KEY `gmp_karyawan_shift_id_index` (`shift_id`),
  KEY `gmp_karyawan_id_area_index` (`id_area`),
  KEY `gmp_karyawan_approved_by_qc_user_id_foreign` (`approved_by_qc_user_id`),
  KEY `gmp_karyawan_approved_by_produksi_user_id_foreign` (`approved_by_produksi_user_id`),
  KEY `gmp_karyawan_approved_by_spv_user_id_foreign` (`approved_by_spv_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gmp_karyawan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gmp_karyawan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gmp_karyawan_id` bigint(20) unsigned NOT NULL,
  `gmp_karyawan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gmp_karyawan_logs_uuid_unique` (`uuid`),
  KEY `gmp_karyawan_logs_gmp_karyawan_id_idx` (`gmp_karyawan_id`),
  KEY `gmp_karyawan_logs_user_id_idx` (`user_id`),
  KEY `gmp_karyawan_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hasil_penggorengan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_penggorengan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penggorengan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer2_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer3_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer4_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer5_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_std_suhu_pusat` bigint(20) unsigned NOT NULL,
  `aktual_suhu_pusat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sensori_kematangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori_kenampakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori_warna` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori_rasa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori_bau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori_tekstur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan hasil penggorengan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hasil_penggorengan_uuid_unique` (`uuid`),
  KEY `hasil_penggorengan_id_plan_foreign` (`id_plan`),
  KEY `hasil_penggorengan_user_id_foreign` (`user_id`),
  KEY `hasil_penggorengan_id_produk_foreign` (`id_produk`),
  KEY `hasil_penggorengan_id_std_suhu_pusat_foreign` (`id_std_suhu_pusat`),
  KEY `hasil_penggorengan_penggorengan_uuid_foreign` (`penggorengan_uuid`),
  KEY `hasil_penggorengan_predust_uuid_foreign` (`predust_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hasil_penggorengan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_penggorengan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_penggorengan_id` bigint(20) unsigned NOT NULL,
  `hasil_penggorengan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hasil_penggorengan_logs_uuid_unique` (`uuid`),
  KEY `hasil_penggorengan_logs_hasil_penggorengan_id_foreign` (`hasil_penggorengan_id`),
  KEY `hasil_penggorengan_logs_hasil_penggorengan_uuid_index` (`hasil_penggorengan_uuid`),
  KEY `hasil_penggorengan_logs_user_id_index` (`user_id`),
  KEY `hasil_penggorengan_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hasil_proses_roasting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_proses_roasting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_roasting_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proses_roasting_fan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer2_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer3_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer4_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer5_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_std_suhu_pusat` bigint(20) unsigned NOT NULL,
  `aktual_suhu_pusat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`aktual_suhu_pusat`)),
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan hasil proses roasting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sensori` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sensori`)),
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hasil_proses_roasting_uuid_unique` (`uuid`),
  KEY `hasil_proses_roasting_id_plan_foreign` (`id_plan`),
  KEY `hasil_proses_roasting_user_id_foreign` (`user_id`),
  KEY `hasil_proses_roasting_id_shift_foreign` (`id_shift`),
  KEY `hasil_proses_roasting_id_produk_foreign` (`id_produk`),
  KEY `hasil_proses_roasting_id_std_suhu_pusat_foreign` (`id_std_suhu_pusat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hasil_proses_roasting_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_proses_roasting_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_proses_roasting_id` bigint(20) unsigned NOT NULL,
  `hasil_proses_roasting_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hasil_proses_roasting_logs_uuid_unique` (`uuid`),
  KEY `hasil_proses_roasting_logs_hasil_proses_roasting_id_foreign` (`hasil_proses_roasting_id`),
  KEY `hasil_proses_roasting_logs_hasil_proses_roasting_uuid_index` (`hasil_proses_roasting_uuid`),
  KEY `hasil_proses_roasting_logs_user_id_index` (`user_id`),
  KEY `hasil_proses_roasting_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `input_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_area` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `input_area_uuid_unique` (`uuid`),
  KEY `input_area_user_id_foreign` (`user_id`),
  KEY `input_area_id_plan_user_id_index` (`id_plan`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `input_mesin_peralatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_mesin_peralatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned NOT NULL,
  `nama_mesin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_produk` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `input_mesin_peralatan_uuid_unique` (`uuid`),
  KEY `input_mesin_peralatan_user_id_foreign` (`user_id`),
  KEY `input_mesin_peralatan_id_area_foreign` (`id_area`),
  KEY `input_mesin_peralatan_id_plan_id_area_index` (`id_plan`,`id_area`),
  CONSTRAINT `input_mesin_peralatan_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `input_area` (`id`) ON DELETE CASCADE,
  CONSTRAINT `input_mesin_peralatan_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `input_mesin_peralatan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `input_metal_detector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_metal_detector` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `line` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `berat_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fe_depan_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fe_tengah_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fe_belakang_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `non_fe_depan_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `non_fe_tengah_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `non_fe_belakang_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sus_depan_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sus_tengah_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sus_belakang_aktual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_json_metal` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`aktual_json_metal`)),
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `input_metal_detector_uuid_unique` (`uuid`),
  KEY `input_metal_detector_id_plan_foreign` (`id_plan`),
  KEY `input_metal_detector_user_id_foreign` (`user_id`),
  KEY `input_metal_detector_id_shift_foreign` (`id_shift`),
  KEY `input_metal_detector_id_produk_foreign` (`id_produk`),
  KEY `input_metal_detector_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `input_metal_detector_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `input_metal_detector_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `input_metal_detector_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_metal_detector_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `input_metal_detector_id` bigint(20) unsigned NOT NULL,
  `input_metal_detector_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `input_metal_detector_logs_uuid_unique` (`uuid`),
  KEY `input_metal_detector_logs_input_metal_detector_id_index` (`input_metal_detector_id`),
  KEY `input_metal_detector_logs_input_metal_detector_uuid_index` (`input_metal_detector_uuid`),
  KEY `input_metal_detector_logs_user_id_index` (`user_id`),
  KEY `input_metal_detector_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `input_roasting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_roasting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_suhu_sebelum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_suhu_sesudah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan roasting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `input_roasting_uuid_unique` (`uuid`),
  KEY `input_roasting_id_plan_foreign` (`id_plan`),
  KEY `input_roasting_user_id_foreign` (`user_id`),
  KEY `input_roasting_shift_id_foreign` (`shift_id`),
  KEY `input_roasting_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `input_roasting_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_roasting_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_roasting_id` bigint(20) unsigned NOT NULL,
  `input_roasting_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `input_roasting_logs_uuid_unique` (`uuid`),
  KEY `ir_logs_ir_id_foreign` (`input_roasting_id`),
  KEY `ir_logs_ir_uuid_index` (`input_roasting_uuid`),
  KEY `ir_logs_user_id_index` (`user_id`),
  KEY `ir_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventaris_qc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventaris_qc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_pemeriksaan` enum('AWAL SHIFT','AKHIR SHIFT') NOT NULL,
  `catatan_umum` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventaris_qc_uuid_unique` (`uuid`),
  KEY `inventaris_qc_id_shift_foreign` (`id_shift`),
  KEY `inventaris_qc_user_id_foreign` (`user_id`),
  KEY `inventaris_qc_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  CONSTRAINT `inventaris_qc_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventaris_qc_id_shift_foreign` FOREIGN KEY (`id_shift`) REFERENCES `data_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventaris_qc_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventaris_qc_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventaris_qc_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_inventaris_qc` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned NOT NULL,
  `id_list_peralatan` bigint(20) unsigned DEFAULT NULL,
  `nama_alat` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `kondisi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventaris_qc_detail_id_area_foreign` (`id_area`),
  KEY `inventaris_qc_detail_id_list_peralatan_foreign` (`id_list_peralatan`),
  KEY `inventaris_qc_detail_id_inventaris_qc_id_area_index` (`id_inventaris_qc`,`id_area`),
  CONSTRAINT `inventaris_qc_detail_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `input_area` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventaris_qc_detail_id_inventaris_qc_foreign` FOREIGN KEY (`id_inventaris_qc`) REFERENCES `inventaris_qc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventaris_qc_detail_id_list_peralatan_foreign` FOREIGN KEY (`id_list_peralatan`) REFERENCES `list_peralatan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jenis_better`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_better` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nama_better` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_formula_better` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `better_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`better_items`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_better_uuid_unique` (`uuid`),
  KEY `jenis_better_user_id_foreign` (`user_id`),
  KEY `jenis_better_id_plan_foreign` (`id_plan`),
  KEY `jenis_better_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jenis_breader`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_breader` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `jenis_breader` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_breader_uuid_unique` (`uuid`),
  KEY `jenis_breader_id_plan_foreign` (`id_plan`),
  KEY `jenis_breader_user_id_foreign` (`user_id`),
  KEY `jenis_breader_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jenis_emulsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_emulsi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_emulsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_emulsi_uuid_unique` (`uuid`),
  KEY `jenis_emulsi_id_plan_foreign` (`id_plan`),
  KEY `jenis_emulsi_user_id_foreign` (`user_id`),
  KEY `jenis_emulsi_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jenis_marinade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_marinade` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `jenis_marinade` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_marinade_uuid_unique` (`uuid`),
  KEY `jenis_marinade_id_plan_foreign` (`id_plan`),
  KEY `jenis_marinade_user_id_foreign` (`user_id`),
  KEY `jenis_marinade_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jenis_predust`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_predust` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `jenis_predust` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_predust_uuid_unique` (`uuid`),
  KEY `jenis_predust_id_produk_foreign` (`id_produk`),
  KEY `jenis_predust_id_plan_id_produk_index` (`id_plan`,`id_produk`),
  KEY `jenis_predust_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jenis_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_produk` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `nama_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk_rework` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`id_produk_rework`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_produk_uuid_unique` (`uuid`),
  KEY `jenis_produk_id_plan_foreign` (`id_plan`),
  KEY `jenis_produk_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ketidaksesuaian_benda_asing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ketidaksesuaian_benda_asing` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `jam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kontaminan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_produk_terdampak` int(11) NOT NULL,
  `tahapan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dokumentasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ketidaksesuaian_benda_asing_uuid_unique` (`uuid`),
  KEY `ketidaksesuaian_benda_asing_id_plan_foreign` (`id_plan`),
  KEY `ketidaksesuaian_benda_asing_user_id_foreign` (`user_id`),
  KEY `ketidaksesuaian_benda_asing_shift_id_foreign` (`shift_id`),
  KEY `ketidaksesuaian_benda_asing_id_produk_foreign` (`id_produk`),
  KEY `ketidaksesuaian_benda_asing_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `ketidaksesuaian_benda_asing_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `ketidaksesuaian_benda_asing_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ketidaksesuaian_benda_asing_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ketidaksesuaian_benda_asing_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ketidaksesuaian_benda_asing_id` bigint(20) unsigned NOT NULL,
  `ketidaksesuaian_benda_asing_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ketidaksesuaian_benda_asing_logs_uuid_unique` (`uuid`),
  KEY `ktdkss_bnd_asg_logs_ktdkss_bnd_asg_id_idx` (`ketidaksesuaian_benda_asing_id`),
  KEY `ktdkss_bnd_asg_logs_user_id_idx` (`user_id`),
  KEY `ktdkss_bnd_asg_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ketidaksesuaian_plastik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ketidaksesuaian_plastik` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `nama_plastik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_ketidaksesuaian` varchar(255) DEFAULT NULL,
  `alasan_hold` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hold_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dokumentasi_tagging` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dokumentasi_penyimpangan_plastik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ketidaksesuaian_plastik_uuid_unique` (`uuid`),
  KEY `ketidaksesuaian_plastik_user_id_foreign` (`user_id`),
  KEY `ketidaksesuaian_plastik_id_plan_foreign` (`id_plan`),
  KEY `ketidaksesuaian_plastik_id_shift_foreign` (`id_shift`),
  KEY `ketidaksesuaian_plastik_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `ketidaksesuaian_plastik_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `ketidaksesuaian_plastik_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ketidaksesuaian_plastik_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ketidaksesuaian_plastik_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ketidaksesuaian_plastik_id` bigint(20) unsigned NOT NULL,
  `ketidaksesuaian_plastik_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ketidaksesuaian_plastik_logs_uuid_unique` (`uuid`),
  KEY `ktdkss_plstk_logs_ktdkss_plstk_id_idx` (`ketidaksesuaian_plastik_id`),
  KEY `ktdkss_plstk_logs_user_id_idx` (`user_id`),
  KEY `ktdkss_plstk_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kontrol_sanitasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontrol_sanitasi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses kontrol sanitasi',
  `entries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suhu_air` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kadar_klorin_food_basin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kadar_klorin_hand_basin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_verifikasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kontrol_sanitasi_uuid_unique` (`uuid`),
  KEY `kontrol_sanitasi_uuid_index` (`uuid`),
  KEY `kontrol_sanitasi_id_plan_index` (`id_plan`),
  KEY `kontrol_sanitasi_user_id_index` (`user_id`),
  KEY `kontrol_sanitasi_shift_id_index` (`shift_id`),
  KEY `kontrol_sanitasi_tanggal_index` (`tanggal`),
  KEY `kontrol_sanitasi_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `kontrol_sanitasi_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `kontrol_sanitasi_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kontrol_sanitasi_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontrol_sanitasi_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kontrol_sanitasi_id` bigint(20) unsigned NOT NULL,
  `kontrol_sanitasi_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kontrol_sanitasi_logs_uuid_unique` (`uuid`),
  KEY `kontrol_sanitasi_logs_kontrol_sanitasi_id_idx` (`kontrol_sanitasi_id`),
  KEY `kontrol_sanitasi_logs_user_id_idx` (`user_id`),
  KEY `kontrol_sanitasi_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `label_start_up_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_start_up_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label_start_up_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `berat` decimal(10,2) DEFAULT NULL,
  `label_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam` time NOT NULL,
  `foto_kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_pengecekan` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_operator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ttd_operator` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_qc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ttd_qc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `label_start_up_details_label_start_up_id_foreign` (`label_start_up_id`),
  KEY `label_start_up_details_id_produk_foreign` (`id_produk`),
  CONSTRAINT `label_start_up_details_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_start_up_details_label_start_up_id_foreign` FOREIGN KEY (`label_start_up_id`) REFERENCES `label_start_ups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `label_start_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_start_ups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label_start_ups_uuid_unique` (`uuid`),
  KEY `label_start_ups_id_plan_foreign` (`id_plan`),
  KEY `label_start_ups_id_shift_foreign` (`id_shift`),
  KEY `label_start_ups_user_id_foreign` (`user_id`),
  CONSTRAINT `label_start_ups_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_start_ups_id_shift_foreign` FOREIGN KEY (`id_shift`) REFERENCES `data_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_start_ups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `list_peralatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `list_peralatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `nama_alat` varchar(255) NOT NULL,
  `jumlah_alat` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list_peralatan_uuid_unique` (`uuid`),
  KEY `list_peralatan_id_area_foreign` (`id_area`),
  KEY `list_peralatan_user_id_foreign` (`user_id`),
  KEY `list_peralatan_id_plan_id_area_index` (`id_plan`,`id_area`),
  CONSTRAINT `list_peralatan_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `input_area` (`id`) ON DELETE CASCADE,
  CONSTRAINT `list_peralatan_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `list_peralatan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `manager_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `manager_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manager_plans_user_id_plan_id_unique` (`user_id`,`plan_id`),
  KEY `manager_plans_plan_id_foreign` (`plan_id`),
  CONSTRAINT `manager_plans_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `manager_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_cip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_cip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `step_key` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kode tahap, misal: ro1, ri1, hc, dst',
  `step_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama label tahap',
  `std_waktu` decimal(10,2) DEFAULT NULL COMMENT 'Standar waktu (detik)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_cip_uuid_unique` (`uuid`),
  KEY `master_cip_id_plan_index` (`id_plan`),
  KEY `master_cip_user_id_index` (`user_id`),
  KEY `master_cip_id_plan_step_key_index` (`id_plan`,`step_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_standar_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_standar_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `module_name` varchar(255) NOT NULL,
  `area_id` bigint(20) unsigned DEFAULT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `column_name` varchar(255) NOT NULL,
  `parameter_type` enum('suhu','suhu_ruang','suhu_kondisi','suhu_pusat','suhu_pemasakan','status_checklist') NOT NULL,
  `min_value` decimal(8,2) DEFAULT NULL,
  `max_value` decimal(8,2) DEFAULT NULL,
  `standar_value` decimal(8,2) DEFAULT NULL,
  `auto_fix_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_standar_audits_uuid_unique` (`uuid`),
  KEY `master_standar_audits_id_plan_module_name_index` (`id_plan`,`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nama-formula-fla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nama-formula-fla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nama_formula_fla` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_formula_fla_uuid_unique` (`uuid`),
  KEY `nama_formula_fla_id_plan_foreign` (`id_plan`),
  KEY `nama_formula_fla_user_id_foreign` (`user_id`),
  KEY `nama_formula_fla_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `no_formula_non_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `no_formula_non_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nomor_formula` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_formula_non_forming_uuid_unique` (`uuid`),
  KEY `no_formula_non_forming_id_produk_foreign` (`id_produk`),
  KEY `no_formula_non_forming_user_id_foreign` (`user_id`),
  KEY `no_formula_non_forming_id_plan_foreign` (`id_plan`),
  CONSTRAINT `no_formula_non_forming_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `no_formula_non_forming_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `no_formula_non_forming_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nomor_emulsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nomor_emulsi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_emulsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_pemakaian_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nama_emulsi_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_emulsi_uuid_unique` (`uuid`),
  KEY `nomor_emulsi_total_pemakaian_id_foreign` (`total_pemakaian_id`),
  KEY `nomor_emulsi_id_produk_foreign` (`id_produk`),
  KEY `nomor_emulsi_nama_emulsi_id_foreign` (`nama_emulsi_id`),
  KEY `nomor_emulsi_id_plan_foreign` (`id_plan`),
  KEY `nomor_emulsi_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nomor_formula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nomor_formula` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nomor_formula` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_formula_uuid_unique` (`uuid`),
  KEY `nomor_formula_id_plan_foreign` (`id_plan`),
  KEY `nomor_formula_id_produk_foreign` (`id_produk`),
  KEY `nomor_formula_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nomor_step_formula_fla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nomor_step_formula_fla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_nama_formula_fla` bigint(20) unsigned NOT NULL,
  `proses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_step` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_step_formula_fla_uuid_unique` (`uuid`),
  KEY `nomor_step_formula_fla_id_plan_foreign` (`id_plan`),
  KEY `nomor_step_formula_fla_user_id_foreign` (`user_id`),
  KEY `nomor_step_formula_fla_id_nama_formula_fla_foreign` (`id_nama_formula_fla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemasakan_nasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemasakan_nasi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_start` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_stop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_bahan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `jumlah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `sensori_kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_cooking` tinyint(1) NOT NULL DEFAULT 0,
  `lama_proses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_std_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_std_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_std_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_warna` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_aroma` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_rasa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_tekstur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemasakan_nasi_uuid_unique` (`uuid`),
  KEY `pemasakan_nasi_id_plan_foreign` (`id_plan`),
  KEY `pemasakan_nasi_user_id_foreign` (`user_id`),
  KEY `pemasakan_nasi_shift_id_foreign` (`shift_id`),
  KEY `pemasakan_nasi_id_produk_foreign` (`id_produk`),
  KEY `pemasakan_nasi_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pemasakan_nasi_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pemasakan_nasi_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `pemasakan_nasi_kode_form_index` (`kode_form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemasakan_nasi_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemasakan_nasi_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemasakan_nasi_id` bigint(20) unsigned NOT NULL,
  `pemasakan_nasi_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemasakan_nasi_logs_uuid_unique` (`uuid`),
  KEY `pmskn_nsi_logs_pmskn_nsi_id_idx` (`pemasakan_nasi_id`),
  KEY `pmskn_nsi_logs_user_id_idx` (`user_id`),
  KEY `pmskn_nsi_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembekuan_iqf_penggorengan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembekuan_iqf_penggorengan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan pembekuan IQF penggorengan',
  `suhu_ruang_iqf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `holding_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer2_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer3_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer4_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer5_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembekuan_iqf_penggorengan_uuid_unique` (`uuid`),
  KEY `pembekuan_iqf_penggorengan_id_plan_foreign` (`id_plan`),
  KEY `pembekuan_iqf_penggorengan_user_id_foreign` (`user_id`),
  KEY `pembekuan_iqf_penggorengan_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pembekuan_iqf_penggorengan_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pembekuan_iqf_penggorengan_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `pembekuan_iqf_penggorengan_is_audit_index` (`is_audit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembekuan_iqf_penggorengan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembekuan_iqf_penggorengan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembekuan_iqf_penggorengan_id` bigint(20) unsigned NOT NULL,
  `pembekuan_iqf_penggorengan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembekuan_iqf_penggorengan_logs_uuid_unique` (`uuid`),
  KEY `piqf_logs_piqf_id_foreign` (`pembekuan_iqf_penggorengan_id`),
  KEY `piqf_logs_piqf_uuid_index` (`pembekuan_iqf_penggorengan_uuid`),
  KEY `piqf_logs_user_id_index` (`user_id`),
  KEY `piqf_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembekuan_iqf_roasting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembekuan_iqf_roasting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_roasting_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan pembekuan IQF roasting',
  `suhu_ruang_iqf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `holding_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_proses_roasting_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proses_roasting_fan_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer_uuid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer2_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer3_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer4_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer5_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggorengan_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembekuan_iqf_roasting_uuid_unique` (`uuid`),
  KEY `pembekuan_iqf_roasting_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pembekuan_iqf_roasting_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pembekuan_iqf_roasting_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `pembekuan_iqf_roasting_is_audit_index` (`is_audit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembuatan_emulsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembuatan_emulsi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `berat_sampling` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_emulsi_id` bigint(20) unsigned NOT NULL,
  `total_pemakaian_id` bigint(20) unsigned NOT NULL,
  `nomor_emulsi_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `kode_produksi_emulsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_emulsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembuatan_emulsi_uuid_unique` (`uuid`),
  KEY `pembuatan_emulsi_id_produk_foreign` (`id_produk`),
  KEY `pembuatan_emulsi_nama_emulsi_id_foreign` (`nama_emulsi_id`),
  KEY `pembuatan_emulsi_total_pemakaian_id_foreign` (`total_pemakaian_id`),
  KEY `pembuatan_emulsi_nomor_emulsi_id_foreign` (`nomor_emulsi_id`),
  KEY `pembuatan_emulsi_id_plan_foreign` (`id_plan`),
  KEY `pembuatan_emulsi_user_id_foreign` (`user_id`),
  KEY `pembuatan_emulsi_shift_id_foreign` (`shift_id`),
  KEY `pembuatan_emulsi_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pembuatan_emulsi_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pembuatan_emulsi_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembuatan_emulsi_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembuatan_emulsi_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembuatan_emulsi_id` bigint(20) unsigned NOT NULL,
  `pembuatan_emulsi_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembuatan_emulsi_logs_uuid_unique` (`uuid`),
  KEY `pembuatan_emulsi_logs_pembuatan_emulsi_id_index` (`pembuatan_emulsi_id`),
  KEY `pembuatan_emulsi_logs_user_id_index` (`user_id`),
  KEY `pembuatan_emulsi_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembuatan_predust`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembuatan_predust` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_jenis_predust` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan pembuatan predust',
  `kondisi_predust` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_pencetakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembuatan_predust_uuid_unique` (`uuid`),
  KEY `pembuatan_predust_id_produk_foreign` (`id_produk`),
  KEY `pembuatan_predust_id_plan_id_produk_index` (`id_plan`,`id_produk`),
  KEY `pembuatan_predust_user_id_index` (`user_id`),
  KEY `pembuatan_predust_id_jenis_predust_index` (`id_jenis_predust`),
  KEY `pembuatan_predust_penggorengan_uuid_index` (`penggorengan_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembuatan_predust_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembuatan_predust_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembuatan_predust_id` bigint(20) unsigned NOT NULL,
  `pembuatan_predust_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembuatan_predust_logs_uuid_unique` (`uuid`),
  KEY `pembuatan_predust_logs_user_id_foreign` (`user_id`),
  KEY `pembuatan_predust_logs_pembuatan_predust_id_created_at_index` (`pembuatan_predust_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembuatan_sample`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembuatan_sample` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `tanggal_expired` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `berat` decimal(8,2) NOT NULL,
  `berat_sampling` decimal(8,2) DEFAULT NULL,
  `jenis_sample` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kode form hanya dapat diisi melalui modal PDF export',
  `items_json` text DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembuatan_sample_uuid_unique` (`uuid`),
  KEY `pembuatan_sample_id_produk_foreign` (`id_produk`),
  KEY `pembuatan_sample_id_plan_foreign` (`id_plan`),
  KEY `pembuatan_sample_id_shift_foreign` (`id_shift`),
  KEY `pembuatan_sample_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pembuatan_sample_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pembuatan_sample_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `pembuatan_sample_created_by_foreign` (`created_by`),
  CONSTRAINT `pembuatan_sample_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembuatan_sample_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembuatan_sample_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pembuatan_sample_id` bigint(20) unsigned NOT NULL,
  `pembuatan_sample_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembuatan_sample_logs_uuid_unique` (`uuid`),
  KEY `pembuatan_sample_logs_pembuatan_sample_id_index` (`pembuatan_sample_id`),
  KEY `pembuatan_sample_logs_pembuatan_sample_uuid_index` (`pembuatan_sample_uuid`),
  KEY `pembuatan_sample_logs_user_id_index` (`user_id`),
  KEY `pembuatan_sample_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_bahan_kemas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_bahan_kemas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time NOT NULL,
  `nama_kemasan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kondisi_bahan_kemasan` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_bahan_kemas_uuid_unique` (`uuid`),
  KEY `pemeriksaan_bahan_kemas_uuid_index` (`uuid`),
  KEY `pemeriksaan_bahan_kemas_id_plan_index` (`id_plan`),
  KEY `pemeriksaan_bahan_kemas_user_id_index` (`user_id`),
  KEY `pemeriksaan_bahan_kemas_shift_id_index` (`shift_id`),
  KEY `pemeriksaan_bahan_kemas_tanggal_index` (`tanggal`),
  KEY `pemeriksaan_bahan_kemas_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pemeriksaan_bahan_kemas_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pemeriksaan_bahan_kemas_spv_approved_by_foreign` (`spv_approved_by`),
  CONSTRAINT `pemeriksaan_bahan_kemas_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_bahan_kemas_produksi_approved_by_foreign` FOREIGN KEY (`produksi_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pemeriksaan_bahan_kemas_qc_approved_by_foreign` FOREIGN KEY (`qc_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pemeriksaan_bahan_kemas_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `data_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_bahan_kemas_spv_approved_by_foreign` FOREIGN KEY (`spv_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pemeriksaan_bahan_kemas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_bahan_kemas_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_bahan_kemas_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_bahan_kemas_id` bigint(20) unsigned NOT NULL,
  `pemeriksaan_bahan_kemas_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_yang_diubah`)),
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nilai_lama`)),
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nilai_baru`)),
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_bahan_kemas_logs_uuid_unique` (`uuid`),
  KEY `pemeriksaan_bahan_kemas_logs_pemeriksaan_bahan_kemas_id_foreign` (`pemeriksaan_bahan_kemas_id`),
  KEY `pemeriksaan_bahan_kemas_logs_pemeriksaan_bahan_kemas_uuid_index` (`pemeriksaan_bahan_kemas_uuid`),
  CONSTRAINT `pemeriksaan_bahan_kemas_logs_pemeriksaan_bahan_kemas_id_foreign` FOREIGN KEY (`pemeriksaan_bahan_kemas_id`) REFERENCES `pemeriksaan_bahan_kemas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_benda_asing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_benda_asing` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned DEFAULT NULL,
  `rm` varchar(255) DEFAULT NULL,
  `jumlah_terdampak` varchar(255) DEFAULT NULL,
  `berat` int(11) DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time NOT NULL,
  `jenis_kontaminasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ukuran_kontaminasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ditemukan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `analisa_masalah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindak_korektif` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diketahui` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_benda_asing_uuid_unique` (`uuid`),
  KEY `pemeriksaan_benda_asing_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `pemeriksaan_benda_asing_user_id_index` (`user_id`),
  KEY `pemeriksaan_benda_asing_shift_id_index` (`shift_id`),
  KEY `pemeriksaan_benda_asing_id_produk_index` (`id_produk`),
  KEY `pemeriksaan_benda_asing_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pemeriksaan_benda_asing_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pemeriksaan_benda_asing_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_benda_asing_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_benda_asing_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_benda_asing_id` bigint(20) unsigned NOT NULL,
  `pemeriksaan_benda_asing_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_benda_asing_logs_uuid_unique` (`uuid`),
  KEY `pmrks_bnd_asg_logs_pmrks_bnd_asg_id_idx` (`pemeriksaan_benda_asing_id`),
  KEY `pmrks_bnd_asg_logs_user_id_idx` (`user_id`),
  KEY `pmrks_bnd_asg_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_produk_cooking_mixer_fla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_produk_cooking_mixer_fla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `id_frm_fla` bigint(20) unsigned NOT NULL,
  `id_stp_frm_fla` bigint(20) unsigned DEFAULT NULL,
  `id_nama_formula_fla` bigint(20) unsigned NOT NULL,
  `kode_produksi_bahan` text DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_start` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_stop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sensori_kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_gas` tinyint(1) NOT NULL DEFAULT 0,
  `lama_proses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `speed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_std_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_std_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temp_std_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_warna` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_aroma` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_tekstur` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organo_rasa` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_produk_cooking_mixer_fla_uuid_unique` (`uuid`),
  KEY `pemeriksaan_produk_cooking_mixer_fla_id_plan_foreign` (`id_plan`),
  KEY `pemeriksaan_produk_cooking_mixer_fla_user_id_foreign` (`user_id`),
  KEY `pemeriksaan_produk_cooking_mixer_fla_shift_id_foreign` (`shift_id`),
  KEY `pemeriksaan_produk_cooking_mixer_fla_id_frm_fla_foreign` (`id_frm_fla`),
  KEY `pemeriksaan_produk_cooking_mixer_fla_id_stp_frm_fla_foreign` (`id_stp_frm_fla`),
  KEY `pemeriksaan_produk_cooking_mixer_fla_id_nama_formula_fla_foreign` (`id_nama_formula_fla`),
  KEY `fk_ppcmf_qc_approved_by` (`qc_approved_by`),
  KEY `fk_ppcmf_produksi_approved_by` (`produksi_approved_by`),
  KEY `fk_ppcmf_spv_approved_by` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_produk_cooking_mixer_fla_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_produk_cooking_mixer_fla_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_produk_cooking_mixer_fla_id` bigint(20) unsigned NOT NULL,
  `pemeriksaan_produk_cooking_mixer_fla_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_produk_cooking_mixer_fla_logs_uuid_unique` (`uuid`),
  KEY `idx_cooking_mixer_fla_logs_id` (`pemeriksaan_produk_cooking_mixer_fla_id`),
  KEY `idx_cooking_mixer_fla_logs_uuid` (`pemeriksaan_produk_cooking_mixer_fla_uuid`),
  KEY `idx_cooking_mixer_fla_logs_user` (`user_id`),
  KEY `idx_cooking_mixer_fla_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_proses_produksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_proses_produksi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `ketidaksesuaian` enum('bahan','produk','proses') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uraian_permasalahan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `analisa_penyebab` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `disposisi` varchar(255) DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_proses_produksi_uuid_unique` (`uuid`),
  KEY `pemeriksaan_proses_produksi_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `pemeriksaan_proses_produksi_user_id_index` (`user_id`),
  KEY `pemeriksaan_proses_produksi_shift_id_index` (`shift_id`),
  KEY `pemeriksaan_proses_produksi_id_area_index` (`id_area`),
  KEY `pemeriksaan_proses_produksi_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pemeriksaan_proses_produksi_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pemeriksaan_proses_produksi_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_proses_produksi_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_proses_produksi_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_proses_produksi_id` bigint(20) unsigned NOT NULL,
  `pemeriksaan_proses_produksi_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_proses_produksi_logs_uuid_unique` (`uuid`),
  KEY `pmrks_prs_prd_logs_pmrks_prs_prd_id_idx` (`pemeriksaan_proses_produksi_id`),
  KEY `pmrks_prs_prd_logs_user_id_idx` (`user_id`),
  KEY `pmrks_prs_prd_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_rheon_machine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_rheon_machine` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `batch` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pukul` time NOT NULL,
  `inner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `outer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `belt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extrusion_speed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_cetakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_dough_adonan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `berat_filler` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `berat_after_forming` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `berat_after_frying` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `jumlah_dough` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rata_rata_dough` decimal(10,2) NOT NULL DEFAULT 0.00,
  `jumlah_filler` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rata_rata_filler` decimal(10,2) NOT NULL DEFAULT 0.00,
  `jumlah_after_forming` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rata_rata_after_forming` decimal(10,2) NOT NULL DEFAULT 0.00,
  `jumlah_after_frying` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rata_rata_after_frying` decimal(10,2) NOT NULL DEFAULT 0.00,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_rheon_machine_uuid_unique` (`uuid`),
  KEY `pemeriksaan_rheon_machine_shift_id_foreign` (`shift_id`),
  KEY `pemeriksaan_rheon_machine_id_produk_foreign` (`id_produk`),
  KEY `pemeriksaan_rheon_machine_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `pemeriksaan_rheon_machine_user_id_tanggal_index` (`user_id`,`tanggal`),
  KEY `pemeriksaan_rheon_machine_uuid_index` (`uuid`),
  KEY `pemeriksaan_rheon_machine_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pemeriksaan_rheon_machine_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pemeriksaan_rheon_machine_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `pemeriksaan_rheon_machine_kode_form_index` (`kode_form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_rheon_machine_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_rheon_machine_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_rheon_machine_id` bigint(20) unsigned NOT NULL,
  `pemeriksaan_rheon_machine_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_rheon_machine_logs_uuid_unique` (`uuid`),
  KEY `idx_rheon_machine_logs_id` (`pemeriksaan_rheon_machine_id`),
  KEY `idx_rheon_machine_logs_uuid` (`pemeriksaan_rheon_machine_uuid`),
  KEY `idx_rheon_machine_logs_user` (`user_id`),
  KEY `idx_rheon_machine_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_rice_bites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_rice_bites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `nomor_formula` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `batch` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_cooking_cycle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahan_baku` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `premix` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`premix`)),
  `parameter_nitrogen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_inject_nitrogen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rpm_cooking_cattle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cold_mixing` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suhu_aktual_adonan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `suhu_adonan_pencampuran` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_suhu` decimal(8,2) DEFAULT NULL,
  `hasil_pencampuran` enum('OK','Tidak OK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_rice_bites_uuid_unique` (`uuid`),
  KEY `pemeriksaan_rice_bites_user_id_foreign` (`user_id`),
  KEY `pemeriksaan_rice_bites_shift_id_foreign` (`shift_id`),
  KEY `pemeriksaan_rice_bites_id_produk_foreign` (`id_produk`),
  KEY `pemeriksaan_rice_bites_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `pemeriksaan_rice_bites_uuid_index` (`uuid`),
  KEY `pemeriksaan_rice_bites_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `pemeriksaan_rice_bites_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `pemeriksaan_rice_bites_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemeriksaan_rice_bites_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemeriksaan_rice_bites_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemeriksaan_rice_bites_id` bigint(20) unsigned NOT NULL,
  `pemeriksaan_rice_bites_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_rice_bites_logs_uuid_unique` (`uuid`),
  KEY `idx_rice_bites_logs_id` (`pemeriksaan_rice_bites_id`),
  KEY `idx_rice_bites_logs_uuid` (`pemeriksaan_rice_bites_uuid`),
  KEY `idx_rice_bites_logs_user` (`user_id`),
  KEY `idx_rice_bites_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penerimaan_chillroom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penerimaan_chillroom` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `nama_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_kedatangan` time DEFAULT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `suhu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sensori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `kemasan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text DEFAULT NULL,
  `standar_berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_rm` longtext DEFAULT NULL,
  `nilai_jumlah_rm` longtext DEFAULT NULL,
  `status_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan_rm` text DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penerimaan_chillroom_uuid_unique` (`uuid`),
  KEY `penerimaan_chillroom_id_plan_foreign` (`id_plan`),
  KEY `penerimaan_chillroom_user_id_foreign` (`user_id`),
  KEY `penerimaan_chillroom_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `penerimaan_chillroom_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `penerimaan_chillroom_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengemasan_karton`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengemasan_karton` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_berat_produk_box` bigint(20) unsigned DEFAULT NULL,
  `id_berat_produk_bag` bigint(20) unsigned DEFAULT NULL,
  `id_pengemasan_plastik` bigint(20) unsigned DEFAULT NULL,
  `id_pengemasan_produk` bigint(20) unsigned DEFAULT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `identitas_produk_pada_karton` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `standar_jumlah_karton` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_jumlah_karton` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kesesuaian_isi_pack` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`kesesuaian_isi_pack`)),
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengemasan_karton_uuid_unique` (`uuid`),
  KEY `pengemasan_karton_id_plan_foreign` (`id_plan`),
  KEY `pengemasan_karton_user_id_foreign` (`user_id`),
  KEY `pengemasan_karton_id_berat_produk_box_foreign` (`id_berat_produk_box`),
  KEY `pengemasan_karton_id_berat_produk_bag_foreign` (`id_berat_produk_bag`),
  KEY `pengemasan_karton_id_pengemasan_plastik_foreign` (`id_pengemasan_plastik`),
  KEY `pengemasan_karton_id_pengemasan_produk_foreign` (`id_pengemasan_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengemasan_karton_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengemasan_karton_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengemasan_karton_id` bigint(20) unsigned NOT NULL,
  `pengemasan_karton_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengemasan_karton_logs_uuid_unique` (`uuid`),
  KEY `pengemasan_karton_logs_pengemasan_karton_id_foreign` (`pengemasan_karton_id`),
  KEY `pengemasan_karton_logs_pengemasan_karton_uuid_index` (`pengemasan_karton_uuid`),
  KEY `pengemasan_karton_logs_user_id_index` (`user_id`),
  KEY `pengemasan_karton_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengemasan_plastik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengemasan_plastik` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_pengemasan_produk` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses_penimbangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses_sealing` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `identitas_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_md` varchar(255) DEFAULT NULL,
  `kode_kemasan_plastik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `line` varchar(255) DEFAULT NULL,
  `waktu_pengemasan` varchar(255) DEFAULT NULL,
  `kesesuaian_label_plastik` varchar(255) DEFAULT NULL,
  `kekuatan_seal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengemasan_plastik_uuid_unique` (`uuid`),
  KEY `pengemasan_plastik_id_plan_foreign` (`id_plan`),
  KEY `pengemasan_plastik_id_shift_foreign` (`id_shift`),
  KEY `pengemasan_plastik_user_id_foreign` (`user_id`),
  KEY `pengemasan_plastik_id_pengemasan_produk_foreign` (`id_pengemasan_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengemasan_plastik_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengemasan_plastik_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pengemasan_plastik_id` bigint(20) unsigned NOT NULL,
  `pengemasan_plastik_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengemasan_plastik_logs_uuid_unique` (`uuid`),
  KEY `pengemasan_plastik_logs_pengemasan_plastik_id_index` (`pengemasan_plastik_id`),
  KEY `pengemasan_plastik_logs_pengemasan_plastik_uuid_index` (`pengemasan_plastik_uuid`),
  KEY `pengemasan_plastik_logs_user_id_index` (`user_id`),
  KEY `pengemasan_plastik_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengemasan_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengemasan_produk` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `tanggal_expired` date NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_kombinasi` tinyint(1) NOT NULL DEFAULT 0,
  `id_produk_2` bigint(20) unsigned DEFAULT NULL,
  `kode_produksi_2` varchar(255) DEFAULT NULL,
  `berat_produk_2` varchar(255) DEFAULT NULL,
  `tanggal_expired_2` date DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_suhu_produk_iqf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-18°C',
  `aktual_suhu_produk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_awal_packing` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status_release_produk` varchar(255) DEFAULT NULL,
  `tindakan_koreksi` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengemasan_produk_uuid_unique` (`uuid`),
  KEY `pengemasan_produk_id_plan_foreign` (`id_plan`),
  KEY `pengemasan_produk_id_produk_foreign` (`id_produk`),
  KEY `pengemasan_produk_id_shift_foreign` (`id_shift`),
  KEY `pengemasan_produk_user_id_foreign` (`user_id`),
  KEY `pengemasan_produk_id_produk_2_foreign` (`id_produk_2`),
  CONSTRAINT `pengemasan_produk_id_produk_2_foreign` FOREIGN KEY (`id_produk_2`) REFERENCES `jenis_produk` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengemasan_produk_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengemasan_produk_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pengemasan_produk_id` bigint(20) unsigned NOT NULL,
  `pengemasan_produk_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengemasan_produk_logs_uuid_unique` (`uuid`),
  KEY `pengemasan_produk_logs_pengemasan_produk_id_foreign` (`pengemasan_produk_id`),
  KEY `pengemasan_produk_logs_pengemasan_produk_uuid_index` (`pengemasan_produk_uuid`),
  KEY `pengemasan_produk_logs_user_id_index` (`user_id`),
  KEY `pengemasan_produk_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penggorengan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penggorengan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_strokes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_pemasakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses penggorengan',
  `hasil_pencetakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penggorengan_uuid_unique` (`uuid`),
  KEY `penggorengan_id_produk_foreign` (`id_produk`),
  KEY `penggorengan_shift_id_foreign` (`shift_id`),
  KEY `penggorengan_id_plan_foreign` (`id_plan`),
  KEY `penggorengan_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penggorengan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penggorengan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penggorengan_id` bigint(20) unsigned NOT NULL,
  `penggorengan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penggorengan_logs_uuid_unique` (`uuid`),
  KEY `penggorengan_logs_user_id_foreign` (`user_id`),
  KEY `penggorengan_logs_penggorengan_id_created_at_index` (`penggorengan_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penyimpanan_bahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyimpanan_bahan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `pemeriksaan_kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemeriksaan_kebersihan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kebersihan_ruang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penyimpanan_bahan_uuid_unique` (`uuid`),
  KEY `penyimpanan_bahan_shift_id_foreign` (`shift_id`),
  KEY `penyimpanan_bahan_id_plan_foreign` (`id_plan`),
  KEY `penyimpanan_bahan_user_id_foreign` (`user_id`),
  KEY `penyimpanan_bahan_group_uuid_index` (`group_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_bahan_better`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_bahan_better` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses',
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_better` bigint(20) unsigned NOT NULL,
  `berat_better` decimal(10,2) NOT NULL,
  `gramase` decimal(10,2) DEFAULT NULL COMMENT 'Total gramase larutan batter (gram)',
  `suhu_air` double(8,2) DEFAULT NULL,
  `sensori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `better_rows` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`better_rows`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kode_produksi_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi_better` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_bahan_better_uuid_unique` (`uuid`),
  KEY `persiapan_bahan_better_user_id_foreign` (`user_id`),
  KEY `persiapan_bahan_better_id_plan_foreign` (`id_plan`),
  KEY `persiapan_bahan_better_id_produk_foreign` (`id_produk`),
  KEY `persiapan_bahan_better_id_better_foreign` (`id_better`),
  KEY `persiapan_bahan_better_shift_id_foreign` (`shift_id`),
  KEY `persiapan_bahan_better_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `persiapan_bahan_better_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `persiapan_bahan_better_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_bahan_better_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_bahan_better_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `persiapan_bahan_better_id` bigint(20) unsigned NOT NULL,
  `persiapan_bahan_better_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_bahan_better_logs_uuid_unique` (`uuid`),
  KEY `persiapan_bahan_better_logs_persiapan_bahan_better_id_index` (`persiapan_bahan_better_id`),
  KEY `persiapan_bahan_better_logs_user_id_index` (`user_id`),
  KEY `persiapan_bahan_better_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_bahan_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_bahan_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `id_suhu_adonan` bigint(20) unsigned DEFAULT NULL,
  `waktu_mulai_mixing` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_selesai_mixing` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_formula` bigint(20) unsigned NOT NULL,
  `berat_sampling` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi_emulsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi_hasil_pencampuran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi_emulsi_oil` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`kode_produksi_emulsi_oil`)),
  `rework` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produk_rework_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`produk_rework_id`)),
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_bahan_forming_uuid_unique` (`uuid`),
  KEY `persiapan_bahan_forming_plan_id_foreign` (`plan_id`),
  KEY `persiapan_bahan_forming_user_id_foreign` (`user_id`),
  KEY `persiapan_bahan_forming_id_formula_foreign` (`id_formula`),
  KEY `persiapan_bahan_forming_shift_id_foreign` (`shift_id`),
  KEY `persiapan_bahan_forming_id_suhu_adonan_foreign` (`id_suhu_adonan`),
  KEY `persiapan_bahan_forming_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `persiapan_bahan_forming_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `persiapan_bahan_forming_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_bahan_forming_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_bahan_forming_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `persiapan_bahan_forming_id` bigint(20) unsigned NOT NULL,
  `persiapan_bahan_forming_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_bahan_forming_logs_uuid_unique` (`uuid`),
  KEY `persiapan_bahan_forming_logs_persiapan_bahan_forming_id_index` (`persiapan_bahan_forming_id`),
  KEY `persiapan_bahan_forming_logs_user_id_index` (`user_id`),
  KEY `persiapan_bahan_forming_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_bahan_non_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_bahan_non_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_no_formula_non_forming` bigint(20) unsigned NOT NULL,
  `berat_sampling` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time NOT NULL,
  `id_suhu_adonan` bigint(20) unsigned DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi_emulsi_oil` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`kode_produksi_emulsi_oil`)),
  `waktu_mulai_mixing` time DEFAULT NULL,
  `waktu_selesai_mixing` time DEFAULT NULL,
  `kondisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rework` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `produksi_approved_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `spv_approved_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_bahan_non_forming_uuid_unique` (`uuid`),
  KEY `persiapan_bahan_non_forming_plan_id_foreign` (`plan_id`),
  KEY `persiapan_bahan_non_forming_user_id_foreign` (`user_id`),
  KEY `persiapan_bahan_non_forming_id_no_formula_non_forming_foreign` (`id_no_formula_non_forming`),
  KEY `persiapan_bahan_non_forming_shift_id_foreign` (`shift_id`),
  KEY `persiapan_bahan_non_forming_id_suhu_adonan_foreign` (`id_suhu_adonan`),
  CONSTRAINT `persiapan_bahan_non_forming_id_no_formula_non_forming_foreign` FOREIGN KEY (`id_no_formula_non_forming`) REFERENCES `no_formula_non_forming` (`id`) ON DELETE CASCADE,
  CONSTRAINT `persiapan_bahan_non_forming_id_suhu_adonan_foreign` FOREIGN KEY (`id_suhu_adonan`) REFERENCES `suhu_adonan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `persiapan_bahan_non_forming_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `persiapan_bahan_non_forming_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `data_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `persiapan_bahan_non_forming_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_bahan_non_forming_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_bahan_non_forming_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_persiapan_bahan_non_forming` bigint(20) unsigned NOT NULL,
  `id_bahan_non_forming` bigint(20) unsigned NOT NULL,
  `suhu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_bahan_non_forming_detail_uuid_unique` (`uuid`),
  KEY `pb_nf_det_persiapan_fk` (`id_persiapan_bahan_non_forming`),
  KEY `pb_nf_det_bahan_fk` (`id_bahan_non_forming`),
  CONSTRAINT `pb_nf_det_bahan_fk` FOREIGN KEY (`id_bahan_non_forming`) REFERENCES `bahan_rm_non_forming` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pb_nf_det_persiapan_fk` FOREIGN KEY (`id_persiapan_bahan_non_forming`) REFERENCES `persiapan_bahan_non_forming` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `persiapan_cold_mixing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `persiapan_cold_mixing` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_suhu_adonan` bigint(20) unsigned NOT NULL,
  `rework` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_pemeriksaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `persiapan_cold_mixing_uuid_unique` (`uuid`),
  KEY `persiapan_cold_mixing_user_id_foreign` (`user_id`),
  KEY `persiapan_cold_mixing_id_plan_foreign` (`id_plan`),
  KEY `persiapan_cold_mixing_id_produk_foreign` (`id_produk`),
  KEY `persiapan_cold_mixing_id_suhu_adonan_foreign` (`id_suhu_adonan`),
  KEY `persiapan_cold_mixing_shift_id_foreign` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_plan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbrivation` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_uuid_unique` (`uuid`),
  KEY `plan_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produk_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_formula` bigint(20) unsigned DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `bahan_baku` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `bahan_penunjang` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `kemasan_plastik` varchar(255) DEFAULT NULL,
  `kemasan_karton` varchar(255) DEFAULT NULL,
  `labelisasi_plastik` varchar(255) DEFAULT NULL,
  `labelisasi_karton` varchar(255) DEFAULT NULL,
  `labelisasi_karton_verifikasi` varchar(255) DEFAULT NULL,
  `labelisasi_karton_tindakan` varchar(255) DEFAULT NULL,
  `labelisasi_plastik_verifikasi` varchar(255) DEFAULT NULL,
  `labelisasi_plastik_tindakan` varchar(255) DEFAULT NULL,
  `kemasan_karton_verifikasi` varchar(255) DEFAULT NULL,
  `kemasan_karton_tindakan` varchar(255) DEFAULT NULL,
  `kemasan_plastik_verifikasi` varchar(255) DEFAULT NULL,
  `kemasan_plastik_tindakan` varchar(255) DEFAULT NULL,
  `mesin_peralatan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mesin_peralatan`)),
  `ruangan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ruangan`)),
  `autogrind` int(11) DEFAULT NULL,
  `bowlcutter` int(11) DEFAULT NULL,
  `ayakan_seasoning` int(11) DEFAULT NULL,
  `unimix` int(11) DEFAULT NULL,
  `revoformer` int(11) DEFAULT NULL,
  `better_mixer` int(11) DEFAULT NULL,
  `wet_coater` int(11) DEFAULT NULL,
  `breader` int(11) DEFAULT NULL,
  `frayer_1` int(11) DEFAULT NULL,
  `frayer_2` int(11) DEFAULT NULL,
  `iqf_jbt` int(11) DEFAULT NULL,
  `keranjang` int(11) DEFAULT NULL,
  `timbangan` int(11) DEFAULT NULL,
  `mhw` int(11) DEFAULT NULL,
  `foot_sealer` int(11) DEFAULT NULL,
  `metal_detector` int(11) DEFAULT NULL,
  `rotary_table` int(11) DEFAULT NULL,
  `carton_sealer` int(11) DEFAULT NULL,
  `meatcar` int(11) DEFAULT NULL,
  `check_weigher_bag` int(11) DEFAULT NULL,
  `check_weigher_box` int(11) DEFAULT NULL,
  `penilaian` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifikasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_forming_uuid_unique` (`uuid`),
  KEY `produk_forming_id_plan_foreign` (`id_plan`),
  KEY `produk_forming_id_shift_foreign` (`id_shift`),
  KEY `produk_forming_user_id_foreign` (`user_id`),
  KEY `produk_forming_id_produk_id_plan_tanggal_index` (`id_produk`,`id_plan`,`tanggal`),
  KEY `produk_forming_uuid_index` (`uuid`),
  KEY `produk_forming_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `produk_forming_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `produk_forming_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produk_forming_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_forming_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_forming_id` bigint(20) unsigned NOT NULL,
  `produk_forming_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_forming_logs_uuid_unique` (`uuid`),
  KEY `produk_forming_logs_produk_forming_id_created_at_index` (`produk_forming_id`,`created_at`),
  KEY `produk_forming_logs_produk_forming_uuid_index` (`produk_forming_uuid`),
  KEY `produk_forming_logs_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produk_non_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_non_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_no_formula_non_forming` bigint(20) unsigned DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `bahan_baku` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `bahan_penunjang` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `kemasan_plastik` varchar(255) DEFAULT NULL,
  `kemasan_karton` varchar(255) DEFAULT NULL,
  `labelisasi_plastik` varchar(255) DEFAULT NULL,
  `labelisasi_karton` varchar(255) DEFAULT NULL,
  `labelisasi_karton_verifikasi` varchar(255) DEFAULT NULL,
  `labelisasi_karton_tindakan` varchar(255) DEFAULT NULL,
  `labelisasi_plastik_verifikasi` varchar(255) DEFAULT NULL,
  `labelisasi_plastik_tindakan` varchar(255) DEFAULT NULL,
  `kemasan_karton_verifikasi` varchar(255) DEFAULT NULL,
  `kemasan_karton_tindakan` varchar(255) DEFAULT NULL,
  `kemasan_plastik_verifikasi` varchar(255) DEFAULT NULL,
  `kemasan_plastik_tindakan` varchar(255) DEFAULT NULL,
  `tumbler` int(11) DEFAULT NULL,
  `frayer` int(11) DEFAULT NULL,
  `hicook` int(11) DEFAULT NULL,
  `iqf_advance_1` int(11) DEFAULT NULL,
  `iqf_advance_2` int(11) DEFAULT NULL,
  `keranjang` int(11) DEFAULT NULL,
  `palet` int(11) DEFAULT NULL,
  `meatcar` int(11) DEFAULT NULL,
  `timbangan` int(11) DEFAULT NULL,
  `mhw` int(11) DEFAULT NULL,
  `foot_sealer` int(11) DEFAULT NULL,
  `metal_detector` int(11) DEFAULT NULL,
  `check_weigher_bag` int(11) DEFAULT NULL,
  `check_weigher_box` int(11) DEFAULT NULL,
  `karton_sealer` int(11) DEFAULT NULL,
  `penilaian` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifikasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `mesin_peralatan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mesin_peralatan`)),
  `ruangan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ruangan`)),
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_non_forming_uuid_unique` (`uuid`),
  KEY `produk_non_forming_id_plan_foreign` (`id_plan`),
  KEY `produk_non_forming_id_shift_foreign` (`id_shift`),
  KEY `produk_non_forming_user_id_foreign` (`user_id`),
  KEY `produk_non_forming_id_produk_id_plan_id_shift_index` (`id_produk`,`id_plan`,`id_shift`),
  KEY `produk_non_forming_tanggal_index` (`tanggal`),
  KEY `produk_non_forming_kode_form_index` (`kode_form`),
  KEY `pnf_qc_approved_fk` (`qc_approved_by`),
  KEY `pnf_produksi_approved_fk` (`produksi_approved_by`),
  KEY `pnf_spv_approved_fk` (`spv_approved_by`),
  KEY `pnf_approval_status_idx` (`approved_by_qc`,`approved_by_produksi`,`approved_by_spv`),
  KEY `produk_non_forming_id_no_formula_non_forming_foreign` (`id_no_formula_non_forming`),
  CONSTRAINT `produk_non_forming_id_no_formula_non_forming_foreign` FOREIGN KEY (`id_no_formula_non_forming`) REFERENCES `no_formula_non_forming` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produk_non_forming_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_non_forming_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_non_forming_id` bigint(20) unsigned NOT NULL,
  `produk_non_forming_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_non_forming_logs_uuid_unique` (`uuid`),
  KEY `produk_non_forming_logs_produk_non_forming_id_created_at_index` (`produk_non_forming_id`,`created_at`),
  KEY `produk_non_forming_logs_produk_non_forming_uuid_index` (`produk_non_forming_uuid`),
  KEY `produk_non_forming_logs_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produk_yum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_yum` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_data_bag` bigint(20) unsigned NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_exp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_pcs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_pcs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_berat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_yum_uuid_unique` (`uuid`),
  KEY `produk_yum_id_produk_foreign` (`id_produk`),
  KEY `produk_yum_id_plan_foreign` (`id_plan`),
  KEY `produk_yum_shift_id_foreign` (`shift_id`),
  KEY `produk_yum_user_id_foreign` (`user_id`),
  KEY `produk_yum_id_data_bag_foreign` (`id_data_bag`),
  KEY `produk_yum_kode_form_index` (`kode_form`),
  KEY `produk_yum_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `produk_yum_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `produk_yum_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produk_yum_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk_yum_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_yum_id` bigint(20) unsigned NOT NULL,
  `produk_yum_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_yum_logs_uuid_unique` (`uuid`),
  KEY `produk_yum_logs_produk_yum_id_index` (`produk_yum_id`),
  KEY `produk_yum_logs_produk_yum_uuid_index` (`produk_yum_uuid`),
  KEY `produk_yum_logs_user_id_index` (`user_id`),
  KEY `produk_yum_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_aging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_aging` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `proses_tumbling_id` bigint(20) unsigned DEFAULT NULL,
  `proses_tumbling_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bahan_baku_tumbling_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_mulai_aging` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_selesai_aging` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `suhu_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kondisi_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `jam` time NOT NULL,
  `tanggal` date NOT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_aging_uuid_unique` (`uuid`),
  KEY `proses_aging_id_produk_foreign` (`id_produk`),
  KEY `proses_aging_id_plan_id_produk_index` (`id_plan`,`id_produk`),
  KEY `proses_aging_user_id_index` (`user_id`),
  KEY `pr_aging_pr_tumbling_id_foreign` (`proses_tumbling_id`),
  KEY `proses_aging_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `proses_aging_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `proses_aging_spv_approved_by_foreign` (`spv_approved_by`),
  CONSTRAINT `pr_aging_pr_tumbling_id_foreign` FOREIGN KEY (`proses_tumbling_id`) REFERENCES `proses_tumbling` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proses_aging_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proses_aging_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proses_aging_produksi_approved_by_foreign` FOREIGN KEY (`produksi_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_aging_qc_approved_by_foreign` FOREIGN KEY (`qc_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_aging_spv_approved_by_foreign` FOREIGN KEY (`spv_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_aging_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_battering`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_battering` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `kode_produksi_better` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_jenis_better` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_better` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses battering',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_battering_uuid_unique` (`uuid`),
  KEY `proses_battering_id_plan_foreign` (`id_plan`),
  KEY `proses_battering_user_id_foreign` (`user_id`),
  KEY `proses_battering_id_produk_foreign` (`id_produk`),
  KEY `proses_battering_id_jenis_better_foreign` (`id_jenis_better`),
  KEY `proses_battering_penggorengan_uuid_index` (`penggorengan_uuid`),
  KEY `proses_battering_predust_uuid_index` (`predust_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_battering_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_battering_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses_battering_id` bigint(20) unsigned NOT NULL,
  `proses_battering_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_battering_logs_uuid_unique` (`uuid`),
  KEY `proses_battering_logs_user_id_foreign` (`user_id`),
  KEY `proses_battering_logs_proses_battering_id_created_at_index` (`proses_battering_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_breader`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_breader` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_jenis_breader` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_breader` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses breader',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_breader_uuid_unique` (`uuid`),
  KEY `proses_breader_id_produk_foreign` (`id_produk`),
  KEY `proses_breader_user_id_foreign` (`user_id`),
  KEY `proses_breader_id_plan_foreign` (`id_plan`),
  KEY `proses_breader_battering_uuid_index` (`battering_uuid`),
  KEY `proses_breader_predust_uuid_index` (`predust_uuid`),
  KEY `proses_breader_penggorengan_uuid_index` (`penggorengan_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_breader_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_breader_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses_breader_id` bigint(20) unsigned NOT NULL,
  `proses_breader_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_breader_logs_uuid_unique` (`uuid`),
  KEY `proses_breader_logs_proses_breader_id_foreign` (`proses_breader_id`),
  KEY `proses_breader_logs_proses_breader_uuid_index` (`proses_breader_uuid`),
  KEY `proses_breader_logs_user_id_index` (`user_id`),
  KEY `proses_breader_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_frayer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_frayer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penggorengan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breader_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_waktu_penggorengan` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer_1` bigint(20) unsigned NOT NULL,
  `aktual_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktual_suhu_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpm_minyak` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ffa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses frayer',
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_frayer_uuid_unique` (`uuid`),
  KEY `proses_frayer_id_plan_foreign` (`id_plan`),
  KEY `proses_frayer_user_id_foreign` (`user_id`),
  KEY `proses_frayer_id_produk_foreign` (`id_produk`),
  KEY `proses_frayer_id_waktu_penggorengan_foreign` (`id_waktu_penggorengan`),
  KEY `proses_frayer_id_suhu_frayer_1_foreign` (`id_suhu_frayer_1`),
  KEY `proses_frayer_penggorengan_uuid_index` (`penggorengan_uuid`),
  KEY `proses_frayer_predust_uuid_index` (`predust_uuid`),
  KEY `proses_frayer_battering_uuid_index` (`battering_uuid`),
  KEY `proses_frayer_breader_uuid_index` (`breader_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_marinade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_marinade` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahan_baku_tumbling_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bahan_baku_tumbling_id` bigint(20) unsigned DEFAULT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `id_jenis_marinade` bigint(20) unsigned NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `tanggal` datetime NOT NULL,
  `hasil_pencampuran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_marinade_uuid_unique` (`uuid`),
  KEY `proses_marinade_id_shift_foreign` (`id_shift`),
  KEY `proses_marinade_id_plan_foreign` (`id_plan`),
  KEY `proses_marinade_id_user_foreign` (`id_user`),
  KEY `proses_marinade_id_jenis_marinade_foreign` (`id_jenis_marinade`),
  KEY `proses_marinade_bahan_baku_tumbling_uuid_index` (`bahan_baku_tumbling_uuid`),
  KEY `proses_marinade_bahan_baku_tumbling_id_index` (`bahan_baku_tumbling_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_marinade_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_marinade_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `proses_marinade_id` bigint(20) unsigned NOT NULL,
  `proses_marinade_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_marinade_logs_uuid_unique` (`uuid`),
  KEY `proses_marinade_logs_proses_marinade_id_index` (`proses_marinade_id`),
  KEY `proses_marinade_logs_proses_marinade_uuid_index` (`proses_marinade_uuid`),
  KEY `proses_marinade_logs_user_id_index` (`user_id`),
  KEY `proses_marinade_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_roasting_fan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_roasting_fan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_roasting_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `blok_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_grouped` tinyint(1) NOT NULL DEFAULT 0,
  `suhu_blok_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `std_fan_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `frayer_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frayer2_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `frayer3_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `frayer4_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `frayer5_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `breader_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `battering_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predust_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggorengan_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan roasting fan',
  `waktu_pemasakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `aktual_waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `aktual_suhu_fan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `aktual_speed_fan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `aktual_lama_proses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proses_roasting_fan_id_plan_foreign` (`id_plan`),
  KEY `proses_roasting_fan_user_id_foreign` (`user_id`),
  KEY `proses_roasting_fan_id_shift_foreign` (`id_shift`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_roasting_fan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_roasting_fan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses_roasting_fan_id` bigint(20) unsigned NOT NULL,
  `proses_roasting_fan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_roasting_fan_logs_uuid_unique` (`uuid`),
  KEY `proses_roasting_fan_logs_proses_roasting_fan_id_foreign` (`proses_roasting_fan_id`),
  KEY `proses_roasting_fan_logs_proses_roasting_fan_uuid_index` (`proses_roasting_fan_uuid`),
  KEY `proses_roasting_fan_logs_user_id_index` (`user_id`),
  KEY `proses_roasting_fan_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_tumbling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_tumbling` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(36) DEFAULT NULL,
  `bahan_baku_tumbling_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bahan_baku_tumbling_id` bigint(20) unsigned DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_tumbling` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `aktual_drum_on` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_drum_off` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_speed` varchar(255) DEFAULT NULL,
  `aktual_total_waktu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_vakum` varchar(255) DEFAULT NULL,
  `aktual_drum_on_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_drum_off_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_speed_non_vakum` varchar(255) DEFAULT NULL,
  `aktual_total_waktu_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktual_tekanan_non_vakum` varchar(255) DEFAULT NULL,
  `suhu_hasil_tumbling` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori_hasil_tumbling` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `waktu_mulai_tumbling` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_selesai_tumbling` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_mulai_tumbling_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_selesai_tumbling_non_vakum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Waktu pelaksanaan proses tumbling',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proses_tumbling_id_plan_foreign` (`id_plan`),
  KEY `proses_tumbling_user_id_foreign` (`user_id`),
  KEY `proses_tumbling_id_produk_foreign` (`id_produk`),
  KEY `proses_tumbling_id_tumbling_foreign` (`id_tumbling`),
  KEY `proses_tumbling_bahan_baku_tumbling_uuid_index` (`bahan_baku_tumbling_uuid`),
  KEY `proses_tumbling_bahan_baku_tumbling_id_index` (`bahan_baku_tumbling_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_tumbling_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_tumbling_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `proses_tumbling_id` bigint(20) unsigned NOT NULL,
  `proses_tumbling_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_tumbling_logs_uuid_unique` (`uuid`),
  KEY `proses_tumbling_logs_proses_tumbling_id_index` (`proses_tumbling_id`),
  KEY `proses_tumbling_logs_proses_tumbling_uuid_index` (`proses_tumbling_uuid`),
  KEY `proses_tumbling_logs_user_id_index` (`user_id`),
  KEY `proses_tumbling_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_twahing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_twahing` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(36) DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_qc_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv_at` timestamp NULL DEFAULT NULL,
  `approved_by_qc_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_produksi_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_spv_user_id` bigint(20) unsigned DEFAULT NULL,
  `waktu_thawing_awal` time DEFAULT NULL,
  `waktu_thawing_akhir` time DEFAULT NULL,
  `kondisi_kemasan_rm` enum('utuh','sobek') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_waktu_thawing_jam` decimal(8,2) DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_twahing_uuid_unique` (`uuid`),
  KEY `proses_twahing_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `proses_twahing_user_id_index` (`user_id`),
  KEY `proses_twahing_id_shift_index` (`id_shift`),
  KEY `proses_twahing_approved_by_qc_user_id_foreign` (`approved_by_qc_user_id`),
  KEY `proses_twahing_approved_by_produksi_user_id_foreign` (`approved_by_produksi_user_id`),
  KEY `proses_twahing_approved_by_spv_user_id_foreign` (`approved_by_spv_user_id`),
  KEY `proses_twahing_id_produk_foreign` (`id_produk`),
  KEY `proses_twahing_group_uuid_index` (`group_uuid`),
  CONSTRAINT `proses_twahing_approved_by_produksi_user_id_foreign` FOREIGN KEY (`approved_by_produksi_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_twahing_approved_by_qc_user_id_foreign` FOREIGN KEY (`approved_by_qc_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_twahing_approved_by_spv_user_id_foreign` FOREIGN KEY (`approved_by_spv_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_twahing_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proses_twahing_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proses_twahing_id_shift_foreign` FOREIGN KEY (`id_shift`) REFERENCES `data_shift` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `proses_twahing_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_twahing_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_twahing_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proses_twahing_id` bigint(20) unsigned NOT NULL,
  `id_rm` bigint(20) unsigned NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi_ruang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_pemeriksaan` time DEFAULT NULL,
  `suhu_rm` decimal(8,2) DEFAULT NULL,
  `suhu_ruang` decimal(8,2) DEFAULT NULL,
  `suhu_air_thawing` decimal(8,2) DEFAULT NULL,
  `suhu_produk` decimal(8,2) DEFAULT NULL,
  `kondisi_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_twahing_detail_uuid_unique` (`uuid`),
  KEY `proses_twahing_detail_proses_twahing_id_index` (`proses_twahing_id`),
  KEY `proses_twahing_detail_id_rm_index` (`id_rm`),
  CONSTRAINT `proses_twahing_detail_id_rm_foreign` FOREIGN KEY (`id_rm`) REFERENCES `data_rm` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `proses_twahing_detail_proses_twahing_id_foreign` FOREIGN KEY (`proses_twahing_id`) REFERENCES `proses_twahing` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_twahing_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proses_twahing_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `proses_twahing_id` bigint(20) unsigned NOT NULL,
  `proses_twahing_uuid` char(36) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_role` varchar(255) DEFAULT NULL,
  `aksi` varchar(255) NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_yang_diubah`)),
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nilai_lama`)),
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nilai_baru`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proses_twahing_logs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rebox_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rebox_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rebox_id` bigint(20) unsigned NOT NULL,
  `rebox_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rebox_logs_uuid_unique` (`uuid`),
  KEY `rebox_logs_rebox_id_index` (`rebox_id`),
  KEY `rebox_logs_user_id_index` (`user_id`),
  KEY `rebox_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reboxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reboxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `nama_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `best_before` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `isi_jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `labelisasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reboxes_uuid_unique` (`uuid`),
  KEY `reboxes_id_plan_foreign` (`id_plan`),
  KEY `reboxes_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `reboxes_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `reboxes_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `reboxes_created_by_foreign` (`created_by`),
  CONSTRAINT `reboxes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `release_produk_finish_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `release_produk_finish_goods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `suhu_iqf` decimal(8,2) NOT NULL,
  `suhu_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lama_pembekuan` int(11) NOT NULL,
  `foto_kemasan_sekunder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_kemasan_primer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi_kemasan_primer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_packing_sekunder` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_box` int(11) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `release_produk_finish_goods_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_uuid_unique` (`uuid`),
  KEY `roles_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seasoning_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seasoning_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seasoning_id` bigint(20) unsigned NOT NULL,
  `seasoning_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seasoning_logs_uuid_unique` (`uuid`),
  KEY `seasoning_logs_seasoning_id_index` (`seasoning_id`),
  KEY `seasoning_logs_user_id_index` (`user_id`),
  KEY `seasoning_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seasonings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seasonings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `nama_rm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `berat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sensori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kemasan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seasonings_uuid_unique` (`uuid`),
  KEY `seasonings_shift_id_foreign` (`shift_id`),
  KEY `seasonings_id_plan_foreign` (`id_plan`),
  KEY `seasonings_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `seasonings_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `seasonings_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `seasonings_created_by_foreign` (`created_by`),
  CONSTRAINT `seasonings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shoestring_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shoestring_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoestring_id` bigint(20) unsigned NOT NULL,
  `shoestring_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  `field_yang_diubah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_sampling` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shoestring_logs_uuid_unique` (`uuid`),
  KEY `shoestring_logs_shoestring_id_index` (`shoestring_id`),
  KEY `shoestring_logs_user_id_index` (`user_id`),
  KEY `shoestring_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shoestrings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shoestrings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(255) DEFAULT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `nama_produsen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `best_before` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_sampling` varchar(255) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `sampling_defect` text DEFAULT NULL,
  `sampling_defect_qty` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_defect` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dokumentasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dokumentasi`)),
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shoestrings_uuid_unique` (`uuid`),
  KEY `shoestrings_id_plan_foreign` (`id_plan`),
  KEY `shoestrings_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `shoestrings_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `shoestrings_spv_approved_by_foreign` (`spv_approved_by`),
  KEY `shoestrings_created_by_foreign` (`created_by`),
  CONSTRAINT `shoestrings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `std_berat_rheon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `std_berat_rheon` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `std_adonan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `std_filler` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_after_forming` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_after_frying` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_berat_rheon_uuid_unique` (`uuid`),
  KEY `std_berat_rheon_id_plan_foreign` (`id_plan`),
  KEY `std_berat_rheon_user_id_foreign` (`user_id`),
  KEY `std_berat_rheon_id_produk_foreign` (`id_produk`),
  CONSTRAINT `std_berat_rheon_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `std_berat_rheon_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `jenis_produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `std_berat_rheon_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `std_fan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `std_fan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_suhu_blok` bigint(20) unsigned NOT NULL,
  `std_fan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `std_fan_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fan_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fan_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_humadity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `std_lama_proses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_fan_uuid_unique` (`uuid`),
  KEY `std_fan_id_produk_foreign` (`id_produk`),
  KEY `std_fan_id_plan_foreign` (`id_plan`),
  KEY `std_fan_user_id_foreign` (`user_id`),
  KEY `std_fan_id_suhu_blok_foreign` (`id_suhu_blok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `std_salinitas_viskositas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `std_salinitas_viskositas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_better` bigint(20) unsigned NOT NULL,
  `std_viskositas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `std_salinitas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `std_suhu_akhir` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_salinitas_viskositas_uuid_unique` (`uuid`),
  KEY `std_salinitas_viskositas_user_id_foreign` (`user_id`),
  KEY `std_salinitas_viskositas_id_plan_foreign` (`id_plan`),
  KEY `std_salinitas_viskositas_id_produk_foreign` (`id_produk`),
  KEY `std_salinitas_viskositas_id_better_foreign` (`id_better`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `std_suhu_pusat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `std_suhu_pusat` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `std_suhu_pusat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_suhu_pusat_uuid_unique` (`uuid`),
  KEY `std_suhu_pusat_id_produk_foreign` (`id_produk`),
  KEY `std_suhu_pusat_user_id_foreign` (`user_id`),
  KEY `std_suhu_pusat_id_plan_foreign` (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `std_suhu_pusat_roasting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `std_suhu_pusat_roasting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `std_suhu_pusat_roasting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_suhu_pusat_roasting_uuid_unique` (`uuid`),
  KEY `std_suhu_pusat_roasting_id_produk_foreign` (`id_produk`),
  KEY `std_suhu_pusat_roasting_id_plan_id_produk_index` (`id_plan`,`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sub_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_area` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_input_area` bigint(20) unsigned NOT NULL,
  `lokasi_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_area_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suhu_adonan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suhu_adonan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `std_suhu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suhu_adonan_uuid_unique` (`uuid`),
  KEY `suhu_adonan_id_produk_foreign` (`id_produk`),
  KEY `suhu_adonan_id_plan_foreign` (`id_plan`),
  KEY `suhu_adonan_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suhu_blok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suhu_blok` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `suhu_blok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suhu_blok_uuid_unique` (`uuid`),
  KEY `suhu_blok_id_plan_foreign` (`id_plan`),
  KEY `suhu_blok_user_id_foreign` (`user_id`),
  KEY `suhu_blok_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suhu_emulsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suhu_emulsi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bahan_emulsi_id` bigint(20) unsigned NOT NULL,
  `pembuatan_emulsi_id` bigint(20) unsigned NOT NULL,
  `suhu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proses_ke` int(11) NOT NULL DEFAULT 1 COMMENT 'Nomor urut proses emulsi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suhu_emulsi_bahan_emulsi_id_foreign` (`bahan_emulsi_id`),
  KEY `suhu_emulsi_pembuatan_emulsi_id_foreign` (`pembuatan_emulsi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suhu_forming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suhu_forming` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_persiapan_bahan_forming` bigint(20) unsigned NOT NULL,
  `id_bahan_forming` bigint(20) unsigned NOT NULL,
  `suhu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_produksi_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sensori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suhu_forming_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suhu_frayer_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suhu_frayer_1` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `suhu_frayer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_penggorengan_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Waktu penggorengan  Frayer 1',
  `suhu_frayer_2` varchar(255) DEFAULT NULL COMMENT 'STD Suhu Frayer 2',
  `waktu_penggorengan_2` varchar(255) DEFAULT NULL COMMENT 'STD Waktu Penggorengan Frayer 2 (detik)',
  `suhu_frayer_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Suhu Frayer 3',
  `waktu_penggorengan_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Waktu penggorengan Frayer 3 ',
  `suhu_frayer_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Suhu Frayer 4',
  `waktu_penggorengan_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Waktu penggorengan  Frayer 4 ',
  `suhu_frayer_5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Suhu Frayer 5',
  `waktu_penggorengan_5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Waktu penggorengan  Frayer 5 ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suhu_frayer_1_uuid_unique` (`uuid`),
  KEY `suhu_frayer_1_user_id_foreign` (`user_id`),
  KEY `suhu_frayer_1_id_produk_foreign` (`id_produk`),
  KEY `suhu_frayer_1_id_plan_foreign` (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suhu_frayer_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suhu_frayer_2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `suhu_frayer_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_penggorengan_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Waktu penggorengan Frayer 2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suhu_frayer_2_uuid_unique` (`uuid`),
  KEY `suhu_frayer_2_uuid_index` (`uuid`),
  KEY `suhu_frayer_2_id_produk_index` (`id_produk`),
  KEY `suhu_frayer_2_id_plan_index` (`id_plan`),
  KEY `suhu_frayer_2_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `thermometer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `thermometer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `entries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`entries`)),
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_verifikasi_0` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_verifikasi_100` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_thermometer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_pengecekan` enum('ok','tidak_ok') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thermometer_uuid_unique` (`uuid`),
  KEY `thermometer_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `thermometer_user_id_index` (`user_id`),
  KEY `thermometer_shift_id_index` (`shift_id`),
  KEY `thermometer_kode_form_index` (`kode_form`),
  KEY `thermometer_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `thermometer_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `thermometer_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `thermometer_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `thermometer_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thermometer_id` bigint(20) unsigned NOT NULL,
  `thermometer_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `thermometer_logs_uuid_unique` (`uuid`),
  KEY `thermometer_logs_thermometer_id_idx` (`thermometer_id`),
  KEY `thermometer_logs_user_id_idx` (`user_id`),
  KEY `thermometer_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `timbangan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `timbangan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `entries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`entries`)),
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_timbangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_pengecekan` enum('ok','tidak_ok') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gram` enum('500','1000') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_verifikasi_500` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_verifikasi_1000` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hasil_verifikasi_10000` varchar(255) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timbangan_uuid_unique` (`uuid`),
  KEY `timbangan_id_plan_tanggal_index` (`id_plan`,`tanggal`),
  KEY `timbangan_user_id_index` (`user_id`),
  KEY `timbangan_shift_id_index` (`shift_id`),
  KEY `timbangan_kode_form_index` (`kode_form`),
  KEY `timbangan_qc_approved_by_foreign` (`qc_approved_by`),
  KEY `timbangan_produksi_approved_by_foreign` (`produksi_approved_by`),
  KEY `timbangan_spv_approved_by_foreign` (`spv_approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `timbangan_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `timbangan_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timbangan_id` bigint(20) unsigned NOT NULL,
  `timbangan_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timbangan_logs_uuid_unique` (`uuid`),
  KEY `timbangan_logs_timbangan_id_idx` (`timbangan_id`),
  KEY `timbangan_logs_user_id_idx` (`user_id`),
  KEY `timbangan_logs_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `total_pemakaian_emulsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `total_pemakaian_emulsi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_pemakaian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_emulsi_id` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `total_pemakaian_emulsi_uuid_unique` (`uuid`),
  KEY `total_pemakaian_emulsi_nama_emulsi_id_foreign` (`nama_emulsi_id`),
  KEY `total_pemakaian_emulsi_id_plan_foreign` (`id_plan`),
  KEY `total_pemakaian_emulsi_user_id_foreign` (`user_id`),
  KEY `total_pemakaian_emulsi_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_role` bigint(20) unsigned DEFAULT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `active_plan_id` bigint(20) unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `force_logout_after` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_id_plan_foreign` (`id_plan`),
  KEY `users_id_role_index` (`id_role`),
  KEY `users_active_plan_id_foreign` (`active_plan_id`),
  CONSTRAINT `users_active_plan_id_foreign` FOREIGN KEY (`active_plan_id`) REFERENCES `plan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verif_cip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verif_cip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` varchar(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_qc_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv_at` timestamp NULL DEFAULT NULL,
  `approved_by_qc_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_produksi_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_spv_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verif_cip_uuid_unique` (`uuid`),
  KEY `verif_cip_id_plan_index` (`id_plan`),
  KEY `verif_cip_user_id_index` (`user_id`),
  KEY `verif_cip_tanggal_index` (`tanggal`),
  KEY `verif_cip_approved_by_qc_user_id_foreign` (`approved_by_qc_user_id`),
  KEY `verif_cip_approved_by_produksi_user_id_foreign` (`approved_by_produksi_user_id`),
  KEY `verif_cip_approved_by_spv_user_id_foreign` (`approved_by_spv_user_id`),
  CONSTRAINT `verif_cip_approved_by_produksi_user_id_foreign` FOREIGN KEY (`approved_by_produksi_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `verif_cip_approved_by_qc_user_id_foreign` FOREIGN KEY (`approved_by_qc_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `verif_cip_approved_by_spv_user_id_foreign` FOREIGN KEY (`approved_by_spv_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verif_peralatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verif_peralatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_shift` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_qc_at` timestamp NULL DEFAULT NULL,
  `approved_by_produksi_at` timestamp NULL DEFAULT NULL,
  `approved_by_spv_at` timestamp NULL DEFAULT NULL,
  `approved_by_qc_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_produksi_user_id` bigint(20) unsigned DEFAULT NULL,
  `approved_by_spv_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kode_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verif_peralatan_uuid_unique` (`uuid`),
  KEY `verif_peralatan_user_id_foreign` (`user_id`),
  KEY `verif_peralatan_id_shift_foreign` (`id_shift`),
  KEY `verif_peralatan_id_plan_id_shift_tanggal_index` (`id_plan`,`id_shift`,`tanggal`),
  KEY `verif_peralatan_approved_by_qc_user_id_foreign` (`approved_by_qc_user_id`),
  KEY `verif_peralatan_approved_by_produksi_user_id_foreign` (`approved_by_produksi_user_id`),
  KEY `verif_peralatan_approved_by_spv_user_id_foreign` (`approved_by_spv_user_id`),
  CONSTRAINT `verif_peralatan_approved_by_produksi_user_id_foreign` FOREIGN KEY (`approved_by_produksi_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `verif_peralatan_approved_by_qc_user_id_foreign` FOREIGN KEY (`approved_by_qc_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `verif_peralatan_approved_by_spv_user_id_foreign` FOREIGN KEY (`approved_by_spv_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `verif_peralatan_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `verif_peralatan_id_shift_foreign` FOREIGN KEY (`id_shift`) REFERENCES `data_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `verif_peralatan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verif_peralatan_bagian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verif_peralatan_bagian` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `verif_peralatan_detail_id` bigint(20) unsigned NOT NULL,
  `id_bagian` bigint(20) unsigned NOT NULL,
  `verifikasi` tinyint(1) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `tindakan_koreksi` text DEFAULT NULL,
  `verifikasi_hasil` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vpb_detail` (`verif_peralatan_detail_id`),
  KEY `fk_vpb_bagian` (`id_bagian`),
  CONSTRAINT `fk_vpb_bagian` FOREIGN KEY (`id_bagian`) REFERENCES `bagian_mesin` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vpb_detail` FOREIGN KEY (`verif_peralatan_detail_id`) REFERENCES `verif_peralatan_detail` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verif_peralatan_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verif_peralatan_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `verif_peralatan_id` bigint(20) unsigned NOT NULL,
  `id_mesin` bigint(20) unsigned NOT NULL,
  `id_area` bigint(20) unsigned NOT NULL,
  `jam` time DEFAULT NULL,
  `verifikasi` tinyint(1) NOT NULL DEFAULT 0,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan_koreksi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifikasi_hasil` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vp_detail_mesin_unique` (`verif_peralatan_id`,`id_mesin`),
  KEY `verif_peralatan_detail_id_mesin_foreign` (`id_mesin`),
  KEY `verif_peralatan_detail_id_area_id_mesin_index` (`id_area`,`id_mesin`),
  CONSTRAINT `verif_peralatan_detail_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `input_area` (`id`) ON DELETE CASCADE,
  CONSTRAINT `verif_peralatan_detail_id_mesin_foreign` FOREIGN KEY (`id_mesin`) REFERENCES `input_mesin_peralatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `verif_peralatan_detail_verif_peralatan_id_foreign` FOREIGN KEY (`verif_peralatan_id`) REFERENCES `verif_peralatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verifikasi_berat_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verifikasi_berat_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `verifikasi_berat_id` bigint(20) unsigned NOT NULL,
  `verifikasi_berat_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_yang_diubah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nilai_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verifikasi_berat_logs_uuid_unique` (`uuid`),
  KEY `verifikasi_berat_logs_verifikasi_berat_id_created_at_index` (`verifikasi_berat_id`,`created_at`),
  KEY `verifikasi_berat_logs_verifikasi_berat_uuid_index` (`verifikasi_berat_uuid`),
  KEY `verifikasi_berat_logs_user_id_index` (`user_id`),
  KEY `verifikasi_berat_logs_aksi_index` (`aksi`),
  KEY `verifikasi_berat_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `verifikasi_berat_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verifikasi_berat_produk` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT 0,
  `source_uuid` char(36) DEFAULT NULL,
  `approved_by_qc` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_spv` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by_produksi` tinyint(1) NOT NULL DEFAULT 0,
  `qc_approved_by` bigint(20) unsigned DEFAULT NULL,
  `spv_approved_by` bigint(20) unsigned DEFAULT NULL,
  `produksi_approved_by` bigint(20) unsigned DEFAULT NULL,
  `qc_approved_at` timestamp NULL DEFAULT NULL,
  `spv_approved_at` timestamp NULL DEFAULT NULL,
  `produksi_approved_at` timestamp NULL DEFAULT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `jam` time DEFAULT NULL,
  `kode_produksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gramase` decimal(10,2) DEFAULT NULL,
  `jenis_produk_kfc` enum('KFC','non-KFC') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_breader` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_breader` decimal(8,2) DEFAULT NULL,
  `pickup_breader` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_total_breader` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `after_forming` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_after_forming` decimal(8,2) DEFAULT NULL,
  `berat_dry_kfc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_dry_kfc` decimal(8,2) DEFAULT NULL,
  `berat_wet_kfc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_wet_kfc` decimal(8,2) DEFAULT NULL,
  `pickup_after_forming_kfc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_predusting` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_predusting` decimal(8,2) DEFAULT NULL,
  `pickup_after_forming_predusting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_battering` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_battering` decimal(8,2) DEFAULT NULL,
  `pickup_after_predusting_battering` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_breadering` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_breadering` decimal(8,2) DEFAULT NULL,
  `pickup_after_battering_breadering` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_fryer_1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_fryer_1` decimal(8,2) DEFAULT NULL,
  `pickup_breadering_fryer_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_total` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_fryer_2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `rata_rata_fryer_2` decimal(8,2) DEFAULT NULL,
  `pickup_fryer_1_fryer_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_total_fryer_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_roasting` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`berat_roasting`)),
  `rata_rata_roasting` decimal(8,2) DEFAULT NULL,
  `pickup_after_breadering_roasting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `pickup_total_roasting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `hasil_adonan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `suhu_adonan` decimal(5,1) DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verifikasi_berat_produk_uuid_unique` (`uuid`),
  KEY `verifikasi_berat_produk_id_plan_foreign` (`id_plan`),
  KEY `verifikasi_berat_produk_user_id_foreign` (`user_id`),
  KEY `verifikasi_berat_produk_shift_id_foreign` (`shift_id`),
  KEY `verifikasi_berat_produk_id_produk_foreign` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `waktu_penggorengan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `waktu_penggorengan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer_1` bigint(20) unsigned NOT NULL,
  `waktu_penggorengan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `waktu_penggorengan_uuid_unique` (`uuid`),
  KEY `waktu_penggorengan_user_id_foreign` (`user_id`),
  KEY `waktu_penggorengan_id_produk_foreign` (`id_produk`),
  KEY `waktu_penggorengan_id_plan_foreign` (`id_plan`),
  KEY `waktu_penggorengan_id_suhu_frayer_1_foreign` (`id_suhu_frayer_1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `waktu_penggorengan_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `waktu_penggorengan_2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` bigint(20) unsigned NOT NULL,
  `id_plan` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `id_suhu_frayer_2` bigint(20) unsigned NOT NULL,
  `waktu_penggorengan_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `waktu_penggorengan_2_uuid_unique` (`uuid`),
  KEY `waktu_penggorengan_2_id_plan_foreign` (`id_plan`),
  KEY `waktu_penggorengan_2_id_suhu_frayer_2_foreign` (`id_suhu_frayer_2`),
  KEY `waktu_penggorengan_2_id_produk_id_plan_index` (`id_produk`,`id_plan`),
  KEY `waktu_penggorengan_2_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

/*M!999999\- enable the sandbox mode */ 
SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
INSERT INTO `migrations` VALUES (1,'2020_01_01_000000_create_users_table',1);
INSERT INTO `migrations` VALUES (2,'2020_01_01_000001_create_password_resets_table',1);
INSERT INTO `migrations` VALUES (3,'2020_01_01_000002_create_failed_jobs_table',1);
INSERT INTO `migrations` VALUES (4,'2020_01_01_000003_create_personal_access_tokens_table',1);
INSERT INTO `migrations` VALUES (5,'2020_01_01_000004_create_missing_legacy_tables',1);
INSERT INTO `migrations` VALUES (6,'2020_01_01_000005_drop_id_shift_from_multiple_tables',1);
INSERT INTO `migrations` VALUES (7,'2020_01_01_000006_force_drop_id_shift_columns',1);
INSERT INTO `migrations` VALUES (8,'2020_01_01_000007_drop_shift_id_columns',1);
INSERT INTO `migrations` VALUES (9,'2020_01_01_000008_create_verifikasi_berat_logs_table',1);
INSERT INTO `migrations` VALUES (10,'2020_01_01_000009_add_username_to_users_table',1);
INSERT INTO `migrations` VALUES (11,'2020_01_01_000010_create_proses_tumbling_logs_table',1);
INSERT INTO `migrations` VALUES (12,'2020_01_01_000011_create_dokumentasi_logs_table',1);
INSERT INTO `migrations` VALUES (13,'2020_01_01_000012_create_pembuatan_sample_logs_table',1);
INSERT INTO `migrations` VALUES (14,'2020_01_01_000013_create_pemeriksaan_produk_cooking_mixer_fla_logs_table',1);
INSERT INTO `migrations` VALUES (15,'2020_01_01_000014_create_pemeriksaan_rheon_machine_logs_table',1);
INSERT INTO `migrations` VALUES (16,'2020_01_01_000015_create_pemeriksaan_rice_bites_logs_table',1);
INSERT INTO `migrations` VALUES (17,'2020_01_01_000016_add_kode_form_to_proses_tumbling_table',1);
INSERT INTO `migrations` VALUES (18,'2020_01_01_000017_drop_line_blok_from_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (19,'2020_01_01_000018_add_block_number_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (20,'2020_01_01_000019_create_seasonings_table',1);
INSERT INTO `migrations` VALUES (21,'2020_01_01_000020_create_seasoning_logs_table',1);
INSERT INTO `migrations` VALUES (22,'2020_01_01_000021_add_kode_form_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (23,'2020_01_01_000022_add_approval_fields_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (24,'2020_01_01_000023_create_shoestrings_table',1);
INSERT INTO `migrations` VALUES (25,'2020_01_01_000024_create_shoestring_logs_table',1);
INSERT INTO `migrations` VALUES (26,'2020_01_01_000025_add_kode_form_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (27,'2020_01_01_000026_add_approval_fields_to_shoestring_table',1);
INSERT INTO `migrations` VALUES (28,'2020_01_01_000027_create_reboxes_table',1);
INSERT INTO `migrations` VALUES (29,'2020_01_01_000028_create_rebox_logs_table',1);
INSERT INTO `migrations` VALUES (30,'2020_01_01_000029_add_kode_form_to_reboxes_table',1);
INSERT INTO `migrations` VALUES (31,'2020_01_01_000030_add_approval_fields_to_rebox_table',1);
INSERT INTO `migrations` VALUES (32,'2020_01_01_000031_rename_best_before_to_tanggal_exp_in_shoestrings_table',1);
INSERT INTO `migrations` VALUES (33,'2020_01_01_000032_create_plan_table',1);
INSERT INTO `migrations` VALUES (34,'2020_01_01_000033_add_id_plan_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (35,'2020_01_01_000034_add_id_plan_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (36,'2020_01_01_000035_add_id_plan_to_reboxes_table',1);
INSERT INTO `migrations` VALUES (37,'2020_01_01_000036_create_input_area_table',1);
INSERT INTO `migrations` VALUES (38,'2020_01_01_000037_create_area_proses_table',1);
INSERT INTO `migrations` VALUES (39,'2020_01_01_000038_add_group_uuid_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (40,'2020_01_01_000039_create_data_barang_table',1);
INSERT INTO `migrations` VALUES (41,'2020_01_01_000040_create_jenis_produk_table',1);
INSERT INTO `migrations` VALUES (42,'2020_01_01_000041_create_jenis_predust_table',1);
INSERT INTO `migrations` VALUES (43,'2020_01_01_000042_create_pembuatan_predust_table',1);
INSERT INTO `migrations` VALUES (44,'2020_01_01_000043_create_std_suhu_pusat_roasting_table',1);
INSERT INTO `migrations` VALUES (45,'2020_01_01_000044_create_nomor_formula_table',1);
INSERT INTO `migrations` VALUES (46,'2020_01_01_000045_create_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (47,'2020_01_01_000046_add_uuid_and_idplan_to_users_table',1);
INSERT INTO `migrations` VALUES (48,'2020_01_01_000047_add_user_id_to_plan_table',1);
INSERT INTO `migrations` VALUES (49,'2020_01_01_000048_add_user_id_to_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (50,'2020_01_01_000049_add_user_id_to_jenis_produk_table',1);
INSERT INTO `migrations` VALUES (51,'2020_01_01_000050_add_user_id_to_nomor_formula_table',1);
INSERT INTO `migrations` VALUES (52,'2020_01_01_000051_create_jenis_emulsi_table',1);
INSERT INTO `migrations` VALUES (53,'2020_01_01_000052_create_total_pemakaian_emulsi_table',1);
INSERT INTO `migrations` VALUES (54,'2020_01_01_000053_create_nomor_emulsi_table',1);
INSERT INTO `migrations` VALUES (55,'2020_01_01_000054_create_bahan_emulsi_table',1);
INSERT INTO `migrations` VALUES (56,'2020_01_01_000055_create_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (57,'2020_01_01_000056_2025_01_04_add_kode_form_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (58,'2020_01_01_000057_add_suhu_to_bahan_emulsi_table',1);
INSERT INTO `migrations` VALUES (59,'2020_01_01_000058_create_suhu_emulsi_table',1);
INSERT INTO `migrations` VALUES (60,'2020_01_01_000059_create_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (61,'2020_01_01_000100_create_persiapan_bahan_forming_logs_table',1);
INSERT INTO `migrations` VALUES (62,'2020_01_01_000101_create_kode_produksi_emulsi_oil_json',1);
INSERT INTO `migrations` VALUES (63,'2020_01_01_000102_drop_kode_produksi_emulsi_oil_new',1);
INSERT INTO `migrations` VALUES (64,'2020_01_01_000103_create_suhu_forming_table',1);
INSERT INTO `migrations` VALUES (65,'2020_01_01_000104_create_jenis_better_table',1);
INSERT INTO `migrations` VALUES (66,'2020_01_01_000105_create_std_salinitas_viskositas_table',1);
INSERT INTO `migrations` VALUES (67,'2020_01_01_000106_create_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (68,'2020_01_01_000107_create_aktual_better_table',1);
INSERT INTO `migrations` VALUES (69,'2020_01_01_000108_add_kode_produksi_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (70,'2020_01_01_000109_add_kode_form_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (71,'2020_01_01_000110_create_suhu_adonan_table',1);
INSERT INTO `migrations` VALUES (72,'2020_01_01_000111_create_table_chillroom_add_nilai_rm',1);
INSERT INTO `migrations` VALUES (73,'2020_01_01_000112_create_chillroom_logs_table',1);
INSERT INTO `migrations` VALUES (74,'2020_01_01_000113_add_kode_form_to_penerimaan_chillroom_table',1);
INSERT INTO `migrations` VALUES (75,'2020_01_01_000114_create_persiapan_cold_mixing_table',1);
INSERT INTO `migrations` VALUES (76,'2020_01_01_000115_create_aktual_suhu_adonan',1);
INSERT INTO `migrations` VALUES (77,'2020_01_01_000116_add_suhu_air_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (78,'2020_01_01_000117_create_data_shift_table',1);
INSERT INTO `migrations` VALUES (79,'2020_01_01_000118_create_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (80,'2020_01_01_000119_create_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (81,'2020_01_01_000120_create_pemeriksaan_benda_asing_logs_table',1);
INSERT INTO `migrations` VALUES (82,'2020_01_01_000121_create_ketidaksesuaian_plastik_table',1);
INSERT INTO `migrations` VALUES (83,'2020_01_01_000122_add_approval_fields_to_ketidaksesuaian_plastik_table',1);
INSERT INTO `migrations` VALUES (84,'2020_01_01_000123_create_ketidaksesuaian_plastik_logs_table',1);
INSERT INTO `migrations` VALUES (85,'2020_01_01_000124_create_ketidaksesuaian_benda_asing_table',1);
INSERT INTO `migrations` VALUES (86,'2020_01_01_000125_add_approval_fields_to_ketidaksesuaian_benda_asing_table',1);
INSERT INTO `migrations` VALUES (87,'2020_01_01_000126_create_ketidaksesuaian_benda_asing_logs_table',1);
INSERT INTO `migrations` VALUES (88,'2020_01_01_000127_add_kode_form_to_ketidaksesuaian_plastik_table',1);
INSERT INTO `migrations` VALUES (89,'2020_01_01_000128_add_kode_form_to_ketidaksesuaian_benda_asing_table',1);
INSERT INTO `migrations` VALUES (90,'2020_01_01_000129_add_kode_form_to_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (91,'2020_01_01_000130_add_shift_id_and_modify_fields_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (92,'2020_01_01_000131_create_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (93,'2020_01_01_000132_create_kontrol_sanitasi_logs_table',1);
INSERT INTO `migrations` VALUES (94,'2020_01_01_000133_create_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (95,'2020_01_01_000134_create_barang_mudah_pecah_logs_table',1);
INSERT INTO `migrations` VALUES (96,'2020_01_01_000135_modify_barang_mudah_pecah_add_id_nama_barang',1);
INSERT INTO `migrations` VALUES (97,'2020_01_01_000136_add_kode_form_to_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (98,'2020_01_01_000137_add_kode_form_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (99,'2020_01_01_000138_add_approval_fields_to_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (100,'2020_01_01_000139_create_produk_forming_table',1);
INSERT INTO `migrations` VALUES (101,'2020_01_01_000140_create_produk_forming_logs_table',1);
INSERT INTO `migrations` VALUES (102,'2020_01_01_000141_create_pemeriksaan_rheon_machine_table',1);
INSERT INTO `migrations` VALUES (103,'2020_01_01_000142_add_approval_fields_to_pemeriksaan_rheon_machine_table',1);
INSERT INTO `migrations` VALUES (104,'2020_01_01_000143_add_kode_form_to_pemeriksaan_rheon_machine_table',1);
INSERT INTO `migrations` VALUES (105,'2020_01_01_000144_add_kode_form_to_produk_forming_table',1);
INSERT INTO `migrations` VALUES (106,'2020_01_01_000145_add_approval_fields_to_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (107,'2020_01_01_000146_remove_old_verification_columns_from_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (108,'2020_01_01_000147_create_penyimpanan_bahan_table',1);
INSERT INTO `migrations` VALUES (109,'2020_01_01_000148_add_user_id_to_penyimpanan_bahans_table',1);
INSERT INTO `migrations` VALUES (110,'2020_01_01_000149_add_plan_id_to_penyimpanan_bahans_table',1);
INSERT INTO `migrations` VALUES (111,'2020_01_01_000150_add_shiftid_tanggal_to_persiapan_cold_mixing_table',1);
INSERT INTO `migrations` VALUES (112,'2020_01_01_000151_add_shiftid_tanggal_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (113,'2020_01_01_000152_add_shiftid_tanggal_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (114,'2020_01_01_000153_add_approval_fields_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (115,'2020_01_01_000154_add_shiftid_tanggal_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (116,'2020_01_01_000155_add_approval_fields_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (117,'2020_01_01_000156_add_shift_id_and_tanggal_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (118,'2020_01_01_000157_create_penggorengan_table',1);
INSERT INTO `migrations` VALUES (119,'2020_01_01_000158_create_jenis_breader_table',1);
INSERT INTO `migrations` VALUES (120,'2020_01_01_000159_add_shift_id_and_tanggal_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (121,'2020_01_01_000200_create_proses_breader_table',1);
INSERT INTO `migrations` VALUES (122,'2020_01_01_000201_add_shift_id_and_tanggal_to_reboxes_table',1);
INSERT INTO `migrations` VALUES (123,'2020_01_01_000202_create_suhu_frayer_1_table',1);
INSERT INTO `migrations` VALUES (124,'2020_01_01_000203_create_waktu_penggorengan_table',1);
INSERT INTO `migrations` VALUES (125,'2020_01_01_000204_create_frayer_5_table',1);
INSERT INTO `migrations` VALUES (126,'2020_01_01_000205_create_jenis_marinade_table',1);
INSERT INTO `migrations` VALUES (127,'2020_01_01_000206_create_input_roasting_table',1);
INSERT INTO `migrations` VALUES (128,'2020_01_01_000207_create_bahan_baku_roasting_table',1);
INSERT INTO `migrations` VALUES (129,'2020_01_01_000208_add_tanggal_to_bahan_baku_roasting_table',1);
INSERT INTO `migrations` VALUES (130,'2020_01_01_000209_create_bahan_baku_tumbling',1);
INSERT INTO `migrations` VALUES (131,'2020_01_01_000210_create_bahan_baku_tumbling_logs_table',1);
INSERT INTO `migrations` VALUES (132,'2020_01_01_000211_add_kode_form_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (133,'2020_01_01_000212_add_shift_id_and_tanggal_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (134,'2020_01_01_000213_create_suhu_blok_table',1);
INSERT INTO `migrations` VALUES (135,'2020_01_01_000214_create_std_fan_table',1);
INSERT INTO `migrations` VALUES (136,'2020_01_01_000215_create_std_lama_proses_table',1);
INSERT INTO `migrations` VALUES (137,'2020_01_01_000216_pengemasan_karton_table',1);
INSERT INTO `migrations` VALUES (138,'2020_01_01_000217_create_pengemasan_karton_logs_table',1);
INSERT INTO `migrations` VALUES (139,'2020_01_01_000218_create_std_suhu_pusat_table',1);
INSERT INTO `migrations` VALUES (140,'2020_01_01_000219_create_proses_battering_table',1);
INSERT INTO `migrations` VALUES (141,'2020_01_01_000220_create_data_gramasi_table',1);
INSERT INTO `migrations` VALUES (142,'2020_01_01_000221_create_data_bag_table',1);
INSERT INTO `migrations` VALUES (143,'2020_01_01_000222_create_produk_yum_table',1);
INSERT INTO `migrations` VALUES (144,'2020_01_01_000223_create_data_box_table',1);
INSERT INTO `migrations` VALUES (145,'2020_01_01_000224_create_produk_yum_logs_table',1);
INSERT INTO `migrations` VALUES (146,'2020_01_01_000225_create_proses_frayer_table',1);
INSERT INTO `migrations` VALUES (147,'2020_01_01_000226_add_tanggal_to_proses_frayer_table',1);
INSERT INTO `migrations` VALUES (148,'2020_01_01_000227_create_frayer_2_table',1);
INSERT INTO `migrations` VALUES (149,'2020_01_01_000228_create_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (150,'2020_01_01_000229_create_hasil_proses_roasting_table',1);
INSERT INTO `migrations` VALUES (151,'2020_01_01_000230_create_data_tumbling_table',1);
INSERT INTO `migrations` VALUES (152,'2020_01_01_000231_create_proses_marinade_table',1);
INSERT INTO `migrations` VALUES (153,'2020_01_01_000232_ensure_tumbling_relations_fields',1);
INSERT INTO `migrations` VALUES (154,'2020_01_01_000233_create_proses_marinade_logs_table',1);
INSERT INTO `migrations` VALUES (155,'2020_01_01_000234_add_kode_form_to_proses_marinade_table',1);
INSERT INTO `migrations` VALUES (156,'2020_01_01_000235_create_permission_tables',1);
INSERT INTO `migrations` VALUES (157,'2020_01_01_000236_create_proses_roasting_fan_2_table',1);
INSERT INTO `migrations` VALUES (158,'2020_01_01_000237_add_std_fan_2_to_std_fan_table',1);
INSERT INTO `migrations` VALUES (159,'2020_01_01_000238_create_input_metal_detector',1);
INSERT INTO `migrations` VALUES (160,'2020_01_01_000239_create_input_metal_detector_logs_table',1);
INSERT INTO `migrations` VALUES (161,'2020_01_01_000240_add_keterangan_to_input_metal_detector_table',1);
INSERT INTO `migrations` VALUES (162,'2020_01_01_000241_add_approval_fields_to_input_metal_detector_table',1);
INSERT INTO `migrations` VALUES (163,'2020_01_01_000242_create_berat_produk_bag_table',1);
INSERT INTO `migrations` VALUES (164,'2020_01_01_000243_create_berat_produk_bag_logs_table',1);
INSERT INTO `migrations` VALUES (165,'2020_01_01_000244_create_berat_produk_box_table',1);
INSERT INTO `migrations` VALUES (166,'2020_01_01_000245_create_berat_produk_box_logs_table',1);
INSERT INTO `migrations` VALUES (167,'2020_01_01_000246_add_line_to_berat_produk_bag_table',1);
INSERT INTO `migrations` VALUES (168,'2020_01_01_000247_create_pengemasan_produk_table',1);
INSERT INTO `migrations` VALUES (169,'2020_01_01_000248_create_pengemasan_produk_logs_table',1);
INSERT INTO `migrations` VALUES (170,'2020_01_01_000249_create_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (171,'2020_01_01_000250_add_status_bahan_jenis_produk_table',1);
INSERT INTO `migrations` VALUES (172,'2020_01_01_000251_create_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (173,'2020_01_01_000252_add_approval_fields_to_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (174,'2020_01_01_000253_add_user_id_to_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (175,'2020_01_01_000254_create_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (176,'2020_01_01_000255_create_pengemasan_plastik_logs_table',1);
INSERT INTO `migrations` VALUES (177,'2020_01_01_000256_add_berat_to_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (178,'2020_01_01_000257_add_group_uuid_to_penyimpanan_bahan_table',1);
INSERT INTO `migrations` VALUES (179,'2020_01_01_000258_drop_suhu_ruang_from_penyimpanan_bahan_table',1);
INSERT INTO `migrations` VALUES (180,'2020_01_01_000259_add_id_pengemasan_produk_to_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (181,'2020_01_01_000300_add_id_pengemasan_plastik_to_berat_produk_bag_table',1);
INSERT INTO `migrations` VALUES (182,'2020_01_01_000301_add_line_blok_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (183,'2020_01_01_000302_add_kode_form_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (184,'2020_01_01_000303_add_approval_fields_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (185,'2020_01_01_000304_drop_id_produk_from_berat_produk_box_table',1);
INSERT INTO `migrations` VALUES (186,'2020_01_01_000305_add_relations_to_berat_produk_box_table',1);
INSERT INTO `migrations` VALUES (187,'2020_01_01_000306_add_berat_to_data_bag_table',1);
INSERT INTO `migrations` VALUES (188,'2020_01_01_000307_add_berat_to_data_box_table',1);
INSERT INTO `migrations` VALUES (189,'2020_01_01_000308_create_pembuatan_emulsi_logs_table',1);
INSERT INTO `migrations` VALUES (190,'2020_01_01_000309_add_kolom_berat_to_pengemasan_produk',1);
INSERT INTO `migrations` VALUES (191,'2020_01_01_000310_add_softdeletes_to_users_table',1);
INSERT INTO `migrations` VALUES (192,'2020_01_01_000311_create_persiapan_bahan_better_logs_table',1);
INSERT INTO `migrations` VALUES (193,'2020_01_01_000312_update_fk_id_pengemasan_produk_on_berat_produk_bag_table',1);
INSERT INTO `migrations` VALUES (194,'2020_01_01_000313_update_pengemasan_karton_table_for_new_columns',1);
INSERT INTO `migrations` VALUES (195,'2020_01_01_000314_update_dokumentasi_table_for_pengemasan_karton_and_produk',1);
INSERT INTO `migrations` VALUES (196,'2020_01_01_000315_add_kode_form_to_pengemasan_produk',1);
INSERT INTO `migrations` VALUES (197,'2020_01_01_000316_add_kolom_user_id_to_pengemasan_produk',1);
INSERT INTO `migrations` VALUES (198,'2020_01_01_000317_create_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (199,'2020_01_01_000318_create_gmp_karyawan_logs_table',1);
INSERT INTO `migrations` VALUES (200,'2020_01_01_000319_add_kode_form_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (201,'2020_01_01_000320_add_kolom_user_id_to_berat_produk_bag',1);
INSERT INTO `migrations` VALUES (202,'2020_01_01_000321_update_gmp_karyawan_temuan_enum',1);
INSERT INTO `migrations` VALUES (203,'2020_01_01_000322_create_pemeriksaan_proses_produksi_table',1);
INSERT INTO `migrations` VALUES (204,'2020_01_01_000323_create_pemeriksaan_proses_produksi_logs_table',1);
INSERT INTO `migrations` VALUES (205,'2020_01_01_000324_add_kode_form_to_pemeriksaan_proses_produksi_table',1);
INSERT INTO `migrations` VALUES (206,'2020_01_01_000325_add_approval_fields_to_pemeriksaan_proses_produksi_table',1);
INSERT INTO `migrations` VALUES (207,'2020_01_01_000326_create_timbangan_table',1);
INSERT INTO `migrations` VALUES (208,'2020_01_01_000327_create_timbangan_logs_table',1);
INSERT INTO `migrations` VALUES (209,'2020_01_01_000328_add_kode_form_to_timbangan_table',1);
INSERT INTO `migrations` VALUES (210,'2020_01_01_000329_create_thermometer_table',1);
INSERT INTO `migrations` VALUES (211,'2020_01_01_000330_create_thermometer_logs_table',1);
INSERT INTO `migrations` VALUES (212,'2020_01_01_000331_add_kode_form_to_thermometer_table',1);
INSERT INTO `migrations` VALUES (213,'2020_01_01_000332_add_approval_fields_to_thermometer_table',1);
INSERT INTO `migrations` VALUES (214,'2020_01_01_000333_add_kolom_user_id_to_berat_produk_box',1);
INSERT INTO `migrations` VALUES (215,'2020_01_01_000334_create_roles_table',1);
INSERT INTO `migrations` VALUES (216,'2020_01_01_000335_add_id_role_to_users_table',1);
INSERT INTO `migrations` VALUES (217,'2020_01_01_000336_create_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (218,'2020_01_01_000337_add_kode_form_to_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (219,'2020_01_01_000338_add_approval_fields_to_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (220,'2020_01_01_000339_create_produk_non_forming_logs_table',1);
INSERT INTO `migrations` VALUES (221,'2020_01_01_000340_create_nama_formula_fla_table',1);
INSERT INTO `migrations` VALUES (222,'2020_01_01_000341_remove_shift_id_from_nama_formula_fla_table',1);
INSERT INTO `migrations` VALUES (223,'2020_01_01_000342_create_nomor_step_formula_fla_table',1);
INSERT INTO `migrations` VALUES (224,'2020_01_01_000343_create_bahan_formula_fla_table',1);
INSERT INTO `migrations` VALUES (225,'2020_01_01_000344_add_penggorengan_uuid_to_pembuatan_predust_table',1);
INSERT INTO `migrations` VALUES (226,'2020_01_01_000345_add_penggorengan_uuid_to_proses_battering_table',1);
INSERT INTO `migrations` VALUES (227,'2020_01_01_000346_add_waktu_selesai_pemasakan_to_penggorengan_table',1);
INSERT INTO `migrations` VALUES (228,'2020_01_01_000347_add_battering_uuid_to_pembuatan_predust_table',1);
INSERT INTO `migrations` VALUES (229,'2020_01_01_000348_remove_battering_uuid_from_pembuatan_predust_table',1);
INSERT INTO `migrations` VALUES (230,'2020_01_01_000349_add_predust_uuid_to_proses_battering_table',1);
INSERT INTO `migrations` VALUES (231,'2020_01_01_000350_add_battering_uuid_to_proses_breader_table',1);
INSERT INTO `migrations` VALUES (232,'2020_01_01_000351_create_pemeriksaan_produk_cooking_mixer_fla_table',1);
INSERT INTO `migrations` VALUES (233,'2020_01_01_000352_add_approval_fields_to_pemeriksaan_produk_cooking_mixer_fla_table',1);
INSERT INTO `migrations` VALUES (234,'2020_01_01_000353_create_suhu_frayer2s_table',1);
INSERT INTO `migrations` VALUES (235,'2020_01_01_000354_create_waktu_penggorengan_2_table',1);
INSERT INTO `migrations` VALUES (236,'2020_01_01_000355_add_organo_rasa_to_pemeriksaan_produk_cooking_mixer_fla_table',1);
INSERT INTO `migrations` VALUES (237,'2020_01_01_000356_update_frayer_2_table_fields',1);
INSERT INTO `migrations` VALUES (238,'2020_01_01_000357_create_frayer_3_table',1);
INSERT INTO `migrations` VALUES (239,'2020_01_01_000358_create_pemasakan_nasi_table',1);
INSERT INTO `migrations` VALUES (240,'2020_01_01_000359_create_pemasakan_nasi_logs_table',1);
INSERT INTO `migrations` VALUES (241,'2020_01_01_000400_add_approval_fields_to_pemasakan_nasi_table',1);
INSERT INTO `migrations` VALUES (242,'2020_01_01_000401_add_kode_form_to_pemasakan_nasi_table',1);
INSERT INTO `migrations` VALUES (243,'2020_01_01_000402_create_frayer_4_table',1);
INSERT INTO `migrations` VALUES (244,'2020_01_01_000403_add_uuid_relations_to_proses_frayer_table',1);
INSERT INTO `migrations` VALUES (245,'2020_01_01_000404_add_frayer_uuid_to_frayer_2_table',1);
INSERT INTO `migrations` VALUES (246,'2020_01_01_000405_add_uuid_relations_to_frayer_3_table',1);
INSERT INTO `migrations` VALUES (247,'2020_01_01_000406_add_uuid_relations_to_frayer_4_table',1);
INSERT INTO `migrations` VALUES (248,'2020_01_01_000407_add_uuid_relations_to_frayer_5_table',1);
INSERT INTO `migrations` VALUES (249,'2020_01_01_000408_add_uuid_relations_to_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (250,'2020_01_01_000409_add_frayer2_uuid_to_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (251,'2020_01_01_000410_create_pembekuan_iqf_penggorengans_table',1);
INSERT INTO `migrations` VALUES (252,'2020_01_01_000411_drop_outlet_cvg_from_pemeriksaan_rheon_machine_table',1);
INSERT INTO `migrations` VALUES (253,'2020_01_01_000412_add_std_lama_proses_to_std_fan_table',1);
INSERT INTO `migrations` VALUES (254,'2020_01_01_000413_add_aktual_lama_proses_to_proses_roasting_fan_tables',1);
INSERT INTO `migrations` VALUES (255,'2020_01_01_000414_create_penggorengan_logs_table',1);
INSERT INTO `migrations` VALUES (256,'2020_01_01_000415_create_pembuatan_predust_logs_table',1);
INSERT INTO `migrations` VALUES (257,'2020_01_01_000416_create_proses_battering_logs_table',1);
INSERT INTO `migrations` VALUES (258,'2020_01_01_000417_create_proses_breader_logs_table',1);
INSERT INTO `migrations` VALUES (259,'2020_01_01_000418_create_hasil_penggorengan_logs_table',1);
INSERT INTO `migrations` VALUES (260,'2020_01_01_000419_create_pembekuan_iqf_penggorengan_logs_table',1);
INSERT INTO `migrations` VALUES (261,'2020_01_01_000420_create_input_roasting_logs_table',1);
INSERT INTO `migrations` VALUES (262,'2020_01_01_000421_create_bahan_baku_roasting_logs_table',1);
INSERT INTO `migrations` VALUES (263,'2020_01_01_000422_create_proses_roasting_fan_logs_table',1);
INSERT INTO `migrations` VALUES (264,'2020_01_01_000423_create_hasil_proses_roasting_logs_table',1);
INSERT INTO `migrations` VALUES (265,'2020_01_01_000424_drop_std_lama_proses_table',1);
INSERT INTO `migrations` VALUES (266,'2020_01_01_000425_add_fan3_fan4_std_humadity_to_std_fan_table',1);
INSERT INTO `migrations` VALUES (267,'2020_01_01_000426_add_actual_fields_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (268,'2020_01_01_000427_delete_column_suhu_seasoning',1);
INSERT INTO `migrations` VALUES (269,'2020_01_01_000428_drop_data_gramasi_table',1);
INSERT INTO `migrations` VALUES (270,'2020_01_01_000429_drop_proses_roasting_fan_2_table',1);
INSERT INTO `migrations` VALUES (271,'2020_01_01_000430_add_uuid_fields_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (272,'2020_01_01_000431_refactor_proses_roasting_fan_structure',1);
INSERT INTO `migrations` VALUES (273,'2020_01_01_000432_add_blok_data_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (274,'2020_01_01_000433_swap_timbangan_thermometer_tables',1);
INSERT INTO `migrations` VALUES (275,'2020_01_01_000434_add_approval_fields_to_timbangan_table',1);
INSERT INTO `migrations` VALUES (276,'2020_01_01_000435_add_uuid_fields_to_hasil_proses_roasting_table',1);
INSERT INTO `migrations` VALUES (277,'2020_01_01_000436_create_pembekuan_iqf_roasting_table',1);
INSERT INTO `migrations` VALUES (278,'2020_01_01_000437_add_input_roasting_uuid_to_bahan_baku_roasting_table',1);
INSERT INTO `migrations` VALUES (279,'2020_01_01_000438_add_uuid_fields_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (280,'2020_01_01_000439_add_input_and_bahan_baku_uuid_to_hasil_proses_roasting_table',1);
INSERT INTO `migrations` VALUES (281,'2020_01_01_000440_add_uuid_fields_to_pembekuan_iqf_roasting_table',1);
INSERT INTO `migrations` VALUES (282,'2020_01_01_000441_remove_duplicate_uuid_columns_from_pembekuan_iqf_roasting_table',1);
INSERT INTO `migrations` VALUES (283,'2020_01_01_000442_add_kondisi_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (284,'2020_01_01_000443_add_aktual_suhu_penggorengan_to_proses_frayer_table',1);
INSERT INTO `migrations` VALUES (285,'2020_01_01_000444_add_aktual_suhu_penggorengan_to_frayer_2_table',1);
INSERT INTO `migrations` VALUES (286,'2020_01_01_000445_add_aktual_suhu_penggorengan_to_frayer_3_table',1);
INSERT INTO `migrations` VALUES (287,'2020_01_01_000446_add_aktual_suhu_penggorengan_to_frayer_4_table',1);
INSERT INTO `migrations` VALUES (288,'2020_01_01_000447_add_aktual_suhu_penggorengan_to_frayer_5_table',1);
INSERT INTO `migrations` VALUES (289,'2020_01_01_000448_add_kode_form_to_pembekuan_iqf_penggorengan_table',1);
INSERT INTO `migrations` VALUES (290,'2020_01_01_000449_add_kode_produksi_better_to_proses_battering_table',1);
INSERT INTO `migrations` VALUES (291,'2020_01_01_000450_add_berat_produk_to_penggorengan_table',1);
INSERT INTO `migrations` VALUES (292,'2020_01_01_000451_2025_10_04_014234_add_berat_produk_to_input_roasting_table',1);
INSERT INTO `migrations` VALUES (293,'2020_01_01_000452_add_kode_form_to_pembekuan_iqf_roasting_table',1);
INSERT INTO `migrations` VALUES (294,'2020_01_01_000453_add_approval_fields_to_penerimaan_chillroom_table',1);
INSERT INTO `migrations` VALUES (295,'2020_01_01_000454_add_kode_form_to_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (296,'2020_01_01_000455_add_approval_fields_to_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (297,'2020_01_01_000456_add_approval_fields_to_produk_forming_table',1);
INSERT INTO `migrations` VALUES (298,'2020_01_01_000457_add_kode_form_to_produk_yum_table',1);
INSERT INTO `migrations` VALUES (299,'2020_01_01_000458_add_rata_rata_berat_to_berat_produk_bag_table',1);
INSERT INTO `migrations` VALUES (300,'2020_01_01_000459_add_rata_rata_berat_to_berat_produk_box_table',1);
INSERT INTO `migrations` VALUES (301,'2020_01_01_000500_add-approval-fields-to-produk-yum',1);
INSERT INTO `migrations` VALUES (302,'2020_01_01_000501_drop_waktu_selesai_pemasakan_from_penggorengan_table',1);
INSERT INTO `migrations` VALUES (303,'2020_01_01_000502_add_approval_fields_to_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (304,'2020_01_01_000503_add_proses_ke_to_suhu_emulsi_table',1);
INSERT INTO `migrations` VALUES (305,'2020_01_01_000504_add_approval_columns_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (306,'2020_01_01_000505_add_approval_columns_to_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (307,'2020_01_01_000506_add_approval_columns_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (308,'2020_01_01_000507_add_kode_form_to_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (309,'2020_01_01_000508_add_approval_fields_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (310,'2020_01_01_000509_add_kode_form_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (311,'2020_01_01_000510_add_approval_fields_to_pembekuan_iqf_roasting_table',1);
INSERT INTO `migrations` VALUES (312,'2020_01_01_000511_add_additional_suhu_frayer_columns_to_suhu_frayer_1_table',1);
INSERT INTO `migrations` VALUES (313,'2020_01_01_000512_add_kode_exp_to_produk_yum_table',1);
INSERT INTO `migrations` VALUES (314,'2020_01_01_000513_add_kode_produksi_bahan_to_suhu_forming_table',1);
INSERT INTO `migrations` VALUES (315,'2020_01_01_000514_drop_total_pemakaian_column_from_total_pemakaian_emulsi_table',1);
INSERT INTO `migrations` VALUES (316,'2020_01_01_000515_add_total_pemakaian_string_to_total_pemakaian_emulsi_table',1);
INSERT INTO `migrations` VALUES (317,'2020_01_01_000516_add_kode_produksi_bahan_to_suhu_emulsi_table',1);
INSERT INTO `migrations` VALUES (318,'2020_01_01_000517_create_data_timbangan_table',1);
INSERT INTO `migrations` VALUES (319,'2020_01_01_000518_create_data_thermo_table',1);
INSERT INTO `migrations` VALUES (320,'2020_01_01_000519_create_data_rm_table',1);
INSERT INTO `migrations` VALUES (321,'2020_01_01_000520_create_data_defect_table',1);
INSERT INTO `migrations` VALUES (322,'2020_01_01_000521_add_line_to_input_metal_detector_table',1);
INSERT INTO `migrations` VALUES (323,'2020_01_01_000522_create_data_seasoning_table',1);
INSERT INTO `migrations` VALUES (324,'2020_01_01_000523_add_jam_to_pengemasan_produk_table',1);
INSERT INTO `migrations` VALUES (325,'2020_01_01_000524_add_jam_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (326,'2020_01_01_000525_add_jam_to_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (327,'2020_01_01_000526_add_jam_to_berat_produk_bag_box_table',1);
INSERT INTO `migrations` VALUES (328,'2020_01_01_000527_add_jam_to_berat_produk_box_table',1);
INSERT INTO `migrations` VALUES (329,'2020_01_01_000528_add_jam_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (330,'2020_01_01_000529_add_jam_to_reboxes_table',1);
INSERT INTO `migrations` VALUES (331,'2020_01_01_000530_add_jam_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (332,'2020_01_01_000531_add_jam_to_pengemasan_karton_table',1);
INSERT INTO `migrations` VALUES (333,'2020_01_01_000532_add_jam_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (334,'2020_01_01_000533_add_jam_to_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (335,'2020_01_01_000534_add_jam_to_input_metal_detector_table',1);
INSERT INTO `migrations` VALUES (336,'2020_01_01_000535_add_jam_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (337,'2020_01_01_000536_add_jam_to_pembuatan_sample',1);
INSERT INTO `migrations` VALUES (338,'2020_01_01_000537_add_jam_to_produk_yum_table',1);
INSERT INTO `migrations` VALUES (339,'2020_01_01_000538_add_jam_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (340,'2020_01_01_000539_add_jam_to_penggorengan_table',1);
INSERT INTO `migrations` VALUES (341,'2020_01_01_000540_add_jam_to_pembuatan_predust_table',1);
INSERT INTO `migrations` VALUES (342,'2020_01_01_000541_add_jam_to_proses_battering_table',1);
INSERT INTO `migrations` VALUES (343,'2020_01_01_000542_add_jam_to_proses_breader_table',1);
INSERT INTO `migrations` VALUES (344,'2020_01_01_000543_add_jam_to_proses_frayer_table',1);
INSERT INTO `migrations` VALUES (345,'2020_01_01_000544_add_jam_to_frayer_2_table',1);
INSERT INTO `migrations` VALUES (346,'2020_01_01_000545_add_jam_to_pemeriksaan_cooking_mixer_fla',1);
INSERT INTO `migrations` VALUES (347,'2020_01_01_000546_add_jam_to_frayer_3_table',1);
INSERT INTO `migrations` VALUES (348,'2020_01_01_000547_add_jam_to_frayer_4_table',1);
INSERT INTO `migrations` VALUES (349,'2020_01_01_000548_add_jam_to_frayer_5_table',1);
INSERT INTO `migrations` VALUES (350,'2020_01_01_000549_add_jam_to_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (351,'2020_01_01_000550_add_jam_to_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (352,'2020_01_01_000551_add_jam_to_input_roasting_table',1);
INSERT INTO `migrations` VALUES (353,'2020_01_01_000552_add_jam_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (354,'2020_01_01_000553_add_jam_to_hasil_proses_roasting_table',1);
INSERT INTO `migrations` VALUES (355,'2020_01_01_000554_add_jam_to_pembekuan_iqf_roasting_table',1);
INSERT INTO `migrations` VALUES (356,'2020_01_01_000555_add_jam_to_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (357,'2020_01_01_000556_add_jam_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (358,'2020_01_01_000557_add_jam_to_proses_tumbling_table',1);
INSERT INTO `migrations` VALUES (359,'2020_01_01_000558_add_jam_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (360,'2020_01_01_000559_add_jam_to_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (361,'2020_01_01_000600_add_jam_to_pemasakan_nasi_table',1);
INSERT INTO `migrations` VALUES (362,'2020_01_01_000601_add_jam_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (363,'2020_01_01_000602_add_jam_to_produk_forming_table',1);
INSERT INTO `migrations` VALUES (364,'2020_01_01_000603_add_jam_to_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (365,'2020_01_01_000604_add_jam_to_thermometer',1);
INSERT INTO `migrations` VALUES (366,'2020_01_01_000605_add_jam_to_timbangan_table',1);
INSERT INTO `migrations` VALUES (367,'2020_01_01_000606_add_jam_to_ketidaksesuaian_plastik_table',1);
INSERT INTO `migrations` VALUES (368,'2020_01_01_000607_add_jam_to_pemeriksaan_proses_produksi',1);
INSERT INTO `migrations` VALUES (369,'2020_01_01_000608_add_waktu_penggorengan_columns_to_suhu_frayer_1_table',1);
INSERT INTO `migrations` VALUES (370,'2020_01_01_000609_add_waktu_penggorengan_2_to_suhu_frayer_2_table',1);
INSERT INTO `migrations` VALUES (371,'2020_01_01_000610_add_kode_form_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (372,'2020_01_01_000611_add_approval_fields_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (373,'2020_01_01_000612_create_master_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (374,'2020_01_01_000613_bahan_non_forming_table',1);
INSERT INTO `migrations` VALUES (375,'2020_01_01_000614_add_salinity_dan_hasil_pencampuran_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (376,'2020_01_01_000615_add_kondisi_barang_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (377,'2020_01_01_000616_add_id_bahan_nonforming_and_manual_bahan_data_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (378,'2020_01_01_000617_create_proses_aging_table',1);
INSERT INTO `migrations` VALUES (379,'2020_01_01_000618_add_tumbling_relation_to_proses_aging_table',1);
INSERT INTO `migrations` VALUES (380,'2020_01_01_000619_add_jumlah_to_data_barang_table',1);
INSERT INTO `migrations` VALUES (381,'2020_01_01_000620_add_berat_bahan_to_suhu_emulsi_table',1);
INSERT INTO `migrations` VALUES (382,'2020_01_01_000621_add_suhu_fields_to_input_roasting_table',1);
INSERT INTO `migrations` VALUES (383,'2020_01_01_000622_add_jumlah_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (384,'2020_01_01_000623_add_waktu_pemasakan_to_proses_roasting_fan_table',1);
INSERT INTO `migrations` VALUES (385,'2020_01_01_000624_drop_bahan_baku_roasting_uuid_columns',1);
INSERT INTO `migrations` VALUES (386,'2020_01_01_000625_modify_hasil_proses_roasting_table',1);
INSERT INTO `migrations` VALUES (387,'2020_01_01_000626_create_sub_area',1);
INSERT INTO `migrations` VALUES (388,'2020_01_01_000627_change_aktual_suhu_pusat_to_json',1);
INSERT INTO `migrations` VALUES (389,'2020_01_01_000628_add_area_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (390,'2020_01_01_000629_add_manual_input_fields_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (391,'2020_01_01_000630_add_approval_fields_to_proses_aging_table',1);
INSERT INTO `migrations` VALUES (392,'2020_01_01_000631_add_id_area_to_data_barang_table',1);
INSERT INTO `migrations` VALUES (393,'2020_01_01_000632_add_hasil_verifikasi_to_timbangan_table',1);
INSERT INTO `migrations` VALUES (394,'2020_01_01_000633_add_spec_defect_to_data_defect_table',1);
INSERT INTO `migrations` VALUES (395,'2020_01_01_000634_add_sampling_defect_qty_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (396,'2020_01_01_000635_add_total_defect_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (397,'2020_01_01_000636_add_hasil_verifikasi_to_thermometer_table',1);
INSERT INTO `migrations` VALUES (398,'2020_01_01_000637_create_pemeriksaan_bahan_kemas_table',1);
INSERT INTO `migrations` VALUES (399,'2020_01_01_000638_create_pemeriksaan_bahan_kemas_logs_table',1);
INSERT INTO `migrations` VALUES (400,'2020_01_01_000639_add_approval_fields_to_pemeriksaan_bahan_kemas_table',1);
INSERT INTO `migrations` VALUES (401,'2020_01_01_000640_create_input_mesin_peralatan_table',1);
INSERT INTO `migrations` VALUES (402,'2020_01_01_000641_change_berat_rm_to_string_in_bahan_rm_non_forming_table',1);
INSERT INTO `migrations` VALUES (403,'2020_01_01_000642_change_suhu_to_string_in_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (404,'2020_01_01_000643_add_kode_form_to_pemeriksaan_bahan_kemas_table',1);
INSERT INTO `migrations` VALUES (405,'2020_01_01_000644_add_penggorengan_uuid_to_frayer_2_table',1);
INSERT INTO `migrations` VALUES (406,'2020_01_01_000645_add_uuid_relations_to_frayer_2_table',1);
INSERT INTO `migrations` VALUES (407,'2020_01_01_000646_add_kode_form_to_proses_aging_table',1);
INSERT INTO `migrations` VALUES (408,'2020_01_01_000647_add_sensori_detail_to_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (409,'2020_01_01_000648_drop_sensori_from_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (410,'2020_01_01_000649_change_suhu_air_to_string_on_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (411,'2020_01_01_000650_add_non_vakum_fields_to_data_tumbling_table',1);
INSERT INTO `migrations` VALUES (412,'2020_01_01_000651_add_non_vakum_actual_fields_to_proses_tumbling_table',1);
INSERT INTO `migrations` VALUES (413,'2020_01_01_000652_add_non_vakum_tumbling_time_fields_to_proses_tumbling_table',1);
INSERT INTO `migrations` VALUES (414,'2020_01_01_000653_add_verifikasi_columns_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (415,'2020_01_01_000654_add_berat_to_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (416,'2020_01_01_000655_add_jam_kedatangan_to_penerimaan_chillroom_table',1);
INSERT INTO `migrations` VALUES (417,'2020_01_01_000656_add_berat_sampling_to_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (418,'2020_01_01_000657_add_created_by_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (419,'2020_01_01_000658_add_created_by_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (420,'2020_01_01_000659_add_created_by_to_reboxes_table',1);
INSERT INTO `migrations` VALUES (421,'2020_01_01_000700_add_created_by_to_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (422,'2020_01_01_000701_create_std_berat_rheon_table',1);
INSERT INTO `migrations` VALUES (423,'2020_01_01_000702_rename_std_rheon_to_std_adonan_in_std_berat_rheon_table',1);
INSERT INTO `migrations` VALUES (424,'2020_01_01_000703_add_std_optional_fields_to_std_berat_rheon_table',1);
INSERT INTO `migrations` VALUES (425,'2020_01_01_000704_add_roasting_fields_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (426,'2020_01_01_000705_add_pickup_total_roasting_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (427,'2020_01_01_000706_create_verif_peralatan_tables',1);
INSERT INTO `migrations` VALUES (428,'2020_01_01_000707_create_proses_twahing_tables',1);
INSERT INTO `migrations` VALUES (429,'2020_01_01_000708_add_jam_to_proses_twahing_table',1);
INSERT INTO `migrations` VALUES (430,'2020_01_01_000709_remove_sub_area_from_verif_peralatan_detail',1);
INSERT INTO `migrations` VALUES (431,'2020_01_01_000710_add_jam_to_verif_peralatan_table',1);
INSERT INTO `migrations` VALUES (432,'2020_01_01_000711_add_approval_columns_to_verif_peralatan_table',1);
INSERT INTO `migrations` VALUES (433,'2020_01_01_000712_add_approval_columns_to_proses_twahing_table',1);
INSERT INTO `migrations` VALUES (434,'2020_01_01_000713_add_kode_form_to_proses_twahing_table',1);
INSERT INTO `migrations` VALUES (435,'2020_01_01_000714_add_kode_form_to_verif_peralatan_table',1);
INSERT INTO `migrations` VALUES (436,'2020_01_01_000715_create_verif_cip_table',1);
INSERT INTO `migrations` VALUES (437,'2020_01_01_000716_add_approval_columns_to_verif_cip_table',1);
INSERT INTO `migrations` VALUES (438,'2020_01_01_000717_add_berat_and_nama_formula_better_to_jenis_better_table',1);
INSERT INTO `migrations` VALUES (439,'2020_01_01_000718_add_better_items_to_jenis_better_table',1);
INSERT INTO `migrations` VALUES (440,'2020_01_01_000719_add_catatan_gramase_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (441,'2020_01_01_000720_create_persiapan_bahan_non_forming_tables',1);
INSERT INTO `migrations` VALUES (442,'2020_01_01_000721_make_aktual_suhu_adonan_polymorphic',1);
INSERT INTO `migrations` VALUES (443,'2020_01_01_000722_add_better_rows_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (444,'2020_01_01_000723_drop_kode_produksi_emulsi_from_persiapan_bahan_non_forming',1);
INSERT INTO `migrations` VALUES (445,'2020_01_01_000724_change_seasonings_berat_to_varchar',1);
INSERT INTO `migrations` VALUES (446,'2020_01_01_000725_drop_berat_air_from_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (447,'2020_01_01_000726_add_dokumentasi_to_shoestring_table',1);
INSERT INTO `migrations` VALUES (448,'2020_01_01_000727_change_aktual_suhu_produk_type_on_pengemasan_produk_table',1);
INSERT INTO `migrations` VALUES (449,'2020_01_01_000728_add_tidak_ada_temuan_to_gmp_karyawan_enum',1);
INSERT INTO `migrations` VALUES (450,'2020_01_01_000729_add_keterangan_to_bahan_baku_tumbling',1);
INSERT INTO `migrations` VALUES (451,'2020_01_01_000730_create_bahan_baku_rice_bites_table',1);
INSERT INTO `migrations` VALUES (452,'2020_01_01_000731_add_berat_sampling_to_persiapan_bahan_forming_and_non_forming_tables',1);
INSERT INTO `migrations` VALUES (453,'2020_01_01_000732_add_area_to_timbangan_and_thermometer_tables',1);
INSERT INTO `migrations` VALUES (454,'2020_01_01_000733_add_entries_to_timbangan_and_thermometer_tables',1);
INSERT INTO `migrations` VALUES (455,'2020_01_01_000734_add_entries_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (456,'2020_01_01_000735_add_id_formula_to_produk_forming_table',1);
INSERT INTO `migrations` VALUES (457,'2020_01_01_000736_add_new_fields_to_proses_tumbling_table',1);
INSERT INTO `migrations` VALUES (458,'2020_01_01_000737_add_entries_to_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (459,'2020_01_01_000738_add_missing_columns_to_proses_roasting_fan',1);
INSERT INTO `migrations` VALUES (460,'2020_01_01_000739_change_frayer_uuid_to_text_in_pembekuan_iqf_roasting',1);
INSERT INTO `migrations` VALUES (461,'2020_01_01_000740_add_frayer_3_4_5_uuids_to_wizard_tables',1);
INSERT INTO `migrations` VALUES (462,'2020_01_01_000741_add_frayer_2_3_4_5_uuids_to_wizard_proses2_tables',1);
INSERT INTO `migrations` VALUES (463,'2020_01_01_000742_add_approval_columns_to_persiapan_bahan_forming_tables',1);
INSERT INTO `migrations` VALUES (464,'2020_01_01_000743_make_id_persiapan_bahan_forming_nullable_in_aktual_suhu_adonan',1);
INSERT INTO `migrations` VALUES (465,'2020_01_01_000744_add_nomor_formula_to_bahan_baku_rice_bites_table',1);
INSERT INTO `migrations` VALUES (466,'2020_01_01_000745_add_nomor_formula_to_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (467,'2020_01_01_000746_add_proses_column_to_bahan_formula_fla_table',1);
INSERT INTO `migrations` VALUES (468,'2020_01_01_000747_modify_id_nomor_step_formula_fla_nullable_in_bahan_formula_fla_table',1);
INSERT INTO `migrations` VALUES (469,'2020_01_01_000748_add_berat_sampling_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (470,'2020_01_01_000749_remove_proses_column_from_bahan_formula_fla_table',1);
INSERT INTO `migrations` VALUES (471,'2020_01_01_000750_make_proses_nullable_in_nomor_step_formula_fla_table',1);
INSERT INTO `migrations` VALUES (472,'2020_01_01_000751_modify_id_stp_frm_fla_nullable_in_pemeriksaan_produk_cooking_mixer_fla_table',1);
INSERT INTO `migrations` VALUES (473,'2020_01_01_000752_add_sensori_and_keterangan_to_suhu_forming_table',1);
INSERT INTO `migrations` VALUES (474,'2020_01_01_000753_add_jumlah_box_finish_good_to_pengemasan_karton',1);
INSERT INTO `migrations` VALUES (475,'2020_01_01_000754_add_sensori_and_keterangan_to_persiapan_bahan_non_forming_detail_table',1);
INSERT INTO `migrations` VALUES (476,'2020_01_01_000755_add_hasil_adonan_and_suhu_adonan_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (477,'2020_01_01_000756_change_id_jenis_better_to_string_in_proses_battering_table',1);
INSERT INTO `migrations` VALUES (478,'2020_01_01_000757_change_kode_produksi_to_text_in_wizard_tables',1);
INSERT INTO `migrations` VALUES (479,'2020_01_01_000758_add_items_json_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (480,'2020_01_01_000759_add_tindakan_koreksi_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (481,'2020_01_01_000800_add_is_audit_to_penerimaan_chillroom_table',1);
INSERT INTO `migrations` VALUES (482,'2020_01_01_000801_add_source_uuid_to_penerimaan_chillroom_table',1);
INSERT INTO `migrations` VALUES (483,'2020_01_01_000802_add_aktual_json_metal_to_input_metal_detector_table',1);
INSERT INTO `migrations` VALUES (484,'2020_01_01_000803_make_metal_detector_old_columns_nullable',1);
INSERT INTO `migrations` VALUES (485,'2020_01_01_000804_make_hasil_pencetakan_nullable_in_penggorengan_table',1);
INSERT INTO `migrations` VALUES (486,'2020_01_01_000805_create_master_cip_table',1);
INSERT INTO `migrations` VALUES (487,'2020_01_01_000806_drop_std_suhu_from_master_cip_table',1);
INSERT INTO `migrations` VALUES (488,'2020_01_01_000807_create_release_produk_finish_goods_table',1);
INSERT INTO `migrations` VALUES (489,'2020_01_01_000808_modify_release_produk_dynamic_fields',1);
INSERT INTO `migrations` VALUES (490,'2020_01_01_000809_revert_jumlah_box_to_single',1);
INSERT INTO `migrations` VALUES (491,'2020_01_01_000810_drop_keterangan_from_master_cip_table',1);
INSERT INTO `migrations` VALUES (492,'2020_01_01_000811_create_label_start_ups_table',1);
INSERT INTO `migrations` VALUES (493,'2020_01_01_000812_create_label_start_up_details_table',1);
INSERT INTO `migrations` VALUES (494,'2020_01_01_000813_add_approval_fields_to_release_produk_finish_goods_table',1);
INSERT INTO `migrations` VALUES (495,'2020_01_01_000814_add_label_produk_to_label_start_up_details',1);
INSERT INTO `migrations` VALUES (496,'2020_01_01_000815_add_bahan_baku_tumbling_uuid_to_proses_aging_table',1);
INSERT INTO `migrations` VALUES (497,'2020_01_01_000816_add_suhu_rm_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (498,'2020_01_01_000817_add_kondisi_hasil_pencampuran_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (499,'2020_01_01_000818_add_ffa_to_all_frayer_tables',1);
INSERT INTO `migrations` VALUES (500,'2020_01_01_000819_add_approval_fields_to_label_start_ups_table',1);
INSERT INTO `migrations` VALUES (501,'2020_01_01_000820_add_id_no_formula_non_forming_to_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (502,'2020_01_01_000821_add_viskositas_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (503,'2020_01_01_000822_change_suhu_produk_to_string_on_release_produk_table',1);
INSERT INTO `migrations` VALUES (504,'2020_01_01_000823_add_keterangan_to_produk_modules_table',1);
INSERT INTO `migrations` VALUES (505,'2020_01_01_000824_add_gramase_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (506,'2020_01_01_000825_add_approvals_to_proses_tumbling',1);
INSERT INTO `migrations` VALUES (507,'2020_01_01_000826_drop_waktu_selesai_packing_from_pengemasan_produk_table',1);
INSERT INTO `migrations` VALUES (508,'2020_01_01_000827_add_kesesuaian_label_plastik_to_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (509,'2020_01_01_000828_refactor_berat_aktual_in_berat_produk_bag_table',1);
INSERT INTO `migrations` VALUES (510,'2020_01_01_000829_add_abbrivation_column_into_plan_table',1);
INSERT INTO `migrations` VALUES (511,'2020_01_01_000830_add_kesesuaian_isi_pack_to_pengemasan_karton_table',1);
INSERT INTO `migrations` VALUES (512,'2020_01_01_000831_drop_jumlah_box_finish_good_from_pengemasan_karton_table',1);
INSERT INTO `migrations` VALUES (513,'2020_01_01_000832_add_keterangan_to_pengemasan_karton_table',1);
INSERT INTO `migrations` VALUES (514,'2020_01_01_000833_add_audit_fields_to_seasonings_table',1);
INSERT INTO `migrations` VALUES (515,'2020_01_01_000834_add_is_audit_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (516,'2020_01_01_000835_add_berat_to_label_start_up_details_table',1);
INSERT INTO `migrations` VALUES (517,'2020_01_01_000836_aad_is_audit_to_reboxes',1);
INSERT INTO `migrations` VALUES (518,'2020_01_01_000837_add_is_audit_to_pemeriksaan_bahan_kemas',1);
INSERT INTO `migrations` VALUES (519,'2020_01_01_000838_fix_bahan_baku_tumbling_foreign_key',1);
INSERT INTO `migrations` VALUES (520,'2020_01_01_000839_add_audit_fields_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (521,'2020_01_01_000840_add_audit_fields_to_persiapan_bahan_non_forming_table',1);
INSERT INTO `migrations` VALUES (522,'2020_01_01_000841_add_audit_fields_to_persiapan_bahan_better_table',1);
INSERT INTO `migrations` VALUES (523,'2020_01_01_000842_add_audit_fields_to_pembuatan_emulsi_table',1);
INSERT INTO `migrations` VALUES (524,'2020_01_01_000843_add_audit_fields_to_pembekuan_iqf_penggorengan',1);
INSERT INTO `migrations` VALUES (525,'2020_01_01_000844_add_audit_fields_to_pembekuan_iqf_roasting',1);
INSERT INTO `migrations` VALUES (526,'2020_01_01_000845_add_is_audit_and_source_uuid_to_proses_aging_table',1);
INSERT INTO `migrations` VALUES (527,'2020_01_01_000846_add_is_audit_and_source_uuid_to_verifikasi_berat_produk_table',1);
INSERT INTO `migrations` VALUES (528,'2020_01_01_000847_add_is_audit_and_source_uuid_to_label_start_up_table',1);
INSERT INTO `migrations` VALUES (529,'2020_01_01_000848_add_is_audit_and_source_uuid_to_release_produk_finish_goods_table',1);
INSERT INTO `migrations` VALUES (530,'2020_01_01_000849_add_is_audit_and_source_uuid_to_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (531,'2020_01_01_000850_add_audit_fields_to_proses_twahing_table',1);
INSERT INTO `migrations` VALUES (532,'2020_01_01_000851_create_proses_twahing_logs_table',1);
INSERT INTO `migrations` VALUES (533,'2020_01_01_000852_add_audit_columns_to_input_metal_detector_table',1);
INSERT INTO `migrations` VALUES (534,'2020_01_01_000853_add_audit_fields_to_produk_yum_table',1);
INSERT INTO `migrations` VALUES (535,'2020_01_01_000854_add_audit_fields_to_pemeriksaan_produk_cooking_mixer_fla_table',1);
INSERT INTO `migrations` VALUES (536,'2020_01_01_000855_add_audit_fields_to_pemeriksaan_rheon_machine_table',1);
INSERT INTO `migrations` VALUES (537,'2020_01_01_000856_add_audit_fields_to_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (538,'2020_01_01_000857_add_audit_fields_to_pemasakan_nasi_table',1);
INSERT INTO `migrations` VALUES (539,'2020_01_01_000858_add_audit_fields_to_ketidaksesuaian_plastik_table',1);
INSERT INTO `migrations` VALUES (540,'2020_01_01_000859_add_audit_fields_to_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (541,'2020_01_01_000900_add_audit_fields_to_pemeriksaan_proses_produksi_table',1);
INSERT INTO `migrations` VALUES (542,'2020_01_01_000901_add_audit_fields_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (543,'2020_01_01_000902_add_audit_columns_to_area_proses_table',1);
INSERT INTO `migrations` VALUES (544,'2020_01_01_000903_add_audit_fields_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (545,'2020_01_01_000904_add_audit_fields_to_gmp_karyawan_table',1);
INSERT INTO `migrations` VALUES (546,'2020_01_01_000905_add_audit_fields_to_kontrol_sanitasi_table',1);
INSERT INTO `migrations` VALUES (547,'2020_01_01_000906_add_audit_fields_to_verif_peralatan_table',1);
INSERT INTO `migrations` VALUES (548,'2020_01_01_000907_add_audit_fields_to_verif_cip_table',1);
INSERT INTO `migrations` VALUES (549,'2020_01_01_000908_add_audit_fields_to_barang_mudah_pecah_table',1);
INSERT INTO `migrations` VALUES (550,'2020_01_01_000909_add_audit_fields_to_timbangan_table',1);
INSERT INTO `migrations` VALUES (551,'2020_01_01_000910_add_audit_fields_to_thermometer_table',1);
INSERT INTO `migrations` VALUES (552,'2020_01_01_000911_add_audit_fields_to_produk_forming_table',1);
INSERT INTO `migrations` VALUES (553,'2020_01_01_000912_add_audit_fields_to_produk_non_forming_table',1);
INSERT INTO `migrations` VALUES (554,'2020_01_01_000913_add_audit_fields_to_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (555,'2020_01_01_000914_add_audit_fields_to_proses_twahing_table_v2',1);
INSERT INTO `migrations` VALUES (556,'2020_01_01_000915_create_bagian_mesin_table',1);
INSERT INTO `migrations` VALUES (557,'2020_01_01_000916_create_verif_peralatan_bagian_table',1);
INSERT INTO `migrations` VALUES (558,'2020_01_01_000917_add_verifikasi_hasil_to_verif_peralatan_tables',1);
INSERT INTO `migrations` VALUES (559,'2020_01_01_000918_make_disposisi_nullable_on_pemeriksaan_proses_produksi',1);
INSERT INTO `migrations` VALUES (560,'2020_01_01_000919_add_rm_and_jumlah_terdampak_to_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (561,'2020_01_01_000920_make_id_produk_nullable_in_pemeriksaan_benda_asing_table',1);
INSERT INTO `migrations` VALUES (562,'2020_01_01_000921_add_jumlah_ketidaksesuaian_to_ketidaksesuaian_plastik_table',1);
INSERT INTO `migrations` VALUES (563,'2020_01_01_000922_add_items_json_to_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (564,'2020_01_01_000923_modify_id_produk_nullable_on_pembuatan_sample_table',1);
INSERT INTO `migrations` VALUES (565,'2020_01_01_000924_restructure_data_defect_table',1);
INSERT INTO `migrations` VALUES (566,'2020_01_01_000925_add_detail_defect_to_data_defect_table',1);
INSERT INTO `migrations` VALUES (567,'2020_01_01_000926_add_line_waktu_pengemasan_to_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (568,'2020_01_01_000927_refactor_berat_aktual_in_berat_produk_box_table',1);
INSERT INTO `migrations` VALUES (569,'2020_01_01_000928_add_std_isi_pack_karton_to_data_box_table',1);
INSERT INTO `migrations` VALUES (570,'2020_01_01_000929_add_keterangan_to_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (571,'2020_01_01_000930_update_dokumentasi_fields',1);
INSERT INTO `migrations` VALUES (572,'2020_01_01_000931_add_jenis_produk_to_input_mesin_peralatan_table',1);
INSERT INTO `migrations` VALUES (573,'2020_01_01_000932_change_jenis_produk_to_varchar_in_input_mesin_peralatan',1);
INSERT INTO `migrations` VALUES (574,'2020_01_01_000933_add_mesin_peralatan_json_to_produk_forming_table',1);
INSERT INTO `migrations` VALUES (575,'2020_01_01_000934_change_kemasan_columns_to_json_in_produk_forming',1);
INSERT INTO `migrations` VALUES (576,'2020_01_01_000935_make_id_formula_nullable_in_produk_forming_table',1);
INSERT INTO `migrations` VALUES (577,'2020_01_01_000936_update_produk_non_forming_add_json_columns',1);
INSERT INTO `migrations` VALUES (578,'2020_01_01_000937_make_formula_nullable_in_produk_non_forming',1);
INSERT INTO `migrations` VALUES (579,'2020_01_01_000938_make_nomor_md_nullable_in_pengemasan_plastik',1);
INSERT INTO `migrations` VALUES (580,'2020_01_01_000939_make_premix_nullable_in_pemeriksaan_rice_bites_table',1);
INSERT INTO `migrations` VALUES (581,'2020_01_01_000940_alter_penerimaan_chillroom_columns_to_longtext',1);
INSERT INTO `migrations` VALUES (582,'2020_01_01_000941_alter_disposisi_column_in_pemeriksaan_proses_produksi_table',1);
INSERT INTO `migrations` VALUES (583,'2020_01_01_000942_alter_proses_tumbling_columns_to_string',1);
INSERT INTO `migrations` VALUES (584,'2020_01_01_000943_add_keterangan_to_frayer_tables',1);
INSERT INTO `migrations` VALUES (585,'2020_01_01_000944_add_keterangan_to_hasil_penggorengan_table',1);
INSERT INTO `migrations` VALUES (586,'2020_01_01_000945_add_keterangan_to_hasil_proses_roasting_table',1);
INSERT INTO `migrations` VALUES (587,'2020_01_01_000946_add_status_release_and_tindakan_koreksi_to_pengemasan_produk_table',1);
INSERT INTO `migrations` VALUES (588,'2020_01_01_000947_add_foto_kode_produksi_sekunder_to_dokumentasi_table',1);
INSERT INTO `migrations` VALUES (589,'2020_01_01_000948_add_force_logout_after_to_users_table',1);
INSERT INTO `migrations` VALUES (590,'2020_01_01_000949_add_kode_produksi_bahan_to_pemeriksaan_produk_cooking_mixer_fla_table',1);
INSERT INTO `migrations` VALUES (591,'2020_01_01_000950_make_berat_nullable_in_pengemasan_plastik_table',1);
INSERT INTO `migrations` VALUES (592,'2020_01_01_000951_add_kombinasi_fields_to_pengemasan_produk_table',1);
INSERT INTO `migrations` VALUES (593,'2020_01_01_000952_add_berat_sampling_to_shoestrings_table',1);
INSERT INTO `migrations` VALUES (594,'2020_01_01_000953_add_ruangan_to_produk_forming_and_non_forming_tables',1);
INSERT INTO `migrations` VALUES (595,'2020_01_01_000954_add_kemasan_tindakan_verifikasi_to_produk_forming_and_non_forming',1);
INSERT INTO `migrations` VALUES (596,'2020_01_01_000955_add_active_plan_id_to_users_table',1);
INSERT INTO `migrations` VALUES (597,'2020_01_01_000956_create_manager_plans_table',1);
INSERT INTO `migrations` VALUES (598,'2020_01_01_000957_add_suhu_rm_to_proses_twahing_detail_table',1);
INSERT INTO `migrations` VALUES (599,'2020_01_01_000958_add_id_produk_rework_to_jenis_produk_table',1);
INSERT INTO `migrations` VALUES (600,'2020_01_01_000959_add_produk_rework_id_to_persiapan_bahan_forming_table',1);
INSERT INTO `migrations` VALUES (601,'2020_01_01_001000_add_frayer2_columns_to_suhu_frayer_1_table',1);
INSERT INTO `migrations` VALUES (602,'2020_01_01_001001_add_id_produk_to_proses_twahing_table',1);
INSERT INTO `migrations` VALUES (603,'2020_01_01_001002_add_group_uuid_to_proses_twahing_table',1);
INSERT INTO `migrations` VALUES (604,'2020_01_01_001003_create_list_peralatan_table',1);
INSERT INTO `migrations` VALUES (605,'2020_01_01_001004_add_jumlah_alat_to_list_peralatan_table',1);
INSERT INTO `migrations` VALUES (606,'2020_01_01_001005_create_inventaris_qc_table',1);
INSERT INTO `migrations` VALUES (607,'2020_01_01_001006_create_inventaris_qc_detail_table',1);
INSERT INTO `migrations` VALUES (608,'2020_01_01_001007_add_jam_to_verif_peralatan_detail_table',1);
INSERT INTO `migrations` VALUES (609,'2020_01_01_001008_add_gramase_to_bahan_baku_tumbling_table',1);
INSERT INTO `migrations` VALUES (610,'2020_01_01_001009_change_sampling_defect_to_text_in_shoestrings_table',1);
INSERT INTO `migrations` VALUES (611,'2020_01_01_001010_create_master_standar_audits_table',1);
INSERT INTO `migrations` VALUES (612,'2020_01_01_001011_add_area_id_to_master_standar_audits_table',1);
INSERT INTO `migrations` VALUES (613,'2020_01_01_001012_add_hasil_verifikasi_10000_to_timbangan',1);
INSERT INTO `migrations` VALUES (614,'2020_01_01_001013_add_id_plan_to_master_standar_audits_table',1);
INSERT INTO `migrations` VALUES (615,'2020_01_01_001014_add_audit_fields_to_proses_tumbling_table',1);
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
