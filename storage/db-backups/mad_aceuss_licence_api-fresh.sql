-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 29, 2022 at 11:32 AM
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
-- Database: `mad_aceuss_licence_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `licence_histories`
--

CREATE TABLE `licence_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `licence_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_attached` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_from` date DEFAULT NULL COMMENT 'licence_key activation date',
  `expire_at` date NOT NULL COMMENT 'expiry date',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `licence_histories`
--

INSERT INTO `licence_histories` (`id`, `top_most_parent_id`, `created_by`, `licence_key`, `module_attached`, `package_details`, `active_from`, `expire_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'ZYX1I-N3O9Q-Q7R8N-3NIJE', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', '2022-09-29 16:27:27', '2022-09-29 16:27:27', NULL),
(2, 3, 1, '8K26G-YTJKC-35NCJ-D93Y7', '[\"1\",\"2\"]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', '2022-09-29 16:31:23', '2022-09-29 16:31:23', NULL),
(3, 4, 1, '7YR4V-KTSON-U3ZFX-FTT8V', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', '2022-09-29 16:37:16', '2022-09-29 16:37:16', NULL),
(4, 5, 1, '2IYKO-EYHTK-QA9AB-OWL7W', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', '2022-09-29 16:46:07', '2022-09-29 16:46:07', NULL),
(5, 6, 1, '1VHHU-5A8L4-QC4H2-PAG7U', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', '2022-09-29 16:50:49', '2022-09-29 16:50:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `licence_key_management`
--

CREATE TABLE `licence_key_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_most_parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User Table id',
  `licence_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_attached` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_from` date DEFAULT NULL COMMENT 'licence_key activation date',
  `expire_at` date NOT NULL COMMENT 'expiry date',
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `is_expired` tinyint(1) NOT NULL DEFAULT '0',
  `reason_for_cancellation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `licence_key_management`
--

INSERT INTO `licence_key_management` (`id`, `top_most_parent_id`, `created_by`, `cancelled_by`, `licence_key`, `module_attached`, `package_details`, `active_from`, `expire_at`, `is_used`, `is_expired`, `reason_for_cancellation`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, NULL, 'ZYX1I-N3O9Q-Q7R8N-3NIJE', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', 1, 0, NULL, '2022-09-29 16:27:27', '2022-09-29 16:27:27', NULL),
(2, 3, 1, NULL, '8K26G-YTJKC-35NCJ-D93Y7', '[\"1\",\"2\"]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', 1, 0, NULL, '2022-09-29 16:31:24', '2022-09-29 16:31:24', NULL),
(3, 4, 1, NULL, '7YR4V-KTSON-U3ZFX-FTT8V', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', 1, 0, NULL, '2022-09-29 16:37:16', '2022-09-29 16:37:16', NULL),
(4, 5, 1, NULL, '2IYKO-EYHTK-QA9AB-OWL7W', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', 1, 0, NULL, '2022-09-29 16:46:07', '2022-09-29 16:46:07', NULL),
(5, 6, 1, NULL, '1VHHU-5A8L4-QC4H2-PAG7U', '[4,3,2,1]', '{\"id\":1,\"name\":\"Basic pack\",\"price\":540,\"is_on_offer\":1,\"discount_type\":\"1\",\"discount_value\":67,\"discounted_price\":178.19999999999998863131622783839702606201171875,\"validity_in_days\":100,\"number_of_patients\":100,\"number_of_employees\":50,\"bankid_charges\":null,\"sms_charges\":null,\"is_sms_enable\":0,\"is_enable_bankid_charges\":0,\"status\":1,\"entry_mode\":null,\"created_at\":\"2022-09-29T10:31:37.000000Z\",\"updated_at\":\"2022-09-29T10:31:37.000000Z\",\"deleted_at\":null}', '2022-09-29', '2023-01-07', 1, 0, NULL, '2022-09-29 16:50:49', '2022-09-29 16:50:49', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `licence_histories`
--
ALTER TABLE `licence_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `licence_key_management`
--
ALTER TABLE `licence_key_management`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `licence_histories`
--
ALTER TABLE `licence_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `licence_key_management`
--
ALTER TABLE `licence_key_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
