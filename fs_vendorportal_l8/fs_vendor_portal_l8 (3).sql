-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 09, 2025 at 12:19 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fs_vendor_portal_l8`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`sapvendor`@`localhost` PROCEDURE `USP_InsertingData` (IN `var_Method_Name` VARCHAR(50))  BEGIN
    IF var_Method_Name = 'VendorMasterData' THEN

        truncate table v001_vendor;
	insert into v001_vendor(LIFNR,LAND1,NAME1,NAME2,NAME3,NAME4,TELF1,SORT1,SORT2,
        HOUSE_NUM1,STREET,STR_SUPPL1,STR_SUPPL2,BUILDING,	
		FLOOR,ROOMNUMBER,REGION,CITY1,CITY2,LOCCO,PFACH,PSTL2,EMAIL,TEXT1,TEXT2,TEXT3,TEXT4,TEXT5) 
		(select LIFNR,LAND1,NAME1,NAME2,NAME3,NAME4,TELF1,SORT1,SORT2,
        HOUSE_NUM1,STREET,STR_SUPPL1,STR_SUPPL2,BUILDING,	
		FLOOR,ROOMNUMBER,REGION,CITY1,CITY2,LOCCO,PFACH,PSTL2,EMAIL,TEXT1,TEXT2,TEXT3,TEXT4,TEXT5
	from temp_v001_vendor);
    
    truncate table temp_v001_vendor;

    ELSEIF var_Method_Name = 'POMasterData' THEN

        INSERT INTO v002_POMasterData (
            EBELN, EBELP, LIFNR, ZTERM, TEXT1, LOEKZ, BEDAT, BUKRS, BUTXT, EKORG, EKOTX, EKGRP, EKNAM, WERKS, 
            PLANT_NAME1, LGORT, LGOBE, AEDAT, VERKF, TELF1, FRGKE, TXZ01, MATNR, NETPR, PEINH, NETWR, BRTWR, MENGE,
            MEINS, WAERS, ADD_TEXT1, ADD_TEXT2, ADD_TEXT3, ADD_TEXT4, ADD_TEXT5, CREATED_ON, CREATED_AT, CREATED_BY
        )
        SELECT
            EBELN, EBELP, LIFNR, ZTERM, TEXT1, LOEKZ, BEDAT, BUKRS, BUTXT, EKORG, EKOTX, EKGRP, EKNAM, WERKS, 
            PLANT_NAME1, LGORT, LGOBE, AEDAT, VERKF, TELF1, FRGKE, TXZ01, MATNR, NETPR, PEINH, NETWR, BRTWR, MENGE,
            MEINS, WAERS, ADD_TEXT1, ADD_TEXT2, ADD_TEXT3, ADD_TEXT4, ADD_TEXT5, CREATED_ON, CREATED_AT, CREATED_BY
        FROM temp_v002_POMasterData
        ON DUPLICATE KEY UPDATE
            LIFNR = VALUES(LIFNR),
            ZTERM = VALUES(ZTERM),
            TEXT1 = VALUES(TEXT1),
            LOEKZ = VALUES(LOEKZ),
            BEDAT = VALUES(BEDAT),
            BUKRS = VALUES(BUKRS),
            BUTXT = VALUES(BUTXT),
            EKORG = VALUES(EKORG),
            EKOTX = VALUES(EKOTX),
            EKGRP = VALUES(EKGRP),
            EKNAM = VALUES(EKNAM),
            WERKS = VALUES(WERKS),
            PLANT_NAME1 = VALUES(PLANT_NAME1),
            LGORT = VALUES(LGORT),
            LGOBE = VALUES(LGOBE),
            AEDAT = VALUES(AEDAT),
            VERKF = VALUES(VERKF),
            TELF1 = VALUES(TELF1),
            FRGKE = VALUES(FRGKE),
            TXZ01 = VALUES(TXZ01),
            MATNR = VALUES(MATNR),
            NETPR = VALUES(NETPR),
            PEINH = VALUES(PEINH),
            NETWR = VALUES(NETWR),
            BRTWR = VALUES(BRTWR),
            MENGE = VALUES(MENGE),
            MEINS = VALUES(MEINS),
            WAERS = VALUES(WAERS),
            ADD_TEXT1 = VALUES(ADD_TEXT1),
            ADD_TEXT2 = VALUES(ADD_TEXT2),
            ADD_TEXT3 = VALUES(ADD_TEXT3),
            ADD_TEXT4 = VALUES(ADD_TEXT4),
            ADD_TEXT5 = VALUES(ADD_TEXT5),
            CREATED_ON = VALUES(CREATED_ON),
            CREATED_AT = VALUES(CREATED_AT),
            CREATED_BY = VALUES(CREATED_BY);

        TRUNCATE TABLE temp_v002_POMasterData;

    ELSEIF var_Method_Name = 'PODeliveryData' THEN

        INSERT INTO v003_PODelivery (
            EBELN, EBELP, ETENR, EINDT, SLFDT, MENGE, AMENG,
            WEMNG, WAMNG, UZEIT, BEDAT, CHARG, MEINS, ADD_TEXT1, ADD_TEXT2, ADD_TEXT3, ADD_TEXT4, ADD_TEXT5
        )
        SELECT
            EBELN, EBELP, ETENR, EINDT, SLFDT, MENGE, AMENG,
            WEMNG, WAMNG, UZEIT, BEDAT, CHARG, MEINS, ADD_TEXT1, ADD_TEXT2, ADD_TEXT3, ADD_TEXT4, ADD_TEXT5
        FROM temp_v003_PODelivery
        ON DUPLICATE KEY UPDATE
            EINDT = VALUES(EINDT),
            SLFDT = VALUES(SLFDT),
            MENGE = VALUES(MENGE),
            AMENG = VALUES(AMENG),
            WEMNG = VALUES(WEMNG),
            WAMNG = VALUES(WAMNG),
            UZEIT = VALUES(UZEIT),
            BEDAT = VALUES(BEDAT),
            CHARG = VALUES(CHARG),
            MEINS = VALUES(MEINS),
            ADD_TEXT1 = VALUES(ADD_TEXT1),
            ADD_TEXT2 = VALUES(ADD_TEXT2),
            ADD_TEXT3 = VALUES(ADD_TEXT3),
            ADD_TEXT4 = VALUES(ADD_TEXT4),
            ADD_TEXT5 = VALUES(ADD_TEXT5);

        TRUNCATE TABLE temp_v003_PODelivery;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `USP_Job_Logs` (IN `var_jobid` VARCHAR(50), IN `var_execute_at` VARCHAR(50), IN `var_type` VARCHAR(45), IN `var_execution_time` VARCHAR(30), IN `var_status` VARCHAR(50))  BEGIN

	Declare New_Log_Id varchar(20);
	Declare Year_Id varchar(10);
	set Year_Id = (select right(left(curdate(),4),(2)));
	Call USP_Number_Range ('v001_jobs', Year_Id, 'V001', '', New_Log_Id );
    
		insert into v001_jobs(log_id, job_id, execute_at, type, execution_time, status ) 
		values (New_Log_Id, var_jobid , var_execute_at, var_type, var_execution_time, var_status );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `USP_Number_Range` (IN `var_Table_Name` VARCHAR(50), IN `var_Year_Id` VARCHAR(50), IN `var_Number_Prefix` VARCHAR(20), IN `var_Number_Suffix` VARCHAR(20), OUT `New_Id` VARCHAR(30))  BEGIN
	Declare RecNo varchar(20);                                
	Declare LastNum bigint;  
        
	Set RecNo = null;
	Set RecNo = (Select Last_Number_Used as Last_Number from s001_number_range where
	Table_Name = var_Table_Name 
	and Year_Id = var_Year_Id 
	and Number_Prefix = var_Number_Prefix 
	and Number_Suffix = var_Number_Suffix);            
	   
	if (isnull(RecNo) = 1) then
			insert into s001_number_range(Table_Name, Year_Id,
			Number_Prefix, Number_Suffix, Start_Number, End_Number, Last_Number_Used)                              
			values (var_Table_Name, var_Year_Id, 
			var_Number_Prefix, var_Number_Suffix, 1, 9999999, 1000001); 
            
			set LastNum = 1000001;                                              
	else                            
			set LastNum = RecNo + 1;                              
            
			update s001_number_range set Last_Number_Used = LastNum where 
			Table_Name = var_Table_Name
			and Year_Id = var_Year_Id 
			and Number_Prefix = var_Number_Prefix 
			and Number_Suffix = var_Number_Suffix;                              
	end if;
    
	SET New_Id = (select concat(var_Number_Prefix , var_Year_Id , cast(LastNum as char) , var_Number_Suffix)); 
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_company_code`
--

