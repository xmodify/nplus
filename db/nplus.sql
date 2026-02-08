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

 Date: 08/02/2026 16:18:34
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
-- Table structure for main_setting
-- ----------------------------
DROP TABLE IF EXISTS `main_setting`;
CREATE TABLE `main_setting`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of main_setting
-- ----------------------------
INSERT INTO `main_setting` VALUES (1, 'Telegram Bot Token', 'telegram_token', '');
INSERT INTO `main_setting` VALUES (2, 'ER ชม.การทำงานพยาบาล', 'er_working_hours', '7');
INSERT INTO `main_setting` VALUES (3, 'ER ชม.ผู้ป่วยวิกฤต(Resuscitation)', 'er_patient_type1', '3.2');
INSERT INTO `main_setting` VALUES (4, 'ER ชม.ผู้ป่วยฉุกเฉินสูง(Emergent)', 'er_patient_type2', '2.5');
INSERT INTO `main_setting` VALUES (5, 'ER ชม.ผู้ป่วยฉุกเฉิน(Urgent) ', 'er_patient_type3', '1');
INSERT INTO `main_setting` VALUES (6, 'ER ชม.ผู้ป่วยฉุกเฉินน้อย(Semi Urgent)', 'er_patient_type4', '0.5');
INSERT INTO `main_setting` VALUES (7, 'ER ชม.ผู้ป่วยไม่ฉุกเฉิน(Non Urgent)', 'er_patient_type5', '0.24');
INSERT INTO `main_setting` VALUES (8, 'ER NotifyTelegram แจ้งเตือน', 'er_notifytelegram', '-4729376994');
INSERT INTO `main_setting` VALUES (9, 'ER NotifyTelegram บันทึก', 'er_notifytelegram_save', '-4729376994,-4776977390');
INSERT INTO `main_setting` VALUES (10, 'IPD รหัส Ward (HOSxP)', 'ipd_ward', '01');
INSERT INTO `main_setting` VALUES (11, 'IPD ชม.การทำงานพยาบาล', 'ipd_working_hours', '7');
INSERT INTO `main_setting` VALUES (12, 'IPD ชม.ผู้ป่วยพักฟื้น(Convalescent)', 'ipd_patient_type1', '1.5');
INSERT INTO `main_setting` VALUES (13, 'IPD ชม.ผู้ป่วยปานกลาง(Moderate)', 'ipd_patient_type2', '3.5');
INSERT INTO `main_setting` VALUES (14, 'IPD ชม.ผู้ป่วยกึ่งหนัก(Semi-critical)', 'ipd_patient_type3', '5.5');
INSERT INTO `main_setting` VALUES (15, 'IPD ชม.ผู้ป่วยหนัก(Critical)', 'ipd_patient_type4', '7.5');
INSERT INTO `main_setting` VALUES (16, 'IPD NotifyTelegram แจ้งเตือน', 'ipd_notifytelegram', '-4729376994');
INSERT INTO `main_setting` VALUES (17, 'IPD NotifyTelegram บันทึก', 'ipd_notifytelegram_save', '-4729376994,-4776977390');
INSERT INTO `main_setting` VALUES (18, 'OPD รหัสห้องตรวจ (HOSxP)', 'opd_department', '002');
INSERT INTO `main_setting` VALUES (19, 'OPD ชม.การทำงานพยาบาล', 'opd_working_hours', '7');
INSERT INTO `main_setting` VALUES (20, 'OPD ชม.ผู้ป่วยทั่วไป', 'opd_patient_type', '0.24');
INSERT INTO `main_setting` VALUES (21, 'OPD NotifyTelegram แจ้งเตือน', 'opd_notifytelegram', '-4729376994');
INSERT INTO `main_setting` VALUES (22, 'OPD NotifyTelegram บันทึก', 'opd_notifytelegram_save', '-4729376994,-4776977390');
INSERT INTO `main_setting` VALUES (23, 'NCD รหัสห้องตรวจ (HOSxP)', 'ncd_department', '025');
INSERT INTO `main_setting` VALUES (24, 'NCD ชม.การทำงานพยาบาล', 'ncd_working_hours', '7');
INSERT INTO `main_setting` VALUES (25, 'NCD ชม.ผู้ป่วยทั่วไป', 'ncd_patient_type', '0.24');
INSERT INTO `main_setting` VALUES (26, 'NCD NotifyTelegram แจ้งเตือน', 'ncd_notifytelegram', '-4729376994');
INSERT INTO `main_setting` VALUES (27, 'NCD NotifyTelegram บันทึก', 'ncd_notifytelegram_save', '-4729376994,-4776977390');
INSERT INTO `main_setting` VALUES (28, 'ARI รหัสห้องตรวจ (HOSxP)', 'ari_department', '032');
INSERT INTO `main_setting` VALUES (29, 'ARI ชม.การทำงานพยาบาล', 'ari_working_hours', '7');
INSERT INTO `main_setting` VALUES (30, 'ARI ชม.ผู้ป่วยทั่วไป', 'ari_patient_type', '0.24');
INSERT INTO `main_setting` VALUES (31, 'ARI NotifyTelegram แจ้งเตือน', 'ari_notifytelegram', '-4729376994');
INSERT INTO `main_setting` VALUES (32, 'ARI NotifyTelegram บันทึก', 'ari_notifytelegram_save', '-4729376994,-4776977390');

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
-- Table structure for productivity_ari
-- ----------------------------
DROP TABLE IF EXISTS `productivity_ari`;
CREATE TABLE `productivity_ari`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL COMMENT 'วันที่',
  `shift_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เวร',
  `nurse_fulltime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังปกติ',
  `nurse_partime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังเสริม',
  `nurse_oncall` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลัง OnCall',
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ผู้บันทึก',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'หมายเหตุ',
  `patient_all` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยทั้งหมด: 0.24 ชั่วโมง',
  `nursing_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาล',
  `working_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการทำงาน',
  `nhppd` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาลผู้ป่วยเฉลี่ยต่อรายต่อวัน',
  `nurse_shift_time` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนพยาบาลที่ต้องการต่อเวร',
  `productivity` double(5, 2) NULL DEFAULT NULL COMMENT 'Productivity',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ipd_report_date`(`report_date`) USING BTREE,
  INDEX `ipd_shift_time`(`shift_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for productivity_er
-- ----------------------------
DROP TABLE IF EXISTS `productivity_er`;
CREATE TABLE `productivity_er`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL COMMENT 'วันที่',
  `shift_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เวร',
  `nurse_fulltime` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังปกติ',
  `nurse_partime` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังเสริม',
  `nurse_oncall` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลัง OnCall',
  `recorder` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ผู้บันทึก',
  `note` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'หมายเหตุ',
  `patient_all` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยทั้งหมด',
  `patient_resuscitation` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยวิกฤต(Resuscitation): 3.2 ชั่วโมง',
  `patient_emergent` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยฉุกเฉินสูง(Emergent): 2.5 ชั่วโมง',
  `patient_urgent` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยฉุกเฉิน(Urgent): 1 ชั่วโมง',
  `patient_semi_urgent` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยฉุกเฉินน้อย(Less Urgent): 0.5 ชั่วโมง',
  `patient_non_urgent` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยไม่ฉุกเฉิน(Non Urgent): 0.24 ชั่วโมง',
  `nursing_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาล',
  `working_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการทำงาน',
  `nhppd` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาลผู้ป่วยเฉลี่ยต่อรายต่อวัน',
  `nurse_shift_time` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนพยาบาลที่ต้องการต่อเวร',
  `productivity` double(5, 2) NULL DEFAULT NULL COMMENT 'Productivity',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `er_report_date`(`report_date`) USING BTREE,
  INDEX `er_shift_time`(`shift_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for productivity_ipd
-- ----------------------------
DROP TABLE IF EXISTS `productivity_ipd`;
CREATE TABLE `productivity_ipd`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL COMMENT 'วันที่',
  `shift_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เวร',
  `nurse_fulltime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังปกติ',
  `nurse_partime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังเสริม',
  `nurse_oncall` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลัง OnCall',
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ผู้บันทึก',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'หมายเหตุ',
  `patient_all` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยทั้งหมด',
  `patient_critical` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยหนัก (Critical): 7.5 ชั่วโมง',
  `patient_semi_critical` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยกึ่งหนัก (Semi-critical): 5.5 ชั่วโมง',
  `patient_moderate` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วย(Moderate): 3.5 ชั่วโมง',
  `patient_convalescent` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วย(Convalescent): 1.5 ชั่วโมง',
  `nursing_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาล',
  `working_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการทำงาน',
  `nhppd` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาลผู้ป่วยเฉลี่ยต่อรายต่อวัน',
  `nurse_shift_time` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนพยาบาลที่ต้องการต่อเวร',
  `productivity` double(5, 2) NULL DEFAULT NULL COMMENT 'Productivity',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ipd_report_date`(`report_date`) USING BTREE,
  INDEX `ipd_shift_time`(`shift_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for productivity_ncd
-- ----------------------------
DROP TABLE IF EXISTS `productivity_ncd`;
CREATE TABLE `productivity_ncd`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL COMMENT 'วันที่',
  `shift_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เวร',
  `nurse_fulltime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังปกติ',
  `nurse_partime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังเสริม',
  `nurse_oncall` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลัง OnCall',
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ผู้บันทึก',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'หมายเหตุ',
  `patient_all` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยทั้งหมด: 0.24 ชั่วโมง',
  `nursing_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาล',
  `working_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการทำงาน',
  `nhppd` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาลผู้ป่วยเฉลี่ยต่อรายต่อวัน',
  `nurse_shift_time` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนพยาบาลที่ต้องการต่อเวร',
  `productivity` double(5, 2) NULL DEFAULT NULL COMMENT 'Productivity',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ipd_report_date`(`report_date`) USING BTREE,
  INDEX `ipd_shift_time`(`shift_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for productivity_opd
-- ----------------------------
DROP TABLE IF EXISTS `productivity_opd`;
CREATE TABLE `productivity_opd`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL COMMENT 'วันที่',
  `shift_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เวร',
  `nurse_fulltime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังปกติ',
  `nurse_partime` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลังเสริม',
  `nurse_oncall` int(11) NULL DEFAULT NULL COMMENT 'จำนวนอัตรากำลัง OnCall',
  `recorder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ผู้บันทึก',
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'หมายเหตุ',
  `patient_all` int(11) NULL DEFAULT NULL COMMENT 'จำนวนผู้ป่วยทั้งหมด: 0.24 ชั่วโมง',
  `nursing_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาล',
  `working_hours` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการทำงาน',
  `nhppd` double(5, 2) NULL DEFAULT NULL COMMENT 'ชั่วโมงการพยาบาลผู้ป่วยเฉลี่ยต่อรายต่อวัน',
  `nurse_shift_time` double(5, 2) NULL DEFAULT NULL COMMENT 'จำนวนพยาบาลที่ต้องการต่อเวร',
  `productivity` double(5, 2) NULL DEFAULT NULL COMMENT 'Productivity',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ipd_report_date`(`report_date`) USING BTREE,
  INDEX `ipd_shift_time`(`shift_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

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
INSERT INTO `sessions` VALUES ('2rSUrRk9o4sug2yrM2ulUqE7wJPRmJpKtcCiKRSa', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidXFFcU1ScnNWM0l1dHlCWkxXbVA0TWRBNTh6dHFjY21RdUQxVmdwVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9obnBsdXMvcHJvZHVjdC9uY2RfbW9ybmluZyI7czo1OiJyb3V0ZSI7czo3OiJobnBsdXMuIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770537925);
INSERT INTO `sessions` VALUES ('RFIxIdZYjbkqBXSJHcQQTOr4Doysv5sMuMYTNSaR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicGhaSGhyV0F5clFZa3MxZlNQR0ZUZ2pkenV1VUNKNmpJUmhJbTdRZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYWluX3NldHRpbmciO3M6NToicm91dGUiO3M6MTg6ImFkbWluLm1haW5fc2V0dGluZyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1770542153);

-- ----------------------------
-- Table structure for telegram_tokens
-- ----------------------------
DROP TABLE IF EXISTS `telegram_tokens`;
CREATE TABLE `telegram_tokens`  (
  `telegram_token_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `telegram_bot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `telegram_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telegram_chat_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telegram_group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`telegram_token_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of telegram_tokens
-- ----------------------------
INSERT INTO `telegram_tokens` VALUES (1, 'N-Plus', '', '-5113190104', 'รพ.เสนาง_กลุ่มการพยาบาล', NULL, NULL);

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
  `del_product` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Y',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin', 'admin@gmail.com', 'admin', 'Y', NULL, '$2y$12$6bRoW.Skmu4TJg/P6gyeI.mGqoBHa4mKZmSuLaa9SXUeEZ1enPAFi', NULL, 'Y', NULL, '2026-01-19 20:47:12');
INSERT INTO `users` VALUES (2, 'ศิริฤกษ์ คณาดี', 'xmodifya@gmail.com', 'user', 'Y', NULL, '$2y$12$wNMPnS6F0QfrgJKD70bkbeHTZQzwwxjskpxrAgzue.N544MvumwHe', NULL, 'Y', '2026-02-08 11:27:41', '2026-02-08 11:27:41');

SET FOREIGN_KEY_CHECKS = 1;
