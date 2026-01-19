/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MariaDB
 Source Server Version : 100017
 Source Host           : 127.0.0.1:3306
 Source Schema         : nplus

 Target Server Type    : MariaDB
 Target Server Version : 100017
 File Encoding         : 65001

 Date: 19/01/2026 22:30:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for budget_year
-- ----------------------------
DROP TABLE IF EXISTS `budget_year`;
CREATE TABLE `budget_year`  (
  `LEAVE_YEAR_ID` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `LEAVE_YEAR_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `DATE_BEGIN` date NULL DEFAULT NULL,
  `DATE_END` date NULL DEFAULT NULL,
  `ACTIVE` enum('True','False') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'False',
  `DAY_PER_YEAR` int(11) NULL DEFAULT 10,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`LEAVE_YEAR_ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of budget_year
-- ----------------------------
INSERT INTO `budget_year` VALUES ('2565', 'ปีงบประมาณ 2565', '2021-10-01', '2022-09-30', 'True', 10, '2021-01-13 04:53:34', '2020-10-19 13:05:21');
INSERT INTO `budget_year` VALUES ('2566', 'ปีงบประมาณ 2566', '2022-10-01', '2023-09-30', 'True', 10, '2022-09-19 02:29:24', '2022-08-05 08:54:11');
INSERT INTO `budget_year` VALUES ('2567', 'ปีงบประมาณ 2567', '2023-10-01', '2024-09-30', 'True', 10, '2023-09-25 07:36:47', '2023-09-25 07:36:33');
INSERT INTO `budget_year` VALUES ('2568', 'ปีงบประมาณ 2568', '2024-10-01', '2025-09-30', 'True', 10, '2024-09-11 15:22:35', '2024-09-11 15:22:32');
INSERT INTO `budget_year` VALUES ('2569', 'ปีงบประมาณ 2569', '2025-10-01', '2026-09-30', 'False', 10, '2025-10-04 21:45:37', '2025-10-04 21:45:37');

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int(11) NULL DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);