CREATE TABLE `admin_company_code` (
  `id` int(20) NOT NULL,
  `admin_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_company_code`
--

INSERT INTO `admin_company_code` (`id`, `admin_email`, `company_code`) VALUES
(1, 'admin@example.com', 1946),
(2, 'admin@example.com', 1947),
(3, 'admin2@example.com', 1946);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_number` bigint(20) NOT NULL,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_value` decimal(18,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grn_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grn_num` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grn_date` date DEFAULT NULL,
  `reconciliation_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authorization_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_block` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_office_account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirmed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `order_id`, `delivery_date`, `delivery_number`, `company`, `department`, `order_value`, `currency`, `status`, `grn_pdf`, `grn_num`, `grn_date`, `reconciliation_account`, `authorization_group`, `payment_block`, `head_office_account_number`, `created_at`, `updated_at`, `notes`, `confirmed_by`, `confirmed_at`) VALUES
(27, 1, '2025-07-09', 100002, 'Admin', 'Procurement', '4800.00', 'KWD', 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 03:31:30', '2025-07-09 03:31:30', NULL, 7, '2025-07-09 03:31:30');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_items`
--

CREATE TABLE `delivery_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED NOT NULL,
  `line_item_num` int(11) NOT NULL,
  `item_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `uom` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expected_delv_date` date NOT NULL,
  `quantity_supplied` decimal(15,3) NOT NULL DEFAULT 0.000,
  `supply_date` date DEFAULT NULL,
  `qty_received_by_amg` decimal(15,3) NOT NULL DEFAULT 0.000,
  `amg_received_date` date DEFAULT NULL,
  `batch_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(18,4) DEFAULT NULL,
  `total_value` decimal(20,2) DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `grn_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `storage_location` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plant` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_items`
--

INSERT INTO `delivery_items` (`id`, `delivery_id`, `purchase_order_item_id`, `line_item_num`, `item_description`, `quantity`, `uom`, `expected_delv_date`, `quantity_supplied`, `supply_date`, `qty_received_by_amg`, `amg_received_date`, `batch_number`, `serial_number`, `unit_price`, `total_value`, `status`, `grn_pdf`, `remarks`, `storage_location`, `plant`, `created_at`, `updated_at`) VALUES
(57, 27, 1, 1, 'CAMERA', '50.000', 'PCS', '2025-07-23', '5.000', '2025-07-09', '0.000', NULL, NULL, NULL, '100.0000', '0.00', 'PARTIAL', NULL, NULL, NULL, NULL, '2025-07-09 03:31:30', '2025-07-09 03:31:39'),
(58, 27, 2, 2, 'LAPTOP', '10.000', 'PCS', '2025-07-23', '0.000', '2025-07-09', '0.000', NULL, NULL, NULL, '1000.0000', '0.00', 'PARTIAL', NULL, NULL, NULL, NULL, '2025-07-09 03:31:30', '2025-07-09 03:31:39');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_due` decimal(12,2) NOT NULL,
  `status` enum('draft','submitted','approved','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invoice_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `miro_document` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(25, '0001_01_01_000000_create_users_table', 1),
(26, '0001_01_01_000001_create_cache_table', 1),
(27, '0001_01_01_000002_create_jobs_table', 1),
(28, '0002_06_24_165108_create_permission_tables', 1),
(29, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(30, '2025_06_16_075116_create_vendors_table', 1),
(31, '2025_06_16_075117_create_vendor_bank_table', 1),
(32, '2025_06_16_075118_create_vendor_contacts_table', 1),
(33, '2025_06_16_075119_create_vendor_address_table', 1),
(34, '2025_06_16_075120_create_vendor_business_details_table', 1),
(35, '2025_06_16_075121_create_vendor_audit_log_table', 1),
(36, '2025_06_16_075122_create_vendor_company_codes_table', 1),
(37, '2025_06_16_075123_create_vendor_purchasing_org_table', 1),
(38, '2025_06_16_075124_create_purchase_orders_table', 1),
(39, '2025_06_16_075125_create_purchase_order_items_table', 1),
(40, '2025_06_16_075126_create_deliveries_table', 1),
(41, '2025_06_16_075127_create_delivery_items_table', 1),
(42, '2025_06_19_075121_create_vendor_invoice_table', 1),
(43, '2025_06_19_090852_add_po_pdf_to_purchase_orders_table', 1),
(44, '2025_06_19_090853_add_invoice_pdf_to_invoices_table', 1),
(45, '2025_06_19_090856_create_vendor_payments_table', 1),
(46, '2025_06_21_082743_add_grn_pdf_to_deliveries_table', 1),
(47, '2025_06_23_065806_add_fields_to_deliveries_table', 1),
(48, '2025_06_24_065806_add_grn_pdf_to_deliveries_items_table', 1),
(50, '2025_07_09_074152_add_is_superadmin_to_users_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 7);

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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api-token', 'e856dd279b1f63cd419c1efa9da065ae021927db19c0552246471288c258d920', '[\"*\"]', NULL, '2025-06-26 13:01:52', '2025-06-26 13:01:52'),
(2, 'App\\Models\\User', 1, 'api-token', '494f94227b566e2d5a9d7a0ca5f9287dd0c4ff59453d5c2bb99fada3a19b6fb0', '[\"*\"]', '2025-06-26 13:09:50', '2025-06-26 13:07:48', '2025-06-26 13:09:50'),
(3, 'App\\Models\\User', 1, 'api-token', '7ec4f2d3829c6daa964b5fc38ffb55e499d72fd5fbd888d659bd2cb8bda0261e', '[\"*\"]', NULL, '2025-06-26 13:09:36', '2025-06-26 13:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `amg_company_code` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date NOT NULL,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_value` decimal(18,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_term` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acknowledgement_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `po_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `order_number`, `vendor_id`, `amg_company_code`, `order_date`, `delivery_date`, `company`, `department`, `order_value`, `currency`, `payment_term`, `status`, `acknowledgement_date`, `created_at`, `updated_at`, `po_pdf`) VALUES
(1, 'PO-001', 8, 1946, '2025-07-06', '2025-07-29', 'Admin', 'Procurement', '4800.00', 'KWD', 'NET30', 'partial delivery', NULL, '2025-07-08 12:39:58', '2025-07-09 09:01:30', 'purchase_orders/PO-001-1752051635.pdf'),
(2, 'PO-002', 8, 2000, '2025-07-06', '2025-07-29', 'IT', 'Protection', '8971.00', 'KWD', 'YBC45', 'not verified', NULL, '2025-07-08 12:39:58', '2025-07-09 10:18:03', 'purchase_orders/PO-002.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `line_item_no` int(11) NOT NULL,
  `item_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `uom` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(18,4) NOT NULL,
  `value` decimal(20,2) NOT NULL,
  `plant` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slocc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPEN',
  `delivery_date` date DEFAULT NULL,
  `material_group` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivered_quantity` decimal(15,3) NOT NULL DEFAULT 0.000,
  `invoiced_quantity` decimal(15,3) NOT NULL DEFAULT 0.000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `order_id`, `product_code`, `line_item_no`, `item_description`, `quantity`, `uom`, `price`, `value`, `plant`, `slocc`, `status`, `delivery_date`, `material_group`, `delivered_quantity`, `invoiced_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, '101', 1, 'CAMERA', '50.000', 'PCS', '100.0000', '10.00', 'plant', 'slocc', 'not verified', '2025-07-23', NULL, '0.000', '0.000', NULL, NULL),
(2, 1, '102', 2, 'LAPTOP', '10.000', 'PCS', '1000.0000', '15.00', 'plant2', 'slocc2', 'not verified', '2025-07-23', NULL, '0.000', '0.000', NULL, NULL),
(3, 2, '102', 2, 'KEYBOARD', '10.000', 'PCS', '1000.0000', '15.00', 'plant2', 'slocc2', 'not verified', '2025-07-23', NULL, '0.000', '0.000', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-06-26 13:00:11', '2025-06-26 13:00:11'),
(2, 'user', 'web', '2025-06-26 13:00:11', '2025-06-26 13:00:11'),
(3, 'vendor', 'web', '2025-06-26 13:00:11', '2025-06-26 13:00:11');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kPYkj5SfWUUOo3EE56cznYdUBu0T2tJ0OmKCwIo8', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY0lHMGU2bm9rbk9nOTRkYTJhQnhKSXNhZFV5cmVlZ1lQZU1uckdYbSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wdXJjaGFzZS1vcmRlcnMvYWxsLXBhcnRpYWwtZGVsaXZlcnkiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O30=', 1752056308);

-- --------------------------------------------------------

--
-- Table structure for table `temp_v001_vendor`
--

CREATE TABLE `temp_v001_vendor` (
  `LFA1` varchar(30) NOT NULL,
  `Land1` varchar(30) NOT NULL,
  `NAME1` varchar(200) DEFAULT NULL,
  `NAME2` varchar(200) DEFAULT NULL,
  `NAME3` varchar(200) DEFAULT NULL,
  `NAME4` varchar(200) DEFAULT NULL,
  `TELF1` varchar(70) DEFAULT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `SORT1` varchar(100) DEFAULT NULL,
  `SORT2` varchar(100) DEFAULT NULL,
  `HOUSE_NUM1` varchar(100) DEFAULT NULL,
  `STREET` varchar(100) DEFAULT NULL,
  `STR_SUPPL1` varchar(100) DEFAULT NULL,
  `STR_SUPPL2` varchar(100) DEFAULT NULL,
  `BUILDING` varchar(50) DEFAULT NULL,
  `FLOOR` varchar(30) DEFAULT NULL,
  `ROOMNUMBER` varchar(30) DEFAULT NULL,
  `REGION` varchar(100) DEFAULT NULL,
  `CITY1` varchar(100) DEFAULT NULL,
  `CITY2` varchar(100) DEFAULT NULL,
  `LOCCO` varchar(70) DEFAULT NULL,
  `PFACH` varchar(70) DEFAULT NULL,
  `PSTL2` varchar(70) DEFAULT NULL,
  `TEXT1` varchar(200) DEFAULT NULL,
  `TEXT2` varchar(200) DEFAULT NULL,
  `TEXT3` varchar(200) DEFAULT NULL,
  `TEXT4` varchar(200) DEFAULT NULL,
  `TEXT5` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temp_v002_pomaster`
--

CREATE TABLE `temp_v002_pomaster` (
  `EBELN` varchar(30) NOT NULL,
  `EBELP` varchar(30) NOT NULL,
  `LIFNR` varchar(70) DEFAULT NULL,
  `ZTERM` varchar(70) DEFAULT NULL,
  `TEXT1` varchar(70) DEFAULT NULL,
  `LOEKZ` varchar(70) DEFAULT NULL,
  `BEDAT` varchar(70) DEFAULT NULL,
  `BUKRS` varchar(70) DEFAULT NULL,
  `BUTXT` varchar(70) DEFAULT NULL,
  `EKORG` varchar(70) DEFAULT NULL,
  `EKOTX` varchar(100) DEFAULT NULL,
  `EKGRP` varchar(100) DEFAULT NULL,
  `EKNAM` varchar(100) DEFAULT NULL,
  `WERKS` varchar(100) DEFAULT NULL,
  `PLANT_NAME1` varchar(100) DEFAULT NULL,
  `LGORT` varchar(100) DEFAULT NULL,
  `LGOBE` varchar(100) DEFAULT NULL,
  `AEDAT` varchar(100) DEFAULT NULL,
  `VERKF` varchar(100) DEFAULT NULL,
  `TELF1` varchar(40) DEFAULT NULL,
  `FRGKE` varchar(40) DEFAULT NULL,
  `TXZ01` varchar(40) DEFAULT NULL,
  `MATNR` varchar(255) DEFAULT NULL,
  `NETPR` varchar(150) DEFAULT NULL,
  `PEINH` varchar(150) DEFAULT NULL,
  `NETWR` varchar(150) DEFAULT NULL,
  `BRTWR` varchar(150) DEFAULT NULL,
  `MENGE` varchar(150) DEFAULT NULL,
  `MEINS` varchar(150) DEFAULT NULL,
  `WAERS` varchar(150) DEFAULT NULL,
  `ADD_TEXT1` varchar(150) DEFAULT NULL,
  `ADD_TEXT2` varchar(150) DEFAULT NULL,
  `ADD_TEXT3` varchar(150) DEFAULT NULL,
  `ADD_TEXT4` varchar(150) DEFAULT NULL,
  `ADD_TEXT5` varchar(150) DEFAULT NULL,
  `CREATED_ON` varchar(150) DEFAULT NULL,
  `CREATED_AT` varchar(150) DEFAULT NULL,
  `CREATED_BY` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `temp_v003_podelivery`
--

CREATE TABLE `temp_v003_podelivery` (
  `EBELN` varchar(30) NOT NULL,
  `EBELP` varchar(30) NOT NULL,
  `ETENR` varchar(70) NOT NULL,
  `EINDT` varchar(70) DEFAULT NULL,
  `SLFDT` varchar(70) DEFAULT NULL,
  `MENGE` varchar(70) DEFAULT NULL,
  `AMENG` varchar(70) DEFAULT NULL,
  `WEMNG` varchar(70) DEFAULT NULL,
  `WAMNG` varchar(70) DEFAULT NULL,
  `UZEIT` varchar(70) DEFAULT NULL,
  `BEDAT` varchar(100) DEFAULT NULL,
  `CHARG` varchar(100) DEFAULT NULL,
  `MEINS` varchar(100) DEFAULT NULL,
  `ADD_TEXT1` varchar(100) DEFAULT NULL,
  `ADD_TEXT2` varchar(100) DEFAULT NULL,
  `ADD_TEXT3` varchar(100) DEFAULT NULL,
  `ADD_TEXT4` varchar(100) DEFAULT NULL,
  `ADD_TEXT5` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_superadmin`) VALUES
(1, 'Admin User', 'admin@example.com', NULL, '$2y$10$vGwZsDDtbFFuonhoE66Iv.cMNoYL5GOvHzqxz07nBYmwOv1eZNfNy', NULL, '2025-06-26 13:00:11', '2025-06-26 13:00:11', 1),
(3, 'Ajax', 'vendor@example.com', NULL, '$2y$10$LYZP6z.BNwWlePzJ.mbKiuFC.xXoI1ZLsuckQf6HNOl90hgv92rJS', NULL, '2025-06-27 13:13:05', '2025-06-27 13:13:05', 0),
(7, '2nd vendor', 'vendor2@example.com', NULL, '$2y$10$Cgh8UyUip2sVJ1QI/edEQuDZHO7.FduPtweF6CRD3Cld0DR0TqmZq', NULL, '2025-07-08 04:54:38', '2025-07-08 04:54:38', 0),
(8, 'Admin Specific User', 'admin2@example.com', NULL, '$2y$10$7NsiZeiXw7AOITWXyPyoheMwLrXB1eEYt7MJdfZ86iz3CO2rAK6ZO', NULL, '2025-07-09 02:04:21', '2025-07-09 02:04:21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `v001_jobs`
--

CREATE TABLE `v001_jobs` (
  `log_id` varchar(20) NOT NULL,
  `job_id` varchar(100) DEFAULT NULL,
  `execute_at` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `execution_time` varchar(45) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `v001_vendor`
--

CREATE TABLE `v001_vendor` (
  `LFA1` varchar(30) NOT NULL,
  `Land1` varchar(30) NOT NULL,
  `NAME1` varchar(200) DEFAULT NULL,
  `NAME2` varchar(200) DEFAULT NULL,
  `NAME3` varchar(200) DEFAULT NULL,
  `NAME4` varchar(200) DEFAULT NULL,
  `TELF1` varchar(70) DEFAULT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `SORT1` varchar(100) DEFAULT NULL,
  `SORT2` varchar(100) DEFAULT NULL,
  `HOUSE_NUM1` varchar(100) DEFAULT NULL,
  `STREET` varchar(100) DEFAULT NULL,
  `STR_SUPPL1` varchar(100) DEFAULT NULL,
  `STR_SUPPL2` varchar(100) DEFAULT NULL,
  `BUILDING` varchar(50) DEFAULT NULL,
  `FLOOR` varchar(30) DEFAULT NULL,
  `ROOMNUMBER` varchar(30) DEFAULT NULL,
  `REGION` varchar(100) DEFAULT NULL,
  `CITY1` varchar(100) DEFAULT NULL,
  `CITY2` varchar(100) DEFAULT NULL,
  `LOCCO` varchar(70) DEFAULT NULL,
  `PFACH` varchar(70) DEFAULT NULL,
  `PSTL2` varchar(70) DEFAULT NULL,
  `TEXT1` varchar(200) DEFAULT NULL,
  `TEXT2` varchar(200) DEFAULT NULL,
  `TEXT3` varchar(200) DEFAULT NULL,
  `TEXT4` varchar(200) DEFAULT NULL,
  `TEXT5` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `v002_pomaster`
--

CREATE TABLE `v002_pomaster` (
  `EBELN` varchar(30) NOT NULL,
  `EBELP` varchar(30) NOT NULL,
  `LIFNR` varchar(70) DEFAULT NULL,
  `ZTERM` varchar(70) DEFAULT NULL,
  `TEXT1` varchar(70) DEFAULT NULL,
  `LOEKZ` varchar(70) DEFAULT NULL,
  `BEDAT` varchar(70) DEFAULT NULL,
  `BUKRS` varchar(70) DEFAULT NULL,
  `BUTXT` varchar(70) DEFAULT NULL,
  `EKORG` varchar(70) DEFAULT NULL,
  `EKOTX` varchar(100) DEFAULT NULL,
  `EKGRP` varchar(100) DEFAULT NULL,
  `EKNAM` varchar(100) DEFAULT NULL,
  `WERKS` varchar(100) DEFAULT NULL,
  `PLANT_NAME1` varchar(100) DEFAULT NULL,
  `LGORT` varchar(100) DEFAULT NULL,
  `LGOBE` varchar(100) DEFAULT NULL,
  `AEDAT` varchar(100) DEFAULT NULL,
  `VERKF` varchar(100) DEFAULT NULL,
  `TELF1` varchar(40) DEFAULT NULL,
  `FRGKE` varchar(40) DEFAULT NULL,
  `TXZ01` varchar(40) DEFAULT NULL,
  `MATNR` varchar(255) DEFAULT NULL,
  `NETPR` varchar(150) DEFAULT NULL,
  `PEINH` varchar(150) DEFAULT NULL,
  `NETWR` varchar(150) DEFAULT NULL,
  `BRTWR` varchar(150) DEFAULT NULL,
  `MENGE` varchar(150) DEFAULT NULL,
  `MEINS` varchar(150) DEFAULT NULL,
  `WAERS` varchar(150) DEFAULT NULL,
  `ADD_TEXT1` varchar(150) DEFAULT NULL,
  `ADD_TEXT2` varchar(150) DEFAULT NULL,
  `ADD_TEXT3` varchar(150) DEFAULT NULL,
  `ADD_TEXT4` varchar(150) DEFAULT NULL,
  `ADD_TEXT5` varchar(150) DEFAULT NULL,
  `CREATED_ON` varchar(150) DEFAULT NULL,
  `CREATED_AT` varchar(150) DEFAULT NULL,
  `CREATED_BY` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `v003_podelivery`
--

CREATE TABLE `v003_podelivery` (
  `EBELN` varchar(30) NOT NULL,
  `EBELP` varchar(30) NOT NULL,
  `ETENR` varchar(70) NOT NULL,
  `EINDT` varchar(70) DEFAULT NULL,
  `SLFDT` varchar(70) DEFAULT NULL,
  `MENGE` varchar(70) DEFAULT NULL,
  `AMENG` varchar(70) DEFAULT NULL,
  `WEMNG` varchar(70) DEFAULT NULL,
  `WAMNG` varchar(70) DEFAULT NULL,
  `UZEIT` varchar(70) DEFAULT NULL,
  `BEDAT` varchar(100) DEFAULT NULL,
  `CHARG` varchar(100) DEFAULT NULL,
  `MEINS` varchar(100) DEFAULT NULL,
  `ADD_TEXT1` varchar(100) DEFAULT NULL,
  `ADD_TEXT2` varchar(100) DEFAULT NULL,
  `ADD_TEXT3` varchar(100) DEFAULT NULL,
  `ADD_TEXT4` varchar(100) DEFAULT NULL,
  `ADD_TEXT5` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `v010_jobs`
--

CREATE TABLE `v010_jobs` (
  `job_id` int(11) NOT NULL,
  `job_name` varchar(45) DEFAULT NULL,
  `job_symbol` varchar(45) DEFAULT NULL,
  `execution_cycle` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorization_group` bigint(20) DEFAULT NULL,
  `account_group` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `vendor_code`, `vendor_name`, `authorization_group`, `account_group`, `created_at`, `updated_at`) VALUES
(8, 'VEND-002', 'Test 2 Foreign Supplier', 200, 200, '2025-07-08 04:54:38', '2025-07-08 04:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_address`
--

CREATE TABLE `vendor_address` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `address_type` enum('BILLING','SHIPPING','HEADQUARTERS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_box` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_address`
--

INSERT INTO `vendor_address` (`id`, `vendor_id`, `address_type`, `address_line1`, `address_line2`, `city`, `state_province`, `postal_code`, `po_box`, `country`, `country_code`, `created_at`, `updated_at`) VALUES
(3, 8, 'BILLING', '123 Business Park', 'Suite 456', 'Kuwait City', 'KW', '10001', 'PO Box 789', 'Kuwait', 'KWT', '2025-07-08 04:54:38', '2025-07-08 04:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_audit_log`
--

CREATE TABLE `vendor_audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` enum('CREATE','UPDATE','DELETE','APPROVE','REJECT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `changed_by` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_timestamp` datetime NOT NULL,
  `company_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `change_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `approved_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bank`
--

CREATE TABLE `vendor_bank` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `bank_country` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_key` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_control_key` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_bank_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collection_authorization` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_details` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_bk_details_hd` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_from` date NOT NULL,
  `eff_to` date NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_bank`
--

INSERT INTO `vendor_bank` (`id`, `vendor_id`, `bank_country`, `bank_key`, `bank_account`, `bank_control_key`, `partner_bank_type`, `collection_authorization`, `reference_details`, `account_holder`, `account_description`, `status_bk_details_hd`, `valid_from`, `eff_to`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 8, 'US', 'BK001', '18273', 'BCK99', 'PRIMARY', 'Y', 'Test Bank Reference', 'Test 2 Foreign Supplier', 'Business Account', 'ACTIVE', '2024-07-08', '2026-07-08', 1, '2025-07-08 04:54:38', '2025-07-08 04:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_business_details`
--

CREATE TABLE `vendor_business_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_classification` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_expiry` date NOT NULL,
  `website` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_business_details`
--

INSERT INTO `vendor_business_details` (`id`, `vendor_id`, `supplier_type`, `supplier_status`, `supplier_classification`, `supplier_category`, `payment_terms`, `currency`, `tax_number`, `vat_number`, `registration_number`, `license_number`, `license_expiry`, `website`, `remarks`, `created_at`, `updated_at`) VALUES
(2, 8, 'FOREIGN', 'ACTIVE', 'D', 'Electronics', 'NET45', 'KWD', 'TAX1289', 'VAT98321', 'REG2001', 'LIC20001', '2027-07-08', 'https://testvendor2.com', 'Primary foreign supplier for raw materials', '2025-07-08 04:54:38', '2025-07-08 04:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_company_codes`
--

CREATE TABLE `vendor_company_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `company_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `head_office_account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reconciliation_account` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_block` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_company_codes`
--

INSERT INTO `vendor_company_codes` (`id`, `vendor_id`, `company_code`, `account_number`, `head_office_account_number`, `reconciliation_account`, `payment_term`, `payment_block`, `created_at`, `updated_at`) VALUES
(2, 8, 'CC02', 'ACC1234567890', 'HO1234567890', 'RECON123456', 'NET30', 'NONE', '2025-07-08 04:54:38', '2025-07-08 04:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_contacts`
--

CREATE TABLE `vendor_contacts` (
  `contact_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `contact_type` enum('PRIMARY','BILLING','SHIPPING','TECHNICAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_contacts`
--

INSERT INTO `vendor_contacts` (`contact_id`, `vendor_id`, `contact_type`, `contact_person`, `department`, `phone`, `fax`, `email`, `mobile`, `created_at`, `updated_at`) VALUES
('CONT-002', 8, 'PRIMARY', '2ND PERSON', 'Admin', '8899', '8899', 'vendor2@example.com', '9901', '2025-07-08 04:54:38', '2025-07-08 04:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payments`
--

CREATE TABLE `vendor_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_num` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_document_number` bigint(20) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deductions` bigint(20) DEFAULT NULL,
  `balance_outstanding` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_purchasing_org`
--

CREATE TABLE `vendor_purchasing_org` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `purchasing_org` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_order_value` decimal(18,2) NOT NULL,
  `terms_of_payment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `incoterms` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_purchasing_org`
--

INSERT INTO `vendor_purchasing_org` (`id`, `vendor_id`, `purchasing_org`, `order_currency`, `min_order_value`, `terms_of_payment`, `incoterms`, `created_at`, `updated_at`) VALUES
(2, 8, 'PO02', 'KWD', '500.00', 'Y123', 'FOB', '2025-07-08 04:54:38', '2025-07-08 04:54:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_company_code`
--
ALTER TABLE `admin_company_code`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `admin_email` (`admin_email`) USING BTREE;

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deliveries_delivery_number_unique` (`delivery_number`),
  ADD KEY `deliveries_order_id_foreign` (`order_id`);

--
-- Indexes for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_items_delivery_id_foreign` (`delivery_id`),
  ADD KEY `delivery_items_purchase_order_item_id_foreign` (`purchase_order_item_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `invoices_delivery_id_foreign` (`delivery_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_order_number_unique` (`order_number`),
  ADD KEY `purchase_orders_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_order_id_foreign` (`order_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `temp_v002_pomaster`
--
ALTER TABLE `temp_v002_pomaster`
  ADD PRIMARY KEY (`EBELN`,`EBELP`);

--
-- Indexes for table `temp_v003_podelivery`
--
ALTER TABLE `temp_v003_podelivery`
  ADD PRIMARY KEY (`EBELN`,`EBELP`,`ETENR`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `v002_pomaster`
--
ALTER TABLE `v002_pomaster`
  ADD PRIMARY KEY (`EBELN`,`EBELP`);

--
-- Indexes for table `v003_podelivery`
--
ALTER TABLE `v003_podelivery`
  ADD PRIMARY KEY (`EBELN`,`EBELP`,`ETENR`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_vendor_code_unique` (`vendor_code`);

--
-- Indexes for table `vendor_address`
--
ALTER TABLE `vendor_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_address_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_audit_log`
--
ALTER TABLE `vendor_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_audit_log_vendor_id_change_timestamp_index` (`vendor_id`,`change_timestamp`),
  ADD KEY `vendor_audit_log_table_name_action_type_index` (`table_name`,`action_type`),
  ADD KEY `vendor_audit_log_changed_by_index` (`changed_by`);

--
-- Indexes for table `vendor_bank`
--
ALTER TABLE `vendor_bank`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_bank_bank_account_unique` (`bank_account`),
  ADD KEY `vendor_bank_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_business_details`
--
ALTER TABLE `vendor_business_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_business_details_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_company_codes`
--
ALTER TABLE `vendor_company_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_company_codes_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `vendor_contacts_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_payments_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `vendor_purchasing_org`
--
ALTER TABLE `vendor_purchasing_org`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_purchasing_org_vendor_id_foreign` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_company_code`
--
ALTER TABLE `admin_company_code`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `delivery_items`
--
ALTER TABLE `delivery_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vendor_address`
--
ALTER TABLE `vendor_address`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor_audit_log`
--
ALTER TABLE `vendor_audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_bank`
--
ALTER TABLE `vendor_bank`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor_business_details`
--
ALTER TABLE `vendor_business_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_company_codes`
--
ALTER TABLE `vendor_company_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_purchasing_org`
--
ALTER TABLE `vendor_purchasing_org`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_company_code`
--
ALTER TABLE `admin_company_code`
  ADD CONSTRAINT `admin_company_code_ibfk_1` FOREIGN KEY (`admin_email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD CONSTRAINT `delivery_items_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`),
  ADD CONSTRAINT `invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`);

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
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_address`
--
ALTER TABLE `vendor_address`
  ADD CONSTRAINT `vendor_address_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_audit_log`
--
ALTER TABLE `vendor_audit_log`
  ADD CONSTRAINT `vendor_audit_log_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_bank`
--
ALTER TABLE `vendor_bank`
  ADD CONSTRAINT `vendor_bank_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `vendor_business_details`
--
ALTER TABLE `vendor_business_details`
  ADD CONSTRAINT `vendor_business_details_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_company_codes`
--
ALTER TABLE `vendor_company_codes`
  ADD CONSTRAINT `vendor_company_codes_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  ADD CONSTRAINT `vendor_contacts_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD CONSTRAINT `vendor_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_purchasing_org`
--
ALTER TABLE `vendor_purchasing_org`
  ADD CONSTRAINT `vendor_purchasing_org_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
