-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 21, 2022 at 10:28 AM
-- Server version: 5.7.23-23
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mad_aceuss`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `action_by` bigint(20) UNSIGNED DEFAULT NULL,
  `group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `how_many_time` int(11) DEFAULT NULL,
  `is_repeat` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:No,1:Yes',
  `every` int(11) DEFAULT NULL,
  `repetition_type` tinyint(4) DEFAULT NULL COMMENT '1:day,2:week,3:month,4:Year',
  `how_many_time_array` longtext COLLATE utf8mb4_unicode_ci,
  `repeat_dates` longtext COLLATE utf8mb4_unicode_ci,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `address_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `information_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `edit_date` date DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `selected_option` text COLLATE utf8mb4_unicode_ci,
  `internal_comment` text COLLATE utf8mb4_unicode_ci,
  `external_comment` text COLLATE utf8mb4_unicode_ci,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=Pending ,1:Done,2:Not Done,3:notapplicable',
  `action_date` date DEFAULT NULL,
  `remind_before_start` tinyint(4) NOT NULL DEFAULT '0',
  `before_minutes` int(11) DEFAULT NULL,
  `before_is_text_notify` tinyint(4) NOT NULL DEFAULT '0',
  `before_is_push_notify` tinyint(4) NOT NULL DEFAULT '0',
  `remind_after_end` tinyint(4) NOT NULL DEFAULT '0',
  `after_minutes` int(11) DEFAULT NULL,
  `after_is_text_notify` tinyint(4) NOT NULL DEFAULT '0',
  `after_is_push_notify` tinyint(4) NOT NULL DEFAULT '0',
  `is_emergency` tinyint(4) NOT NULL DEFAULT '0',
  `emergency_minutes` int(11) DEFAULT NULL,
  `emergency_is_text_notify` tinyint(4) NOT NULL DEFAULT '0',
  `emergency_is_push_notify` tinyint(4) NOT NULL DEFAULT '0',
  `in_time` tinyint(4) NOT NULL DEFAULT '0',
  `in_time_is_text_notify` tinyint(4) NOT NULL DEFAULT '0',
  `in_time_is_push_notify` tinyint(4) NOT NULL DEFAULT '0',
  `is_risk` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `message` text COLLATE utf8mb4_unicode_ci,
  `is_compulsory` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_tag` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_comment` text COLLATE utf8mb4_unicode_ci COMMENT 'for delete',
  `is_latest_entry` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_assignes`
--

CREATE TABLE `activity_assignes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_by` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_date` date DEFAULT NULL,
  `assignment_day` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:done ,2:notdone,3:notapplicable',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_notify` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_classifications`
--

CREATE TABLE `activity_classifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `created_at`, `updated_at`) VALUES
(1, 'default', 'created', 'App\\Models\\Package', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.2,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:51.000000Z\",\"updated_at\":\"2022-09-21T10:12:51.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(2, 'default', 'created', 'App\\Models\\Module', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"name\":\"Activity\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:51.000000Z\",\"updated_at\":\"2022-09-21T10:12:51.000000Z\"}}', '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(3, 'default', 'created', 'App\\Models\\Module', 2, NULL, NULL, '{\"attributes\":{\"id\":2,\"name\":\"Journal\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:51.000000Z\",\"updated_at\":\"2022-09-21T10:12:51.000000Z\"}}', '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(4, 'default', 'created', 'App\\Models\\Module', 3, NULL, NULL, '{\"attributes\":{\"id\":3,\"name\":\"Deviation\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:51.000000Z\",\"updated_at\":\"2022-09-21T10:12:51.000000Z\"}}', '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(5, 'default', 'created', 'App\\Models\\Module', 4, NULL, NULL, '{\"attributes\":{\"id\":4,\"name\":\"Schedule\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:51.000000Z\",\"updated_at\":\"2022-09-21T10:12:51.000000Z\"}}', '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(6, 'default', 'created', 'App\\Models\\Module', 5, NULL, NULL, '{\"attributes\":{\"id\":5,\"name\":\"Stampling\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:51.000000Z\",\"updated_at\":\"2022-09-21T10:12:51.000000Z\"}}', '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(7, 'default', 'created', 'App\\Models\\User', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"unique_id\":\"VfgXiv5O6Dir\",\"custom_unique_id\":null,\"user_type_id\":1,\"category_id\":null,\"top_most_parent_id\":1,\"parent_id\":null,\"dept_id\":null,\"role_id\":1,\"company_type_id\":null,\"branch_id\":null,\"govt_id\":null,\"name\":\"admin\",\"email\":\"admin@gmail.com\",\"email_verified_at\":null,\"password\":\"$2y$10$XMQpV4VfncBnbCbVmrJFQeJrmT8\\/tmqh3kJIET6.4NzbUgjenbSnC\",\"contact_number\":\"8103099592\",\"gender\":null,\"personal_number\":null,\"organization_number\":null,\"patient_type_id\":null,\"country_id\":null,\"city\":null,\"postal_area\":null,\"zipcode\":null,\"full_address\":null,\"licence_key\":null,\"licence_end_date\":null,\"licence_status\":1,\"employee_type\":null,\"joining_date\":null,\"establishment_year\":null,\"user_color\":null,\"disease_description\":null,\"created_by\":null,\"password_token\":null,\"is_file_required\":0,\"is_secret\":0,\"status\":1,\"is_fake\":0,\"is_password_change\":0,\"documents\":null,\"step_one\":0,\"step_two\":0,\"step_three\":0,\"step_four\":0,\"step_five\":0,\"entry_mode\":null,\"contact_person_number\":null,\"contact_person_name\":null,\"language_id\":1,\"contract_type\":\"1\",\"contract_value\":\"0.00\",\"avatar\":\"https:\\/\\/aceuss.3mad.in\\/uploads\\/no-image.png\",\"schedule_start_date\":null,\"report_verify\":\"no\",\"verification_method\":\"normal\",\"is_family_member\":0,\"is_caretaker\":0,\"is_contact_person\":0,\"is_guardian\":0,\"is_other\":0,\"is_other_name\":null,\"remember_token\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(8, 'default', 'created', 'App\\Models\\CategoryType', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Activity\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(9, 'default', 'created', 'App\\Models\\CategoryType', 2, NULL, NULL, '{\"attributes\":{\"id\":2,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Implementation Plan\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(10, 'default', 'created', 'App\\Models\\CategoryType', 3, NULL, NULL, '{\"attributes\":{\"id\":3,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"User\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(11, 'default', 'created', 'App\\Models\\CategoryType', 4, NULL, NULL, '{\"attributes\":{\"id\":4,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Deviation\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(12, 'default', 'created', 'App\\Models\\CategoryType', 5, NULL, NULL, '{\"attributes\":{\"id\":5,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"FollowUps\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(13, 'default', 'created', 'App\\Models\\CategoryType', 6, NULL, NULL, '{\"attributes\":{\"id\":6,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Journal\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(14, 'default', 'created', 'App\\Models\\CategoryType', 7, NULL, NULL, '{\"attributes\":{\"id\":7,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Patient\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(15, 'default', 'created', 'App\\Models\\CategoryType', 8, NULL, NULL, '{\"attributes\":{\"id\":8,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Employee\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(16, 'default', 'created', 'App\\Models\\CategoryType', 9, NULL, NULL, '{\"attributes\":{\"id\":9,\"top_most_parent_id\":1,\"created_by\":1,\"name\":\"Category Update Permission\",\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(17, 'default', 'created', 'App\\Models\\CategoryMaster', 1, NULL, NULL, '{\"attributes\":{\"id\":1,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"L\\u00e4rande och att till\\u00e4mpa kunskap\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(18, 'default', 'created', 'App\\Models\\CategoryMaster', 2, NULL, NULL, '{\"attributes\":{\"id\":2,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":1,\"category_type_id\":2,\"name\":\"Att ta reda p\\u00e5 information\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(19, 'default', 'created', 'App\\Models\\CategoryMaster', 3, NULL, NULL, '{\"attributes\":{\"id\":3,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":1,\"category_type_id\":2,\"name\":\"Att f\\u00f6rv\\u00e4rva f\\u00e4rdigheter \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(20, 'default', 'created', 'App\\Models\\CategoryMaster', 4, NULL, NULL, '{\"attributes\":{\"id\":4,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":1,\"category_type_id\":2,\"name\":\"Att fokusera uppm\\u00e4rksamhet\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(21, 'default', 'created', 'App\\Models\\CategoryMaster', 5, NULL, NULL, '{\"attributes\":{\"id\":5,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":1,\"category_type_id\":2,\"name\":\"Att l\\u00f6sa problem \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(22, 'default', 'created', 'App\\Models\\CategoryMaster', 6, NULL, NULL, '{\"attributes\":{\"id\":6,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":1,\"category_type_id\":2,\"name\":\"Att fatta beslut\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(23, 'default', 'created', 'App\\Models\\CategoryMaster', 7, NULL, NULL, '{\"attributes\":{\"id\":7,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"llm\\u00e4nna uppgifter och \\n                krav \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(24, 'default', 'created', 'App\\Models\\CategoryMaster', 8, NULL, NULL, '{\"attributes\":{\"id\":8,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":7,\"category_type_id\":2,\"name\":\"Att f\\u00f6reta en enstaka uppgift\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(25, 'default', 'created', 'App\\Models\\CategoryMaster', 9, NULL, NULL, '{\"attributes\":{\"id\":9,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":7,\"category_type_id\":2,\"name\":\"Att genomf\\u00f6ra daglig rutin\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(26, 'default', 'created', 'App\\Models\\CategoryMaster', 10, NULL, NULL, '{\"attributes\":{\"id\":10,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":7,\"category_type_id\":2,\"name\":\"Att hantera stress och andra \\n                        psykologiska krav\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(27, 'default', 'created', 'App\\Models\\CategoryMaster', 11, NULL, NULL, '{\"attributes\":{\"id\":11,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":7,\"category_type_id\":2,\"name\":\"Att hantera sitt eget beteende\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(28, 'default', 'created', 'App\\Models\\CategoryMaster', 12, NULL, NULL, '{\"attributes\":{\"id\":12,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"Kommunikation\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(29, 'default', 'created', 'App\\Models\\CategoryMaster', 13, NULL, NULL, '{\"attributes\":{\"id\":13,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att kommunicera genom att ta \\n                        emot talade meddelanden\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(30, 'default', 'created', 'App\\Models\\CategoryMaster', 14, NULL, NULL, '{\"attributes\":{\"id\":14,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att kommunicera genom att ta \\n                        emot icke-verbala \\n                        meddelanden\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(31, 'default', 'created', 'App\\Models\\CategoryMaster', 15, NULL, NULL, '{\"attributes\":{\"id\":15,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att kommunicera genom att ta \\n                        emot meddelanden p\\u00e5 \\n                        teckenspr\\u00e5k\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(32, 'default', 'created', 'App\\Models\\CategoryMaster', 16, NULL, NULL, '{\"attributes\":{\"id\":16,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att kommunicera genom att ta \\n                        emot skrivna meddelanden\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(33, 'default', 'created', 'App\\Models\\CategoryMaster', 17, NULL, NULL, '{\"attributes\":{\"id\":17,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att tala\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(34, 'default', 'created', 'App\\Models\\CategoryMaster', 18, NULL, NULL, '{\"attributes\":{\"id\":18,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att uttrycka sig genom icke-verbala meddelanden\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(35, 'default', 'created', 'App\\Models\\CategoryMaster', 19, NULL, NULL, '{\"attributes\":{\"id\":19,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att uttrycka sig genom \\n                        meddelanden p\\u00e5 teckenspr\\u00e5k\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(36, 'default', 'created', 'App\\Models\\CategoryMaster', 20, NULL, NULL, '{\"attributes\":{\"id\":20,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att skriva meddelanden\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(37, 'default', 'created', 'App\\Models\\CategoryMaster', 21, NULL, NULL, '{\"attributes\":{\"id\":21,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Konversation\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(38, 'default', 'created', 'App\\Models\\CategoryMaster', 22, NULL, NULL, '{\"attributes\":{\"id\":22,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":12,\"category_type_id\":2,\"name\":\"Att anv\\u00e4nda \\n                        kommunikationsutrustningar \\n                        och kommunikationstekniker\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(39, 'default', 'created', 'App\\Models\\CategoryMaster', 23, NULL, NULL, '{\"attributes\":{\"id\":23,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"F\\u00f6rflyttning\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(40, 'default', 'created', 'App\\Models\\CategoryMaster', 24, NULL, NULL, '{\"attributes\":{\"id\":24,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att \\u00e4ndra grundl\\u00e4ggande \\n                        kroppsst\\u00e4llning\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(41, 'default', 'created', 'App\\Models\\CategoryMaster', 25, NULL, NULL, '{\"attributes\":{\"id\":25,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att bibeh\\u00e5lla en kroppsst\\u00e4llning\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(42, 'default', 'created', 'App\\Models\\CategoryMaster', 26, NULL, NULL, '{\"attributes\":{\"id\":26,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att f\\u00f6rflytta sig sj\\u00e4lv\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(43, 'default', 'created', 'App\\Models\\CategoryMaster', 27, NULL, NULL, '{\"attributes\":{\"id\":27,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att lyfta och b\\u00e4ra f\\u00f6rem\\u00e5l\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(44, 'default', 'created', 'App\\Models\\CategoryMaster', 28, NULL, NULL, '{\"attributes\":{\"id\":28,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Handens finmotoriska \\n                        anv\\u00e4ndning\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(45, 'default', 'created', 'App\\Models\\CategoryMaster', 29, NULL, NULL, '{\"attributes\":{\"id\":29,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att g\\u00e5\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(46, 'default', 'created', 'App\\Models\\CategoryMaster', 30, NULL, NULL, '{\"attributes\":{\"id\":30,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att r\\u00f6ra sig omkring p\\u00e5 olika \\n                        s\\u00e4tt\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(47, 'default', 'created', 'App\\Models\\CategoryMaster', 31, NULL, NULL, '{\"attributes\":{\"id\":31,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att r\\u00f6ra sig omkring p\\u00e5 olika \\n                        platser\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(48, 'default', 'created', 'App\\Models\\CategoryMaster', 32, NULL, NULL, '{\"attributes\":{\"id\":32,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":23,\"category_type_id\":2,\"name\":\"Att anv\\u00e4nda transportmedel\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(49, 'default', 'created', 'App\\Models\\CategoryMaster', 33, NULL, NULL, '{\"attributes\":{\"id\":33,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"Personlig v\\u00e5rd\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(50, 'default', 'created', 'App\\Models\\CategoryMaster', 34, NULL, NULL, '{\"attributes\":{\"id\":34,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att tv\\u00e4tta sig\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(51, 'default', 'created', 'App\\Models\\CategoryMaster', 35, NULL, NULL, '{\"attributes\":{\"id\":35,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Kroppsv\\u00e5rd\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(52, 'default', 'created', 'App\\Models\\CategoryMaster', 36, NULL, NULL, '{\"attributes\":{\"id\":36,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att sk\\u00f6ta toalettbehov\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(53, 'default', 'created', 'App\\Models\\CategoryMaster', 37, NULL, NULL, '{\"attributes\":{\"id\":37,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att kl\\u00e4 sig\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(54, 'default', 'created', 'App\\Models\\CategoryMaster', 38, NULL, NULL, '{\"attributes\":{\"id\":38,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att \\u00e4ta\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(55, 'default', 'created', 'App\\Models\\CategoryMaster', 39, NULL, NULL, '{\"attributes\":{\"id\":39,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att dricka \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(56, 'default', 'created', 'App\\Models\\CategoryMaster', 40, NULL, NULL, '{\"attributes\":{\"id\":40,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att sk\\u00f6ta sin egen h\\u00e4lsa\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(57, 'default', 'created', 'App\\Models\\CategoryMaster', 41, NULL, NULL, '{\"attributes\":{\"id\":41,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":33,\"category_type_id\":2,\"name\":\"Att se till sin egen s\\u00e4kerhet\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(58, 'default', 'created', 'App\\Models\\CategoryMaster', 42, NULL, NULL, '{\"attributes\":{\"id\":42,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"Hemliv\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(59, 'default', 'created', 'App\\Models\\CategoryMaster', 43, NULL, NULL, '{\"attributes\":{\"id\":43,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att skaffa bostad\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(60, 'default', 'created', 'App\\Models\\CategoryMaster', 44, NULL, NULL, '{\"attributes\":{\"id\":44,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att skaffa varor och tj\\u00e4nster\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(61, 'default', 'created', 'App\\Models\\CategoryMaster', 45, NULL, NULL, '{\"attributes\":{\"id\":45,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att bereda m\\u00e5ltider\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(62, 'default', 'created', 'App\\Models\\CategoryMaster', 46, NULL, NULL, '{\"attributes\":{\"id\":46,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Hush\\u00e5llsarbete\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(63, 'default', 'created', 'App\\Models\\CategoryMaster', 47, NULL, NULL, '{\"attributes\":{\"id\":47,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att tv\\u00e4tta och torka kl\\u00e4der\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(64, 'default', 'created', 'App\\Models\\CategoryMaster', 48, NULL, NULL, '{\"attributes\":{\"id\":48,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att st\\u00e4da k\\u00f6ksutrymmen och \\n                        k\\u00f6ksredskap \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(65, 'default', 'created', 'App\\Models\\CategoryMaster', 49, NULL, NULL, '{\"attributes\":{\"id\":49,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att st\\u00e4da bostaden (inkl. k\\u00f6k)\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(66, 'default', 'created', 'App\\Models\\CategoryMaster', 50, NULL, NULL, '{\"attributes\":{\"id\":50,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att hantera hush\\u00e5llsapparater\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(67, 'default', 'created', 'App\\Models\\CategoryMaster', 51, NULL, NULL, '{\"attributes\":{\"id\":51,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att f\\u00f6rvara f\\u00f6rn\\u00f6denheter f\\u00f6r \\n                        det dagliga livet\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(68, 'default', 'created', 'App\\Models\\CategoryMaster', 52, NULL, NULL, '{\"attributes\":{\"id\":52,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att avl\\u00e4gsna avfall\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(69, 'default', 'created', 'App\\Models\\CategoryMaster', 53, NULL, NULL, '{\"attributes\":{\"id\":53,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att tv\\u00e4tta och torka kl\\u00e4der och \\n                        textilier med hush\\u00e5llsapparater\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(70, 'default', 'created', 'App\\Models\\CategoryMaster', 54, NULL, NULL, '{\"attributes\":{\"id\":54,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att ta hand om hemmets \\n                        f\\u00f6rem\\u00e5l\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(71, 'default', 'created', 'App\\Models\\CategoryMaster', 55, NULL, NULL, '{\"attributes\":{\"id\":55,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":42,\"category_type_id\":2,\"name\":\"Att bist\\u00e5 andra\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(72, 'default', 'created', 'App\\Models\\CategoryMaster', 56, NULL, NULL, '{\"attributes\":{\"id\":56,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"Mellanm\\u00e4nskliga \\n                interaktioner\\n                och relationer\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(73, 'default', 'created', 'App\\Models\\CategoryMaster', 57, NULL, NULL, '{\"attributes\":{\"id\":57,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":56,\"category_type_id\":2,\"name\":\"Sammansatta mellanm\\u00e4nskliga \\n                        interaktioner\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(74, 'default', 'created', 'App\\Models\\CategoryMaster', 58, NULL, NULL, '{\"attributes\":{\"id\":58,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":56,\"category_type_id\":2,\"name\":\"Att ha kontakt med ok\\u00e4nda \\n                        personer\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(75, 'default', 'created', 'App\\Models\\CategoryMaster', 59, NULL, NULL, '{\"attributes\":{\"id\":59,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":56,\"category_type_id\":2,\"name\":\"Formella relationer\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(76, 'default', 'created', 'App\\Models\\CategoryMaster', 60, NULL, NULL, '{\"attributes\":{\"id\":60,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":56,\"category_type_id\":2,\"name\":\"Informella sociala relationer \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(77, 'default', 'created', 'App\\Models\\CategoryMaster', 61, NULL, NULL, '{\"attributes\":{\"id\":61,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":56,\"category_type_id\":2,\"name\":\"Familjerelationer\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(78, 'default', 'created', 'App\\Models\\CategoryMaster', 62, NULL, NULL, '{\"attributes\":{\"id\":62,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":56,\"category_type_id\":2,\"name\":\"Parrelationer*\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(79, 'default', 'created', 'App\\Models\\CategoryMaster', 63, NULL, NULL, '{\"attributes\":{\"id\":63,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"Utbildning, arbete, \\n                syssels\\u00e4ttning och \\n                ekonomiskt liv*\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(80, 'default', 'created', 'App\\Models\\CategoryMaster', 64, NULL, NULL, '{\"attributes\":{\"id\":64,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":63,\"category_type_id\":2,\"name\":\"Utbildning, annan specificerad \\n                        och ospecificerad\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(81, 'default', 'created', 'App\\Models\\CategoryMaster', 65, NULL, NULL, '{\"attributes\":{\"id\":65,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":63,\"category_type_id\":2,\"name\":\"Att skaffa, beh\\u00e5lla och sluta ett \\n                        arbete\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(82, 'default', 'created', 'App\\Models\\CategoryMaster', 66, NULL, NULL, '{\"attributes\":{\"id\":66,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":63,\"category_type_id\":2,\"name\":\"Arbete och syssels\\u00e4ttning, \\n                        annat specificerat och \\n                        ospecificerat\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(83, 'default', 'created', 'App\\Models\\CategoryMaster', 67, NULL, NULL, '{\"attributes\":{\"id\":67,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":63,\"category_type_id\":2,\"name\":\"Grundl\\u00e4ggande ekonomiska \\n                        transaktioner\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(84, 'default', 'created', 'App\\Models\\CategoryMaster', 68, NULL, NULL, '{\"attributes\":{\"id\":68,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":63,\"category_type_id\":2,\"name\":\"Komplexa ekonomiska \\n                        transaktioner\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(85, 'default', 'created', 'App\\Models\\CategoryMaster', 69, NULL, NULL, '{\"attributes\":{\"id\":69,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":63,\"category_type_id\":2,\"name\":\"Ekonomisk sj\\u00e4lvf\\u00f6rs\\u00f6rjning\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(86, 'default', 'created', 'App\\Models\\CategoryMaster', 70, NULL, NULL, '{\"attributes\":{\"id\":70,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"Samh\\u00e4llsgemenskap, socialt \\n                och medborgligt liv\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(87, 'default', 'created', 'App\\Models\\CategoryMaster', 71, NULL, NULL, '{\"attributes\":{\"id\":71,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":70,\"category_type_id\":2,\"name\":\"Rekreation och fritid \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(88, 'default', 'created', 'App\\Models\\CategoryMaster', 72, NULL, NULL, '{\"attributes\":{\"id\":72,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":70,\"category_type_id\":2,\"name\":\"Religion och andlighet \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(89, 'default', 'created', 'App\\Models\\CategoryMaster', 73, NULL, NULL, '{\"attributes\":{\"id\":73,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":70,\"category_type_id\":2,\"name\":\"Politiskt liv och medborgarskap\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(90, 'default', 'created', 'App\\Models\\CategoryMaster', 74, NULL, NULL, '{\"attributes\":{\"id\":74,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"KROPPSFUNKTIONER \\n                - neds\\u00e4ttning inom \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(91, 'default', 'created', 'App\\Models\\CategoryMaster', 75, NULL, NULL, '{\"attributes\":{\"id\":75,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":74,\"category_type_id\":2,\"name\":\"K\\u00e4nsla av trygghet \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(92, 'default', 'created', 'App\\Models\\CategoryMaster', 76, NULL, NULL, '{\"attributes\":{\"id\":76,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"KROPPSSTRUKTURER \\n                - avvikelse inom \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(93, 'default', 'created', 'App\\Models\\CategoryMaster', 77, NULL, NULL, '{\"attributes\":{\"id\":77,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":2,\"name\":\"OMGIVNINGSFAKTORER\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(94, 'default', 'created', 'App\\Models\\CategoryMaster', 78, NULL, NULL, '{\"attributes\":{\"id\":78,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":77,\"category_type_id\":2,\"name\":\"Personligt st\\u00f6d fr\\u00e5n person \\n                        som v\\u00e5rdar eller st\\u00f6djer en \\n                        n\\u00e4rst\\u00e5ende\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(95, 'default', 'created', 'App\\Models\\CategoryMaster', 79, NULL, NULL, '{\"attributes\":{\"id\":79,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":77,\"category_type_id\":2,\"name\":\"Service, tj\\u00e4nster, system och \\n                        policies \\u2013 Upplevd kvalitet \",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(96, 'default', 'created', 'App\\Models\\CategoryMaster', 80, NULL, NULL, '{\"attributes\":{\"id\":80,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":null,\"category_type_id\":4,\"name\":\"Ej utf\\u00f6rd insatser\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(97, 'default', 'created', 'App\\Models\\CategoryMaster', 81, NULL, NULL, '{\"attributes\":{\"id\":81,\"top_most_parent_id\":1,\"created_by\":1,\"parent_id\":80,\"category_type_id\":4,\"name\":\"Ej utf\\u00f6rd insatser subcat\",\"category_color\":\"#ff0000\",\"is_global\":1,\"status\":1,\"follow_up_image\":null,\"entry_mode\":\"Web\",\"created_at\":\"2022-09-21T10:12:52.000000Z\",\"updated_at\":\"2022-09-21T10:12:52.000000Z\",\"deleted_at\":null}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `created_at`, `updated_at`) VALUES
(98, 'default', 'created', 'App\\Models\\User', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"unique_id\":\"M3bLuy4qFeBz\",\"custom_unique_id\":null,\"user_type_id\":2,\"category_id\":null,\"top_most_parent_id\":null,\"parent_id\":null,\"dept_id\":null,\"role_id\":2,\"company_type_id\":\"[3,2,1]\",\"branch_id\":null,\"govt_id\":null,\"name\":\"TS Corp\",\"email\":\"company@gmail.com\",\"email_verified_at\":null,\"password\":\"$2y$10$q4D6VtTgJW4uzIbh5F6Cz.egIBYlkkoigsNmB0aOPN2Gg7anTynT.\",\"contact_number\":\"1235647980\",\"gender\":null,\"personal_number\":null,\"organization_number\":\"7894561230\",\"patient_type_id\":null,\"country_id\":209,\"city\":\"stockholm\",\"postal_area\":\"87954\",\"zipcode\":\"13254\",\"full_address\":\"Address\",\"licence_key\":\"DLL51-5AJ78-5MUKC-P2HVQ\",\"licence_end_date\":\"2022-12-30\",\"licence_status\":1,\"employee_type\":null,\"joining_date\":null,\"establishment_year\":1968,\"user_color\":null,\"disease_description\":null,\"created_by\":null,\"password_token\":null,\"is_file_required\":0,\"is_secret\":0,\"status\":1,\"is_fake\":0,\"is_password_change\":0,\"documents\":\"null\",\"step_one\":0,\"step_two\":0,\"step_three\":0,\"step_four\":0,\"step_five\":0,\"entry_mode\":\"web-0.0.1\",\"contact_person_number\":null,\"contact_person_name\":\"T.S\",\"language_id\":1,\"contract_type\":null,\"contract_value\":null,\"avatar\":\"https:\\/\\/aceuss.3mad.in\\/uploads\\/no-image.png\",\"schedule_start_date\":null,\"report_verify\":null,\"verification_method\":\"normal\",\"is_family_member\":0,\"is_caretaker\":0,\"is_contact_person\":0,\"is_guardian\":0,\"is_other\":0,\"is_other_name\":null,\"remember_token\":null,\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(99, 'default', 'created', 'App\\Models\\LicenceHistory', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"top_most_parent_id\":2,\"created_by\":1,\"licence_key\":\"DLL51-5AJ78-5MUKC-P2HVQ\",\"module_attached\":\"[4,3,2,1,5]\",\"package_details\":\"{\\\"id\\\":1,\\\"name\\\":\\\"Basic pack\\\",\\\"price\\\":540,\\\"is_on_offer\\\":1,\\\"discount_type\\\":\\\"1\\\",\\\"discount_value\\\":67,\\\"discounted_price\\\":178.19999999999998863131622783839702606201171875,\\\"validity_in_days\\\":100,\\\"number_of_patients\\\":100,\\\"number_of_employees\\\":50,\\\"bankid_charges\\\":null,\\\"sms_charges\\\":null,\\\"is_sms_enable\\\":0,\\\"is_enable_bankid_charges\\\":0,\\\"status\\\":1,\\\"entry_mode\\\":null,\\\"created_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"updated_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"deleted_at\\\":null}\",\"active_from\":\"2022-09-21\",\"expire_at\":\"2022-12-30\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(100, 'default', 'created', 'App\\Models\\LicenceKeyManagement', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"top_most_parent_id\":2,\"created_by\":1,\"cancelled_by\":null,\"licence_key\":\"DLL51-5AJ78-5MUKC-P2HVQ\",\"module_attached\":\"[4,3,2,1,5]\",\"package_details\":\"{\\\"id\\\":1,\\\"name\\\":\\\"Basic pack\\\",\\\"price\\\":540,\\\"is_on_offer\\\":1,\\\"discount_type\\\":\\\"1\\\",\\\"discount_value\\\":67,\\\"discounted_price\\\":178.19999999999998863131622783839702606201171875,\\\"validity_in_days\\\":100,\\\"number_of_patients\\\":100,\\\"number_of_employees\\\":50,\\\"bankid_charges\\\":null,\\\"sms_charges\\\":null,\\\"is_sms_enable\\\":0,\\\"is_enable_bankid_charges\\\":0,\\\"status\\\":1,\\\"entry_mode\\\":null,\\\"created_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"updated_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"deleted_at\\\":null}\",\"active_from\":\"2022-09-21\",\"expire_at\":\"2022-12-30\",\"is_used\":1,\"reason_for_cancellation\":null,\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(101, 'default', 'created', 'App\\Models\\Subscription', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"user_id\":2,\"package_id\":1,\"package_details\":{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T13:42:51.000000Z\",\"updated_at\":\"2022-09-21T13:42:51.000000Z\",\"deleted_at\":null},\"licence_key\":\"DLL51-5AJ78-5MUKC-P2HVQ\",\"start_date\":\"2022-09-21\",\"end_date\":\"2022-12-30\",\"status\":1,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\"}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(102, 'default', 'created', 'App\\Models\\AssigneModule', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"user_id\":2,\"module_id\":4,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(103, 'default', 'created', 'App\\Models\\AssigneModule', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"user_id\":2,\"module_id\":3,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(104, 'default', 'created', 'App\\Models\\AssigneModule', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":3,\"user_id\":2,\"module_id\":2,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(105, 'default', 'created', 'App\\Models\\AssigneModule', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":4,\"user_id\":2,\"module_id\":1,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(106, 'default', 'created', 'App\\Models\\AssigneModule', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":5,\"user_id\":2,\"module_id\":5,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:16:26.000000Z\",\"updated_at\":\"2022-09-21T10:16:26.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(107, 'default', 'created', 'App\\Models\\User', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":3,\"unique_id\":\"iRIqLARHIFNW\",\"custom_unique_id\":null,\"user_type_id\":2,\"category_id\":null,\"top_most_parent_id\":null,\"parent_id\":null,\"dept_id\":null,\"role_id\":2,\"company_type_id\":\"[3]\",\"branch_id\":null,\"govt_id\":null,\"name\":\"Comp 2\",\"email\":\"company2@gmail.com\",\"email_verified_at\":null,\"password\":\"$2y$10$hirPwjwsxfHvysdM.OP8jeg87PQp4COMQzML1pJNpbwcOjNPOZPSO\",\"contact_number\":\"1234567410\",\"gender\":null,\"personal_number\":null,\"organization_number\":\"1234567890\",\"patient_type_id\":null,\"country_id\":209,\"city\":\"Stcock\",\"postal_area\":\"12457\",\"zipcode\":\"12454\",\"full_address\":\"Address\",\"licence_key\":\"52LAE-H3NCK-L1M5T-K6J3C\",\"licence_end_date\":\"2022-12-30\",\"licence_status\":1,\"employee_type\":null,\"joining_date\":null,\"establishment_year\":1995,\"user_color\":null,\"disease_description\":null,\"created_by\":null,\"password_token\":null,\"is_file_required\":0,\"is_secret\":0,\"status\":1,\"is_fake\":0,\"is_password_change\":0,\"documents\":\"null\",\"step_one\":0,\"step_two\":0,\"step_three\":0,\"step_four\":0,\"step_five\":0,\"entry_mode\":\"web-0.0.1\",\"contact_person_number\":null,\"contact_person_name\":\"C.P\",\"language_id\":1,\"contract_type\":null,\"contract_value\":null,\"avatar\":\"https:\\/\\/aceuss.3mad.in\\/uploads\\/no-image.png\",\"schedule_start_date\":null,\"report_verify\":null,\"verification_method\":\"normal\",\"is_family_member\":0,\"is_caretaker\":0,\"is_contact_person\":0,\"is_guardian\":0,\"is_other\":0,\"is_other_name\":null,\"remember_token\":null,\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(108, 'default', 'created', 'App\\Models\\LicenceHistory', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"top_most_parent_id\":3,\"created_by\":1,\"licence_key\":\"52LAE-H3NCK-L1M5T-K6J3C\",\"module_attached\":\"[4,3,2,1]\",\"package_details\":\"{\\\"id\\\":1,\\\"name\\\":\\\"Basic pack\\\",\\\"price\\\":540,\\\"is_on_offer\\\":1,\\\"discount_type\\\":\\\"1\\\",\\\"discount_value\\\":67,\\\"discounted_price\\\":178.19999999999998863131622783839702606201171875,\\\"validity_in_days\\\":100,\\\"number_of_patients\\\":100,\\\"number_of_employees\\\":50,\\\"bankid_charges\\\":null,\\\"sms_charges\\\":null,\\\"is_sms_enable\\\":0,\\\"is_enable_bankid_charges\\\":0,\\\"status\\\":1,\\\"entry_mode\\\":null,\\\"created_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"updated_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"deleted_at\\\":null}\",\"active_from\":\"2022-09-21\",\"expire_at\":\"2022-12-30\",\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(109, 'default', 'created', 'App\\Models\\LicenceKeyManagement', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"top_most_parent_id\":3,\"created_by\":1,\"cancelled_by\":null,\"licence_key\":\"52LAE-H3NCK-L1M5T-K6J3C\",\"module_attached\":\"[4,3,2,1]\",\"package_details\":\"{\\\"id\\\":1,\\\"name\\\":\\\"Basic pack\\\",\\\"price\\\":540,\\\"is_on_offer\\\":1,\\\"discount_type\\\":\\\"1\\\",\\\"discount_value\\\":67,\\\"discounted_price\\\":178.19999999999998863131622783839702606201171875,\\\"validity_in_days\\\":100,\\\"number_of_patients\\\":100,\\\"number_of_employees\\\":50,\\\"bankid_charges\\\":null,\\\"sms_charges\\\":null,\\\"is_sms_enable\\\":0,\\\"is_enable_bankid_charges\\\":0,\\\"status\\\":1,\\\"entry_mode\\\":null,\\\"created_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"updated_at\\\":\\\"2022-09-21T13:42:51.000000Z\\\",\\\"deleted_at\\\":null}\",\"active_from\":\"2022-09-21\",\"expire_at\":\"2022-12-30\",\"is_used\":1,\"reason_for_cancellation\":null,\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(110, 'default', 'created', 'App\\Models\\Subscription', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"user_id\":3,\"package_id\":1,\"package_details\":{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T13:42:51.000000Z\",\"updated_at\":\"2022-09-21T13:42:51.000000Z\",\"deleted_at\":null},\"licence_key\":\"52LAE-H3NCK-L1M5T-K6J3C\",\"start_date\":\"2022-09-21\",\"end_date\":\"2022-12-30\",\"status\":1,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\"}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(111, 'default', 'created', 'App\\Models\\AssigneModule', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":6,\"user_id\":3,\"module_id\":4,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(112, 'default', 'created', 'App\\Models\\AssigneModule', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":7,\"user_id\":3,\"module_id\":3,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(113, 'default', 'created', 'App\\Models\\AssigneModule', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":8,\"user_id\":3,\"module_id\":2,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(114, 'default', 'created', 'App\\Models\\AssigneModule', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":9,\"user_id\":3,\"module_id\":1,\"entry_mode\":\"web-0.0.1\",\"created_at\":\"2022-09-21T10:18:28.000000Z\",\"updated_at\":\"2022-09-21T10:18:28.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(115, 'default', 'created', 'App\\Models\\User', 4, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":4,\"unique_id\":\"lrNNyceq2uVx\",\"custom_unique_id\":\"16637-557-02053\",\"user_type_id\":6,\"category_id\":null,\"top_most_parent_id\":2,\"parent_id\":2,\"dept_id\":null,\"role_id\":20,\"company_type_id\":\"[3]\",\"branch_id\":2,\"govt_id\":null,\"name\":\"TS Arvida Fahlgren\",\"email\":\"arvida.fahlgren@spamherelots.com\",\"email_verified_at\":null,\"password\":\"$2y$10$gqrn\\/gWqIMpZRMeKk16KROu627w9NmxQule3\\/YK3sXEK\\/4H1fTWUG\",\"contact_number\":\"04913975983\",\"gender\":\"male\",\"personal_number\":\"196001208369\",\"organization_number\":null,\"patient_type_id\":\"[\\\"5\\\"]\",\"country_id\":null,\"city\":null,\"postal_area\":null,\"zipcode\":null,\"full_address\":\"579 32 H\\u00d6GSBY\",\"licence_key\":null,\"licence_end_date\":null,\"licence_status\":1,\"employee_type\":null,\"joining_date\":null,\"establishment_year\":null,\"user_color\":\"#1fff\",\"disease_description\":\"Better Care and Better Understanding.\",\"created_by\":2,\"password_token\":null,\"is_file_required\":0,\"is_secret\":0,\"status\":1,\"is_fake\":0,\"is_password_change\":0,\"documents\":\"[]\",\"step_one\":1,\"step_two\":1,\"step_three\":1,\"step_four\":1,\"step_five\":1,\"entry_mode\":\"web-0.0.1\",\"contact_person_number\":null,\"contact_person_name\":null,\"language_id\":1,\"contract_type\":null,\"contract_value\":null,\"avatar\":\"https:\\/\\/aceuss.3mad.in\\/uploads\\/no-image.png\",\"schedule_start_date\":null,\"report_verify\":null,\"verification_method\":null,\"is_family_member\":0,\"is_caretaker\":0,\"is_contact_person\":0,\"is_guardian\":0,\"is_other\":0,\"is_other_name\":null,\"remember_token\":null,\"created_at\":\"2022-09-21T10:25:55.000000Z\",\"updated_at\":\"2022-09-21T10:25:55.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:25:55', '2022-09-21 12:25:55'),
(116, 'default', 'created', 'App\\Models\\PersonalInfoDuringIp', 1, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":1,\"patient_id\":4,\"ip_id\":null,\"user_id\":null,\"follow_up_id\":null,\"is_presented\":0,\"is_participated\":0,\"how_helped\":null,\"entry_mode\":null,\"is_approval_requested\":0,\"created_at\":\"2022-09-21T10:25:58.000000Z\",\"updated_at\":\"2022-09-21T10:25:58.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:25:58', '2022-09-21 12:25:58'),
(117, 'default', 'created', 'App\\Models\\User', 5, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":5,\"unique_id\":\"KUeRuY5WkjGF\",\"custom_unique_id\":null,\"user_type_id\":8,\"category_id\":null,\"top_most_parent_id\":2,\"parent_id\":4,\"dept_id\":null,\"role_id\":22,\"company_type_id\":null,\"branch_id\":2,\"govt_id\":null,\"name\":\"Tyra Christiansson\",\"email\":\"tyra.christiansson@sogetthis.com\",\"email_verified_at\":null,\"password\":\"$2y$10$Yyu76P..mjvmvT\\/bSaqlUOEfFkUbkK8\\/tZEykGbkkurkl67X9YDQ6\",\"contact_number\":\"06904618596\",\"gender\":null,\"personal_number\":null,\"organization_number\":null,\"patient_type_id\":null,\"country_id\":null,\"city\":\"Hudiksv\\u00e4gen\",\"postal_area\":\"2313\",\"zipcode\":\"21312\",\"full_address\":\"841 92 \\u00c5NGE\",\"licence_key\":null,\"licence_end_date\":null,\"licence_status\":1,\"employee_type\":null,\"joining_date\":null,\"establishment_year\":null,\"user_color\":null,\"disease_description\":null,\"created_by\":null,\"password_token\":null,\"is_file_required\":0,\"is_secret\":0,\"status\":1,\"is_fake\":0,\"is_password_change\":0,\"documents\":null,\"step_one\":0,\"step_two\":0,\"step_three\":0,\"step_four\":0,\"step_five\":0,\"entry_mode\":null,\"contact_person_number\":null,\"contact_person_name\":null,\"language_id\":1,\"contract_type\":\"1\",\"contract_value\":\"0.00\",\"avatar\":\"https:\\/\\/aceuss.3mad.in\\/uploads\\/no-image.png\",\"schedule_start_date\":null,\"report_verify\":\"no\",\"verification_method\":\"normal\",\"is_family_member\":1,\"is_caretaker\":0,\"is_contact_person\":0,\"is_guardian\":0,\"is_other\":0,\"is_other_name\":\"0\",\"remember_token\":null,\"created_at\":\"2022-09-21T10:25:58.000000Z\",\"updated_at\":\"2022-09-21T10:25:58.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:25:58', '2022-09-21 12:25:58'),
(118, 'default', 'updated', 'App\\Models\\PersonalInfoDuringIp', 1, 'App\\Models\\User', 2, '{\"attributes\":{\"user_id\":5,\"is_approval_requested\":0},\"old\":{\"user_id\":null,\"is_approval_requested\":null}}', '2022-09-21 12:25:58', '2022-09-21 12:25:58'),
(119, 'default', 'created', 'App\\Models\\PersonalInfoDuringIp', 2, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":2,\"patient_id\":4,\"ip_id\":null,\"user_id\":null,\"follow_up_id\":null,\"is_presented\":0,\"is_participated\":0,\"how_helped\":null,\"entry_mode\":null,\"is_approval_requested\":0,\"created_at\":\"2022-09-21T10:26:00.000000Z\",\"updated_at\":\"2022-09-21T10:26:00.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:26:00', '2022-09-21 12:26:00'),
(120, 'default', 'created', 'App\\Models\\User', 6, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":6,\"unique_id\":\"DoQKvm4vAvOZ\",\"custom_unique_id\":null,\"user_type_id\":9,\"category_id\":null,\"top_most_parent_id\":2,\"parent_id\":4,\"dept_id\":null,\"role_id\":23,\"company_type_id\":null,\"branch_id\":2,\"govt_id\":null,\"name\":\"Christer Cedergren\",\"email\":\"christer.cedergren@mailinater.com\",\"email_verified_at\":null,\"password\":\"$2y$10$R33w2wEnoYeI9KrwNLAlxOpU\\/IaAwvgj5wUYTZkAz.XnWh2Kn1woq\",\"contact_number\":\"09268877474\",\"gender\":null,\"personal_number\":null,\"organization_number\":null,\"patient_type_id\":null,\"country_id\":null,\"city\":\"Bergs\\u00e4ng krusberg\",\"postal_area\":\"12450\",\"zipcode\":\"78451\",\"full_address\":\"956 31 \\u00d6VERKALIX\",\"licence_key\":null,\"licence_end_date\":null,\"licence_status\":1,\"employee_type\":null,\"joining_date\":null,\"establishment_year\":null,\"user_color\":null,\"disease_description\":null,\"created_by\":null,\"password_token\":null,\"is_file_required\":0,\"is_secret\":0,\"status\":1,\"is_fake\":0,\"is_password_change\":0,\"documents\":null,\"step_one\":0,\"step_two\":0,\"step_three\":0,\"step_four\":0,\"step_five\":0,\"entry_mode\":null,\"contact_person_number\":null,\"contact_person_name\":null,\"language_id\":1,\"contract_type\":\"1\",\"contract_value\":\"0.00\",\"avatar\":\"https:\\/\\/aceuss.3mad.in\\/uploads\\/no-image.png\",\"schedule_start_date\":null,\"report_verify\":\"no\",\"verification_method\":\"normal\",\"is_family_member\":0,\"is_caretaker\":0,\"is_contact_person\":1,\"is_guardian\":0,\"is_other\":0,\"is_other_name\":\"0\",\"remember_token\":null,\"created_at\":\"2022-09-21T10:26:00.000000Z\",\"updated_at\":\"2022-09-21T10:26:00.000000Z\",\"deleted_at\":null}}', '2022-09-21 12:26:00', '2022-09-21 12:26:00'),
(121, 'default', 'updated', 'App\\Models\\PersonalInfoDuringIp', 2, 'App\\Models\\User', 2, '{\"attributes\":{\"user_id\":6,\"is_approval_requested\":0},\"old\":{\"user_id\":null,\"is_approval_requested\":null}}', '2022-09-21 12:26:00', '2022-09-21 12:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `activity_options`
--

CREATE TABLE `activity_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `option` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_journal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `is_deviation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_options`
--

INSERT INTO `activity_options` (`id`, `option`, `is_journal`, `is_deviation`, `created_at`, `updated_at`) VALUES
(1, 'Efforts managed with staff on time', 0, 0, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(2, 'Efforts managed with staff not on time', 1, 0, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(3, 'Could fix himself', 1, 0, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(4, 'The customer did not want', 1, 0, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(5, 'Staff could not', 1, 1, '2022-09-21 15:42:51', '2022-09-21 15:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `activity_time_logs`
--

CREATE TABLE `activity_time_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  `action_time` time DEFAULT NULL,
  `time_diff` int(11) DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:Done,2:Not Done,3:notapplicable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_files`
--

CREATE TABLE `admin_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `user_type_id` int(11) DEFAULT NULL,
  `company_ids` text COLLATE utf8mb4_unicode_ci COMMENT 'if admin wants to share this file to selected company',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agencies`
--

CREATE TABLE `agencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agencies`
--

INSERT INTO `agencies` (`id`, `name`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 'Agency1', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(2, 'Agency2', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(3, 'Agency3', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(4, 'Agency4', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `agency_weekly_hours`
--

CREATE TABLE `agency_weekly_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `assigned_hours_per_day` decimal(10,2) NOT NULL DEFAULT '0.00',
  `assigned_hours_per_week` decimal(10,2) NOT NULL DEFAULT '0.00',
  `assigned_hours_per_month` decimal(10,2) NOT NULL DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `scheduled_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `completed_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remaining_hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_patient` tinyint(1) DEFAULT '0' COMMENT '1 for approved,0 for not approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agency_weekly_hours`
--

INSERT INTO `agency_weekly_hours` (`id`, `user_id`, `name`, `assigned_hours`, `assigned_hours_per_day`, `assigned_hours_per_week`, `assigned_hours_per_month`, `start_date`, `end_date`, `scheduled_hours`, `completed_hours`, `remaining_hours`, `entry_mode`, `approved_by_patient`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, NULL, '450.00', '3.38', '23.68', '101.50', '2022-09-21', '2023-01-31', '0.00', '0.00', '0.00', NULL, 0, '2022-09-21 12:25:58', '2022-09-21 12:25:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assigne_modules`
--

CREATE TABLE `assigne_modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assigne_modules`
--

INSERT INTO `assigne_modules` (`id`, `user_id`, `module_id`, `entry_mode`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 4, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26', NULL),
(2, 2, 3, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26', NULL),
(3, 2, 2, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26', NULL),
(4, 2, 1, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26', NULL),
(5, 2, 5, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26', NULL),
(6, 3, 4, 'web-0.0.1', '2022-09-21 12:18:28', '2022-09-21 12:18:28', NULL),
(7, 3, 3, 'web-0.0.1', '2022-09-21 12:18:28', '2022-09-21 12:18:28', NULL),
(8, 3, 2, 'web-0.0.1', '2022-09-21 12:18:28', '2022-09-21 12:18:28', NULL),
(9, 3, 1, 'web-0.0.1', '2022-09-21 12:18:28', '2022-09-21 12:18:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assign_tasks`
--

CREATE TABLE `assign_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `assigned_by` bigint(20) UNSIGNED NOT NULL,
  `assignment_date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Not Done,1:done',
  `is_notify` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Not send,1:send',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clearance_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bookmark_master_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_masters`
--

CREATE TABLE `bookmark_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `target` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_types` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookmark_masters`
--

INSERT INTO `bookmark_masters` (`id`, `target`, `title`, `icon`, `icon_type`, `user_types`, `link`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', 'Analytics Dashboard', 'Home', NULL, NULL, '/dashboard/analytics', '2022-09-08 15:22:55', '2022-09-08 15:22:55'),
(2, 'country-list-bookmark', 'country-list', 'Flag', 'feather', '[16,1]', '/country-list', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(3, 'licences-bookmark', 'Licences', 'Key', 'feather', '[1,16]', '/settings/licences', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(4, 'red-word-bookmark', 'red-word', 'SignalCellular0Bar', 'material', '[1,16,2,3,11]', '/master/red-word', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(5, 'menu-packages-bookmark', 'packages', 'Package', 'feather', '[1,16]', '/master/packages', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(6, 'activity-stats-bookmark', 'statistics', 'TrendingUp', 'feather', '[2,3,11]', '/users/stats', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(7, 'ov-bookmark', 'ov', 'Crosshair', 'feather', '[2,3,11]', '/schedule/ov', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(8, 'schedule_template-bookmark', 'schedule-template', 'Anchor', 'feather', '[2,3,11]', '/schedule/template', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(9, 'paragraphs-bookmark', 'paragraphs', 'PartyMode', 'material', '[1,16,2,3,11]', '/master/paragraphs', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(10, 'journal-bookmark', 'journal', 'FileText', 'feather', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/journal', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(11, 'stampling-bookmark', 'Stampling', 'Trello', 'feather', '[2,3,11]', '/stampling', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(12, 'settings-bookmark', 'Settings', 'Settings', 'feather', '[2,3,11]', '/settings/general', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(13, 'notifications-bookmark', 'Notifications', 'Bell', 'feather', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/notifications', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(14, 'hours_approval-bookmark', 'hours-approval', 'Columns', 'feather', '[2,3,11]', '/employee/hoursApproval', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(15, 'userTypes-bookmark', 'User Types', 'Users', 'feather', '[1,16]', '/master/user-types', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(16, 'roles-bookmark', 'Roles', 'Layers', 'feather', '[1,16,2,3,11]', '/master/roles-permissions', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(17, 'tasks-bookmark', 'tasks', 'Target', 'feather', '[1,16,2,3,11]', '/master/tasks', '2022-09-10 11:52:00', '2022-09-10 11:52:00'),
(18, 'shifts-bookmark', 'work-shift', 'Slack', 'feather', '[2,3,11]', '/schedule/shifts', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(19, 'home-bookmark', 'Dashboard', 'HomeWorkOutlined', 'material', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/dashboard/home', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(20, 'file-log-bookmark', 'file-access-log', 'Circle', 'feather', '[1,16]', '/log/file', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(21, 'language-import1996-bookmark', 'import-language', 'Language', 'material', '[1,16]', '/master/languages', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(22, 'schedule-bookmark', 'schedule', 'Clock', 'feather', '[2,3,11]', '/schedule/calendar', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(23, 'deviation-stats-bookmark', 'Deviation Stats', 'TrendingUp', 'feather', '[2,3,11]', '/deviation-stats', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(24, 'companies-bookmark', 'Companies', 'BusinessOutlined', 'material', '[1,16]', '/companies', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(25, 'manageFiles-bookmark', 'Manage Files', 'File', 'feather', '[16,1,2,3,11]', '/settings/manageFiles', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(26, 'trashed-activity-bookmark', 'Trashed Activity', 'LocalActivityOutlined', 'material', '[2,3,11]', '/trashed-activity', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(27, 'questions-bookmark', 'questions', 'QuestionAnswer', 'material', '[1,16,2,3,11]', '/questions', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(28, 'calendar-bookmark', 'calendar', 'Calendar', 'feather', '[2,3,11]', '/calendar', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(29, 'companyTypes-bookmark', 'Company Types', 'Briefcase', 'feather', '[1,16]', '/master/company-types', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(30, 'followup-bookmark', 'followups', 'StopCircle', 'feather', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/followups', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(31, 'implementations-bookmark', 'ip', 'Rotate90DegreesCcw', 'material', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/implementation-plans', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(32, 'emailTemplate-bookmark', 'email-notifications', 'Mail', 'feather', '[16,1]', '/settings/email-template', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(33, 'bankID-log-bookmark', 'bankID-log', 'Circle', 'feather', '[1,16]', '/log/bankID', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(34, 'smsTemplate-bookmark', 'SMS Template', 'MessageSquare', 'feather', '[16,1]', '/settings/sms-template', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(35, 'chat-msg-bookmark', 'Messages', 'MessageSquare', 'feather', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/messages', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(36, 'active-bookmark', 'activity-classification', 'ClassOutlined', 'material', '[1,16]', '/master/activity-classification', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(37, 'modules-bookmark', 'Modules', 'Bluetooth', 'feather', '[1,16]', '/master/modules', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(38, 'stats-bookmark', 'stats', 'TrendingUp', 'feather', '[2,3,11]', '/stats', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(39, 'sms-log-bookmark', 'sms-log', 'Circle', 'feather', '[1,16]', '/log/sms', '2022-09-10 11:52:02', '2022-09-10 11:52:02'),
(40, 'activity-log-bookmark', 'activity-log', 'Circle', 'feather', '[1,16]', '/log/activity', '2022-09-10 11:52:01', '2022-09-10 11:52:01'),
(41, 'company-package-bookmark', 'company-package', 'Package', 'feather', '[2,3,11]', '/company-package', '2022-09-10 11:52:02', '2022-09-10 11:52:02'),
(42, 'branch-bookmark', 'branch', 'Pocket', 'feather', '[2,3,11]', '/branch', '2022-09-10 11:52:02', '2022-09-10 11:52:02'),
(43, 'requests-bookmark', 'Requests', 'HelpCircle', 'feather', '[2,3,11,1,16]', '/requests', '2022-09-10 11:52:02', '2022-09-10 11:52:02'),
(44, 'deviation-bookmark', 'Deviation', 'FileText', 'feather', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/deviation', '2022-09-10 11:52:02', '2022-09-10 11:52:02'),
(45, 'activity-bookmark', 'activity', 'Activity', 'feather', '[1,2,3,16,4,5,6,7,8,9,10,11]', '/timeline', '2022-09-10 11:52:02', '2022-09-10 11:52:02'),
(46, 'leave-bookmark', 'leave', 'Twitch', 'feather', '[2,3,11]', '/schedule/leave/calendar', '2022-09-10 11:52:02', '2022-09-10 11:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `category_masters`
--

CREATE TABLE `category_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_type_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `follow_up_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_masters`
--

INSERT INTO `category_masters` (`id`, `top_most_parent_id`, `created_by`, `parent_id`, `category_type_id`, `name`, `category_color`, `is_global`, `status`, `follow_up_image`, `entry_mode`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, 2, 'Lärande och att tillämpa kunskap', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(2, 1, 1, 1, 2, 'Att ta reda på information', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(3, 1, 1, 1, 2, 'Att förvärva färdigheter ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(4, 1, 1, 1, 2, 'Att fokusera uppmärksamhet', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(5, 1, 1, 1, 2, 'Att lösa problem ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(6, 1, 1, 1, 2, 'Att fatta beslut', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(7, 1, 1, NULL, 2, 'llmänna uppgifter och \n                krav ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(8, 1, 1, 7, 2, 'Att företa en enstaka uppgift', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(9, 1, 1, 7, 2, 'Att genomföra daglig rutin', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(10, 1, 1, 7, 2, 'Att hantera stress och andra \n                        psykologiska krav', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(11, 1, 1, 7, 2, 'Att hantera sitt eget beteende', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(12, 1, 1, NULL, 2, 'Kommunikation', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(13, 1, 1, 12, 2, 'Att kommunicera genom att ta \n                        emot talade meddelanden', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(14, 1, 1, 12, 2, 'Att kommunicera genom att ta \n                        emot icke-verbala \n                        meddelanden', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(15, 1, 1, 12, 2, 'Att kommunicera genom att ta \n                        emot meddelanden på \n                        teckenspråk', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(16, 1, 1, 12, 2, 'Att kommunicera genom att ta \n                        emot skrivna meddelanden', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(17, 1, 1, 12, 2, 'Att tala', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(18, 1, 1, 12, 2, 'Att uttrycka sig genom icke-verbala meddelanden', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(19, 1, 1, 12, 2, 'Att uttrycka sig genom \n                        meddelanden på teckenspråk', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(20, 1, 1, 12, 2, 'Att skriva meddelanden', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(21, 1, 1, 12, 2, 'Konversation', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(22, 1, 1, 12, 2, 'Att använda \n                        kommunikationsutrustningar \n                        och kommunikationstekniker', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(23, 1, 1, NULL, 2, 'Förflyttning', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(24, 1, 1, 23, 2, 'Att ändra grundläggande \n                        kroppsställning', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(25, 1, 1, 23, 2, 'Att bibehålla en kroppsställning', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(26, 1, 1, 23, 2, 'Att förflytta sig själv', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(27, 1, 1, 23, 2, 'Att lyfta och bära föremål', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(28, 1, 1, 23, 2, 'Handens finmotoriska \n                        användning', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(29, 1, 1, 23, 2, 'Att gå', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(30, 1, 1, 23, 2, 'Att röra sig omkring på olika \n                        sätt', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(31, 1, 1, 23, 2, 'Att röra sig omkring på olika \n                        platser', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(32, 1, 1, 23, 2, 'Att använda transportmedel', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(33, 1, 1, NULL, 2, 'Personlig vård', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(34, 1, 1, 33, 2, 'Att tvätta sig', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(35, 1, 1, 33, 2, 'Kroppsvård', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(36, 1, 1, 33, 2, 'Att sköta toalettbehov', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(37, 1, 1, 33, 2, 'Att klä sig', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(38, 1, 1, 33, 2, 'Att äta', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(39, 1, 1, 33, 2, 'Att dricka ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(40, 1, 1, 33, 2, 'Att sköta sin egen hälsa', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(41, 1, 1, 33, 2, 'Att se till sin egen säkerhet', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(42, 1, 1, NULL, 2, 'Hemliv', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(43, 1, 1, 42, 2, 'Att skaffa bostad', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(44, 1, 1, 42, 2, 'Att skaffa varor och tjänster', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(45, 1, 1, 42, 2, 'Att bereda måltider', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(46, 1, 1, 42, 2, 'Hushållsarbete', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(47, 1, 1, 42, 2, 'Att tvätta och torka kläder', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(48, 1, 1, 42, 2, 'Att städa köksutrymmen och \n                        köksredskap ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(49, 1, 1, 42, 2, 'Att städa bostaden (inkl. kök)', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(50, 1, 1, 42, 2, 'Att hantera hushållsapparater', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(51, 1, 1, 42, 2, 'Att förvara förnödenheter för \n                        det dagliga livet', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(52, 1, 1, 42, 2, 'Att avlägsna avfall', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(53, 1, 1, 42, 2, 'Att tvätta och torka kläder och \n                        textilier med hushållsapparater', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(54, 1, 1, 42, 2, 'Att ta hand om hemmets \n                        föremål', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(55, 1, 1, 42, 2, 'Att bistå andra', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(56, 1, 1, NULL, 2, 'Mellanmänskliga \n                interaktioner\n                och relationer', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(57, 1, 1, 56, 2, 'Sammansatta mellanmänskliga \n                        interaktioner', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(58, 1, 1, 56, 2, 'Att ha kontakt med okända \n                        personer', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(59, 1, 1, 56, 2, 'Formella relationer', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(60, 1, 1, 56, 2, 'Informella sociala relationer ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(61, 1, 1, 56, 2, 'Familjerelationer', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(62, 1, 1, 56, 2, 'Parrelationer*', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(63, 1, 1, NULL, 2, 'Utbildning, arbete, \n                sysselsättning och \n                ekonomiskt liv*', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(64, 1, 1, 63, 2, 'Utbildning, annan specificerad \n                        och ospecificerad', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(65, 1, 1, 63, 2, 'Att skaffa, behålla och sluta ett \n                        arbete', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(66, 1, 1, 63, 2, 'Arbete och sysselsättning, \n                        annat specificerat och \n                        ospecificerat', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(67, 1, 1, 63, 2, 'Grundläggande ekonomiska \n                        transaktioner', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(68, 1, 1, 63, 2, 'Komplexa ekonomiska \n                        transaktioner', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(69, 1, 1, 63, 2, 'Ekonomisk självförsörjning', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(70, 1, 1, NULL, 2, 'Samhällsgemenskap, socialt \n                och medborgligt liv', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(71, 1, 1, 70, 2, 'Rekreation och fritid ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(72, 1, 1, 70, 2, 'Religion och andlighet ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(73, 1, 1, 70, 2, 'Politiskt liv och medborgarskap', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(74, 1, 1, NULL, 2, 'KROPPSFUNKTIONER \n                - nedsättning inom ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(75, 1, 1, 74, 2, 'Känsla av trygghet ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(76, 1, 1, NULL, 2, 'KROPPSSTRUKTURER \n                - avvikelse inom ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(77, 1, 1, NULL, 2, 'OMGIVNINGSFAKTORER', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(78, 1, 1, 77, 2, 'Personligt stöd från person \n                        som vårdar eller stödjer en \n                        närstående', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(79, 1, 1, 77, 2, 'Service, tjänster, system och \n                        policies – Upplevd kvalitet ', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(80, 1, 1, NULL, 4, 'Ej utförd insatser', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(81, 1, 1, 80, 4, 'Ej utförd insatser subcat', '#ff0000', 1, 1, NULL, 'Web', '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_types`
--

CREATE TABLE `category_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_by` bigint(20) UNSIGNED NOT NULL COMMENT 'User Table id',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_types`
--

INSERT INTO `category_types` (`id`, `top_most_parent_id`, `created_by`, `name`, `status`, `entry_mode`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Activity', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(2, 1, 1, 'Implementation Plan', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(3, 1, 1, 'User', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(4, 1, 1, 'Deviation', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(5, 1, 1, 'FollowUps', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(6, 1, 1, 'Journal', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(7, 1, 1, 'Patient', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(8, 1, 1, 'Employee', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(9, 1, 1, 'Category Update Permission', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `replied_to` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `source_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_logo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'img/logo.png',
  `company_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_contact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `follow_up_reminder` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `before_minute` int(11) DEFAULT NULL,
  `relaxation_time` int(11) DEFAULT '15',
  `extra_hour_rate` double(8,2) DEFAULT '0.00',
  `ob_hour_rate` double(8,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `user_id`, `company_name`, `company_logo`, `company_email`, `company_contact`, `company_address`, `contact_person_name`, `contact_person_email`, `contact_person_phone`, `company_website`, `follow_up_reminder`, `before_minute`, `relaxation_time`, `extra_hour_rate`, `ob_hour_rate`, `created_at`, `updated_at`) VALUES
(1, 2, 'TS Corp', 'img/logo.png', 'company@gmail.com', '1235647980', 'Address', 'T.S', NULL, NULL, NULL, 0, NULL, 15, 0.00, 0.00, '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(2, 3, 'Comp 2', 'img/logo.png', 'company2@gmail.com', '1234567410', 'Address', 'C.P', NULL, NULL, NULL, 0, NULL, 15, 0.00, 0.00, '2022-09-21 12:18:28', '2022-09-21 12:18:28');

-- --------------------------------------------------------

--
-- Table structure for table `company_types`
--

CREATE TABLE `company_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_types`
--

INSERT INTO `company_types` (`id`, `top_most_parent_id`, `created_by`, `name`, `status`, `entry_mode`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, 'Group Living', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(2, NULL, 1, 'Home Living', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(3, NULL, 1, 'Single Living', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_work_shifts`
--

CREATE TABLE `company_work_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_type` enum('normal','emergency') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `shift_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_start_time` time NOT NULL,
  `shift_end_time` time NOT NULL,
  `rest_start_time` time DEFAULT NULL,
  `rest_end_time` time DEFAULT NULL,
  `shift_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Like India, United States',
  `country_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Like IN, US',
  `dial_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Like +91, +12',
  `currency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Like Indian rupee, United States dollar',
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Like INR, USD',
  `currency_symbol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Like ₹, $',
  `is_govt_certifcate_valid` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `country_code`, `dial_code`, `currency`, `currency_code`, `currency_symbol`, `is_govt_certifcate_valid`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Afghanistan', 'AF', '+93', 'Afghan afghani', 'AFN', '؋', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(2, 'Aland Islands', 'AX', '+358', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(3, 'Albania', 'AL', '+355', 'Albanian lek', 'ALL', 'L', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(4, 'Algeria', 'DZ', '+213', 'Algerian dinar', 'DZD', 'د.ج', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(5, 'AmericanSamoa', 'AS', '+1684', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(6, 'Andorra', 'AD', '+376', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(7, 'Angola', 'AO', '+244', 'Angolan kwanza', 'AOA', 'Kz', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(8, 'Anguilla', 'AI', '+1264', 'East Caribbean dolla', 'XCD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(9, 'Antarctica', 'AQ', '+672', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(10, 'Antigua and Barbuda', 'AG', '+1268', 'East Caribbean dolla', 'XCD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(11, 'Argentina', 'AR', '+54', 'Argentine peso', 'ARS', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(12, 'Armenia', 'AM', '+374', 'Armenian dram', 'AMD', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(13, 'Aruba', 'AW', '+297', 'Aruban florin', 'AWG', 'ƒ', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(14, 'Australia', 'AU', '+61', 'Australian dollar', 'AUD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(15, 'Austria', 'AT', '+43', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(16, 'Azerbaijan', 'AZ', '+994', 'Azerbaijani manat', 'AZN', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(17, 'Bahamas', 'BS', '+1242', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(18, 'Bahrain', 'BH', '+973', 'Bahraini dinar', 'BHD', '.د.ب', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(19, 'Bangladesh', 'BD', '+880', 'Bangladeshi taka', 'BDT', '৳', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(20, 'Barbados', 'BB', '+1246', 'Barbadian dollar', 'BBD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(21, 'Belarus', 'BY', '+375', 'Belarusian ruble', 'BYR', 'Br', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(22, 'Belgium', 'BE', '+32', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(23, 'Belize', 'BZ', '+501', 'Belize dollar', 'BZD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(24, 'Benin', 'BJ', '+229', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(25, 'Bermuda', 'BM', '+1441', 'Bermudian dollar', 'BMD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(26, 'Bhutan', 'BT', '+975', 'Bhutanese ngultrum', 'BTN', 'Nu.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(27, 'Bolivia, Plurination', 'BO', '+591', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(28, 'Bosnia and Herzegovi', 'BA', '+387', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(29, 'Botswana', 'BW', '+267', 'Botswana pula', 'BWP', 'P', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(30, 'Brazil', 'BR', '+55', 'Brazilian real', 'BRL', 'R$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(31, 'British Indian Ocean', 'IO', '+246', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(32, 'Brunei Darussalam', 'BN', '+673', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(33, 'Bulgaria', 'BG', '+359', 'Bulgarian lev', 'BGN', 'лв', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(34, 'Burkina Faso', 'BF', '+226', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(35, 'Burundi', 'BI', '+257', 'Burundian franc', 'BIF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(36, 'Cambodia', 'KH', '+855', 'Cambodian riel', 'KHR', '៛', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(37, 'Cameroon', 'CM', '+237', 'Central African CFA ', 'XAF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(38, 'Canada', 'CA', '+1', 'Canadian dollar', 'CAD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(39, 'Cape Verde', 'CV', '+238', 'Cape Verdean escudo', 'CVE', 'Esc or $', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(40, 'Cayman Islands', 'KY', '+ 345', 'Cayman Islands dolla', 'KYD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(41, 'Central African Repu', 'CF', '+236', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(42, 'Chad', 'TD', '+235', 'Central African CFA ', 'XAF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(43, 'Chile', 'CL', '+56', 'Chilean peso', 'CLP', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(44, 'China', 'CN', '+86', 'Chinese yuan', 'CNY', '¥ or 元', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(45, 'Christmas Island', 'CX', '+61', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(46, 'Cocos (Keeling) Isla', 'CC', '+61', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(47, 'Colombia', 'CO', '+57', 'Colombian peso', 'COP', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(48, 'Comoros', 'KM', '+269', 'Comorian franc', 'KMF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(49, 'Congo', 'CG', '+242', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(50, 'Congo, The Democrati', 'CD', '+243', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(51, 'Cook Islands', 'CK', '+682', 'New Zealand dollar', 'NZD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(52, 'Costa Rica', 'CR', '+506', 'Costa Rican colón', 'CRC', '₡', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(53, 'Cote d\'Ivoire', 'CI', '+225', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(54, 'Croatia', 'HR', '+385', 'Croatian kuna', 'HRK', 'kn', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(55, 'Cuba', 'CU', '+53', 'Cuban convertible pe', 'CUC', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(56, 'Cyprus', 'CY', '+357', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(57, 'Czech Republic', 'CZ', '+420', 'Czech koruna', 'CZK', 'Kč', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(58, 'Denmark', 'DK', '+45', 'Danish krone', 'DKK', 'kr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(59, 'Djibouti', 'DJ', '+253', 'Djiboutian franc', 'DJF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(60, 'Dominica', 'DM', '+1767', 'East Caribbean dolla', 'XCD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(61, 'Dominican Republic', 'DO', '+1849', 'Dominican peso', 'DOP', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(62, 'Ecuador', 'EC', '+593', 'United States dollar', 'USD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(63, 'Egypt', 'EG', '+20', 'Egyptian pound', 'EGP', '£ or ج.م', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(64, 'El Salvador', 'SV', '+503', 'United States dollar', 'USD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(65, 'Equatorial Guinea', 'GQ', '+240', 'Central African CFA ', 'XAF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(66, 'Eritrea', 'ER', '+291', 'Eritrean nakfa', 'ERN', 'Nfk', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(67, 'Estonia', 'EE', '+372', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(68, 'Ethiopia', 'ET', '+251', 'Ethiopian birr', 'ETB', 'Br', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(69, 'Falkland Islands (Ma', 'FK', '+500', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(70, 'Faroe Islands', 'FO', '+298', 'Danish krone', 'DKK', 'kr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(71, 'Fiji', 'FJ', '+679', 'Fijian dollar', 'FJD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(72, 'Finland', 'FI', '+358', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(73, 'France', 'FR', '+33', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(74, 'French Guiana', 'GF', '+594', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(75, 'French Polynesia', 'PF', '+689', 'CFP franc', 'XPF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(76, 'Gabon', 'GA', '+241', 'Central African CFA ', 'XAF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(77, 'Gambia', 'GM', '+220', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(78, 'Georgia', 'GE', '+995', 'Georgian lari', 'GEL', 'ლ', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(79, 'Germany', 'DE', '+49', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(80, 'Ghana', 'GH', '+233', 'Ghana cedi', 'GHS', '₵', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(81, 'Gibraltar', 'GI', '+350', 'Gibraltar pound', 'GIP', '£', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(82, 'Greece', 'GR', '+30', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(83, 'Greenland', 'GL', '+299', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(84, 'Grenada', 'GD', '+1473', 'East Caribbean dolla', 'XCD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(85, 'Guadeloupe', 'GP', '+590', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(86, 'Guam', 'GU', '+1671', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(87, 'Guatemala', 'GT', '+502', 'Guatemalan quetzal', 'GTQ', 'Q', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(88, 'Guernsey', 'GG', '+44', 'British pound', 'GBP', '£', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(89, 'Guinea', 'GN', '+224', 'Guinean franc', 'GNF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(90, 'Guinea-Bissau', 'GW', '+245', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(91, 'Guyana', 'GY', '+595', 'Guyanese dollar', 'GYD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(92, 'Haiti', 'HT', '+509', 'Haitian gourde', 'HTG', 'G', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(93, 'Holy See (Vatican Ci', 'VA', '+379', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(94, 'Honduras', 'HN', '+504', 'Honduran lempira', 'HNL', 'L', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(95, 'Hong Kong', 'HK', '+852', 'Hong Kong dollar', 'HKD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(96, 'Hungary', 'HU', '+36', 'Hungarian forint', 'HUF', 'Ft', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(97, 'Iceland', 'IS', '+354', 'Icelandic króna', 'ISK', 'kr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(98, 'India', 'IN', '+91', 'Indian rupee', 'INR', '₹', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(99, 'Indonesia', 'ID', '+62', 'Indonesian rupiah', 'IDR', 'Rp', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(100, 'Iran, Islamic Republ', 'IR', '+98', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(101, 'Iraq', 'IQ', '+964', 'Iraqi dinar', 'IQD', 'ع.د', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(102, 'Ireland', 'IE', '+353', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(103, 'Isle of Man', 'IM', '+44', 'British pound', 'GBP', '£', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(104, 'Israel', 'IL', '+972', 'Israeli new shekel', 'ILS', '₪', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(105, 'Italy', 'IT', '+39', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(106, 'Jamaica', 'JM', '+1876', 'Jamaican dollar', 'JMD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(107, 'Japan', 'JP', '+81', 'Japanese yen', 'JPY', '¥', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(108, 'Jersey', 'JE', '+44', 'British pound', 'GBP', '£', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(109, 'Jordan', 'JO', '+962', 'Jordanian dinar', 'JOD', 'د.ا', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(110, 'Kazakhstan', 'KZ', '+7 7', 'Kazakhstani tenge', 'KZT', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(111, 'Kenya', 'KE', '+254', 'Kenyan shilling', 'KES', 'Sh', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(112, 'Kiribati', 'KI', '+686', 'Australian dollar', 'AUD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(113, 'Korea, Democratic Pe', 'KP', '+850', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(114, 'Korea, Republic of S', 'KR', '+82', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(115, 'Kuwait', 'KW', '+965', 'Kuwaiti dinar', 'KWD', 'د.ك', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(116, 'Kyrgyzstan', 'KG', '+996', 'Kyrgyzstani som', 'KGS', 'лв', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(117, 'Laos', 'LA', '+856', 'Lao kip', 'LAK', '₭', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(118, 'Latvia', 'LV', '+371', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(119, 'Lebanon', 'LB', '+961', 'Lebanese pound', 'LBP', 'ل.ل', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(120, 'Lesotho', 'LS', '+266', 'Lesotho loti', 'LSL', 'L', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(121, 'Liberia', 'LR', '+231', 'Liberian dollar', 'LRD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(122, 'Libyan Arab Jamahiri', 'LY', '+218', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(123, 'Liechtenstein', 'LI', '+423', 'Swiss franc', 'CHF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(124, 'Lithuania', 'LT', '+370', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(125, 'Luxembourg', 'LU', '+352', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(126, 'Macao', 'MO', '+853', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(127, 'Macedonia', 'MK', '+389', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(128, 'Madagascar', 'MG', '+261', 'Malagasy ariary', 'MGA', 'Ar', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(129, 'Malawi', 'MW', '+265', 'Malawian kwacha', 'MWK', 'MK', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(130, 'Malaysia', 'MY', '+60', 'Malaysian ringgit', 'MYR', 'RM', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(131, 'Maldives', 'MV', '+960', 'Maldivian rufiyaa', 'MVR', '.ރ', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(132, 'Mali', 'ML', '+223', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(133, 'Malta', 'MT', '+356', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(134, 'Marshall Islands', 'MH', '+692', 'United States dollar', 'USD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(135, 'Martinique', 'MQ', '+596', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(136, 'Mauritania', 'MR', '+222', 'Mauritanian ouguiya', 'MRO', 'UM', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(137, 'Mauritius', 'MU', '+230', 'Mauritian rupee', 'MUR', '₨', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(138, 'Mayotte', 'YT', '+262', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(139, 'Mexico', 'MX', '+52', 'Mexican peso', 'MXN', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(140, 'Micronesia, Federate', 'FM', '+691', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(141, 'Moldova', 'MD', '+373', 'Moldovan leu', 'MDL', 'L', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(142, 'Monaco', 'MC', '+377', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(143, 'Mongolia', 'MN', '+976', 'Mongolian tögrög', 'MNT', '₮', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(144, 'Montenegro', 'ME', '+382', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(145, 'Montserrat', 'MS', '+1664', 'East Caribbean dolla', 'XCD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(146, 'Morocco', 'MA', '+212', 'Moroccan dirham', 'MAD', 'د.م.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(147, 'Mozambique', 'MZ', '+258', 'Mozambican metical', 'MZN', 'MT', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(148, 'Myanmar', 'MM', '+95', 'Burmese kyat', 'MMK', 'Ks', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(149, 'Namibia', 'NA', '+264', 'Namibian dollar', 'NAD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(150, 'Nauru', 'NR', '+674', 'Australian dollar', 'AUD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(151, 'Nepal', 'NP', '+977', 'Nepalese rupee', 'NPR', '₨', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(152, 'Netherlands', 'NL', '+31', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(153, 'Netherlands Antilles', 'AN', '+599', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(154, 'New Caledonia', 'NC', '+687', 'CFP franc', 'XPF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(155, 'New Zealand', 'NZ', '+64', 'New Zealand dollar', 'NZD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(156, 'Nicaragua', 'NI', '+505', 'Nicaraguan córdoba', 'NIO', 'C$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(157, 'Niger', 'NE', '+227', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(158, 'Nigeria', 'NG', '+234', 'Nigerian naira', 'NGN', '₦', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(159, 'Niue', 'NU', '+683', 'New Zealand dollar', 'NZD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(160, 'Norfolk Island', 'NF', '+672', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(161, 'Northern Mariana Isl', 'MP', '+1670', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(162, 'Norway', 'NO', '+47', 'Norwegian krone', 'NOK', 'kr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(163, 'Oman', 'OM', '+968', 'Omani rial', 'OMR', 'ر.ع.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(164, 'Pakistan', 'PK', '+92', 'Pakistani rupee', 'PKR', '₨', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(165, 'Palau', 'PW', '+680', 'Palauan dollar', '', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(166, 'Palestinian Territor', 'PS', '+970', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(167, 'Panama', 'PA', '+507', 'Panamanian balboa', 'PAB', 'B/.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(168, 'Papua New Guinea', 'PG', '+675', 'Papua New Guinean ki', 'PGK', 'K', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(169, 'Paraguay', 'PY', '+595', 'Paraguayan guaraní', 'PYG', '₲', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(170, 'Peru', 'PE', '+51', 'Peruvian nuevo sol', 'PEN', 'S/.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(171, 'Philippines', 'PH', '+63', 'Philippine peso', 'PHP', '₱', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(172, 'Pitcairn', 'PN', '+872', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(173, 'Poland', 'PL', '+48', 'Polish z?oty', 'PLN', 'zł', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(174, 'Portugal', 'PT', '+351', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(175, 'Puerto Rico', 'PR', '+1939', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(176, 'Qatar', 'QA', '+974', 'Qatari riyal', 'QAR', 'ر.ق', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(177, 'Romania', 'RO', '+40', 'Romanian leu', 'RON', 'lei', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(178, 'Russia', 'RU', '+7', 'Russian ruble', 'RUB', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(179, 'Rwanda', 'RW', '+250', 'Rwandan franc', 'RWF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(180, 'Reunion', 'RE', '+262', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(181, 'Saint Barthelemy', 'BL', '+590', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(182, 'Saint Helena, Ascens', 'SH', '+290', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(183, 'Saint Kitts and Nevi', 'KN', '+1869', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(184, 'Saint Lucia', 'LC', '+1758', 'East Caribbean dolla', 'XCD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(185, 'Saint Martin', 'MF', '+590', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(186, 'Saint Pierre and Miq', 'PM', '+508', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(187, 'Saint Vincent and th', 'VC', '+1784', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(188, 'Samoa', 'WS', '+685', 'Samoan t?l?', 'WST', 'T', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(189, 'San Marino', 'SM', '+378', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(190, 'Sao Tome and Princip', 'ST', '+239', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(191, 'Saudi Arabia', 'SA', '+966', 'Saudi riyal', 'SAR', 'ر.س', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(192, 'Senegal', 'SN', '+221', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(193, 'Serbia', 'RS', '+381', 'Serbian dinar', 'RSD', 'дин. or din.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(194, 'Seychelles', 'SC', '+248', 'Seychellois rupee', 'SCR', '₨', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(195, 'Sierra Leone', 'SL', '+232', 'Sierra Leonean leone', 'SLL', 'Le', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(196, 'Singapore', 'SG', '+65', 'Brunei dollar', 'BND', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(197, 'Slovakia', 'SK', '+421', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(198, 'Slovenia', 'SI', '+386', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(199, 'Solomon Islands', 'SB', '+677', 'Solomon Islands doll', 'SBD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(200, 'Somalia', 'SO', '+252', 'Somali shilling', 'SOS', 'Sh', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(201, 'South Africa', 'ZA', '+27', 'South African rand', 'ZAR', 'R', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(202, 'South Georgia and th', 'GS', '+500', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(203, 'Spain', 'ES', '+34', 'Euro', 'EUR', '€', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(204, 'Sri Lanka', 'LK', '+94', 'Sri Lankan rupee', 'LKR', 'Rs or රු', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(205, 'Sudan', 'SD', '+249', 'Sudanese pound', 'SDG', 'ج.س.', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(206, 'Suriname', 'SR', '+597', 'Surinamese dollar', 'SRD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(207, 'Svalbard and Jan May', 'SJ', '+47', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(208, 'Swaziland', 'SZ', '+268', 'Swazi lilangeni', 'SZL', 'L', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(209, 'Sweden', 'SE', '+46', 'Swedish krona', 'SEK', 'kr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(210, 'Switzerland', 'CH', '+41', 'Swiss franc', 'CHF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(211, 'Syrian Arab Republic', 'SY', '+963', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(212, 'Taiwan', 'TW', '+886', 'New Taiwan dollar', 'TWD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(213, 'Tajikistan', 'TJ', '+992', 'Tajikistani somoni', 'TJS', 'ЅМ', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(214, 'Tanzania, United Rep', 'TZ', '+255', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(215, 'Thailand', 'TH', '+66', 'Thai baht', 'THB', '฿', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(216, 'Timor-Leste', 'TL', '+670', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(217, 'Togo', 'TG', '+228', 'West African CFA fra', 'XOF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(218, 'Tokelau', 'TK', '+690', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(219, 'Tonga', 'TO', '+676', 'Tongan pa?anga', 'TOP', 'T$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(220, 'Trinidad and Tobago', 'TT', '+1868', 'Trinidad and Tobago ', 'TTD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(221, 'Tunisia', 'TN', '+216', 'Tunisian dinar', 'TND', 'د.ت', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(222, 'Turkey', 'TR', '+90', 'Turkish lira', 'TRY', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(223, 'Turkmenistan', 'TM', '+993', 'Turkmenistan manat', 'TMT', 'm', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(224, 'Turks and Caicos Isl', 'TC', '+1649', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(225, 'Tuvalu', 'TV', '+688', 'Australian dollar', 'AUD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(226, 'Uganda', 'UG', '+256', 'Ugandan shilling', 'UGX', 'Sh', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(227, 'Ukraine', 'UA', '+380', 'Ukrainian hryvnia', 'UAH', '₴', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(228, 'United Arab Emirates', 'AE', '+971', 'United Arab Emirates', 'AED', 'د.إ', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(229, 'United Kingdom', 'GB', '+44', 'British pound', 'GBP', '£', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(230, 'United States', 'US', '+1', 'United States dollar', 'USD', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(231, 'Uruguay', 'UY', '+598', 'Uruguayan peso', 'UYU', '$', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(232, 'Uzbekistan', 'UZ', '+998', 'Uzbekistani som', 'UZS', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(233, 'Vanuatu', 'VU', '+678', 'Vanuatu vatu', 'VUV', 'Vt', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(234, 'Venezuela, Bolivaria', 'VE', '+58', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(235, 'Vietnam', 'VN', '+84', 'Vietnamese ??ng', 'VND', '₫', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(236, 'Virgin Islands, Brit', 'VG', '+1284', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(237, 'Virgin Islands, U.S.', 'VI', '+1340', '', '', '', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(238, 'Wallis and Futuna', 'WF', '+681', 'CFP franc', 'XPF', 'Fr', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(239, 'Yemen', 'YE', '+967', 'Yemeni rial', 'YER', '﷼', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(240, 'Zambia', 'ZM', '+260', 'Zambian kwacha', 'ZMW', 'ZK', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(241, 'Zimbabwe', 'ZW', '+263', 'Botswana pula', 'BWP', 'P', 0, '2020-04-29 16:12:15', '2020-04-29 16:12:15', NULL),
(242, 'UAE', '', '', '', '', '', 0, '2021-04-28 00:55:54', '2021-04-28 00:55:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deviations`
--

CREATE TABLE `deviations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_note` text COLLATE utf8mb4_unicode_ci,
  `immediate_action` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `probable_cause_of_the_incident` text COLLATE utf8mb4_unicode_ci,
  `suggestion_to_prevent_event_again` text COLLATE utf8mb4_unicode_ci,
  `critical_range` int(11) NOT NULL COMMENT '1 to 5',
  `related_factor` text COLLATE utf8mb4_unicode_ci,
  `further_investigation` text COLLATE utf8mb4_unicode_ci,
  `follow_up` text COLLATE utf8mb4_unicode_ci,
  `is_secret` tinyint(1) DEFAULT '0',
  `is_signed` tinyint(1) DEFAULT '0',
  `sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  `completed_by` bigint(20) DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `edited_by` bigint(20) DEFAULT NULL,
  `edited_date` date DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_login_history`
--

CREATE TABLE `device_login_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_token` longtext COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_via` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0:Web,1:android,2:ios',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `device_login_history`
--

INSERT INTO `device_login_history` (`id`, `user_id`, `device_id`, `device_model`, `device_token`, `user_token`, `ip_address`, `login_via`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjM3N2Y0MGQ0YTIwMjk3NDQ2YWZhNmIzMGY3ZWMyNGQ0NmZiMWE0OTYxNTA3MjMzYWFkMDE0MThlOGQ1NDY4Y2I1ZTAzNDVhNTE4Y2U5OGEiLCJpYXQiOjE2NjM3NTUyNDMuMzUwMjgxMDAwMTM3MzI5MTAxNTYyNSwibmJmIjoxNjYzNzU1MjQzLjM1MDI4Njk2MDYwMTgwNjY0MDYyNSwiZXhwIjoxNjYzODQxNjQzLjM0MTIxMTA4MDU1MTE0NzQ2MDkzNzUsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.iFBM0xUl5ttoj4xN0SGcLfGtwxmUXJqZVg8i_elh6bJauFijeGUf008vGS_4wch7-ZKhFU8wTMBAI0KtkHtQ-buzgLbZmV75zK5EiQdwXJQgnX9UBJjlmzS5aPex_mgZeVEwTXBRr5UurOxZIq9VhABspNBNEu5OQxspV9s-Eg89OIUAZ85UMMxEtmXzFv1HRoZGQ3PhDMBleoJQ_Fgyrq5QFIcSGdIpBVSO_ycpDqRq3NQz8EOy0nDUz4jYq3JsP87afUMyzrd9ryWjczx_80zioZKDjSOAUcsqZntPCMMRLNMrjNE0yX3XVT6XK_qfx0WIFPWZy1m3pHjCrgKcjeTzUcdflNMmX0HurEDnwTUswYRmG_OnRQPaq2Y3QmmhyJQeBVJDkP6pqGLG5Yd9mcInNskeS_PaARAp0sI7st4y1tQJEswtBfUc5blnoPH7iPojWRUVh7h24N2zxnTnxvOVyrVt5Lni8SIUt21yTUfMn7TBu7dry4iUa8ot_YXa6WbWsNnSKG9EhY6ouc2DZJKwFKi4qcQ5oWvmnwDyj4S6urVfHcX8ADFv20ck9f4s8eK-UuHi9kAvj99JlJ3MrHwXubEuEMrz-Biuf66mLIXi2akdQ1w8y7ByUqmF5r-KlQroEOzCyexdJoLilrgndeis88GP0neQUE17IjMvI_c', '122.168.49.152', '0', '2022-09-21 12:14:03', '2022-09-21 12:14:03'),
(2, 1, NULL, NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjQ4ZDhhMWYxMWU5ZTU5NTgwNDU5ZGY2OWI2OTcwYzM0MTc2YmZkZTllYzU4YThlOGI0NWU2MzUwMmExNTgzMzg0ZGZiY2U3OWZiNDk3MWIiLCJpYXQiOjE2NjM3NTUzMTEuMzQzNTYzMDc5ODMzOTg0Mzc1LCJuYmYiOjE2NjM3NTUzMTEuMzQzNTY5OTkzOTcyNzc4MzIwMzEyNSwiZXhwIjoxNjYzODQxNzExLjM0MDUzNDkyNTQ2MDgxNTQyOTY4NzUsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.QJD509umYCFkEP9OFiWkiX_x6wyrilZD1Sura_eYEzJ-wjdjGcDLz_ff6M_kS2PKlJ2cLAMmE5FhlsCTEOqAO7iWmgC9aZtDj3G84tzwQ0mSnw417M539PIy2fyKYep1cz6n3nn_oCpBXo8mWuzkpTFoTZt-0dAt7TfuOZaMiXdaqp5ZvYv0UKBgjA9m6fMlzah7y7Qc8OAGsTIwdgwcm5xs8wcaom7F_ot2eq2Eh7No8RbxbcgO2kTVF95a3JnJ96cSO_H65dddWPT2_CSvgNKp1TNAxqbjfg6uPwpiZASLTvtDDW5lwjuSodJKxxq4DSaruS_e8AhoDzNpq3vhboP7svsyqMsuf9vmL-IP5bLOalQAphyo7bjhW9Ic21j0s5mib8wmNC7jJzVGchBbNo7XxjsC-0jrKh_5t0Jn7w-iparuyZynORXfQ8_L0FhXihfdeeRF5Y3Rp4ZdTDXUYODM1mxQgxgVWRJuXXTKo_cCqHEu-4Ild9bpztUdKX6DR7j7cklt48ONDTGv6j2f2Fvn5uz7ktymKQsne05hT8b9uiGaGTf9uBAOvFrWJGQwvGcwGBVDbN8XhLWZ3nc5OibSoIBN1mtBXumqNLIqBFq3h7K-U_Vrvo7XU6zk2swk4cOoSKBX1xExBLyKCR2kAYJUFI_7ccvgg0e9nXSTFhI', '122.168.49.152', '0', '2022-09-21 12:15:11', '2022-09-21 12:15:11'),
(3, 2, NULL, NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiODdjNTVlMTEzZWQ0Y2I4MDQzODE0NDdmZTdlN2M2NTE2YTAyZTRhZDZmZDY3MjIwYjRlODZmMDBiN2U5ZTY2ZGE1YjM3ZDNmMGRjYTFkNDciLCJpYXQiOjE2NjM3NTU1ODMuNzY2MTM2ODg0Njg5MzMxMDU0Njg3NSwibmJmIjoxNjYzNzU1NTgzLjc2NjEzOTk4NDEzMDg1OTM3NSwiZXhwIjoxNjYzODQxOTgzLjc2NDgzODkzMzk0NDcwMjE0ODQzNzUsInN1YiI6IjIiLCJzY29wZXMiOltdfQ.Uj689hAqJTvFESkRF7TDCWhCudU5BXfps-wc5xobEE_THupea93yjRXSQQscqOeAX4rbNz2Du6mXscAwlJL9uOip-l9tBFhlDXEoCSAE11g820UeGS7szXPv3SNF8GFGLvBUaulU4v3Rhr--dU7rkD6uJBKZdYe3uBierUZ8-W7E4RqRIurLsWrZSdp9dLSZuSC5bcEeDOivD723gAWaGuS3Wu4KmNOjVpW-vr6gAWKB4IiTSB9MdU6RSTMYhWUXR8qMfXSlfwhsqadL6MB-B0l2tLQ7E8WNxZ2XmrBPJGTruXrEnU801UYilp95mIRAkx9ZP4_MN6LHBIBQCLk7wlrs4jh5uvRb5QZ9_cW-luaspUJQRT5UR4zOOuvLx89A4P6_bDoTahVf-RyHv1cQCEaQGRAUjaUzG-X9P3_clsdrZNqRJ_HzA-ek0O9LR7BjyN4tOu6APdMGtNZs4x8nAnYYXNTr_vb2W5VQUhaxmmw7TamtOTrZMxWvkXyHSDur4qoU5cUsgHiaMRS4lEL-JuRaX9wvXdQTKmwGf_Nn4m6_u-i1yZli82-zyc8jMqb1AVOZFQSqtOP75NPPegBgSJKBL34XATR_b_p8Jd8i8AuSPro0FHeoM6s1Gv0S_X7fuAtBqyyzCVjt04wkV0UwvKJT4iiHbeZULkcVksHY0sc', '122.168.49.152', '0', '2022-09-21 12:19:43', '2022-09-21 12:19:43'),
(4, 2, NULL, NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjc4NmUxZjNhZDVhNjljMmQ1MjJiN2Q3MmEyNWIyZGY5ZmE0ZjRiM2YyZjMyYzM3NjJmN2E3MjE1ODlhNTg2YzJiZWY5ZmMxMGM2YmJjNzEiLCJpYXQiOjE2NjM3NTU2MDkuOTYwMDU1MTEyODM4NzQ1MTE3MTg3NSwibmJmIjoxNjYzNzU1NjA5Ljk2MDA1ODkyNzUzNjAxMDc0MjE4NzUsImV4cCI6MTY2Mzg0MjAwOS45NTc4NjE5MDAzMjk1ODk4NDM3NSwic3ViIjoiMiIsInNjb3BlcyI6W119.GnL87rYnRWT4NKj6MVkO1jYCtrJv1Ltyi_qeJQKSWPDpW-ItszTQ50JyPVaT6FMkJkNco27h59PF73cVafLZFd9_kYYUUFqqBGTfNqt9vq6E87GND2t_mBvQIm-vNsD4htSR-K-p-TYfKQKMpFz_qYlQ8Vuo2W8IPN3efp-8i13SxZqpR3hLgVsY7b3S4BFu39PdCfk58y6JtmmRCVjf5ICXQf-vTCWr2o4KiVPfBi9IcwyNyN3d4Rp6oYV0-vrCsqT-2RuM5yzeymPNIlmHx1MkE3JsS2Xe7tXFDCRfvvK-gs4kJAc5xTRy1wtsUOb4PvpH8tp5urkQgaZLfmBN1D3QVocEZVT4T5gQb8tKjkos4liHrXyF_TM987v2EZFyBPbSmXq4I6n0ry0Do7dIKTAYW_PHIB6Z88tmjfYPRJ3xCJK7dE_WxVrScNaJMEUwBPHJuWK15MFcoXiwjMfRLBJ51sjycOsMDs3fZOpMMrJsXCY5AE2KinF-zs3lJu8EHqfy0RZWOosjF3VkKiOgcKSeke2ORHGraJ1f8QJYY6EtDZ_X1UgI5q-KnSo08-4qb7TeQLhVbFfxiLJdqu7lBuTXhuHYd6-RS6gpAoG-bShZAAZTI66q2i3XB9pEY1xd-P-yp2aAvB1w5sEokUyCRzQpGjMHXckiGBrd9LY2Ot8', '122.168.49.152', '0', '2022-09-21 12:20:09', '2022-09-21 12:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mail_sms_for` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_subject` longtext COLLATE utf8mb4_unicode_ci,
  `mail_body` longtext COLLATE utf8mb4_unicode_ci,
  `sms_body` longtext COLLATE utf8mb4_unicode_ci,
  `notify_body` longtext COLLATE utf8mb4_unicode_ci,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'success,info,warning,danger',
  `save_to_database` tinyint(1) DEFAULT '0',
  `custom_attributes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `mail_sms_for`, `mail_subject`, `mail_body`, `sms_body`, `notify_body`, `module`, `type`, `event`, `screen`, `status_code`, `save_to_database`, `custom_attributes`, `created_at`, `updated_at`) VALUES
(1, 'forgot-password', 'Forgot Password', 'This email is to confirm a recent password reset request for your account. To confirm this request and reset your password, {{message}}:', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '{{name}}, {{email}},{{token}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}{{message}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(2, 'welcome-mail', 'Welcome to Aceuss System', 'Dear {{name}}, welocome to {{company_email}} Please change your password into website/App for your future safety.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '{{name}}, {{email}},{{contact_number}},{{city}},{{address}},{{zipcode}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(3, 'activity', 'New Activity Assigned', NULL, 'Dear {{name}}, New Activity {{title}} is assigne to you  for patient id {{patient_id}} Activity start at {{start_date}}\r\n        {{start_time}}.', 'Dear {{name}}, New Activity {{title}} is assigned to yout  for patient id {{patient_id}} Activity start at {{start_date}} {{start_time}}.', 'activity', 'activity', 'assigned', 'detail', 'info', 1, '{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(4, 'task', 'New Task Assigned', NULL, 'Dear {{name}}, New task {{title}} is assigned to you. start at {{start_date}} {{start_time}}.', 'Dear {{name}}, New task {{title}} is assigned to you. start at {{start_date}} {{start_time}}.', 'task', 'task', 'assigned', 'detail', 'info', 1, '{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(5, 'activity', 'Activity Notification', NULL, '', 'Activity Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'activity', 'activity', 'created', 'detail', 'info', 1, '', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(6, 'request-approval', 'New Approval Request', NULL, '', 'Request for approval Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'request-approval', 'request-approval', 'request-approval', 'detail', 'info', 1, '', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(7, 'activity-assignment', 'New Activity Assigned', NULL, '', 'Dear {{name}}, New Activity {{activity_title}} starts at {{start_date}}   {{start_time}}  is assigned to you  by {{assigned_by}}', 'activity', 'activity', 'assigned', 'detail', 'info', 1, '{{name}}, {{activity_title}},{{start_date}},{{start_time}},{{assigned_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(8, 'activity-done', 'Activity Marked As Done', NULL, '', 'Dear {{name}}, Activity {{activity_title}} is Marked as done  starts at {{start_date}} {{start_time}} by {{action_by}}', 'activity', 'activity', 'activity-marked-done', 'detail', 'info', 1, '{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(9, 'activity-not-done', 'Activity Marked As Not Done', NULL, '', 'Dear {{name}}, Activity {{activity_title}} is Marked as not done  starts at {{start_date}} {{start_time}} by {{action_by}}', 'activity', 'activity', 'activity-marked-not-done', 'detail', 'danger', 1, '{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(10, 'activity-not-applicable', 'Activity Marked As Not Applicable', NULL, '', 'Dear {{name}}, Activity {{activity_title}} is Marked as Not Applicable  starts at {{start_date}} {{start_time}} by {{action_by}}', 'activity', 'activity', 'activity-marked-not-applicable', 'detail', 'warning', 1, '{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(11, 'activity-comment', 'Activity comment posted', NULL, '', 'Dear {{name}}, comment is posted on Activity {{activity_title}} by {{comment_by}}', 'activity', 'comment', 'created', 'detail', 'info', 1, '{{name}},{{activity_title}}},{{comment_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(12, 'journal', 'Journal Created', NULL, '', 'Dear {{name}}, New Journal is created by {{created_by}}', 'journal', 'journal', 'created', 'detail', 'info', 1, '{{name}},{{created_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(13, 'deviation', 'Deviation Created', NULL, '', 'Dear {{name}}, New Deviation is created by {{created_by}}', 'deviation', 'deviation', 'created', 'detail', 'info', 1, '{{name}}, {{created_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(14, 'task-created-assigned', 'Task Created And Assigned', NULL, '', 'Dear {{name}}, New Tasks {{task_title}} is  created and assigned successfully.', 'task', 'task', 'created-assigned', 'detail', 'info', 1, '{{name}},{{task_title}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(15, 'task-assignment', 'New Task Assigned', NULL, '', 'Dear {{name}}, New Tasks {{task_title}} is  assigned to by {{assigned_by}}.', 'task', 'task', 'assigned', 'detail', 'info', 1, '{{name}},{{task_title}},{{assigned_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(16, 'task-done', 'Task Marked As Done', NULL, '', 'Dear {{name}}, task {{task_title}} is masrked as done  by {{action_by}}.', 'task', 'task', 'marked-done', 'detail', 'success', 1, '{{name}}, {{task_title}},{{action_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(17, 'task-not-done', 'Task Marked As Not Done', NULL, '', 'Dear {{name}}, task {{task_title}} is masrked as not done  by {{action_by}}.', 'task', 'task', 'marked-not-done', 'detail', 'danger', 1, '{{name}}, {{task_title}},{{action_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(18, 'schedule-assignment', 'New scheduled Assigned', NULL, '', 'Dear {{name}}, New Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is assigned to you  by {{assigned_by}}', 'schedule', 'leave', 'schedule-assigned', 'detail', 'info', 1, '{{name}}, {{schedule_title}},{{date}},{{start_time}},{{assigned_by}},{{end_time}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(19, 'schedule-request', 'New schedule Request', NULL, '', 'Dear {{name}}, New Schedule for dates {{dates}}  requested to you  by {{requested_by}}', 'schedule', 'leave', 'requested', 'list', 'info', 1, '{{name}},{{dates}},{{requested_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(20, 'schedule-slot-selected', 'Schedule  Slot Selected', NULL, '', 'Dear {{name}}, Schedule slot for {{selected_dates}} is selected by {{selected_by}} and  dates {{vacant_dates}}  are still available to select.', 'schedule', 'leave', 'scheduleSlotSelected', 'list', 'info', 1, '{{name}},{{vacant_dates}},{{selected_by}},{{selected_dates}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(21, 'leave-applied', 'New Leave Request', NULL, '', 'Leave  on {{date}} requested  by {{requested_by}} beacause of {{reason}}', 'schedule', 'leave', 'leave-applied', 'detail', 'info', 1, '{{date}},{{requested_by}},{{reason}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(22, 'leave-approved', 'Leave Approved', NULL, '', 'Dear {{name}}, your leave request for {{date}} is approved   by {{approved_by}}', 'schedule', 'leave', 'leave-approved', 'detail', 'info', 1, '{{name}},{{date}},{{approved_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(23, 'leave-approved-multiple', 'Leave Approved', NULL, '', 'Dear {{name}}, your leave request for {{dates}} is approved   by {{approved_by}}', 'schedule', 'leave', 'leave-approved', 'list', 'info', 1, '{{name}},{{dates}},{{approved_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(24, 'leave-applied-approved', 'Leave Applied And Approved', NULL, '', 'Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}', 'schedule', 'leave', 'leave-applied-approved', 'list', 'info', 1, '{{name}},{{dates}},{{approved_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(25, 'schedule-approved', 'Scheduled Approved', NULL, '', 'Dear {{name}}, Your Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is approved by {{approved_by}}', 'schedule', 'schedule', 'schedule-approved', 'detail', 'info', 1, '{{name}}, {{schedule_title}},{{date}},{{start_time}},{{approved_by}},{{end_time}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(26, 'schedule-verified', 'Scheduled Verified', NULL, '', 'Dear {{name}}, Your Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is verified by {{verified_by}}', 'schedule', 'schedule', 'schedule-verified', 'detail', 'info', 1, '{{name}}, {{schedule_title}},{{date}},{{start_time}},{{verified_by}},{{end_time}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(27, 'birthday-wish', 'Birthday Wishes', NULL, '', 'Dear {{name}}, Happy And Blessed Birthday. Wishing You A Great Year Ahead.', 'user', 'user', 'birthday', 'detail', 'info', 0, '{{name}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(28, 'module-request', 'Module Request', NULL, '', 'Dear {{name}}, there is a new request for modules {{modules}} by {{requested_by}} on {{request_date}} because of {{request_comment}}', 'Module', 'module-request', 'module-request', 'detail', 'info', 1, '{{name}},{{modules}},{{requested_by}},{{request_date}},{{request_comment}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(29, 'module-request-approved', 'Module Request Approved', NULL, '', 'Dear {{name}}, your request for modules {{modules}} is approved   by {{approved_by}} on {{reply_date}} because of {{reply_comment}}', 'Module', 'module-request', 'module-request-approved', 'detail', 'info', 1, '{{name}},{{modules}},{{approved_by}},{{reply_date}},{{reply_comment}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(30, 'module-request-rejected', 'Module Request Rejected', NULL, '', 'Dear {{name}}, your request for modules {{modules}} is rejected   by {{rejected_by}} on {{reply_date}} because of {{reply_comment}}', 'Module', 'module-request', 'module-request-rejected', 'detail', 'info', 1, '{{name}},{{modules}},{{rejected_by}},{{reply_date}}{{reply_comment}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(31, 'verify-schedule-reminder', 'Verify Schedule Reminder', NULL, '', 'Dear {{name}}, your have some unverified schedules this month, please verify...or will be auto verified by system on end of this month.', 'schedule', 'schedule', 'verify-schedule-reminder', 'list', 'warning', 1, '{{name}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(32, 'punch-in-reminder', 'Punch In Reminder', NULL, '', 'Dear {{name}}, your have an assigned schedule which will start at {{shift_start_time}}, punch in on time,', 'schedule', 'schedule', 'punch-in-reminder', 'detail', 'info', 1, '{{name}},{{shift_start_time}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(33, 'punch-out-reminder', 'Punch Out Reminder', NULL, '', 'Dear {{name}}, your assigned schedule ends at {{shift_end_time}}, punch out on time,', 'schedule', 'schedule', 'punch-out-reminder', 'detail', 'info', 1, '{{name}},{{shift_end_time}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(34, 'employee-created', 'Employee has Created', NULL, '', 'Dear {{name}}, new employee {{user_name}} has been added.', 'user', 'employee', 'created', 'detail', 'info', 1, '{{name}},{{user_name}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(35, 'patient-created', 'Patient has Created', NULL, '', 'Dear {{name}}, new patient {{user_name}} has been added.', 'user', 'patient', 'created', 'list', 'info', 1, '{{name}},{{user_name}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(36, 'branch-created', 'Branch has Created', NULL, '', 'Dear {{name}}, new branch {{user_name}} has been added.', 'user', 'branch', 'created', 'list', 'info', 1, '{{name}},{{user_name}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(37, 'followup-created', 'Followup Created', NULL, '', 'Dear {{name}}, new followup {{title}} starts at {{start_date}} {{start_time}} ends at {{end_date}} {{end_time}} created by {{created_by}}', 'plan', 'followup', 'created', 'list', 'info', 1, '{{name}},{{title}},{{start_date}}, {{start_time}},{{end_date}}, {{end_time}},{{created_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(38, 'ip-created', 'IP Created', NULL, '', 'Dear {{name}}, new IP {{title}} sis created by {{created_by}}', 'plan', 'ip', 'created', 'list', 'info', 1, '{{name}},{{title}},{{created_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(39, 'ip-assigned', 'IP Assigned', NULL, '', 'Dear {{name}}, new IP is assigned to you by {{assigned_by}}', 'plan', 'ip', 'assigned', 'list', 'info', 1, '{{name}},{{assigned_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(40, 'trashed-activity-created', 'Trashed Activity has Created', NULL, '', 'Dear {{name}}, activity {{title}} is deleted by {{deleted_by}}', 'activity', 'trashed-activity', 'created', 'list', 'info', 1, '{{name}},{{title}},{{deleted_by}}', '2022-09-21 15:42:52', '2022-09-21 15:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_assigned_working_hours`
--

CREATE TABLE `employee_assigned_working_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_working_hour_per_week` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `working_percent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '100',
  `actual_working_hour_per_week` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_types`
--

CREATE TABLE `employee_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_types`
--

INSERT INTO `employee_types` (`id`, `type`, `designation`, `description`, `created_at`, `updated_at`) VALUES
(1, 'patient', 'Minor Child', NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(2, 'patient', 'Student', NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(3, 'patient', 'Working', NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(4, 'patient', 'Old age', NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(5, 'patient', 'Not Working', NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `folder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `source_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_extension` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_compulsory` tinyint(1) NOT NULL DEFAULT '0',
  `approval_required` tinyint(1) NOT NULL DEFAULT '0',
  `approved_date` date DEFAULT NULL,
  `visible_to_users` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_access_logs`
--

CREATE TABLE `file_access_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `admin_file_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Admin File id',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible_to_users` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followup_completes`
--

CREATE TABLE `followup_completes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `follow_up_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer` longtext COLLATE utf8mb4_unicode_ci,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 'BcCommon', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(2, 'BcValidation', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(3, 'Schedule', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(4, 'ScheduleTemplate', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(5, 'Leave', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(6, 'OVHour', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(7, 'ModuleRequest', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(8, 'LoginValidation', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(9, 'PasswordReset', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(10, 'LicenceKey', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(11, 'ChangePassword', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(12, 'UserValidation', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(13, 'Package', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(14, 'Activity', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(15, 'Task', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(16, 'Module', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(17, 'CompanyType', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(18, 'CategoryType', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(19, 'CategoryMaster', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(20, 'Salary', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(21, 'Bank', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(22, 'Department', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(23, 'CompanyWorkShift', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(24, 'IP', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(25, 'FollowUp', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(26, 'Journal', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(27, 'JournalAction', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(28, 'Deviation', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(29, 'role', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(30, 'permission', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(31, 'common', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(32, 'Stampling', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(33, 'EmployeeAssignedWorkingHour', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(34, 'Folder', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(35, 'File', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(36, 'Company', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(37, 'User', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(38, 'Notification', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(39, 'mobile_web', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(40, 'employee_listing', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `ip_assigne_to_employees`
--

CREATE TABLE `ip_assigne_to_employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_follow_ups`
--

CREATE TABLE `ip_follow_ups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `action_by` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` text COLLATE utf8mb4_unicode_ci,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `approved_date` date DEFAULT NULL,
  `documents` longtext COLLATE utf8mb4_unicode_ci,
  `action_date` timestamp NULL DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `witness` text COLLATE utf8mb4_unicode_ci,
  `more_witness` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Approved,2:Completed,3:Reject,4:Hold',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_latest_entry` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_follow_up_creations`
--

CREATE TABLE `ip_follow_up_creations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip_id` bigint(20) UNSIGNED DEFAULT NULL,
  `follow_up_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_family_member` tinyint(1) NOT NULL DEFAULT '0',
  `is_caretaker` tinyint(1) NOT NULL DEFAULT '0',
  `is_contact_person` tinyint(1) NOT NULL DEFAULT '0',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_templates`
--

CREATE TABLE `ip_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_id` bigint(20) UNSIGNED NOT NULL,
  `template_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `signed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_note` text COLLATE utf8mb4_unicode_ci,
  `date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2022-09-21',
  `time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '03:42',
  `description` text COLLATE utf8mb4_unicode_ci,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `edit_date` datetime DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_signed` tinyint(1) NOT NULL DEFAULT '0',
  `signed_date` date DEFAULT NULL,
  `sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_secret` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_actions`
--

CREATE TABLE `journal_actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `signed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `comment_action` text COLLATE utf8mb4_unicode_ci,
  `comment_result` text COLLATE utf8mb4_unicode_ci,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `edit_date` datetime DEFAULT NULL,
  `is_signed` tinyint(1) NOT NULL DEFAULT '0',
  `signed_date` timestamp NULL DEFAULT NULL,
  `sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_action_logs`
--

CREATE TABLE `journal_action_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_action_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `comment_action` text COLLATE utf8mb4_unicode_ci,
  `comment_result` text COLLATE utf8mb4_unicode_ci,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `comment_created_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_logs`
--

CREATE TABLE `journal_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `description_created_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `label_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'message_unauthorized', 'You are nor authorized to perform this action!', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(2, 1, 1, 'message_list', 'List Fetched', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(3, 1, 1, 'message_create', 'Created Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(4, 1, 1, 'message_update', 'Updated Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(5, 1, 1, 'message_show', 'Detail Fetched!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(6, 1, 1, 'message_delete', 'Deleted Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(7, 1, 1, 'message_record_not_found', 'Record does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(8, 1, 1, 'message_record_already_exists', 'Record Already exists!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(9, 1, 1, 'message_approve', 'Approved Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(10, 1, 1, 'message_assign', 'Assigned Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(11, 1, 1, 'message_import', 'Data Imported Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(12, 1, 1, 'message_some_data_not_imported', 'Some Data not Imported <br>', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(13, 1, 1, 'message_export', 'Data Exported Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(14, 1, 1, 'message_action', 'Action performed Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(15, 1, 1, 'message_log', 'Log history fetched Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(16, 1, 1, 'message_mark_not_applicable', 'Marked as not applicable Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(17, 1, 1, 'message_tag_added', 'Tag added Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(18, 1, 1, 'message_package_already_assigned', 'Package is Already Assigned!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(19, 1, 1, 'message_english_cannot_be_deleted', 'English language cannot be deleted!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(20, 1, 1, 'message_subscription_already_expired', 'Subscription already Expired!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(21, 1, 1, 'message_stats', 'Statistics Fetched!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(22, 1, 1, 'message_cancel', 'Cancelled succesfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(23, 2, 1, 'message_user_type_id_required', 'Title required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(24, 2, 1, 'message_title_required', 'Title required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(25, 2, 1, 'message_name_required', 'Name required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(26, 2, 1, 'message_email_required', 'Email required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(27, 2, 1, 'message_email_invalid', 'Email invalid!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(28, 2, 1, 'message_password_required', 'Password required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(29, 2, 1, 'message_password_min', 'min Password 6!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(30, 2, 1, 'message_password_max', 'max Password 30!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(31, 2, 1, 'message_password_confirm_match', 'Password confirm doesnt match!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(32, 2, 1, 'message_contact_number_required', 'Contact number required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(33, 2, 1, 'message_package_id_required', 'Package Id required!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(34, 3, 1, 'message_list', 'Schedules List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(35, 3, 1, 'message_create', 'Schedule Created Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(36, 3, 1, 'message_show', 'Schedule View!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(37, 3, 1, 'message_delete', 'Schedule Deleted Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(38, 3, 1, 'message_record_not_found', 'Schedule does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(39, 3, 1, 'message_dates_list', 'Schedules Dates List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(40, 3, 1, 'message_statistics', 'Schedules Statistics', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(41, 3, 1, 'message_patient_assigned_hours_export', 'Patient Assigned hours data exported', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(42, 3, 1, 'message_employee_working_hours_export', 'employee working hours data exported', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(43, 3, 1, 'message_verify', 'Schedules verified', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(44, 3, 1, 'message_approve', 'Schedules Approved', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(45, 3, 1, 'message_employee_hours', 'employee worked hours data', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(46, 3, 1, 'message_patients_data', 'patient data', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(47, 3, 1, 'message_patient_hours', 'patient completed hours data', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(48, 4, 1, 'message_list', 'Schedules List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(49, 4, 1, 'message_create', 'Schedule Created Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(50, 4, 1, 'message_record_not_found', 'Schedule does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(51, 4, 1, 'message_update', 'Schedule Updated Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(52, 4, 1, 'message_delete', 'Schedule Deleted Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(53, 4, 1, 'message_already_deactivated', 'Once deactivated can not be Activated.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(54, 4, 1, 'message_cannot_activate', 'can not activate as it has not user assigned schedules.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(55, 4, 1, 'message_cannot_deactivate', 'can not deactivate as it is only active template', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(56, 4, 1, 'message_change_status', 'Status changed', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(57, 5, 1, 'message_list', 'Leaves List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(58, 5, 1, 'message_create', 'Leave Created Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(59, 5, 1, 'message_show', 'Leave View!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(60, 5, 1, 'message_delete', 'Leave Deleted Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(61, 5, 1, 'message_id_not_found', 'Leave does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(62, 5, 1, 'message_approve', 'Leaves Approved', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(63, 5, 1, 'message_date_must_be_greater_than_today_date', 'date must be grater than', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(64, 5, 1, 'message_create_approve', 'applied and approved successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(65, 6, 1, 'message_list', 'Obe Hours List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(66, 6, 1, 'message_create', 'Obe Hour Created Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(67, 6, 1, 'message_update', 'Obe Hour Updated Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(68, 6, 1, 'message_delete', 'Obe Hour Deleted Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(69, 6, 1, 'message_show', 'Obe Hour Detail Fetched!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(70, 6, 1, 'message_record_not_found', 'Obe Hour does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(71, 7, 1, 'message_list', 'Module Requests List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(72, 7, 1, 'message_create', 'Module Request Created Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(73, 7, 1, 'message_update', 'Module Request Updated Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(74, 7, 1, 'message_show', 'Module Request Detail Fetched!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(75, 7, 1, 'message_delete', 'Module Request Deleted Successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(76, 7, 1, 'message_record_not_found', 'Module Request does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(77, 7, 1, 'message_change_status', 'Module Request status changed.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(78, 8, 1, 'message_email', 'The email field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(79, 8, 1, 'message_email_invalid', 'The email must be a valid email address', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(80, 8, 1, 'message_password', 'The password field is required.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(81, 8, 1, 'message_language_cannot_be_deleted', 'You cannot delete default languages.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(82, 8, 1, 'message_language_deleted', 'Language deleted successfully.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(83, 8, 1, 'message_confirm_password', 'Password  and confirm password does not match.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(84, 8, 1, 'message_user_not_found', 'Unable to find user', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(85, 8, 1, 'message_account_inactive', 'Your account is temparory inactive please contact to your admin', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(86, 8, 1, 'message_account_deactive', 'Your account is permanently deactivate please contact to your admin', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(87, 8, 1, 'message_unable_generate_token', 'Unable to generate token', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(88, 8, 1, 'message_wrong_password', 'Wrong Password', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(89, 8, 1, 'message_email_not_exists', 'This email id is not exists', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(90, 8, 1, 'message_incorrect_old_password', 'Old Password incorrect', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(91, 8, 1, 'message_logout', 'LogOut Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(92, 8, 1, 'message_otp_invalid', 'Invalid OTP', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(93, 8, 1, 'message_password_reset_link', 'Password Reset link has been sent to your registered email id', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(94, 8, 1, 'message_token', 'Token', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(95, 8, 1, 'message_no_token', 'No token found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(96, 8, 1, 'message_login', 'Logged In Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(97, 9, 1, 'message_token', 'The token field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(98, 9, 1, 'message_email', 'The email field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(99, 9, 1, 'message_email_invalid', 'The email must be a valid email address', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(100, 9, 1, 'message_password', 'The password field is required.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(101, 9, 1, 'message_confirm_password', 'Password  and confirm password does not match.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(102, 9, 1, 'message_success', 'Password reset successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(103, 10, 1, 'message_invalid_data', 'Invalid license Key, Please enter valid license key', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(104, 10, 1, 'message_updated', 'License key updated and your license is now activated.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(105, 10, 1, 'message_status_active', 'License is activated.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(106, 10, 1, 'message_status_inactive', 'License key expired, please contact to admin for reactivate license.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(107, 10, 1, 'message_data_doesnt_exist', 'License key not exist, please contact to admin.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(108, 10, 1, 'message_already_assigned', 'License key is already assigned.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(109, 10, 1, 'message_create', 'License created.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(110, 10, 1, 'message_record_not_found', 'License not found.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(111, 10, 1, 'message_subscription_already_expired', 'Subscription already expired.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(112, 10, 1, 'message_cancel', 'Cancelled Successfully.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(113, 11, 1, 'message_old_password', 'The old password field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(114, 11, 1, 'message_new_password', 'The  new password field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(115, 11, 1, 'message_new_password_confirm', 'Password  and confirm password does not match', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(116, 11, 1, 'message_new_password_confirmation', 'The confirm password field is required.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(117, 12, 1, 'message_id', 'User id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(118, 12, 1, 'message_role_id', 'Please select user role', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(119, 12, 1, 'message_user_type_id', 'User type is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(120, 12, 1, 'message_company_type_id', 'Company type is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(121, 12, 1, 'message_category_id', 'Category is is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(122, 12, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(123, 12, 1, 'message_email', 'Email address is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(124, 12, 1, 'message_email_invalid', 'Email address is invalid', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(125, 12, 1, 'message_password', 'Password is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(126, 12, 1, 'message_password_min', 'Password should be 8 character long', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(127, 12, 1, 'message_contact_number', 'Contact number is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(128, 12, 1, 'message_create', 'User Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(129, 12, 1, 'message_update', 'User Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(130, 12, 1, 'message_delete', 'User Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(131, 12, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(132, 13, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(133, 13, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(134, 13, 1, 'message_price', 'Price is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(135, 13, 1, 'message_validity_in_days', 'Validity in days field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(136, 13, 1, 'message_number_of_patients', 'No of patients field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(137, 13, 1, 'message_number_of_employees', 'No of employee field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(138, 13, 1, 'message_discount_type', 'The discount type field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(139, 13, 1, 'message_discount_value', 'The discount value field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(140, 13, 1, 'message_create', 'Package Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(141, 13, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(142, 13, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(143, 13, 1, 'message_update', 'Package Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(144, 13, 1, 'message_delete', 'Package Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(145, 13, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(146, 13, 1, 'message_assigne', 'Package Assigne successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(147, 13, 1, 'message_restore', 'Package Restored successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(148, 14, 1, 'message_record_not_found', 'Activity Record with given data does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(149, 14, 1, 'message_record_already_exists', 'Activity Record Already exists with given data!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(150, 14, 1, 'message_no_date_found', 'No Date data found for activity!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(151, 14, 1, 'message_already_assigned', 'Activity is Already Assigned to this user!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(152, 14, 1, 'message_assign', 'Activity assigned successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(153, 14, 1, 'message_log', 'Activity Log History fetched successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(154, 14, 1, 'message_cancel', 'Cancelled succesfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(155, 14, 1, 'message_restore', 'Restored succesfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(156, 14, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(157, 14, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(158, 14, 1, 'message_activity_class_id.required', 'Activity classification is field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(159, 14, 1, 'message_category_id.required', 'Category field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(160, 14, 1, 'message_title.required', 'Title field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(161, 14, 1, 'message_description.required', 'Description is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(162, 14, 1, 'message_activity_type.required', 'Activity type field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(163, 14, 1, 'message_activity_type_in.required', 'Please select Activity type correct option', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(164, 14, 1, 'message_start_date.required', 'Start date field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(165, 14, 1, 'message_start_time.required', 'Start time field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(166, 14, 1, 'message_end_date.required', 'End date must be greather than start date', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(167, 14, 1, 'message_end_time.required', 'End time must be greather than start time', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(168, 14, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(169, 14, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(170, 14, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(171, 14, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(172, 14, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(173, 14, 1, 'message_approve', 'Approved Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(174, 14, 1, 'message_activity_id', 'Activity id field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(175, 14, 1, 'message_user_id', 'User id field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(176, 14, 1, 'message_assignment_date', 'Assignment date field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(177, 14, 1, 'message_assignment_day', 'Assignment day field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(178, 15, 1, 'message_record_not_found', 'Task Record with given data does not exists.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(179, 15, 1, 'message_record_already_exists', 'Task Record Already exists with given data!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(180, 15, 1, 'message_no_date_found', 'No Date data found for activity!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(181, 15, 1, 'message_action', 'Action performed on Task !', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(182, 15, 1, 'message_log', 'Task Log History fetched successfully!', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(183, 15, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(184, 15, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(185, 15, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(186, 15, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(187, 15, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(188, 15, 1, 'message_approve', 'Approved Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(189, 15, 1, 'message_assigne', 'Assigned Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(190, 16, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(191, 16, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(192, 16, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(193, 16, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(194, 16, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(195, 16, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(196, 16, 1, 'message_name_already_exists', 'This name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(197, 16, 1, 'message_assigne', 'Moduel Assigne successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(198, 17, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(199, 17, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(200, 17, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(201, 17, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(202, 17, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(203, 17, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(204, 17, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(205, 17, 1, 'message_record_not_found', 'Record Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(206, 17, 1, 'message_name_already_exists', 'This name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(207, 18, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(208, 18, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(209, 18, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(210, 18, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(211, 18, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(212, 18, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(213, 18, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(214, 18, 1, 'message_record_not_found', 'Record Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(215, 18, 1, 'message_name_already_exists', 'This name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(216, 19, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(217, 19, 1, 'message_category_type_id', 'Category type Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(218, 19, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(219, 19, 1, 'message_list', 'List Fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(220, 19, 1, 'message_parent_list', 'Parent List Fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(221, 19, 1, 'message_child_list', 'Child List Fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(222, 19, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(223, 19, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(224, 19, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(225, 19, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(226, 19, 1, 'message_record_not_found', 'Record Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(227, 19, 1, 'message_parent_record_not_found', 'Parent id Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(228, 19, 1, 'message_name_already_exists', 'This name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(229, 20, 1, 'message_user_id', 'User Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(230, 20, 1, 'message_salary_per_month', 'Salary per month field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(231, 20, 1, 'message_salary_package_start_date', 'Package Start date  is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(232, 20, 1, 'message_salary_package_end_date', 'Package End date is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(233, 20, 1, 'message_salary_package_end_date_after', 'Package end date must be grether then package start date', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(234, 20, 1, 'message_update', 'Salary Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(235, 21, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(236, 21, 1, 'message_bank_name', 'Bank Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(237, 21, 1, 'message_account_number', 'Account Number is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(238, 21, 1, 'message_clearance_number', 'Clearance Number is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(239, 21, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(240, 21, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(241, 21, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(242, 21, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(243, 21, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(244, 21, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(245, 21, 1, 'message_name_already_exists', 'This name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(246, 22, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(247, 22, 1, 'message_name', 'Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(248, 22, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(249, 22, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(250, 22, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(251, 22, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(252, 22, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(253, 22, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(254, 22, 1, 'message_name_already_exists', 'This name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(255, 22, 1, 'message_chil_level_exceed', 'Child level exceed you do not create department more than five level ', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(256, 23, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(257, 23, 1, 'message_shift_name', 'Shift Name is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(258, 23, 1, 'message_shift_start_time', 'Shift start time is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(259, 23, 1, 'message_shift_end_time', 'Shift end time is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(260, 23, 1, 'message_shift_end_time_after', 'Shift End time must be greather than start time', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(261, 23, 1, 'message_list', 'Company Work Shifts List', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(262, 23, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(263, 23, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(264, 23, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(265, 23, 1, 'message_record_not_found', 'Record Not Found', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(266, 23, 1, 'message_shift_name_exists', 'This Shift Name Already Exist', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(267, 23, 1, 'message_user_id', 'User id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(268, 23, 1, 'message_shift_id', 'Shift id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(269, 23, 1, 'message_shift_start_date', 'Shift start date is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(270, 23, 1, 'message_shift_end_date', 'Shift end date is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(271, 23, 1, 'message_shift_end_date_after', 'Shift End date must be greather than start date', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(272, 23, 1, 'message_shift_already_assigne', 'This shift is already assigne to this user', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(273, 23, 1, 'message_shift_already_assigne_date', 'shift is already assigne to this user on this date.', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(274, 24, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(275, 24, 1, 'message_user_id', 'User Id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(276, 24, 1, 'message_category_id', 'Category id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(277, 24, 1, 'message_subcategory_id', 'Subcategory id is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(278, 24, 1, 'message_what_happened', 'What happend field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(279, 24, 1, 'message_how_it_happened', 'How is happend field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(280, 24, 1, 'message_when_it_started', 'When it started field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(281, 24, 1, 'message_what_to_do', 'What to do field required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(282, 24, 1, 'message_goal', 'Goal field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(283, 24, 1, 'message_sub_goal', 'Sub goal field required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(284, 24, 1, 'message_plan_start_date', 'Plan start date is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(285, 24, 1, 'message_plan_start_time', 'Plan start time is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(286, 24, 1, 'message_remark', 'Remark field is required', 1, NULL, '2022-09-21 15:42:53', '2022-09-21 15:42:53'),
(287, 24, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(288, 24, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(289, 24, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(290, 24, 1, 'message_assigne', 'Ip Assigne successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(291, 24, 1, 'message_approve', 'Ip Approve successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(292, 24, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(293, 24, 1, 'message_ip_id', 'Ip id is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(294, 24, 1, 'message_patient_already_assigne', 'This Patient plan is already assigne to this employee.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(295, 24, 1, 'message_list', ' Patients List Fetched', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(296, 24, 1, 'message_show', ' Patient Detail Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(297, 24, 1, 'message_template_list', 'Patient has already one subsciption', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(298, 24, 1, 'message_not_approved', 'Cannot complete this IP because the IP is not yet approved. please go back and approve this IP first.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(299, 25, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(300, 25, 1, 'message_ip_id', 'Ip id  is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(301, 25, 1, 'message_title', 'Title field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(302, 25, 1, 'message_description', 'Description field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(303, 25, 1, 'message_follow_up_type', 'Follow up type is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(304, 25, 1, 'message_start_date', 'Start date field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(305, 25, 1, 'message_start_time', 'Start time  field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(306, 25, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(307, 25, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(308, 25, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(309, 25, 1, 'message_approve', 'Approved Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(310, 25, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(311, 25, 1, 'message_ip_not_found', 'Ip Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(312, 25, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(313, 25, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(314, 25, 1, 'message_complete', 'Completed Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(315, 25, 1, 'message_log', 'Edit History fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(316, 26, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(317, 26, 1, 'message_activity_id', 'Activity id field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(318, 26, 1, 'message_category_id', 'Category id field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(319, 26, 1, 'message_title', 'Title field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(320, 26, 1, 'message_description', 'Description field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(321, 26, 1, 'message_list', 'Journals List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(322, 26, 1, 'message_create', 'Journal Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(323, 26, 1, 'message_update', 'Journal Updated Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(324, 26, 1, 'message_show', 'Journal Detail Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(325, 26, 1, 'message_delete', 'Journal Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(326, 26, 1, 'message_record_not_found', 'Journal does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(327, 26, 1, 'message_print', 'Journal Printed!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(328, 26, 1, 'message_sign', 'Journal Sign status changed!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(329, 26, 1, 'message_active', 'Journal Active status changed', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(330, 26, 1, 'message_approve', 'Approved Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(331, 27, 1, 'message_journal_id_required', 'Journal Id required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(332, 27, 1, 'message_atleast_one_field_required', 'You need to fill atleast one field', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(333, 27, 1, 'message_list', 'Journal Actions List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(334, 27, 1, 'message_create', 'Journal Action Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(335, 27, 1, 'message_update', 'Journal Action Updated Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(336, 27, 1, 'message_show', 'Journal Action Detail Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(337, 27, 1, 'message_delete', 'Journal Action Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(338, 27, 1, 'message_record_not_found', 'Journal Action does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(339, 27, 1, 'message_sign', 'Journal Action Sign status changed!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(340, 28, 1, 'message_id', 'Id is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(341, 28, 1, 'message_category_id', 'Category id field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(342, 28, 1, 'message_sub_category_id', 'Subcategory field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(343, 28, 1, 'message_description', 'Description field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(344, 28, 1, 'message_date_time', 'date and time field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(345, 28, 1, 'message_immediate_action', 'Immediate action field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(346, 28, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(347, 28, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(348, 28, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(349, 28, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(350, 28, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(351, 28, 1, 'message_approve', 'Approved Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(352, 28, 1, 'message_print', 'Printed Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(353, 28, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(354, 29, 1, 'message_se_name', 'Se name field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(355, 29, 1, 'message_permissions', 'Permissions id field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(356, 29, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(357, 29, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(358, 29, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(359, 29, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(360, 29, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(361, 29, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(362, 29, 1, 'message_role_not_found', 'Role Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(363, 30, 1, 'message_name', 'Name field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(364, 30, 1, 'message_name_unique', 'Name field must be unique', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(365, 30, 1, 'message_se_name', 'Se name field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(366, 30, 1, 'message_se_name_unique', 'Se name field must be unique', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(367, 30, 1, 'message_group_name', 'Group name field is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(368, 30, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(369, 30, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(370, 30, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(371, 30, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(372, 30, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(373, 30, 1, 'message_record_not_found', 'Id Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(374, 30, 1, 'message_per_not_found', 'Permission Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(375, 30, 1, 'message_groupwise_list', 'Groupwise list', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(376, 30, 1, 'message_assigne', 'Permission assigned successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(377, 31, 1, 'cant_delete', 'You cannot delete this record.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(378, 32, 1, 'message_list', 'Stamplings List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(379, 32, 1, 'message_create', 'Stampling Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(380, 32, 1, 'message_show', 'Stampling View!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(381, 32, 1, 'message_delete', 'Stampling Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(382, 32, 1, 'message_record_not_found', 'Stampling does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(383, 32, 1, 'message_employee_hours', 'employee worked hours data', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(384, 32, 1, 'message_active_template_unavailable', 'Not any active template available', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(385, 33, 1, 'message_emp_id', 'Employee Id is required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(386, 33, 1, 'message_create', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(387, 33, 1, 'message_list', 'List fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(388, 33, 1, 'message_show', 'Detail fetched Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(389, 33, 1, 'message_update', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(390, 33, 1, 'message_delete', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(391, 33, 1, 'message_record_not_found', 'Record Not Found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(392, 34, 1, 'message_list', 'Folders List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(393, 34, 1, 'message_create', 'Folder Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(394, 34, 1, 'message_show', 'Folder View!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(395, 34, 1, 'message_delete', 'Folder Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(396, 34, 1, 'message_record_not_found', 'Folder does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(397, 34, 1, 'message_parent_record_not_found', 'Folder Parent does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(398, 34, 1, 'message_parent_list', 'parent list data fetched successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(399, 34, 1, 'message_name_already_exists', 'folder with given name already exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(400, 35, 1, 'message_list_admin', 'Admin Files List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(401, 35, 1, 'message_list_company', 'Admin Files List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(402, 35, 1, 'message_access_log_create', 'File access log Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(403, 35, 1, 'message_delete', 'File Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(404, 35, 1, 'message_record_not_found', 'File does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(405, 35, 1, 'message_file_not_allowed', 'File not allowed!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(406, 35, 1, 'message_create', 'File uploaded.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(407, 36, 1, 'message_list', ' Companies List Fetched', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(408, 36, 1, 'message_create', ' Company Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(409, 36, 1, 'message_update', ' Company Updated Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(410, 36, 1, 'message_show', ' Company Detail Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(411, 36, 1, 'message_delete', ' Company Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(412, 36, 1, 'message_record_not_found', ' Company Record does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(413, 36, 1, 'message_record_already_exists', ' Company Record with entered data Already exists!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(414, 36, 1, 'message_already_subscribed', 'Company has already one subsciption', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(415, 36, 1, 'message_stats', 'Company Statistics Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(416, 37, 1, 'message_list', ' Users List Fetched', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(417, 37, 1, 'message_create', ' User Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(418, 37, 1, 'message_update', ' User Updated Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(419, 37, 1, 'message_show', ' User Detail Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(420, 37, 1, 'message_delete', ' User Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54');
INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(421, 37, 1, 'message_record_not_found', ' User Record does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(422, 37, 1, 'message_record_already_exists', ' User Record with entered data Already exists!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(423, 37, 1, 'message_password_change', ' Password changed successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(424, 37, 1, 'message_email_dob_error', 'Email or DOB doesnt match', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(425, 37, 1, 'message_language_change', 'Language changed successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(426, 38, 1, 'message_list', ' Notifications List Fetched', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(427, 38, 1, 'message_create', ' Notification Created Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(428, 38, 1, 'message_update', ' Notification Updated Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(429, 38, 1, 'message_show', ' Notification Detail Fetched!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(430, 38, 1, 'message_delete', ' Notification Deleted Successfully!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(431, 38, 1, 'message_record_not_found', ' Notification Record does not exists.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(432, 38, 1, 'message_record_already_exists', ' Notification Record with entered data Already exists!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(433, 38, 1, 'message_read', ' Notification read successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(434, 38, 1, 'message_count', 'notification counts fetched successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(435, 39, 1, 'message_alert', 'Alert', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(436, 39, 1, 'message_delete_confirmation', 'Do you want to delete this ?', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(437, 39, 1, 'message_delete_success', 'Deleted Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(438, 39, 1, 'message_add_success', 'Added Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(439, 39, 1, 'message_update_success', 'Updated Successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(440, 39, 1, 'message_lose_data_alert_category', 'Upon selecting new category you will lose all your filled data, do you wish to continue ?', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(441, 39, 1, 'message_something_went_wrong', 'Something went wrong !!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(442, 39, 1, 'message_choose_document_first', 'Choose document first', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(443, 39, 1, 'message_url_can_not_open', 'Can not open URL', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(444, 39, 1, 'message_uploaded_successfully', 'Uploaded Successfully !!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(445, 39, 1, 'message_no_file_uploaded', 'No file uploaded yet !!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(446, 39, 1, 'message_removed_successfully', 'Removed successfully', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(447, 39, 1, 'message_select_persons_to_request', 'First select persons to send request', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(448, 39, 1, 'message_fill_all_required_fields', 'Please fill all required fields', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(449, 39, 1, 'message_bad_word_alert', 'Red words detected in form, Do you want to continue ?', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(450, 39, 1, 'maximum_limit_reached', 'Maximum limit reached', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(451, 39, 1, 'message_login_first', 'Please login first', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(452, 39, 1, 'action_is_not_available', 'Action is not available', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(453, 39, 1, 'license_assigned', 'License assigned', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(454, 39, 1, 'company_has_active_license', 'Company has active license', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(455, 39, 1, 'required_fields_are_empty', 'Required fields are empty', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(456, 39, 1, 'something_went_wrong', 'Something went wrong !!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(457, 39, 1, 'connect_internet_message', 'Please check your internet connection !!', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(458, 39, 1, 'alert_message', 'Alert', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(459, 39, 1, 'please_select_some_data_first', 'Please select some data first', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(460, 39, 1, 'session_expired', 'Session expired, Please re-login', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(461, 39, 1, 'location_permission_required', 'Location permission required', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(462, 39, 1, 'grant_location_permission', 'Please grant the location permission to proceed furthur', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(463, 39, 1, 'app_camera_permission', 'App Camera Permission', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(464, 39, 1, 'app_needs_access_to_your_camera', 'App needs access to your camera', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(465, 39, 1, 'app_image_permission', 'App Phone\'s Storage Permission', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(466, 39, 1, 'app_needs_access_to_your_image_library', 'App needs access to your phone\'s storage', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(467, 39, 1, 'ask_me_later', 'Ask Me Later', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(468, 39, 1, 'cancel', 'Cancel', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(469, 39, 1, 'ok', 'OK', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(470, 39, 1, 'select_photo', 'Select photo', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(471, 39, 1, 'launch_camera', 'Launch camera', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(472, 39, 1, 'load_from_gallery', 'Load from gallery', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(473, 39, 1, 'something_went_wrong_upload', 'Something went wrong while uploading file', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(474, 39, 1, 'file_size_error_message', 'File size should be less than or equal to 5 MB', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(475, 39, 1, 'file_variable_size_error_message', 'File size should be less than or equal to', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(476, 39, 1, 'license_validity_restored_Log_in_again', 'License validity restored log in again', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(477, 39, 1, 'license_expired_renew_now', 'License Expired, Renew Now', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(478, 39, 1, 'max_limit_reached', 'Max limit reached', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(479, 39, 1, 'camera_permission_not_available', 'Camera permission not available', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(480, 39, 1, 'image_permission_not_available', 'Phone\'s storage permission not available', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(481, 39, 1, 'activities', 'Manage Activities', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:55'),
(482, 39, 1, 'journals', 'Journals', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:02'),
(483, 39, 1, 'deviations', 'Deviations', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:02'),
(484, 39, 1, 'schedule', 'Schedule', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:00'),
(485, 39, 1, 'categories', 'Categories', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:55'),
(486, 39, 1, 'branches', 'Branches', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:02'),
(487, 39, 1, 'departments', 'Departments', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:02'),
(488, 39, 1, 'employees', 'Employees', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(489, 39, 1, 'patients', 'Patients', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(490, 39, 1, 'iP', 'IP', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:59'),
(491, 39, 1, 'IP_mobile_dashboard', 'Implementation Plans', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(492, 39, 1, 'ContactPerson', 'Contact Person', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(493, 39, 1, 'tasks', 'Tasks', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:58'),
(494, 39, 1, 'Word', 'Word', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(495, 39, 1, 'paragraph', 'Paragraph', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:57'),
(496, 39, 1, 'package', 'Package', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(497, 39, 1, 'Modules', 'Modules', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:55'),
(498, 39, 1, 'companies', 'Companies', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(499, 39, 1, 'roles', 'Roles', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:56'),
(500, 39, 1, 'log_in', 'Log In', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(501, 39, 1, 'log_out', 'Log Out', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(502, 39, 1, 'empty_notification_message', 'No notifications to show', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(503, 39, 1, 'category_imported_from_ip', 'Category imported from IP', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(504, 39, 1, 'sub_category_imported_from_ip', 'Sub category imported from IP', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(505, 39, 1, 'minimum_value_exist', 'Minimum value exist', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(506, 39, 1, 'link_is_not_available', 'Link is not Available', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(507, 39, 1, 'click_add', 'Click on + Button to add data', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(508, 39, 1, 'no_data_found', 'Data not found.', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(509, 39, 1, 'permission_required_for_this_action', 'Permission required for this action', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(510, 39, 1, 'load_more', 'Load More', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(511, 39, 1, 'minor_child_can_not_be_a_old_age', 'Minor Child Can Not Be A Old Age', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(512, 39, 1, 'minor_child_can_not_be_a_worker', 'Minor Child Can Not Be A Worker', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(513, 39, 1, 'working_or_old_age_can_not_be_minor_child', 'Working Or Old Age Can Not Be Minor Child', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(514, 39, 1, 'empty_chat_message', 'No messages to show', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(515, 39, 1, 'chats', 'Chats', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(516, 39, 1, 'Dashboards', 'Dashboards', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(517, 39, 1, 'Analytics', 'Analytics', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(518, 39, 1, 'Apps', 'Apps', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(519, 39, 1, 'Ecommerce', 'E-commerce', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(520, 39, 1, 'email', 'Email', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:01'),
(521, 39, 1, 'logs', 'Logs', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(522, 39, 1, 'Chat', 'Chat', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(523, 39, 1, 'inactive-this-journal', 'Inactive this journal', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(524, 39, 1, 'sign-this-journal', 'Sign This Journal', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(525, 39, 1, 'edited-at', 'Edited At', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(526, 39, 1, 'sign-at', 'Sign At', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(527, 39, 1, 'personal-no', 'Personal no', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(528, 39, 1, 'sign-by', 'Sign By', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(529, 39, 1, 'not-signed', 'Not Signed', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(530, 39, 1, 'total-active', 'Total active', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(531, 39, 1, 'total-secret', 'Total secret', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(532, 39, 1, 'click-to-sign', 'Click to sign', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(533, 39, 1, 'edit-action/result', 'Edit Action/Result', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(534, 39, 1, 'total-signed', 'Total signed', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(535, 39, 1, 'with-activity', 'With activity', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(536, 39, 1, 'without-activity', 'Without activity', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(537, 39, 1, 'no-activity-found', 'no activity found', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(538, 39, 1, 'result', 'Result', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(539, 39, 1, 'Todo', 'Todo', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(540, 39, 1, 'activity-note', 'Activity note', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(541, 39, 1, 'please-select-patient', 'Please select patient', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(542, 39, 1, 'sign', 'Sign', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(543, 39, 1, 'action/result', 'Action/Result', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(544, 39, 1, 'calendar', 'Calendar', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:55'),
(545, 39, 1, 'Shop', 'Shop', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(546, 39, 1, 'created-by', 'Created By', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(547, 39, 1, 'Wish List', 'Wish List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(548, 39, 1, 'details', 'Details', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:56'),
(549, 39, 1, 'Checkout', 'Checkout', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(550, 39, 1, 'User', 'User', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(551, 39, 1, 'list', 'List', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:04'),
(552, 39, 1, 'secret-journal', 'Secret Journal', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(553, 39, 1, 'view', 'View', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:01'),
(554, 39, 1, 'edit', 'Edit', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:04'),
(555, 39, 1, 'Starter Kit', 'Starter Kit', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:42:54'),
(556, 39, 1, '1', '1', 1, NULL, '2022-09-21 15:42:54', '2022-09-21 15:43:03'),
(557, 39, 1, '2', '2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:43:03'),
(558, 39, 1, 'Fixed Navbar', 'Fixed Navbar', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(559, 39, 1, 'Floating Navbar', 'Floating Navbar', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(560, 39, 1, 'Fixed Layout', 'Fixed Layout', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(561, 39, 1, 'Static Layout', 'Static Layout', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(562, 39, 1, 'Dark Layout', 'Dark Layout', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(563, 39, 1, 'Light Layout', 'Light Layout', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(564, 39, 1, 'UI Elements', 'UI Elements', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(565, 39, 1, 'Content', 'Content', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(566, 39, 1, 'Grid', 'Grid', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(567, 39, 1, 'Typography', 'Typography', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(568, 39, 1, 'Text Utilities', 'Text Utilities', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(569, 39, 1, 'Syntax Highlighter', 'Syntax Highlighter', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(570, 39, 1, 'Icons', 'Icons', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(571, 39, 1, 'Feather', 'Feather', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(572, 39, 1, 'Card', 'Card', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(573, 39, 1, 'upload-document', 'Upload Document', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(574, 39, 1, 'basic', 'Basic', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:43:01'),
(575, 39, 1, 'special-information', 'Special Information', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(576, 39, 1, 'Advance', 'Advance', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(577, 39, 1, 'statistics', 'Statistics', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:59'),
(578, 39, 1, 'actions', 'Actions', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(579, 39, 1, 'Table', 'Table', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(580, 39, 1, 'Reactstrap Tables', 'Reactstrap Tables', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(581, 39, 1, 'React Tables', 'React Tables', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(582, 39, 1, 'DataTable', 'DataTable', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(583, 39, 1, 'Advanced', 'Advanced', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(584, 39, 1, 'create-journal', 'Create Journal', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(585, 39, 1, 'Mail Template', 'Mail Template', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(586, 39, 1, 'Page Layouts', 'Page Layouts', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(587, 39, 1, 'Collapsed Menu', 'Collapsed Menu', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(588, 39, 1, 'Layout Boxed', 'Layout Boxed', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(589, 39, 1, 'Without Menu', 'Without Menu', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(590, 39, 1, 'Layout Empty', 'Layout Empty', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(591, 39, 1, 'Layout Blank', 'Layout Blank', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(592, 39, 1, 'Components', 'Components', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(593, 39, 1, 'Alerts', 'Alerts', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(594, 39, 1, 'edit/strike', 'Edit/Strike', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(595, 39, 1, 'change-event-date', 'Change event date', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(596, 39, 1, 'Buttons', 'Buttons', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(597, 39, 1, 'Breadcrumbs', 'Breadcrumbs', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(598, 39, 1, 'Carousel', 'Carousel', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(599, 39, 1, 'collapse', 'collapse', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:43:02'),
(600, 39, 1, 'Dropdowns', 'Dropdown', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(601, 39, 1, 'List Group', 'List Group', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(602, 39, 1, 'Modals', 'Modals', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(603, 39, 1, 'Pagination', 'Pagination', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(604, 39, 1, 'Navs Component', 'Nav Component', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(605, 39, 1, 'Navbar', 'Navbar', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(606, 39, 1, 'Tabs Component', 'Tabs Component', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(607, 39, 1, 'Pills Component', 'Pills Component', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(608, 39, 1, 'Tooltips', 'Tooltips', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(609, 39, 1, 'Popovers', 'Popovers', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(610, 39, 1, 'Badges', 'Badges', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(611, 39, 1, 'Pill Badges', 'Pill Badges', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(612, 39, 1, 'Progress', 'Progress', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(613, 39, 1, 'Media Objects', 'Media Objects', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(614, 39, 1, 'Spinner', 'Spinner', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(615, 39, 1, 'Toasts', 'Toasts', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(616, 39, 1, 'timeline', 'Timeline', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:57'),
(617, 39, 1, 'Extra Components', 'Extra Components', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(618, 39, 1, 'Avatar', 'Avatar', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(619, 39, 1, 'Chips', 'Chips', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(620, 39, 1, 'Divider', 'Divider', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(621, 39, 1, 'Wizard', 'Wizard', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(622, 39, 1, 'Forms & Tables', 'Forms & Tables', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(623, 39, 1, 'Form Elements', 'Form Elements', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(624, 39, 1, 'select', 'Select', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(625, 39, 1, 'Switch', 'Switch', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(626, 39, 1, 'Checkbox', 'Checkbox', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(627, 39, 1, 'Radio', 'Radio', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(628, 39, 1, 'Input', 'Input', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(629, 39, 1, 'log', 'Log', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(630, 39, 1, 'sms-log', 'Sms Log', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(631, 39, 1, 'bankID-log', 'BankID Log', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(632, 39, 1, 'subject-type', 'Subject Type', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(633, 39, 1, 'causer-type', 'Causer Type', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(634, 39, 1, 'subject-id', 'Subject Id', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(635, 39, 1, 'Input Groups', 'Input Groups', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(636, 39, 1, 'Number Input', 'Number Input', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(637, 39, 1, 'Textarea', 'Textarea', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(638, 39, 1, 'Date & Time Picker', 'Date & Time Picker', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(639, 39, 1, 'Input Mask', 'Input Mask', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(640, 39, 1, 'Form Layout', 'Form Layout', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(641, 39, 1, 'Form Wizard', 'Form Wizard', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(642, 39, 1, 'React Hook Form', 'React Hook Form', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(643, 39, 1, 'Form Validation', 'Form Validation', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(644, 39, 1, 'Pages', 'Pages', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(645, 39, 1, 'log-name', 'Log Name', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(646, 39, 1, 'activity-log', 'Activity Log', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(647, 39, 1, 'Authentication', 'Authentication', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(648, 39, 1, 'Login v1', 'Login v1', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(649, 39, 1, 'Login v2', 'Login v2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(650, 39, 1, 'Register v1', 'Register v1', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(651, 39, 1, 'Register v2', 'Register v2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(652, 39, 1, 'Forgot Password v1', 'Forgot Password v1', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(653, 39, 1, 'Forgot Password v2', 'Forgot Password v2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(654, 39, 1, 'Reset Password v1', 'Reset Password v1', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(655, 39, 1, 'Reset Password v2', 'Reset Password v2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(656, 39, 1, 'Miscellaneous', 'Miscellaneous', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(657, 39, 1, 'Coming Soon', 'Coming Soon', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(658, 39, 1, 'Error', 'Error', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(659, 39, 1, 'Not Authorized', 'Not Authorized', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(660, 39, 1, 'Maintenance', 'Maintenance', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(661, 39, 1, 'Extensions', 'Extensions', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(662, 39, 1, 'profile', 'Profile', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:56'),
(663, 39, 1, 'Account Settings', 'Account Settings', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(664, 39, 1, 'FAQ', 'FAQ', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(665, 39, 1, 'Knowledge Base', 'Knowledge Base', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(666, 39, 1, 'search', 'search', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(667, 39, 1, 'Invoice', 'Invoice', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(668, 39, 1, 'Charts & Maps', 'Charts & Maps', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(669, 39, 1, 'Charts', 'Charts', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(670, 39, 1, 'Apex', 'Apex', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(671, 39, 1, 'ChartJS', 'ChartJS', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(672, 39, 1, 'Recharts', 'Recharts', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(673, 39, 1, 'Leaflet Maps', 'Leaflet Maps', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(674, 39, 1, 'Sweet Alert', 'Sweet Alert', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(675, 39, 1, 'Toastr', 'Toastr', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(676, 39, 1, 'Sliders', 'Sliders', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(677, 39, 1, 'File Uploader', 'File Uploader', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(678, 39, 1, 'Editor', 'Editor', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(679, 39, 1, 'Drag & Drop', 'Drag & Drop', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(680, 39, 1, 'Tour', 'Tour', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(681, 39, 1, 'Auto Complete', 'Auto Complete', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(682, 39, 1, 'Clipboard', 'Clipboard', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(683, 39, 1, 'React Player', 'React Player', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(684, 39, 1, 'Swiper', 'Swiper', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(685, 39, 1, 'Context Menu', 'Context Menu', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(686, 39, 1, 'Tree', 'Tree', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(687, 39, 1, 'I18n', 'I18n', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(688, 39, 1, 'React Paginate', 'React Paginate', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(689, 39, 1, 'Export', 'Export', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(690, 39, 1, 'import', 'Import', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:43:06'),
(691, 39, 1, 'Export Selected', 'Export Selected', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(692, 39, 1, 'Access Control', 'Access Control', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(693, 39, 1, 'Others', 'Others', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(694, 39, 1, 'Menu Levels', 'Menu Levels', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(695, 39, 1, 'Second Level', 'Second Level', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(696, 39, 1, 'Second Level 2.1', 'Second Level 2.1', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(697, 39, 1, 'Second Level 2.2', 'Second Level 2.2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(698, 39, 1, 'Third Level 3.1', 'Third Level 3.1', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(699, 39, 1, 'Third Level 3.2', 'Third Level 3.2', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(700, 39, 1, 'Disabled Menu', 'Disabled Menu', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(701, 39, 1, 'Documentation', 'Documentation', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(702, 39, 1, 'Raise Support', 'Raise Support', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(703, 39, 1, 'Change Log', 'Change Log', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(704, 39, 1, 'text', 'Cake sesame snaps cupcake gingerbread danish I love gingerbread. Apple pie pie jujubes chupa chups muffin halvah lollipop. Chocolate cake oat cake tiramisu marzipan sugar plum. Donut sweet pi', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(705, 39, 1, 'Pricing', 'Pricing', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(706, 39, 1, 'Blog', 'Blog', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(707, 39, 1, 'Detail', 'Detail', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(708, 39, 1, 'Form Repeater', 'Form Repeater', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(709, 39, 1, 'preview', 'Preview', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:57'),
(710, 39, 1, 'add', 'Add', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:43:01'),
(711, 39, 1, 'click-to-inactive', 'Click to inactive', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(712, 39, 1, 'click-to-active', 'Click to active', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(713, 39, 1, 'strike', 'Strike', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(714, 39, 1, 'Ratings', 'Ratings', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(715, 39, 1, 'show', 'show', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(716, 39, 1, 'entries', 'entries', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(717, 39, 1, 'prev', 'Prev', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:57'),
(718, 39, 1, 'signed-by', 'Signed by', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(719, 39, 1, 'next', 'Next', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:57'),
(720, 39, 1, 'task-details', 'Task Details', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(721, 39, 1, 'BlockUI', 'BlockUI', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(722, 39, 1, 'Reactstrap', 'Reactstrap', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(723, 39, 1, 'welcome', 'Welcome', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:56'),
(724, 39, 1, 'Reset Password', 'Reset Password', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(725, 39, 1, 'Verify Email', 'Verify Email', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(726, 39, 1, 'Deactivate Account', 'Deactivate Account', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(727, 39, 1, 'Promotional', 'Promotional', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(728, 39, 1, 'Apps & Pages', 'Apps & Pages', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(729, 39, 1, 'User Interface', 'User Interface', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(730, 39, 1, 'Misc', 'Misc', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(731, 39, 1, 'License', 'License', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(732, 39, 1, 'API Key', 'API Key', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(733, 39, 1, 'Accordion', 'Accordion', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(734, 39, 1, 'OffCanvas', 'OffCanvas', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(735, 39, 1, 'permissions', 'Permissions', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:56'),
(736, 39, 1, 'Modal Examples', 'Modal Examples', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(737, 39, 1, 'Roles & Permissions', 'Roles & Permissions', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(738, 39, 1, 'dashboard', 'Dashboard', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:59'),
(739, 39, 1, 'loading-error', 'Unable to load the resource, please try again.', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(740, 39, 1, 'login-failed', 'Login Failed, try again', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(741, 39, 1, 'login-successful', 'Login Successful', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(742, 39, 1, 'abc', 'ABC', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(743, 39, 1, 'Home', 'Home', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(744, 39, 1, 'activity', 'Activity', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:58'),
(745, 39, 1, 'Examples', 'Examples', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(746, 39, 1, 'my-activity', 'My Activity', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(747, 39, 1, 'filter', 'Filter', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(748, 39, 1, 'all-companies', 'All Companies', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(749, 39, 1, 'manage-categories', 'Manage Categories', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(750, 39, 1, 'manage-category-types', 'Manage Category Types', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(751, 39, 1, 'create-new', 'Create New', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(752, 39, 1, 'category-type', 'Category Types', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(753, 39, 1, 'enter-category-name', 'Enter Category Name', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(754, 39, 1, 'save', 'Save', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(755, 39, 1, 'no.-of-employees', 'No. of employees', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(756, 39, 1, 'not-done', 'Not Done', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(757, 39, 1, 'process', 'Process', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(758, 39, 1, 'no.-of-patients', 'No. of patients', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(759, 39, 1, 'completed', 'Completed', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(760, 39, 1, 'data-fetch-failed', 'Data loading failed, Please check the internet connection or contact the administration.', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(761, 39, 1, 'data-save-failed', 'Date save failed, Please try again later', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(762, 39, 1, 'data-saved', 'Data Saved Successfully', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(763, 39, 1, 'no.-of-implementation', 'No. of implementation', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(764, 39, 1, 'no.-of-follow up', 'No. of Follow up', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(765, 39, 1, 'no.-of-activity', 'No. of Activity', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(766, 39, 1, 'no.-of-department', 'No. of Department', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(767, 39, 1, 'no.-of-branches', 'No. of Branches', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(768, 39, 1, 'delete', 'Delete', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(769, 39, 1, 'activate', 'Activate', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(770, 39, 1, 'deactivate', 'Deactivate', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(771, 39, 1, 'status', 'Status', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(772, 39, 1, 'active', 'Active', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(773, 39, 1, 'inactive', 'Inactive', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(774, 39, 1, 'Masters', 'Masters', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(775, 39, 1, 'licenses', 'Licenses', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(776, 39, 1, 'packages', 'Packages', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(777, 39, 1, 'are-you-sure', 'Are You Sure', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(778, 39, 1, 'delete-this', 'Delete {{name}}?', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(779, 39, 1, 'you-wont-be-able-to-revert-this', 'You won\'t be able to revert this!', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(780, 39, 1, 'deleted', 'Deleted!', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(781, 39, 1, 'item-deleted', 'Delete Successful', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(782, 39, 1, 'yes', 'Yes', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(783, 39, 1, 'change-status', 'Change Status', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(784, 39, 1, 'data-edited', 'Data Updated Successfully', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(785, 39, 1, 'unable-to-create', 'Unable to create, Please retry.', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(786, 39, 1, 'unable-to-update', 'Unable to update, Please retry.', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(787, 39, 1, 'refresh-data', 'Refresh Data', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(788, 39, 1, 'close', 'Close', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(789, 39, 1, 'done', 'Done', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(790, 39, 1, 'create-new-category', 'Create New Category', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(791, 39, 1, 'parent-category', 'Parent Category', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(792, 39, 1, 'category-color', 'Category Color', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(793, 39, 1, 'please-select-valid-color', 'Please select a valid color', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(794, 39, 1, 'edit-category', 'Update Category', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(795, 39, 1, 'not-authorized', 'You are not authorized!', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(796, 39, 1, 'no-permission-message', 'Ops!!, Look likes you don\'t have permissions to access this page.', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(797, 39, 1, 'enter-name', 'Enter Name', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(798, 39, 1, 'company-types', 'Company Types', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(799, 39, 1, 'manage-types', 'Manage Types', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(800, 39, 1, 'manage-packages', 'Manage Packages', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(801, 39, 1, 'edit-package', 'Edit Package', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(802, 39, 1, 'create-new-package', 'Create New Package', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(803, 39, 1, 'activity-classification', 'Activities Classification', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(804, 39, 1, 'failed', 'Failed!', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(805, 39, 1, 'item-deleted-failed', 'Delete Failed, Please type after some time.', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(806, 39, 1, 'password-changed', 'Password Changed Successfully', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(807, 39, 1, 'module', 'Module Master', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(808, 39, 1, 'user-types', 'User Type List', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(809, 39, 1, 'usertypes', 'All types users', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(810, 39, 1, 'manage-licenses', 'Manage All Licenses', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(811, 39, 1, 'request', 'Request', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(812, 39, 1, 'title', 'Title', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(813, 39, 1, 'description', 'Description', 1, NULL, '2022-09-21 15:42:55', '2022-09-21 15:42:55'),
(814, 39, 1, 'message', 'Message', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(815, 39, 1, 'password', 'Password', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(816, 39, 1, 'please_sign_in', 'Please sign-in to your account and start the adventure', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(817, 39, 1, 'forgot-password', 'Forgot Password?', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(818, 39, 1, 'remember-me', 'Remember Me', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(819, 39, 1, 'sign-in', 'Sign In', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(820, 39, 1, 'send-reset-link', 'Send Reset Link', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(821, 39, 1, 'forgot-password-message', 'Enter your email and we\'ll send you instructions to reset your password', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(822, 39, 1, 'back-to-login', 'Back to Login', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(823, 39, 1, 'reset-password', 'Reset Password', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(824, 39, 1, 'reset-password-message', 'Your new password must be different from previously used passwords', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(825, 39, 1, 'confirm-password', 'Confirm Password', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(826, 39, 1, 'role', 'Role', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(827, 39, 1, 'contact', 'Contact', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(828, 39, 1, 'enter-price', 'Enter Price', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(829, 39, 1, 'price', 'Price', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(830, 39, 1, 'package_id', 'Package', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(831, 39, 1, 'personal_number', 'Personal Number', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(832, 39, 1, 'organization_number', 'Organization Number', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(833, 39, 1, 'country_id', 'Country', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(834, 39, 1, 'zipCode', 'Zipcode', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:43:03'),
(835, 39, 1, 'full-address', 'Full Address', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(836, 39, 1, 'postal-code', 'Postal Area', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(837, 39, 1, 'address-details', 'Street Name', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(838, 39, 1, 'gender', 'Gender', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(839, 39, 1, 'male', 'Male', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(840, 39, 1, 'female', 'Female', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(841, 39, 1, 'licence_end_date', 'License End Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(842, 39, 1, 'joining_date', 'Joining Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(843, 39, 1, 'substitute', 'Substitute', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(844, 39, 1, 'regular', 'Regular', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(845, 39, 1, 'seasonal', 'Seasonal', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(846, 39, 1, 'is_file_required', 'File Required', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(847, 39, 1, 'licence_key', 'License Key', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(848, 39, 1, 'user_color', 'User Color', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(849, 39, 1, 'manage-roles', 'Manage Roles & Permissions', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(850, 39, 1, 'request-log', 'Request Log', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(851, 39, 1, 'requests', 'Requests', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(852, 39, 1, 'notifications', 'Notification', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(853, 39, 1, 'manage-module', 'Manage Module', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(854, 39, 1, 'contact_number', 'Phone', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(855, 39, 1, 'please-enter-valid-email', 'Please enter a valid email address', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(856, 39, 1, 'valid-password', 'Password must be 6 to 16 character, Please add at least one Capital letter, one Special character and one number', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(857, 39, 1, 'call-company', 'Click here to call the company', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(858, 39, 1, 'send-email', 'Click here to send email', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(859, 39, 1, 'joining-date', 'Joining Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(860, 39, 1, 'gov-id', 'Government Id', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(861, 39, 1, 'country', 'Country', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(862, 39, 1, 'currency', 'Currency', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(863, 39, 1, 'dial_code', 'Dial Code', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(864, 39, 1, 'currency_code', 'Currency Code', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(865, 39, 1, 'currency_symbol', 'Currency Symbol', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(866, 39, 1, 'country-list', 'Country List', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(867, 39, 1, 'salary', 'Salary', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(868, 39, 1, 'validity_in_days', 'Validity in days', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(869, 39, 1, 'number_of_patients', 'No of Patients', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(870, 39, 1, 'number_of_employees', 'No of employees', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(871, 39, 1, 'entry_mode', 'Mode', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(872, 39, 1, 'is_on_offer', 'Apply Offer?', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(873, 39, 1, 'discount_type', 'Discount', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(874, 39, 1, 'discount_value', 'Discount Value', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(875, 39, 1, 'session-expired', 'Your session is expired, Please login to continue..', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(876, 39, 1, 'login', 'Login', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(877, 39, 1, 'feedback', 'Feedback', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(878, 39, 1, 'Reports', 'Reports', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:43:01'),
(879, 39, 1, 'file', 'File Upload Required?', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(880, 39, 1, 'create-company', 'Create Company', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(881, 39, 1, 'update-company', 'Update Company', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(882, 39, 1, 'company-details', 'Company Details', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(883, 39, 1, 'company-name', 'Company Name', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(884, 39, 1, 'establishment_date', 'Est. Year', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(885, 39, 1, 'added-date', 'Added Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(886, 39, 1, 'discount_value_message', 'Discount value without % sign, 1 to 99.', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(887, 39, 1, 'discount_value_message_flat', 'Flat discount value', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(888, 39, 1, 'discount_value_feedback', 'Please add a valid discount value, discount value should be 1 to 99', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(889, 39, 1, 'discount_value_feedback_flat', 'Please add a valid discount value, discount value should be lower than price', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(890, 39, 1, 'price_message', 'Price must be under {{length}} digit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(891, 39, 1, 'price_feedback', 'Please enter a valid price, Price must be under {{length}} digit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(892, 39, 1, 'department', 'Department', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(893, 39, 1, 'manage-permissions', 'Manage Permissions', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(894, 39, 1, 'total-permissions-assigned', 'This role has {{count}} permissions.', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(895, 39, 1, 'total-packages-assigned', 'This package has {{count}} users.', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(896, 39, 1, 'total-company-patients', 'This company has {{patients}} patients and {{employees}} employees.', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(897, 39, 1, 'role-name', 'Role Name', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(898, 39, 1, 'add-role', 'Create Role', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(899, 39, 1, 'edit-role', 'Edit Role', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(900, 39, 1, 'reload-permissions', 'Reload Permission', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(901, 39, 1, 'permission-required', 'Please select permissions, Can\'t create role without permissions', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56');
INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(902, 39, 1, 'assign-permissions', 'Please assign permission to create the role, the users with this roles can access the related modules', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(903, 39, 1, 'roles-description', 'A role provides access to predefined menus and features depending on the assigned role to an administrator that can have access to what he needs.', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(904, 39, 1, 'user-management', 'User Management', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(905, 39, 1, 'create-user', 'Create User', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(906, 39, 1, 'update-user', 'Update User', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(907, 39, 1, 'user-details', 'User Details', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(908, 39, 1, 'discount', 'Discount', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(909, 39, 1, 'flat-discount', 'Flat', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(910, 39, 1, 'discount-type', 'Discount Type', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(911, 39, 1, 'percent-discount', 'Percentage', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(912, 39, 1, 'price_after_discount', 'Price after discount', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(913, 39, 1, 'package-description', 'A Package provides access to Employees and Patients management for a Company with restricted environment.', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(914, 39, 1, 'employee-desc', 'This employee has some limited access ', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(915, 39, 1, 'patient-desc', 'Patient can share disease', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(916, 39, 1, 'branch-name', 'Branch Name', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(917, 39, 1, 'sub-category', 'Sub Category', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(918, 39, 1, 'create-new-branch', 'Cerate New Branch', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(919, 39, 1, 'edit-branch', 'Update Branch', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(920, 39, 1, 'branch', 'Branch', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(921, 39, 1, 'branch-desc', 'This branch is used to creating a user profile', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(922, 39, 1, 'what-happened', 'What Happened', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(923, 39, 1, 'how-happened', 'How Happened', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(924, 39, 1, 'when-happened', 'When Happened', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(925, 39, 1, 'what-to-do', 'What To Do', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(926, 39, 1, 'goal', 'Goal', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(927, 39, 1, 'sub-goal', 'Sub Goal', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(928, 39, 1, 'plan-start-date', 'Plan Start Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(929, 39, 1, 'plan-start-time', 'Plan Start Time', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(930, 39, 1, 'remark', 'Remark', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(931, 39, 1, 'activity-message', 'Activity Message', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(932, 39, 1, 'company-dashboard', 'Company Dashboard', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(933, 39, 1, 'admin-dashboard', 'Admin Dashboard', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(934, 39, 1, 'ip-details', 'Implementations Details', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(935, 39, 1, 'create-ip', 'Implementations Create', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(936, 39, 1, 'create_ips', 'Create Implementation Plan', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(937, 39, 1, 'update-ip', 'Implementations Update', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(938, 39, 1, 'view-sub-categories', 'View Sub Categories', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(939, 39, 1, 'sub-categories', 'Sub Categories', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(940, 39, 1, 'manage-sub-categories', 'Manage Sub Categories', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(941, 39, 1, 'postal-area', 'Postal Area', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(942, 39, 1, 'start-date', 'Start Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(943, 39, 1, 'end-date', 'End Date', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(944, 39, 1, 'start-time', 'Start time', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(945, 39, 1, 'end-time', 'End Time', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(946, 39, 1, 'city', 'City', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(947, 39, 1, 'weekly-hour-alloted-by-govt', 'Weekly Hour', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(948, 39, 1, 'is_family_required', 'Family Member', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(949, 39, 1, 'disease-description', 'Disease Description', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(950, 39, 1, 'implementations', 'Implementations', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(951, 39, 1, 'when-it-started', 'When It Started', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(952, 39, 1, 'repetition-type', 'Repetition Type', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(953, 39, 1, 'repetition-days', 'Repetition Days', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(954, 39, 1, 'shift-color', 'Shift Color', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(955, 39, 1, 'all-employees', 'All Employees', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(956, 39, 1, 'all-patients', 'All Patients', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(957, 39, 1, 'all-nurse', 'All Nurse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(958, 39, 1, 'nurses', 'Nurses', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(959, 39, 1, 'followups', 'Follow Ups', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(960, 39, 1, 'shifts', 'Company Work Shift', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(961, 39, 1, 'companies-browse', 'Companies Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(962, 39, 1, 'companies-read', 'Companies Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(963, 39, 1, 'companies-Add', 'Companies Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(964, 39, 1, 'companies-edit', 'Companies Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(965, 39, 1, 'companies-delete', 'Companies Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(966, 39, 1, 'role-browse', 'Role Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(967, 39, 1, 'role-read', 'Role Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(968, 39, 1, 'role-add', 'Role Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(969, 39, 1, 'role-edit', 'Role Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(970, 39, 1, 'role-delete', 'Role Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(971, 39, 1, 'dashboard-browse', 'Dashboard Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(972, 39, 1, 'notifications-browse', 'Notifications Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(973, 39, 1, 'notifications-add', 'Notifications Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(974, 39, 1, 'notifications-edit', 'Notifications Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(975, 39, 1, 'notifications-delete', 'Notifications Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(976, 39, 1, 'requests-browse', 'Requests Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(977, 39, 1, 'requests-add', 'Requests Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(978, 39, 1, 'requests-read', 'Requests Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(979, 39, 1, 'requests-edit', 'Requests Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(980, 39, 1, 'requests-delete', 'Requests Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(981, 39, 1, 'users-browse', 'Users Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(982, 39, 1, 'users-read', 'Users Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(983, 39, 1, 'users-add', 'Users Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(984, 39, 1, 'users-edit', 'Users Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(985, 39, 1, 'users-delete', 'Users Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(986, 39, 1, 'activitiesCls-browse', 'ActivitiesCls Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(987, 39, 1, 'activitiesCls-read', 'ActivitiesCls Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(988, 39, 1, 'activitiesCls-add', 'ActivitiesCls Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(989, 39, 1, 'activitiesCls-edit', 'ActivitiesCls Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(990, 39, 1, 'activitiesCls-delete', 'ActivitiesCls Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(991, 39, 1, 'categories-browse', 'Categories Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(992, 39, 1, 'categories-read', 'Categories Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(993, 39, 1, 'categories-add', 'Categories Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(994, 39, 1, 'categories-edit', 'Categories Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(995, 39, 1, 'categories-delete', 'Categories Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(996, 39, 1, 'licenses-browse', 'Licenses Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(997, 39, 1, 'licenses-read', 'Licenses Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(998, 39, 1, 'licenses-add', 'Licenses Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(999, 39, 1, 'licenses-edit', 'Licenses Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1000, 39, 1, 'licenses-delete', 'Licenses Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1001, 39, 1, 'modules-browse', 'Modules Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1002, 39, 1, 'modules-read', 'Modules Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1003, 39, 1, 'modules-add', 'Modules Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1004, 39, 1, 'modules-edit', 'Modules Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1005, 39, 1, 'modules-delete', 'Modules Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1006, 39, 1, 'packages-browse', 'Packages Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1007, 39, 1, 'packages-read', 'Packages Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1008, 39, 1, 'packages-add', 'Packages Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1009, 39, 1, 'packages-edit', 'Packages Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1010, 39, 1, 'packages-delete', 'Packages Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1011, 39, 1, 'userType-add', 'User Type Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1012, 39, 1, 'userType-browse', 'User Type Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1013, 39, 1, 'userType-edit', 'User Type Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1014, 39, 1, 'userType-read', 'User Type Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1015, 39, 1, 'userType-delete', 'User Type Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1016, 39, 1, 'companyType-browse', 'Company Type Browser', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1017, 39, 1, 'companyType-read', 'Company Type Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1018, 39, 1, 'companyType-add', 'Company Type Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1019, 39, 1, 'companyType-edit', 'Company Type Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1020, 39, 1, 'companyType-delete', 'Company Type Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1021, 39, 1, 'settings-browse', 'Settings Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1022, 39, 1, 'settings-read', 'Settings Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1023, 39, 1, 'settings-add', 'Settings Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1024, 39, 1, 'settings-edit', 'Settings Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1025, 39, 1, 'settings-delete', 'Settings Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1026, 39, 1, 'employees-browse', 'Employee Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1027, 39, 1, 'employees-read', 'Employee Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1028, 39, 1, 'employees-add', 'Employee Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1029, 39, 1, 'employees-edit', 'Employee Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1030, 39, 1, 'employees-delete', 'Employee Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1031, 39, 1, 'patients-browse', 'Patients Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1032, 39, 1, 'patients-read', 'Patients Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1033, 39, 1, 'patients-add', 'Patients Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1034, 39, 1, 'patients-edit', 'Patients Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1035, 39, 1, 'patients-delete', 'Patients Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1036, 39, 1, 'nurses-browse', 'Nurses Browse', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1037, 39, 1, 'nurses-read', 'Nurses Read', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1038, 39, 1, 'nurses-add', 'Nurses Add', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1039, 39, 1, 'nurses-edit', 'Nurses Edit', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1040, 39, 1, 'nurses-delete', 'Nurses Delete', 1, NULL, '2022-09-21 15:42:56', '2022-09-21 15:42:56'),
(1041, 39, 1, 'departments-browse', 'Departments Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1042, 39, 1, 'departments-read', 'Departments Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1043, 39, 1, 'departments-add', 'Departments Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1044, 39, 1, 'departments-edit', 'Departments Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1045, 39, 1, 'departments-delete', 'Departments Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1046, 39, 1, 'activitySelf-browse', 'Activity Self Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1047, 39, 1, 'activitySelf-read', 'Activity Self Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1048, 39, 1, 'activitySelf-add', 'Activity Self Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1049, 39, 1, 'activitySelf-edit', 'Activity Self Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1050, 39, 1, 'activitySelf-delete', 'Activity Self Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1051, 39, 1, 'activityPatients-browse', 'Activity Patients Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1052, 39, 1, 'activityPatients-read', 'Activity Patients Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1053, 39, 1, 'activityPatients-add', 'Activity Patients Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1054, 39, 1, 'activityPatients-edit', 'Activity Patients Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1055, 39, 1, 'activityPatients-delete', 'Activity Patients Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1056, 39, 1, 'activityEmployees-browse', 'Activity Employee Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1057, 39, 1, 'activityEmployees-read', 'Activity Employee Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1058, 39, 1, 'activityEmployees-add', 'Activity Employee Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1059, 39, 1, 'activityEmployees-edit', 'Activity Employee Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1060, 39, 1, 'activityEmployees-delete', 'Activity Employee Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1061, 39, 1, 'journalSelf-browse', 'Journal Self Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1062, 39, 1, 'journalSelf-read', 'Journal Self Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1063, 39, 1, 'journalSelf-add', 'Journal Self Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1064, 39, 1, 'journalSelf-edit', 'Journal Self Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1065, 39, 1, 'journalSelf-delete', 'Journal Self Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1066, 39, 1, 'journalEmployees-browse', 'Journal Employee Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1067, 39, 1, 'journalEmployees-read', 'Journal Employee read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1068, 39, 1, 'journalEmployees-add', 'Journal Employee add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1069, 39, 1, 'journalEmployees-edit', 'Journal Employee edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1070, 39, 1, 'journalEmployees-delete', 'Journal Employee delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1071, 39, 1, 'journalPatients-add', 'Journal Patients Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1072, 39, 1, 'journalPatients-browse', 'Journal Patients Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1073, 39, 1, 'journalPatients-read', 'Journal Patients Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1074, 39, 1, 'journalPatients-edit', 'Journal Patients Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1075, 39, 1, 'journalPatients-delete', 'Journal Patients Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1076, 39, 1, 'deviationSelf-browse', 'Deviation Self Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1077, 39, 1, 'deviationSelf-read', 'Deviation Self Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1078, 39, 1, 'deviationSelf-add', 'Deviation Self Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1079, 39, 1, 'deviationSelf-edit', 'Deviation Self Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1080, 39, 1, 'deviationSelf-delete', 'Deviation Self Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1081, 39, 1, 'deviationPatients-browse', 'Deviation Patients Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1082, 39, 1, 'deviationPatients-read', 'Deviation Patients Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1083, 39, 1, 'deviationPatients-add', 'Deviation Patients Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1084, 39, 1, 'deviationPatients-edit', 'Deviation Patients Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1085, 39, 1, 'deviationPatients-delete', 'Deviation Patients Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1086, 39, 1, 'deviationEmployees-browse', 'Deviation Employee Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1087, 39, 1, 'deviationEmployees-read', 'Deviation Employee Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1088, 39, 1, 'deviationEmployees-add', 'Deviation Employee Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1089, 39, 1, 'deviationEmployees-edit', 'Deviation Employee Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1090, 39, 1, 'deviationEmployees-delete', 'Deviation Employee Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1091, 39, 1, 'scheduleSelf-browse', 'Schedule Self Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1092, 39, 1, 'scheduleSelf-read', 'Schedule Self Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1093, 39, 1, 'scheduleSelf-add', 'Schedule Self Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1094, 39, 1, 'scheduleSelf-edit', 'Schedule Self Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1095, 39, 1, 'scheduleSelf-delete', 'Schedule Self Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1096, 39, 1, 'scheduleEmployees-browse', 'Schedule Employee Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1097, 39, 1, 'scheduleEmployees-read', 'Schedule Employee Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1098, 39, 1, 'scheduleEmployees-add', 'Schedule Employee Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1099, 39, 1, 'scheduleEmployees-edit', 'Schedule Employee Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1100, 39, 1, 'scheduleEmployees-delete', 'Schedule Employee Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1101, 39, 1, 'schedulePatients-browse', 'Schedule Patient Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1102, 39, 1, 'schedulePatients-read', 'Schedule Patient Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1103, 39, 1, 'schedulePatients-add', 'Schedule Patient Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1104, 39, 1, 'schedulePatients-edit', 'Schedule Patient Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1105, 39, 1, 'schedulePatients-delete', 'Schedule Patient Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1106, 39, 1, 'patientFamily-browse', 'Patient Family Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1107, 39, 1, 'patientFamily-read', 'Patient Family Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1108, 39, 1, 'patientFamily-add', 'Patient Family Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1109, 39, 1, 'patientFamily-edit', 'Patient Family Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1110, 39, 1, 'patientFamily-delete', 'Patient Family Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1111, 39, 1, 'patientContactPersion-browse', 'Patient Contact Person Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1112, 39, 1, 'patientContactPersion-read', 'Patient Contact Person Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1113, 39, 1, 'patientContactPersion-add', 'Patient Contact Person Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1114, 39, 1, 'patientContactPersion-edit', 'Patient Contact Person Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1115, 39, 1, 'patientContactPersion-delete', 'Patient Contact Person Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1116, 39, 1, 'branch-browse', 'Branch Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1117, 39, 1, 'branch-read', 'Branch Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1118, 39, 1, 'branch-add', 'Branch Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1119, 39, 1, 'branch-edit', 'Branch Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1120, 39, 1, 'branch-delete', 'Branch Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1121, 39, 1, 'ipSelf-browse', 'Implementations Self Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1122, 39, 1, 'ipSelf-read', 'Implementations Self Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1123, 39, 1, 'ipSelf-add', 'Implementations Self Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1124, 39, 1, 'ipSelf-edit', 'Implementations Self Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1125, 39, 1, 'ipSelf-delete', 'Implementations Self Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1126, 39, 1, 'ipPatients-browse', 'Implementation Patient Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1127, 39, 1, 'ipPatients-Read', 'Implementation Patient Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1128, 39, 1, 'ipPatients-add', 'Implementation Patient Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1129, 39, 1, 'ipPatients-edit', 'Implementation Patient Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1130, 39, 1, 'ipPatients-delete', 'Implementation Patient Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1131, 39, 1, 'ipEmployees-browse', 'Implementation Employee Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1132, 39, 1, 'ipEmployees-read', 'Implementation Employee Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1133, 39, 1, 'ipEmployees-add', 'Implementation Employee Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1134, 39, 1, 'ipEmployees-edit', 'Implementation Employee Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1135, 39, 1, 'ipEmployees-delete', 'Implementation Employee Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1136, 39, 1, 'ipFollowUpsSelf-browse', 'Implements Followups Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1137, 39, 1, 'ipFollowUpsSelf-read', 'Implements Followups Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1138, 39, 1, 'ipFollowUpsSelf-add', 'Implements Followups Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1139, 39, 1, 'ipFollowUpsSelf-edit', 'Implements Followups Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1140, 39, 1, 'ipFollowUpsSelf-delete', 'Implements Followups Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1141, 39, 1, 'ipFollowUpsPatients-browse', 'Implements Followup Patients Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1142, 39, 1, 'ipFollowUpsPatients-read', 'Implements Followup Patients Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1143, 39, 1, 'ipFollowUpsPatients-add', 'Implements Followup Patients Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1144, 39, 1, 'ipFollowUpsPatients-edit', 'Implements Followup Patients Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1145, 39, 1, 'ipFollowUpsPatients-delete', 'Implements Followup Patients Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1146, 39, 1, 'ipFollowUpsEmployees-browse', 'Implements Followup Employee Browse', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1147, 39, 1, 'ipFollowUpsEmployees-read', 'Implements Followup Employee Read', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1148, 39, 1, 'ipFollowUpsEmployees-add', 'Implements Followup Employee Add', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1149, 39, 1, 'ipFollowUpsEmployees-edit', 'Implements Followup Employee Edit', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1150, 39, 1, 'ipFollowUpsEmployees-delete', 'Implements Followup Employee Delete', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1151, 39, 1, 'all-activity', 'All Activity', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1152, 39, 1, 'se-name', 'Se Name', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1153, 39, 1, 'group-name', 'Group Name', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1154, 39, 1, 'activity-link', 'Activity Link', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1155, 39, 1, 'activity-details', 'Activity Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1156, 39, 1, 'add-activity', 'Add Activity', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1157, 39, 1, 'update-activity', 'Update Activity', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1158, 39, 1, 'full-address-details', 'Full Address Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1159, 39, 1, 'govt-id', 'Government Id', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1160, 39, 1, 'patient-type', 'Patient Types', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1161, 39, 1, 'patient_category', 'Patient Category', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1162, 39, 1, 'new_employee_category', 'New Employee Category', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1163, 39, 1, 'employee_category', 'Employee Category', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1164, 39, 1, 'work-shift', 'Work Shift', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1165, 39, 1, 'followup-filter', 'FollowUp Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1166, 39, 1, 'activity-filter', 'Activity Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1167, 39, 1, 'dept-filter', 'Department Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1168, 39, 1, 'branch-filter', 'Branch Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1169, 39, 1, 'how-it-happened', 'How It Happened', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1170, 39, 1, 'when-it-end', 'When It End', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1171, 39, 1, 'created-at', 'Created At', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1172, 39, 1, 'is-family-member', 'Family Member', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1173, 39, 1, 'is-caretaker', 'Caretaker', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1174, 39, 1, 'is-contact-person', 'Contact Person', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1175, 39, 1, 'name', 'Full Name', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1176, 39, 1, 'contact-number', 'Phone', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1177, 39, 1, 'address', 'Address', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1178, 39, 1, 'subCategory', 'Sub Category', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:43:02'),
(1179, 39, 1, 'history', 'History', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1180, 39, 1, 'assign-employee', 'Assign Employee', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1181, 39, 1, 'reason-for-editing', 'Edit Reason', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1182, 39, 1, 'click-for-sign', 'Click for sign', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1183, 39, 1, 'followUp', 'Follow Ups', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:43:04'),
(1184, 39, 1, 'enter-details', 'Enter Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1185, 39, 1, 'persons', 'Persons', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1186, 39, 1, 'add-persons', 'Add Persons (Optionals)', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1187, 39, 1, 'have-question', 'Add Questions', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1188, 39, 1, 'questions', 'Questions', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1189, 39, 1, 'preview-details', 'View Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1190, 39, 1, 'review-submit-form', 'Review Details & Submit form', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1191, 39, 1, 'select-ip', 'Select IP', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1192, 39, 1, 'please-select-ip-plan', 'Please select IP Plan', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1193, 39, 1, 'please-select-branch', 'Please select Branch', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1194, 39, 1, 'select-branch', 'Select Branch', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1195, 39, 1, 'employee-filter', 'Employee Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1196, 39, 1, 'patient-filter', 'Patient Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1197, 39, 1, 'start-date-time', 'Start Date & Time', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1198, 39, 1, 'end-date-time', 'End Date & Time', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1199, 39, 1, 'workshift-status', 'WorkShit Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1200, 39, 1, 'followup-status', 'FollowUp Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1201, 39, 1, 'workshift-filter', 'Work-Shift Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1202, 39, 1, 'employee-status', 'Employee Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1203, 39, 1, 'patient-status', 'Patient Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1204, 39, 1, 'department-status', 'Department Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1205, 39, 1, 'category-status', 'Category Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1206, 39, 1, 'subcategory-status', 'Sub-Category Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1207, 39, 1, 'activity-status', 'Activity Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1208, 39, 1, 'category-filter', 'Category Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1209, 39, 1, 'subcategory-filter', 'SubCategory Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1210, 39, 1, 'permission-filter', 'Permission Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1211, 39, 1, 'ip-filter', 'Implementations Filter', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1212, 39, 1, 'ip-status', 'Implementations Status', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1213, 39, 1, 'no-record', 'No Record found', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1214, 39, 1, 'add-repetition', 'Add Repetition ?', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1215, 39, 1, 'enable-repetition', 'Enable Repetition', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1216, 39, 1, 'add-repetition-details', 'You can add repetition by day, week, month, year.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1217, 39, 1, 'every', 'Every', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1218, 39, 1, 'repetition-info', 'Enter from 1 - {{day}} along with repetition type.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1219, 39, 1, 'day', 'Day', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1220, 39, 1, 'week', 'Week', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1221, 39, 1, 'month', 'Month', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1222, 39, 1, 'year', 'Year', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1223, 39, 1, '1-99', '1-99', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1224, 39, 1, 'mon', 'mån', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1225, 39, 1, 'tus', 'tis', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1226, 39, 1, 'wed', 'ons', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1227, 39, 1, 'thu', 'tor', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1228, 39, 1, 'fri', 'fre', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1229, 39, 1, 'sat', 'lör', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1230, 39, 1, 'sun', 'sön', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1231, 39, 1, 'last_day', 'Last day of month', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1232, 39, 1, 'select-day', 'Select Day', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1233, 39, 1, 'please-select-some-question', 'Please select at least one question to continue.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1234, 39, 1, 'done-by', 'Done By', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1235, 39, 1, 'notify-to-users', 'Notify To Users', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1236, 39, 1, 'not-done-reason', 'Not Done Reason', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1237, 39, 1, 'not-applicable-reason', 'Not Applicable Reason', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1238, 39, 1, 'remind-before-start', 'Remind Before Start', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1239, 39, 1, 'remind-after-end', 'Remind After End', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1240, 39, 1, 'add-students-details', 'Add Student Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1241, 39, 1, 'add-working-details', 'Add Working Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1242, 39, 1, 'selected-agencies-hours', 'Selected Agencies Hours', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1243, 39, 1, 'institute-name', 'Institute Name', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1244, 39, 1, 'time-from', 'Time From', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1245, 39, 1, 'time-to', 'Time To', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1246, 39, 1, 'working-from', 'Working From', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1247, 39, 1, 'work-to', 'Work To', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1248, 39, 1, 'persons-assign', 'Persons Assign', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1249, 39, 1, 'role-id', 'Role', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1250, 39, 1, 'hours', 'Hours', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1251, 39, 1, 'person-type', 'Person Type', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1252, 39, 1, 'please-select-person-type', 'Please Select At Least One Person Type.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1253, 39, 1, 'personal-info', 'Personal Info', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1254, 39, 1, 'enter-personal-info', 'Enter Personal Info, All fields are required.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1255, 39, 1, 'add-person-optional', 'You can add multiple persons. Click on the add person button to add a person', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1256, 39, 1, 'Person', 'Person', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:43:04'),
(1257, 39, 1, 'added-persons', 'Added Persons', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1258, 39, 1, 'edit-persons-info', 'Add more persons or edit. Click on next to continue.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1259, 39, 1, 'please-select-question', 'Please Select at least one question.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1260, 39, 1, 'under-development', 'Under Development', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1261, 39, 1, 'personal-details', 'Personal Details', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1262, 39, 1, 'work-type', 'Work Type', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1263, 39, 1, 'red-word', 'Red Words', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1264, 39, 1, 'enter-paragraph', 'Enter Paragraph', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1265, 39, 1, 'paragraphs', 'Paragraphs', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1266, 39, 1, 'edit-paragraph', 'Edit Paragraph', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1267, 39, 1, 'create-paragraph', 'Create Paragraph', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1268, 39, 1, 'enter-word', 'Enter Word', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1269, 39, 1, 'hours-allocation', 'Hours Allocation', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1270, 39, 1, 'license-key', 'License Key', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1271, 39, 1, 'create-branch', 'Create Branch', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1272, 39, 1, 'question', 'Question', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1273, 39, 1, 'answer', 'Answer', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1274, 39, 1, 'remarks', 'Remarks', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1275, 39, 1, 'followups-history', 'Followups History', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1276, 39, 1, 'complete-followups', 'Complete Followups', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1277, 39, 1, 'edit-followups', 'Edit Followups', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1278, 39, 1, 'create-followups', 'Create Followups', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1279, 39, 1, 'edit-implementations', 'Update Implementations', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1280, 39, 1, 'activity-type', 'Activity Type', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1281, 39, 1, 'add-reminder', 'Add Reminders', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1282, 39, 1, 'add-emergency-reminder', 'Add Emergency Reminders', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1283, 39, 1, 'add-reminder-details', 'Add reminder details to get notifications.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1284, 39, 1, 'add-reminder-to-emergency', 'Add reminder to emergency numbers.', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1285, 39, 1, 'before-start', 'Before Start', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1286, 39, 1, 'after-end', 'After End', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1287, 39, 1, 'on-start', 'On Start', 1, NULL, '2022-09-21 15:42:57', '2022-09-21 15:42:57'),
(1288, 39, 1, 'time', 'Time', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1289, 39, 1, 'time-in-minute', 'Time in minutes', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1290, 39, 1, 'enable', 'Enable', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1291, 39, 1, 'time-limit-reminder', 'Please enter a valid time in minutes from 1 to 240', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1292, 39, 1, 'you-will-be-reminded-immediately', 'You will be reminded immediately', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1293, 39, 1, 'you-will-be-reminded-in', 'You will be reminded in', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1294, 39, 1, 'you-will-be-reminded-before', 'You will be reminded before', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1295, 39, 1, 'you-will-be-reminded-after', 'You will be reminded after', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1296, 39, 1, 'you-will-be-reminded-emergency', 'You will be reminded in emergency after', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1297, 39, 1, 'remind-on-emergency', 'To Emergency Number.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1298, 39, 1, 'on-device', 'Send as Notification', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1299, 39, 1, 'on-text', 'Send as Text Message', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1300, 39, 1, 'remind-on-device', 'Remind as Notification', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1301, 39, 1, 'remind-on-text', 'Remind as Text Message', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1302, 39, 1, 'add-urls', 'Add URLs', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1303, 39, 1, 'add-urls-details', 'You can add URLs', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1304, 39, 1, 'video-link', 'Video URL', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1305, 39, 1, 'address-link', 'Address Link', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1306, 39, 1, 'information-link', 'Information Link', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1307, 39, 1, 'comment', 'Comment', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1308, 39, 1, 'not-applicable', 'Not Applicable', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1309, 39, 1, 'tag', 'Tag', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1310, 39, 1, 'repeat', 'Repeat', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1311, 39, 1, 'share', 'Share', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1312, 39, 1, 'task-edit', 'Task Edit', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1313, 39, 1, 'add-task', 'Add Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1314, 39, 1, 'upcoming', 'Upcoming', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1315, 39, 1, 'pending', 'Pending', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1316, 39, 1, 'tasks-create', 'Create Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1317, 39, 1, 'update-task', 'Update Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1318, 39, 1, 'add-subtask', 'Add Subtask', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1319, 39, 1, 'subtask-create', 'Subtask Create', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1320, 39, 1, 'activity-task', 'Activity Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1321, 39, 1, 'subtask', 'Subtask', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1322, 39, 1, 'task', 'Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1323, 39, 1, 'add-attachment', 'Add Attachment', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1324, 39, 1, 'add-attachment-details', 'Add attachments', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1325, 39, 1, 'select-file', 'Select File', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1326, 39, 1, 'invalid-file-selected', 'Invalid File Selected', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1327, 39, 1, 'upload', 'Upload', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1328, 39, 1, 'drop-files-here-or-click', 'Drop files here or click', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1329, 39, 1, 'browse', 'Browse', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1330, 39, 1, 'through-your-machine', 'thorough your machine', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1331, 39, 1, 'max-upload-limit-reached', 'Max upload limit reached.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1332, 39, 1, 'max-upload-size', 'Max {{size}}', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1333, 39, 1, 'remove-all', 'Remove All', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1334, 39, 1, 'file-uploaded', 'File Uploaded Successfully', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1335, 39, 1, 'file-upload-failed', 'File Upload Failed', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1336, 39, 1, 'is-gaurdian', 'Guardian', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1337, 39, 1, 'users', 'Users', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1338, 39, 1, 'attached-fallowups', 'Attached Followups', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1339, 39, 1, 'view-fallowups', 'View Followups', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1340, 39, 1, 'repeat-reminder', 'Reminders', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1341, 39, 1, 'repeat-reminder-details', 'Add Reminders', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1342, 39, 1, 'url-attachments', 'Url & Attachments', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1343, 39, 1, 'url-attachments-details', 'Add URLs & Attachments', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1344, 39, 1, 'clear', 'Clear', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1345, 39, 1, 'repeat-activity', 'Repeat Activity', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1346, 39, 1, 'do-you-want-to-repeat-this-activity', 'Do you want to repeat this activity?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1347, 39, 1, 'assign-activity', 'Assign Activity', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1348, 39, 1, 'mark-as-not-applicable', 'Mark Not Applicable', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1349, 39, 1, 'mark-as-complete', 'Mark As Complete', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1350, 39, 1, 'mark-as-completed', 'Mark As Complete', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1351, 39, 1, 'add-new', 'Add New', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1352, 39, 1, 'add-address', 'Add Address', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1353, 39, 1, 'add-address-details', 'Add Address Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1354, 39, 1, 'patient', 'Patient', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1355, 39, 1, 'Employee', 'All Employees', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:43:04'),
(1356, 39, 1, 'add-details', 'Add Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1357, 39, 1, 'new-date', 'New Date', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1358, 39, 1, 'followup-date-time', 'Follow Up Date & Time', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1359, 39, 1, 'remove', 'Remove', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1360, 39, 1, 'alloted_hours', 'Alloted Hours', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1361, 39, 1, 'agency', 'Agency', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1362, 39, 1, 'internal-comment', 'Internal Comment', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1363, 39, 1, 'external-comment', 'External Comment', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58');
INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1364, 39, 1, 'other', 'Other', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1365, 39, 1, 'date-of-birth', 'Date of Birth', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1366, 39, 1, 'add-person', 'Add Person', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1367, 39, 1, 'custom-unique-id', 'Unique ID', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1368, 39, 1, 'hidden-patient', 'Secret Patient', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1369, 39, 1, 'save-create-ip', 'Save & Create IP', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1370, 39, 1, 'category', 'Category', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1371, 39, 1, 'num-assgin-act-count', 'Activity', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1372, 39, 1, 'assign-ip-count', 'IP', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1373, 39, 1, 'assign-task-count', 'Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1374, 39, 1, 'is-sms-enable', 'Is SMS Enable', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1375, 39, 1, 'sms-tooltip', 'Please enter a valid number', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1376, 39, 1, 'bankid_charges', 'BankID Charges', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1377, 39, 1, 'task-filter', 'Tasks Filter', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1378, 39, 1, 'is-enable-bankid', 'Is Enable BankID', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1379, 39, 1, 'bankid-charges', 'BankID Charges', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1380, 39, 1, 'sms-charges', 'SMS Charges', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1381, 39, 1, 'package-filter', 'Packages Filter', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1382, 39, 1, 'company-filter', 'Company Filter', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1383, 39, 1, 'phone-number', 'Phone Number', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1384, 39, 1, 'mail-subject', 'Mail Subject', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1385, 39, 1, 'mail-body', 'Mail Body', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1386, 39, 1, 'sms-body', 'SMS Body', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1387, 39, 1, 'edit-emailTemplate', 'Edit Email/Notification Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1388, 39, 1, 'create-emailTemplate', 'Create Email/Notification Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1389, 39, 1, 'view-emailTemplate', 'View Email Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1390, 39, 1, 'mail-sms-for', 'Mail/SMS For', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1391, 39, 1, 'mail-for', 'Mail For', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1392, 39, 1, 'sms-for', 'SMS For', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1393, 39, 1, 'notify-body', 'Notify Body', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1394, 39, 1, 'custom-attributes', 'Custom Attributes', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1395, 39, 1, 'edit-smsTemplate', 'Edit SMS Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1396, 39, 1, 'create-smsTemplate', 'Create SMS Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1397, 39, 1, 'view-smsTemplate', 'View SMS Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1398, 39, 1, 'is-guardian', 'Guardian', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1399, 39, 1, 'ip-modal', 'Implementation Plan', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1400, 39, 1, 'creating-plan-for', 'You are creating Implementation Plan for patient {{name}}', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1401, 39, 1, 'save-as-template', 'Save as template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1402, 39, 1, 'ip-persons', 'Persons', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1403, 39, 1, 'ip-persons-details', 'Enter Persons Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1404, 39, 1, 'select-patient', 'Select Patient', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1405, 39, 1, 'enter-person-details', 'Enter Patient Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1406, 39, 1, 'enter-ip-details', 'Enter Ip Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1407, 39, 1, 'ip-detail', 'Ip Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1408, 39, 1, 'all-details-filled', 'All Details Filled', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1409, 39, 1, 'partial-filled', 'Partial Filled', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1410, 39, 1, 'save-create-followup', 'Save & Create Followup', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1411, 39, 1, 'upcoming-and-done', 'Upcoming And Done', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1412, 39, 1, 'pending-and-not-applicable', 'Pending', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1413, 39, 1, 'select-category', 'Select Category', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1414, 39, 1, 'select-category-details', 'Enter Category & Subcategory', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1415, 39, 1, 'living-area', 'Livsområden', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1416, 39, 1, 'living-area-details', 'Enter Living Area (Livsområden) Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1417, 39, 1, 'ip-present-participating', 'Present & Participant', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1418, 39, 1, 'ip-present-participating-details', 'Who is Presented and Participating?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1419, 39, 1, 'ip-related-factors', 'Related Factors', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1420, 39, 1, 'ip-related-factors-details', 'Enter Related Factors', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1421, 39, 1, 'ip-overall-goal', 'Overall Goal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1422, 39, 1, 'ip-overall-goal-details', 'Enter Customer\'s Goal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1423, 39, 1, 'you-can-select-or-add-patient', 'You can create new or select patient from dropdown', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1424, 39, 1, 'save-create-task', 'Save & Create Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1425, 39, 1, 'save-create-activity', 'Save & Create Activity', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1426, 39, 1, 'save-and-create', 'Save And Create', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1427, 39, 1, 'select-category-and-subcategory', 'Select Category & Subcategory', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1428, 39, 1, 'select-category-and-subcategory-details', 'Please choose category and subcategory to start filling the details about the living plan', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1429, 39, 1, 'ip-treatment-working', 'Working and Treatment', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1430, 39, 1, 'ip-treatment-working-details', 'Enter treatment and working details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1431, 39, 1, 'select-template', 'Select Template', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1432, 39, 1, 'add-patient', 'Add Patient', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1433, 39, 1, 'save-patient', 'Save Patient', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1434, 39, 1, 'relative-caretaker', 'Relatives & Caretakers', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1435, 39, 1, 'relative-caretaker-details', 'Enter Persons Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1436, 39, 1, 'disability-details', 'Disability Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1437, 39, 1, 'disability-details-details', 'Enter Disability Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1438, 39, 1, 'studies-work', 'Studies & Work', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1439, 39, 1, 'studies-work-details', 'Enter Studies & Work Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1440, 39, 1, 'other-activities', 'Other Activities', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1441, 39, 1, 'other-activities-details', 'Enter Other Activities', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1442, 39, 1, 'decision', 'Decision', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1443, 39, 1, 'decision-details', 'Enter About Decision', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1444, 39, 1, 'save-person', 'Save Person', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1445, 39, 1, 'present-participated', 'Person Helped in creating IP?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1446, 39, 1, 'present-participated-details', 'Please Enter how Person is helped.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1447, 39, 1, 'presented-only', 'Presented', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1448, 39, 1, 'participated', 'Participated', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1449, 39, 1, 'how-participated', 'How Helped?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1450, 39, 1, 'aids', 'Aids Information', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1451, 39, 1, 'spacial-information', 'Special Information', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1452, 39, 1, 'company-phone', 'Company\'s Phone No.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1453, 39, 1, 'company-address', 'Company\'s Address', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1454, 39, 1, 'institute-phone', 'Institute\'s Phone No.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1455, 39, 1, 'institute-address', 'Institute\'s Address', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1456, 39, 1, 'institute-details', 'Institute Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1457, 39, 1, 'enter-institute-details', 'Enter Institute Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1458, 39, 1, 'enter-company-details', 'Enter Company Details', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1459, 39, 1, 'short-term-activity', 'Shot Term Activity', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1460, 39, 1, 'daily-activity', 'Daily Activity', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1461, 39, 1, 'no-of-hours', 'Hours', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1462, 39, 1, 'issuer', 'Issuer', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1463, 39, 1, 'Period', 'Period', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:43:03'),
(1464, 39, 1, 'is-other', 'Other', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1465, 39, 1, 'is_other_name', 'Name', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1466, 39, 1, 'presented', 'Presented', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1467, 39, 1, 'limitations', 'Limitations', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1468, 39, 1, 'no-restriction', 'Ingen Begränsning', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1469, 39, 1, 'slight-restriction', 'Lätt Begräsning', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1470, 39, 1, 'moderate-restriction', 'Måttlig Begränsning', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1471, 39, 1, 'large-limitation', 'Stor Begräsning', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1472, 39, 1, 'total-limitation', 'Total Begräsning', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1473, 39, 1, 'non-specific', 'Ej specifik', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1474, 39, 1, 'choose-goals', 'Choose Goal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1475, 39, 1, 'more-about-limitation', 'Write more details.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1476, 39, 1, 'how-support-should-be-given', 'How the support should be given?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1477, 39, 1, 'specify-when-the-support-is-to-be-given', 'Please Specify when the support is to be given to the patient', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1478, 39, 1, 'when-the-support-is-to-be-given', 'When the support is to be given?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1479, 39, 1, 'select-days', 'Select Days', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1480, 39, 1, 'how-many-times-a-day', 'How many times a day?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1481, 39, 1, 'when-during-the-day-the-support-is-to-be-given', 'When during the day the support is to be given?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1482, 39, 1, 'who-should-give-the-support', 'Who should give the support?', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1483, 39, 1, 'caretaker', 'Caretaker', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1484, 39, 1, 'staff', 'Staff', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1485, 39, 1, 'sub-goals', 'Sub Goals', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1486, 39, 1, 'maintain-ability', 'Maintain Ability', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1487, 39, 1, 'improve-ability', 'Improve Ability', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1488, 39, 1, 'develop-ability', 'Develop Ability', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1489, 39, 1, 'improve-participation', 'Improve Participation', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1490, 39, 1, 'manage-independently', 'Manage Independently', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1491, 39, 1, 'more-about-sub-goal', 'Write more details.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1492, 39, 1, 'comments', 'Comments', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1493, 39, 1, 'address-url', 'Address Link', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1494, 39, 1, 'video-url', 'Video Link', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1495, 39, 1, 'info-url', 'Information Link', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1496, 39, 1, 'body-functions', 'Body Functions', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1497, 39, 1, 'personal-factors', 'Personal Factors', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1498, 39, 1, 'health-conditions', 'Health Conditions', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1499, 39, 1, 'other-factors', 'Other Factors', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1500, 39, 1, 'overall-goal', 'Overall Goal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1501, 39, 1, 'overall-goal-details', 'Write more details.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1502, 39, 1, 'treatment', 'Treatment', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1503, 39, 1, 'working_method', 'Working Method', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1504, 39, 1, 'ip-file', 'Documents / Decisions', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1505, 39, 1, 'ip-file-details', 'Upload Documents', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1506, 39, 1, 'add-event', 'Add Event', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1507, 39, 1, 'goals', 'Goals', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1508, 39, 1, 'ip-for', 'Implementation Plan', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1509, 39, 1, 'write-patient-goal', 'Write Patient\'s Goal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1510, 39, 1, 'write-patient-sub-goal', 'Write Patient\'s Sub Goal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1511, 39, 1, 'please-enter-required-fields-denoted-with-start', 'Please enter required fields denoted with start/asterisk (*)', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1512, 39, 1, 'select-add-patient', 'Please select a patient or click on + button to create a new patient.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1513, 39, 1, 'write-goal-or-choose', 'Choose from suggested goals or write the user\'s own.', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1514, 39, 1, 'select-template-or-write-description', 'Write title of the implementation plan', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1515, 39, 1, 'enter-title', 'Enter Title', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1516, 39, 1, 'living-an-independent-life', 'Living an independent life', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1517, 39, 1, 'not-specified', 'Not Specified', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1518, 39, 1, 'good-living-conditions', 'Good Living Conditions', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1519, 39, 1, 'full-participation', 'Full Participation', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1520, 39, 1, 'living-like-others', 'Living Like Others', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1521, 39, 1, 'bank-name', 'Bank Name', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1522, 39, 1, 'account-number', 'Account Number', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1523, 39, 1, 'clearance-number', 'Clearance Number', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1524, 39, 1, 'create-bankDetail', 'Create Bank Detail', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1525, 39, 1, 'edit-bankDetail', 'Edit Bank Detail', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1526, 39, 1, 'view-bankDetail', 'View Bank Detail', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1527, 39, 1, 'menu', 'Menu', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1528, 39, 1, 'tag-input', 'Tag Input', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1529, 39, 1, 'action', 'Action', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1530, 39, 1, 'This-option-will-create-Journal', ' This option will create Journal', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1531, 39, 1, 'This-option-will-create-Deviation-&-Journal', ' This option will create Deviation & Journal ', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1532, 39, 1, 'implementation-plan', 'Implementation  Plan', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1533, 39, 1, 'completed-task', 'No. of Task completed', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1534, 39, 1, 'no-of-task', 'No. of Task', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1535, 39, 1, 'ip-completed', 'No. of IP completed', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1536, 39, 1, 'saved', 'Saved', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1537, 39, 1, 'completed-by-staff-not-on-time', 'Completed by staff not on time', 1, NULL, '2022-09-21 15:42:58', '2022-09-21 15:42:58'),
(1538, 39, 1, 'completed-by-staff-on-time', 'Completed by staff on time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1539, 39, 1, 'pending-task', 'No. of Task pending', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1540, 39, 1, 'ip-pending', 'No. of IP pending', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1541, 39, 1, 'no.-of-ip', 'No. of IP', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1542, 39, 1, 'no.-of-activity-completed', 'No. of activity completed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1543, 39, 1, 'no.-of-activity-pending', 'No. of activity pending', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1544, 39, 1, 'create-followup', 'Create Followup', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1545, 39, 1, 'create-activity', 'Create Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1546, 39, 1, 'create-task', 'Create Task', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1547, 39, 1, 'already-saved', 'This {{item}} is already saved.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1548, 39, 1, 'decision-document', 'Decisions & Documents', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1549, 39, 1, 'seasonal-or-regular', 'Seasonal or Regular?', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1550, 39, 1, 'date-&-time', 'Date/Time & Repeater', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1551, 39, 1, 'date-time-repeat-details', 'Add Date/Time & Repeater', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1552, 39, 1, 'add-date-&-time', 'Add Date and Time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1553, 39, 1, 'ip-patient', 'IP & Patient', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1554, 39, 1, 'select-ip-patient', 'Select IP and Patient', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1555, 39, 1, 'cat-sbcat', 'Patient & Category', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1556, 39, 1, 'select-cat-subcat', 'Select Category and SubCategory', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1557, 39, 1, 'emp-branch', 'Employee & Branch', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1558, 39, 1, 'select-emp-branch', 'Select Employee and Branch', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1559, 39, 1, 'title-disc', 'Title, Description & Others', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1560, 39, 1, 'fill-title-disc', 'Fill Title, Description and others', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1561, 39, 1, 'how-many-days', 'How many times per day?', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1562, 39, 1, 'activity-time', 'Activity Time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1563, 39, 1, 'enter-time', 'Enter start and end time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1564, 39, 1, 'calendar-timeline', 'Calendar & Timeline', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1565, 39, 1, 'patient-did-not-want', 'Patient did not want', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1566, 39, 1, 'not-done-by-employee', 'Not done by employee', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1567, 39, 1, 'completed-by-patient-itself', 'Completed by patient itself', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1568, 39, 1, 'completed-by-staff', 'Completed by staff', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1569, 39, 1, 'fake-email', 'Create Fake Email', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1570, 39, 1, 'request-subcategory-edit', 'Request Edit', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1571, 39, 1, 'request-edit', 'Request Text', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1572, 39, 1, 'add-task-details', 'You can add your task details here.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1573, 39, 1, 'risky-and-must', 'Risky And Must', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1574, 39, 1, 'create-custom-unique-id', 'Generate', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1575, 39, 1, 'persons-approval', 'Person\'s Approval', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1576, 39, 1, 'by-mobile-bank', 'Via Mobile Bank', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1577, 39, 1, 'by-digital-signature', 'Via Digital Signature', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1578, 39, 1, 'by-manual', 'Manually', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1579, 39, 1, 'mobile-bank', 'Mobile Bank', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1580, 39, 1, 'digital-signature', 'Digital Signature', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1581, 39, 1, 'manual', 'Manual', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1582, 39, 1, 'task-time', 'Task Time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1583, 39, 1, 'date-time', 'Date & Time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1584, 39, 1, 'add-date-time', 'Add Date & time', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1585, 39, 1, 'attachments', 'Attachments', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1586, 39, 1, 'attachments-details', 'Add Attachments Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1587, 39, 1, 'send', 'Send', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1588, 39, 1, 'personal-number', 'Personal Number', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1589, 39, 1, 'persons-implementation', 'Patient IP', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1590, 39, 1, 'request-for-approval', 'Request For Approval', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1591, 39, 1, 'manual-approval', 'Manual Approval', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1592, 39, 1, 'request-sent', 'Request Sent', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1593, 39, 1, 'bank-id', 'Bank Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1594, 39, 1, 'assign-by', 'Assign By', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1595, 39, 1, 'detail-mode', 'View More', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1596, 39, 1, 'user-type', 'User Type', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1597, 39, 1, 'company-type', 'Company Type', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1598, 39, 1, 'agency-details', 'Agency Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1599, 39, 1, 'r-description', 'R Description', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1600, 39, 1, 'r', 'R', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1601, 39, 1, 'priority', 'Priority', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1602, 39, 1, 'request-ip', 'Request IP', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1603, 39, 1, 'do-you-want-to-approve-this', 'Do You Want To Approve This', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1604, 39, 1, 'approve-this', 'Approve', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1605, 39, 1, 'approved', 'Approved', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1606, 39, 1, 'not-approved', 'Not Approve', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1607, 39, 1, 'reason', 'Reason', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1608, 39, 1, 'requested-to', 'Requested To', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1609, 39, 1, 'approve-type', 'Approve Type', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1610, 39, 1, 'by-bank-id', 'By Bank Id', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1611, 39, 1, 'digital', 'Digital', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1612, 39, 1, 'manual-or-bank-id', 'Manual Or Bank Id', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1613, 39, 1, 'print', 'Print', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1614, 39, 1, 'ip-name', 'IP Name', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1615, 39, 1, 'followups-details', 'FollowUp Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1616, 39, 1, 'reason-for-edit', 'Reason for Edit', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1617, 39, 1, 'document', 'Document', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1618, 39, 1, 'comments-and-replies', 'Comments and Replies', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1619, 39, 1, 'tap-to-reply', 'Tap to Reply', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1620, 39, 1, 'assignment_date', 'Assignment Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1621, 39, 1, 'assignment_day', 'Assignment Day', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1622, 39, 1, 'assign-by-user', 'Assign by User', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1623, 39, 1, 'activity-information', 'Activity Information', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1624, 39, 1, 'patient-information', 'Information', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1625, 39, 1, 'classes-timing', 'Class Timing', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1626, 39, 1, 'from', 'From', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1627, 39, 1, 'to', 'To', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1628, 39, 1, 'unique-id', 'Unique Id', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1629, 39, 1, 'about', 'About', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1630, 39, 1, 'agencies', 'Agencies', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1631, 39, 1, 'update', 'Update', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1632, 39, 1, 'institute', 'Institute', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1633, 39, 1, 'timing', 'Timing', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1634, 39, 1, 'years-old', 'Years Old', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1635, 39, 1, 'year-old', 'Year Old', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1636, 39, 1, 'months-old', 'Months Old', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1637, 39, 1, 'month-old', 'Month Old', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1638, 39, 1, 'days-old', 'Days Old', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1639, 39, 1, 'day-old', 'Day Old', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1640, 39, 1, 'secret-patient', 'Secret Patient', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1641, 39, 1, 'info', 'Info', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1642, 39, 1, 'relatives', 'Relatives', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1643, 39, 1, 'diseases', 'Diseases', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1644, 39, 1, 'work-study', 'Work/Study', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1645, 39, 1, 'decisions', 'Decisions', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1646, 39, 1, 'patient-id', 'Patient Id', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1647, 39, 1, 'main-branch', 'Main Branch', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1648, 39, 1, 'ip-created', 'IP Created', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1649, 39, 1, 'ip-not-created', 'IP Not Created', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1650, 39, 1, 'incomplete-details', 'Incomplete Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1651, 39, 1, 'patient-details', 'Patient Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1652, 39, 1, 'updated-at', 'Updated At', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1653, 39, 1, 'overall-goal-detail', 'Overall Goal Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1654, 39, 1, 'approvals', 'Approvals', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1655, 39, 1, 'approved-by', 'Approved By', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1656, 39, 1, 'approved-date', 'Approved Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1657, 39, 1, 'Implementations-details', 'Implementations Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1658, 39, 1, 'establishment-year', 'Establishment Year', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1659, 39, 1, 'zip-code', 'Zip Code', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1660, 39, 1, 'branch-details', 'Branch Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1661, 39, 1, 'contact-person', 'Contact Person', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1662, 39, 1, 'plan', 'Plan', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1663, 39, 1, 'deviation', 'Deviation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1664, 39, 1, 'journal', 'Journal', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1665, 39, 1, 'module-inactive', 'Module Inactive', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1666, 39, 1, 'ip-approved', 'Plan Approved', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1667, 39, 1, 'ip-not-approved', 'Plan Not Approved Yet.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1668, 39, 1, 'incomplete', 'Incomplete', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1669, 39, 1, 'complete', 'Complete', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1670, 39, 1, 'limitation', 'Limitation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1671, 39, 1, 'limitation-info', 'Limitation Info', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1672, 39, 1, 'goal-info', 'Goal Info', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1673, 39, 1, 'sub-goal-info', 'Sub Goal Info', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1674, 39, 1, 'overall-goal-info', 'Overall Goal Info', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1675, 39, 1, 'who-will-give-support', 'Who\'ll Give Support', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1676, 39, 1, 'how-will-support', 'How Support will be given', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1677, 39, 1, 'working', 'Working', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1678, 39, 1, 'working-info', 'Working Information', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1679, 39, 1, 'treatment-info', 'Treatment Information', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1680, 39, 1, 'uploaded-files', 'Uploaded Files', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1681, 39, 1, 'upload-your-files', 'Upload your files here.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1682, 39, 1, 'please-be-careful', 'Please be careful', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1683, 39, 1, 'this-activity-is-compulsory', 'This activity is compulsory', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1684, 39, 1, 'this-task-is-compulsory', 'This task is compulsory', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1685, 39, 1, 'view-ip', 'View IP Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1686, 39, 1, 'view-comments', 'View Comments', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1687, 39, 1, 'add-tag', 'Add Tag', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1688, 39, 1, 'complete-activity', 'Complete Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1689, 39, 1, 'select-witness', 'Select Witness', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1690, 39, 1, 'approve-ip', 'Approve IP', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1691, 39, 1, 'no', 'No', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1692, 39, 1, 'is-public', 'Is Public', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1693, 39, 1, 'trashed-activity', 'Trashed Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1694, 39, 1, 'all-trashed-activity', 'All Trashed Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1695, 39, 1, 'permanent-delete', 'Permanent Delete', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1696, 39, 1, 'restore-activity', 'Restore Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1697, 39, 1, 'file-title', 'File Title', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1698, 39, 1, 'uploaded-by', 'Uploaded By', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1699, 39, 1, 'item-restore', 'Item Restored Successfully', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1700, 39, 1, 'restore', 'Restore', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1701, 39, 1, 'item-restore-failed', 'Item restore failed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1702, 39, 1, 'restore-this', 'Restore this', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1703, 39, 1, 'validity', 'Validity (in days)', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1704, 39, 1, 'view-activity-comments', 'View Activity Comments', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1705, 39, 1, 'graphical-data', 'Graphical Representation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1706, 39, 1, 'activity-count', 'Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1707, 39, 1, 'ip-count', 'IP', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1708, 39, 1, 'task-count', 'Task', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1709, 39, 1, 'patient-detail', 'Patient Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1710, 39, 1, 'contact-person-name', 'Contact Person Name', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1711, 39, 1, 'establishment_year', 'Establishment Year', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1712, 39, 1, 'license-status', 'License Status', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1713, 39, 1, 'subscription', 'Subscription', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1714, 39, 1, 'package-name', 'Package Name', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1715, 39, 1, 'activity-moved-to-not-applicable', 'Activity moved to not applicable.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1716, 39, 1, 'activity-moved-to-done', 'Activity moved to done.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1717, 39, 1, 'add-this-activity-to-trash', 'Add this activity to trash.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1718, 39, 1, 'no-comments-yet', 'No Comments.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1719, 39, 1, 'no-activities', 'No Activities', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1720, 39, 1, 'upcoming-activity', 'Upcoming', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1721, 39, 1, 'done-activity', 'Completed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1722, 39, 1, 'not-done-activity', 'Incomplete', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1723, 39, 1, 'not-applicable-activity', 'Not Applicable', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1724, 39, 1, 'journal-activity', 'Journal', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1725, 39, 1, 'deviation-activity', 'Deviation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1726, 39, 1, 'ips', 'Patient Plan', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1727, 39, 1, 'organization-number', 'Organization Number', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1728, 39, 1, 'devitation', 'Deviation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1729, 39, 1, 'dev-form', 'Deviation Details', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1730, 39, 1, 'customer', 'Customer', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1731, 39, 1, 'add-description', 'Add Description', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1732, 39, 1, 'add-description-details', 'Please add your description here.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1733, 39, 1, 'add-other-details', 'Add other details here.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1734, 39, 1, 'branch-patient', 'Branch & Patient', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1735, 39, 1, 'related-factor', 'Related Factor', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1736, 39, 1, 'deviation-details', 'Deviation Detail', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1737, 39, 1, 'investigation', 'Investigation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1738, 39, 1, 'add-investigation-details', 'Add Investigation Details.', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1739, 39, 1, 'edited_date', 'Edited Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1740, 39, 1, 'receipt-no', 'Receipt No', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1741, 39, 1, 'patient-cashier', 'Patient Cashier', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1742, 39, 1, 'amount', 'Amount', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1743, 39, 1, 'in', 'In', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1744, 39, 1, 'out', 'Out', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1745, 39, 1, 'add-patient-cashier', 'Add Patient Cashier', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1746, 39, 1, 'is-signed', 'Is Signed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1747, 39, 1, 'is-completed', 'Is Completed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1748, 39, 1, 'deviation-filter', 'Deviation Filter', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1749, 39, 1, 'is-secret', 'Is Secret', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1750, 39, 1, 'date', 'Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1751, 39, 1, 'assigned-to', 'Assigned To', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1752, 39, 1, 'witnessed_by', 'Witnessed By', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1753, 39, 1, 'first-name', 'First Name', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1754, 39, 1, 'last-name', 'Last Name', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1755, 39, 1, 'more-witness', 'More Witness', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1756, 39, 1, 'edit-history', 'Change Logs', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1757, 39, 1, 'download', 'Download', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1758, 39, 1, 'sign-ip', 'Sign IP?', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1759, 39, 1, 'ip-sign-failed', 'IP Sign Failed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1760, 39, 1, 'ip-sign-successfully', 'IP Signed Successfully', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1761, 39, 1, 'sign-failed', 'Sign Failed', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1762, 39, 1, 'deviation-date', 'Deviation Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1763, 39, 1, 'completed-date', 'Completed Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1764, 39, 1, 'save-print', 'Save & Print', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1765, 39, 1, 'created-date', 'Created Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1766, 39, 1, 'incident-date', 'Incident Date', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1767, 39, 1, 'type', 'Type', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1768, 39, 1, 'active-this-journal', 'Active this journal', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1769, 39, 1, 'activity-calender', 'Activity Calender', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1770, 39, 1, 'with-deviation', 'With Deviation', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1771, 39, 1, 'with-journal', 'With Journal', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1772, 39, 1, 'linked-activity', 'Linked Activity', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1773, 39, 1, 'activity-stats', 'Activity Stats', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1774, 39, 1, 'activity-report', 'Activity Report', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1775, 39, 1, 'total-journal', 'Total Journal', 1, NULL, '2022-09-21 15:42:59', '2022-09-21 15:42:59'),
(1776, 39, 1, 'stats', 'Stats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1777, 39, 1, 'total-deviation', 'Total Deviation', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1778, 39, 1, 'total_completed', 'Total Completed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1779, 39, 1, 'total_signed', 'Total Signed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1780, 39, 1, 'with_activity', 'With Activity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1781, 39, 1, 'without_activity', 'Without Activity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1782, 39, 1, 'import-patient', 'Import Patient', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1783, 39, 1, 'do-you-want-to-active-this-journal', 'Do You Want To Active This Journal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1784, 39, 1, 'completed-on', 'Completed On', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1785, 39, 1, 'completed-by', 'Completed By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1786, 39, 1, 'approve', 'Approve', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1787, 39, 1, 'click-yes-to-approve-ip', 'Click Yes To Approve IP', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1788, 39, 1, 'not-completed', 'Not Completed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1789, 39, 1, 'completed-date-&-completed-by', 'Completed Date & Completed By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1790, 39, 1, 'complete-this-deviation     ', 'Do you want to Sign it.', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:05'),
(1791, 39, 1, 'dataset-total-activity', 'Total Activity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1792, 39, 1, 'dataset-total-completed-by-patient-itself', 'Completed By Patient Itself', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1793, 39, 1, 'dataset-total-completed-by-staff-not-0n-time', 'Completed By Staff Not On TIme', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1794, 39, 1, 'dataset-total-completed-by-staff-on-time', 'Completed By Staff On Time', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1795, 39, 1, 'dataset-total-compulsory', 'Compulsory', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1796, 39, 1, 'dataset-total-not-done-by-employee', 'Not Done By Employee', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1797, 39, 1, 'dataset-total-patient-did-not-want', 'Patient Did Not Want', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1798, 39, 1, 'deviation-completed', 'Deviation is Signed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1799, 39, 1, 'completed-failed', 'Failed to complete this action', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1800, 39, 1, 'completed-at', 'Completed At', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1801, 39, 1, '&-completed-by', '& Completed By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1802, 39, 1, 'with_or_without_activity', 'With Activity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1803, 39, 1, 'action-stats', 'Action Stats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1804, 39, 1, 'patient-stats', 'Patient Stats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1805, 39, 1, 'read-all-notifications', 'Read All Notifications', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1806, 39, 1, 'ip-with', 'IP With', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1807, 39, 1, 'not-approve', 'Not Approved', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1808, 39, 1, 'no-more-data', 'No More Data', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1809, 39, 1, 'no-document', 'No document available', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1810, 39, 1, 'latest-entry', 'Latest Entry', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1811, 39, 1, 'task-complete', 'Complete Task', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1812, 39, 1, 'mark-all-read', 'Mark All as Read', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1813, 39, 1, 'journal-stats', 'Journal Stats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1814, 39, 1, 'goal-sub-goal-stats', 'Goal/SubGoal Report', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1815, 39, 1, 'dataset-total-goal', 'Total Goal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1816, 39, 1, 'dataset-total-sub-goal', 'Total SubGoal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1817, 39, 1, 'assigned-employee', 'Assigned Employee', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1818, 39, 1, 'marked-complete-by', 'Marked Completed By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1819, 39, 1, 'marked-incomplete-by', 'Marked Not Done By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1820, 39, 1, 'marked-not-applicable-by', 'Marked Not Applicable By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1821, 39, 1, 'total-completed', 'Total Completed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1822, 39, 1, 'today', 'Today', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1823, 39, 1, 'task-for', 'Task For', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1824, 39, 1, 'Please-download-this-Excel-file-and-fill-all-the-details-accordingly.-After-that-you-can-upload-the-file-to-import-your-items.', 'Please download this Excel file and fill all the details accordingly. After that you can upload the file to import your items.', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1825, 39, 1, 'download-sample-file', 'Download Sample File', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1826, 39, 1, 'send-request', 'Send Request', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1827, 39, 1, 'no-notifications', 'No Notification', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1828, 39, 1, 'view-all-notifications', 'View All Notifications', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00');
INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1829, 39, 1, 'edit-journal', 'Edit Journal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1830, 39, 1, 'branch-suggeston', 'Branch Suggestion', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1831, 39, 1, 'adminEmployee', 'Admin Employee', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1832, 39, 1, 'ip-not-assigned', 'No IP Assigned', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1833, 39, 1, 'deviation-stats', 'Deviation Stats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1834, 39, 1, 'no-deviation', 'No deviation is available here', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1835, 39, 1, 'please-wait-setting-company', 'Please Wait. We are setting database for company. It can take up to 2 min.', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1836, 39, 1, 'patient-date', 'Patient & Date', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1837, 39, 1, 'event-description', 'Event Description', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1838, 39, 1, 'immediate-action', 'Immediate Action', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1839, 39, 1, 'probable-cause-of-the-incident', 'Probable Cause Of The Incident', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1840, 39, 1, 'click-to-preview', 'Click to preview image.', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1841, 39, 1, 'edited-date', 'Edited Date', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1842, 39, 1, 'edited-by', 'Edited By', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1843, 39, 1, 'today-journal', 'Today\'s Journal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1844, 39, 1, 'today-deviation', 'Today\'s Deviations', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1845, 39, 1, 'no-action-performed', 'No Action Performed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1846, 39, 1, 'no-activity-today', 'No Activities For Today!', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1847, 39, 1, 'showing-upcoming-activities', 'Showing Upcoming Activities', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1848, 39, 1, 'overall-chart', 'Overall Chart', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1849, 39, 1, 'dashboard-menu', 'Dashboard', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1850, 39, 1, 'activity-menu', 'Activities', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1851, 39, 1, 'journal-menu', 'Journals', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1852, 39, 1, 'deviation-menu', 'Deviations', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1853, 39, 1, 'user-management-menu', 'User Management', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1854, 39, 1, 'master-menu', 'Masters', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1855, 39, 1, 'setting-menu', 'Settings', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1856, 39, 1, 'report-feedback-menu', 'Reports & Feedbacks', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1857, 39, 1, 'message-menu', 'Notifications & Chats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1858, 39, 1, 'bookmarks', 'Bookmarks', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1859, 39, 1, 'employee-dashboard', 'Employee Dashboard', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1860, 39, 1, 'choose-color', 'Choose Color', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1861, 39, 1, 'minor-child', 'Minor Child', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1862, 39, 1, 'another-activity', 'Another Activity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1863, 39, 1, 'add-more-decision', 'Add More Decision', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1864, 39, 1, 'another-activity-contact-person', 'Another Activity Contact Person', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1865, 39, 1, 'weekdays', 'Weekdays', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1866, 39, 1, 'decision-date', 'Decision Date', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1867, 39, 1, 'registration-details', 'Registration Details', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1868, 39, 1, 'registration-date', 'Registration Date', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1869, 39, 1, 'is-risk', 'Risky', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1870, 39, 1, 'urlmsg', 'URL Format Is Not Correct', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1871, 39, 1, 'action-date', 'Action Date', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1872, 39, 1, 'data-is-not-available', 'Data Is Not Available', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1873, 39, 1, 'branch-id', 'Branch Id', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1874, 39, 1, 'habitats', 'Livsområden', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1875, 39, 1, 'choose-patient', 'Choose Patient', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1876, 39, 1, 'or', 'Or', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1877, 39, 1, 'back', 'Back', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1878, 39, 1, 'can-not-add-multiple-ip-message', 'Can Not Add Multiple Ip Message', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1879, 39, 1, 'visible-creating-IP-msg', 'Only persons those were present at the time of creating Implementation Plan will be visible below', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1880, 39, 1, 'save-and-skip', 'Save & Skip', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1881, 39, 1, 'patientAndhabitatsDetails', 'Patient And Livsområden Details', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1882, 39, 1, 'selected-sub-goal', 'Selected Sub Goal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1883, 39, 1, 'new-question', 'New Question', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1884, 39, 1, 'question-required', 'Question Required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1885, 39, 1, 'select-questions', 'Select Questions', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1886, 39, 1, 'followup-for', 'Follow up for', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1887, 39, 1, 'add-more-follow-up-dates', 'Add more follow up dates', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1888, 39, 1, 'journal-filter', 'Journal Filter', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1889, 39, 1, 'apply-filter', 'Apply Filter', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1890, 39, 1, 'reset', 'Reset', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1891, 39, 1, 'old-description', 'Previous Description', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1892, 39, 1, 'add-journal', 'Add Journal', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1893, 39, 1, 'all-data', 'All Data', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1894, 39, 1, 'want-to-create-complete-deviation', 'Do you want to complete this deviation', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1895, 39, 1, 'want-to-create-sign-deviation', 'Do you want to sign this deviation', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1896, 39, 1, 'add-deviation', 'Add Deviation', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1897, 39, 1, 'suggestion', 'Suggestion', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1898, 39, 1, 'severity', 'Severity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1899, 39, 1, 'further-investigation', 'Further Investigation', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1900, 39, 1, 'add-contact-person', 'Add Contact Person', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1901, 39, 1, 'custom', 'Custom', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1902, 39, 1, 'edit_package', 'Edit New Package', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1903, 39, 1, 'add_package', 'Add New Package', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1904, 39, 1, 'flat_discount', 'Flat Discount', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1905, 39, 1, 'add_price_first', 'Please add price first', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1906, 39, 1, 'package_details', 'Package Details', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1907, 39, 1, 'edit_package_details', 'Edit Package Details', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1908, 39, 1, 'name_required', 'Please enter name', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1909, 39, 1, 'price_required', 'Please enter price', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1910, 39, 1, 'type_of_discount_required', 'Type of discount required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1911, 39, 1, 'discounted_price_required', 'Discounted price required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1912, 39, 1, 'validity_in_days_required', 'Validity required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1913, 39, 1, 'number_of_patients_required', 'Number of patients required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1914, 39, 1, 'number_of_employees_required', 'Number of patients required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1915, 39, 1, 'flat_discount_required', 'Flat discount required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1916, 39, 1, 'discounted_price_can_not_lesser', 'Price after discount can not be lesser than or equal to 0', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1917, 39, 1, 'package_saved_successfully', 'Package saved successfully', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1918, 39, 1, 'package_edited_successfully', 'Package edited successfully', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1919, 39, 1, 'off', 'Off', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1920, 39, 1, 'flat', 'Flat', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1921, 39, 1, 'bank_id', 'Bank ID', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1922, 39, 1, 'days_validity', 'Days Validity', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1923, 39, 1, 'sms', 'SMS', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1924, 39, 1, 'disable', 'Disable', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1925, 39, 1, 'free', 'FREE', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1926, 39, 1, 'this_package_has', 'This package has', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1927, 39, 1, 'risk-description', 'Risk Description', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1928, 39, 1, 'time-and-repetition', 'Time And Repetition', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1929, 39, 1, 'word-listing', 'Word Listing', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1930, 39, 1, 'read-more', 'Read More', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1931, 39, 1, 'this-role-has', 'This role has', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1932, 39, 1, 'new-password', 'New Password', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1933, 39, 1, 'submit', 'Submit', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1934, 39, 1, 'edit-profile', 'Edit Profile', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1935, 39, 1, 'get-otp', 'Get OTP', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1936, 39, 1, 'enter-OTP', 'Enter OTP', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1937, 39, 1, 'per', 'per', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1938, 39, 1, 'student', 'Student', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1939, 39, 1, 'old_age', 'Old age', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1940, 39, 1, 'creating-company', 'Creating Company', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1941, 39, 1, 'do-not-press-back-button', 'Do not press back button or close app', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1942, 39, 1, 'add-license', 'Add License', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1943, 39, 1, 'expire_date', 'Expire Date', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1944, 39, 1, 'license-key-details', 'License Key Details', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1945, 39, 1, 'valid_date_range_for_selected_IP_is', 'Valid date range for selected IP is', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1946, 39, 1, 'assign-licence-key', 'Assign License Key', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1947, 39, 1, 'not-used', 'Not used', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1948, 39, 1, 'used', 'Used', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1949, 39, 1, 'notification_list', 'Notification', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1950, 39, 1, 'module_inactive', 'Module Inactive', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1951, 39, 1, 'contact-admin', 'Please contact Admin to enable this module', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1952, 39, 1, 'Loading', 'Loading', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1953, 39, 1, 'enable_range', 'Enable Date Range', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1954, 39, 1, 'create_schedule', 'Create Schedule', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1955, 39, 1, 'workshifts', 'Work Shifts', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1956, 39, 1, 'DS', 'DS', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1957, 39, 1, 'ES', 'ES', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1958, 39, 1, 'NS', 'NS', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1959, 39, 1, 'Day_Shift', 'Day Shift', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1960, 39, 1, 'Evening_Shift', 'Evening Shift', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1961, 39, 1, 'Night_Shift', 'Night Shift', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1962, 39, 1, 'add_schedule', 'Add Schedule', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1963, 39, 1, 'assigned_to', 'Assigned To', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1964, 39, 1, 'shift', 'Shift', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1965, 39, 1, 'assigned-module', 'Assigned Module', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1966, 39, 1, 'no-employee-assigned', 'No Employee Assigned', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1967, 39, 1, 'company-stats', 'Company Stats', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1968, 39, 1, 'total-patients', 'Total Patients', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1969, 39, 1, 'total-employee', 'Total Employee', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1970, 39, 1, 'total-branches', 'Total Branches', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1971, 39, 1, 'total-department', 'Total Department', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1972, 39, 1, 'total-module', 'Total Modules', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1973, 39, 1, 'total-followups', 'Total Followups', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1974, 39, 1, 'total-task', 'Total Task', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1975, 39, 1, 'workShift', 'Work Shift', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1976, 39, 1, 'selected_dates', 'Selected Dates', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1977, 39, 1, 'shift_required', 'Shift Required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1978, 39, 1, 'ov', 'Obe', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1979, 39, 1, 'every_week', 'Every Week', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1980, 39, 1, 'create_Ov', 'Create OBE', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1981, 39, 1, 'max_week_count', 'max week count =', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1982, 39, 1, 'workShiftNameRequired', 'Work Shift Name is required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1983, 39, 1, 'shiftName', 'Shift Name', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1984, 39, 1, 'addWorkShift', 'Add Work Shift', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1985, 39, 1, 'municipalName', 'Municipal Name', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1986, 39, 1, 'hourPerWeek', 'Allowed Hour Per Week', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1987, 39, 1, 'workPercentage', 'Work Percentage', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1988, 39, 1, 'contractFixed', 'Contract Fixed', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1989, 39, 1, 'contractType', 'Contract Type', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1990, 39, 1, 'contractHourly', 'Contract Hourly', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1991, 39, 1, 'required', 'Required', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1992, 39, 1, 'Leaves', 'Leaves', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1993, 39, 1, 'appliyed-on', 'Appliyed on', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1994, 39, 1, 'editLeave', 'Edit Leave', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1995, 39, 1, 'approve_leave', 'approve_leave', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1996, 39, 1, 'notifie-employee', 'notifie-employee', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1997, 39, 1, 'assignWork', 'Assign Work', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1998, 39, 1, 'employee-type', 'Employee Type', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(1999, 39, 1, 'license_key', 'License Key', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:05'),
(2000, 39, 1, 'ReNew', 'ReNew', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(2001, 39, 1, 'return-to-login-page', 'Return to Login page', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(2002, 39, 1, 'hours-per-day', 'Hours Per Day', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(2003, 39, 1, 'hours-per-week', 'Hours Per Week', 1, NULL, '2022-09-21 15:43:00', '2022-09-21 15:43:00'),
(2004, 39, 1, 'hours-per-month', 'Hours Per Month', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2005, 39, 1, 'risk_description_required', 'Risk Description Required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2006, 39, 1, 'schedule_type', 'Schedule Type', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2007, 39, 1, 'extra', 'Extra', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2008, 39, 1, 'emergency', 'Emergency', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2009, 39, 1, 'emergency_shift', 'Emergency Shift', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2010, 39, 1, 'assigned-working-hours-per-week', 'Assigned Working Hours Per Week', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2011, 39, 1, 'working-percentage', 'Working Percentage', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2012, 39, 1, 'actual-working-hours-per-week', 'Actual Working Hours Per Week', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2013, 39, 1, 'assigned-work-hours', 'Assigned Work Hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2014, 39, 1, 's', 's', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2015, 39, 1, 'shift_type', 'Shift Type', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2016, 39, 1, 'schadule_type_required', 'Schadule Type Required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2017, 39, 1, 'marked_dates_required', 'Please select date', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2018, 39, 1, 'h_per_day', 'H/Day', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2019, 39, 1, 'shift_hours', 'Shift Hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2020, 39, 1, 'remaining_h_per_w', 'Remaining H/W', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2021, 39, 1, 'ScheduleTemplate', 'Schedule Template', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2022, 39, 1, 'create_schedule_template', 'Create Schedule Template', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2023, 39, 1, 'edit_schedule_template', 'Edit Schedule Template', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2024, 39, 1, 'Department_Status', 'Department Status', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2025, 39, 1, 'Vacation-Trip', 'Vacation Trip', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2026, 39, 1, 'reason_required', 'Reason Required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2027, 39, 1, 'shift_time', 'Shift Time', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2028, 39, 1, 'obe_hours', 'Obe Hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2029, 39, 1, 'regular_hours', 'regular hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2030, 39, 1, 'total_hours', 'total hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2031, 39, 1, 'emergency_hours', 'Emergency Hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2032, 39, 1, 'extra_hours', 'Extra Hours', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2033, 39, 1, 'data_not_found', 'Data not found', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2034, 39, 1, 'schedule_report', 'Schedule Report', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2035, 39, 1, 'employee_stats', 'Employee Stats', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2036, 39, 1, 'leave_created_for', 'Leave Created For (Select Employee)', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2037, 39, 1, 'assign_employee', 'Assign Employee', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2038, 39, 1, 'shift-start-date', 'Shift Start Date', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2039, 39, 1, 'date_to', 'Date To', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2040, 39, 1, 'select-schedule-template-alert', 'Please select a Schedule Template First', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2041, 39, 1, 'hide', 'Hide', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2042, 39, 1, 'more_filter_options', 'More filter options', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2043, 39, 1, 'no_internet_alert', 'There is connection error. Please check your internet and try again', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2044, 39, 1, 'exit_app', 'Exit App', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2045, 39, 1, 'restart', 'Restart', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2046, 39, 1, 'opps', 'Opps...', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2047, 39, 1, 'N/A', 'N/A', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2048, 39, 1, 'Please-Wait', 'Please Wait', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2049, 39, 1, 'rename_schedule_template', 'Rename Schedule Template', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2050, 39, 1, 'stampling_duration', 'Stampling Duration', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2051, 39, 1, 'mobile_tab_bar_label_message', 'Messages', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2052, 39, 1, 'mobile_tab_bar_label_request', 'Requests', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2053, 39, 1, 'mobile_tab_bar_label_home', 'Home', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2054, 39, 1, 'mobile_tab_bar_label_notification', 'Notifications', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2055, 39, 1, 'mobile_tab_bar_label_module', 'Modules', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2056, 39, 1, 'select_ip_to_continue_activity', 'Select IP to Continue Add Activity Form', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2057, 39, 1, 'successfully_sent_request', 'Successfull sent request', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2058, 39, 1, 'send_request', 'Send request', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2059, 39, 1, 'visible_creating_IP_msg', 'Only persons those were persent at the time of creating Implementation Plan will be visible below', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2060, 39, 1, 'bank_id_personal_number_msg', 'In case of Bank Id, you can only send request to persons that have personal number', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2061, 39, 1, 'add_new_person', 'Add New Person', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2062, 39, 1, 'no_persons_found', 'No Persons Found', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2063, 39, 1, 'save_skip', 'Skip and save', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2064, 39, 1, 'manually', 'Manually', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2065, 39, 1, 'apoorved_by', 'Person\'s Approval', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2066, 39, 1, 'by_digi_signature', 'By Digital Signature', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2067, 39, 1, 'by_bank_id', 'By Bank Id', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2068, 39, 1, 'describe', 'Describe', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2069, 39, 1, 'for', 'For', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2070, 39, 1, 'details_for', 'Details for', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2071, 39, 1, 'first_choose_or_add_patient', 'First choose or add patient', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2072, 39, 1, 'first_choose_cat', 'First choose category and subcategory', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2073, 39, 1, 'first_fill_ip', 'First fill implementation plan', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2074, 39, 1, 'choose_patient', 'Choose or add patient', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2075, 39, 1, 'add_persons', 'Add persons', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2076, 39, 1, 'more_goals_fields', 'More goals fields', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2077, 39, 1, 'no_restriction', 'No Restriction', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2078, 39, 1, 'slight_restriction', 'Slight Restriction', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2079, 39, 1, 'large_limit', 'Large limit', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2080, 39, 1, 'moderate_restriction', 'Moderate Restriction', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2081, 39, 1, 'total_restriction', 'Total restriction', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2082, 39, 1, 'non_specific', 'Non Specific', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2083, 39, 1, 'attachment', 'Attachment', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2084, 39, 1, 'choose_document', 'Choose document', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2085, 39, 1, 'choose_from_template', 'Choose from template', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2086, 39, 1, 'already_saved_message', 'You have already saved this implementation plan, if you wish to edit then you can edit it from implementation plan listing screen', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2087, 39, 1, 'create_followups', 'Create Follow Up', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2088, 39, 1, 'create_activity', 'Create Activity', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2089, 39, 1, 'add_activity', 'Create Activity', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2090, 39, 1, 'add_followup', 'Create follow up for', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2091, 39, 1, 'skip', 'Skip', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2092, 39, 1, 'add_patient', 'Add New Patient', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2093, 39, 1, 'reason_for_editing', 'Reason For Editing', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2094, 39, 1, 'reason_for_editing_required', 'Required Field', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2095, 39, 1, 'ip_edited_successfully', 'Implementation plan edited successfully', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2096, 39, 1, 'ip_added_successfully', 'Implementation plan added successfully', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2097, 39, 1, 'end_date', 'End Date', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2098, 39, 1, 'can_not_add_multiple_ip_message', 'You will not be able to add multiple IP\'s while editing a particular IP', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2099, 39, 1, 'implementationPlan', 'Implementation Plan', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2100, 39, 1, 'planDetails', 'Plan Details', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2101, 39, 1, 'personDetails', 'Person Details', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2102, 39, 1, 'what_happened', 'What Happened ', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2103, 39, 1, 'how_it_happened', 'How It Happened', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2104, 39, 1, 'when_it_started', 'When it Started', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2105, 39, 1, 'what_to_do', 'What to Do', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2106, 39, 1, 'sub_goal', 'Sub Goal', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2107, 39, 1, 'plan_start_date', 'Start Date', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2108, 39, 1, 'plan_start_time', 'Start Time', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2109, 39, 1, 'activity_message', 'Activity Message', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2110, 39, 1, 'save_as_template', 'Save As Template', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2111, 39, 1, 'save_and_create_activity', 'Save & Create Activity', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2112, 39, 1, 'days', 'Select Days', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2113, 39, 1, 'how_many_times_a_day', 'How Many Times A Day', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2114, 39, 1, 'when_support_is_to_be_given', 'When Support Is To Be Given', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2115, 39, 1, 'who_should_give_the_support', 'Who should give the Support', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2116, 39, 1, 'how_the_support_to_be_given', 'How The Support To Be Given', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2117, 39, 1, 'describe_limitation', 'Describe Limitation', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2118, 39, 1, 'goal_details', 'Goal Details', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2119, 39, 1, 'sub_goal_details', 'Sub Goal Details', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2120, 39, 1, 'selet_patient', 'Select Patient', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2121, 39, 1, 'end_time', 'End Time', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2122, 39, 1, 'maintain_ability', 'Maintain Ability', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2123, 39, 1, 'improve_ability', 'Improve Ability', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2124, 39, 1, 'develop_ability', 'Develop Ability', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2125, 39, 1, 'improve_participation', 'Improve Participation', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2126, 39, 1, 'manage_independently', 'Manage Independently', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2127, 39, 1, 'ej_specifik', 'Ej Specifik', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2128, 39, 1, 'large_limitation', 'Large Limitation', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2129, 39, 1, 'total_limitation', 'Total Limitation', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2130, 39, 1, 'patient_required', 'Patient required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2131, 39, 1, 'category_required', 'Please Category', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2132, 39, 1, 'subCategory_required', 'Sub Category required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:04'),
(2133, 39, 1, 'what_happened_required', 'Please enter What Happened', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2134, 39, 1, 'how_it_happened_required', 'Please enter How it Happened', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2135, 39, 1, 'what_to_do_required', 'Please enter What to Do', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2136, 39, 1, 'when_it_started_required', 'Please enter When it Happened', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2137, 39, 1, 'goal_required', 'Please enter Goal', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2138, 39, 1, 'plan_start_date_required', 'Please select Start Date', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2139, 39, 1, 'plan_start_time_required', 'Please select Start Time', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2140, 39, 1, 'title_required', 'Title required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2141, 39, 1, 'employee_required', 'Employee required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2142, 39, 1, 'body_function', 'Body function', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2143, 39, 1, 'personal_factors', 'Personal Factors', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2144, 39, 1, 'health_condition', 'Health condition', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2145, 39, 1, 'factors', 'Factors', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2146, 39, 1, 'related_factors', 'Related factors', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2147, 39, 1, 'overall_goals', 'Overall Goals', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2148, 39, 1, 'good_living_conditions', 'Good living conditions', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2149, 39, 1, 'full_participation', 'Full participation', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2150, 39, 1, 'living_like_others', 'Living like others', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2151, 39, 1, 'living_an_independent_life', 'Living An Independent Life', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2152, 39, 1, 'not_specified', 'Not specified', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2153, 39, 1, 'not_applicable', 'Not Applicable', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2154, 39, 1, 'treatment_and_working', 'Treatment And Working', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2155, 39, 1, 'company_types', 'Company Types', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2156, 39, 1, 'make_it_active', 'Make It Active', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2157, 39, 1, 'company_type', 'Company Type', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2158, 39, 1, 'user_types', 'User Types', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2159, 39, 1, 'category_types', 'Category Types', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2160, 39, 1, 'package_name', 'Package Name', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2161, 39, 1, 'package_price', 'Price', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2162, 39, 1, 'discount_in_percentage', 'Discount in percentage', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2163, 39, 1, 'number_of_employee', 'Number Of Employee', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2164, 39, 1, 'price_per_employee_required', 'Price per Employee Required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2165, 39, 1, 'price_per_employee', 'Price Per Employee*', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2166, 39, 1, 'apply_price_on_employees', 'Apply Price On Employees', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2167, 39, 1, 'apply_price_on_patient', 'Apply Price On Patient', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2168, 39, 1, 'price_per_patient', 'Price Per Patient', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2169, 39, 1, 'price_per_patient_required', 'Price Per Patient Required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2170, 39, 1, 'maximum_employees', 'Maximum Employees', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2171, 39, 1, 'maximum_patients', 'Maximum Patients', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2172, 39, 1, 'category_name_required', 'Category Name Is Required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2173, 39, 1, 'category_name', 'Category name', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2174, 39, 1, 'select_category', 'Select category', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2175, 39, 1, 'category_type_required', 'Category type required', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2176, 39, 1, 'select_category_type', 'Select Category Type', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2177, 39, 1, 'choose_color', 'Choose Color', 1, NULL, '2022-09-21 15:43:01', '2022-09-21 15:43:01'),
(2178, 39, 1, 'color_required', 'Color required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2179, 39, 1, 'add_company', 'Add Company', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2180, 39, 1, 'full_address', 'Street Address', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2181, 39, 1, 'email_invalid', 'Invalid email', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2182, 39, 1, 'email_required', 'Email Required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2183, 39, 1, 'contact_number_required', 'Contact number required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2184, 39, 1, 'contact_number_invalid', 'Contact number invalid', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2185, 39, 1, 'organization_number_required', 'Organization number required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2186, 39, 1, 'organization_number_invalid', 'Organization number invalid', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2187, 39, 1, 'country_required', 'Country required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2188, 39, 1, 'city_required', 'city required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2189, 39, 1, 'zipCode_required', 'ZipCode required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:03'),
(2190, 39, 1, 'full_address_required', 'Street address required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2191, 39, 1, 'establishment_date_required', 'Establishment date required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2192, 39, 1, 'select_sub_category', 'Select sub category', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2193, 39, 1, 'select_company_type', 'Company Type', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2194, 39, 1, 'file_required', 'File required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2195, 39, 1, 'registrationFile', 'Registration File', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2196, 39, 1, 'company_details', 'Company Details', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2197, 39, 1, 'edit_company_details', 'Edit Company Details', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2198, 39, 1, 'modules_required', 'Modules Required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2199, 39, 1, 'package_required', 'Package Required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2200, 39, 1, 'company_added_successfully', 'Company added successfully', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2201, 39, 1, 'company_edited_successfully', 'Company edited successfully', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2202, 39, 1, 'documents', 'documents', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2203, 39, 1, 'add_branch', 'Add Branch', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2204, 39, 1, 'branch_name', 'Branch Name', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2205, 39, 1, 'company_types_required', 'Company Types Required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2206, 39, 1, 'invalid_email', 'Invalid email', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2207, 39, 1, 'invalid_contact_number', 'Invalid Contact Number', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2208, 39, 1, 'invalid_organization_number', 'Invalid Organization Number', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2209, 39, 1, 'registration_file', 'Registration File', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2210, 39, 1, 'select_file', 'Select File', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2211, 39, 1, 'company_type_required', 'Company type required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2212, 39, 1, 'branch_added_successfully', 'Branch added successfully', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2213, 39, 1, 'branch_edited_successfully', 'Branch edited successfully', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2214, 39, 1, 'basicDetails', 'Basic Details', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2215, 39, 1, 'addressDetails', 'Address Details', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2216, 39, 1, 'add_department', 'Add Department', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2217, 39, 1, 'belongs_to', 'Belongs To', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2218, 39, 1, 'belongs_to_required', 'Belongs To Required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2219, 39, 1, 'years_old', ' Years Old', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2220, 39, 1, 'unique_id', 'Unique Id', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2221, 39, 1, 'activity_count', 'Activity', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2222, 39, 1, 'task_count', 'Task', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2223, 39, 1, 'ip_count', 'IP', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2224, 39, 1, 'implementation_plans', 'Implementation Plans', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2225, 39, 1, 'not_approve', 'Not Approve', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2226, 39, 1, 'already_not_applicable', 'Already not applicable', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2227, 39, 1, 'already_done', 'Already Done', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2228, 39, 1, 'add_new_task', 'Add New Task', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2229, 39, 1, 'startDate', 'Start Date', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2230, 39, 1, 'endDate', 'End Date', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2231, 39, 1, 'link', 'Link', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2232, 39, 1, 'add_task', 'Add Task', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2233, 39, 1, 'start_time', 'Start Time', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2234, 39, 1, 'external_link', 'External link', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2235, 39, 1, 'add_comment', 'Add Comment', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2236, 39, 1, 'add_employee', 'Add Employee', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2237, 39, 1, 'assign_days', 'Assign Days', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2238, 39, 1, 'mark_done', 'Comment', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2239, 39, 1, 'assignment_day_required', 'Assign Days Is Required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2240, 39, 1, 'why_mark_done_required', 'This Field is required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2241, 39, 1, 'mark_not_applicable', 'Describe Why You Mark Not Applicable', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2242, 39, 1, 'implementation_plan', 'Implementation plan', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2243, 39, 1, 'applyFilter', 'Apply Filter', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2244, 39, 1, 'enterComment', 'Enter Comment', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2245, 39, 1, 'reply', 'Reply', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2246, 39, 1, 'reply_to', 'Replying to', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2247, 39, 1, 'start_date', 'Start Date', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2248, 39, 1, 'completed_by_patient_itself', 'Completed By Patient Itself', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2249, 39, 1, 'patient_did_not_want', 'Patient Did Not Want', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2250, 39, 1, 'not_done_by_employee', 'Not Done By Employee', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2251, 39, 1, 'completed_by_staff_on_time', 'Completed By Staff On Time', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2252, 39, 1, 'completed_by_staff_after_time', 'Completed By Staff After Time', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2253, 39, 1, 'select_action', 'Select Action*', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2254, 39, 1, 'want_to_create_social_journal', 'Want To Create Social Journal', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2255, 39, 1, 'journal_will_be_created', 'Journal will be created for this action', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2256, 39, 1, 'journal_and_deviation_will_be_created', 'Journal and deviation will be created for this action', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2257, 39, 1, 'secret_journal', 'Secret Journal', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2258, 39, 1, 'signed', 'Signed', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2259, 39, 1, 'journalFilter', 'Journal Filter', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2260, 39, 1, 'is_signed', 'Is Signed', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2261, 39, 1, 'is_completed', 'Is Completed', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2262, 39, 1, 'want_to_create_complete_deviation', 'Do you want to complete this deviation', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2263, 39, 1, 'want_to_create_sign_deviation', 'Do you want to sign this deviation', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2264, 39, 1, 'you_wont_be_able_to_revert_this', 'You won\'t be able to revert this!', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2265, 39, 1, 'is_secret', 'Is Secret', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2266, 39, 1, 'conform', 'Conform', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2267, 39, 1, 'allData', 'All Data', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2268, 39, 1, 'personal_details', 'Personal Details', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2269, 39, 1, 'custom_unique_id', 'Patient ID', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2270, 39, 1, 'patient_types', 'Patient Type', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2271, 39, 1, 'relatedFactor', 'Related Factor', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2272, 39, 1, 'date_time', 'Date & Time', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2273, 39, 1, 'created_at', 'Created At', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2274, 39, 1, 'descriptions', 'Descriptions', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2275, 39, 1, 'immediate_action', 'Immediate Action', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2276, 39, 1, 'suggestion_to', 'Suggestion', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2277, 39, 1, 'probable', 'Probable Cause', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2278, 39, 1, 'deviation_detail', 'Deviation Detail', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2279, 39, 1, 'module_name', 'module name', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2280, 39, 1, 'module_name_required', 'module name required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2281, 39, 1, 'module_type_required', 'module type required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2282, 39, 1, 'module_required', 'module required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2283, 39, 1, 'inactive_modules', 'inactive modules', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2284, 39, 1, 'forget_password', 'Fogot Password ?', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2285, 39, 1, 'mobile_forget_password_messsage', 'Don\'t worry! It happens. Please enter the email associated with your account', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2286, 39, 1, 'get_otp', 'Get OTP', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2287, 39, 1, 'email_is_required', 'Email is required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2288, 39, 1, 'enter_OTP', 'Enter OTP', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02');
INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(2289, 39, 1, 'mobile_forget_password_otp_messsage', 'A verification code has been sent to your email address', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2290, 39, 1, 'invalid_otp', 'Invalid OTP', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2291, 39, 1, 'package_listing', 'Packages', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2292, 39, 1, 'company_listing', 'Company', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2293, 39, 1, 'total_patients', 'Total Patients', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2294, 39, 1, 'total_employee', 'Total Employee', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2295, 39, 1, 'change_password', 'Change Password', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2296, 39, 1, 'overview', 'Overview', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2297, 39, 1, 'registration_details', 'Registration Details', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2298, 39, 1, 'weekly_hours_alloted_by_govt', 'Weekly Hours Alloted By Govt', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2299, 39, 1, 'aceuss', 'ACEUSS', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2300, 39, 1, 'nothing_to_show', 'Nothing to show', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2301, 39, 1, 'reset_password', 'Reset Password', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2302, 39, 1, 'password_is_required', 'Password is required', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2303, 39, 1, 'please_re_enter_the_password', 'Please re-enter the password', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2304, 39, 1, 'password_not_matched', 'Password not matched', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2305, 39, 1, 'password_changed_successfully', 'Password changed successfully', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2306, 39, 1, 'new_password', 'New Password', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2307, 39, 1, 'confirm_new_password', 'Confirm New Password', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2308, 39, 1, 'forgot_password', 'Forgot password ?', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2309, 39, 1, 'agree_to_all', 'I agree to all', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2310, 39, 1, 'Terms_and_Privacy', 'Terms of Use, Privacy Policy.', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2311, 39, 1, 'remember_me', 'Remember me', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2312, 39, 1, 'activity_classification', 'Activity Classification', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2313, 39, 1, 'Activity_classification_name', 'Activity Classification name', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2314, 39, 1, 'click_here_to', 'Click here to', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2315, 39, 1, 'read_more', 'read more', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2316, 39, 1, 'totle', 'Totle', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2317, 39, 1, 'after_discount', 'After Discount', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2318, 39, 1, 'buy', 'Buy', 1, NULL, '2022-09-21 15:43:02', '2022-09-21 15:43:02'),
(2319, 39, 1, 'rs', 'Rs', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2320, 39, 1, 'add_start_time_and_end_time', 'Add Start Time And End Time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2321, 39, 1, 'add-start-time-and-end-time', 'Add Start Time And End Time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2322, 39, 1, 'select_days_for_week', 'Select repetition days for week', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2323, 39, 1, 'repetition_week_days_array_required', 'Select atleast one date', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2324, 39, 1, 'minutes_required', 'Minutes required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2325, 39, 1, 'task_edited_successfully', 'Task edited successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2326, 39, 1, 'task_added_successfully', 'Task added successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2327, 39, 1, 'category_type', 'Category type', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2328, 39, 1, 'task_details', 'Task Details', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2329, 39, 1, 'notify_emergency_contact', 'Notify emergency contact', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2330, 39, 1, 'minutes', 'Minutes (Max 240)', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2331, 39, 1, 'send_notification', 'Send push notification on device', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2332, 39, 1, 'send_text', 'Send text message', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2333, 39, 1, 'discription', 'Discription', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2334, 39, 1, 'startTime', 'Start Time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2335, 39, 1, 'endTime', 'End Time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2336, 39, 1, 'notify_users_before_start', 'Notify users before start', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2337, 39, 1, 'notify_users_after_end', 'Notify users after end', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2338, 39, 1, 'notify_users_after_start', 'Notify users after end', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2339, 39, 1, 'start_time_greater_message', 'Start time can not be greater or equal to end time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2340, 39, 1, 'notify_users_in_time', 'Notify users at the time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2341, 39, 1, 'videoUrl', 'Video URL', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2342, 39, 1, 'informationURL', 'Information URL', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2343, 39, 1, 'addressURL', 'Address URL', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2344, 39, 1, 'highPriority', 'High priority', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2345, 39, 1, 'invalid_url', 'Invalid URL', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2346, 39, 1, 'startDate_required', 'Start date required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2347, 39, 1, 'endDate_required', 'End Date required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2348, 39, 1, 'startTime_required', 'Start time required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2349, 39, 1, 'endTime_required', 'End time required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2350, 39, 1, 'discription_required', 'Discription required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2351, 39, 1, 'repetition_type', 'Repetition type', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2352, 39, 1, 'repetition_time', 'Set time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2353, 39, 1, 'day_in_month', 'Day', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2354, 39, 1, 'description_required', 'Required Field', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2355, 39, 1, 'start_date_required', 'Start date required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2356, 39, 1, 'end_date_required', 'End date required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2357, 39, 1, 'start_time_required', 'Start time required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2358, 39, 1, 'end_time_required', 'End time required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2359, 39, 1, 'every_required', 'Every required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2360, 39, 1, 'repetition_time_required', 'Time required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2361, 39, 1, 'day_in_month_required', 'Day required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2362, 39, 1, 'week_days_required', 'Week days required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2363, 39, 1, 'activity_edited_successfully', 'Activity Edited Successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2364, 39, 1, 'activity_added_successfully', 'Activity Added Successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2365, 39, 1, 'activity_details', 'Activity Details', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2366, 39, 1, 'time_and_repetition', 'Time And Repetition', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2367, 39, 1, 'reminders', 'Reminders', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2368, 39, 1, 'external_comment', 'External Comment', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2369, 39, 1, 'internal_comment', 'Internal Comment', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2370, 39, 1, 'is_risk', 'is Risk', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2371, 39, 1, 'risk_description', 'Risk Description', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2372, 39, 1, 'how_many_time', 'How Many Times Per Day', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2373, 39, 1, 'activity_time', 'Activity Time', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2374, 39, 1, 'companyType', 'Company type', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2375, 39, 1, 'addEmployee', 'Add Employee', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2376, 39, 1, 'employee_type', 'Employee Type', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2377, 39, 1, 'color', 'Color', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2378, 39, 1, 'verification_file_required', 'Verification file required ?', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2379, 39, 1, 'verification_file', 'Select Verification File', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2380, 39, 1, 'branch_required', 'Branch Name Required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2381, 39, 1, 'department_required', 'Department required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2382, 39, 1, 'enter_valid_contact_number', 'Please enter valid contact number', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2383, 39, 1, 'personal_number_required', 'Personal number required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2384, 39, 1, 'gender_required', 'Gender required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2385, 39, 1, 'employee_type_required', 'Employee type required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2386, 39, 1, 'joining_date_required', 'Joining data required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2387, 39, 1, 'confirmPassword', 'Confirm Password', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2388, 39, 1, 'password_required', 'Password required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2389, 39, 1, 'confirmPassword_required', 'Please re-enter the password', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2390, 39, 1, 'companyType_required', 'Company type required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2391, 39, 1, 'employee_added_successfully', 'Employee added successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2392, 39, 1, 'employee_edited_successfully', 'Employee edited successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2393, 39, 1, 'postal_area', 'Postal Area', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2394, 39, 1, 'postalArea_required', 'Postal area required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2395, 39, 1, 'company', 'Company', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2396, 39, 1, 'personalDetails', 'Personal Details', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2397, 39, 1, 'another_activity_contact_person', 'Contact Person', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2398, 39, 1, 'company_contact_person', 'Contact Person', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2399, 39, 1, 'institute_contact_person', 'Institute Contact Person', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2400, 39, 1, 'custom_unique_id_required', 'Custom unique id required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2401, 39, 1, 'add_ip', 'Add Implementation Plan', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2402, 39, 1, 'shift_timing_from_required', 'Shift timing from required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2403, 39, 1, 'shift_timing_to_required', 'Shift timing to required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2404, 39, 1, 'shift_timing_from', 'Shift timing from', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2405, 39, 1, 'shift_timing_to', 'Shift timing to', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2406, 39, 1, 'company_name', 'Company Name', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2407, 39, 1, 'company_name_required', 'Company name required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2408, 39, 1, 'institute_name_required', 'Institute name required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2409, 39, 1, 'institute_name', 'Institute Name', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2410, 39, 1, 'classes_from', 'Time From', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2411, 39, 1, 'classes_to', 'Time To', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2412, 39, 1, 'classes_from_required', 'Classes from required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2413, 39, 1, 'classes_to_required', 'Classes to required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2414, 39, 1, 'patient_type', 'Patient Type', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2415, 39, 1, 'patient_type_required', 'Patient type required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2416, 39, 1, 'gov_agency_valid_message', 'Please enter valid government agencies alloted hours', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2417, 39, 1, 'gov_agency_required', 'Government agency required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2418, 39, 1, 'assigned_hours', 'Hours', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2419, 39, 1, 'gov_agency', 'Government Agencies', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2420, 39, 1, 'addPatient', 'Add Patient', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2421, 39, 1, 'hours_alloted_by_govt', 'Hours Alloted by Government', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2422, 39, 1, 'hours_alloted_by_govt_required', 'Hours alloted by government required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2423, 39, 1, 'disease_details', 'Disease Details', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2424, 39, 1, 'disease_details_required', 'Disease details required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2425, 39, 1, 'skip_and_continue', 'Skip & Continue', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2426, 39, 1, 'zipCode_is_not_vailid', 'ZipCode Is Not Vailid', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2427, 39, 1, 'enter_valid_personal_number', 'Enter Valid Personal Number', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2428, 39, 1, 'gov_id_required', 'Government ID required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2429, 39, 1, 'gov_id', 'Government ID', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2430, 39, 1, 'disease_description', 'Disease Description', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2431, 39, 1, 'weekly_hours_alloted_by_govt_required', 'Weekly hours alloted by government required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2432, 39, 1, 'patient_edited_successfully', 'Patient edited successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2433, 39, 1, 'patient_added_successfully', 'Patient added successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2434, 39, 1, 'is_caretaker', 'Is Care Taker', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2435, 39, 1, 'is_family_member', 'Is Family Member', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2436, 39, 1, 'is_contact_person', 'Is Contact Person', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2437, 39, 1, 'social_security_number', 'Social Security Number', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2438, 39, 1, 'employee_name_required', 'Employee Name Required', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2439, 39, 1, 'patientForm', 'Patient Form', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2440, 39, 1, 'special_information', 'Special Information', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2441, 39, 1, 'schools_contact_number', 'School\'s Contact Number', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2442, 39, 1, 'companys_contact_number', 'Company Contact Number', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2443, 39, 1, 'schools_full_address', 'School\'s Full Address', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2444, 39, 1, 'companys_full_address', 'Company Full Address', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2445, 39, 1, 'another_activity_name', 'Full Name', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2446, 39, 1, 'activitys_contact_number', 'Phone', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2447, 39, 1, 'activitys_full_address', 'Address', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2448, 39, 1, 'another_activity', 'Activity Type', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2449, 39, 1, 'another_activity_page', 'Other Activities', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2450, 39, 1, 'number_of_hours', 'Number Of Hours', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2451, 39, 1, 'issuer_name', 'Issuer Name', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2452, 39, 1, 'select_days', 'Select Days', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2453, 39, 1, 'Short_term_stay', 'Short-term stay', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2454, 39, 1, 'daily_activity', 'Daily activity', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2455, 39, 1, 'relatives_and_caretakers', 'Relatives & Caretakers', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2456, 39, 1, 'disability_details', 'Disability Details', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2457, 39, 1, 'studies_and_work', 'Studies & Work', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2458, 39, 1, 'add_new', 'Add New', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2459, 39, 1, 'alloted_hours_details', 'Alloted Hours Detail', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2460, 39, 1, 'decision_dates', 'Decision Dates', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2461, 39, 1, 'add_more_decision', 'Add More Decision', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2462, 39, 1, 'total_branches', 'Total Branches', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2463, 39, 1, 'total_department', 'Total Department', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2464, 39, 1, 'package_validity_in_days', 'Package Validity In Days', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2465, 39, 1, 'package_status', 'Package Status', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2466, 39, 1, 'ragistration_date', 'Ragistration Date', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2467, 39, 1, 'package_end_date', 'Package End Date', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2468, 39, 1, 'package_start_date', 'Package Start Date', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2469, 39, 1, 'family_members', 'Family Members', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2470, 39, 1, 'nurse', 'Nurse', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2471, 39, 1, 'decisionsDocuments', 'Decisions & Documents', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2472, 39, 1, 'otherActivities', 'Other Activities', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2473, 39, 1, 'studiesWork', 'Studies Work', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2474, 39, 1, 'disabilityDetails', 'Disability Details', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2475, 39, 1, 'contact_person', 'Contact Person', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2476, 39, 1, 'institute_contact_number', 'Institute\'s Phone No.', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2477, 39, 1, 'institute_full_address', 'Institute\'s Address', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2478, 39, 1, 'weekly_hours_name', 'Agency', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2479, 39, 1, 'weekly_hours_start_date', 'Start Date', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2480, 39, 1, 'weekly_hours_end_date', 'End Date', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2481, 39, 1, 'company_contact_number', 'Company\'s Phone No.', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2482, 39, 1, 'company_full_address', 'Company\'s Address', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2483, 39, 1, 'from_timing', 'Working From', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2484, 39, 1, 'to_timing', 'Work To', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2485, 39, 1, 'edit_profile', 'Edit Profile', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2486, 39, 1, 'date_of_birth', 'Date Of Birth', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2487, 39, 1, 'your_details_updated_successfully', 'Your details are updated successfully ,!!', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2488, 39, 1, 'postalArea', 'Postal Area', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2489, 39, 1, 'street_address', 'Street Address', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2490, 39, 1, 'select_the_view', 'Select The View', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2491, 39, 1, 'add_contact_person', 'Add Contact Person', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2492, 39, 1, 'name_invalid', 'Enter A Valid Name', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2493, 39, 1, 'contact_invalid', 'Enter A Valid Contact Number', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2494, 39, 1, 'person_edited_successfully', 'Person Edited Successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2495, 39, 1, 'person_added_successfully', 'Person Added Successfully', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2496, 39, 1, 'minor_child', 'Minor Child', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2497, 39, 1, '3', '3', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2498, 39, 1, '4', '4', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2499, 39, 1, '5', '5', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2500, 39, 1, '6', 'Friday', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2501, 39, 1, '7', 'Saturday', 1, NULL, '2022-09-21 15:43:03', '2022-09-21 15:43:03'),
(2502, 39, 1, 'body_functions', 'Body Functions', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2503, 39, 1, 'health_conditions', 'Health Conditions', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2504, 39, 1, 'other_factors', 'Other Factors', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2505, 39, 1, 'relatedFactors', 'Related Factors', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2506, 39, 1, 'showOverallGoal', 'Overall Goal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2507, 39, 1, 'overall_goal_detail', 'Overall goal detail', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2508, 39, 1, 'week_days', 'Week Days', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2509, 39, 1, 'how_support_should_be_given', 'How Support Should Be Given', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2510, 39, 1, 'when_during_the_day', 'When during the day', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2511, 39, 1, 'who_give_support', 'Who Should Give Support', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2512, 39, 1, 'limitation_detail', 'Limitation Detail', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2513, 39, 1, 'add_follow_up', 'Create Follow Up', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2514, 39, 1, 'create_follow_up', 'Follow Ups', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2515, 39, 1, 'view_edit_history', 'View Edit History', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2516, 39, 1, 'implementationplandetail', 'Implementation Plan Detail', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2517, 39, 1, 'sub_goal_selected', 'Selected Sub Goal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2518, 39, 1, 'sub_goal_detail', 'Sub Goal Detail', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2519, 39, 1, 'basic_details', 'Basic Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2520, 39, 1, 'sub_category_details', 'Implementation Plan Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2521, 39, 1, 'plan_end_date', 'End date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2522, 39, 1, 'family_member', 'Family Member', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2523, 39, 1, 'proceed', 'Proceed to sign IP', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2524, 39, 1, 'signIp', 'Sign Implementation Plan', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2525, 39, 1, 'IpEditHistory', 'IP Edit History', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2526, 39, 1, 'approved_by', 'Approved By', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2527, 39, 1, 'approved_date', 'Approved Date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2528, 39, 1, 'created_by', 'Created By', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2529, 39, 1, 'edited_by', 'Edited By', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2530, 39, 1, 'View_more_details', 'View More Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2531, 39, 1, 'is_guardian', 'Is Guardian', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2532, 39, 1, 'is_participating', 'Is Participating', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2533, 39, 1, 'participating_in_what_way', 'Participating in what way', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2534, 39, 1, 'participating_in_what_way_required', 'Participating in what way required', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2535, 39, 1, 'is_present', 'Is Present', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2536, 39, 1, 'other_name', 'Title', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2537, 39, 1, 'followup_for', 'Follow up for', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2538, 39, 1, 'add_at_least_starting', 'Add at least starting date and starting time of every follow up', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2539, 39, 1, 'add_more_follow_up_dates', 'Add more follow up dates', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2540, 39, 1, 'followup_dates', 'Follow up Dates', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2541, 39, 1, 'follow_up', 'Follow Up', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2542, 39, 1, 'follow_added_successfully', 'Follow-up added successfully!!', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2543, 39, 1, 'add_a_question', 'Add new question', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2544, 39, 1, 'new_question', 'New question', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2545, 39, 1, 'question_required', 'Question required', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2546, 39, 1, 'question_added_successfully', 'Question added successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2547, 39, 1, 'follow_edited_successfully', 'Follow edited successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2548, 39, 1, 'implementation', 'Implementation', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2549, 39, 1, 'select_questions', 'Select Questions', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2550, 39, 1, 'follow_up_completed_successfully', 'Follow up completed successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2551, 39, 1, 'write_your_answer', 'Write your answer', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2552, 39, 1, 'follow_up_questions', 'Follow up questions', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2553, 39, 1, 'complete_follow_up', 'Complete follow up', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2554, 39, 1, 'followUpDetails', 'Follow Ups Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2555, 39, 1, 'ipDetails', 'Implementation Plan Detail', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2556, 39, 1, 'q', 'Q.', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2557, 39, 1, 'ans', 'ans.', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2558, 39, 1, 'history_not_available', 'Edit History Not Available', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2559, 39, 1, 'FollowUpEditHistory', 'Follow UpEdit History', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2560, 39, 1, 'choose', 'Choose', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2561, 39, 1, 'choose_from_library', 'Choose from library', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2562, 39, 1, 'open_camera', 'Open camera', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2563, 39, 1, 'choose_image', 'Choose image', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2564, 39, 1, 'WordListing', 'Word Listing', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2565, 39, 1, 'error_title', 'paragraph requered', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2566, 39, 1, 'AddWord', 'Add Word', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2567, 39, 1, 'EditWord', 'Edit Word', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2568, 39, 1, 'errText', 'Input field can\'t be empty.', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2569, 39, 1, 'ParagraphsList', 'Paragraphs List', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2570, 39, 1, 'readMore', 'Read More', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2571, 39, 1, 'AddParagraph', 'Add Paragraph', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2572, 39, 1, 'EditParagraph', 'Edit Paragraph', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2573, 39, 1, 'branch_id', 'Branch Id', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2574, 39, 1, 'patient_name', 'Full Name', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2575, 39, 1, 'patient_detail', 'Patient detail', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2576, 39, 1, 'Patient_ID', 'Patient ID', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2577, 39, 1, 'BranchName', 'Branch', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2578, 39, 1, 'data_is_not_available', 'Data Is Not Available', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2579, 39, 1, 'assigned_by', 'Assigned By', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2580, 39, 1, 'ip_title', 'IP Title', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2581, 39, 1, 'action_date', 'Action Date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2582, 39, 1, 'Ip_details', 'IP Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2583, 39, 1, 'view_profile', 'View Profile', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2584, 39, 1, 'patient_details', 'Patient Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2585, 39, 1, 'ActivityDetails', 'Activity Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2586, 39, 1, 'not_done', 'Not Done', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2587, 39, 1, 'taskDetails', 'Task Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2588, 39, 1, 'phone', 'Phone', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2589, 39, 1, 'addJournal', 'Add Journal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2590, 39, 1, 'otherDetails', 'Other details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2591, 39, 1, 'changeEventDate', 'Change Event Date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2592, 39, 1, 'secretJournal', 'Secret Journal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2593, 39, 1, 'journal_saved_msg', 'Journal saved successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2594, 39, 1, 'journal_updated_msg', 'Journal updated successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2595, 39, 1, 'old_description', 'Previous Description', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2596, 39, 1, 'information', 'Information', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2597, 39, 1, 'incidentDate', 'Incident Date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2598, 39, 1, 'no_actions_to_view', 'No Actions To View', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2599, 39, 1, 'edited_at', 'Edited At', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2600, 39, 1, 'full_name', 'Full Name', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2601, 39, 1, 'journal_sign_msg', 'Journal signed successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2602, 39, 1, 'journal_approve_msg', 'Journal approved successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2603, 39, 1, 'journal_sign_confirm_msg', 'Do you want to sign this Journal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2604, 39, 1, 'journal_approve_confirm_msg', 'Do you want to approve this Journal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2605, 39, 1, 'journal_active_confirm_msg', 'Do you want to active this Journal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2606, 39, 1, 'journal_active_msg', 'This journal is active now', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2607, 39, 1, 'journal_inactive_msg', 'This journal is In-active now', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2608, 39, 1, 'action_required', 'Action required', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2609, 39, 1, 'result_required', 'Result required', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2610, 39, 1, 'no_journal_found', 'No Journal Found !!', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2611, 39, 1, 'select_patient', 'Select Patient', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2612, 39, 1, 'already_signed', 'Already Signed', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2613, 39, 1, 'inactive_msg', 'Do you want to In-active this journal', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2614, 39, 1, 'select_patient_first', 'Select patient first', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2615, 39, 1, 'from_date', 'From Date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2616, 39, 1, 'to_date', 'To Date', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2617, 39, 1, 'to_date_required', 'To date required', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2618, 39, 1, 'from_date_required', 'From date required', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2619, 39, 1, 'print_with_secret', 'Print with secret', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2620, 39, 1, 'date_and_time', 'Date and Time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2621, 39, 1, 'branch_and_patient', 'Branch And Patient', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2622, 39, 1, 'category_and_subCategory', 'Category And SubCategory', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2623, 39, 1, 'deviation_details', 'Deviation Details', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2624, 39, 1, 'probable_cause_of_the_incident', 'Probable Cause of The Incident', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2625, 39, 1, 'further_investigation', 'Further-Investigation', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2626, 39, 1, 'immediate_action_required', 'Required Field', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2627, 39, 1, 'date_time_required', 'Required Field', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2628, 39, 1, 'i_want_to_report_a_value_damage', 'I want to report a value damage', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2629, 39, 1, 'jag_vill_rapportera_en_virdiskada', 'Jag Vill Rapportera En Virdiskada', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2630, 39, 1, 'jag_vill_rapportera_en_virdiskada_value', 'risk of host damage I want to report a significant risk of misconduct', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2631, 39, 1, 'deviation_statistics', 'Deviation Statistics', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2632, 39, 1, 'total_deviation', 'Total Deviation', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2633, 39, 1, 'clear_filter', 'Clear', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2634, 39, 1, 'apply', 'Apply', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2635, 39, 1, 'this_role_has', 'This role has', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2636, 39, 1, 'manage_roles', 'Manage Roles & Permissions', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2637, 39, 1, 'create_role', 'Create Role', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2638, 39, 1, 'assign_permission_message', 'Please assign permission to create the role, the users with this roles can access the related modules', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2639, 39, 1, 'role_saved_msg', 'Role saved successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2640, 39, 1, 'role_updated_msg', 'Role updated successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2641, 39, 1, 'required_field', 'Required field', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2642, 39, 1, 'user_type', 'User Type', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2643, 39, 1, 'role_name', 'Role Name', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2644, 39, 1, 'select_permissions', 'Select permissions first', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2645, 39, 1, 'trashedActivity', 'Trashed Activities', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2646, 39, 1, 'activity_restored_successfully_msg', 'Activity restored successfully !!', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2647, 39, 1, 'activity_restored_confirmation_msg', 'Do you want to restore this activity ?', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2648, 39, 1, 'mobile_languages', 'Languages', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2649, 39, 1, 'mobile_sign_ip', 'Sign IP', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2650, 39, 1, 'stampling', 'Stampling', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2651, 39, 1, 'in_time', 'In Time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2652, 39, 1, 'out_time', 'Out Time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2653, 39, 1, 'currently_logged_in', 'Currently Stamped In', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2654, 39, 1, 'previous', 'Previous', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2655, 39, 1, 'choose_schedule', 'Choose Schedule', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2656, 39, 1, 'mobile_reason_for_early_login', 'Reason for early stamp in', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2657, 39, 1, 'mobile_reason_for_late_login', 'Reason for late stamp in', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2658, 39, 1, 'mobile_reason_for_early_logout', 'Reason for early stamp out', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2659, 39, 1, 'mobile_reason_for_late_logout', 'Reason for late stamp out', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2660, 39, 1, 'mobile_no_chats_to_show', 'No chats to show', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2661, 39, 1, 'see_older_messages', 'See older messages', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2662, 39, 1, 'all_contacts', 'Contacts', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2663, 39, 1, 'mobile_online', 'Online', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2664, 39, 1, 'mobile_no_contacts_to_show', 'No contacts to show', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2665, 39, 1, 'login_at_regular_time', 'Stamp me in at the scheduled time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2666, 39, 1, 'stamp_out', 'Stamp Out', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2667, 39, 1, 'stamp_in', 'Stamp In', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2668, 39, 1, 'already_stamped_inMsg', 'You are already stamped in, please stamp out first to continue', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2669, 39, 1, 'stamp_out_success_msg', 'Successfully Stamped Out', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2670, 39, 1, 'stamp_in_success_msg', 'Successfully Stamped In', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2671, 39, 1, 'language_changed_success_msg', 'Language changed successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2672, 39, 1, 'persons_not_found', 'No Persons Found', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2673, 39, 1, 'relative_patient_same_number', 'Relative or caretaker can not have same contact number as of patient', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2674, 39, 1, 'relative_patient_same_personal_number', 'Relative or caretaker can not have same personal number as of patient', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2675, 39, 1, 'relative_patient_same_email', 'Relative or caretaker can not have same email as of patient', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2676, 39, 1, 'from_time_invalid_msg', 'From time can not be equal or greater than To time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2677, 39, 1, 'classess_time_invalid_msg', 'Classess from time can not be equal or greater than Classess to time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2678, 39, 1, 'expected_out_time_msg', 'Expected out time can not be less or equal to stamp in time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2679, 39, 1, 'expected_out_time', 'Expected Out Time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2680, 39, 1, 'start_time_invalid_msg', 'Start time can not be greater than End time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2681, 39, 1, 'new_request_msg', 'Click on + button to generate new request', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2682, 39, 1, 'new_request', 'New Request', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2683, 39, 1, 'success_request_msg', 'Requested Successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2684, 39, 1, 'module_m', 'Module', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2685, 39, 1, 'requested_at_m', 'Requested At', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2686, 39, 1, 'rejected', 'Rejected', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2687, 39, 1, 'reply_on_comment', 'Reply on Comment', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2688, 39, 1, 'requested_by', 'Requested By', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2689, 39, 1, 'reject', 'Reject', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2690, 39, 1, 'rejected_successfully', 'Rejected Successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2691, 39, 1, 'approved_successfully', 'Approved Successfully', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2692, 39, 1, 'schedule_use_web_msg', 'Please use website in order to create or edit Schedule', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2693, 39, 1, 'schedule_template_use_web_msg', 'Please use website in order to create or edit Schedule Template', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2694, 39, 1, 'request_admin', 'Request Admin', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2695, 39, 1, 'passed', 'Passed', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2696, 39, 1, 'vacation', 'Vacation', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2697, 39, 1, 'leave', 'Leaves', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2698, 39, 1, 'plans_mob', 'Plans', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2699, 39, 1, 'active_modules', 'Active Modules', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2700, 39, 1, 'manage_files', 'Manage Files', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2701, 39, 1, 'categoryType', 'Category Type', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2702, 39, 1, 'admin', 'Admin', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2703, 39, 1, 'upload_file', 'Upload File', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2704, 39, 1, 'upload_for', 'Upload For', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2705, 39, 1, 'file_for_employee', 'File for Employee', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2706, 39, 1, 'file_for_patient', 'File for Patient', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2707, 39, 1, 'section_not_accessible', 'Section Not Accessible', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2708, 39, 1, 'rest_start_time', 'Rest start time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2709, 39, 1, 'rest_end_time', 'Rest end time', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2710, 39, 1, 'create-work-shift', 'Create work shift', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2711, 39, 1, 'edit-work-shift', 'Edit work shift', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2712, 39, 1, 'h', 'H', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2713, 39, 1, 'notify-employee', 'Notify Employee', 1, NULL, '2022-09-21 15:43:04', '2022-09-21 15:43:04'),
(2714, 39, 1, 'do-you-want-to-notify-your-substitute', 'Do you want to notify your substitute', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2715, 39, 1, 'hourly', 'Hourly', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2716, 39, 1, 'fixed', 'Fixed', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2717, 39, 1, 'edit-reason', 'Edit Reason', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2718, 39, 1, 'manage-licences', 'Manage All License', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2719, 39, 1, 'licences', 'License', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2720, 39, 1, 'license_end_date', 'License End Date', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2721, 39, 1, 'every-first-week', 'Every first week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2722, 39, 1, 'every-second-week', 'Every second week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2723, 39, 1, 'every-third-week', 'Every third week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2724, 39, 1, 'every-fourth-week', 'Every fourth week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2725, 39, 1, 'every-fifth-week', 'Every fifth week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2726, 39, 1, 'every-sixth-week', 'Every sixth week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2727, 39, 1, 'every-seventh-week', 'Every seventh week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2728, 39, 1, 'every-eighth-week', 'Every eighth-week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2729, 39, 1, 'All', 'All', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2730, 39, 1, 'please-upload-file-less-than', 'Please upload file less than {{size}}', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2731, 39, 1, 'restore-this-activity', 'Restore this Activity: {{name}}', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2732, 39, 1, 'bankId', 'BankId', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2733, 39, 1, 'total-branch', 'Total Branch', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2734, 39, 1, 'branch-type', 'Branch Type', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2735, 39, 1, 'file-filter', 'File Filter', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2736, 39, 1, 'module-inactive-text', 'Please contact Admin to enable this module.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2737, 39, 1, 'followup-details', 'Followup Details', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2738, 39, 1, 'mark-as-read-all', 'Mark {{count}} Notifications As Read', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05');
INSERT INTO `labels` (`id`, `group_id`, `language_id`, `label_name`, `label_value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(2739, 39, 1, 'reload', 'Reload', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2740, 39, 1, 'import-language', 'Import Language', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2741, 39, 1, 'languages', 'Languages', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2742, 39, 1, 'language-name', 'Name', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2743, 39, 1, 'language-short-name', 'Short Name', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2744, 39, 1, 'language-file', 'Language File', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2745, 39, 1, 'docs', 'Docs', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2746, 39, 1, 'ip-menu', 'Plans', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2747, 39, 1, 'plans', 'Plans', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2748, 39, 1, 'task-menu', 'Task & Calender', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2749, 39, 1, 'assign-to', 'Assign To', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2750, 39, 1, 'assigned-to-companies', 'Assigned to {{count}} Companies', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2751, 39, 1, 'company-to-employee', 'File for Employee', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2752, 39, 1, 'company-to-patient', 'File to Patient', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2753, 39, 1, 'filtered-activity', 'Filtered Activity', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2754, 39, 1, 'create-new-license', 'Create new License', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2755, 39, 1, 'licenses-description', 'Here you can manage all your licenses.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2756, 39, 1, 'expired-at', 'Expired At', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2757, 39, 1, 'edit-license', 'Edit License', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2758, 39, 1, 'license-filter', 'License Filter', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2759, 39, 1, 'is-used', 'Is Used', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2760, 39, 1, 'license-assigned', 'License assigned successfully', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2761, 39, 1, 'assign-license', 'Assign License', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2762, 39, 1, 'assigned', 'Assigned', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2763, 39, 1, 'assign-this-license', 'Assign this License Key', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2764, 39, 1, 'parent', 'Parent', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2765, 39, 1, 'used-not-allowed-words', '{{count}} words are being used which are not allowed.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2766, 39, 1, 'view-license-details', 'View license detail', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2767, 39, 1, 'active-from', 'Active From', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2768, 39, 1, 'expire-at', 'Expire At', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2769, 39, 1, 'update-profile', 'Update Profile', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2770, 39, 1, 'profile-update', 'Profile Update', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2771, 39, 1, 'create-subcategory', 'Create Subcategory', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2772, 39, 1, 'edit-subcategory', 'Edit Subcategory', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2773, 39, 1, 'check-all', 'Check All', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2774, 39, 1, 'uncheck-all', 'Uncheck All', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2775, 39, 1, 'Please-download-this-csv-file-and-fill-all-the-details-accordingly.-After-that-you-can-upload-the-file-to-import-your-items.', 'Please download this csv file and fill all the details accordingly. After that you can upload the file to import your items', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2776, 39, 1, 'uploaded-for', 'Uploaded For', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2777, 39, 1, 'contact-type', 'Contract Type', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2778, 39, 1, 'contract-hourly', 'Hourly Contract', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2779, 39, 1, 'contract-fixed', 'Fixed Contract', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2780, 39, 1, 'language-labels', 'Language Labels', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2781, 39, 1, 'schedule-menu', 'Schedule', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2782, 39, 1, 'obe', 'Obe\'s', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2783, 39, 1, 'title-&-time', 'Title & Time', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2784, 39, 1, 'add-title-&-time', 'Add Title & Time', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2785, 39, 1, 'enable-range', 'Enable Range', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2786, 39, 1, 'select-dates', 'Select Dates', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2787, 39, 1, 'every-week', 'Every Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2788, 39, 1, 'start-time-and-end-time', 'Start & End Time', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2789, 39, 1, 'max-week-accept-', 'Max Week Accept', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2790, 39, 1, 'shift-date', 'Shift Date', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2791, 39, 1, 'leave-applied', 'Leave Applied', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2792, 39, 1, 'no-leave-applied', 'No Leave Applied', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2793, 39, 1, 'applied', 'Applied', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2794, 39, 1, 'leave-approval', 'Leave Approval', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2795, 39, 1, 'no-leave-approval', 'No Leave Approval', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2796, 39, 1, 'create-ov', 'Create Ov', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2797, 39, 1, 'applied-on', 'Applied on', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2798, 39, 1, 'create-schedule', 'Create Schedule', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2799, 39, 1, 'employee-&-shift', 'Employee & Shift', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2800, 39, 1, 'select-employee-$-shift', 'Select Employee & Shift', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2801, 39, 1, 'add-dates', 'Add Dates', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2802, 39, 1, 'new-message-received', 'New Message Received', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2803, 39, 1, 'permanent-delete-activity-text', 'All resources related to this activity will be deleted, Are you sure to proceed.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2804, 39, 1, 'deleted-at', 'Deleted At', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2805, 39, 1, 'edit-schedule', 'Edit Schedule', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2806, 39, 1, 'apply-for-leave', 'Apply for leave', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2807, 39, 1, 'i-want-to-login-at-schedule-time', 'I want to login at schedule time', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2808, 39, 1, 'view-emergency-shift', 'Load Emergency Shifts', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2809, 39, 1, 'employee-assigned-work-hours', '{{employee}}\'s Work Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2810, 39, 1, 'patients-total-assigned-hours', '{{patient}}\'s Total Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2811, 39, 1, 'actual_working_hour_per_week', 'Actual Working Hours Per Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2812, 39, 1, 'assigned_working_hour_per_week', 'Assigned Working Hours Per Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2813, 39, 1, 'working_percent', 'Working Percentage', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2814, 39, 1, 'actual-work-hours-per-day', 'Act.   Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2815, 39, 1, 'shift-hours-per-day', 'Shift Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2816, 39, 1, 'enable-date-range', 'Enable Date Range', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2817, 39, 1, 'per-week', 'Per Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2818, 39, 1, 'actual-total-work-hours-this-week', 'Actual Hours This Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2819, 39, 1, 'actual-total-work-hours-per-day', 'Actual Hours Assigned Per Day', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2820, 39, 1, 'assigned-total-work-hours-this-week', 'Assigned Hours This Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2821, 39, 1, 'extra-hours-this-week', 'Extra Hours This Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2822, 39, 1, 'extra-hours', 'Extra Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2823, 39, 1, 'remaining-hours-this-week', 'Remaining Hours This Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2824, 39, 1, 'remaining-hours', 'Rem. Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2825, 39, 1, 'rest-between-shift', 'Rest B/W Shifts', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2826, 39, 1, 'there-must-be-9-11-hours-gap-between-2-shifts', 'There must be 9 to 11 hours rest between 2 shifts', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2827, 39, 1, 'all-time-is-hh-mm', 'All time is shown in HH:MM format', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2828, 39, 1, 'remaining-hours-per-week', 'Remaining Hours Per Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2829, 39, 1, 'extra-hours-per-week', 'Extra Hours Per Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2830, 39, 1, 'schedule-type', 'Schedule Type', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2831, 39, 1, 'shift-type', 'Shift Type', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2832, 39, 1, 'select-company-types', 'Select Company Types', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2833, 39, 1, 'enter-copy-template-title', 'Enter Copy Template Title', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2834, 39, 1, 'schedule-template', 'Schedule Template', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2835, 39, 1, 'multiple-date', 'Multiple Date', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2836, 39, 1, 'random-date', 'Random Date', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2837, 39, 1, 'leave-calender', 'Leave Calendar', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2838, 39, 1, 'apply-leave', 'Apply Leave', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2839, 39, 1, 'select-date', 'Select Date', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2840, 39, 1, 'card-view', 'Card View', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2841, 39, 1, 'calendar-view', 'Calendar View', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2842, 39, 1, 'schedule-calender', 'Calender', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2843, 39, 1, 'copy-template', 'Copy Schedule', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2844, 39, 1, 'new-template-name', 'New Template Name', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2845, 39, 1, 'create-schedule-template', 'Create Schedule Template', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2846, 39, 1, 'template-title', 'Template Title', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2847, 39, 1, 'enabled-date-range-selection', 'Enable Date Range Selection', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2848, 39, 1, 'work-hours', 'Work Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2849, 39, 1, 'actual-work-hours', 'Actual Work Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2850, 39, 1, 'actual-hours', 'Actual Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2851, 39, 1, 'work-grade', 'Work Grade', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2852, 39, 1, 'total-hours', 'Total Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2853, 39, 1, 'shift-time', 'Shift Time', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2854, 39, 1, 'activate-template', 'Activate Template', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2855, 39, 1, 'rename', 'Rename', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2856, 39, 1, 'merge-template', 'Merge Template', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2857, 39, 1, 'template-calendar', 'Template Calendar', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2858, 39, 1, 'invalid-personal-number', 'Invalid personal number', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2859, 39, 1, 'emergency-shift', 'Emergency Shift', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2860, 39, 1, 'living-type', 'Living Type', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2861, 39, 1, 'group-living', 'Group Living', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2862, 39, 1, 'home-living', 'Home Living', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2863, 39, 1, 'single-living', 'Single Living', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2864, 39, 1, 'invalid-shift-start-end-time', 'Invalid Shift - Please check Start Time And/Or End Time', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2865, 39, 1, 'schedule-reports', 'Schedule Reports', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2866, 39, 1, 'total-emergency-hours', 'Emergency Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2867, 39, 1, 'assigned-hours', 'Assigned Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2868, 39, 1, 'emergency-hours', 'Emergency Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2869, 39, 1, 'schedules', 'Schedules', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2870, 39, 1, 'table-view', 'Table View', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2871, 39, 1, 'new-schedule-template', 'New Schedule Template', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2872, 39, 1, 'schedule-table-view-description', 'Please select a Template first to view the Schedules. Here you can view employees and their shifts all together, to change the schedule of any employee click on the employee name.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2873, 39, 1, 'schedule-template-table-view-description', 'Please select a Template first to view the Schedules. Here you can add employees and their shifts all together, to change the employee of any shift click on the shift.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2874, 39, 1, 'emergency-hours-exceeded-the-limit', 'Emergency hours are exceeded the allowed limit of 12h/week. Please adjust the emergency shift.', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2875, 39, 1, 'thisMonth', 'This Month', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2876, 39, 1, 'thisWeek', 'This Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2877, 39, 1, 'thisYear', 'This Year', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2878, 39, 1, 'prevMonth', 'Previous Month', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2879, 39, 1, 'nextMonth', 'Next Month', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2880, 39, 1, 'date_filter', 'Select Range', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2881, 39, 1, 'please-select-schedule-first', 'Please select a Schedule Template First', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2882, 39, 1, 'from-date', 'From', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2883, 39, 1, 'to-date', 'To', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2884, 39, 1, 'all-employee', 'All Employee', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2885, 39, 1, 'report-employee', 'Report Employee', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2886, 39, 1, 'report-with-week', 'Report With Week', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2887, 39, 1, 'obe-hours', 'Obe Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2888, 39, 1, 'vocation-hours', 'vocation Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2889, 39, 1, 'regular-hour', 'Regular Hours', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2890, 39, 1, 'please select start date stats will appear 4 week of start date', 'please select start date stats will appear 4 week of start date', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2891, 39, 1, 'cashier', 'Cashier', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2892, 39, 1, 'hours-stats', 'Hours Stats', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2893, 39, 1, 'hours-deduction', 'Hours Deduction', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2894, 39, 1, 'please select employees stats will appear as per employee data', 'please select employees stats will appear as per employee data', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2895, 39, 1, 'stats-report', 'Stats Report', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2896, 39, 1, 'employee-reports', 'Employee Reports', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2897, 39, 1, 'preset', 'Preset', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2898, 39, 1, 'template', 'Template', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2899, 39, 1, 'resize-table', 'Resize Table', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2900, 39, 1, 'full-screen', 'Full Screen', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2901, 39, 1, 'exit-full-screen', 'Exit Full Screen', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2902, 39, 1, 'schedule-vacation-short-form', 'S', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2903, 39, 1, 'schedule-leave-short-form', 'L', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2904, 39, 1, 'showing-of-employee-data', 'Showing {{current}} of {{total}} employees', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2905, 39, 1, 'per-page', 'Per Page', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2906, 39, 1, 'per-page-10', '10', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2907, 39, 1, 'per-page-20', '20', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2908, 39, 1, 'per-page-30', '30', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2909, 39, 1, 'per-page-40', '40', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2910, 39, 1, 'per-page-50', '50', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2911, 39, 1, '00-00', '00:00', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2912, 39, 1, 'assign-hours-per-day', 'Assign Hours Per Day', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2913, 39, 1, 'hours-approval', 'Hours Approval', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2914, 39, 1, 'copy-from', 'Copy From', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2915, 39, 1, 'shift-and-type', 'Shift And Type', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2916, 39, 1, 'please-select-shift-first', 'Please Select a template and shift first', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2917, 39, 1, 'please-select-a-employee-first', 'Please Select a Employee First', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2918, 39, 1, 'please-select-a-patient-first', 'Please Select a Patient First', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2919, 39, 1, 'please-select-a-patient-or-employee-first', 'Please Select a Patient or Employee First', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2920, 39, 1, 'replace-with', 'Replace with', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2921, 39, 1, 'no-employee', 'No Employee', 1, NULL, '2022-09-21 15:43:05', '2022-09-21 15:43:05'),
(2922, 39, 1, 'no-patient', 'No Patient', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2923, 39, 1, 'add-employee', 'Add Employee', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2924, 39, 1, 'agency-name', 'Agency Name', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2925, 39, 1, 'remove-employee', 'Remove Employee', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2926, 39, 1, 'add-more-employee', 'Add More', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2927, 39, 1, 'add-patient-and-employee', 'Add Patient & Emp.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2928, 39, 1, 'remove-patient-and-employee', 'Remove Patient & Emp.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2929, 39, 1, 'replace-patient-and-employee', 'Replace Patient & Emp.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2930, 39, 1, 'patient-and-employee-branch-not-matching', 'Patient and Employee Branch not matching', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2931, 39, 1, 'replace', 'Replace', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2932, 39, 1, 'remove-shift', 'Remove Shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2933, 39, 1, 'new-shift', 'New Shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2934, 39, 1, 'new-shift-with', 'New Shift with', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2935, 39, 1, 'new-shift-with-patient-and-employee', 'New Shift with Patient & Emp.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2936, 39, 1, 'schedule-with-same-time-and-employee', 'A Schedule with same Employee and Time already exists.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2937, 39, 1, 'employee-options', 'Employee Options', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2938, 39, 1, 'patient-options', 'Patient Options', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2939, 39, 1, 'actutal-hours', 'Actual Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2940, 39, 1, 'per-day', 'Per Day', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2941, 39, 1, 'per-month', 'Per Month', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2942, 39, 1, 'add-shift', 'Add Shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2943, 39, 1, 'add-more-shift', 'Add More Shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2944, 39, 1, 'patient-hours', 'Patient Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2945, 39, 1, 'the-joining-date-of-selected-employee-is', 'The Joining Date of selected employee is {{date}}, You can not create schedule before that date.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2946, 39, 1, 'verified', 'Verified', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2947, 39, 1, 'no-action', 'No Action', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2948, 39, 1, 'request-form', 'Request Form', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2949, 39, 1, 'joining-note', 'Shift start from the joining date and this date cannot be changed.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2950, 39, 1, 'replace-week', 'Replace Week', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2951, 39, 1, 'repeat-week', 'Copy Week', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2952, 39, 1, 'how-many-times', 'How many times?', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2953, 39, 1, 'select-week-to-replace', 'Select Week to replace.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2954, 39, 1, 'select-week-to-copy', 'Select Week to copy.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2955, 39, 1, 'start-from', 'Start From', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2956, 39, 1, 'week-from-to-data-will-be-replaced', 'Data of week {{from}} to week {{to}} will be replaced / copied.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2957, 39, 1, 'please-check-input-you-can-copy-data-up-to-52-weeks', 'Please check input, You can copy data up to 52 weeks', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2958, 39, 1, 'roles-added', 'Roles Added', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2959, 39, 1, 'verify-schedule-hours', 'Verify Schedule Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2960, 39, 1, 'verification-method-type', 'Verification Method Type', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2961, 39, 1, 'by-mobile-bankID', 'By Mobile BankID', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2962, 39, 1, 'normal', 'Normal', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2963, 39, 1, 'follow-ups', 'Follow Ups', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2964, 39, 1, 'please-give-rest-at-least-36-hours', 'This Employee has worked more than 40 hours this week. Please give at least 36 hours of rest before creating next shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2965, 39, 1, 'custom-shift', 'Custom Shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2966, 39, 1, 'source-type', 'Source Type', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2967, 39, 1, 'monthly', 'Monthly', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2968, 39, 1, 'weekly', 'Weekly', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2969, 39, 1, 'daily', 'Daily', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2970, 39, 1, 'please-make-at-lease-30-min-shift', 'Please Make At Lease 30 Min Shift', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2971, 39, 1, 'view-all', 'View All', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2972, 39, 1, 'view-inactive', 'View Inactive', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2973, 39, 1, 'view-active', 'View Active', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2974, 39, 1, 'view-in-calender', 'View In Calender', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2975, 39, 1, 'create-a-copy', 'Create a Copy', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2976, 39, 1, 'you-can-choose-a-template-to-replace-it-will-replace-the-selected-template-with-this-template', 'You can choose a template to replace. It will replace the selected template with this template', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2977, 39, 1, 'there-is-an-active-schedule-exist-please-change-the-schedule-before-activating-this-template', 'There is an active schedule exist, Please change the schedule before activating this template', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2978, 39, 1, 'schedule-exits-on-date-of-employee', 'Schedule exists of {{employee}} on {{date}}', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2979, 39, 1, 'view-template', 'View Template', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2980, 39, 1, 'table-reports', 'Table Reports', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2981, 39, 1, 'please-select-employee-first', 'Please Select Employee First', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2982, 39, 1, 'table-report', 'Table Report', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2983, 39, 1, 'contact-person-number', 'Contact Person Number', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2984, 39, 1, 'license-end-date', 'License End Date', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2985, 39, 1, 'please-select-employee', 'Please Select Employee', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2986, 39, 1, 'no-dates-available', 'No Dates Available', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2987, 39, 1, 'last-six-month-stats', 'Last Six Month Stats', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2988, 39, 1, 'scueess', 'Success', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2989, 39, 1, 'assigned-hour', 'Assigned Hour', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2990, 39, 1, 'executed-successfully', 'Executed Successfully', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2991, 39, 1, 'execution-failed', 'Execution Failed', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2992, 39, 1, 'activity-restored-successfully', 'Activity Restored Successfully', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2993, 39, 1, 'license-start-date', 'License Start Date', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2994, 39, 1, 'all-resources-related-to-this-activity-will-be-deleted-are-you-sure-to-proceed', 'All Resources Related To This Activity Will Be Deleted Are You Sure To Proceed', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2995, 39, 1, 'approve-leave', 'Approve Leave', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2996, 39, 1, 'view-licence-details', 'View License Details', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2997, 39, 1, 'licence-key', 'License Key', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2998, 39, 1, 'licence-filter', 'License Filter', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(2999, 39, 1, 'create-new-licence', 'Create New License', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3000, 39, 1, 'licences-description', 'Here you can create and manage your License for the respected companies.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3001, 39, 1, 'emergency-work-duration', 'Emergency Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3002, 39, 1, 'extra-work-duration', 'Extra Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3003, 39, 1, 'obe-work-duration', 'Obe Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3004, 39, 1, 'schedule-work-duration', 'Schedule Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3005, 39, 1, 'vacation-duration', 'Vacation Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3006, 39, 1, 'total-hour', 'Total Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3007, 39, 1, 'employee-on-leave', 'Employee On Leave, Please remove this shift or employee, otherwise it will override the leave.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3008, 39, 1, 'select-employee', 'Select Employee', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3009, 39, 1, 'no-data-available', 'No Data Available', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3010, 39, 1, 'email-notifications', 'Email & Notifications', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3011, 39, 1, 'patient-info', 'Patient Information', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3012, 39, 1, 'other-act', 'Other activites of patient', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3013, 39, 1, 'check-in', 'Check In', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3014, 39, 1, 'check-out', 'Check Out', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3015, 39, 1, 'stampling-details', 'Stampling Details', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3016, 39, 1, 'edit-stampling', 'Edit Stampling', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3017, 39, 1, 'walkin', 'WalkIn', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3018, 39, 1, 'in-time', 'In Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3019, 39, 1, 'out-time', 'Out Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3020, 39, 1, 'in-location', 'In Location', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3021, 39, 1, 'out-location', 'Out Location', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3022, 39, 1, 'stamp-filter', 'Stampling Filter', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3023, 39, 1, 'is-extra-hours-approved', 'Is extra hours approved', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3024, 39, 1, 'is-extra-hours-ov-hours', 'Is extra hours ov hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3025, 39, 1, 'is-scheduled-hours-ov-hours', 'Is scheduled hours ov hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3026, 39, 1, 'scheduled-hours-rate', 'Scheduled hours rate', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3027, 39, 1, 'extra-hours-sum', 'Extra hours sum', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3028, 39, 1, 'scheduled-hours-sum', 'Scheduled hours sum', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3029, 39, 1, 'reason-for-early-out', 'Reason for early out', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3030, 39, 1, 'reason-for-late-in', 'Reason for late in', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3031, 39, 1, 'reason-for-early-in', 'Reason for early in', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3032, 39, 1, 'reason-for-late-out', 'Reason for late out', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3033, 39, 1, 'in-location-ip', 'In Location IP', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3034, 39, 1, 'out-location-ip', 'Out Location IP', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3035, 39, 1, 'reason_for_early_in', 'Reason for early in', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3036, 39, 1, 'reason_for_early_out', 'Reason for early out', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3037, 39, 1, 'reason_for_late_in', 'Reason for late in', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3038, 39, 1, 'reason_for_late_out', 'Reason for late out', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3039, 39, 1, 'assign-work', 'Assign Work', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3040, 39, 1, 'verify', 'Verify', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3041, 39, 1, 'please-check-dates', 'Please Check Dates', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3042, 39, 1, 'stampling-report', 'Stampling Report', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3043, 39, 1, 'no-schedules-this-month', 'No Schedules This Month', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3044, 39, 1, 'calender-view', 'Calender View', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3045, 39, 1, 'is-rest', 'Is Rest', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3046, 39, 1, 'rest-start-time', 'Rest Start Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3047, 39, 1, 'rest-end-time', 'Rest End Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3048, 39, 1, 'something-went-wrong', 'Something Went Wrong', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3049, 39, 1, 'shift-rest-hours', 'Rest', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3050, 39, 1, 'rest-hours-available', 'Rest Available', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3051, 39, 1, 'rest-from', 'Rest From', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3052, 39, 1, 'rest-to', 'Rest To', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3053, 39, 1, 'schedule-details', 'Schedule Details', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3054, 39, 1, 'shift-name', 'Shift Name', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3055, 39, 1, 'shift-end-time', 'Shift End Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3056, 39, 1, 'shift-start-time', 'Shift Start Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3057, 39, 1, 'leave-approved', 'Leave Approved', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3058, 39, 1, 'shift_start_time', 'Shift Start Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3059, 39, 1, 'shift_end_time', 'Shift End Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3060, 39, 1, 'stamp_in_time', 'Stamp-In-Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3061, 39, 1, 'stamp_out_time', 'Stamp-Out-Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3062, 39, 1, 'stampling_hours', 'Stampling Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3063, 39, 1, 'remaining_hours', 'Remaining Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3064, 39, 1, 'shift_start_time-end-time', 'Shift Start - End Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3065, 39, 1, 'total_ob_hours', 'Total OB Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3066, 39, 1, 'stampling-time', 'Stampling Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3067, 39, 1, 'you-will-be-relaxed', 'Your relaxation time will be', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3068, 39, 1, 'relaxation-time', 'Relaxation Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3069, 39, 1, 'stampling-reports', 'Stampling Reports', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3070, 39, 1, 'shiftWise-report', 'ShiftWise Report', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3071, 39, 1, 'dayWise-report', 'DayWise Report', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3072, 39, 1, 'total_schedule_work_done', 'Total Schedule Work Done', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3073, 39, 1, 'total_extra_work_done', 'Total Extra Work Done', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3074, 39, 1, 'total_ob_work_done', 'Total OB Work Done', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3075, 39, 1, 'total_hour', 'Total Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3076, 39, 1, 'rest-in-time', 'Rest In Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3077, 39, 1, 'rest-out-time', 'Rest Out Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3078, 39, 1, 'total_stampling_hours', 'Total Stampling Hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3079, 39, 1, 'stampout', 'StampOut', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3080, 39, 1, 'stampin', 'Not StampOut', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3081, 39, 1, 'stampling_reports', 'Stampling Reports', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3082, 39, 1, 'change-password', 'Change Password', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3083, 39, 1, 'current-password', 'Current Password', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3084, 39, 1, 'confirm-new-password', 'Confirm New Password', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3085, 39, 1, 'check_all', 'Check All', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3086, 39, 1, 'uncheck_all', 'Uncheck All', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3087, 39, 1, 'stampling-type', 'Stampling Type', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3088, 39, 1, 'schedule-time', 'Schedule Time', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3089, 39, 1, 'you-can-choose-a-xlsx-file-to-import', 'You can choose a .xlsx file to import the Ov Hours.', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3090, 39, 1, 'import-ov', 'Import Obe', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3091, 39, 1, 'ob-hours-rate', 'OB hours rate', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3092, 39, 1, 'extra-hours-rate', 'Extra hours rate', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3093, 39, 1, 'total-extra-hours', 'Total extra hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3094, 39, 1, 'total-ob-hours', 'Total OB hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3095, 39, 1, 'total-schedule-hours', 'Total schedule hours', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3096, 39, 1, 'total-working-percent', 'Total working percentage', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3097, 39, 1, 'bank-id-filter', 'BankId Filter', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3098, 39, 1, 'ip-address', 'IP Address', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3099, 39, 1, 'request-from', 'Request From', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3100, 39, 1, 'personnel-number', 'Personnel Number', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3101, 39, 1, 'session-Id', 'Session ID', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3102, 39, 1, 'file-access-log', 'File Access Log', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3103, 39, 1, 'log-menu', 'Log Menu', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3104, 39, 1, 'file-access-log-filter', 'File Access Log Filter', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3105, 39, 1, 'file-name', 'File Name', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3106, 39, 1, 'sms-log-filter', 'SMS Log Filter', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3107, 40, 1, 'employees', 'Employees', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3108, 40, 1, 'email', 'Email', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3109, 40, 1, 'full_name', 'Full Name', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3110, 40, 1, 'personal_number', 'Personal Number', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3111, 40, 1, 'apply', 'Apply ', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3112, 40, 1, 'clear', 'Clear', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3113, 40, 1, 'name', 'Name', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06'),
(3114, 40, 1, 'contact_number', 'Contact Number', 1, NULL, '2022-09-21 15:43:06', '2022-09-21 15:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `title`, `value`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(2, 'Swedish', 'sw', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(345, '0000_00_00_000000_create_websockets_statistics_entries_table', 1),
(346, '2013_11_29_090604_create_user_types_table', 1),
(347, '2014_10_11_000000_create_countries_table', 1),
(348, '2014_10_12_000000_create_users_table', 1),
(349, '2014_10_12_100000_create_password_resets_table', 1),
(350, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(351, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(352, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(353, '2016_06_01_000004_create_oauth_clients_table', 1),
(354, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(355, '2019_08_19_000000_create_failed_jobs_table', 1),
(356, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(357, '2019_12_14_000002_create_licence_key_management_table', 1),
(358, '2019_12_14_000003_create_licence_histories_table', 1),
(359, '2020_05_04_072751_create_device_login_history_table', 1),
(360, '2021_02_17_120645_create_activity_log_table', 1),
(361, '2021_11_29_091615_create_category_types_table', 1),
(362, '2021_11_29_092659_create_category_masters_table', 1),
(363, '2021_11_29_093256_create_modules_table', 1),
(364, '2021_11_29_095529_create_company_types_table', 1),
(365, '2021_11_29_095954_create_packages_table', 1),
(366, '2021_11_29_102053_create_departments_table', 1),
(367, '2021_11_29_103700_create_subscriptions_table', 1),
(368, '2021_11_29_104547_create_bank_details_table', 1),
(369, '2021_11_29_105020_create_salary_details_table', 1),
(370, '2021_11_29_105518_create_company_work_shifts_table', 1),
(371, '2021_11_29_110514_create_shift_assignes_table', 1),
(372, '2021_11_29_112309_create_ip_templates_table', 1),
(373, '2021_11_29_112403_create_permission_tables', 1),
(374, '2021_11_29_112653_create_patient_implementation_plans_table', 1),
(375, '2021_11_29_114155_create_ip_follow_ups_table', 1),
(376, '2021_11_29_115027_create_ip_assigne_to_employees_table', 1),
(377, '2021_11_29_115316_create_ip_follow_up_creations_table', 1),
(378, '2021_11_29_120238_create_activity_classifications_table', 1),
(379, '2021_11_29_132427_create_notifications_table', 1),
(380, '2021_11_30_053633_create_activities_table', 1),
(381, '2021_11_30_060704_create_activity_assignes_table', 1),
(382, '2021_11_30_060948_create_journals_table', 1),
(383, '2021_11_30_061031_create_deviations_table', 1),
(384, '2021_11_30_062558_create_messages_table', 1),
(385, '2021_11_30_062758_create_comments_table', 1),
(386, '2021_11_30_063047_create_folders_table', 1),
(387, '2021_11_30_063404_create_files_table', 1),
(388, '2021_11_30_064230_create_request_for_approvals_table', 1),
(389, '2021_12_09_112838_create_assigne_modules_table', 1),
(390, '2021_12_10_074431_create_groups_table', 1),
(391, '2021_12_10_074457_create_languages_table', 1),
(392, '2021_12_10_074458_create_labels_table', 1),
(393, '2021_12_21_061031_add_clm_to_permissions_table', 1),
(394, '2021_12_21_061223_add_clm_to_roles_table', 1),
(395, '2022_02_04_114351_create_personal_info_during_ips_table', 1),
(396, '2022_02_05_073407_create_employee_types_table', 1),
(397, '2022_02_10_114225_create_sms_logs_table', 1),
(398, '2022_02_10_114515_create_mobile_bank_id_login_logs_table', 1),
(399, '2022_02_11_083834_create_email_templates_table', 1),
(400, '2022_03_10_104452_create_agency_weekly_hours_table', 1),
(401, '2022_03_10_115320_create_agencies_table', 1),
(402, '2022_03_16_063825_create_questions_table', 1),
(403, '2022_03_22_070436_create_followup_completes_table', 1),
(404, '2022_03_24_134035_create_words_table', 1),
(405, '2022_03_24_134145_create_paragraphs_table', 1),
(406, '2022_03_25_085857_create_tasks_table', 1),
(407, '2022_03_25_103113_create_assign_tasks_table', 1),
(408, '2022_03_25_125540_create_emergency_contacts_table', 1),
(409, '2022_03_29_112256_create_company_settings_table', 1),
(410, '2022_04_07_072424_create_user_type_has_permissions_table', 1),
(411, '2022_04_16_075808_create_activity_options_table', 1),
(412, '2022_04_19_095439_create_patient_information_table', 1),
(413, '2022_04_19_105417_create_activity_time_logs_table', 1),
(414, '2022_04_28_124537_create_admin_files_table', 1),
(415, '2022_04_29_154456_create_file_access_logs_table', 1),
(416, '2022_05_04_093215_create_journal_logs_table', 1),
(417, '2022_05_04_130158_create_journal_actions_table', 1),
(418, '2022_05_04_140111_create_journal_action_logs_table', 1),
(419, '2022_05_05_112704_create_patient_cashiers_table', 1),
(420, '2022_05_17_084400_create_bookmark_masters_table', 1),
(421, '2022_05_17_085500_create_bookmarks_table', 1),
(422, '2022_06_01_070249_create_schedule_templates_table', 1),
(423, '2022_06_01_124633_create_o_v_hours_table', 1),
(424, '2022_06_01_135653_create_employee_assigned_working_hours_table', 1),
(425, '2022_06_02_101523_create_schedules_table', 1),
(426, '2022_06_02_120642_create_stamplings_table', 1),
(427, '2022_06_18_094736_create_user_scheduled_dates_table', 1),
(428, '2022_07_14_110557_create_schedule_template_data_table', 1),
(429, '2022_07_19_105210_create_module_requests_table', 1),
(430, '2022_08_27_173145_create_schedule_stampling_datewise_reports_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mobile_bank_id_login_logs`
--

CREATE TABLE `mobile_bank_id_login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` int(11) NOT NULL COMMENT 'comes from users table (user company id)',
  `sessionId` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `personnel_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_from` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_info` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(20, 'App\\Models\\User', 4),
(22, 'App\\Models\\User', 5),
(23, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 'Activity', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(2, 'Journal', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(3, 'Deviation', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(4, 'Schedule', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(5, 'Stampling', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `module_requests`
--

CREATE TABLE `module_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `modules` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_comment` text COLLATE utf8mb4_unicode_ci,
  `reply_comment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_date` date DEFAULT NULL,
  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0:pending,1:approved,2:rejected',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'success' COMMENT 'success, failed, warning, primary, secondary, error, alert, info',
  `device_platform` tinyint(1) DEFAULT NULL COMMENT '1:android,2:ios',
  `user_type` int(11) DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_id` int(11) DEFAULT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT '0',
  `extra_param` text COLLATE utf8mb4_unicode_ci,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `top_most_parent_id`, `user_id`, `sender_id`, `device_id`, `status_code`, `device_platform`, `user_type`, `module`, `event`, `title`, `sub_title`, `message`, `image_url`, `screen`, `data_id`, `read_status`, `extra_param`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 'patient', NULL, 2, 2, NULL, 'info', NULL, 2, 'user', 'created', 'Patient has Created', NULL, 'Dear TS Corp, new patient TS Arvida Fahlgren has been added.', '', 'list', 4, 0, '{\"name\":\"TS Arvida Fahlgren\"}', NULL, '2022-09-21 12:25:58', '2022-09-21 12:25:58');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('6377f40d4a20297446afa6b30f7ec24d46fb1a4961507233aad01418e8d5468cb5e0345a518ce98a', 1, 1, 'authToken', '[]', 0, '2022-09-21 12:14:03', '2022-09-21 12:14:03', '2022-09-22 12:14:03'),
('648d8a1f11e9e59580459df69b6970c34176bfde9ec58a8e8b45e63502a1583384dfbce79fb4971b', 1, 1, 'authToken', '[]', 1, '2022-09-21 12:15:11', '2022-09-21 12:15:11', '2022-09-22 12:15:11'),
('87c55e113ed4cb804381447fe7e7c6516a02e4ad6fd67220b4e86f00b7e9e66da5b37d3f0dca1d47', 2, 1, 'authToken', '[]', 0, '2022-09-21 12:19:43', '2022-09-21 12:19:43', '2022-09-22 12:19:43'),
('b786e1f3ad5a69c2d522b7d72a25b2df9fa4f4b3f2f32c3762f7a721589a586c2bef9fc10c6bbc71', 2, 1, 'authToken', '[]', 0, '2022-09-21 12:20:09', '2022-09-21 12:20:09', '2022-09-22 12:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'ACEUSS Personal Access Client', 'ZRjB1pm2kGQPjsCGSf1mmJVgqerZA3EcAZ45FTO6', NULL, 'http://localhost', 1, 0, 0, '2022-09-21 10:13:11', '2022-09-21 10:13:11'),
(2, NULL, 'ACEUSS Password Grant Client', '9DQUmEUka0fJ7P3wkeXdIsUuDcMdilql2nBzs6sh', 'users', 'http://localhost', 0, 1, 0, '2022-09-21 10:13:11', '2022-09-21 10:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2022-09-21 10:13:11', '2022-09-21 10:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `o_v_hours`
--

CREATE TABLE `o_v_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `ob_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_mode` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `is_on_offer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `discount_type` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '1:Percentage ,2:Direct Value',
  `discount_value` int(11) NOT NULL DEFAULT '0',
  `discounted_price` double NOT NULL DEFAULT '0',
  `validity_in_days` int(11) NOT NULL,
  `number_of_patients` int(11) NOT NULL,
  `number_of_employees` int(11) NOT NULL,
  `bankid_charges` double DEFAULT NULL,
  `sms_charges` double DEFAULT NULL,
  `is_sms_enable` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `is_enable_bankid_charges` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive,2:Deleted',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `price`, `is_on_offer`, `discount_type`, `discount_value`, `discounted_price`, `validity_in_days`, `number_of_patients`, `number_of_employees`, `bankid_charges`, `sms_charges`, `is_sms_enable`, `is_enable_bankid_charges`, `status`, `entry_mode`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Basic pack', 540, 1, '1', 67, 178.2, 100, 100, 50, NULL, NULL, 0, 0, 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `paragraphs`
--

CREATE TABLE `paragraphs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `paragraph` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paragraphs`
--

INSERT INTO `paragraphs` (`id`, `top_most_parent_id`, `paragraph`, `created_at`, `updated_at`) VALUES
(1, NULL, 'When you have a dream, you\'ve got to grab it and never let go.', '2022-08-03 15:31:55', '2022-08-03 15:31:55'),
(2, NULL, 'Nothing is impossible. The word itself says \'I\'m possible!\'', '2022-08-03 15:32:05', '2022-08-03 15:32:05'),
(3, NULL, 'There is nothing impossible to they who will try.', '2022-08-03 15:32:16', '2022-08-03 15:32:16'),
(4, NULL, 'The bad news is time flies. The good news is you\'re the pilot.', '2022-08-03 15:32:23', '2022-08-03 15:32:23'),
(5, NULL, 'Life has got all those twists and turns. You\'ve got to hold on tight and off you go.', '2022-08-03 15:32:31', '2022-08-03 15:32:31'),
(12, NULL, 'Better Care and Better Understanding.', '2022-08-04 13:58:38', '2022-08-04 13:58:38'),
(13, NULL, 'Empowering People to Improve Their Lives', '2022-08-04 13:59:43', '2022-08-16 08:04:55'),
(17, NULL, 'Work', '2022-08-26 14:49:14', '2022-08-26 14:49:14');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_cashiers`
--

CREATE TABLE `patient_cashiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receipt_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1:IN, 2:OUT',
  `amount` double(9,2) DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) NOT NULL,
  `entry_mode` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_implementation_plans`
--

CREATE TABLE `patient_implementation_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `action_by` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `save_as_template` tinyint(1) NOT NULL DEFAULT '0',
  `goal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limitations` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limitation_details` text COLLATE utf8mb4_unicode_ci,
  `how_support_should_be_given` text COLLATE utf8mb4_unicode_ci,
  `week_days` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `how_many_time` int(11) DEFAULT NULL,
  `when_during_the_day` longtext COLLATE utf8mb4_unicode_ci,
  `who_give_support` text COLLATE utf8mb4_unicode_ci,
  `sub_goal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_goal_details` text COLLATE utf8mb4_unicode_ci,
  `sub_goal_selected` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overall_goal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overall_goal_details` text COLLATE utf8mb4_unicode_ci,
  `body_functions` text COLLATE utf8mb4_unicode_ci,
  `personal_factors` text COLLATE utf8mb4_unicode_ci,
  `health_conditions` text COLLATE utf8mb4_unicode_ci,
  `other_factors` text COLLATE utf8mb4_unicode_ci,
  `treatment` text COLLATE utf8mb4_unicode_ci,
  `working_method` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `documents` text COLLATE utf8mb4_unicode_ci,
  `reason_for_editing` text COLLATE utf8mb4_unicode_ci,
  `approved_date` date DEFAULT NULL,
  `action_date` date DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `step_one` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_two` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_three` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_four` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_five` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_six` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_seven` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_latest_entry` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_information`
--

CREATE TABLE `patient_information` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `special_information` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institute_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institute_contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institute_contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institute_full_address` text COLLATE utf8mb4_unicode_ci,
  `institute_week_days` text COLLATE utf8mb4_unicode_ci,
  `classes_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `classes_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_full_address` text COLLATE utf8mb4_unicode_ci,
  `company_week_days` text COLLATE utf8mb4_unicode_ci,
  `from_timing` text COLLATE utf8mb4_unicode_ci,
  `to_timing` text COLLATE utf8mb4_unicode_ci,
  `aids` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `another_activity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `another_activity_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `another_activity_contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activitys_contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activitys_full_address` text COLLATE utf8mb4_unicode_ci,
  `another_activity_start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `another_activity_end_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `week_days` text COLLATE utf8mb4_unicode_ci,
  `issuer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_of_hours` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `period` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_information`
--

INSERT INTO `patient_information` (`id`, `patient_id`, `special_information`, `institute_name`, `institute_contact_number`, `institute_contact_person`, `institute_full_address`, `institute_week_days`, `classes_from`, `classes_to`, `company_name`, `company_contact_person`, `company_contact_number`, `company_full_address`, `company_week_days`, `from_timing`, `to_timing`, `aids`, `another_activity`, `another_activity_name`, `another_activity_contact_person`, `activitys_contact_number`, `activitys_full_address`, `another_activity_start_time`, `another_activity_end_time`, `week_days`, `issuer_name`, `number_of_hours`, `period`, `created_at`, `updated_at`) VALUES
(1, 4, 'Empowering People to Improve Their Lives', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Life has got all those twists and turns. You\'ve got to hold on tight and off you go.', 'short-term', NULL, NULL, NULL, NULL, NULL, NULL, 'null', NULL, NULL, NULL, '2022-09-21 12:25:55', '2022-09-21 12:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `se_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `belongs_to` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:Admin,2:Company,3:Other',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `group_name`, `se_name`, `description`, `belongs_to`, `created_at`, `updated_at`, `entry_mode`) VALUES
(1, 'companies-browse', 'api', 'companies', 'companies-browse', NULL, 1, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(2, 'companies-read', 'api', 'companies', 'companies-read', NULL, 1, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(3, 'companies-add', 'api', 'companies', 'companies-create', NULL, 1, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(4, 'companies-edit', 'api', 'companies', 'companies-edit', NULL, 1, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(5, 'companies-delete', 'api', 'companies', 'companies-delete', NULL, 1, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(6, 'role-browse', 'api', 'role', 'role-browse', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(7, 'role-read', 'api', 'role', 'role-read', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(8, 'role-add', 'api', 'role', 'role-add', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(9, 'role-edit', 'api', 'role', 'role-edit', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(10, 'role-delete', 'api', 'role', 'role-delete', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(11, 'dashboard-browse', 'api', 'dashboard', 'dashboard-browse', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(12, 'notifications-browse', 'api', 'notifications', 'notifications-browse', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(13, 'notifications-add', 'api', 'notifications', 'notifications-add', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(14, 'notifications-edit', 'api', 'notifications', 'notifications-edit', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(15, 'notifications-delete', 'api', 'notifications', 'notifications-delete', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(16, 'requests-browse', 'api', 'requests', 'requests-browse', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(17, 'requests-add', 'api', 'requests', 'requests-add', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(18, 'requests-read', 'api', 'requests', 'requests-read', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(19, 'requests-edit', 'api', 'requests', 'requests-edit', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(20, 'requests-delete', 'api', 'requests', 'requests-delete', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(21, 'users-browse', 'api', 'users', 'users-browse', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(22, 'users-add', 'api', 'users', 'users-add', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(23, 'users-read', 'api', 'users', 'users-read', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(24, 'users-edit', 'api', 'users', 'users-edit', NULL, 3, '2022-07-14 06:40:10', '2022-07-14 06:40:10', NULL),
(25, 'users-delete', 'api', 'users', 'users-delete', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(26, 'categories-browse', 'api', 'categories', 'categories-browse', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(27, 'categories-add', 'api', 'categories', 'categories-add', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(28, 'categories-read', 'api', 'categories', 'categories-read', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(29, 'categories-edit', 'api', 'categories', 'categories-edit', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(30, 'categories-delete', 'api', 'categories', 'categories-delete', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(31, 'licences-browse', 'api', 'licences', 'licences-browse', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(32, 'licences-add', 'api', 'licences', 'licences-add', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(33, 'licences-read', 'api', 'licences', 'licences-read', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(34, 'licences-edit', 'api', 'licences', 'licences-edit', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(35, 'licences-delete', 'api', 'licences', 'licences-delete', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(36, 'modules-browse', 'api', 'modules', 'modules-browse', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(37, 'modules-add', 'api', 'modules', 'modules-add', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(38, 'modules-read', 'api', 'modules', 'modules-read', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(39, 'modules-edit', 'api', 'modules', 'modules-edit', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(40, 'modules-delete', 'api', 'modules', 'modules-delete', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(41, 'packages-browse', 'api', 'packages', 'packages-browse', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(42, 'packages-add', 'api', 'packages', 'packages-add', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(43, 'packages-read', 'api', 'packages', 'packages-read', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(44, 'packages-edit', 'api', 'packages', 'packages-edit', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(45, 'packages-delete', 'api', 'packages', 'packages-delete', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(46, 'userType-browse', 'api', 'userType', 'userType-browse', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(47, 'userType-add', 'api', 'userType', 'userType-add', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(48, 'userType-read', 'api', 'userType', 'userType-read', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(49, 'userType-edit', 'api', 'userType', 'userType-edit', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(50, 'userType-delete', 'api', 'userType', 'userType-delete', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(51, 'companyType-browse', 'api', 'companyType', 'companyType-browse', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(52, 'companyType-add', 'api', 'companyType', 'companyType-add', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(53, 'companyType-read', 'api', 'companyType', 'companyType-read', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(54, 'companyType-edit', 'api', 'companyType', 'companyType-edit', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(55, 'companyType-delete', 'api', 'companyType', 'companyType-delete', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(56, 'settings-browse', 'api', 'settings', 'settings-browse', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(57, 'settings-add', 'api', 'settings', 'settings-add', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(58, 'settings-read', 'api', 'settings', 'settings-read', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(59, 'settings-edit', 'api', 'settings', 'settings-edit', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(60, 'settings-delete', 'api', 'settings', 'settings-delete', NULL, 3, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(61, 'adminEmployee-browse', 'api', 'adminEmployee', 'adminEmployee-browse', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(62, 'adminEmployee-add', 'api', 'adminEmployee', 'adminEmployee-add', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(63, 'adminEmployee-read', 'api', 'adminEmployee', 'adminEmployee-read', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(64, 'adminEmployee-edit', 'api', 'adminEmployee', 'adminEmployee-edit', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(65, 'adminEmployee-delete', 'api', 'adminEmployee', 'adminEmployee-delete', NULL, 1, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(66, 'employees-browse', 'api', 'employees', 'employees-browse', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(67, 'employees-add', 'api', 'employees', 'employees-add', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(68, 'employees-read', 'api', 'employees', 'employees-read', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(69, 'employees-edit', 'api', 'employees', 'employees-edit', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(70, 'employees-delete', 'api', 'employees', 'employees-delete', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(71, 'patients-browse', 'api', 'patients', 'patients-browse', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(72, 'patients-add', 'api', 'patients', 'patients-add', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(73, 'patients-read', 'api', 'patients', 'patients-read', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(74, 'patients-edit', 'api', 'patients', 'patients-edit', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(75, 'patients-delete', 'api', 'patients', 'patients-delete', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(76, 'departments-browse', 'api', 'departments', 'departments-browse', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(77, 'departments-add', 'api', 'departments', 'departments-add', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(78, 'departments-read', 'api', 'departments', 'departments-read', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(79, 'departments-edit', 'api', 'departments', 'departments-edit', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(80, 'departments-delete', 'api', 'departments', 'departments-delete', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(81, 'journal-browse', 'api', 'journal', 'journal-browse', NULL, 2, '2022-07-14 06:40:11', '2022-07-14 06:40:11', NULL),
(82, 'journal-add', 'api', 'journal', 'journal-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(83, 'journal-read', 'api', 'journal', 'journal-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(84, 'journal-edit', 'api', 'journal', 'journal-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(85, 'journal-delete', 'api', 'journal', 'journal-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(86, 'journal-action', 'api', 'journal', 'journal-action', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(87, 'journal-stats-view', 'api', 'journal', 'journal-stats-view', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(88, 'deviation-browse', 'api', 'deviation', 'deviation-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(89, 'deviation-add', 'api', 'deviation', 'deviation-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(90, 'deviation-read', 'api', 'deviation', 'deviation-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(91, 'deviation-edit', 'api', 'deviation', 'deviation-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(92, 'deviation-delete', 'api', 'deviation', 'deviation-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(93, 'schedule-browse', 'api', 'schedule', 'schedule-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(94, 'schedule-add', 'api', 'schedule', 'schedule-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(95, 'schedule-read', 'api', 'schedule', 'schedule-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(96, 'schedule-edit', 'api', 'schedule', 'schedule-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(97, 'schedule-delete', 'api', 'schedule', 'schedule-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(98, 'persons-browse', 'api', 'persons', 'persons-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(99, 'persons-add', 'api', 'persons', 'persons-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(100, 'persons-read', 'api', 'persons', 'persons-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(101, 'persons-edit', 'api', 'persons', 'persons-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(102, 'persons-delete', 'api', 'persons', 'persons-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(103, 'workShift-browse', 'api', 'workShift', 'workShift-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(104, 'workShift-add', 'api', 'workShift', 'workShift-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(105, 'workShift-read', 'api', 'workShift', 'workShift-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(106, 'workShift-edit', 'api', 'workShift', 'workShift-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(107, 'workShift-delete', 'api', 'workShift', 'workShift-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(108, 'branch-browse', 'api', 'branch', 'branch-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(109, 'branch-add', 'api', 'branch', 'branch-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(110, 'branch-read', 'api', 'branch', 'branch-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(111, 'branch-edit', 'api', 'branch', 'branch-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(112, 'branch-delete', 'api', 'branch', 'branch-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(113, 'ip-browse', 'api', 'ip', 'ip-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(114, 'ip-add', 'api', 'ip', 'ip-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(115, 'ip-read', 'api', 'ip', 'ip-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(116, 'ip-edit', 'api', 'ip', 'ip-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(117, 'ip-delete', 'api', 'ip', 'ip-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(118, 'followup-browse', 'api', 'followup', 'followup-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(119, 'followup-add', 'api', 'followup', 'followup-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(120, 'followup-read', 'api', 'followup', 'followup-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(121, 'followup-edit', 'api', 'followup', 'followup-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(122, 'followup-delete', 'api', 'followup', 'followup-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(123, 'activity-browse', 'api', 'activity', 'activity-browse', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(124, 'activity-add', 'api', 'activity', 'activity-add', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(125, 'activity-read', 'api', 'activity', 'activity-read', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(126, 'activity-edit', 'api', 'activity', 'activity-edit', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(127, 'activity-delete', 'api', 'activity', 'activity-delete', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(128, 'activity-stats', 'api', 'activity', 'activity-stats', NULL, 2, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(129, 'reports-delete', 'api', 'reports', 'reports-delete', NULL, 3, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(130, 'words-browse', 'api', 'words', 'words-browse', NULL, 3, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(131, 'words-add', 'api', 'words', 'words-add', NULL, 3, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(132, 'words-read', 'api', 'words', 'words-read', NULL, 3, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(133, 'words-edit', 'api', 'words', 'words-edit', NULL, 3, '2022-07-14 06:40:12', '2022-07-14 06:40:12', NULL),
(134, 'words-delete', 'api', 'words', 'words-delete', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(135, 'paragraphs-browse', 'api', 'paragraphs', 'paragraphs-browse', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(136, 'paragraphs-add', 'api', 'paragraphs', 'paragraphs-add', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(137, 'paragraphs-read', 'api', 'paragraphs', 'paragraphs-read', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(138, 'paragraphs-edit', 'api', 'paragraphs', 'paragraphs-edit', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(139, 'paragraphs-delete', 'api', 'paragraphs', 'paragraphs-delete', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(140, 'task-browse', 'api', 'task', 'task-browse', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(141, 'task-add', 'api', 'task', 'task-add', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(142, 'task-read', 'api', 'task', 'task-read', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(143, 'task-edit', 'api', 'task', 'task-edit', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(144, 'task-delete', 'api', 'task', 'task-delete', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(145, 'internalCom-read', 'api', 'command', 'internalCom-read', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(146, 'EmailTemplate-browse', 'api', 'EmailTemplate', 'EmailTemplate-browse', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(147, 'EmailTemplate-add', 'api', 'EmailTemplate', 'EmailTemplate-add', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(148, 'EmailTemplate-read', 'api', 'EmailTemplate', 'EmailTemplate-read', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(149, 'EmailTemplate-edit', 'api', 'EmailTemplate', 'EmailTemplate-edit', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(150, 'EmailTemplate-delete', 'api', 'EmailTemplate', 'EmailTemplate-delete', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(151, 'questions-browse', 'api', 'questions', 'questions-browse', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(152, 'questions-add', 'api', 'questions', 'questions-add', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(153, 'questions-read', 'api', 'questions', 'questions-read', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(154, 'questions-edit', 'api', 'questions', 'questions-edit', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(155, 'questions-delete', 'api', 'questions', 'questions-delete', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(156, 'isCategoryEditPermission-edit', 'api', 'isCategoryEditPermission', 'isCategoryEditPermission-edit', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(157, 'calendar-browse', 'api', 'calendar', 'calendar-browse', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(158, 'patientimport-add', 'api', 'import', 'patientimport-add', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(159, 'files-browse', 'api', 'files', 'files-browse', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(160, 'files-read', 'api', 'files', 'files-read', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(161, 'files-edit', 'api', 'files', 'files-edit', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(162, 'files-add', 'api', 'files', 'files-add', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(163, 'files-delete', 'api', 'files', 'files-delete', NULL, 3, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(164, 'import-language', 'api', 'language', 'import-language', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(165, 'trashed-activites', 'api', 'activity', 'trashed-activites', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(166, 'trashed-activites-permanent-delete', 'api', 'activity', 'trashed-activites-permanent-delete', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(167, 'trashed-activites-restore', 'api', 'activity', 'trashed-activites-restore', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(168, 'bookmark-read', 'api', 'bookmark', 'bookmark-read', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(169, 'bookmark-add', 'api', 'bookmark', 'bookmark-create', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(170, 'bookmark-edit', 'api', 'bookmark', 'bookmark-edit', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(171, 'bookmark-delete', 'api', 'bookmark', 'bookmark-delete', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(172, 'bookmark-browse', 'api', 'bookmark', 'bookmark-browse', NULL, 2, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(173, 'label-read', 'api', 'label', 'label-read', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(174, 'label-add', 'api', 'label', 'label-create', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(175, 'label-edit', 'api', 'label', 'label-edit', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(176, 'label-delete', 'api', 'label', 'label-delete', NULL, 1, '2022-07-14 06:40:13', '2022-07-14 06:40:13', NULL),
(177, 'label-browse', 'api', 'label', 'label-browse', NULL, 1, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(178, 'patient_cashiers', 'api', 'patient_cashiers', 'patient_cashiers', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(179, 'patient_cashier-add', 'api', 'patient_cashiers', 'patient_cashier-add', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(180, 'schedule-template-read', 'api', 'schedule-template', 'schedule-template-read', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(181, 'schedule-template-add', 'api', 'schedule-template', 'schedule-template-create', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(182, 'schedule-template-edit', 'api', 'schedule-template', 'schedule-template-edit', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(183, 'schedule-template-delete', 'api', 'schedule-template', 'schedule-template-delete', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(184, 'schedule-template-browse', 'api', 'schedule-template', 'schedule-template-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(185, 'hours-approval-read', 'api', 'hours-approval', 'hours-approval-read', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(186, 'hours-approval-add', 'api', 'hours-approval', 'hours-approval-create', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(187, 'hours-approval-edit', 'api', 'hours-approval', 'hours-approval-edit', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(188, 'hours-approval-delete', 'api', 'hours-approval', 'hours-approval-delete', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(189, 'hours-approval-browse', 'api', 'hours-approval', 'hours-approval-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(190, 'leave-read', 'api', 'leave', 'leave-read', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(191, 'leave-add', 'api', 'leave', 'leave-create', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(192, 'leave-edit', 'api', 'leave', 'leave-edit', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(193, 'leave-delete', 'api', 'leave', 'leave-delete', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(194, 'leave-browse', 'api', 'leave', 'leave-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(195, 'smsLog-browse', 'api', 'Log', 'smsLog-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(196, 'bankIdLog-browse', 'api', 'Log', 'bankIdLog-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(197, 'activityLog-browse', 'api', 'Log', 'activityLog-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(198, 'fileLog-browse', 'api', 'Log', 'fileLog-browse', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(199, 'activityLog-read', 'api', 'Log', 'activityLog-read', NULL, 2, '2022-07-14 06:40:14', '2022-07-14 06:40:14', NULL),
(200, 'files-upload-for-usertype', 'api', 'files', 'files-upload-for-usertype', NULL, 1, '2022-07-20 08:15:25', '2022-07-20 08:15:25', 'Web'),
(201, 'packages-view-company', 'api', 'packages', 'packages-view-company', NULL, 1, '2022-09-09 12:33:10', '2022-09-09 12:33:10', 'Web'),
(202, 'journal-print', 'api', 'print', 'journal-print', NULL, 1, '2022-09-09 12:35:22', '2022-09-09 12:35:22', 'Web'),
(203, 'deviation-print', 'api', 'print', 'deviation-print', NULL, 1, '2022-09-09 12:36:00', '2022-09-09 12:36:00', 'Web'),
(204, 'licences-assign', 'api', 'licences', 'licences-assign', NULL, 1, '2022-09-09 12:38:47', '2022-09-09 12:38:47', 'Web'),
(205, 'licences-expire', 'api', 'licences', 'licences-expire', NULL, 1, '2022-09-09 12:39:07', '2022-09-09 12:39:07', 'Web'),
(206, 'result', 'api', 'journal', 'result', NULL, 1, '2022-09-09 12:43:54', '2022-09-09 12:43:54', 'Web'),
(207, 'stampling-browse', 'api', 'stampling', 'stampling-browse', NULL, 1, '2022-09-10 11:55:56', '2022-09-10 11:55:56', 'Web'),
(208, 'stampling-add', 'api', 'stampling', 'stampling-add', NULL, 1, '2022-09-10 11:56:18', '2022-09-10 11:56:18', 'Web'),
(209, 'stampling-edit', 'api', 'stampling', 'stampling-edit', NULL, 1, '2022-09-10 11:56:41', '2022-09-10 11:56:41', 'Web'),
(210, 'stampling-delete', 'api', 'stampling', 'stampling-delete', NULL, 1, '2022-09-10 11:57:51', '2022-09-10 11:57:51', 'Web'),
(211, 'stampling-read', 'api', 'stampling', 'stampling-read', NULL, 1, '2022-09-10 11:58:12', '2022-09-10 11:58:12', 'Web'),
(212, 'investigation', 'api', 'investigation', 'investigation', NULL, 1, '2022-09-10 13:52:40', '2022-09-10 13:52:40', 'Web');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_info_during_ips`
--

CREATE TABLE `personal_info_during_ips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `follow_up_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_presented` tinyint(1) NOT NULL DEFAULT '0',
  `is_participated` tinyint(1) NOT NULL DEFAULT '0',
  `how_helped` text COLLATE utf8mb4_unicode_ci,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approval_requested` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_info_during_ips`
--

INSERT INTO `personal_info_during_ips` (`id`, `patient_id`, `ip_id`, `user_id`, `follow_up_id`, `is_presented`, `is_participated`, `how_helped`, `entry_mode`, `is_approval_requested`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, NULL, 5, NULL, 0, 0, NULL, NULL, 0, '2022-09-21 12:25:58', '2022-09-21 12:25:58', NULL),
(2, 4, NULL, 6, NULL, 0, 0, NULL, NULL, 0, '2022-09-21 12:26:00', '2022-09-21 12:26:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `group_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_visible` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:true,0:false',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_for_approvals`
--

CREATE TABLE `request_for_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requested_by` bigint(20) UNSIGNED DEFAULT NULL,
  `requested_to` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `request_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `request_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'if request_type_id is multiple then action performed according to this',
  `reason_for_requesting` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason_for_rejection` text COLLATE utf8mb4_unicode_ci,
  `other_info` text COLLATE utf8mb4_unicode_ci,
  `approved_date` date DEFAULT NULL,
  `approval_type` tinyint(4) DEFAULT NULL COMMENT '1:Manual, 2:Digital Signature, 3:Mobile Bank Id',
  `status` enum('0','1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0:inactive, 1:active, 2:approved ,3:rejected',
  `sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `se_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Det Default role',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `top_most_parent_id`, `user_type_id`, `name`, `guard_name`, `se_name`, `is_default`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'Admin', 'api', 'Super Admin', 0, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(2, NULL, 2, 'Company', 'api', 'Company', 0, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(3, NULL, 3, 'Employee', 'api', 'Employee', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(4, NULL, 4, 'Hospital', 'api', 'Hospital', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(5, NULL, 5, 'Nuser', 'api', 'Nuser', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(6, NULL, 6, 'Patient', 'api', 'Patient', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(7, NULL, 7, 'careTaker', 'api', 'careTaker', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(8, NULL, 8, 'FamilyMember', 'api', 'FamilyMember', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(9, NULL, 9, 'ContactPerson', 'api', 'ContactPerson', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(10, NULL, 10, 'careTakerFamily', 'api', 'careTakerFamily', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(11, NULL, 11, 'Branch', 'api', 'Branch', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(12, NULL, 12, 'Guardian', 'api', 'Guardian', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(13, NULL, 13, 'Presented', 'api', 'Presented', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(14, NULL, 14, 'Participated', 'api', 'Participated', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(15, NULL, 15, 'Other', 'api', 'Other', 1, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(16, NULL, 16, 'Admin Employee', 'api', 'Admin Employee', 0, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52'),
(17, 2, 3, '2-employee', 'api', 'Employee', 0, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(18, 2, 4, '2-hospital', 'api', 'Hospital', 0, 'web-0.0.1', '2022-09-21 12:16:35', '2022-09-21 12:16:35'),
(19, 2, 5, '2-nuser', 'api', 'Nuser', 0, 'web-0.0.1', '2022-09-21 12:16:35', '2022-09-21 12:16:35'),
(20, 2, 6, '2-patient', 'api', 'Patient', 0, 'web-0.0.1', '2022-09-21 12:16:35', '2022-09-21 12:16:35'),
(21, 2, 7, '2-caretaker', 'api', 'careTaker', 0, 'web-0.0.1', '2022-09-21 12:16:37', '2022-09-21 12:16:37'),
(22, 2, 8, '2-familymember', 'api', 'FamilyMember', 0, 'web-0.0.1', '2022-09-21 12:16:38', '2022-09-21 12:16:38'),
(23, 2, 9, '2-contactperson', 'api', 'ContactPerson', 0, 'web-0.0.1', '2022-09-21 12:16:39', '2022-09-21 12:16:39'),
(24, 2, 10, '2-caretakerfamily', 'api', 'careTakerFamily', 0, 'web-0.0.1', '2022-09-21 12:16:41', '2022-09-21 12:16:41'),
(25, 2, 11, '2-branch', 'api', 'Branch', 0, 'web-0.0.1', '2022-09-21 12:16:42', '2022-09-21 12:16:42'),
(26, 2, 12, '2-guardian', 'api', 'Guardian', 0, 'web-0.0.1', '2022-09-21 12:16:53', '2022-09-21 12:16:53'),
(27, 2, 13, '2-presented', 'api', 'Presented', 0, 'web-0.0.1', '2022-09-21 12:16:53', '2022-09-21 12:16:53'),
(28, 2, 14, '2-participated', 'api', 'Participated', 0, 'web-0.0.1', '2022-09-21 12:16:53', '2022-09-21 12:16:53'),
(29, 2, 15, '2-other', 'api', 'Other', 0, 'web-0.0.1', '2022-09-21 12:16:54', '2022-09-21 12:16:54'),
(30, 3, 3, '3-employee', 'api', 'Employee', 0, 'web-0.0.1', '2022-09-21 12:18:28', '2022-09-21 12:18:28'),
(31, 3, 4, '3-hospital', 'api', 'Hospital', 0, 'web-0.0.1', '2022-09-21 12:18:40', '2022-09-21 12:18:40'),
(32, 3, 5, '3-nuser', 'api', 'Nuser', 0, 'web-0.0.1', '2022-09-21 12:18:40', '2022-09-21 12:18:40'),
(33, 3, 6, '3-patient', 'api', 'Patient', 0, 'web-0.0.1', '2022-09-21 12:18:40', '2022-09-21 12:18:40'),
(34, 3, 7, '3-caretaker', 'api', 'careTaker', 0, 'web-0.0.1', '2022-09-21 12:18:41', '2022-09-21 12:18:41'),
(35, 3, 8, '3-familymember', 'api', 'FamilyMember', 0, 'web-0.0.1', '2022-09-21 12:18:43', '2022-09-21 12:18:43'),
(36, 3, 9, '3-contactperson', 'api', 'ContactPerson', 0, 'web-0.0.1', '2022-09-21 12:18:45', '2022-09-21 12:18:45'),
(37, 3, 10, '3-caretakerfamily', 'api', 'careTakerFamily', 0, 'web-0.0.1', '2022-09-21 12:18:46', '2022-09-21 12:18:46'),
(38, 3, 11, '3-branch', 'api', 'Branch', 0, 'web-0.0.1', '2022-09-21 12:18:48', '2022-09-21 12:18:48'),
(39, 3, 12, '3-guardian', 'api', 'Guardian', 0, 'web-0.0.1', '2022-09-21 12:19:01', '2022-09-21 12:19:01'),
(40, 3, 13, '3-presented', 'api', 'Presented', 0, 'web-0.0.1', '2022-09-21 12:19:01', '2022-09-21 12:19:01'),
(41, 3, 14, '3-participated', 'api', 'Participated', 0, 'web-0.0.1', '2022-09-21 12:19:01', '2022-09-21 12:19:01'),
(42, 3, 15, '3-other', 'api', 'Other', 0, 'web-0.0.1', '2022-09-21 12:19:01', '2022-09-21 12:19:01');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(142, 1),
(143, 1),
(144, 1),
(146, 1),
(147, 1),
(148, 1),
(149, 1),
(150, 1),
(159, 1),
(160, 1),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(168, 1),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(195, 1),
(196, 1),
(197, 1),
(198, 1),
(199, 1),
(204, 1),
(205, 1),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2),
(88, 2),
(89, 2),
(90, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(99, 2),
(100, 2),
(101, 2),
(102, 2),
(103, 2),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 2),
(109, 2),
(110, 2),
(111, 2),
(112, 2),
(113, 2),
(114, 2),
(115, 2),
(116, 2),
(117, 2),
(118, 2),
(119, 2),
(120, 2),
(121, 2),
(122, 2),
(123, 2),
(124, 2),
(125, 2),
(126, 2),
(127, 2),
(128, 2),
(129, 2),
(130, 2),
(131, 2),
(132, 2),
(133, 2),
(134, 2),
(135, 2),
(136, 2),
(137, 2),
(138, 2),
(139, 2),
(140, 2),
(141, 2),
(142, 2),
(143, 2),
(144, 2),
(145, 2),
(151, 2),
(152, 2),
(153, 2),
(154, 2),
(155, 2),
(156, 2),
(157, 2),
(158, 2),
(159, 2),
(160, 2),
(165, 2),
(166, 2),
(167, 2),
(168, 2),
(169, 2),
(170, 2),
(171, 2),
(172, 2),
(178, 2),
(179, 2),
(180, 2),
(181, 2),
(182, 2),
(183, 2),
(184, 2),
(185, 2),
(186, 2),
(187, 2),
(188, 2),
(189, 2),
(190, 2),
(191, 2),
(192, 2),
(193, 2),
(194, 2),
(200, 2),
(201, 2),
(202, 2),
(203, 2),
(206, 2),
(207, 2),
(208, 2),
(209, 2),
(210, 2),
(211, 2),
(212, 2),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(66, 3),
(67, 3),
(68, 3),
(69, 3),
(70, 3),
(71, 3),
(72, 3),
(73, 3),
(74, 3),
(75, 3),
(76, 3),
(77, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 3),
(84, 3),
(85, 3),
(86, 3),
(87, 3),
(88, 3),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3),
(96, 3),
(97, 3),
(98, 3),
(99, 3),
(100, 3),
(101, 3),
(102, 3),
(103, 3),
(104, 3),
(105, 3),
(106, 3),
(107, 3),
(108, 3),
(109, 3),
(110, 3),
(111, 3),
(112, 3),
(113, 3),
(114, 3),
(115, 3),
(116, 3),
(117, 3),
(118, 3),
(119, 3),
(120, 3),
(121, 3),
(122, 3),
(123, 3),
(124, 3),
(125, 3),
(126, 3),
(127, 3),
(128, 3),
(129, 3),
(130, 3),
(131, 3),
(132, 3),
(133, 3),
(134, 3),
(135, 3),
(136, 3),
(137, 3),
(138, 3),
(139, 3),
(140, 3),
(141, 3),
(142, 3),
(143, 3),
(144, 3),
(145, 3),
(151, 3),
(152, 3),
(153, 3),
(154, 3),
(155, 3),
(156, 3),
(157, 3),
(158, 3),
(159, 3),
(160, 3),
(165, 3),
(166, 3),
(167, 3),
(168, 3),
(169, 3),
(170, 3),
(171, 3),
(172, 3),
(178, 3),
(179, 3),
(180, 3),
(181, 3),
(182, 3),
(183, 3),
(184, 3),
(185, 3),
(186, 3),
(187, 3),
(188, 3),
(189, 3),
(190, 3),
(191, 3),
(192, 3),
(193, 3),
(194, 3),
(200, 3),
(202, 3),
(203, 3),
(207, 3),
(211, 3),
(11, 4),
(11, 5),
(11, 6),
(21, 6),
(23, 6),
(71, 6),
(73, 6),
(81, 6),
(83, 6),
(88, 6),
(90, 6),
(98, 6),
(100, 6),
(113, 6),
(115, 6),
(118, 6),
(120, 6),
(123, 6),
(125, 6),
(140, 6),
(142, 6),
(160, 6),
(178, 6),
(11, 7),
(21, 7),
(23, 7),
(71, 7),
(73, 7),
(81, 7),
(83, 7),
(88, 7),
(90, 7),
(98, 7),
(100, 7),
(113, 7),
(115, 7),
(118, 7),
(120, 7),
(123, 7),
(125, 7),
(140, 7),
(142, 7),
(160, 7),
(178, 7),
(11, 8),
(21, 8),
(23, 8),
(71, 8),
(73, 8),
(81, 8),
(83, 8),
(88, 8),
(90, 8),
(98, 8),
(100, 8),
(113, 8),
(115, 8),
(118, 8),
(120, 8),
(123, 8),
(125, 8),
(140, 8),
(142, 8),
(160, 8),
(178, 8),
(11, 9),
(21, 9),
(23, 9),
(71, 9),
(73, 9),
(81, 9),
(83, 9),
(88, 9),
(90, 9),
(98, 9),
(100, 9),
(113, 9),
(115, 9),
(118, 9),
(120, 9),
(123, 9),
(125, 9),
(140, 9),
(142, 9),
(160, 9),
(178, 9),
(11, 10),
(21, 10),
(23, 10),
(71, 10),
(73, 10),
(81, 10),
(83, 10),
(88, 10),
(90, 10),
(98, 10),
(100, 10),
(113, 10),
(115, 10),
(118, 10),
(120, 10),
(123, 10),
(125, 10),
(140, 10),
(142, 10),
(160, 10),
(178, 10),
(6, 11),
(7, 11),
(8, 11),
(9, 11),
(10, 11),
(11, 11),
(12, 11),
(13, 11),
(14, 11),
(15, 11),
(21, 11),
(22, 11),
(23, 11),
(24, 11),
(25, 11),
(26, 11),
(27, 11),
(28, 11),
(29, 11),
(30, 11),
(66, 11),
(67, 11),
(68, 11),
(69, 11),
(70, 11),
(71, 11),
(72, 11),
(73, 11),
(74, 11),
(75, 11),
(76, 11),
(77, 11),
(78, 11),
(79, 11),
(80, 11),
(81, 11),
(82, 11),
(83, 11),
(84, 11),
(85, 11),
(86, 11),
(87, 11),
(88, 11),
(89, 11),
(90, 11),
(91, 11),
(92, 11),
(93, 11),
(94, 11),
(95, 11),
(96, 11),
(97, 11),
(98, 11),
(99, 11),
(100, 11),
(101, 11),
(102, 11),
(103, 11),
(104, 11),
(105, 11),
(106, 11),
(107, 11),
(108, 11),
(109, 11),
(110, 11),
(111, 11),
(112, 11),
(113, 11),
(114, 11),
(115, 11),
(116, 11),
(117, 11),
(118, 11),
(119, 11),
(120, 11),
(121, 11),
(122, 11),
(123, 11),
(124, 11),
(125, 11),
(126, 11),
(127, 11),
(128, 11),
(129, 11),
(130, 11),
(131, 11),
(132, 11),
(133, 11),
(134, 11),
(135, 11),
(136, 11),
(137, 11),
(138, 11),
(139, 11),
(140, 11),
(141, 11),
(142, 11),
(143, 11),
(144, 11),
(145, 11),
(151, 11),
(152, 11),
(153, 11),
(154, 11),
(155, 11),
(156, 11),
(157, 11),
(158, 11),
(159, 11),
(160, 11),
(165, 11),
(166, 11),
(167, 11),
(168, 11),
(169, 11),
(170, 11),
(171, 11),
(172, 11),
(178, 11),
(179, 11),
(180, 11),
(181, 11),
(182, 11),
(183, 11),
(184, 11),
(185, 11),
(186, 11),
(187, 11),
(188, 11),
(189, 11),
(190, 11),
(191, 11),
(192, 11),
(193, 11),
(194, 11),
(202, 11),
(203, 11),
(207, 11),
(208, 11),
(209, 11),
(210, 11),
(211, 11),
(11, 12),
(11, 13),
(11, 14),
(11, 15),
(1, 16),
(2, 16),
(3, 16),
(4, 16),
(5, 16),
(6, 16),
(7, 16),
(8, 16),
(9, 16),
(10, 16),
(11, 16),
(12, 16),
(13, 16),
(14, 16),
(15, 16),
(16, 16),
(17, 16),
(18, 16),
(19, 16),
(20, 16),
(21, 16),
(22, 16),
(23, 16),
(24, 16),
(25, 16),
(26, 16),
(27, 16),
(28, 16),
(29, 16),
(30, 16),
(31, 16),
(32, 16),
(33, 16),
(34, 16),
(35, 16),
(36, 16),
(37, 16),
(38, 16),
(39, 16),
(40, 16),
(41, 16),
(42, 16),
(43, 16),
(44, 16),
(45, 16),
(46, 16),
(47, 16),
(48, 16),
(49, 16),
(50, 16),
(51, 16),
(52, 16),
(53, 16),
(54, 16),
(55, 16),
(56, 16),
(57, 16),
(58, 16),
(59, 16),
(60, 16),
(61, 16),
(62, 16),
(63, 16),
(64, 16),
(65, 16),
(129, 16),
(130, 16),
(131, 16),
(132, 16),
(133, 16),
(134, 16),
(135, 16),
(136, 16),
(137, 16),
(138, 16),
(139, 16),
(140, 16),
(141, 16),
(142, 16),
(143, 16),
(144, 16),
(146, 16),
(147, 16),
(148, 16),
(149, 16),
(150, 16),
(159, 16),
(160, 16),
(161, 16),
(162, 16),
(163, 16),
(164, 16),
(173, 16),
(174, 16),
(175, 16),
(176, 16),
(177, 16),
(6, 17),
(7, 17),
(8, 17),
(9, 17),
(10, 17),
(11, 17),
(12, 17),
(13, 17),
(14, 17),
(15, 17),
(16, 17),
(17, 17),
(18, 17),
(19, 17),
(20, 17),
(21, 17),
(22, 17),
(23, 17),
(24, 17),
(25, 17),
(26, 17),
(27, 17),
(28, 17),
(29, 17),
(30, 17),
(66, 17),
(67, 17),
(68, 17),
(69, 17),
(70, 17),
(71, 17),
(72, 17),
(73, 17),
(74, 17),
(75, 17),
(76, 17),
(77, 17),
(78, 17),
(79, 17),
(80, 17),
(81, 17),
(82, 17),
(83, 17),
(84, 17),
(85, 17),
(86, 17),
(87, 17),
(88, 17),
(89, 17),
(90, 17),
(91, 17),
(92, 17),
(93, 17),
(94, 17),
(95, 17),
(96, 17),
(97, 17),
(98, 17),
(99, 17),
(100, 17),
(101, 17),
(102, 17),
(103, 17),
(104, 17),
(105, 17),
(106, 17),
(107, 17),
(108, 17),
(109, 17),
(110, 17),
(111, 17),
(112, 17),
(113, 17),
(114, 17),
(115, 17),
(116, 17),
(117, 17),
(118, 17),
(119, 17),
(120, 17),
(121, 17),
(122, 17),
(123, 17),
(124, 17),
(125, 17),
(126, 17),
(127, 17),
(128, 17),
(129, 17),
(130, 17),
(131, 17),
(132, 17),
(133, 17),
(134, 17),
(135, 17),
(136, 17),
(137, 17),
(138, 17),
(139, 17),
(140, 17),
(141, 17),
(142, 17),
(143, 17),
(144, 17),
(145, 17),
(151, 17),
(152, 17),
(153, 17),
(154, 17),
(155, 17),
(156, 17),
(157, 17),
(158, 17),
(159, 17),
(160, 17),
(165, 17),
(166, 17),
(167, 17),
(168, 17),
(169, 17),
(170, 17),
(171, 17),
(172, 17),
(178, 17),
(179, 17),
(180, 17),
(181, 17),
(182, 17),
(183, 17),
(184, 17),
(185, 17),
(186, 17),
(187, 17),
(188, 17),
(189, 17),
(190, 17),
(191, 17),
(192, 17),
(193, 17),
(194, 17),
(200, 17),
(202, 17),
(203, 17),
(207, 17),
(211, 17),
(11, 18),
(11, 19),
(11, 20),
(21, 20),
(23, 20),
(71, 20),
(73, 20),
(81, 20),
(83, 20),
(88, 20),
(90, 20),
(98, 20),
(100, 20),
(113, 20),
(115, 20),
(118, 20),
(120, 20),
(123, 20),
(125, 20),
(140, 20),
(142, 20),
(160, 20),
(178, 20),
(11, 21),
(21, 21),
(23, 21),
(71, 21),
(73, 21),
(81, 21),
(83, 21),
(88, 21),
(90, 21),
(98, 21),
(100, 21),
(113, 21),
(115, 21),
(118, 21),
(120, 21),
(123, 21),
(125, 21),
(140, 21),
(142, 21),
(160, 21),
(178, 21),
(11, 22),
(21, 22),
(23, 22),
(71, 22),
(73, 22),
(81, 22),
(83, 22),
(88, 22),
(90, 22),
(98, 22),
(100, 22),
(113, 22),
(115, 22),
(118, 22),
(120, 22),
(123, 22),
(125, 22),
(140, 22),
(142, 22),
(160, 22),
(178, 22),
(11, 23),
(21, 23),
(23, 23),
(71, 23),
(73, 23),
(81, 23),
(83, 23),
(88, 23),
(90, 23),
(98, 23),
(100, 23),
(113, 23),
(115, 23),
(118, 23),
(120, 23),
(123, 23),
(125, 23),
(140, 23),
(142, 23),
(160, 23),
(178, 23),
(11, 24),
(21, 24),
(23, 24),
(71, 24),
(73, 24),
(81, 24),
(83, 24),
(88, 24),
(90, 24),
(98, 24),
(100, 24),
(113, 24),
(115, 24),
(118, 24),
(120, 24),
(123, 24),
(125, 24),
(140, 24),
(142, 24),
(160, 24),
(178, 24),
(6, 25),
(7, 25),
(8, 25),
(9, 25),
(10, 25),
(11, 25),
(12, 25),
(13, 25),
(14, 25),
(15, 25),
(21, 25),
(22, 25),
(23, 25),
(24, 25),
(25, 25),
(26, 25),
(27, 25),
(28, 25),
(29, 25),
(30, 25),
(66, 25),
(67, 25),
(68, 25),
(69, 25),
(70, 25),
(71, 25),
(72, 25),
(73, 25),
(74, 25),
(75, 25),
(76, 25),
(77, 25),
(78, 25),
(79, 25),
(80, 25),
(81, 25),
(82, 25),
(83, 25),
(84, 25),
(85, 25),
(86, 25),
(87, 25),
(88, 25),
(89, 25),
(90, 25),
(91, 25),
(92, 25),
(93, 25),
(94, 25),
(95, 25),
(96, 25),
(97, 25),
(98, 25),
(99, 25),
(100, 25),
(101, 25),
(102, 25),
(103, 25),
(104, 25),
(105, 25),
(106, 25),
(107, 25),
(108, 25),
(109, 25),
(110, 25),
(111, 25),
(112, 25),
(113, 25),
(114, 25),
(115, 25),
(116, 25),
(117, 25),
(118, 25),
(119, 25),
(120, 25),
(121, 25),
(122, 25),
(123, 25),
(124, 25),
(125, 25),
(126, 25),
(127, 25),
(128, 25),
(129, 25),
(130, 25),
(131, 25),
(132, 25),
(133, 25),
(134, 25),
(135, 25),
(136, 25),
(137, 25),
(138, 25),
(139, 25),
(140, 25),
(141, 25),
(142, 25),
(143, 25),
(144, 25),
(145, 25),
(151, 25),
(152, 25),
(153, 25),
(154, 25),
(155, 25),
(156, 25),
(157, 25),
(158, 25),
(159, 25),
(160, 25),
(165, 25),
(166, 25),
(167, 25),
(168, 25),
(169, 25),
(170, 25),
(171, 25),
(172, 25),
(178, 25),
(179, 25),
(180, 25),
(181, 25),
(182, 25),
(183, 25),
(184, 25),
(185, 25),
(186, 25),
(187, 25),
(188, 25),
(189, 25),
(190, 25),
(191, 25),
(192, 25),
(193, 25),
(194, 25),
(202, 25),
(203, 25),
(207, 25),
(208, 25),
(209, 25),
(210, 25),
(211, 25),
(11, 26),
(11, 27),
(11, 28),
(11, 29),
(6, 30),
(7, 30),
(8, 30),
(9, 30),
(10, 30),
(11, 30),
(12, 30),
(13, 30),
(14, 30),
(15, 30),
(16, 30),
(17, 30),
(18, 30),
(19, 30),
(20, 30),
(21, 30),
(22, 30),
(23, 30),
(24, 30),
(25, 30),
(26, 30),
(27, 30),
(28, 30),
(29, 30),
(30, 30),
(66, 30),
(67, 30),
(68, 30),
(69, 30),
(70, 30),
(71, 30),
(72, 30),
(73, 30),
(74, 30),
(75, 30),
(76, 30),
(77, 30),
(78, 30),
(79, 30),
(80, 30),
(81, 30),
(82, 30),
(83, 30),
(84, 30),
(85, 30),
(86, 30),
(87, 30),
(88, 30),
(89, 30),
(90, 30),
(91, 30),
(92, 30),
(93, 30),
(94, 30),
(95, 30),
(96, 30),
(97, 30),
(98, 30),
(99, 30),
(100, 30),
(101, 30),
(102, 30),
(103, 30),
(104, 30),
(105, 30),
(106, 30),
(107, 30),
(108, 30),
(109, 30),
(110, 30),
(111, 30),
(112, 30),
(113, 30),
(114, 30),
(115, 30),
(116, 30),
(117, 30),
(118, 30),
(119, 30),
(120, 30),
(121, 30),
(122, 30),
(123, 30),
(124, 30),
(125, 30),
(126, 30),
(127, 30),
(128, 30),
(129, 30),
(130, 30),
(131, 30),
(132, 30),
(133, 30),
(134, 30),
(135, 30),
(136, 30),
(137, 30),
(138, 30),
(139, 30),
(140, 30),
(141, 30),
(142, 30),
(143, 30),
(144, 30),
(145, 30),
(151, 30),
(152, 30),
(153, 30),
(154, 30),
(155, 30),
(156, 30),
(157, 30),
(158, 30),
(159, 30),
(160, 30),
(165, 30),
(166, 30),
(167, 30),
(168, 30),
(169, 30),
(170, 30),
(171, 30),
(172, 30),
(178, 30),
(179, 30),
(180, 30),
(181, 30),
(182, 30),
(183, 30),
(184, 30),
(185, 30),
(186, 30),
(187, 30),
(188, 30),
(189, 30),
(190, 30),
(191, 30),
(192, 30),
(193, 30),
(194, 30),
(200, 30),
(202, 30),
(203, 30),
(207, 30),
(211, 30),
(11, 31),
(11, 32),
(11, 33),
(21, 33),
(23, 33),
(71, 33),
(73, 33),
(81, 33),
(83, 33),
(88, 33),
(90, 33),
(98, 33),
(100, 33),
(113, 33),
(115, 33),
(118, 33),
(120, 33),
(123, 33),
(125, 33),
(140, 33),
(142, 33),
(160, 33),
(178, 33),
(11, 34),
(21, 34),
(23, 34),
(71, 34),
(73, 34),
(81, 34),
(83, 34),
(88, 34),
(90, 34),
(98, 34),
(100, 34),
(113, 34),
(115, 34),
(118, 34),
(120, 34),
(123, 34),
(125, 34),
(140, 34),
(142, 34),
(160, 34),
(178, 34),
(11, 35),
(21, 35),
(23, 35),
(71, 35),
(73, 35),
(81, 35),
(83, 35),
(88, 35),
(90, 35),
(98, 35),
(100, 35),
(113, 35),
(115, 35),
(118, 35),
(120, 35),
(123, 35),
(125, 35),
(140, 35),
(142, 35),
(160, 35),
(178, 35),
(11, 36),
(21, 36),
(23, 36),
(71, 36),
(73, 36),
(81, 36),
(83, 36),
(88, 36),
(90, 36),
(98, 36),
(100, 36),
(113, 36),
(115, 36),
(118, 36),
(120, 36),
(123, 36),
(125, 36),
(140, 36),
(142, 36),
(160, 36),
(178, 36),
(11, 37),
(21, 37),
(23, 37),
(71, 37),
(73, 37),
(81, 37),
(83, 37),
(88, 37),
(90, 37),
(98, 37),
(100, 37),
(113, 37),
(115, 37),
(118, 37),
(120, 37),
(123, 37),
(125, 37),
(140, 37),
(142, 37),
(160, 37),
(178, 37),
(6, 38),
(7, 38),
(8, 38),
(9, 38),
(10, 38),
(11, 38),
(12, 38),
(13, 38),
(14, 38),
(15, 38),
(21, 38),
(22, 38),
(23, 38),
(24, 38),
(25, 38),
(26, 38),
(27, 38),
(28, 38),
(29, 38),
(30, 38),
(66, 38),
(67, 38),
(68, 38),
(69, 38),
(70, 38),
(71, 38),
(72, 38),
(73, 38),
(74, 38),
(75, 38),
(76, 38),
(77, 38),
(78, 38),
(79, 38),
(80, 38),
(81, 38),
(82, 38),
(83, 38),
(84, 38),
(85, 38),
(86, 38),
(87, 38),
(88, 38),
(89, 38),
(90, 38),
(91, 38),
(92, 38),
(93, 38),
(94, 38),
(95, 38),
(96, 38),
(97, 38),
(98, 38),
(99, 38),
(100, 38),
(101, 38),
(102, 38),
(103, 38),
(104, 38),
(105, 38),
(106, 38),
(107, 38),
(108, 38),
(109, 38),
(110, 38),
(111, 38),
(112, 38),
(113, 38),
(114, 38),
(115, 38),
(116, 38),
(117, 38),
(118, 38),
(119, 38),
(120, 38),
(121, 38),
(122, 38),
(123, 38),
(124, 38),
(125, 38),
(126, 38),
(127, 38),
(128, 38),
(129, 38),
(130, 38),
(131, 38),
(132, 38),
(133, 38),
(134, 38),
(135, 38),
(136, 38),
(137, 38),
(138, 38),
(139, 38),
(140, 38),
(141, 38),
(142, 38),
(143, 38),
(144, 38),
(145, 38),
(151, 38),
(152, 38),
(153, 38),
(154, 38),
(155, 38),
(156, 38),
(157, 38),
(158, 38),
(159, 38),
(160, 38),
(165, 38),
(166, 38),
(167, 38),
(168, 38),
(169, 38),
(170, 38),
(171, 38),
(172, 38),
(178, 38),
(179, 38),
(180, 38),
(181, 38),
(182, 38),
(183, 38),
(184, 38),
(185, 38),
(186, 38),
(187, 38),
(188, 38),
(189, 38),
(190, 38),
(191, 38),
(192, 38),
(193, 38),
(194, 38),
(202, 38),
(203, 38),
(207, 38),
(208, 38),
(209, 38),
(210, 38),
(211, 38),
(11, 39),
(11, 40),
(11, 41),
(11, 42);

-- --------------------------------------------------------

--
-- Table structure for table `salary_details`
--

CREATE TABLE `salary_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `salary_per_month` double NOT NULL DEFAULT '0',
  `salary_package_start_date` date NOT NULL,
  `salary_package_end_date` date NOT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `slot_assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_assigned_working_hour_id` bigint(20) UNSIGNED DEFAULT NULL,
  `schedule_template_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'active schedule template',
  `leave_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `schedule_type` enum('basic','extra','emergency') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'only for schedule',
  `shift_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_start_time` datetime DEFAULT NULL,
  `shift_end_time` datetime DEFAULT NULL,
  `rest_start_time` datetime DEFAULT NULL,
  `rest_end_time` datetime DEFAULT NULL,
  `shift_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `only_leave` tinyint(1) DEFAULT '0',
  `leave_applied` tinyint(1) NOT NULL DEFAULT '0',
  `leave_group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'only for leave',
  `leave_type` enum('leave','vacation','extra') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_reason` text COLLATE utf8mb4_unicode_ci,
  `leave_approved` tinyint(1) NOT NULL DEFAULT '0',
  `leave_approved_date_time` datetime DEFAULT NULL,
  `leave_notified_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notified_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assign_status` enum('vacant','assigned') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `scheduled_work_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'total worked - ob worked , when type basic',
  `extra_work_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'total worked - ob worked , when type extra',
  `emergency_work_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'total worked - ob worked , when type emergency',
  `ob_work_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `vacation_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `ob_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ob_start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ob_end_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_company` tinyint(1) DEFAULT '0',
  `company_sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by_employee` tinyint(1) DEFAULT '0',
  `employee_sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_stampling_datewise_reports`
--

CREATE TABLE `schedule_stampling_datewise_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `scheduled_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'total scheduled',
  `stampling_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'total worked',
  `ob_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `regular_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'stampling_duration - ob_duration',
  `extra_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'regular_duration - scheduled_duration',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_templates`
--

CREATE TABLE `schedule_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 for not active, 1 for active',
  `activation_date` date DEFAULT NULL,
  `deactivation_date` date DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_template_data`
--

CREATE TABLE `schedule_template_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `schedule_template_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'active schedule template',
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `schedule_type` enum('basic','extra','emergency') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift_start_time` datetime DEFAULT NULL,
  `shift_end_time` datetime DEFAULT NULL,
  `shift_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift_assignes`
--

CREATE TABLE `shift_assignes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `shift_id` int(11) NOT NULL,
  `shift_start_date` date NOT NULL,
  `shift_end_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` int(11) NOT NULL COMMENT 'comes from users table (user company id)',
  `resource_id` int(11) DEFAULT NULL COMMENT 'comes from any table',
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stamplings`
--

CREATE TABLE `stamplings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `stampling_type` enum('scheduled','walkin') COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `schedule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `in_time` datetime NOT NULL,
  `out_time` datetime DEFAULT NULL,
  `rest_start_time` datetime DEFAULT NULL,
  `rest_end_time` datetime DEFAULT NULL,
  `in_location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `out_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_early_in` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_early_out` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_late_in` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_late_out` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_hours_rate` double(8,2) DEFAULT NULL COMMENT 'amount or salary / hr',
  `extra_hours_rate` double(8,2) DEFAULT NULL COMMENT 'amount or salary / hr',
  `ob_hours_rate` double(8,2) DEFAULT NULL COMMENT 'amount or salary / hr',
  `ob_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_schedule_hours` double(8,2) DEFAULT '0.00' COMMENT 'total worked - ob worked - extra worked',
  `total_extra_hours` double(8,2) DEFAULT '0.00',
  `total_ob_hours` double(8,2) DEFAULT '0.00',
  `working_percent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `is_extra_hours_approved` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '0 for pending,1 for approved,2 for rejected',
  `reason_for_rejection` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logout_by` enum('self','system') COLLATE utf8mb4_unicode_ci DEFAULT 'self',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `package_details` text COLLATE utf8mb4_unicode_ci,
  `licence_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `package_id`, `package_details`, `licence_key`, `start_date`, `end_date`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T13:42:51.000000Z\",\"updated_at\":\"2022-09-21T13:42:51.000000Z\",\"deleted_at\":null}', 'DLL51-5AJ78-5MUKC-P2HVQ', '2022-09-21', '2022-12-30', 1, 'web-0.0.1', '2022-09-21 12:16:26', '2022-09-21 12:16:26'),
(2, 3, 1, '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-21T13:42:51.000000Z\",\"updated_at\":\"2022-09-21T13:42:51.000000Z\",\"deleted_at\":null}', '52LAE-H3NCK-L1M5T-K6J3C', '2022-09-21', '2022-12-30', 1, 'web-0.0.1', '2022-09-21 12:18:28', '2022-09-21 12:18:28');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `user_type_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `action_by` bigint(20) UNSIGNED DEFAULT NULL,
  `type_id` tinyint(4) DEFAULT NULL COMMENT '1:Activity,2:IP,3:User,4:Deviation,5:FollowUps,6:Journal,7:Patient,8:Employee,9:',
  `resource_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `how_many_time` int(11) DEFAULT NULL,
  `is_repeat` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:No,1:Yes',
  `every` int(11) DEFAULT NULL,
  `repetition_type` tinyint(4) DEFAULT NULL COMMENT '1:day,2:week,3:month,4:Year',
  `how_many_time_array` longtext COLLATE utf8mb4_unicode_ci,
  `repeat_dates` longtext COLLATE utf8mb4_unicode_ci,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remind_before_start` tinyint(4) NOT NULL DEFAULT '0',
  `before_minutes` int(11) DEFAULT NULL,
  `before_is_text_notify` tinyint(4) NOT NULL DEFAULT '0',
  `before_is_push_notify` tinyint(4) NOT NULL DEFAULT '0',
  `action_date` timestamp NULL DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=Not Done,1:Done',
  `is_latest_entry` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_unique_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `company_type_id` text COLLATE utf8mb4_unicode_ci,
  `branch_id` int(11) DEFAULT NULL,
  `govt_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personal_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_type_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_area` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_address` text COLLATE utf8mb4_unicode_ci,
  `licence_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `licence_end_date` date DEFAULT NULL,
  `licence_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `employee_type` enum('1','2','3','4') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '1:regular, 2:substitute, 3:seasonal, 4 extra',
  `joining_date` date DEFAULT NULL,
  `establishment_year` int(11) DEFAULT NULL,
  `user_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disease_description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `password_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_file_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `is_secret` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive,2:deleted',
  `is_fake` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `is_password_change` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No',
  `documents` text COLLATE utf8mb4_unicode_ci,
  `step_one` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_two` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_three` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_four` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `step_five` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Pending,1:Partial Completed,2:Completed',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_id` int(11) DEFAULT '1',
  `contract_type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '1:Hourly rate, 2: Fixed cost',
  `contract_value` decimal(9,2) DEFAULT '0.00',
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'https://aceuss.3mad.in/uploads/no-image.png',
  `schedule_start_date` date DEFAULT NULL,
  `report_verify` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `verification_method` enum('normal','bank_id') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `is_family_member` tinyint(1) NOT NULL DEFAULT '0',
  `is_caretaker` tinyint(1) NOT NULL DEFAULT '0',
  `is_contact_person` tinyint(1) NOT NULL DEFAULT '0',
  `is_guardian` tinyint(1) NOT NULL DEFAULT '0',
  `is_other` tinyint(1) NOT NULL DEFAULT '0',
  `is_other_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unique_id`, `custom_unique_id`, `user_type_id`, `category_id`, `top_most_parent_id`, `parent_id`, `dept_id`, `role_id`, `company_type_id`, `branch_id`, `govt_id`, `name`, `email`, `email_verified_at`, `password`, `contact_number`, `gender`, `personal_number`, `organization_number`, `patient_type_id`, `country_id`, `city`, `postal_area`, `zipcode`, `full_address`, `licence_key`, `licence_end_date`, `licence_status`, `employee_type`, `joining_date`, `establishment_year`, `user_color`, `disease_description`, `created_by`, `password_token`, `is_file_required`, `is_secret`, `status`, `is_fake`, `is_password_change`, `documents`, `step_one`, `step_two`, `step_three`, `step_four`, `step_five`, `entry_mode`, `contact_person_number`, `contact_person_name`, `language_id`, `contract_type`, `contract_value`, `avatar`, `schedule_start_date`, `report_verify`, `verification_method`, `is_family_member`, `is_caretaker`, `is_contact_person`, `is_guardian`, `is_other`, `is_other_name`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'VfgXiv5O6Dir', NULL, 1, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, 'admin', 'admin@gmail.com', NULL, '$2y$10$XMQpV4VfncBnbCbVmrJFQeJrmT8/tmqh3kJIET6.4NzbUgjenbSnC', '8103099592', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL, 1, '1', '0.00', 'https://aceuss.3mad.in/uploads/no-image.png', NULL, 'no', 'normal', 0, 0, 0, 0, 0, NULL, NULL, '2022-09-21 15:42:52', '2022-09-21 15:42:52', NULL),
(2, 'M3bLuy4qFeBz', NULL, 2, NULL, 2, NULL, NULL, 2, '[3,2,1]', NULL, NULL, 'TS Corp', 'company@gmail.com', NULL, '$2y$10$q4D6VtTgJW4uzIbh5F6Cz.egIBYlkkoigsNmB0aOPN2Gg7anTynT.', '1235647980', NULL, NULL, '7894561230', NULL, 209, 'stockholm', '87954', '13254', 'Address', 'DLL51-5AJ78-5MUKC-P2HVQ', '2022-12-30', 1, NULL, NULL, 1968, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 0, 'null', 0, 0, 0, 0, 0, 'web-0.0.1', NULL, 'T.S', 1, NULL, NULL, 'https://aceuss.3mad.in/uploads/no-image.png', NULL, NULL, 'normal', 0, 0, 0, 0, 0, NULL, NULL, '2022-09-21 12:16:26', '2022-09-21 12:16:26', NULL),
(3, 'iRIqLARHIFNW', NULL, 2, NULL, 3, NULL, NULL, 2, '[3]', NULL, NULL, 'Comp 2', 'company2@gmail.com', NULL, '$2y$10$hirPwjwsxfHvysdM.OP8jeg87PQp4COMQzML1pJNpbwcOjNPOZPSO', '1234567410', NULL, NULL, '1234567890', NULL, 209, 'Stcock', '12457', '12454', 'Address', '52LAE-H3NCK-L1M5T-K6J3C', '2022-12-30', 1, NULL, NULL, 1995, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 0, 'null', 0, 0, 0, 0, 0, 'web-0.0.1', NULL, 'C.P', 1, NULL, NULL, 'https://aceuss.3mad.in/uploads/no-image.png', NULL, NULL, 'normal', 0, 0, 0, 0, 0, NULL, NULL, '2022-09-21 12:18:28', '2022-09-21 12:18:28', NULL),
(4, 'lrNNyceq2uVx', '16637-557-02053', 6, NULL, 2, 2, NULL, 20, '[3]', 2, NULL, 'TS Arvida Fahlgren', 'arvida.fahlgren@spamherelots.com', NULL, '$2y$10$gqrn/gWqIMpZRMeKk16KROu627w9NmxQule3/YK3sXEK/4H1fTWUG', '04913975983', 'male', '196001208369', NULL, '[\"5\"]', NULL, NULL, NULL, NULL, '579 32 HÖGSBY', NULL, NULL, 1, NULL, NULL, NULL, '#1fff', 'Better Care and Better Understanding.', 2, NULL, 0, 0, 1, 0, 0, '[]', 1, 1, 1, 1, 1, 'web-0.0.1', NULL, NULL, 1, NULL, NULL, 'https://aceuss.3mad.in/uploads/no-image.png', NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, '2022-09-21 12:25:55', '2022-09-21 12:25:55', NULL),
(5, 'KUeRuY5WkjGF', NULL, 8, NULL, 2, 4, NULL, 22, NULL, 2, NULL, 'Tyra Christiansson', 'tyra.christiansson@sogetthis.com', NULL, '$2y$10$Yyu76P..mjvmvT/bSaqlUOEfFkUbkK8/tZEykGbkkurkl67X9YDQ6', '06904618596', NULL, NULL, NULL, NULL, NULL, 'Hudiksvägen', '2313', '21312', '841 92 ÅNGE', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL, 1, '1', '0.00', 'https://aceuss.3mad.in/uploads/no-image.png', NULL, 'no', 'normal', 1, 0, 0, 0, 0, '0', NULL, '2022-09-21 12:25:58', '2022-09-21 12:25:58', NULL),
(6, 'DoQKvm4vAvOZ', NULL, 9, NULL, 2, 4, NULL, 23, NULL, 2, NULL, 'Christer Cedergren', 'christer.cedergren@mailinater.com', NULL, '$2y$10$R33w2wEnoYeI9KrwNLAlxOpU/IaAwvgj5wUYTZkAz.XnWh2Kn1woq', '09268877474', NULL, NULL, NULL, NULL, NULL, 'Bergsäng krusberg', '12450', '78451', '956 31 ÖVERKALIX', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 0, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL, 1, '1', '0.00', 'https://aceuss.3mad.in/uploads/no-image.png', NULL, 'no', 'normal', 0, 0, 1, 0, 0, '0', NULL, '2022-09-21 12:26:00', '2022-09-21 12:26:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_scheduled_dates`
--

CREATE TABLE `user_scheduled_dates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `working_percent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '100',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:Active,0:Inactive',
  `entry_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `name`, `status`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(2, 'Company', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(3, 'Employee', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(4, 'Hospital', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(5, 'Nurse', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(6, 'Patient', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(7, 'careTaker', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(8, 'FamilyMember', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(9, 'ContactPerson', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(10, 'careTakerFamily', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(11, 'Branch', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(12, 'Guardian', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(13, 'Presented', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(14, 'Participated', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(15, 'Other', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51'),
(16, 'Admin Employee', 1, NULL, '2022-09-21 15:42:51', '2022-09-21 15:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `user_type_has_permissions`
--

CREATE TABLE `user_type_has_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `permission_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_type_has_permissions`
--

INSERT INTO `user_type_has_permissions` (`id`, `user_type_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(374, 4, 11, '2022-07-14 06:40:53', '2022-07-14 06:40:53'),
(375, 5, 11, '2022-07-14 06:40:53', '2022-07-14 06:40:53'),
(654, 12, 11, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(655, 12, 21, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(656, 12, 23, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(657, 12, 71, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(658, 12, 72, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(659, 12, 73, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(660, 12, 74, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(661, 12, 75, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(662, 12, 81, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(663, 12, 83, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(664, 12, 86, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(665, 12, 88, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(666, 12, 96, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(667, 12, 97, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(668, 12, 98, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(669, 12, 99, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(670, 12, 100, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(671, 12, 106, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(672, 12, 108, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(673, 12, 111, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(674, 12, 121, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(675, 12, 123, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(676, 12, 137, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(677, 12, 139, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(678, 12, 160, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(679, 12, 161, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(680, 12, 171, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(681, 12, 172, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(682, 13, 11, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(683, 14, 11, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(684, 15, 11, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(685, 16, 1, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(686, 16, 2, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(687, 16, 3, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(688, 16, 4, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(689, 16, 5, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(690, 16, 6, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(691, 16, 7, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(692, 16, 8, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(693, 16, 9, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(694, 16, 10, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(695, 16, 11, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(696, 16, 12, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(697, 16, 13, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(698, 16, 14, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(699, 16, 15, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(700, 16, 16, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(701, 16, 17, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(702, 16, 18, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(703, 16, 19, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(704, 16, 20, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(705, 16, 21, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(706, 16, 22, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(707, 16, 23, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(708, 16, 24, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(709, 16, 25, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(710, 16, 26, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(711, 16, 27, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(712, 16, 28, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(713, 16, 29, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(714, 16, 30, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(715, 16, 31, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(716, 16, 32, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(717, 16, 33, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(718, 16, 34, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(719, 16, 35, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(720, 16, 36, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(721, 16, 37, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(722, 16, 38, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(723, 16, 39, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(724, 16, 40, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(725, 16, 41, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(726, 16, 42, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(727, 16, 43, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(728, 16, 44, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(729, 16, 45, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(730, 16, 46, '2022-07-14 06:40:54', '2022-07-14 06:40:54'),
(731, 16, 47, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(732, 16, 48, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(733, 16, 49, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(734, 16, 50, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(735, 16, 51, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(736, 16, 52, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(737, 16, 53, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(738, 16, 54, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(739, 16, 55, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(740, 16, 56, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(741, 16, 57, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(742, 16, 58, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(743, 16, 59, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(744, 16, 60, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(745, 16, 61, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(746, 16, 62, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(747, 16, 63, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(748, 16, 64, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(749, 16, 65, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(750, 16, 129, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(751, 16, 130, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(752, 16, 131, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(753, 16, 132, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(754, 16, 133, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(755, 16, 134, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(756, 16, 135, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(757, 16, 136, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(758, 16, 137, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(759, 16, 138, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(760, 16, 139, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(761, 16, 140, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(762, 16, 141, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(763, 16, 142, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(764, 16, 143, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(765, 16, 144, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(766, 16, 146, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(767, 16, 147, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(768, 16, 148, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(769, 16, 149, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(770, 16, 150, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(771, 16, 159, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(772, 16, 160, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(773, 16, 161, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(774, 16, 162, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(775, 16, 163, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(776, 16, 164, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(777, 16, 173, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(778, 16, 174, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(779, 16, 175, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(780, 16, 176, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(781, 16, 177, '2022-07-14 06:40:55', '2022-07-14 06:40:55'),
(2920, 10, 11, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2921, 10, 21, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2922, 10, 23, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2923, 10, 71, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2924, 10, 73, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2925, 10, 81, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2926, 10, 83, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2927, 10, 88, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2928, 10, 98, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2929, 10, 100, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2930, 10, 123, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2931, 10, 160, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2932, 10, 90, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2933, 10, 113, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2934, 10, 115, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2935, 10, 120, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2936, 10, 118, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2937, 10, 125, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2938, 10, 140, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2939, 10, 142, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2940, 10, 178, '2022-08-03 11:42:08', '2022-08-03 11:42:08'),
(2941, 9, 11, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2942, 9, 21, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2943, 9, 23, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2944, 9, 71, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2945, 9, 73, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2946, 9, 81, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2947, 9, 83, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2948, 9, 88, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2949, 9, 98, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2950, 9, 100, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2951, 9, 123, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2952, 9, 160, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2953, 9, 90, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2954, 9, 113, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2955, 9, 115, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2956, 9, 120, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2957, 9, 118, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2958, 9, 125, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2959, 9, 140, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2960, 9, 142, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2961, 9, 178, '2022-08-03 11:43:07', '2022-08-03 11:43:07'),
(2962, 8, 11, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2963, 8, 21, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2964, 8, 23, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2965, 8, 71, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2966, 8, 73, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2967, 8, 81, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2968, 8, 83, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2969, 8, 88, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2970, 8, 98, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2971, 8, 100, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2972, 8, 123, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2973, 8, 160, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2974, 8, 90, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2975, 8, 113, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2976, 8, 115, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2977, 8, 118, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2978, 8, 120, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2979, 8, 125, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2980, 8, 178, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2981, 8, 140, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(2982, 8, 142, '2022-08-03 11:43:51', '2022-08-03 11:43:51'),
(3007, 7, 11, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3008, 7, 21, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3009, 7, 23, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3010, 7, 71, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3011, 7, 81, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3012, 7, 83, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3013, 7, 88, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3014, 7, 98, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3015, 7, 100, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3016, 7, 123, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3017, 7, 113, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3018, 7, 73, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3019, 7, 90, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3020, 7, 115, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3021, 7, 118, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3022, 7, 125, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3023, 7, 140, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3024, 7, 142, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3025, 7, 178, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3026, 7, 120, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3027, 7, 160, '2022-08-03 11:45:17', '2022-08-03 11:45:17'),
(3028, 6, 11, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3029, 6, 21, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3030, 6, 23, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3031, 6, 71, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3032, 6, 73, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3033, 6, 81, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3034, 6, 88, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3035, 6, 98, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3036, 6, 100, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3037, 6, 123, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3038, 6, 160, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3039, 6, 90, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3040, 6, 113, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3041, 6, 115, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3042, 6, 118, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3043, 6, 120, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3044, 6, 125, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3045, 6, 140, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3046, 6, 142, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3047, 6, 178, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3048, 6, 83, '2022-08-03 11:46:32', '2022-08-03 11:46:32'),
(3882, 1, 1, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3883, 1, 2, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3884, 1, 3, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3885, 1, 4, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3886, 1, 5, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3887, 1, 6, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3888, 1, 7, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3889, 1, 8, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3890, 1, 9, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3891, 1, 10, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3892, 1, 11, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3893, 1, 12, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3894, 1, 13, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3895, 1, 14, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3896, 1, 15, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3897, 1, 16, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3898, 1, 17, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3899, 1, 18, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3900, 1, 19, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3901, 1, 20, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3902, 1, 21, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3903, 1, 22, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3904, 1, 23, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3905, 1, 24, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3906, 1, 25, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3907, 1, 26, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3908, 1, 27, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3909, 1, 28, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3910, 1, 29, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3911, 1, 30, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3912, 1, 31, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3913, 1, 32, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3914, 1, 33, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3915, 1, 34, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3916, 1, 35, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3917, 1, 36, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3918, 1, 37, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3919, 1, 38, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3920, 1, 39, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3921, 1, 40, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3922, 1, 41, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3923, 1, 42, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3924, 1, 43, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3925, 1, 44, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3926, 1, 45, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3927, 1, 46, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3928, 1, 47, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3929, 1, 48, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3930, 1, 49, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3931, 1, 50, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3932, 1, 51, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3933, 1, 52, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3934, 1, 53, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3935, 1, 54, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3936, 1, 55, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3937, 1, 61, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3938, 1, 62, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3939, 1, 63, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3940, 1, 64, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3941, 1, 65, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3942, 1, 129, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3943, 1, 130, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3944, 1, 131, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3945, 1, 132, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3946, 1, 133, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3947, 1, 134, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3948, 1, 135, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3949, 1, 136, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3950, 1, 137, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3951, 1, 138, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3952, 1, 139, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3953, 1, 140, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3954, 1, 141, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3955, 1, 142, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3956, 1, 143, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3957, 1, 144, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3958, 1, 146, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3959, 1, 147, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3960, 1, 148, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3961, 1, 149, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3962, 1, 150, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3963, 1, 159, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3964, 1, 160, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3965, 1, 161, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3966, 1, 162, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3967, 1, 163, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3968, 1, 164, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3969, 1, 173, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3970, 1, 174, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3971, 1, 175, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3972, 1, 176, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3973, 1, 177, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3974, 1, 195, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3975, 1, 196, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3976, 1, 198, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3977, 1, 199, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3978, 1, 197, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3979, 1, 168, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3980, 1, 172, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3981, 1, 169, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3982, 1, 170, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3983, 1, 171, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3984, 1, 204, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(3985, 1, 205, '2022-09-09 12:39:36', '2022-09-09 12:39:36'),
(4436, 3, 6, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4437, 3, 7, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4438, 3, 8, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4439, 3, 9, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4440, 3, 10, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4441, 3, 11, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4442, 3, 12, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4443, 3, 13, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4444, 3, 14, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4445, 3, 15, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4446, 3, 16, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4447, 3, 17, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4448, 3, 18, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4449, 3, 19, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4450, 3, 20, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4451, 3, 21, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4452, 3, 23, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4453, 3, 26, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4454, 3, 27, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4455, 3, 28, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4456, 3, 29, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4457, 3, 30, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4458, 3, 66, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4459, 3, 67, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4460, 3, 68, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4461, 3, 69, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4462, 3, 70, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4463, 3, 71, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4464, 3, 72, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4465, 3, 73, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4466, 3, 74, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4467, 3, 75, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4468, 3, 76, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4469, 3, 77, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4470, 3, 78, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4471, 3, 79, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4472, 3, 80, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4473, 3, 81, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4474, 3, 82, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4475, 3, 83, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4476, 3, 84, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4477, 3, 85, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4478, 3, 86, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4479, 3, 87, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4480, 3, 88, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4481, 3, 89, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4482, 3, 90, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4483, 3, 91, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4484, 3, 92, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4485, 3, 93, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4486, 3, 94, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4487, 3, 95, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4488, 3, 96, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4489, 3, 97, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4490, 3, 98, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4491, 3, 99, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4492, 3, 100, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4493, 3, 101, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4494, 3, 102, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4495, 3, 103, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4496, 3, 104, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4497, 3, 105, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4498, 3, 106, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4499, 3, 107, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4500, 3, 108, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4501, 3, 109, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4502, 3, 110, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4503, 3, 111, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4504, 3, 112, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4505, 3, 113, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4506, 3, 114, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4507, 3, 115, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4508, 3, 116, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4509, 3, 117, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4510, 3, 118, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4511, 3, 119, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4512, 3, 120, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4513, 3, 121, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4514, 3, 122, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4515, 3, 123, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4516, 3, 124, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4517, 3, 125, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4518, 3, 126, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4519, 3, 127, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4520, 3, 128, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4521, 3, 129, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4522, 3, 130, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4523, 3, 131, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4524, 3, 132, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4525, 3, 133, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4526, 3, 134, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4527, 3, 135, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4528, 3, 136, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4529, 3, 137, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4530, 3, 138, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4531, 3, 139, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4532, 3, 140, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4533, 3, 141, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4534, 3, 142, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4535, 3, 143, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4536, 3, 144, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4537, 3, 145, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4538, 3, 151, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4539, 3, 152, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4540, 3, 153, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4541, 3, 154, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4542, 3, 155, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4543, 3, 156, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4544, 3, 157, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4545, 3, 158, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4546, 3, 159, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4547, 3, 160, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4548, 3, 165, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4549, 3, 166, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4550, 3, 167, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4551, 3, 168, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4552, 3, 169, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4553, 3, 170, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4554, 3, 171, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4555, 3, 172, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4556, 3, 178, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4557, 3, 179, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4558, 3, 180, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4559, 3, 181, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4560, 3, 182, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4561, 3, 183, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4562, 3, 184, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4563, 3, 185, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4564, 3, 186, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4565, 3, 187, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4566, 3, 188, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4567, 3, 189, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4568, 3, 190, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4569, 3, 191, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4570, 3, 192, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4571, 3, 193, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4572, 3, 194, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4573, 3, 200, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4574, 3, 24, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4575, 3, 25, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4576, 3, 22, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4577, 3, 207, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4578, 3, 211, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4579, 3, 202, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4580, 3, 203, '2022-09-10 12:36:39', '2022-09-10 12:36:39'),
(4581, 11, 6, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4582, 11, 7, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4583, 11, 8, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4584, 11, 9, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4585, 11, 10, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4586, 11, 11, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4587, 11, 12, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4588, 11, 13, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4589, 11, 14, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4590, 11, 15, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4591, 11, 21, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4592, 11, 22, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4593, 11, 23, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4594, 11, 24, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4595, 11, 25, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4596, 11, 26, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4597, 11, 27, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4598, 11, 28, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4599, 11, 29, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4600, 11, 30, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4601, 11, 66, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4602, 11, 67, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4603, 11, 68, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4604, 11, 69, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4605, 11, 70, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4606, 11, 71, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4607, 11, 72, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4608, 11, 73, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4609, 11, 74, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4610, 11, 75, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4611, 11, 76, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4612, 11, 77, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4613, 11, 78, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4614, 11, 79, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4615, 11, 80, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4616, 11, 81, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4617, 11, 82, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4618, 11, 83, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4619, 11, 84, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4620, 11, 85, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4621, 11, 86, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4622, 11, 87, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4623, 11, 88, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4624, 11, 89, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4625, 11, 90, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4626, 11, 91, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4627, 11, 92, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4628, 11, 93, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4629, 11, 94, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4630, 11, 95, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4631, 11, 96, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4632, 11, 97, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4633, 11, 98, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4634, 11, 99, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4635, 11, 100, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4636, 11, 101, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4637, 11, 102, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4638, 11, 103, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4639, 11, 104, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4640, 11, 105, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4641, 11, 106, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4642, 11, 107, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4643, 11, 108, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4644, 11, 109, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4645, 11, 110, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4646, 11, 111, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4647, 11, 112, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4648, 11, 113, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4649, 11, 114, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4650, 11, 115, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4651, 11, 116, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4652, 11, 117, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4653, 11, 118, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4654, 11, 119, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4655, 11, 120, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4656, 11, 121, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4657, 11, 122, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4658, 11, 123, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4659, 11, 124, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4660, 11, 125, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4661, 11, 126, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4662, 11, 127, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4663, 11, 128, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4664, 11, 129, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4665, 11, 130, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4666, 11, 131, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4667, 11, 132, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4668, 11, 133, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4669, 11, 134, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4670, 11, 135, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4671, 11, 136, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4672, 11, 137, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4673, 11, 138, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4674, 11, 139, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4675, 11, 140, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4676, 11, 141, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4677, 11, 142, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4678, 11, 143, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4679, 11, 144, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4680, 11, 145, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4681, 11, 151, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4682, 11, 152, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4683, 11, 153, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4684, 11, 154, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4685, 11, 155, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4686, 11, 156, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4687, 11, 157, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4688, 11, 158, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4689, 11, 159, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4690, 11, 160, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4691, 11, 165, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4692, 11, 166, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4693, 11, 167, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4694, 11, 168, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4695, 11, 169, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4696, 11, 170, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4697, 11, 171, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4698, 11, 172, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4699, 11, 178, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4700, 11, 179, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4701, 11, 180, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4702, 11, 181, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4703, 11, 182, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4704, 11, 183, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4705, 11, 184, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4706, 11, 185, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4707, 11, 188, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4708, 11, 189, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4709, 11, 186, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4710, 11, 187, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4711, 11, 190, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4712, 11, 191, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4713, 11, 192, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4714, 11, 193, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4715, 11, 194, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4716, 11, 200, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4717, 11, 202, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4718, 11, 203, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4719, 11, 207, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4720, 11, 211, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4721, 11, 208, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4722, 11, 209, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4723, 11, 210, '2022-09-10 12:38:16', '2022-09-10 12:38:16'),
(4724, 2, 6, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4725, 2, 7, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4726, 2, 8, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4727, 2, 9, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4728, 2, 10, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4729, 2, 11, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4730, 2, 12, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4731, 2, 13, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4732, 2, 14, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4733, 2, 15, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4734, 2, 16, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4735, 2, 17, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4736, 2, 18, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4737, 2, 19, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4738, 2, 20, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4739, 2, 21, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4740, 2, 22, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4741, 2, 23, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4742, 2, 24, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4743, 2, 25, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4744, 2, 26, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4745, 2, 27, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4746, 2, 28, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4747, 2, 29, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4748, 2, 30, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4749, 2, 56, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4750, 2, 57, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4751, 2, 58, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4752, 2, 59, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4753, 2, 60, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4754, 2, 66, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4755, 2, 67, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4756, 2, 68, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4757, 2, 69, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4758, 2, 70, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4759, 2, 71, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4760, 2, 72, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4761, 2, 73, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4762, 2, 74, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4763, 2, 75, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4764, 2, 76, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4765, 2, 77, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4766, 2, 78, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4767, 2, 79, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4768, 2, 80, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4769, 2, 81, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4770, 2, 82, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4771, 2, 83, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4772, 2, 84, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4773, 2, 85, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4774, 2, 86, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4775, 2, 87, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4776, 2, 88, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4777, 2, 89, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4778, 2, 90, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4779, 2, 91, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4780, 2, 92, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4781, 2, 93, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4782, 2, 94, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4783, 2, 95, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4784, 2, 96, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4785, 2, 97, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4786, 2, 98, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4787, 2, 99, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4788, 2, 100, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4789, 2, 101, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4790, 2, 102, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4791, 2, 103, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4792, 2, 104, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4793, 2, 105, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4794, 2, 106, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4795, 2, 107, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4796, 2, 108, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4797, 2, 109, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4798, 2, 110, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4799, 2, 111, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4800, 2, 112, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4801, 2, 113, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4802, 2, 114, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4803, 2, 115, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4804, 2, 116, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4805, 2, 117, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4806, 2, 118, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4807, 2, 119, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4808, 2, 120, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4809, 2, 121, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4810, 2, 122, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4811, 2, 123, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4812, 2, 124, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4813, 2, 125, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4814, 2, 126, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4815, 2, 127, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4816, 2, 128, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4817, 2, 129, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4818, 2, 130, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4819, 2, 131, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4820, 2, 132, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4821, 2, 133, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4822, 2, 134, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4823, 2, 135, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4824, 2, 136, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4825, 2, 137, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4826, 2, 138, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4827, 2, 139, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4828, 2, 140, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4829, 2, 141, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4830, 2, 142, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4831, 2, 143, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4832, 2, 144, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4833, 2, 145, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4834, 2, 151, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4835, 2, 152, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4836, 2, 153, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4837, 2, 154, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4838, 2, 155, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4839, 2, 156, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4840, 2, 157, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4841, 2, 158, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4842, 2, 159, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4843, 2, 160, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4844, 2, 165, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4845, 2, 166, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4846, 2, 167, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4847, 2, 168, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4848, 2, 169, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4849, 2, 170, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4850, 2, 171, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4851, 2, 172, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4852, 2, 178, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4853, 2, 179, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4854, 2, 180, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4855, 2, 181, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4856, 2, 182, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4857, 2, 183, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4858, 2, 184, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4859, 2, 185, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4860, 2, 186, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4861, 2, 187, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4862, 2, 188, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4863, 2, 189, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4864, 2, 190, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4865, 2, 191, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4866, 2, 192, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4867, 2, 193, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4868, 2, 194, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4869, 2, 200, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4870, 2, 201, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4871, 2, 202, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4872, 2, 203, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4873, 2, 206, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4874, 2, 207, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4875, 2, 211, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4876, 2, 208, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4877, 2, 209, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4878, 2, 210, '2022-09-10 13:53:01', '2022-09-10 13:53:01'),
(4879, 2, 212, '2022-09-10 13:53:01', '2022-09-10 13:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `websockets_statistics_entries`
--

CREATE TABLE `websockets_statistics_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `app_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

CREATE TABLE `words` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `activities_emp_id_foreign` (`emp_id`),
  ADD KEY `activities_branch_id_foreign` (`branch_id`),
  ADD KEY `activities_parent_id_foreign` (`parent_id`),
  ADD KEY `activities_ip_id_foreign` (`ip_id`),
  ADD KEY `activities_patient_id_foreign` (`patient_id`),
  ADD KEY `activities_shift_id_foreign` (`shift_id`),
  ADD KEY `activities_category_id_foreign` (`category_id`),
  ADD KEY `activities_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `activities_created_by_foreign` (`created_by`),
  ADD KEY `activities_edited_by_foreign` (`edited_by`),
  ADD KEY `activities_approved_by_foreign` (`approved_by`),
  ADD KEY `activities_action_by_foreign` (`action_by`);

--
-- Indexes for table `activity_assignes`
--
ALTER TABLE `activity_assignes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_assignes_activity_id_foreign` (`activity_id`),
  ADD KEY `activity_assignes_assigned_by_foreign` (`assigned_by`),
  ADD KEY `activity_assignes_user_id_foreign` (`user_id`);

--
-- Indexes for table `activity_classifications`
--
ALTER TABLE `activity_classifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`);

--
-- Indexes for table `activity_options`
--
ALTER TABLE `activity_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_time_logs`
--
ALTER TABLE `activity_time_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_time_logs_activity_id_foreign` (`activity_id`);

--
-- Indexes for table `admin_files`
--
ALTER TABLE `admin_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_files_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `admin_files_created_by_foreign` (`created_by`);

--
-- Indexes for table `agencies`
--
ALTER TABLE `agencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agency_weekly_hours`
--
ALTER TABLE `agency_weekly_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agency_weekly_hours_user_id_foreign` (`user_id`);

--
-- Indexes for table `assigne_modules`
--
ALTER TABLE `assigne_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigne_modules_user_id_foreign` (`user_id`),
  ADD KEY `assigne_modules_module_id_foreign` (`module_id`);

--
-- Indexes for table `assign_tasks`
--
ALTER TABLE `assign_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assign_tasks_task_id_foreign` (`task_id`),
  ADD KEY `assign_tasks_user_id_foreign` (`user_id`),
  ADD KEY `assign_tasks_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookmarks_bookmark_master_id_foreign` (`bookmark_master_id`),
  ADD KEY `bookmarks_user_id_foreign` (`user_id`);

--
-- Indexes for table `bookmark_masters`
--
ALTER TABLE `bookmark_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_masters`
--
ALTER TABLE `category_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_masters_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `category_masters_created_by_foreign` (`created_by`),
  ADD KEY `category_masters_parent_id_foreign` (`parent_id`),
  ADD KEY `category_masters_category_type_id_foreign` (`category_type_id`);

--
-- Indexes for table `category_types`
--
ALTER TABLE `category_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_types_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `category_types_created_by_foreign` (`created_by`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`),
  ADD KEY `comments_created_by_foreign` (`created_by`),
  ADD KEY `comments_replied_to_foreign` (`replied_to`),
  ADD KEY `comments_edited_by_foreign` (`edited_by`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_settings_user_id_foreign` (`user_id`);

--
-- Indexes for table `company_types`
--
ALTER TABLE `company_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_types_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `company_types_created_by_foreign` (`created_by`);

--
-- Indexes for table `company_work_shifts`
--
ALTER TABLE `company_work_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_work_shifts_top_most_parent_id_foreign` (`top_most_parent_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_user_id_foreign` (`user_id`),
  ADD KEY `departments_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `departments_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `deviations`
--
ALTER TABLE `deviations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deviations_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `deviations_activity_id_foreign` (`activity_id`),
  ADD KEY `deviations_branch_id_foreign` (`branch_id`),
  ADD KEY `deviations_patient_id_foreign` (`patient_id`),
  ADD KEY `deviations_emp_id_foreign` (`emp_id`),
  ADD KEY `deviations_category_id_foreign` (`category_id`),
  ADD KEY `deviations_subcategory_id_foreign` (`subcategory_id`);

--
-- Indexes for table `device_login_history`
--
ALTER TABLE `device_login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_login_history_user_id_foreign` (`user_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emergency_contacts_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `emergency_contacts_user_id_foreign` (`user_id`),
  ADD KEY `emergency_contacts_created_by_foreign` (`created_by`);

--
-- Indexes for table `employee_assigned_working_hours`
--
ALTER TABLE `employee_assigned_working_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_assigned_working_hours_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `employee_assigned_working_hours_emp_id_foreign` (`emp_id`),
  ADD KEY `employee_assigned_working_hours_created_by_foreign` (`created_by`);

--
-- Indexes for table `employee_types`
--
ALTER TABLE `employee_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `files_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `files_folder_id_foreign` (`folder_id`),
  ADD KEY `files_created_by_foreign` (`created_by`),
  ADD KEY `files_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `file_access_logs`
--
ALTER TABLE `file_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_access_logs_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `file_access_logs_admin_file_id_foreign` (`admin_file_id`),
  ADD KEY `file_access_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folders_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `folders_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `followup_completes`
--
ALTER TABLE `followup_completes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followup_completes_follow_up_id_foreign` (`follow_up_id`),
  ADD KEY `followup_completes_question_id_foreign` (`question_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ip_assigne_to_employees`
--
ALTER TABLE `ip_assigne_to_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_assigne_to_employees_ip_id_foreign` (`ip_id`),
  ADD KEY `ip_assigne_to_employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `ip_follow_ups`
--
ALTER TABLE `ip_follow_ups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_follow_ups_ip_id_foreign` (`ip_id`),
  ADD KEY `ip_follow_ups_patient_id_foreign` (`patient_id`),
  ADD KEY `ip_follow_ups_branch_id_foreign` (`branch_id`),
  ADD KEY `ip_follow_ups_parent_id_foreign` (`parent_id`),
  ADD KEY `ip_follow_ups_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `ip_follow_ups_created_by_foreign` (`created_by`),
  ADD KEY `ip_follow_ups_edited_by_foreign` (`edited_by`),
  ADD KEY `ip_follow_ups_approved_by_foreign` (`approved_by`),
  ADD KEY `ip_follow_ups_action_by_foreign` (`action_by`);

--
-- Indexes for table `ip_follow_up_creations`
--
ALTER TABLE `ip_follow_up_creations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_follow_up_creations_ip_id_foreign` (`ip_id`),
  ADD KEY `ip_follow_up_creations_follow_up_id_foreign` (`follow_up_id`);

--
-- Indexes for table `ip_templates`
--
ALTER TABLE `ip_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_templates_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `ip_templates_created_by_foreign` (`created_by`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journals_activity_id_foreign` (`activity_id`),
  ADD KEY `journals_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `journals_branch_id_foreign` (`branch_id`),
  ADD KEY `journals_patient_id_foreign` (`patient_id`),
  ADD KEY `journals_emp_id_foreign` (`emp_id`),
  ADD KEY `journals_category_id_foreign` (`category_id`),
  ADD KEY `journals_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `journals_edited_by_foreign` (`edited_by`),
  ADD KEY `journals_signed_by_foreign` (`signed_by`);

--
-- Indexes for table `journal_actions`
--
ALTER TABLE `journal_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_actions_journal_id_foreign` (`journal_id`),
  ADD KEY `journal_actions_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `journal_actions_edited_by_foreign` (`edited_by`),
  ADD KEY `journal_actions_signed_by_foreign` (`signed_by`);

--
-- Indexes for table `journal_action_logs`
--
ALTER TABLE `journal_action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_action_logs_journal_action_id_foreign` (`journal_action_id`),
  ADD KEY `journal_action_logs_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `journal_action_logs_edited_by_foreign` (`edited_by`);

--
-- Indexes for table `journal_logs`
--
ALTER TABLE `journal_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_logs_journal_id_foreign` (`journal_id`),
  ADD KEY `journal_logs_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `journal_logs_edited_by_foreign` (`edited_by`);

--
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `labels_group_id_foreign` (`group_id`),
  ADD KEY `labels_language_id_foreign` (`language_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobile_bank_id_login_logs`
--
ALTER TABLE `mobile_bank_id_login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module_requests`
--
ALTER TABLE `module_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_requests_user_id_foreign` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `o_v_hours`
--
ALTER TABLE `o_v_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `o_v_hours_top_most_parent_id_foreign` (`top_most_parent_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paragraphs`
--
ALTER TABLE `paragraphs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paragraphs_top_most_parent_id_foreign` (`top_most_parent_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `patient_cashiers`
--
ALTER TABLE `patient_cashiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_cashiers_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `patient_cashiers_branch_id_foreign` (`branch_id`),
  ADD KEY `patient_cashiers_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `patient_implementation_plans`
--
ALTER TABLE `patient_implementation_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_implementation_plans_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `patient_implementation_plans_user_id_foreign` (`user_id`),
  ADD KEY `patient_implementation_plans_branch_id_foreign` (`branch_id`),
  ADD KEY `patient_implementation_plans_parent_id_foreign` (`parent_id`),
  ADD KEY `patient_implementation_plans_category_id_foreign` (`category_id`),
  ADD KEY `patient_implementation_plans_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `patient_implementation_plans_created_by_foreign` (`created_by`),
  ADD KEY `patient_implementation_plans_edited_by_foreign` (`edited_by`),
  ADD KEY `patient_implementation_plans_approved_by_foreign` (`approved_by`),
  ADD KEY `patient_implementation_plans_action_by_foreign` (`action_by`);

--
-- Indexes for table `patient_information`
--
ALTER TABLE `patient_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_information_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_se_name_unique` (`se_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `personal_info_during_ips`
--
ALTER TABLE `personal_info_during_ips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_during_ips_patient_id_foreign` (`patient_id`),
  ADD KEY `personal_info_during_ips_ip_id_foreign` (`ip_id`),
  ADD KEY `personal_info_during_ips_user_id_foreign` (`user_id`),
  ADD KEY `personal_info_during_ips_follow_up_id_foreign` (`follow_up_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `questions_created_by_foreign` (`created_by`);

--
-- Indexes for table `request_for_approvals`
--
ALTER TABLE `request_for_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_for_approvals_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `request_for_approvals_requested_by_foreign` (`requested_by`),
  ADD KEY `request_for_approvals_requested_to_foreign` (`requested_to`),
  ADD KEY `request_for_approvals_rejected_by_foreign` (`rejected_by`),
  ADD KEY `request_for_approvals_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roles_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `roles_user_type_id_foreign` (`user_type_id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `salary_details`
--
ALTER TABLE `salary_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `schedules_branch_id_foreign` (`branch_id`),
  ADD KEY `schedules_user_id_foreign` (`user_id`),
  ADD KEY `schedules_patient_id_foreign` (`patient_id`),
  ADD KEY `schedules_shift_id_foreign` (`shift_id`),
  ADD KEY `schedules_parent_id_foreign` (`parent_id`),
  ADD KEY `schedules_created_by_foreign` (`created_by`),
  ADD KEY `schedules_slot_assigned_to_foreign` (`slot_assigned_to`),
  ADD KEY `schedules_employee_assigned_working_hour_id_foreign` (`employee_assigned_working_hour_id`),
  ADD KEY `schedules_schedule_template_id_foreign` (`schedule_template_id`),
  ADD KEY `schedules_leave_approved_by_foreign` (`leave_approved_by`);

--
-- Indexes for table `schedule_stampling_datewise_reports`
--
ALTER TABLE `schedule_stampling_datewise_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_stampling_datewise_reports_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `schedule_stampling_datewise_reports_user_id_foreign` (`user_id`);

--
-- Indexes for table `schedule_templates`
--
ALTER TABLE `schedule_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_templates_top_most_parent_id_foreign` (`top_most_parent_id`);

--
-- Indexes for table `schedule_template_data`
--
ALTER TABLE `schedule_template_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_template_data_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `schedule_template_data_schedule_template_id_foreign` (`schedule_template_id`),
  ADD KEY `schedule_template_data_shift_id_foreign` (`shift_id`),
  ADD KEY `schedule_template_data_created_by_foreign` (`created_by`);

--
-- Indexes for table `shift_assignes`
--
ALTER TABLE `shift_assignes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_assignes_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `shift_assignes_user_id_foreign` (`user_id`),
  ADD KEY `shift_assignes_created_by_foreign` (`created_by`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stamplings`
--
ALTER TABLE `stamplings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stamplings_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `stamplings_user_id_foreign` (`user_id`),
  ADD KEY `stamplings_schedule_id_foreign` (`schedule_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_user_id_foreign` (`user_id`),
  ADD KEY `subscriptions_package_id_foreign` (`package_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `tasks_branch_id_foreign` (`branch_id`),
  ADD KEY `tasks_parent_id_foreign` (`parent_id`),
  ADD KEY `tasks_user_type_id_foreign` (`user_type_id`),
  ADD KEY `tasks_created_by_foreign` (`created_by`),
  ADD KEY `tasks_edited_by_foreign` (`edited_by`),
  ADD KEY `tasks_action_by_foreign` (`action_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_unique_id_unique` (`unique_id`),
  ADD UNIQUE KEY `users_custom_unique_id_unique` (`custom_unique_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_user_type_id_foreign` (`user_type_id`),
  ADD KEY `users_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `users_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `user_scheduled_dates`
--
ALTER TABLE `user_scheduled_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_scheduled_dates_top_most_parent_id_foreign` (`top_most_parent_id`),
  ADD KEY `user_scheduled_dates_emp_id_foreign` (`emp_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_type_has_permissions`
--
ALTER TABLE `user_type_has_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type_has_permissions_user_type_id_foreign` (`user_type_id`),
  ADD KEY `user_type_has_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`),
  ADD KEY `words_top_most_parent_id_foreign` (`top_most_parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_assignes`
--
ALTER TABLE `activity_assignes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_classifications`
--
ALTER TABLE `activity_classifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `activity_options`
--
ALTER TABLE `activity_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `activity_time_logs`
--
ALTER TABLE `activity_time_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_files`
--
ALTER TABLE `admin_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agencies`
--
ALTER TABLE `agencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `agency_weekly_hours`
--
ALTER TABLE `agency_weekly_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assigne_modules`
--
ALTER TABLE `assigne_modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `assign_tasks`
--
ALTER TABLE `assign_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookmark_masters`
--
ALTER TABLE `bookmark_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `category_masters`
--
ALTER TABLE `category_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `category_types`
--
ALTER TABLE `category_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_types`
--
ALTER TABLE `company_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_work_shifts`
--
ALTER TABLE `company_work_shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deviations`
--
ALTER TABLE `deviations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_login_history`
--
ALTER TABLE `device_login_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_assigned_working_hours`
--
ALTER TABLE `employee_assigned_working_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_types`
--
ALTER TABLE `employee_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_access_logs`
--
ALTER TABLE `file_access_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followup_completes`
--
ALTER TABLE `followup_completes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `ip_assigne_to_employees`
--
ALTER TABLE `ip_assigne_to_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_follow_ups`
--
ALTER TABLE `ip_follow_ups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_follow_up_creations`
--
ALTER TABLE `ip_follow_up_creations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_templates`
--
ALTER TABLE `ip_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_actions`
--
ALTER TABLE `journal_actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_action_logs`
--
ALTER TABLE `journal_action_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_logs`
--
ALTER TABLE `journal_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3115;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=431;

--
-- AUTO_INCREMENT for table `mobile_bank_id_login_logs`
--
ALTER TABLE `mobile_bank_id_login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `module_requests`
--
ALTER TABLE `module_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `o_v_hours`
--
ALTER TABLE `o_v_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paragraphs`
--
ALTER TABLE `paragraphs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `patient_cashiers`
--
ALTER TABLE `patient_cashiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_implementation_plans`
--
ALTER TABLE `patient_implementation_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_information`
--
ALTER TABLE `patient_information`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_info_during_ips`
--
ALTER TABLE `personal_info_during_ips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `request_for_approvals`
--
ALTER TABLE `request_for_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `salary_details`
--
ALTER TABLE `salary_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_stampling_datewise_reports`
--
ALTER TABLE `schedule_stampling_datewise_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_templates`
--
ALTER TABLE `schedule_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_template_data`
--
ALTER TABLE `schedule_template_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_assignes`
--
ALTER TABLE `shift_assignes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stamplings`
--
ALTER TABLE `stamplings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_scheduled_dates`
--
ALTER TABLE `user_scheduled_dates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_type_has_permissions`
--
ALTER TABLE `user_type_has_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4880;

--
-- AUTO_INCREMENT for table `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `words`
--
ALTER TABLE `words`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_emp_id_foreign` FOREIGN KEY (`emp_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_ip_id_foreign` FOREIGN KEY (`ip_id`) REFERENCES `patient_implementation_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shift_assignes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_assignes`
--
ALTER TABLE `activity_assignes`
  ADD CONSTRAINT `activity_assignes_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_assignes_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_assignes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_time_logs`
--
ALTER TABLE `activity_time_logs`
  ADD CONSTRAINT `activity_time_logs_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_files`
--
ALTER TABLE `admin_files`
  ADD CONSTRAINT `admin_files_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_files_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `agency_weekly_hours`
--
ALTER TABLE `agency_weekly_hours`
  ADD CONSTRAINT `agency_weekly_hours_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assigne_modules`
--
ALTER TABLE `assigne_modules`
  ADD CONSTRAINT `assigne_modules_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigne_modules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assign_tasks`
--
ALTER TABLE `assign_tasks`
  ADD CONSTRAINT `assign_tasks_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assign_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assign_tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD CONSTRAINT `bank_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_bookmark_master_id_foreign` FOREIGN KEY (`bookmark_master_id`) REFERENCES `bookmark_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_masters`
--
ALTER TABLE `category_masters`
  ADD CONSTRAINT `category_masters_category_type_id_foreign` FOREIGN KEY (`category_type_id`) REFERENCES `category_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_masters_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_masters_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_masters_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_types`
--
ALTER TABLE `category_types`
  ADD CONSTRAINT `category_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_types_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_replied_to_foreign` FOREIGN KEY (`replied_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD CONSTRAINT `company_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_types`
--
ALTER TABLE `company_types`
  ADD CONSTRAINT `company_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_types_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_work_shifts`
--
ALTER TABLE `company_work_shifts`
  ADD CONSTRAINT `company_work_shifts_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deviations`
--
ALTER TABLE `deviations`
  ADD CONSTRAINT `deviations_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deviations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deviations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deviations_emp_id_foreign` FOREIGN KEY (`emp_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deviations_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deviations_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deviations_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `device_login_history`
--
ALTER TABLE `device_login_history`
  ADD CONSTRAINT `device_login_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_contacts_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_assigned_working_hours`
--
ALTER TABLE `employee_assigned_working_hours`
  ADD CONSTRAINT `employee_assigned_working_hours_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_assigned_working_hours_emp_id_foreign` FOREIGN KEY (`emp_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_assigned_working_hours_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `file_access_logs`
--
ALTER TABLE `file_access_logs`
  ADD CONSTRAINT `file_access_logs_admin_file_id_foreign` FOREIGN KEY (`admin_file_id`) REFERENCES `admin_files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_access_logs_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_access_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `followup_completes`
--
ALTER TABLE `followup_completes`
  ADD CONSTRAINT `followup_completes_follow_up_id_foreign` FOREIGN KEY (`follow_up_id`) REFERENCES `ip_follow_ups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followup_completes_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_assigne_to_employees`
--
ALTER TABLE `ip_assigne_to_employees`
  ADD CONSTRAINT `ip_assigne_to_employees_ip_id_foreign` FOREIGN KEY (`ip_id`) REFERENCES `patient_implementation_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_assigne_to_employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_follow_ups`
--
ALTER TABLE `ip_follow_ups`
  ADD CONSTRAINT `ip_follow_ups_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_ip_id_foreign` FOREIGN KEY (`ip_id`) REFERENCES `patient_implementation_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ip_follow_ups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_ups_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_follow_up_creations`
--
ALTER TABLE `ip_follow_up_creations`
  ADD CONSTRAINT `ip_follow_up_creations_follow_up_id_foreign` FOREIGN KEY (`follow_up_id`) REFERENCES `ip_follow_ups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_follow_up_creations_ip_id_foreign` FOREIGN KEY (`ip_id`) REFERENCES `patient_implementation_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_templates`
--
ALTER TABLE `ip_templates`
  ADD CONSTRAINT `ip_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ip_templates_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `journals_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_emp_id_foreign` FOREIGN KEY (`emp_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_signed_by_foreign` FOREIGN KEY (`signed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journals_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_actions`
--
ALTER TABLE `journal_actions`
  ADD CONSTRAINT `journal_actions_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_actions_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_actions_signed_by_foreign` FOREIGN KEY (`signed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_actions_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_action_logs`
--
ALTER TABLE `journal_action_logs`
  ADD CONSTRAINT `journal_action_logs_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_action_logs_journal_action_id_foreign` FOREIGN KEY (`journal_action_id`) REFERENCES `journal_actions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_action_logs_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_logs`
--
ALTER TABLE `journal_logs`
  ADD CONSTRAINT `journal_logs_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_logs_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `journal_logs_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `labels_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `module_requests`
--
ALTER TABLE `module_requests`
  ADD CONSTRAINT `module_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `o_v_hours`
--
ALTER TABLE `o_v_hours`
  ADD CONSTRAINT `o_v_hours_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paragraphs`
--
ALTER TABLE `paragraphs`
  ADD CONSTRAINT `paragraphs_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_cashiers`
--
ALTER TABLE `patient_cashiers`
  ADD CONSTRAINT `patient_cashiers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_cashiers_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_cashiers_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_implementation_plans`
--
ALTER TABLE `patient_implementation_plans`
  ADD CONSTRAINT `patient_implementation_plans_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `patient_implementation_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `category_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_implementation_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_information`
--
ALTER TABLE `patient_information`
  ADD CONSTRAINT `patient_information_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `personal_info_during_ips`
--
ALTER TABLE `personal_info_during_ips`
  ADD CONSTRAINT `personal_info_during_ips_follow_up_id_foreign` FOREIGN KEY (`follow_up_id`) REFERENCES `ip_follow_ups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `personal_info_during_ips_ip_id_foreign` FOREIGN KEY (`ip_id`) REFERENCES `patient_implementation_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `personal_info_during_ips_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `personal_info_during_ips_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_for_approvals`
--
ALTER TABLE `request_for_approvals`
  ADD CONSTRAINT `request_for_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_for_approvals_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_for_approvals_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_for_approvals_requested_to_foreign` FOREIGN KEY (`requested_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_for_approvals_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_details`
--
ALTER TABLE `salary_details`
  ADD CONSTRAINT `salary_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_employee_assigned_working_hour_id_foreign` FOREIGN KEY (`employee_assigned_working_hour_id`) REFERENCES `employee_assigned_working_hours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_leave_approved_by_foreign` FOREIGN KEY (`leave_approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_schedule_template_id_foreign` FOREIGN KEY (`schedule_template_id`) REFERENCES `schedule_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `company_work_shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_slot_assigned_to_foreign` FOREIGN KEY (`slot_assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_stampling_datewise_reports`
--
ALTER TABLE `schedule_stampling_datewise_reports`
  ADD CONSTRAINT `schedule_stampling_datewise_reports_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_stampling_datewise_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_templates`
--
ALTER TABLE `schedule_templates`
  ADD CONSTRAINT `schedule_templates_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_template_data`
--
ALTER TABLE `schedule_template_data`
  ADD CONSTRAINT `schedule_template_data_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_template_data_schedule_template_id_foreign` FOREIGN KEY (`schedule_template_id`) REFERENCES `schedule_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_template_data_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `company_work_shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_template_data_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_assignes`
--
ALTER TABLE `shift_assignes`
  ADD CONSTRAINT `shift_assignes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_assignes_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_assignes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stamplings`
--
ALTER TABLE `stamplings`
  ADD CONSTRAINT `stamplings_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stamplings_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stamplings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_scheduled_dates`
--
ALTER TABLE `user_scheduled_dates`
  ADD CONSTRAINT `user_scheduled_dates_emp_id_foreign` FOREIGN KEY (`emp_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_scheduled_dates_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_type_has_permissions`
--
ALTER TABLE `user_type_has_permissions`
  ADD CONSTRAINT `user_type_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_type_has_permissions_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `words`
--
ALTER TABLE `words`
  ADD CONSTRAINT `words_top_most_parent_id_foreign` FOREIGN KEY (`top_most_parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
