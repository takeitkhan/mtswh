-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 27, 2025 at 04:39 AM
-- Server version: 8.0.13
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mts_warehouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_name` enum('Unit','Brand','Contact Type') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `unique_name`, `value`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Unit', 'Piece', 'piece', 'Active', NULL, NULL),
(2, 'Brand', 'Brand One', 'brand-one', 'Active', NULL, NULL),
(3, 'Contact Type', 'Customer', 'customer', 'Active', NULL, '2025-09-13 06:09:54'),
(4, 'Contact Type', 'Supplier', 'supplier', 'Active', NULL, '2025-09-13 06:10:07'),
(5, 'Contact Type', 'Sub Supplier', 'sub-supplier', 'Active', NULL, '2025-09-13 06:18:50'),
(6, 'Contact Type', 'Vendor', 'vendor', 'Active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_type` enum('Inclusive','Exclusive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_percent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_type` enum('Inclusive','Exclusive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `phone`, `email`, `address`, `license_no`, `contact_person`, `contact_person_no`, `vat_type`, `vat_percent`, `tax_type`, `tax_percent`, `note`, `created_at`, `updated_at`) VALUES
(1, 'BL', '230482304982', 'bl@banglalink.com', '2304892-03, ljss, wlewlew', '2309232', 'Banglalink Manager', '2039284203984', 'Inclusive', NULL, 'Inclusive', NULL, NULL, '2025-09-13 06:26:24', '2025-09-13 06:26:24');

-- --------------------------------------------------------

