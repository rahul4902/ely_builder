-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: elyleads_central
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `elyleads_central`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `elyleads_central` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `elyleads_central`;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `text` varchar(191) NOT NULL,
  `source_type` varchar(191) NOT NULL,
  `source_id` int(11) DEFAULT NULL,
  `ip_address` varchar(64) NOT NULL,
  `action` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_notifications`
--

DROP TABLE IF EXISTS `app_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `content` varchar(500) NOT NULL,
  `n_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_company_id_index` (`company_id`),
  KEY `idx_app_notif_user_read` (`user_id`,`n_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_notifications`
--

LOCK TABLES `app_notifications` WRITE;
/*!40000 ALTER TABLE `app_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_invoice`
--

DROP TABLE IF EXISTS `client_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_invoice`
--

LOCK TABLES `client_invoice` WRITE;
/*!40000 ALTER TABLE `client_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `primary_number` varchar(191) DEFAULT NULL,
  `secondary_number` varchar(191) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `zipcode` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `company_name` varchar(191) NOT NULL,
  `vat` varchar(191) DEFAULT NULL,
  `industry` varchar(191) NOT NULL,
  `company_type` varchar(191) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `industry_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_email_unique` (`email`),
  KEY `clients_user_id_foreign` (`user_id`),
  KEY `clients_industry_id_foreign` (`industry_id`),
  KEY `clients_company_id_index` (`company_id`),
  CONSTRAINT `clients_industry_id_foreign` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`),
  CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_task_id_foreign` (`task_id`),
  CONSTRAINT `comments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `plan_id` bigint(20) unsigned DEFAULT NULL,
  `plan_expires_at` datetime DEFAULT NULL,
  `subscription_status` varchar(32) NOT NULL DEFAULT 'active',
  `max_users` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_tenant_id_unique` (`tenant_id`),
  UNIQUE KEY `companies_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'bf6d67c8-ce6e-4380-9dbd-2290e812b999','RiskyRush Company','risky-rush-company',2,'2026-10-12 16:47:01','active',10,1,'2026-09-08 10:10:48','2026-09-12 11:17:01'),(2,'9a6560cc-7a47-4952-a0f3-c90a780c842d','Demo','demo',2,'2027-03-12 12:55:18','active',NULL,0,'2026-09-09 07:48:50','2026-09-12 11:17:01'),(3,'16a7314c-314f-4922-9f4d-801493d2d28f','demo','demo-2',2,'2027-03-12 12:55:18','active',NULL,1,'2026-09-09 08:04:53','2026-09-09 08:04:53');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_integrations`
--

DROP TABLE IF EXISTS `company_integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_integrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `source_id` bigint(20) unsigned DEFAULT NULL,
  `provider` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `credentials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`credentials`)),
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sync_frequency` varchar(191) NOT NULL DEFAULT 'hourly',
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_integrations_company_id_provider_name_unique` (`company_id`,`provider`,`name`),
  KEY `company_integrations_company_id_index` (`company_id`),
  KEY `company_integrations_source_id_index` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_integrations`
--

LOCK TABLES `company_integrations` WRITE;
/*!40000 ALTER TABLE `company_integrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_payments`
--

DROP TABLE IF EXISTS `company_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `subscription_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `payment_method` varchar(50) NOT NULL DEFAULT 'bank_transfer',
  `transaction_reference` varchar(191) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `recorded_by_user_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_payments_subscription_id_foreign` (`subscription_id`),
  KEY `company_payments_company_id_payment_date_index` (`company_id`,`payment_date`),
  CONSTRAINT `company_payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `company_subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_payments`
--

LOCK TABLES `company_payments` WRITE;
/*!40000 ALTER TABLE `company_payments` DISABLE KEYS */;
INSERT INTO `company_payments` VALUES (1,1,NULL,2499.00,'INR','upi','TEST-UPI-123456','2026-09-12','completed','Test payment verification',NULL,'2026-09-12 07:39:14','2026-09-12 07:39:14'),(4,1,4,2499.00,'INR','razorpay','pay_test_1ab96c1f2722','2026-09-12','completed','Razorpay Payment ID: pay_test_1ab96c1f2722 | Order: order_demo_C6F1C826D1F0',1,'2026-09-12 09:59:23','2026-09-12 09:59:23');
/*!40000 ALTER TABLE `company_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_subscriptions`
--

DROP TABLE IF EXISTS `company_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `starts_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'active',
  `billing_cycle` varchar(32) NOT NULL DEFAULT 'monthly',
  `price_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_subscriptions_plan_id_foreign` (`plan_id`),
  KEY `company_subscriptions_company_id_status_index` (`company_id`,`status`),
  CONSTRAINT `company_subscriptions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_subscriptions`
--

LOCK TABLES `company_subscriptions` WRITE;
/*!40000 ALTER TABLE `company_subscriptions` DISABLE KEYS */;
INSERT INTO `company_subscriptions` VALUES (4,1,2,'2026-09-12 15:29:23','2027-04-12 12:55:18','active','monthly',2499.00,'Renewed via Razorpay. Order ID: order_demo_C6F1C826D1F0','2026-09-12 09:59:23','2026-09-12 09:59:23');
/*!40000 ALTER TABLE `company_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_user`
--

DROP TABLE IF EXISTS `company_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `teamlead_user_id` int(10) unsigned DEFAULT NULL,
  `role` varchar(32) NOT NULL DEFAULT 'employee',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_user_company_id_user_id_unique` (`company_id`,`user_id`),
  KEY `company_user_user_id_foreign` (`user_id`),
  KEY `company_user_company_id_role_index` (`company_id`,`role`),
  CONSTRAINT `company_user_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_user`
--

LOCK TABLES `company_user` WRITE;
/*!40000 ALTER TABLE `company_user` DISABLE KEYS */;
INSERT INTO `company_user` VALUES (40,1,192,NULL,'company_admin',1,'2026-09-12 10:58:23','2026-09-12 11:17:01'),(41,2,192,NULL,'member',1,'2026-09-12 10:58:23','2026-09-12 11:17:01'),(42,2,193,NULL,'company_admin',1,'2026-09-12 10:58:24','2026-09-12 11:17:01'),(44,1,1,NULL,'company_admin',1,'2026-09-13 16:27:45','2026-09-13 17:01:23');
/*!40000 ALTER TABLE `company_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_user`
--

DROP TABLE IF EXISTS `department_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_user_department_id_foreign` (`department_id`),
  KEY `department_user_user_id_foreign` (`user_id`),
  CONSTRAINT `department_user_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_user`
--

LOCK TABLES `department_user` WRITE;
/*!40000 ALTER TABLE `department_user` DISABLE KEYS */;
INSERT INTO `department_user` VALUES (2,1,1,NULL,NULL);
/*!40000 ALTER TABLE `department_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_index` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Managment',NULL,'2026-09-12 09:41:45','2026-09-12 09:41:45',NULL);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `size` varchar(191) NOT NULL,
  `path` varchar(191) NOT NULL,
  `file_display` varchar(191) NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_client_id_foreign` (`client_id`),
  KEY `documents_company_id_index` (`company_id`),
  CONSTRAINT `documents_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `tenant_id` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dump_leads1`
--

DROP TABLE IF EXISTS `dump_leads1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dump_leads1` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `name` varchar(225) DEFAULT NULL,
  `contact_no` varchar(225) DEFAULT NULL,
  `email` varchar(225) DEFAULT NULL,
  `source` varchar(225) DEFAULT NULL,
  `country` varchar(225) DEFAULT NULL,
  `state` varchar(225) DEFAULT NULL,
  `city` varchar(225) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `location` varchar(225) DEFAULT NULL,
  `project` varchar(225) DEFAULT NULL,
  `requirement` varchar(225) DEFAULT NULL,
  `Budget` varchar(225) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `user_assigned_id` int(10) unsigned DEFAULT NULL,
  `user_assign_date` datetime DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `user_created_id` int(10) unsigned NOT NULL DEFAULT 1,
  `contact_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `lead_type` varchar(250) DEFAULT NULL,
  `action_date` datetime DEFAULT NULL,
  `lead_reverse` int(11) NOT NULL DEFAULT 0,
  `lead_rev_date` datetime DEFAULT NULL,
  `reverse_remark` varchar(191) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  KEY `dump_leads1_company_id_index` (`company_id`),
  KEY `idx_dump_id` (`id`),
  KEY `idx_dump_user_status` (`user_assigned_id`,`status`),
  KEY `idx_dump_status` (`status`),
  KEY `idx_dump_updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dump_leads1`
--

LOCK TABLES `dump_leads1` WRITE;
/*!40000 ALTER TABLE `dump_leads1` DISABLE KEYS */;
/*!40000 ALTER TABLE `dump_leads1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `industries`
--

DROP TABLE IF EXISTS `industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `industries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `industries`
--

LOCK TABLES `industries` WRITE;
/*!40000 ALTER TABLE `industries` DISABLE KEYS */;
INSERT INTO `industries` VALUES (1,'Accommodations'),(2,'Accounting'),(3,'Auto'),(4,'Beauty & Cosmetics'),(5,'Carpenter'),(6,'Communications'),(7,'Computer & IT'),(8,'Construction'),(9,'Consulting'),(10,'Education'),(11,'Electronics'),(12,'Entertainment'),(13,'Food & Beverages'),(14,'Legal Services'),(15,'Marketing'),(16,'Real Estate'),(17,'Retail'),(18,'Sports'),(19,'Technology'),(20,'Tourism'),(21,'Transportation'),(22,'Travel'),(23,'Utilities'),(24,'Web Services'),(25,'Other');
/*!40000 ALTER TABLE `industries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integrations`
--

DROP TABLE IF EXISTS `integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_secret` int(11) DEFAULT NULL,
  `api_key` varchar(191) DEFAULT NULL,
  `api_type` varchar(191) DEFAULT NULL,
  `org_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `integrations_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integrations`
--

LOCK TABLES `integrations` WRITE;
/*!40000 ALTER TABLE `integrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_task_time`
--

DROP TABLE IF EXISTS `invoice_task_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_task_time` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_time_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_task_time`
--

LOCK TABLES `invoice_task_time` WRITE;
/*!40000 ALTER TABLE `invoice_task_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_task_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sent` int(11) NOT NULL,
  `received` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_follow_up`
--

DROP TABLE IF EXISTS `lead_follow_up`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_follow_up` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow_up_date` datetime NOT NULL,
  `comment` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `meeting_date` timestamp NULL DEFAULT NULL,
  `senior_visit` varchar(250) DEFAULT NULL,
  `meeting_type` varchar(250) DEFAULT NULL,
  `follow_up_status` varchar(250) DEFAULT NULL,
  `action_date` datetime DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_follow_up_lead_id_index` (`lead_id`),
  KEY `lead_follow_up_company_id_index` (`company_id`),
  KEY `idx_follow_up_lead_date` (`lead_id`,`follow_up_date`),
  KEY `idx_follow_up_lead_meeting` (`lead_id`,`meeting_date`),
  KEY `idx_follow_up_action_date` (`action_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_follow_up`
--

LOCK TABLES `lead_follow_up` WRITE;
/*!40000 ALTER TABLE `lead_follow_up` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_follow_up` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `note` text NOT NULL,
  `status` int(11) NOT NULL,
  `user_assigned_id` int(10) unsigned NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `user_created_id` int(10) unsigned NOT NULL,
  `contact_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `provider` varchar(191) DEFAULT NULL,
  `provider_lead_id` varchar(191) DEFAULT NULL,
  `external_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`external_payload`)),
  `name` varchar(225) DEFAULT NULL,
  `contact_no` varchar(225) DEFAULT NULL,
  `email` varchar(225) DEFAULT NULL,
  `source` varchar(225) DEFAULT NULL,
  `country` varchar(225) DEFAULT NULL,
  `state` varchar(225) DEFAULT NULL,
  `city` varchar(225) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `location` varchar(225) DEFAULT NULL,
  `project` varchar(225) DEFAULT NULL,
  `requirement` varchar(225) DEFAULT NULL,
  `Budget` varchar(225) DEFAULT NULL,
  `user_assign_date` datetime DEFAULT NULL,
  `lead_type` varchar(250) DEFAULT NULL,
  `action_date` datetime DEFAULT NULL,
  `lead_reverse` int(11) NOT NULL DEFAULT 0,
  `lead_rev_date` datetime DEFAULT NULL,
  `reverse_remark` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leads_company_provider_external_unique` (`company_id`,`provider`,`provider_lead_id`),
  KEY `leads_client_id_foreign` (`client_id`),
  KEY `leads_user_created_id_foreign` (`user_created_id`),
  KEY `idx_leads_assigned_status` (`user_assigned_id`,`status`),
  KEY `idx_leads_updated_at` (`updated_at`),
  KEY `leads_company_id_index` (`company_id`),
  KEY `leads_action_date_index` (`action_date`),
  CONSTRAINT `leads_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `leads_user_assigned_id_foreign` FOREIGN KEY (`user_assigned_id`) REFERENCES `users` (`id`),
  CONSTRAINT `leads_user_created_id_foreign` FOREIGN KEY (`user_created_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2015_06_04_124835_create_industries_table',1),(4,'2015_12_28_163028_create_clients_table',1),(5,'2015_12_29_204031_tasks_table',1),(6,'2016_01_10_204413_create_comments_table',1),(7,'2016_01_18_113656_create_leads_table',1),(8,'2016_01_18_202853_create_notes_table',1),(9,'2016_01_23_144854_settings',1),(10,'2016_01_26_003903_documents',1),(11,'2016_01_31_211926_task_time_table',1),(12,'2016_03_21_171847_create_department_table',1),(13,'2016_03_21_172416_create_department_user_table',1),(14,'2016_04_06_230504_integrations',1),(15,'2016_05_21_205532_create_activity_log_table',1),(16,'2016_07_25_150049_create_invoice_table',1),(17,'2016_07_25_151634_create_invoices_client_table',1),(18,'2016_07_25_154026_create_invocies_tasktime_table',1),(19,'2016_08_26_205017_entrust_setup_tables',1),(20,'2016_11_04_200855_create_notifications_table',1),(21,'2019_09_15_000010_create_tenants_table',1),(22,'2019_09_15_000020_create_domains_table',1),(23,'2026_04_23_152230_create_schedulers_table',1),(24,'2026_09_07_000000_create_legacy_support_tables',2),(25,'2026_09_07_141710_add_performance_indexes_to_leads_and_followup',2),(26,'2026_09_07_142024_add_indexes_to_dump_leads_and_notifications',2),(27,'2026_09_08_000001_create_companies_and_memberships',2),(28,'2026_09_08_000002_add_company_scope_to_core_records',2),(29,'2026_09_08_000003_scope_settings_by_company',2),(30,'2026_09_08_000004_add_company_scope_to_remaining_modules',2),(31,'2026_09_08_000005_create_company_settings_and_integrations',2),(32,'2026_09_08_000006_add_external_identity_to_leads',2),(33,'2026_09_08_000007_create_queue_tables',2),(34,'2026_09_08_000008_complete_scheduler_columns',2),(35,'2026_09_08_000009_consolidate_company_settings',2),(36,'2026_09_08_000010_add_platform_super_administrator_role',2),(37,'2026_09_09_000011_name_legacy_company_riskyrush',2),(38,'2026_09_09_000012_add_legacy_push_token_to_users',3),(39,'2026_09_09_000013_align_tenant_lead_schema',4),(40,'2026_09_12_000001_create_plans_subscriptions_payments_and_tickets',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `note` text NOT NULL,
  `status` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notes_user_id_foreign` (`user_id`),
  KEY `notes_lead_id_foreign` (`lead_id`),
  KEY `notes_company_id_index` (`company_id`),
  CONSTRAINT `notes_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(9,2),(9,3),(10,1),(10,2);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'user-create','Create user','Permission to create user','2017-06-28 07:45:33','2017-06-28 07:45:33'),(2,'user-update','Update user','Permission to update user','2017-06-28 07:45:33','2017-06-28 07:45:33'),(3,'user-delete','Delete user','Permission to update delete','2017-06-28 07:45:33','2017-06-28 07:45:33'),(4,'client-create','Create client','Permission to create client','2017-06-28 07:45:33','2017-06-28 07:45:33'),(5,'client-update','Update client','Permission to update client','2017-06-28 07:45:33','2017-06-28 07:45:33'),(6,'client-delete','Delete client','Permission to delete client','2017-06-28 07:45:33','2017-06-28 07:45:33'),(7,'task-create','Create task','Permission to create task','2017-06-28 07:45:33','2017-06-28 07:45:33'),(8,'task-update','Update task','Permission to update task','2017-06-28 07:45:33','2017-06-28 07:45:33'),(9,'lead-create','Create lead','Permission to create lead','2017-06-28 07:45:33','2017-06-28 07:45:33'),(10,'lead-update','Update lead','Permission to update lead','2017-06-28 07:45:33','2017-06-28 07:45:33');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `billing_cycle` varchar(32) NOT NULL DEFAULT 'monthly',
  `max_users` int(10) unsigned DEFAULT NULL,
  `features` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES (1,'Trial','trial','14-day free trial for testing CRM features',0.00,'monthly',5,'Lead Management, Scheduler, Call Logs, Basic Reports',1,'2026-09-12 07:25:18','2026-09-12 07:25:18'),(2,'Starter','starter','Standard plan for growing sales teams',2499.00,'monthly',10,'Full Lead Management, Meeting & Visits, Reverse Leads, Attendance, Bulk Upload',1,'2026-09-12 07:25:18','2026-09-12 07:25:18'),(3,'Professional','professional','Comprehensive package for high-volume brokerages',4999.00,'monthly',30,'All Starter Features, Integrations, HR Module, Unlimited Scheduler, Priority Support',1,'2026-09-12 07:25:18','2026-09-12 07:25:18'),(4,'Enterprise','enterprise','Dedicated solution for large real estate enterprises',9999.00,'monthly',NULL,'Unlimited Users, Dedicated Tenant DB, Custom Integrations, 24/7 Phone Support',1,'2026-09-12 07:25:18','2026-09-12 07:25:18');
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,1),(15,3),(16,1),(17,1),(18,3),(19,3),(20,3),(21,1),(22,3),(23,3),(24,3),(28,3),(33,3),(34,3),(35,3),(36,2),(37,3),(38,2),(39,2),(40,3),(43,3),(44,3),(51,3),(57,3),(59,3),(61,2),(66,3),(68,3),(69,3),(73,3),(74,3),(75,3),(76,3),(77,3),(78,3),(79,3),(83,3),(88,3),(89,3),(90,3),(91,3),(92,3),(93,3),(94,3),(95,3),(96,3),(97,3),(98,3),(99,2),(100,3),(101,3),(102,3),(103,3),(104,3),(105,3),(106,3),(107,3),(109,3),(113,3),(114,3),(116,3),(117,3),(118,2),(119,3),(120,1),(122,3),(124,3),(126,3),(127,3),(128,2),(129,3),(130,3),(131,3),(132,3),(134,3),(135,3),(136,3),(137,3),(138,3),(139,3),(142,2),(143,2),(144,2),(145,3),(146,3),(147,3),(148,3),(149,3),(151,3),(153,3),(154,3),(155,3),(157,3),(158,3),(161,3),(163,3),(164,1),(167,3),(168,3),(171,3),(172,3),(175,3),(178,3),(179,3),(195,5);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'administrator','Administrator','System Administrator','2017-06-28 07:45:33','2017-06-28 07:45:33'),(2,'team leader','Manager','System Manager','2017-06-28 07:45:33','2017-06-28 07:45:33'),(3,'employee','Employee','Employee','2017-06-28 07:45:33','2017-06-28 07:45:33'),(5,'super_administrator','Platform Super Admin','Manages companies and tenants across the platform.','2026-09-08 11:48:12','2026-09-08 11:48:12'),(6,'manager','Manager','System Manager','2026-09-12 09:38:56','2026-09-12 09:38:56');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedulers`
--

DROP TABLE IF EXISTS `schedulers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedulers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `user_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`user_ids`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `projects` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`projects`)),
  `sec_index` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `schedulers_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedulers`
--

LOCK TABLES `schedulers` WRITE;
/*!40000 ALTER TABLE `schedulers` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedulers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `task_complete_allowed` int(11) NOT NULL,
  `task_assign_allowed` int(11) NOT NULL,
  `lead_complete_allowed` int(11) NOT NULL,
  `lead_assign_allowed` int(11) NOT NULL,
  `time_change_allowed` int(11) NOT NULL,
  `comment_allowed` int(11) NOT NULL,
  `country` varchar(191) DEFAULT NULL,
  `company` varchar(191) DEFAULT NULL,
  `timezone` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_company_id_unique` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,1,2,2,2,2,2,2,'en','RiskyRush Company','Asia/Kolkata',NULL,NULL),(6,6,2,2,2,2,0,0,NULL,'Royal Palms Realty 80','Asia/Kolkata','2026-09-12 09:43:20','2026-09-12 09:43:20');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(191) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `subject` varchar(191) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `status` varchar(32) NOT NULL DEFAULT 'open',
  `last_reply_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_company_id_status_index` (`company_id`,`status`),
  KEY `support_tickets_status_priority_index` (`status`,`priority`),
  CONSTRAINT `support_tickets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,'TCK-2026-0001',1,1,'Integration with WhatsApp API','feature_request','medium','in_progress','2026-09-12 13:09:14','2026-09-12 07:39:14','2026-09-12 07:39:14');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL,
  `user_assigned_id` int(10) unsigned NOT NULL,
  `user_created_id` int(10) unsigned NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `deadline` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_user_assigned_id_foreign` (`user_assigned_id`),
  KEY `tasks_user_created_id_foreign` (`user_created_id`),
  KEY `tasks_client_id_foreign` (`client_id`),
  KEY `tasks_company_id_index` (`company_id`),
  CONSTRAINT `tasks_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `tasks_user_assigned_id_foreign` FOREIGN KEY (`user_assigned_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tasks_user_created_id_foreign` FOREIGN KEY (`user_created_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks_time`
--

DROP TABLE IF EXISTS `tasks_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks_time` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `comment` text NOT NULL,
  `value` int(11) NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  `time` int(11) DEFAULT NULL,
  `overtime` int(11) DEFAULT NULL,
  `weekendOvertime` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_time_task_id_foreign` (`task_id`),
  CONSTRAINT `tasks_time_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks_time`
--

LOCK TABLES `tasks_time` WRITE;
/*!40000 ALTER TABLE `tasks_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenants` (
  `id` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES ('1372f328-5a83-48a8-b319-b71e6904b96b','2026-09-12 09:39:30','2026-09-12 09:39:30','{\"updated_at\":\"2026-09-12 15:09:30\",\"created_at\":\"2026-09-12 15:09:30\",\"company_name\":\"Automated Test Realty 559\",\"tenancy_db_name\":\"tenant1372f328-5a83-48a8-b319-b71e6904b96b\"}'),('16a7314c-314f-4922-9f4d-801493d2d28f','2026-09-09 08:04:53','2026-09-09 08:04:53','{\"updated_at\":\"2026-09-09 13:34:53\",\"created_at\":\"2026-09-09 13:34:53\",\"company_name\":\"demo\",\"tenancy_db_name\":\"tenant16a7314c-314f-4922-9f4d-801493d2d28f\"}'),('621409b9-1560-422c-9b1c-3d826f992413','2026-09-12 09:43:16','2026-09-12 09:43:16','{\"updated_at\":\"2026-09-12 15:13:16\",\"created_at\":\"2026-09-12 15:13:16\",\"company_name\":\"Royal Palms Realty 80\",\"tenancy_db_name\":\"tenant621409b9-1560-422c-9b1c-3d826f992413\"}'),('9a6560cc-7a47-4952-a0f3-c90a780c842d','2026-09-09 07:48:50','2026-09-09 07:56:06','{\"created_at\":\"2026-09-09 13:18:50\",\"updated_at\":\"2026-09-09 13:18:50\",\"tenancy_db_name\":\"tenant9a6560cc-7a47-4952-a0f3-c90a780c842d\"}'),('ba9b2a0a-ae51-4961-9013-4f19e5c77ea0','2026-09-12 09:40:48','2026-09-12 09:40:48','{\"updated_at\":\"2026-09-12 15:10:48\",\"created_at\":\"2026-09-12 15:10:48\",\"company_name\":\"Automated Test Realty 888\",\"tenancy_db_name\":\"tenantba9b2a0a-ae51-4961-9013-4f19e5c77ea0\"}'),('bf6d67c8-ce6e-4380-9dbd-2290e812b999','2026-09-08 10:10:48','2026-09-09 07:41:54','{\"company_name\":\"RiskyRush Company\",\"tenancy_db_name\":\"propfin_crm\",\"tenancy_db_connection\":\"mysql\"}');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_replies`
--

DROP TABLE IF EXISTS `ticket_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_replies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `is_superadmin_reply` tinyint(1) NOT NULL DEFAULT 0,
  `attachment_path` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_replies_support_ticket_id_index` (`support_ticket_id`),
  CONSTRAINT `ticket_replies_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_replies`
--

LOCK TABLES `ticket_replies` WRITE;
/*!40000 ALTER TABLE `ticket_replies` DISABLE KEYS */;
INSERT INTO `ticket_replies` VALUES (1,1,1,'Can we connect our official WhatsApp business account for lead alerts?',0,NULL,'2026-09-12 07:39:14','2026-09-12 07:39:14'),(2,1,1,'Hello! Yes, WhatsApp integration is available under Settings > Company Integrations.',1,NULL,'2026-09-12 07:39:14','2026-09-12 07:39:14');
/*!40000 ALTER TABLE `ticket_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(60) NOT NULL,
  `address` varchar(191) DEFAULT NULL,
  `work_number` varchar(191) DEFAULT NULL,
  `personal_number` varchar(191) DEFAULT NULL,
  `pushtoken` text DEFAULT NULL,
  `image_path` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `teamlead` int(11) DEFAULT NULL,
  `user_token` varchar(225) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_teamlead_index` (`teamlead`),
  KEY `users_user_token_index` (`user_token`),
  KEY `idx_users_teamlead` (`teamlead`),
  KEY `idx_users_token` (`user_token`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Default Company Admin','admin@admin.com','$2y$10$J.sh1ZgSP3sQsM9KQCGVA.a2ULcuGun/lSos1IzbL6SKe3L2BGBoS','','0','0',NULL,'',NULL,'2016-06-04 08:12:19','2026-09-13 17:01:23',NULL,'0'),(192,'Multi Workspace User','testmulti@test.com','$2y$10$OPaa25d47DHEFCgFl9BKEOo0dvtMPzqMpQKlFLA4B/Phqj54CIUyK','','1234567890','1234567890',NULL,NULL,NULL,'2026-09-12 10:58:23','2026-09-12 10:58:23',NULL,'0'),(193,'Suspended Workspace User','suspended@test.com','$2y$10$9IKheS/rb7MkByk619zXSO52dYja33gph2SjPQhBDFV.SvWi5WUuK','','1234567890','1234567890',NULL,NULL,NULL,'2026-09-12 10:58:24','2026-09-12 10:58:24',NULL,'0'),(194,'Alice','alice@example.test','$2y$10$94qFeJ0IuuQ/tPjbt5qINeGutEr240QjznaYAAE9SZaZRlQdAV5eS',NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-13 03:56:38','2026-09-13 03:56:38',NULL,'0'),(195,'Platform Super Admin','superadmin@admin.com','$2y$10$R9wtwXDKI8BQde8l7nQl5eAYNQ1BVe6YgiB6KhmibjSLdTyH1OJrC','Platform HQ','0000000000','0000000000',NULL,NULL,NULL,'2026-09-13 17:01:22','2026-09-13 17:01:22',NULL,'0');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'elyleads_central'
--

--
-- Dumping routines for database 'elyleads_central'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-22 21:51:31