-- ----------------------------
-- Table structure for nurse_productivity_ers
-- ----------------------------
DROP TABLE IF EXISTS `nurse_productivity_ers`;
CREATE TABLE `nurse_productivity_ers`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL,
  `shift_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_all` int(11) NOT NULL,
  `emergent` int(11) NOT NULL,
  `urgent` int(11) NOT NULL,
  `acute_illness` int(11) NOT NULL,
  `non_acute_illness` int(11) NOT NULL,
  `patient_hr` double(8, 2) NOT NULL,
  `nurse_oncall` int(11) NOT NULL,
  `nurse_partime` int(11) NOT NULL,
  `nurse_fulltime` int(11) NOT NULL,
  `nurse_hr` double(8, 2) NOT NULL,
  `productivity` double(8, 2) NOT NULL,
  `hhpuos` double(8, 2) NOT NULL,
  `nurse_shift_time` double(8, 2) NOT NULL,
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2489 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for nurse_productivity_ipds
-- ----------------------------
DROP TABLE IF EXISTS `nurse_productivity_ipds`;
CREATE TABLE `nurse_productivity_ipds`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL,
  `shift_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_all` int(11) NOT NULL,
  `convalescent` int(11) NOT NULL,
  `moderate_ill` int(11) NOT NULL,
  `semi_critical_ill` int(11) NOT NULL,
  `critical_ill` int(11) NOT NULL,
  `patient_hr` double(8, 2) NOT NULL,
  `nurse_oncall` int(11) NOT NULL,
  `nurse_partime` int(11) NOT NULL,
  `nurse_fulltime` int(11) NOT NULL,
  `nurse_hr` double(8, 2) NOT NULL,
  `productivity` double(8, 2) NOT NULL,
  `hhpuos` double(8, 2) NOT NULL,
  `nurse_shift_time` double(8, 2) NOT NULL,
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2784 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for nurse_productivity_ncds
-- ----------------------------
DROP TABLE IF EXISTS `nurse_productivity_ncds`;
CREATE TABLE `nurse_productivity_ncds`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL,
  `shift_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_all` int(11) NOT NULL,
  `patient_hr` double(8, 2) NOT NULL,
  `nurse_oncall` int(11) NOT NULL,
  `nurse_partime` int(11) NOT NULL,
  `nurse_fulltime` int(11) NOT NULL,
  `nurse_hr` double(8, 2) NOT NULL,
  `productivity` double(8, 2) NOT NULL,
  `hhpuos` double(8, 2) NOT NULL,
  `nurse_shift_time` double(8, 2) NOT NULL,
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 455 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for nurse_productivity_opds
-- ----------------------------
DROP TABLE IF EXISTS `nurse_productivity_opds`;
CREATE TABLE `nurse_productivity_opds`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL,
  `shift_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_all` int(11) NOT NULL,
  `opd` int(11) NOT NULL,
  `ari` int(11) NOT NULL,
  `patient_hr` double(8, 2) NOT NULL,
  `nurse_oncall` int(11) NOT NULL,
  `nurse_partime` int(11) NOT NULL,
  `nurse_fulltime` int(11) NOT NULL,
  `nurse_hr` double(8, 2) NOT NULL,
  `productivity` double(8, 2) NOT NULL,
  `hhpuos` double(8, 2) NOT NULL,
  `nurse_shift_time` double(8, 2) NOT NULL,
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1692 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for nurse_setting
-- ----------------------------
DROP TABLE IF EXISTS `nurse_setting`;
CREATE TABLE `nurse_setting`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of nurse_setting
-- ----------------------------
INSERT INTO `nurse_setting` VALUES (1, 'Telegram Token', 'telegram_token', '8210676912:AAE39UTOlwCn7LP_4HINvls7ND2H4xpfEEo');
INSERT INTO `nurse_setting` VALUES (2, 'Telegram ChatID Product ER แจ้งเตือน', 'telegram_chat_id_product_er', '-4729376994');
INSERT INTO `nurse_setting` VALUES (3, 'Telegram ChatID Product ER บันทึก', 'telegram_chat_id_product_er_save', '-4729376994');
INSERT INTO `nurse_setting` VALUES (4, 'Telegram ChatID Product IPD แจ้งเตือน', 'telegram_chat_id_product_ipd', '-4729376994');
INSERT INTO `nurse_setting` VALUES (5, 'Telegram ChatID Product IPD บันทึก', 'telegram_chat_id_product_ipd_save', '-4729376994');
INSERT INTO `nurse_setting` VALUES (6, 'Telegram ChatID Product OPD แจ้งเตือน', 'telegram_chat_id_product_opd', '-4729376994');
INSERT INTO `nurse_setting` VALUES (7, 'Telegram ChatID Product OPD บันทึก', 'telegram_chat_id_product_opd_save', '-4729376994');
INSERT INTO `nurse_setting` VALUES (8, 'Telegram ChatID Product NCD แจ้งเตือน', 'telegram_chat_id_product_ncd', '-4729376994');
INSERT INTO `nurse_setting` VALUES (9, 'Telegram ChatID Product NCD บันทึก', 'telegram_chat_id_product_ncd_save', '-4729376994');

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id`) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('5DnC3wkqLqY8ASuBbOumWiOk4IzvpeDF1NBVB8ej', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiejJoME1xOGxlSXJFM2l1bWxzNE5qeHVoM3hydzI2QzY0VXhLU05WRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYWluX3NldHRpbmciO3M6NToicm91dGUiO3M6MTg6ImFkbWluLm1haW5fc2V0dGluZyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1768836282);
INSERT INTO `sessions` VALUES ('fbLOYCHFr2qZu064gOSYSt7FA1dVXExuQfCAVnem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR25Qc1FlUENMdk5NdUkyQjAwQUhiOThZSHh2cnF6c09oeW94Z3pheCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1768813624);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'user',
  `active` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Y',
  `email_verified_at` timestamp(0) NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `del_product` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Y',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin', 'admin@gmail.com', 'admin', 'Y', NULL, '$2y$12$6bRoW.Skmu4TJg/P6gyeI.mGqoBHa4mKZmSuLaa9SXUeEZ1enPAFi', NULL, NULL, '2026-01-19 20:47:12', 'Y');

SET FOREIGN_KEY_CHECKS = 1;