--
-- Stand-in structure for view `faulty_products`
-- (See below for the actual view)
--
CREATE TABLE `faulty_products` (
`barcode_format` enum('Tag','Without-Tag')
,`barcode_prefix` varchar(255)
,`brand_id` int(11)
,`category_id` int(11)
,`code` varchar(255)
,`created_at` timestamp
,`description` text
,`faulty_product` double
,`faulty_product_bundle` decimal(32,0)
,`id` bigint(20) unsigned
,`name` varchar(255)
,`product_type` varchar(255)
,`slug` varchar(255)
,`stock_qty_alert` int(11)
,`unique_key` varchar(255)
,`unit_id` int(11)
,`updated_at` timestamp
,`user_id` bigint(20)
,`warehouse_id` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `global_settings`
--

CREATE TABLE `global_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_type` enum('Text','Textarea','Select','Richeditor','Number','Checkbox') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_group` enum('General','Homepage','Header Section') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_order` int(11) DEFAULT NULL,
  `meta_placeholder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `global_settings`
--

INSERT INTO `global_settings` (`id`, `meta_title`, `meta_name`, `meta_value`, `meta_type`, `meta_group`, `meta_order`, `meta_placeholder`, `created_at`, `updated_at`) VALUES
(1, 'PPI Auto Approve Boss', 'ppi_auto_approve_boss', NULL, 'Checkbox', NULL, NULL, NULL, NULL, '2021-11-11 05:59:26'),
(2, 'Boss User ID', 'boos_user_id', '23', 'Text', NULL, NULL, 'Type Boss user ID', NULL, '2021-11-09 12:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ppi_bundle_products`
--

CREATE TABLE `ppi_bundle_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_product_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `bundle_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bundle_size` int(11) NOT NULL,
  `bundle_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ppi_data_accumulated`
-- (See below for the actual view)
--
CREATE TABLE `ppi_data_accumulated` (
`action_format` enum('Ppi','Spi')
,`action_performed_by` bigint(20) unsigned
,`created_at` timestamp
,`created_dates` text
,`id` bigint(20) unsigned
,`note` varchar(255)
,`ppi_ids` text
,`ppi_spi_type` enum('Supply','Service','Other')
,`product_id` bigint(20) unsigned
,`project` varchar(255)
,`purchase` varchar(100)
,`source_type` varchar(255)
,`tran_type` enum('With Money','Without Money','Other')
,`transferable` varchar(50)
,`updated_at` timestamp
,`warehouse_id` bigint(20) unsigned
,`who_source` text
);

-- --------------------------------------------------------

--
-- Table structure for table `ppi_products`
--

CREATE TABLE `ppi_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `qty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_state` enum('New','Used','Cut-Piece') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `health_status` enum('Useable','Scrapped','Faulty') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppi_products`
--

INSERT INTO `ppi_products` (`id`, `ppi_id`, `warehouse_id`, `product_id`, `qty`, `unit_price`, `price`, `product_state`, `health_status`, `note`, `action_performed_by`, `created_at`, `updated_at`) VALUES
(2, 2, 1, 1, '123', '0', '0.00', 'Used', 'Scrapped', NULL, 33, '2025-09-21 14:34:39', '2025-09-21 14:34:39');

-- --------------------------------------------------------

--
-- Table structure for table `ppi_set_products`
--

CREATE TABLE `ppi_set_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `set_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ppi_product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ppi_site_based_products`
-- (See below for the actual view)
--
CREATE TABLE `ppi_site_based_products` (
`bundle_size` decimal(32,0)
,`faulty_qty` double
,`id` bigint(20) unsigned
,`name` varchar(255)
,`scrapped_qty` double
,`site_name` varchar(255)
,`total_qty` double
);

-- --------------------------------------------------------

--
-- Table structure for table `ppi_spis`
--

CREATE TABLE `ppi_spis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action_format` enum('Ppi','Spi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ppi_spi_type` enum('Supply','Service','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `project` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tran_type` enum('With Money','Without Money','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `transferable` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'if transferable value will be yes',
  `purchase` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'if purchase value should be yes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppi_spis`
--

INSERT INTO `ppi_spis` (`id`, `action_format`, `ppi_spi_type`, `project`, `tran_type`, `note`, `warehouse_id`, `action_performed_by`, `transferable`, `purchase`, `created_at`, `updated_at`) VALUES
(2, 'Ppi', 'Service', 'BL_Relocation', 'Without Money', 'B Warehouse', 1, 33, NULL, NULL, '2025-09-21 14:34:22', '2025-09-21 14:34:22');

-- --------------------------------------------------------

--
-- Table structure for table `ppi_spi_disputes`
--

CREATE TABLE `ppi_spi_disputes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_status_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_id` bigint(20) UNSIGNED NOT NULL,
  `status_for` enum('Ppi','Spi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ppi_spi_product_id` bigint(20) UNSIGNED NOT NULL,
  `issue_column` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `action_format` enum('Dispute','Correction') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correction_dispute_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ppi_spi_histories`
--

CREATE TABLE `ppi_spi_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_id` bigint(20) UNSIGNED NOT NULL,
  `action_format` enum('Ppi','Spi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `chunck_old_data` longtext COLLATE utf8mb4_unicode_ci,
  `chunck_new_data` longtext COLLATE utf8mb4_unicode_ci,
  `status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `action_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppi_spi_histories`
--

INSERT INTO `ppi_spi_histories` (`id`, `ppi_spi_id`, `action_format`, `chunck_old_data`, `chunck_new_data`, `status_id`, `action_performed_by`, `action_time`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ppi', '{\"ppi_basic_info\":{\"id\":1,\"action_format\":\"Ppi\",\"ppi_spi_type\":\"Service\",\"project\":\"BL_Relocation\",\"tran_type\":\"Without Money\",\"note\":\"Test_Site_One\",\"warehouse_id\":1,\"action_performed_by\":33,\"transferable\":null,\"purchase\":null,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},\"ppi_source\":[{\"id\":1,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Customer\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":2,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Vendor\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":3,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Site\",\"who_source\":\"Test_Site_One\",\"who_source_id\":null,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"}],\"ppi_products\":[],\"ppi_set_products\":[],\"ppi_bundle_products\":[]}', '{\"ppi_basic_info\":{\"id\":1,\"action_format\":\"Ppi\",\"ppi_spi_type\":\"Service\",\"project\":\"BL_Relocation\",\"tran_type\":\"Without Money\",\"note\":\"Test_Site_One\",\"warehouse_id\":1,\"action_performed_by\":33,\"transferable\":null,\"purchase\":null,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},\"ppi_source\":[{\"id\":1,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Customer\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":2,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Vendor\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":3,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Site\",\"who_source\":\"Test_Site_One\",\"who_source_id\":null,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"}],\"ppi_products\":[{\"id\":1,\"ppi_id\":1,\"warehouse_id\":1,\"product_id\":1,\"qty\":\"100\",\"unit_price\":\"0\",\"price\":\"0.00\",\"product_state\":\"New\",\"health_status\":\"Useable\",\"note\":null,\"action_performed_by\":33,\"created_at\":\"2025-09-13T06:34:26.000000Z\",\"updated_at\":\"2025-09-13T06:34:26.000000Z\"}],\"ppi_set_products\":[],\"ppi_bundle_products\":[]}', 2, 33, '2025-09-13 12:34:26', '2025-09-13 06:34:26', '2025-09-13 06:34:26'),
(2, 1, 'Ppi', '{\"ppi_basic_info\":{\"id\":1,\"action_format\":\"Ppi\",\"ppi_spi_type\":\"Service\",\"project\":\"BL_Relocation\",\"tran_type\":\"Without Money\",\"note\":\"Test_Site_One\",\"warehouse_id\":1,\"action_performed_by\":33,\"transferable\":null,\"purchase\":null,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},\"ppi_source\":[{\"id\":1,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Customer\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":2,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Vendor\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":3,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Site\",\"who_source\":\"Test_Site_One\",\"who_source_id\":null,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"}],\"ppi_products\":[{\"id\":1,\"ppi_id\":1,\"warehouse_id\":1,\"product_id\":1,\"qty\":\"100\",\"unit_price\":\"0\",\"price\":\"0.00\",\"product_state\":\"New\",\"health_status\":\"Useable\",\"note\":null,\"action_performed_by\":33,\"created_at\":\"2025-09-13T06:34:26.000000Z\",\"updated_at\":\"2025-09-13T06:34:26.000000Z\"}],\"ppi_set_products\":[],\"ppi_bundle_products\":[]}', '{\"ppi_basic_info\":{\"id\":1,\"action_format\":\"Ppi\",\"ppi_spi_type\":\"Service\",\"project\":\"BL_Relocation\",\"tran_type\":\"Without Money\",\"note\":\"Test_Site_One\",\"warehouse_id\":1,\"action_performed_by\":33,\"transferable\":null,\"purchase\":null,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},\"ppi_source\":[{\"id\":1,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Customer\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":2,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Vendor\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"},{\"id\":3,\"ppi_spi_id\":1,\"action_format\":\"Ppi\",\"source_type\":\"Site\",\"who_source\":\"Test_Site_One\",\"who_source_id\":null,\"levels\":\"3\",\"warehouse_id\":1,\"created_at\":\"2025-09-13T06:27:34.000000Z\",\"updated_at\":\"2025-09-13T06:27:34.000000Z\"}],\"ppi_products\":[],\"ppi_set_products\":[],\"ppi_bundle_products\":[]}', 3, 33, '2025-09-21 20:27:45', '2025-09-21 14:27:45', '2025-09-21 14:27:45'),
(3, 2, 'Ppi', '{\"ppi_basic_info\":{\"id\":2,\"action_format\":\"Ppi\",\"ppi_spi_type\":\"Service\",\"project\":\"BL_Relocation\",\"tran_type\":\"Without Money\",\"note\":\"B Warehouse\",\"warehouse_id\":1,\"action_performed_by\":33,\"transferable\":null,\"purchase\":null,\"created_at\":\"2025-09-21T14:34:22.000000Z\",\"updated_at\":\"2025-09-21T14:34:22.000000Z\"},\"ppi_source\":[{\"id\":4,\"ppi_spi_id\":2,\"action_format\":\"Ppi\",\"source_type\":\"Customer\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"2\",\"warehouse_id\":1,\"created_at\":\"2025-09-21T14:34:22.000000Z\",\"updated_at\":\"2025-09-21T14:34:22.000000Z\"},{\"id\":5,\"ppi_spi_id\":2,\"action_format\":\"Ppi\",\"source_type\":\"Warehouse\",\"who_source\":\"BL Warehouse\",\"who_source_id\":null,\"levels\":\"2\",\"warehouse_id\":1,\"created_at\":\"2025-09-21T14:34:22.000000Z\",\"updated_at\":\"2025-09-21T14:34:22.000000Z\"}],\"ppi_products\":[],\"ppi_set_products\":[],\"ppi_bundle_products\":[]}', '{\"ppi_basic_info\":{\"id\":2,\"action_format\":\"Ppi\",\"ppi_spi_type\":\"Service\",\"project\":\"BL_Relocation\",\"tran_type\":\"Without Money\",\"note\":\"B Warehouse\",\"warehouse_id\":1,\"action_performed_by\":33,\"transferable\":null,\"purchase\":null,\"created_at\":\"2025-09-21T14:34:22.000000Z\",\"updated_at\":\"2025-09-21T14:34:22.000000Z\"},\"ppi_source\":[{\"id\":4,\"ppi_spi_id\":2,\"action_format\":\"Ppi\",\"source_type\":\"Customer\",\"who_source\":\"BL\",\"who_source_id\":1,\"levels\":\"2\",\"warehouse_id\":1,\"created_at\":\"2025-09-21T14:34:22.000000Z\",\"updated_at\":\"2025-09-21T14:34:22.000000Z\"},{\"id\":5,\"ppi_spi_id\":2,\"action_format\":\"Ppi\",\"source_type\":\"Warehouse\",\"who_source\":\"BL Warehouse\",\"who_source_id\":null,\"levels\":\"2\",\"warehouse_id\":1,\"created_at\":\"2025-09-21T14:34:22.000000Z\",\"updated_at\":\"2025-09-21T14:34:22.000000Z\"}],\"ppi_products\":[{\"id\":2,\"ppi_id\":2,\"warehouse_id\":1,\"product_id\":1,\"qty\":\"123\",\"unit_price\":\"0\",\"price\":\"0.00\",\"product_state\":\"Used\",\"health_status\":\"Scrapped\",\"note\":null,\"action_performed_by\":33,\"created_at\":\"2025-09-21T14:34:39.000000Z\",\"updated_at\":\"2025-09-21T14:34:39.000000Z\"}],\"ppi_set_products\":[],\"ppi_bundle_products\":[]}', 5, 33, '2025-09-21 20:34:39', '2025-09-21 14:34:39', '2025-09-21 14:34:39');

-- --------------------------------------------------------

--
-- Table structure for table `ppi_spi_notifications`
--

CREATE TABLE `ppi_spi_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL,
  `is_read` int(11) NOT NULL DEFAULT '0',
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppi_spi_notifications`
--

INSERT INTO `ppi_spi_notifications` (`id`, `status_id`, `is_read`, `action_performed_by`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 33, '2025-09-13 06:27:36', '2025-09-13 06:27:36'),
(2, 2, 2, 33, '2025-09-13 06:34:27', '2025-09-13 06:34:27'),
(3, 3, 2, 33, '2025-09-21 14:27:45', '2025-09-21 14:27:45'),
(4, 4, 2, 33, '2025-09-21 14:34:23', '2025-09-21 14:34:23'),
(5, 5, 2, 33, '2025-09-21 14:34:40', '2025-09-21 14:34:40'),
(6, 4, 2, 1, '2025-09-21 14:50:58', '2025-09-21 14:50:58'),
(7, 5, 2, 1, '2025-09-21 14:50:58', '2025-09-21 14:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `ppi_spi_sources`
--

CREATE TABLE `ppi_spi_sources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_format` enum('Ppi','Spi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `who_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `who_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `levels` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppi_spi_sources`
--

INSERT INTO `ppi_spi_sources` (`id`, `ppi_spi_id`, `action_format`, `source_type`, `who_source`, `who_source_id`, `levels`, `warehouse_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ppi', 'Customer', 'BL', 1, '3', 1, '2025-09-13 06:27:34', '2025-09-13 06:27:34'),
(2, 1, 'Ppi', 'Vendor', 'BL', 1, '3', 1, '2025-09-13 06:27:34', '2025-09-13 06:27:34'),
(3, 1, 'Ppi', 'Site', 'Test_Site_One', NULL, '3', 1, '2025-09-13 06:27:34', '2025-09-13 06:27:34'),
(4, 2, 'Ppi', 'Customer', 'BL', 1, '2', 1, '2025-09-21 14:34:22', '2025-09-21 14:34:22'),
(5, 2, 'Ppi', 'Warehouse', 'BL Warehouse', NULL, '2', 1, '2025-09-21 14:34:22', '2025-09-21 14:34:22');

-- --------------------------------------------------------

--
-- Table structure for table `ppi_spi_statuses`
--

CREATE TABLE `ppi_spi_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_id` bigint(20) UNSIGNED NOT NULL,
  `status_for` enum('Ppi','Spi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `status_order` int(11) DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_type` enum('success','danger','warning','info','purple','success-complete') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_format` enum('Main','Optional') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci,
  `ppi_spi_product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppi_spi_statuses`
--

INSERT INTO `ppi_spi_statuses` (`id`, `ppi_spi_id`, `status_for`, `warehouse_id`, `code`, `action_performed_by`, `status_order`, `message`, `status_type`, `status_format`, `note`, `ppi_spi_product_id`, `created_at`, `updated_at`) VALUES
(4, 2, 'Ppi', 1, 'ppi_created', 33, 1, 'Ppi created', 'success', 'Main', NULL, NULL, '2025-09-21 14:34:22', '2025-09-21 14:34:22'),
(5, 2, 'Ppi', 1, 'ppi_product_added', 33, 2, 'Product Added in PPI', 'success', 'Optional', 'Product: Test 1', '2', '2025-09-21 14:34:39', '2025-09-21 14:34:39');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `brand_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `warehouse_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode_format` enum('Tag','Without-Tag') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode_prefix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unique_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_qty_alert` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `code`, `slug`, `description`, `brand_id`, `unit_id`, `warehouse_id`, `product_type`, `barcode_format`, `barcode_prefix`, `unique_key`, `stock_qty_alert`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 33, 'Test 1', 'TEST1', NULL, 'Test 1', 2, 1, '1', 'Supply,Service', 'Without-Tag', NULL, 'MTSb7f6', 20, 1, '2025-09-13 05:57:10', '2025-09-13 05:58:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `parent_id`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Category 1', 'category_1_203a', NULL, 'Category 1', NULL, '2025-09-13 05:28:51', '2025-09-13 05:28:51');

-- --------------------------------------------------------

--
-- Table structure for table `product_stocks`
--

CREATE TABLE `product_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_id` bigint(20) UNSIGNED NOT NULL,
  `action_format` enum('Ppi','Spi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ppi_spi_product_id` bigint(20) UNSIGNED NOT NULL,
  `from_ppi_product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'for api',
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `bundle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_unique_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_action` enum('In','Out') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_type` enum('Existing','New','Purchase') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `entry_date` date DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Supply','Service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `code`, `type`, `customer`, `vendor`, `note`, `created_at`, `updated_at`) VALUES
(1, 'BL_Relocation', 'BL_Relocation', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(2, 'Edotco_Smart lock', 'Edotco_Smart lock', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(3, 'Base_Edotco_Rollout', 'Base_Edotco_Rollout', 'Service', 'Base Technologies Ltd', 'Base Technologies Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(4, 'SCL_Maintanance_Narshingdi', 'SCL_Maintanance_Narshingdi', 'Service', 'Summit Communications Ltd', 'Summit Communications Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(5, 'SCL_Maintanance_Gazipur', 'SCL_Maintanance_Gazipur', 'Service', 'Suumit Communications Ltd', 'Suumit Communications Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(6, 'SCL_Maintanance_Tangail', 'SCL_Maintanance_Tangail', 'Service', 'Summit Communications Ltd', 'Summit Communications Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(7, 'Cable Pulling SCL Maintanance_Narshingdi', 'Cable Pulling SCL Maintanance_Narshingdi', 'Service', 'Summit Communications Ltd', 'Summit Communications Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(8, 'Cable Pulling SCL Maintanance_Gazipur', 'Cable Pulling SCL Maintanance_Gazipur', 'Service', 'Summit Communications Ltd', 'Summit Communications Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(9, 'BL_BTS Dismantle', 'BL_BTS Dismantle', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(10, 'Cable Pulling SCL Maintanance_Tangail', 'Cable Pulling SCL Maintanance_Tangail', 'Service', 'Summit Communications Ltd', 'Summit Communications Ltd', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(11, 'Edotco_RMS', 'Edotco_RMS', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(12, 'BL_Sidearm_Supply_Installation', 'BL_Sidearm_Supply_Installation', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(13, 'BL_TX Rack Installation & Supply', 'BL_TX Rack Installation & Supply', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(14, 'BL_Pico_BTS', 'BL_Pico_BTS', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:17', '2025-09-13 05:59:17'),
(15, 'Bl_NewBTS_Rollout', 'Bl_NewBTS_Rollout', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(16, 'MRT_E.Co_Rollout', 'MRT_E.Co_Rollout', 'Service', 'E.Co', 'E.Co', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(17, 'BL_Low_Revenue', 'BL_Low_Revenue', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(18, 'BL_DT_Support', 'BL_DT_Support', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(19, 'BL_Antenna_Swap', 'BL_Antenna_Swap', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(20, 'BL_SPMS Service', 'BL_SPMS Service', 'Service', 'Banglalink', 'BL', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(21, 'BL_VSWR', 'BL_VSWR', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(22, 'BL New Rollout 2021-2022 Without Civil', 'BL New Rollout 2021-2022 Without Civil', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(23, 'BL_Grounding Restoration Project', 'BL_Grounding Restoration Project', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(24, 'BL New Rollout 2021-2022 With Civil', 'BL New Rollout 2021-2022 With Civil', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(25, 'LTE modernization', 'LTE modernization', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(26, 'Smart lock/RMS Rectification', 'Smart lock/RMS Rectification', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(27, 'Parameter Check', 'Parameter Check', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(28, 'Edotco DPC', 'Edotco DPC', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(29, 'NEC_MW', 'NEC_MW', 'Service', 'NEC', 'NEC', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(30, 'Vehicle Used For Site', 'Vehicle Used For Site', 'Service', 'All', 'All', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(31, 'Vehicle Maintanance Expense', 'Vehicle Maintanance Expense', 'Service', 'Vehicle Maintanance Expense', 'Vehicle Maintanance Expense', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(32, 'Labour Costing_MTS Warehouse', 'Labour Costing_MTS Warehouse', 'Service', 'Labour Costing_MTS Warehouse', 'Labour Costing_MTS Warehouse', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(33, 'Asset Tag Installation_BL', 'Asset Tag Installation_BL', 'Service', 'BL', 'BL', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(34, 'BL_Cell Edition', 'BL_Cell Edition', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(35, 'PBR', 'PBR', 'Service', 'BL', 'BL', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(36, 'BL_L900', 'BL_L900', 'Service', 'BL', 'BL', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(37, 'NEC_GP_Dismantle', 'NEC_GP_Dismantle', 'Service', 'NEC', 'NEC', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(38, 'NEC_GP_New Link', 'NEC_GP_New Link', 'Service', 'NEC_GP', 'NEC_GP', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(39, 'Robi red sea', 'Robi red sea', 'Service', 'Ericsson', 'Ericsson', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(40, 'EDOTCO_DCDB_Installation', 'EDOTCO_DCDB_Installation', 'Service', 'EDOTCO', 'EDOTCO', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(41, '2196 MW Link Dismantle', '2196 MW Link Dismantle', 'Service', 'Robi', 'Robi', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(42, 'Edotco_Relocation_Power_DC System', 'Edotco_Relocation_Power_DC System', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(43, 'SAQ', 'SAQ', 'Service', 'banglalink', 'banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(44, 'EDOTCO_Battery_Protection', 'EDOTCO_Battery_Protection', 'Service', 'EDOTCO', 'EDOTCO', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(45, 'Edotco_New Rollout', 'Edotco_New Rollout', 'Service', 'edotco', 'edotco', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(46, 'Edotco rectifier swap', 'Edotco rectifier swap', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(47, 'CCD L2600 colo_Survey', 'CCD L2600 colo_Survey', 'Service', 'Ericsson', 'Ericsson', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(48, 'MW Physical Survey', 'MW Physical Survey', 'Service', 'Robi', 'Robi', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(49, 'Battery Optimization', 'Battery Optimization', 'Service', 'Edotco', 'Edotco', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(50, 'Robi New Site RAN_MW Warriors', 'Robi New Site RAN_MW Warriors', 'Service', 'Robi', 'MTS', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(51, 'Huawei Robi MW link Swap Project', 'Huawei Robi MW link Swap Project', 'Service', 'Huawei', 'Huawei', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(52, 'Huawei RMS Survey', 'Huawei RMS Survey', 'Service', 'Huawei RMS Survey', 'Huawei RMS Survey', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(53, 'EDOTCO_BL_Colo Dismantle Project', 'EDOTCO_BL_Colo Dismantle Project', 'Service', 'EDOTCO_BL_Colo Dismantle Project', 'EDOTCO_BL_Colo Dismantle Project', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(54, 'Huawei RMS installation project', 'Huawei RMS installation project', 'Service', 'Huawei RMS installation project', 'Huawei RMS installation project', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(55, 'Edotco Huawei RMS Swap', 'Edotco Huawei RMS Swap', 'Service', 'Edotco Huawei RMS Swap', 'Edotco Huawei RMS Swap', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(56, 'BL Opex Saving RRU Add Remove', 'BL Opex Saving RRU Add Remove', 'Service', 'BL', 'BL', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(57, 'Courier Bill Maintaince', 'Courier Bill Maintaince', 'Service', 'MTS', 'MTS', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(58, 'Edotco Battery Deployment Project', 'Edotco Battery Deployment Project', 'Service', 'Edotco Battery Deployment Project', 'Edotco Battery Deployment Project', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(59, 'Edotco_Colo', 'Edotco_Colo', 'Service', 'Edotco_Colo', 'Edotco_Colo', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(60, 'BL MW link', 'BL MW link', 'Service', 'BL MW link', 'BL MW link', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(61, '4 PA Upgradation', '4 PA Upgradation', 'Service', 'Banglalink', 'Banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18'),
(62, 'TDD Add & Remove', 'TDD Add & Remove', 'Service', 'banglalink', 'banglalink', NULL, '2025-09-13 05:59:18', '2025-09-13 05:59:18');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_vendors`
--

CREATE TABLE `purchase_vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `spi_id` bigint(20) UNSIGNED NOT NULL,
  `spi_product_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `qty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `create_ppi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `create_ppi_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Global','General','Custom') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `code`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super_admin', 'Global', NULL, '2025-09-06 09:00:12'),
(3, 'User', 'user', 'General', NULL, '2024-10-23 04:48:50'),
(4, 'Subordinate Manager', 'subordinate_manager', 'Custom', NULL, '2025-09-21 16:53:53'),
(6, 'Warehouse Manager', 'warehouse_manager', 'Custom', '2021-07-28 11:06:30', '2025-09-10 09:28:35'),
(13, 'Boss', 'boss', 'Custom', '2021-09-24 14:55:58', '2025-09-09 07:20:42'),
(16, 'Report Permission', 'report_permission', 'General', '2024-10-23 04:50:19', '2024-10-28 18:36:39'),
(17, 'Boss Permission', 'boss_permission', 'General', '2024-10-28 18:42:26', '2024-10-28 18:42:26'),
(18, 'BL', 'bl', 'General', '2024-11-13 18:26:45', '2024-11-13 22:40:24');

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`id`, `role_id`, `user_id`, `warehouse_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, '2025-09-21 16:40:31'),
(2, 3, 1, NULL, NULL, '2025-09-21 16:40:31'),
(3, 3, 18, NULL, '2025-09-21 16:43:45', '2025-09-21 16:43:45'),
(4, 3, 23, NULL, '2025-09-21 16:43:48', '2025-09-21 16:43:48'),
(5, 3, 24, NULL, '2025-09-21 16:43:51', '2025-09-21 16:43:51'),
(6, 3, 26, NULL, '2025-09-21 16:43:54', '2025-09-21 16:43:54'),
(7, 3, 33, NULL, '2025-09-21 16:43:58', '2025-09-21 16:43:58'),
(8, 3, 34, NULL, '2025-09-21 16:44:01', '2025-09-21 16:44:01'),
(9, 4, 33, 1, '2025-09-21 16:52:44', '2025-09-21 16:52:44');

-- --------------------------------------------------------

--
-- Table structure for table `route_groups`
--

CREATE TABLE `route_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `route_groups`
--

INSERT INTO `route_groups` (`id`, `name`, `code`, `route_order`, `created_at`, `updated_at`) VALUES
(105, 'Role', 'role', NULL, '2021-09-21 06:07:57', '2021-09-21 06:07:57'),
(106, 'User', 'user', NULL, '2021-09-21 06:07:57', '2021-09-21 06:07:57'),
(107, 'Routelist', 'routelist', NULL, '2021-09-21 06:07:57', '2021-09-21 06:07:57'),
(108, 'Warehouse', 'warehouse', NULL, '2021-09-21 06:07:57', '2021-09-21 06:07:57'),
(109, 'Product', 'product', NULL, '2021-09-21 06:07:58', '2021-09-21 06:07:58'),
(110, 'PPI', 'ppi', NULL, '2021-09-21 06:07:58', '2021-09-21 06:07:58'),
(111, 'Project', 'project', NULL, '2021-09-21 06:07:59', '2021-09-21 06:07:59'),
(112, 'Contact', 'contact', NULL, '2021-09-21 06:07:59', '2021-09-21 06:07:59'),
(113, 'Attribute', 'attribute', NULL, '2021-09-21 06:07:59', '2021-09-21 06:07:59'),
(114, 'PPI Action', 'ppiaction', NULL, '2021-09-26 05:22:38', '2021-09-26 05:22:38'),
(115, 'PPI Elements', 'ppielements', NULL, '2022-01-12 19:05:34', '2022-01-12 19:05:34'),
(116, 'SPI', 'spi', NULL, '2022-02-07 13:16:55', '2022-02-07 13:16:55'),
(117, 'SPI Action', 'spiaction', NULL, '2022-03-07 22:21:02', '2022-03-07 22:21:02'),
(118, 'Report', 'report', NULL, '2022-03-23 04:13:30', '2022-03-23 04:13:30');

-- --------------------------------------------------------

--
-- Table structure for table `route_lists`
--

CREATE TABLE `route_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_parameter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_group` bigint(20) UNSIGNED DEFAULT NULL,
  `route_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_order` int(11) DEFAULT NULL,
  `route_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_menu` enum('Yes','No') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_menu_id` int(11) DEFAULT NULL,
  `dashboard_position` set('Left','Right','Top','Bottom') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_for` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_show_as` enum('Yes','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `route_lists`
--

INSERT INTO `route_lists` (`id`, `route_title`, `route_name`, `route_parameter`, `route_description`, `route_group`, `route_icon`, `route_order`, `route_hash`, `show_menu`, `parent_menu_id`, `dashboard_position`, `show_for`, `is_show_as`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'admin_dashboard', '', 'Dashboard', NULL, 'fas fa-th', NULL, '$2y$10$iw/TeZUhuanUCf/6eAHxnOnmsyDLczVHIhwPfAabYcLmCS/IG2Siq', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(2, 'Notifications', 'admin_notifications', '', 'Notifications', NULL, 'fa-regular fa-bell', 9, '$2y$10$LidyeuMNYw1bQZ398aAB0eR.i56PC0vhAUGypJe3yYV53hr9VEgUi', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:55', '2025-09-21 14:59:11'),
(3, 'Global Settings', 'admin_global_settings', '', 'Global Settings', NULL, 'fas fa-cog', NULL, '$2y$10$YxRjy9bghYLu01InrfYhLuo5rG7A/UArap/Hm1L1CyYvNRAJGywkK', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(4, 'Manage Roles', 'role_index', '', 'Manage Roles', 105, 'far fa-folder', NULL, '$2y$10$9uYEPDQFDRIOPyR7xu/6KONY8ZeN/YK5EDKQlpzi.kSqbRkKFFD06', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(5, 'Add', 'role_create', '', 'Add', 105, 'far fa-folder', NULL, '$2y$10$UQYnQ9JEsFvCZhfSSFKkGu4eAI30RcC4tssxTz/sAeRXIJyeH1Tpm', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(6, 'Edit', 'role_edit', '', 'Edit', 105, 'far fa-folder', NULL, '$2y$10$rlVFMG2uB/0HnYX3Qw9RHOQdYAOz2GA7hgi6XyrCkqTZejxzJph2C', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(7, 'Delete', 'role_destroy', '', 'Delete', 105, 'far fa-folder', NULL, '$2y$10$SvBwwLWhPq76mDvpPWGGdeaFxa46NgO6DWIpk8U2fPcewidUSSdpm', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(8, 'Manage Users', 'user_index', '', 'Manage Users', 106, 'far fa-folder', NULL, '$2y$10$MDI.KHCVzzCZUInNf3ucaeoBbwuT9Om.0ZBBU8ajsyKSoixhhAbQu', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(9, 'Add', 'user_create', '', 'Add', 106, 'far fa-folder', NULL, '$2y$10$qgoj2xfXcvEPI8my9URx1usXh9B6K/5YbMiTPjkDoZOtfpBQUSsQK', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(10, 'Edit', 'user_edit', '', 'Edit', 106, 'far fa-folder', NULL, '$2y$10$jDLvVf66dGW4YXtBcNRyCev0qB5Jr5vpHI8tey1qI1xdjdixUxoJG', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(11, 'Edit Profile', 'user_edit_profile', '', 'Edit Profile', 106, 'far fa-folder', NULL, '$2y$10$GMmInR.swHWUj69Vus5FHe5GXEK6o2U/IZYHXItLlIMU.noIODtX.', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(12, 'Delete', 'user_destroy', '', 'Delete', 106, 'far fa-folder', NULL, '$2y$10$gtHZr0LCwgFEMXCCZSzfW.QhCA62Qsq6oMmMAsgyfkAjNYtvWw7v6', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(13, 'Manage Route', 'routelist_index', '', 'Manage Route', 107, 'far fa-folder', NULL, '$2y$10$JRgUITx3IKVKNAHtNahutOR11evdv8ROxBR0Dcl6XknQJCvzmtKXa', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:55', '2025-09-10 09:16:55'),
(14, 'Add', 'routelist_create', '', 'Add', 107, 'far fa-folder', NULL, '$2y$10$WU.S8B6tjZqYeoPwy.Djhu0rctlAMbDzy04UZOayC.pi1JADdbPA.', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(15, 'Edit', 'routelist_edit', '', 'Edit', 107, 'far fa-folder', NULL, '$2y$10$qsvb7eT7sAsO1Iru2CxI4OIs8aZYQTb7dJpFMRWai9eW7oS/EG54O', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(16, 'Delete', 'routelist_destroy', '', 'Delete', 107, 'far fa-folder', NULL, '$2y$10$3kQP.ruPeb2umqrvi2Hi1.TRMpFA2QND8f.HFcTH1kqG88q.Y2iL6', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(17, 'Manage Warehouse', 'warehouse_index', '', 'Manage Warehouse', 108, 'far fa-folder', NULL, '$2y$10$tNDROTL8BRnlFgXjyB86o.PvAQVEKeRVNtRJYKUe8ehVEaLMG9Ksq', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(18, 'Add', 'warehouse_create', '', 'Add', 108, 'far fa-folder', NULL, '$2y$10$pcQNl6j/NkVulZeIeYpp4eClXV1qyNJg/t8T2aUKz2ZVnnwE9gQOm', 'Yes', NULL, 'Left', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(19, 'Edit', 'warehouse_edit', '', 'Edit', 108, 'far fa-folder', NULL, '$2y$10$zQgQMbtM9GiU8HgK0K1z5.lIgpoBigIf9Gxs42KZDBLdIzNdbLO1G', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(20, 'Delete', 'warehouse_destroy', '', 'Delete', 108, 'far fa-folder', NULL, '$2y$10$NysFQRPMbAysQl.6TzjNBupm85FR9tWcPT58HmytOML0EUbvszr/e', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(21, 'View Warehouse', 'warehouse_single_index', '', 'View Warehouse', 108, 'far fa-folder', NULL, '$2y$10$M9hsHqrDL5Bh9VBRKUoVJun24vQWq4aut85s5Mp4AwxePgyw6ZAqu', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(22, 'Product Stock', 'report_product_stock', '', 'Product Stock', 118, 'fas fa-th', NULL, '$2y$10$IZ841PPi9tk6BjLhKC4cfOVexGRJMA/1nkYK8AeREHA3fxXh9cRE6', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(23, 'Product Stock Details', 'report_product_stock_details', '', 'Product Stock Details', 118, 'far fa-folder', NULL, '$2y$10$fNwV3TrpWGiT6obzF/JQ5eybR.BgrQ3dW35Bg7iFTCaryfx3kjAde', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(24, 'Product Report Of Site (Ppi)', 'report_ppi_site_based_product_report', '', 'Product Report Of Site (Ppi)', 118, 'fas fa-th', NULL, '$2y$10$BXLusR0ALt420W09.KlrCuU3si.znjGI3NQ3cdcwwE2o/SJ8o8WN6', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(25, 'Product Report Of Site (Spi)', 'report_spi_site_based_product_report', '', 'Product Report Of Site (Spi)', 118, 'fas fa-th', NULL, '$2y$10$0VAV1jp4clIaJYB.IxVs4.Dcu71irhIKNdjeRAQ8t7ldWLZTzsR3e', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(26, 'Ppi Product Use To Spi Product', 'report_ppi_product_to_spi', '', 'Ppi Product Use To Spi Product', 118, 'far fa-folder', NULL, '$2y$10$x.04QQzxf28ahKAb5loDxeSBZ7u88j.Mcd2JArwbA45/dBYnaKTRO', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(27, 'Ppi Spi Accumulated Report', 'report_ppi_spi_accumulated_report', '', 'Ppi Spi Accumulated Report', 118, 'fas fa-th', NULL, '$2y$10$nKAl0H8V/ZI/Rx2AtFhnAOnV51CR4Jl6mOUnFyqAvoxxXNtqrsxLi', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:56', '2025-09-10 09:16:56'),
(28, 'Vendor Report', 'report_vendor_report', '', 'Vendor Report', 118, 'fas fa-th', NULL, '$2y$10$Hz.rrO4FyS2o2/YJy4IzOupieBlV73pWuTpSy2bjdjtErD/M3unze', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(29, 'Purchase Vendor', 'report_purchase_vendor_report', '', 'Purchase Vendor', 118, 'fas fa-th', NULL, '$2y$10$Mfra/Mbnchf1B.NNRoNT3edtl4y0AWR76O5zX3hKgJxVbcLs2jDd.', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(30, 'Scrapped Product', 'report_scrapped_product', '', 'Scrapped Product', 118, 'fas fa-th', NULL, '$2y$10$7dMPLWtsT/jvJCBH9.AgdOp.V00Pckzshw03nkinMQdCqU7GemFj2', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(31, 'Scrapped Product Details', 'report_scrapped_product_details', '', 'Scrapped Product Details', 118, 'far fa-folder', NULL, '$2y$10$moFCBvViNogcGveF1O6Z5Ocb/2vPq8ZGfE3w9oaUY.AwK/IZOtGpi', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(32, 'Faulty Product', 'report_faulty_product', '', 'Faulty Product', 118, 'fas fa-th', NULL, '$2y$10$985Y4kFEJxWzSRcnw1.jjeUnNdjY4fWn05zrZUYcDUgNbCAq01vlq', 'Yes', NULL, 'Left,Top', NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(33, 'Faulty Product Details', 'report_faulty_product_details', '', 'Faulty Product Details', 118, 'far fa-folder', NULL, '$2y$10$kXr4NfOoqt4FJ1kHDCJMiuytGFxTwr9vqniZaHG9Dii780bbQ0d.O', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(34, 'Product Lend Report of Project', 'report_lended_from_project', '', 'Product Lend Report of Project', 118, 'far fa-folder', NULL, '$2y$10$eO/a8nDQVdVjSNrp2TsdleJsymLoJYlC9.tzlSQ8eCu.S/PYH1L1a', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(35, 'Product Lend Return Permit of Project', 'report_lended_from_project_start_return', '', 'Product Lend Return Permit of Project', 118, 'far fa-folder', NULL, '$2y$10$YUltVqGLYoz7iIPK4dlyGu6kaE9MXsd3KESSmc8oGC5lD4BpwORzS', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(36, 'Manage Products', 'product_index', '', 'Manage Products', 109, 'far fa-folder', NULL, '$2y$10$6yYIGWzeql9DDOZSoPuio.8.mVCTyepQOqCj/KUZU9CczQWqRJ.iS', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(37, 'Add Products', 'product_create', '', 'Add Products', 109, 'far fa-folder', NULL, '$2y$10$69QxO0yQjM.CMvZtAh5RuuGWYLy8dUgsS0CKtCzilPLTVlYwfQvui', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(38, 'Edit', 'product_edit', '', 'Edit', 109, 'far fa-folder', NULL, '$2y$10$a8r3XcSXPZPxOnNOulASPu8hQ90oPh34zfyCuxZrUEjGP3QZPMyai', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(39, 'Delete', 'product_destroy', '', 'Delete', 109, 'far fa-folder', NULL, '$2y$10$1Y5jURdYKi8WNSRNH4z1furUIoG0BG.OtBrCSXxCQmkFuoY9UmJ4q', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(40, 'Manage Categories', 'product_category_index', '', 'Manage Categories', 109, 'far fa-folder', NULL, '$2y$10$gNw.mpPIw/zmXx0ZN0Kn1uadov4qPI3fIne3nOMF04yanwG.BsNNW', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(41, 'Edit Category', 'product_category_edit', '', 'Edit Category', 109, 'far fa-folder', NULL, '$2y$10$R47Q/DCL.KuRpE6xNn2e/uzjTkTTMfFCbR2Xg9/2RTTWbgGq.dxya', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:57', '2025-09-10 09:16:57'),
(42, 'Delete Categories', 'product_category_destroy', '', 'Delete Categories', 109, 'far fa-folder', NULL, '$2y$10$xAvs0OHJTj2iTfebbQi2e.yJaypK9XmIJMP9b9OvQnXreLCRwmhbS', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(43, 'Manage PPI', 'ppi_index', '', 'Manage PPI', 110, 'far fa-folder', NULL, '$2y$10$/xFQKh6dt0XMxLC0I7/7kOnOFe1T7PiQN5ZE.oF7SJggUkdQ0tD/e', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(44, 'Create PPI', 'ppi_create', '', 'Create PPI', 110, 'far fa-folder', NULL, '$2y$10$QeIKsOXd0HKxsi.Mrj1p0uH6btRuDCob4jTPqpyUjRSq7d7yy6dLW', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(45, 'Edit', 'ppi_edit', '', 'Edit', 110, 'far fa-folder', NULL, '$2y$10$YsAeFzqRmvqrO6AL6VPmpeCrIcIjZDePdqRfL0HvJaBcJsnDQji8a', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(46, 'Delete PPI', 'ppi_destroy', '', 'Delete PPI', 110, 'far fa-folder', NULL, '$2y$10$YkTsYseK.yN5sQLlUS1GbOu8trLQA1XA3iRwQq3gkDqzOk/Op5AC.', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(47, 'Add Product to Ppi', 'ppi_product_add', '', 'Add Product to Ppi', 110, 'far fa-folder', NULL, '$2y$10$FPAlHTOFWOaaovQxjJKwwuor0Xtim69AwpbEGTQw.8x8gROt.zOeC', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(48, 'Edit Ppi Product', 'ppi_product_edit', '', 'Edit Ppi Product', 110, 'far fa-folder', NULL, '$2y$10$dYYrC6oL8hvxpD/JYrpxE.SonPaR5krMygIxwPaAlUDrUPJJ/GTyu', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(49, 'Delete Product from Ppi', 'ppi_product_destroy', '', 'Delete Product from Ppi', 110, 'far fa-folder', NULL, '$2y$10$kLp6izXwODgbQ1Y/oUyaHePL.Ea1LrByExN79Gai5bO6hMfuWsrwK', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(50, 'Import Product from Another PPI', 'ppi_product_import_from_another_ppi', '', 'Import Product from Another PPI', 110, 'far fa-folder', NULL, '$2y$10$3J4EdY.u4EcsN2KwT1wvtOO1EJt3ijVZqENA0fUClaU4b7Exunoo6', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(51, 'Create Set Product to Ppi', 'ppi_set_product_add', '', 'Create Set Product to Ppi', 110, 'far fa-folder', NULL, '$2y$10$ZjTJkaLJrfGABuf/QRJyteIc/xiZxEHEqHzNnXhcz7H3wxz8iEy.6', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(52, 'Delete Set from Ppi', 'ppi_set_product_destroy', '', 'Delete Set from Ppi', 110, 'far fa-folder', NULL, '$2y$10$RZJSdIAXNwXt6VKO3LkTj.E5bixcf2wjno5m1LcIkQTnuZ4q7cw3e', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(53, 'Delete Product from Set', 'ppi_product_destroy_from_set', '', 'Delete Product from Set', 110, 'far fa-folder', NULL, '$2y$10$fZtA2rawDzpcUqKSfG2RmuBwyDN4chy29.AYXcbU4w9O09SgBfL0m', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(54, 'Barcode Page', 'ppi_get_line_item', '', 'Barcode Page', 110, 'far fa-folder', NULL, '$2y$10$upFTjdGvHz9OjO/cR4JLzOF61nNMBpo7I171Um/X7KBP3ZyymvBdS', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(55, 'Sent to Boss', 'ppi_sent_to_boss_action', '', 'Sent to Boss', 114, 'far fa-folder', NULL, '$2y$10$pJbUI01se7RU6HktCAKQfOU0J9m6i8kV7jQWIBm6aaREDLo0rUb2i', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:58', '2025-09-10 09:16:58'),
(56, 'Sent to Warehouse manager', 'ppi_sent_to_wh_manager_action', '', 'Sent to Warehouse manager', 114, 'far fa-folder', NULL, '$2y$10$Ch2rCQbMwri7sr1gQpf8n.XZINL/0V/DYMA1DsgQZGzEYO5PBXIW.', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(57, 'Ready to physical validation', 'ppi_ready_to_physical_validation_action', '', 'Ready to physical validation', 114, 'far fa-folder', NULL, '$2y$10$KVm1cbcITKwLIeDmVdVlke93ogGwgvQNLG5HYnS8W7N4GvIn639b6', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(58, 'Dispute by Warehouse Manager', 'ppi_dispute_by_wh_manager_action', '', 'Dispute by Warehouse Manager', 114, 'far fa-folder', NULL, '$2y$10$n.7CQaZf0fEaY53JEj5GB.Twm78VEL9KVdFJ3zW.iTn4NtJkq8jwK', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(59, 'Ppi Product Information correction by boss', 'ppi_product_info_correction_by_boss_action', '', 'Ppi Product Information correction by boss', 114, 'far fa-folder', NULL, '$2y$10$0OrwokKpc/BFs9.X5mV9Ledyji9jRGMD.3L7wvEcenYjyrI8Sv00m', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(60, 'Ppi resent to Warehouse Manager', 'ppi_resent_to_wh_manager_action', '', 'Ppi resent to Warehouse Manager', 114, 'far fa-folder', NULL, '$2y$10$nKeoOMA3ux1PI0Mad4sfFeZ8d7TleMh5a5yzddrvCrgFhOSFQQEL.', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(61, 'Ppi Product Price Show', 'ppi_product_price_show_element', '', 'Ppi Product Price Show', 115, 'far fa-folder', NULL, '$2y$10$j5WSsP7NLVS5W4g3iSPxTuHjgI8P5YjZ3CFhU9iIr7ZITgqyuWX1i', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(62, 'Manage SPI', 'spi_index', '', 'Manage SPI', 116, 'far fa-folder', NULL, '$2y$10$NCxA8.n6Yl5Q.WrjeUJDXeH0LY05n5NRfNjBbcb6WY.dk0egRP4ni', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(63, 'Create SPI', 'spi_create', '', 'Create SPI', 116, 'far fa-folder', NULL, '$2y$10$oPkP/b8a5cn/xq8V0bwknu/Kn/iiM5HSDH1qf2P1DhUkSXMnVkMq6', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(64, 'Edit', 'spi_edit', '', 'Edit', 116, 'far fa-folder', NULL, '$2y$10$lk16Nn2xwOvr/fRFuSSI3./lCRTlmE7xlU4zB332bQ4SvR6EyoRkK', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(65, 'Delete SPI', 'spi_destroy', '', 'Delete SPI', 116, 'far fa-folder', NULL, '$2y$10$iLgNKX8mE/1PBRfVChOhIOXpNgf4yprmSMLmyr6zaeS55xSX3RURi', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(66, 'Add Product to Spi', 'spi_product_add', '', 'Add Product to Spi', 116, 'far fa-folder', NULL, '$2y$10$U5itddo2v/uUuhhWoMMTvetTwS9CBceppnsdSF3SxBFxbZRPVzk/u', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(67, 'Edit Ppi Product', 'spi_product_edit', '', 'Edit Ppi Product', 116, 'far fa-folder', NULL, '$2y$10$8RgHi5UpBGJm.72aWZgV8u9HAFOGLBiOF6xaIC.8.hwKsURtY9knW', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(68, 'Delete Product from Spi', 'spi_product_destroy', '', 'Delete Product from Spi', 116, 'far fa-folder', NULL, '$2y$10$R2DIon4tRZoBkWDaeOMNCuA3MZqPRPK70sZd8.MPCqyCbtNXzPZoq', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(69, 'Import Product from Another SPI', 'spi_product_import_from_another_spi', '', 'Import Product from Another SPI', 116, 'far fa-folder', NULL, '$2y$10$z76h.jO3EdvzxC6djmODqO1kQYlTeVM62XL55BXOQE3seTKjsXSwq', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:16:59', '2025-09-10 09:16:59'),
(70, 'Physical Validate Page', 'spi_get_line_item', '', 'Physical Validate Page', 116, 'far fa-folder', NULL, '$2y$10$/lS5RBP.0RyQrQZAduEGv.D7YloHlqcCzvwsiaSAWzIomHs17SOje', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(71, 'Spi Transfer', 'spi_transfer', '', 'Spi Transfer', 116, 'far fa-folder', NULL, '$2y$10$HKmJ6FiZGbs0ceI3wnWQEeUxQ5sltUKkRaj0XXBOxoF8jMz6a7j9C', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(72, 'Product Purchase From Vendor', 'spi_buy_product_form_vendor', '', 'Product Purchase From Vendor', 116, 'far fa-folder', NULL, '$2y$10$lWY3gyuebCuSBUoRNptWJenZYf9Wd0MTGZ3n4DxOblmVyASk4H0JC', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(73, 'Sent to Boss', 'spi_sent_to_boss_action', '', 'Sent to Boss', 117, 'far fa-folder', NULL, '$2y$10$.cuT2RjEaccxg0bsQg81SOlzvg.WPt9qL4zOKEsJDlAPclpZPUJIe', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(74, 'Sent to Warehouse manager', 'spi_sent_to_wh_manager_action', '', 'Sent to Warehouse manager', 117, 'far fa-folder', NULL, '$2y$10$eigp0aPMwuyI0zCfXrEuPuZcv8z/mEEFXZ4qGyus3LzoY.tIUuvue', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(75, 'Ready to physical validation', 'spi_ready_to_physical_validation_action', '', 'Ready to physical validation', 117, 'far fa-folder', NULL, '$2y$10$L0JT.XCEdywZLqIkXklaBuesjQ3wS3g7OJ5GEWV2hsvUHeJQqF182', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(76, 'Dispute by Warehouse Manager', 'spi_dispute_by_wh_manager_action', '', 'Dispute by Warehouse Manager', 117, 'far fa-folder', NULL, '$2y$10$eno8M/PicaM9gIIsYfQp/eYj7tAbUMJ6gf8.ZHUbcwdPJk0/U.qVu', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(77, 'Spi Product Information correction by boss', 'spi_product_info_correction_by_boss_action', '', 'Spi Product Information correction by boss', 117, 'far fa-folder', NULL, '$2y$10$WsQ.Dk12Ot8YRQt8X1zo1.eBeW01XhX45BleY97zt84amkC6/iVhi', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(78, 'Spi resent to Warehouse Manager', 'spi_resent_to_wh_manager_action', '', 'Spi resent to Warehouse Manager', 117, 'far fa-folder', NULL, '$2y$10$IUwkZPMeMBlo9YxWzK4eguw3GQnYe/csq7tb6N105w4.3OcpQZFZq', NULL, NULL, NULL, 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(79, 'SPI Lended', 'spi_lended', '', 'SPI Lended', 116, 'far fa-folder', NULL, '$2y$10$lK5IG5dcvDXyMcJJkWjtEeg1kQzKMtaHi9iqPOI9Rs10hswNmivC2', 'Yes', NULL, 'Left', 'Warehouse', 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(80, 'Manage Project', 'project_index', '', 'Manage Project', 111, 'far fa-folder', NULL, '$2y$10$klVd8RXuoFf/pZiJgmirbeZuvUGsLVEm.9XpFYB0MREeKaMt87FAi', 'Yes', NULL, 'Top', NULL, 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(81, 'Add Project', 'project_create', '', 'Add Project', 111, 'far fa-folder', NULL, '$2y$10$92M2xiRNKm/RZkiZvLsOXeEQtE1BRwfuGgKm6YwX6Izz250QxWoPm', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(82, 'Edit Project', 'project_edit', '', 'Edit Project', 111, 'far fa-folder', NULL, '$2y$10$C5jyQYunTNZFJ3QZvdN7QOe/PuJQBM6eAX6./BttVgydAwn.RsvBm', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(83, 'Delete Project', 'project_destroy', '', 'Delete Project', 111, 'far fa-folder', NULL, '$2y$10$RAtT7rngY1a9KjtfwEKTrey3naVpiIehOPqr0CSzUnfEajOVX74ma', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:17:00', '2025-09-10 09:17:00'),
(84, 'Manage Contact', 'contact_index', '', 'Manage Contact', 112, 'far fa-folder', NULL, '$2y$10$.65unD9FQ5DkhrQnJDtW8.lI8boF.IROQBP2OQ7ewFaTt8RQ6Dj9W', 'Yes', NULL, 'Top', NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01'),
(85, 'Add Contact', 'contact_create', '', 'Add Contact', 112, 'far fa-folder', NULL, '$2y$10$RLEADVebYEDaCPtKgF6IkOc5sqtG.D7YVLBWxJA.dwAdcU6bRb.ye', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01'),
(86, 'Edit Contact', 'contact_edit', '', 'Edit Contact', 112, 'far fa-folder', NULL, '$2y$10$3KM79ZZOg.d4H9nAQYEv.O0WX0CjkYwiyqkh82MZib6s6XUtzB5X.', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01'),
(87, 'Delete Contact', 'contact_destroy', '', 'Delete Contact', 112, 'far fa-folder', NULL, '$2y$10$eyPPDSvCYpCYc51NGOMgFersINeHNdudHArtJCtCFwXnbEGI5h2dO', NULL, NULL, NULL, NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01'),
(88, 'Manage Unit', 'attribute_unit_index', 'Unit', 'Manage Unit', 113, 'far fa-folder', NULL, '$2y$10$lGYQWzLhUwozO50lKsgpVOKoS.yNtJ43x1cxeXXtN3qIj00sZ.y52', 'Yes', NULL, 'Top', NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01'),
(89, 'Manage Brand', 'attribute_brand_index', 'Brand', 'Manage Brand', 113, 'far fa-folder', NULL, '$2y$10$htmc2SrYZP3011OPhnfPE.32I/Yyl4G1oNxOjhLC1LJWeBtRILNTm', 'Yes', NULL, 'Top', NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01'),
(90, 'Manage Contact Type', 'attribute_contact type_index', 'Contact Type', 'Manage Contact Type', 113, 'far fa-folder', NULL, '$2y$10$v67ypA29IY5Of9HgcfFhzuvFXPoKxQuF5W3Pnf6p2wQoJEHRmtmUa', 'Yes', NULL, 'Top', NULL, 'No', '2025-09-10 09:17:01', '2025-09-10 09:17:01');

-- --------------------------------------------------------

--
-- Table structure for table `route_list_roles`
--

CREATE TABLE `route_list_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `show_as` enum('All','User','Permission') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `route_list_roles`
--

INSERT INTO `route_list_roles` (`id`, `role_id`, `route_id`, `show_as`, `created_at`, `updated_at`) VALUES
(1, 4, 36, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(2, 4, 37, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(3, 4, 38, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(4, 4, 39, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(5, 4, 40, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(6, 4, 41, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(7, 4, 42, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(8, 4, 43, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(9, 4, 44, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(10, 4, 45, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(11, 4, 46, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(12, 4, 47, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(13, 4, 48, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(14, 4, 49, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(15, 4, 50, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(16, 4, 51, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(17, 4, 52, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(18, 4, 53, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(19, 4, 54, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(20, 4, 55, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(21, 4, 56, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(22, 4, 57, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(23, 4, 58, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(24, 4, 59, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(25, 4, 60, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(26, 4, 61, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(27, 4, 62, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(28, 4, 63, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(29, 4, 64, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(30, 4, 65, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(31, 4, 66, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(32, 4, 67, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(33, 4, 68, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(34, 4, 69, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(35, 4, 70, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(36, 4, 71, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(37, 4, 72, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(38, 4, 79, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(39, 4, 73, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(40, 4, 74, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(41, 4, 75, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(42, 4, 76, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(43, 4, 77, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53'),
(44, 4, 78, NULL, '2025-09-21 16:53:53', '2025-09-21 16:53:53');

-- --------------------------------------------------------

--
-- Stand-in structure for view `scrapped_products`
-- (See below for the actual view)
--
CREATE TABLE `scrapped_products` (
`barcode_format` enum('Tag','Without-Tag')
,`barcode_prefix` varchar(255)
,`brand_id` int(11)
,`category_id` int(11)
,`code` varchar(255)
,`created_at` timestamp
,`description` text
,`id` bigint(20) unsigned
,`name` varchar(255)
,`product_type` varchar(255)
,`scrapped_product` double
,`scrapped_product_bundle` decimal(32,0)
,`slug` varchar(255)
,`stock_qty_alert` int(11)
,`unique_key` varchar(255)
,`unit_id` int(11)
,`updated_at` timestamp
,`user_id` bigint(20)
,`warehouse_id` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `spi_products`
--

CREATE TABLE `spi_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `spi_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `from_warehouse` int(11) DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_product_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_id` bigint(20) UNSIGNED NOT NULL,
  `bundle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_performed_by` bigint(20) UNSIGNED NOT NULL,
  `any_warning_cls` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spi_product_loan_from_projects`
--

CREATE TABLE `spi_product_loan_from_projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `spi_id` bigint(20) UNSIGNED NOT NULL,
  `spi_product_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_product_id` bigint(20) UNSIGNED NOT NULL,
  `original_project` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_project_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landed_project` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landed_project_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('processing','done') COLLATE utf8mb4_unicode_ci DEFAULT 'processing',
  `generate_ppi_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spi_transfers`
--

CREATE TABLE `spi_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `spi_id` bigint(20) UNSIGNED NOT NULL,
  `from_warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `stock_in_hand`
-- (See below for the actual view)
--
CREATE TABLE `stock_in_hand` (
`barcode_format` enum('Tag','Without-Tag')
,`barcode_prefix` varchar(255)
,`category_id` int(11)
,`code` varchar(255)
,`id` bigint(20) unsigned
,`name` varchar(255)
,`stock_in` double
,`stock_in_hand` double
,`stock_out` double
,`stock_qty_alert` int(11)
,`unit_id` int(11)
,`waiting_stockin` double
,`waiting_stockout` double
,`warehouse_based_data` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `stock_in_hand_new`
-- (See below for the actual view)
--
CREATE TABLE `stock_in_hand_new` (
`barcode_format` enum('Tag','Without-Tag')
,`barcode_prefix` varchar(255)
,`category_id` int(11)
,`code` varchar(255)
,`id` bigint(20) unsigned
,`name` varchar(255)
,`stock_in` double
,`stock_in_hand` double
,`stock_out` double
,`stock_qty_alert` int(11)
,`unit_id` int(11)
,`waiting_stock_in` double
,`waiting_stock_out` double
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `stock_in_hand_old`
-- (See below for the actual view)
--
CREATE TABLE `stock_in_hand_old` (
`barcode_prefix` varchar(255)
,`category_id` int(11)
,`code` varchar(255)
,`id` bigint(20) unsigned
,`name` varchar(255)
,`stock_in` double
,`stock_in_hand` double
,`stock_out` double
,`stock_qty_alert` int(11)
,`unit_id` int(11)
,`warehouse_based_data` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `temporary_stocks`
--

CREATE TABLE `temporary_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_spi_id` bigint(20) UNSIGNED NOT NULL,
  `ppi_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `spi_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `waiting_stock_in` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `waiting_stock_out` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `temporary_stocks`
--

INSERT INTO `temporary_stocks` (`id`, `action_format`, `product_id`, `ppi_spi_id`, `ppi_product_id`, `spi_product_id`, `waiting_stock_in`, `waiting_stock_out`, `warehouse_id`, `created_at`, `updated_at`) VALUES
(2, 'Ppi', 1, 2, 2, NULL, '123', '0', 1, '2025-09-21 14:34:39', '2025-09-21 14:34:39');

-- --------------------------------------------------------

--
-- Stand-in structure for view `tempo_import`
-- (See below for the actual view)
--
CREATE TABLE `tempo_import` (
`action_format` enum('Ppi','Spi')
,`id` bigint(20) unsigned
,`ppi_product_id` bigint(20) unsigned
,`ppi_spi_product_id` bigint(20) unsigned
,`product_id` bigint(20) unsigned
,`qty` varchar(100)
,`stock_action` enum('In','Out')
);

-- --------------------------------------------------------

--
-- Table structure for table `translates`
--

CREATE TABLE `translates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `translate_for` enum('Role','User') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_id` int(11) DEFAULT NULL,
  `lang` enum('English','Bangla') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `employee_status` enum('Enroll','Terminated','Long Leave','Left Job','On Hold') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `employee_no`, `username`, `phone`, `gender`, `marital_status`, `father`, `mother`, `emergency_phone`, `company`, `department`, `address`, `postcode`, `district`, `email_verified_at`, `birthday`, `join_date`, `employee_status`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Md. Khalakuzzaman Khan', 'info@tritiyo.com', NULL, NULL, '01680139540', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'Block L, Road No. 8, South Banasree', '1703', 'Dhaka', NULL, NULL, NULL, 'Enroll', '$2y$10$.qnxQEbp.n7VSaKWMP0sMe34737/Phlq/eO3iawnJqywftukdxmuq', 'DPJxCAa46aJfSdccNj8c2gZR1UYmvygTsskEpA0uyk5EW9pwX2uQtjFIsXzM', '2021-07-14 11:54:50', '2025-11-27 04:16:37'),
(18, 'Anowarul Haque', 'anowar@mtsbd.net', NULL, NULL, '0', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$9Qedu7fa3k2wKCsAJaKZ9O8TrFyUPaCup2AX87MuUrD1pwmDD.WqG', 'tK710x5XBjfeRMemYGaKfqrvNbSE0ZCqYTO8j01fijo7jbb8NHdPhWGndDyo', '2021-07-30 15:03:53', '2025-09-21 16:43:45'),
(23, 'Zabidur Rahman', 'zabid@mtsbd.net', NULL, NULL, '0171', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'n/a', 'n/a', 'Dhaka', NULL, NULL, NULL, NULL, '$2y$10$3pUhxHcozQJ93N8fAnhME.0azpnkPSZmbcxsIthogJtgwnNntQouC', '3bCfvW48MaP6OG2pbM3mv65IxaXFXOi4fq93rJffEe537ePyFMONjs5Im6Fk', '2021-09-24 23:09:51', '2025-09-21 16:43:48'),
(24, 'Zakir Hossain', 'zakir@mtsbd.net', 'u001', NULL, '019', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'Dhaka', '1215', 'Dhaka', NULL, NULL, NULL, NULL, '$2y$10$3NchffRjcZxaJ7D4E1EsUOhHPqT8UwPMPuKaw2UNqTOCtM/Dv9cT2', 'We4tfI0zZT0Gx6wip0lLLYO9gNj7brXgBm64VD44o3klBGrkkwK97gUHZeQp', '2021-09-28 07:09:52', '2025-09-21 16:43:51'),
(26, 'Nazmul Hoque', 'nazmul@mtsbd.net', NULL, NULL, '01844217301', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$PU28pm/JsxP9uIKy.gDO5uD.FCJORyFiU7.8xAoDzAa703MjWsemW', NULL, '2022-11-14 14:16:14', '2025-09-21 16:43:54'),
(33, 'Shahin Reza', 'shahin.reza@mtsbd.net', '39082301', NULL, '01844217300', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, '447 Janata Bank Road, Kodalia, New Bus Terminal', '1900', 'Tangail', NULL, NULL, NULL, NULL, '$2y$10$DfC2s/So5nLFxjh3NVqXfuMOubhAa4RaQ5tFOnz7nwvKAdBzmNFZa', NULL, '2025-06-22 01:19:39', '2025-09-21 16:43:58'),
(34, 'Koponur  Islam', 'koponur@mtsbd.net', NULL, NULL, '01654464566', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, '447 Janata Bank Road, Kodalia, New Bus Terminal', '1900', 'Tangail', NULL, NULL, NULL, NULL, '$2y$10$sdXTG6cMT/v2XqUw7b3b2.Rhvk1jfiaKYNmSKaNL1kYsWSSTnUb9a', NULL, '2025-09-10 08:45:58', '2025-09-21 16:44:01');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` enum('No','Yes') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `location`, `code`, `phone`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dhokkhin Khan Warehouse', 'Dhokkhin Khan', 'dhokkhin_khan_warehouse_5c54', '01821660066', 'takeitkhan@gmail.com', 'Yes', '2025-09-06 08:52:19', '2025-09-21 16:34:12');

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `global_settings`
--
ALTER TABLE `global_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_bundle_products`
--
ALTER TABLE `ppi_bundle_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_products`
--
ALTER TABLE `ppi_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_set_products`
--
ALTER TABLE `ppi_set_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_spis`
--
ALTER TABLE `ppi_spis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_spi_disputes`
--
ALTER TABLE `ppi_spi_disputes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_spi_histories`
--
ALTER TABLE `ppi_spi_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_spi_notifications`
--
ALTER TABLE `ppi_spi_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_spi_sources`
--
ALTER TABLE `ppi_spi_sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppi_spi_statuses`
--
ALTER TABLE `ppi_spi_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_stocks`
--
ALTER TABLE `product_stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_vendors`
--
ALTER TABLE `purchase_vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `route_groups`
--
ALTER TABLE `route_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `route_lists`
--
ALTER TABLE `route_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `route_list_roles`
--
ALTER TABLE `route_list_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spi_products`
--
ALTER TABLE `spi_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spi_product_loan_from_projects`
--
ALTER TABLE `spi_product_loan_from_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spi_transfers`
--
ALTER TABLE `spi_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temporary_stocks`
--
ALTER TABLE `temporary_stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translates`
--
ALTER TABLE `translates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `global_settings`
--
ALTER TABLE `global_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ppi_bundle_products`
--
ALTER TABLE `ppi_bundle_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ppi_products`
--
ALTER TABLE `ppi_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ppi_set_products`
--
ALTER TABLE `ppi_set_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ppi_spis`
--
ALTER TABLE `ppi_spis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ppi_spi_disputes`
--
ALTER TABLE `ppi_spi_disputes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ppi_spi_histories`
--
ALTER TABLE `ppi_spi_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ppi_spi_notifications`
--
ALTER TABLE `ppi_spi_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ppi_spi_sources`
--
ALTER TABLE `ppi_spi_sources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ppi_spi_statuses`
--
ALTER TABLE `ppi_spi_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_stocks`
--
ALTER TABLE `product_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `purchase_vendors`
--
ALTER TABLE `purchase_vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_users`
--
ALTER TABLE `role_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `route_groups`
--
ALTER TABLE `route_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `route_lists`
--
ALTER TABLE `route_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `route_list_roles`
--
ALTER TABLE `route_list_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `spi_products`
--
ALTER TABLE `spi_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spi_product_loan_from_projects`
--
ALTER TABLE `spi_product_loan_from_projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spi_transfers`
--
ALTER TABLE `spi_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temporary_stocks`
--
ALTER TABLE `temporary_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `translates`
--
ALTER TABLE `translates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;