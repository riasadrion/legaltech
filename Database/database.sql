-- MySQL dump 10.13  Distrib 8.0.45, for macos14.8 (arm64)
--
-- Host: 127.0.0.1    Database: legaltech
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `properties` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_log_causer_type_causer_id_index` (`causer_type`,`causer_id`),
  KEY `activity_log_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_log_action_index` (`action`),
  KEY `activity_log_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_notifications_admin_id_read_at_index` (`admin_id`,`read_at`),
  CONSTRAINT `admin_notifications_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_password_reset_tokens`
--

DROP TABLE IF EXISTS `admin_password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_password_reset_tokens`
--

LOCK TABLES `admin_password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `admin_password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role`
--

DROP TABLE IF EXISTS `admin_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_role` (
  `admin_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`admin_id`,`role_id`),
  KEY `admin_role_role_id_foreign` (`role_id`),
  CONSTRAINT `admin_role_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `admin_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role`
--

LOCK TABLES `admin_role` WRITE;
/*!40000 ALTER TABLE `admin_role` DISABLE KEYS */;
INSERT INTO `admin_role` VALUES (1,1);
/*!40000 ALTER TABLE `admin_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Super Admin','admin@example.com','$2y$12$qFZ4XTqAeZm1q8PhV7xhBuBWU78.k2/n6Z3gT5nxQ3HTn5KO4oc/W',NULL,'active',0,NULL,NULL,NULL,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` json NOT NULL,
  `description` json DEFAULT NULL,
  `meta_title` json DEFAULT NULL,
  `meta_description` json DEFAULT NULL,
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (1,'technology','{\"ar\": \"تقنية\", \"en\": \"Technology\"}',NULL,NULL,NULL,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'news','{\"en\": \"News\"}',NULL,NULL,NULL,2,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_post_tag`
--

DROP TABLE IF EXISTS `blog_post_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_post_tag` (
  `blog_post_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`blog_post_id`,`tag_id`),
  KEY `blog_post_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `blog_post_tag_blog_post_id_foreign` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_post_tag`
--

LOCK TABLES `blog_post_tag` WRITE;
/*!40000 ALTER TABLE `blog_post_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_post_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_category_id` bigint unsigned DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `title` json NOT NULL,
  `excerpt` json DEFAULT NULL,
  `body` json NOT NULL,
  `meta_title` json DEFAULT NULL,
  `meta_description` json DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','scheduled','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_blog_category_id_foreign` (`blog_category_id`),
  KEY `blog_posts_author_id_foreign` (`author_id`),
  CONSTRAINT `blog_posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blog_posts_blog_category_id_foreign` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bug_reports`
--

DROP TABLE IF EXISTS `bug_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bug_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bug_reports_admin_id_foreign` (`admin_id`),
  CONSTRAINT `bug_reports_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bug_reports`
--

LOCK TABLES `bug_reports` WRITE;
/*!40000 ALTER TABLE `bug_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `bug_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_parties`
--

DROP TABLE IF EXISTS `contract_parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_parties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('signer','viewer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'signer',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contract_parties_token_unique` (`token`),
  KEY `contract_parties_contract_id_foreign` (`contract_id`),
  CONSTRAINT `contract_parties_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_parties`
--

LOCK TABLES `contract_parties` WRITE;
/*!40000 ALTER TABLE `contract_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_parties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_template_categories`
--

DROP TABLE IF EXISTS `contract_template_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_template_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contract_template_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_template_categories`
--

LOCK TABLES `contract_template_categories` WRITE;
/*!40000 ALTER TABLE `contract_template_categories` DISABLE KEYS */;
INSERT INTO `contract_template_categories` VALUES (1,'Non-Disclosure Agreements','non-disclosure-agreements','heroicon-o-lock-closed',1,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(2,'Employment & HR','employment-hr','heroicon-o-user-group',2,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(3,'Freelance & Services','freelance-services','heroicon-o-briefcase',3,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(4,'SaaS & Software','saas-software','heroicon-o-computer-desktop',4,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(5,'Real Estate & Leases','real-estate-leases','heroicon-o-home',5,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(6,'Partnerships & Joint Ventures','partnerships-joint-ventures','heroicon-o-building-office-2',6,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(7,'Sales & Purchase','sales-purchase','heroicon-o-shopping-cart',7,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(8,'Intellectual Property','intellectual-property','heroicon-o-light-bulb',8,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(9,'Finance & Loans','finance-loans','heroicon-o-banknotes',9,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(10,'General & Miscellaneous','general-miscellaneous','heroicon-o-document-text',10,'2026-05-30 07:36:28','2026-05-30 07:36:28');
/*!40000 ALTER TABLE `contract_template_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_template_fields`
--

DROP TABLE IF EXISTS `contract_template_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_template_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_id` bigint unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('text','textarea','date','number','select','email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `placeholder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` json DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '1',
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract_template_fields_template_id_foreign` (`template_id`),
  CONSTRAINT `contract_template_fields_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `contract_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_template_fields`
--

LOCK TABLES `contract_template_fields` WRITE;
/*!40000 ALTER TABLE `contract_template_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_template_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_templates`
--

DROP TABLE IF EXISTS `contract_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `ai_system_prompt` text COLLATE utf8mb4_unicode_ci,
  `credits_cost` int unsigned NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract_templates_category_id_foreign` (`category_id`),
  CONSTRAINT `contract_templates_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `contract_template_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_templates`
--

LOCK TABLES `contract_templates` WRITE;
/*!40000 ALTER TABLE `contract_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `template_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_data` json DEFAULT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','generating','editing','ready','sent','signed','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `share_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credits_used` int unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contracts_share_token_unique` (`share_token`),
  KEY `contracts_user_id_foreign` (`user_id`),
  KEY `contracts_template_id_foreign` (`template_id`),
  CONSTRAINT `contracts_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `contract_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contracts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_logs`
--

DROP TABLE IF EXISTS `credit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subscription_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_logs_subscription_id_foreign` (`subscription_id`),
  KEY `credit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `credit_logs_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `user_subscriptions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `credit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_logs`
--

LOCK TABLES `credit_logs` WRITE;
/*!40000 ALTER TABLE `credit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_events`
--

DROP TABLE IF EXISTS `email_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_log_id` bigint unsigned DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json DEFAULT NULL,
  `occurred_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_events_notification_log_id_foreign` (`notification_log_id`),
  KEY `email_events_email_event_index` (`email`,`event`),
  KEY `email_events_message_id_index` (`message_id`),
  CONSTRAINT `email_events_notification_log_id_foreign` FOREIGN KEY (`notification_log_id`) REFERENCES `notification_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_events`
--

LOCK TABLES `email_events` WRITE;
/*!40000 ALTER TABLE `email_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_fields`
--

DROP TABLE IF EXISTS `form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` json NOT NULL,
  `placeholder` json DEFAULT NULL,
  `options` json DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `validation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_fields_form_id_foreign` (`form_id`),
  CONSTRAINT `form_fields_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_fields`
--

LOCK TABLES `form_fields` WRITE;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` json NOT NULL,
  `context` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forms_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateways`
--

DROP TABLE IF EXISTS `gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `currency_symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `exchange_rate` decimal(20,8) NOT NULL DEFAULT '1.00000000',
  `decimal_places` tinyint NOT NULL DEFAULT '2',
  `symbol_position` enum('before','after') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'before',
  `credentials` text COLLATE utf8mb4_unicode_ci,
  `config` json DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `max_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `fee_flat` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `fee_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gateways_driver_unique` (`driver`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` VALUES (1,'stripe','Stripe',1,'USD','$',1.00000000,2,'before','eyJpdiI6Imo1UHdOSkgvcENPbVdyb3oxMDR4Rmc9PSIsInZhbHVlIjoiODZqMDNXUS9jcld0YUVvaWx6ZmlDNkEyby9aQ1JPVGlLelVBNHBPcllPMno2WnlPRElYZk5XU1JaekFESU0rNTVrSkhaLzcrVFV4Z2YxZnlIVFZRVEZCemJvR0x6b3YwKzVOTzcwTXUzSituWU9Xc3AyWi9sdkkwVTlyQnZIaWpzaS9SMlFpNWFRQnMwaGMzTWErZlBJNzYxaTZ2KzFWTW4xaXBZc2dWTktrVW94eGtmdkczRjNFYUh0VlgyWEdEcDUvL0V0bkw2TDcvdGtIQlNyN2ZWZlJMVEp6WnBjNEY4RjF1MDNNQ3BQd3ZlNnBXOEhkNFBSWW00b0YvaVQ3cXJ5SGhIZDQ1NGxQN29mTU5KK1gzMnhVOWcxWTMzV3dRSjBYWThUS3FyWnd5UWQxd3lhQXk0c0FqaWdzYUZCNC90aHR5NC9ZeUFyUGFPQmhrMmVIZFdORkhsWHdhWHhYSnU0VjRDMTFPTnptaGc3WGE2djFieU81c3JFbVkwYlNPdVMzd3liMWF6Yk1paDNzeDdhd3dzMGdJRnh6NzRtSzNJMCs5ZmloL1A5MD0iLCJtYWMiOiI0ZTE4MDMzMDRlYjZkNzE3Zjg0YmQ1MTQ1YWZlMDVhYjAzOGU2NmNhODNkNzI5NzZhOTVkMGUwOGM2NTAzN2I0IiwidGFnIjoiIn0=','[]',0.50000000,999999.99000000,0.30000000,2.90,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(2,'paypal','PayPal',1,'USD','$',1.00000000,2,'before',NULL,'[]',1.00000000,999999.99000000,0.49000000,3.49,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(3,'razorpay','Razorpay',0,'INR','₹',83.12000000,2,'before',NULL,'[]',10.00000000,9999999.99000000,0.00000000,2.36,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(4,'paystack','Paystack',0,'NGN','₦',770.50000000,2,'before',NULL,'[]',50.00000000,9999999.99000000,0.00000000,1.50,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(5,'flutterwave','Flutterwave',0,'NGN','₦',770.50000000,2,'before',NULL,'[]',100.00000000,9999999.99000000,0.00000000,1.40,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(6,'mollie','Mollie',0,'EUR','€',0.92000000,2,'after',NULL,'[]',0.01000000,999999.99000000,0.00000000,3.50,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(7,'mercadopago','Mercado Pago',0,'ARS','$',835.50000000,2,'before',NULL,'[]',1.00000000,999999.99000000,0.00000000,2.90,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(8,'coinbase_commerce','Coinbase Commerce',0,'USD','$',1.00000000,6,'before',NULL,'[]',0.00000100,999999.99999900,0.00000000,1.00,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(9,'coingate','CoinGate',0,'USD','$',1.00000000,8,'before',NULL,'[]',0.00000001,999999.99999999,0.00000000,1.00,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(10,'nowpayments','NOWPayments',0,'USD','$',1.00000000,8,'before',NULL,'[]',0.00000001,999999.99999999,0.00000000,0.50,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(11,'izipay','Izipay',0,'XOF','CFA',609.50000000,2,'after',NULL,'[]',100.00000000,9999999.99000000,0.00000000,1.50,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(12,'manual','Manual Payment',0,'USD','$',1.00000000,2,'before',NULL,'[]',0.01000000,999999.99000000,0.00000000,0.00,0,NULL,'2026-05-30 07:36:28','2026-05-30 07:36:28');
/*!40000 ALTER TABLE `gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kyc_submissions`
--

DROP TABLE IF EXISTS `kyc_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kyc_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `data` json NOT NULL,
  `documents` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kyc_submissions_user_id_foreign` (`user_id`),
  KEY `kyc_submissions_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `kyc_submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kyc_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc_submissions`
--

LOCK TABLES `kyc_submissions` WRITE;
/*!40000 ALTER TABLE `kyc_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `kyc_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `native_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` enum('ltr','rtl') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ltr',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `languages_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English','English','en','ltr',1,1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'Arabic','العربية','ar','rtl',0,1,2,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `label` json NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_id` bigint unsigned DEFAULT NULL,
  `open_in_new_tab` tinyint(1) NOT NULL DEFAULT '0',
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  KEY `menu_items_parent_id_foreign` (`parent_id`),
  KEY `menu_items_page_id_foreign` (`page_id`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_items_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,1,NULL,'{\"en\": \"Home\"}','/',NULL,0,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,1,NULL,'{\"en\": \"About\"}','/about',NULL,0,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,1,NULL,'{\"en\": \"Contact\"}','/contact',NULL,0,2,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(4,2,NULL,'{\"en\": \"Product\"}',NULL,NULL,0,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(5,2,4,'{\"en\": \"Features\"}','/#features',NULL,0,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(6,2,4,'{\"en\": \"How it works\"}','/#workflow',NULL,0,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(7,2,4,'{\"en\": \"Pricing\"}','/#pricing',NULL,0,2,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(8,2,4,'{\"en\": \"FAQ\"}','/#faq',NULL,0,3,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(9,2,NULL,'{\"en\": \"Resources\"}',NULL,NULL,0,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(10,2,9,'{\"en\": \"Blog\"}','/blog',NULL,0,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(11,2,9,'{\"en\": \"Contact\"}','/contact',NULL,0,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(12,2,NULL,'{\"en\": \"Company\"}',NULL,NULL,0,2,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(13,2,12,'{\"en\": \"About\"}','/about',NULL,0,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(14,2,12,'{\"en\": \"Contact\"}','/contact',NULL,0,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(15,2,NULL,'{\"en\": \"Legal\"}',NULL,NULL,0,3,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(16,2,15,'{\"en\": \"Terms\"}','/p/terms',NULL,0,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(17,2,15,'{\"en\": \"Privacy\"}','/p/privacy-policy',NULL,0,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(18,2,15,'{\"en\": \"Refund\"}','/p/refund-policy',NULL,0,2,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(19,2,15,'{\"en\": \"Cookies\"}','/p/cookie-policy',NULL,0,3,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `handle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_handle_unique` (`handle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'header','{\"en\": \"Header\"}','2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'footer','{\"en\": \"Footer\"}','2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,'mobile','{\"en\": \"Mobile\"}','2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'0001_01_01_000003_create_admins_table',1),(5,'0001_01_01_000004_create_roles_permissions_tables',1),(6,'0001_01_01_000005_create_social_accounts_table',1),(7,'0001_01_01_000006_create_user_logins_table',1),(8,'0001_01_01_000007_create_settings_table',1),(9,'0001_01_01_000008_create_activity_log_table',1),(10,'0001_01_01_000009_create_languages_table',1),(11,'0001_01_01_000010_create_translations_table',1),(12,'2026_05_08_125004_create_admin_notifications_table',1),(13,'2026_05_08_171649_create_transactions_table',1),(14,'2026_05_08_171650_create_webhook_events_table',1),(15,'2026_05_08_173148_create_gateways_table',1),(16,'2026_05_08_175501_create_forms_table',1),(17,'2026_05_08_175502_create_form_fields_table',1),(18,'2026_05_08_175504_create_kyc_submissions_table',1),(19,'2026_05_09_000001_create_pages_table',1),(20,'2026_05_09_000002_create_page_sections_table',1),(21,'2026_05_09_000003_create_menus_table',1),(22,'2026_05_09_000004_create_subscribers_table',1),(23,'2026_05_09_100001_create_blog_categories_table',1),(24,'2026_05_09_100002_create_tags_table',1),(25,'2026_05_09_100003_create_blog_posts_table',1),(26,'2026_05_09_100004_create_blog_post_tag_table',1),(27,'2026_05_09_200000_create_user_notifications_table',1),(28,'2026_05_09_200001_create_notification_templates_table',1),(29,'2026_05_09_200002_create_notification_template_versions_table',1),(30,'2026_05_09_200003_create_notification_logs_table',1),(31,'2026_05_09_200004_create_notification_suppressions_table',1),(32,'2026_05_09_200005_create_email_events_table',1),(33,'2026_05_09_200006_create_notification_preferences_table',1),(34,'2026_05_09_300001_create_support_tickets_table',1),(35,'2026_05_09_300002_create_support_messages_table',1),(36,'2026_05_09_300003_create_support_attachments_table',1),(37,'2026_05_09_400001_create_bug_reports_table',1),(38,'2026_05_10_000002_create_plans_table',1),(39,'2026_05_10_000003_create_user_subscriptions_table',1),(40,'2026_05_10_000004_create_credit_logs_table',1),(41,'2026_05_17_000001_create_payments_table',1),(42,'2026_05_18_000000_create_phone_verification_codes_table',1),(43,'2026_05_23_000001_create_contract_template_categories_table',1),(44,'2026_05_23_000002_create_contract_templates_table',1),(45,'2026_05_23_000003_create_contract_template_fields_table',1),(46,'2026_05_23_000004_create_contracts_table',1),(47,'2026_05_23_000005_create_contract_parties_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notifiable_id` bigint unsigned DEFAULT NULL,
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `error` text COLLATE utf8mb4_unicode_ci,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_logs_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notification_logs_notification_key_channel_index` (`notification_key`,`channel`),
  KEY `notification_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_preferences`
--

DROP TABLE IF EXISTS `notification_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `notification_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_preferences_user_id_notification_key_channel_unique` (`user_id`,`notification_key`,`channel`),
  CONSTRAINT `notification_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_preferences`
--

LOCK TABLES `notification_preferences` WRITE;
/*!40000 ALTER TABLE `notification_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_suppressions`
--

DROP TABLE IF EXISTS `notification_suppressions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_suppressions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_suppressions_email_unique` (`email`),
  KEY `notification_suppressions_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_suppressions`
--

LOCK TABLES `notification_suppressions` WRITE;
/*!40000 ALTER TABLE `notification_suppressions` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_suppressions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_template_versions`
--

DROP TABLE IF EXISTS `notification_template_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_template_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_template_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `version` int NOT NULL DEFAULT '1',
  `subject` json DEFAULT NULL,
  `email_body` json DEFAULT NULL,
  `sms_body` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_template_versions_created_by_foreign` (`created_by`),
  KEY `notif_template_versions_idx` (`notification_template_id`,`version`),
  CONSTRAINT `notification_template_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notification_template_versions_notification_template_id_foreign` FOREIGN KEY (`notification_template_id`) REFERENCES `notification_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_template_versions`
--

LOCK TABLES `notification_template_versions` WRITE;
/*!40000 ALTER TABLE `notification_template_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_template_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `channels` json DEFAULT NULL,
  `subject` json DEFAULT NULL,
  `email_body` json DEFAULT NULL,
  `sms_body` json DEFAULT NULL,
  `variables` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_templates_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_templates`
--

LOCK TABLES `notification_templates` WRITE;
/*!40000 ALTER TABLE `notification_templates` DISABLE KEYS */;
INSERT INTO `notification_templates` VALUES (1,'welcome','Welcome','Sent when a new user registers.','[\"email\", \"in_app\"]','{\"en\": \"Welcome to {{app.name}}!\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Welcome to {{app.name}}! We\'re glad to have you on board.</p><p>If you have any questions, feel free to reach out to our support team.</p><p>Best regards,<br>The {{app.name}} Team</p>\"}','{\"en\": \"Welcome to {{app.name}}, {{user.name}}! Your account is ready.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"app.name\", \"description\": \"Application name\"}, {\"key\": \"app.url\", \"description\": \"Application URL\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'email_verification','Email Verification','Sent to verify the user\'s email address.','[\"email\"]','{\"en\": \"Verify your email address\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Please click the button below to verify your email address.</p><p><a href=\\\"{{verification.url}}\\\" style=\\\"display:inline-block;background:#1e40af;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;\\\">Verify Email</a></p><p>If you did not create an account, no further action is required.</p>\"}','{\"en\": \"\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"verification.url\", \"description\": \"Email verification link\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,'password_reset','Password Reset','Sent when a user requests a password reset.','[\"email\"]','{\"en\": \"Reset your password\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>You requested a password reset. Click below to set a new password.</p><p><a href=\\\"{{reset.url}}\\\" style=\\\"display:inline-block;background:#1e40af;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;\\\">Reset Password</a></p><p>This link expires in {{reset.expires}} minutes. If you did not request a reset, ignore this email.</p>\"}','{\"en\": \"\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"reset.url\", \"description\": \"Password reset link\"}, {\"key\": \"reset.expires\", \"description\": \"Minutes until link expires\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(4,'payment_received','Payment Received','Sent when a payment is pending review.','[\"email\", \"in_app\"]','{\"en\": \"Payment received — {{amount}} {{currency}}\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>We received your payment of <strong>{{amount}} {{currency}}</strong> via {{gateway}}.</p><p>Your payment is currently being reviewed. You will be notified once it is approved.</p>\"}','{\"en\": \"Payment of {{amount}} {{currency}} received and is under review.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"amount\", \"description\": \"Payment amount\"}, {\"key\": \"currency\", \"description\": \"Currency code\"}, {\"key\": \"gateway\", \"description\": \"Payment gateway name\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(5,'payment_approved','Payment Approved','Sent when a payment is approved and wallet is credited.','[\"email\", \"sms\", \"in_app\"]','{\"en\": \"Payment approved — {{amount}} {{currency}} added to your wallet\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Your payment of <strong>{{amount}} {{currency}}</strong> has been approved and added to your wallet.</p><p>Your new balance is <strong>{{balance}}</strong>.</p>\"}','{\"en\": \"Your payment of {{amount}} {{currency}} was approved. New balance: {{balance}}.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"amount\", \"description\": \"Payment amount\"}, {\"key\": \"currency\", \"description\": \"Currency code\"}, {\"key\": \"balance\", \"description\": \"New wallet balance\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(6,'payment_rejected','Payment Rejected','Sent when a payment is rejected.','[\"email\", \"in_app\"]','{\"en\": \"Payment rejected\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Unfortunately your payment of <strong>{{amount}} {{currency}}</strong> was rejected.</p><p>Reason: {{reason}}</p><p>If you have questions, please contact support.</p>\"}','{\"en\": \"Your payment of {{amount}} {{currency}} was rejected. Reason: {{reason}}\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"amount\", \"description\": \"Payment amount\"}, {\"key\": \"currency\", \"description\": \"Currency code\"}, {\"key\": \"reason\", \"description\": \"Rejection reason\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(7,'kyc_submitted','KYC Submitted','Sent when a user submits KYC documents.','[\"email\", \"in_app\"]','{\"en\": \"KYC documents received\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>We have received your KYC documents and they are currently under review. This usually takes 1–3 business days.</p>\"}','{\"en\": \"Your KYC documents have been received and are under review.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(8,'kyc_approved','KYC Approved','Sent when a KYC submission is approved.','[\"email\", \"sms\", \"in_app\"]','{\"en\": \"KYC approved — your account is verified\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Great news! Your identity has been verified. You now have full access to all platform features.</p>\"}','{\"en\": \"Your KYC verification was approved. You now have full access.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(9,'kyc_rejected','KYC Rejected','Sent when a KYC submission is rejected.','[\"email\", \"in_app\"]','{\"en\": \"KYC verification not approved\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Unfortunately your KYC documents were not approved.</p><p>Reason: {{reason}}</p><p>Please resubmit with the correct documents.</p>\"}','{\"en\": \"Your KYC was not approved. Reason: {{reason}}. Please resubmit.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"reason\", \"description\": \"Rejection reason\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(10,'ticket_created','Support Ticket Created','Sent when a user opens a support ticket.','[\"email\", \"in_app\"]','{\"en\": \"Support ticket #{{ticket.reference}} received\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>Thank you for contacting support. Your ticket has been received.</p><p><strong>Reference:</strong> #{{ticket.reference}}<br><strong>Subject:</strong> {{ticket.subject}}</p><p>We\'ll respond as soon as possible. <a href=\\\"{{ticket.url}}\\\">View your ticket →</a></p>\"}','{\"en\": \"Support ticket #{{ticket.reference}} received. We will be in touch shortly.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"ticket.reference\", \"description\": \"Ticket reference number\"}, {\"key\": \"ticket.subject\", \"description\": \"Ticket subject\"}, {\"key\": \"ticket.url\", \"description\": \"Link to view the ticket\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(11,'ticket_update','Support Ticket Update','Sent when an admin replies to or updates a ticket.','[\"email\", \"in_app\"]','{\"en\": \"Update on your ticket #{{ticket.reference}}\"}','{\"en\": \"<p>Hi {{user.name}},</p><p>There\'s an update on your support ticket.</p><p><strong>Reference:</strong> #{{ticket.reference}}<br><strong>Subject:</strong> {{ticket.subject}}</p><p><a href=\\\"{{ticket.url}}\\\">View ticket and reply →</a></p>\"}','{\"en\": \"Update on ticket #{{ticket.reference}}. Visit {{ticket.url}} to view.\"}','[{\"key\": \"user.name\", \"description\": \"User\'s name\"}, {\"key\": \"ticket.reference\", \"description\": \"Ticket reference number\"}, {\"key\": \"ticket.subject\", \"description\": \"Ticket subject\"}, {\"key\": \"ticket.url\", \"description\": \"Link to view the ticket\"}, {\"key\": \"update\", \"description\": \"Brief description of the update\"}]',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `notification_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_sections`
--

DROP TABLE IF EXISTS `page_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_sections`
--

LOCK TABLES `page_sections` WRITE;
/*!40000 ALTER TABLE `page_sections` DISABLE KEYS */;
INSERT INTO `page_sections` VALUES (1,'hero','{\"pill_url\": \"/features\", \"pill_text\": \"AI-powered legal document automation\", \"pill_label\": \"New\", \"subheading\": \"Generate contracts, NDAs, privacy policies and more. Reviewed by lawyers, customised for your business.\", \"primary_url\": \"/register\", \"primary_text\": \"Get Started Free\", \"secondary_url\": \"/#how-it-works\", \"heading_line_1\": \"Contracts,\", \"heading_line_2\": \"and signed\", \"secondary_text\": \"See How It Works\", \"heading_italic_1\": \"drafted\", \"heading_italic_2\": \"in minutes.\", \"subheading_emphasis\": \"Trusted by 2,000+ businesses\"}',1,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'logos','{\"logos\": [{\"alt\": \"Northbeam\", \"url\": \"sections/logo-northbeam.png\"}, {\"alt\": \"Meridian\", \"url\": \"sections/logo-meridian.png\"}, {\"alt\": \"Helix\", \"url\": \"sections/logo-helix.png\"}, {\"alt\": \"Kairos\", \"url\": \"sections/logo-kairos.png\"}, {\"alt\": \"Atelier\", \"url\": \"sections/logo-atelier.png\"}, {\"alt\": \"Quill\", \"url\": \"sections/logo-quill.png\"}, {\"alt\": \"Sablecourt\", \"url\": \"sections/logo-sablecourt.png\"}, {\"alt\": \"Petrichor\", \"url\": \"sections/logo-petrichor.png\"}], \"heading\": \"Trusted by leading teams\"}',1,1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,'workflow','{\"steps\": [{\"tag\": \"brief\", \"body\": \"Type a sentence, paste an email thread, or upload a deal memo. LegalTech extracts the structure.\", \"title\": \"Describe the deal.\"}, {\"tag\": \"draft\", \"body\": \"Clauses are selected from your library, jurisdiction is set, and every variable is filled in.\", \"title\": \"AI assembles the contract.\"}, {\"tag\": \"review\", \"body\": \"Share with counterparties, redline in real time, and accept AI-suggested fallbacks.\", \"title\": \"Negotiate & redline.\"}, {\"tag\": \"sign\", \"body\": \"Sequential or parallel signing, eIDAS qualified, with a tamper-proof audit certificate attached to the PDF.\", \"title\": \"Both parties sign.\"}], \"cta_url\": \"#pricing\", \"eyebrow\": \"Workflow\", \"heading\": \"From <span class=\\\"italic-display\\\">brief</span> to <span class=\\\"italic-display\\\">binding</span><br>in four moves.\", \"cta_text\": \"See it in action\"}',1,2,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(4,'pricing','{\"eyebrow\": \"Pricing\", \"heading\": \"Pay <span class=\\\"italic-display\\\">per contract.</span><br>Or pay <span class=\\\"italic-display\\\">monthly.</span>\", \"subheading\": \"Two ways to run LegalTech. Switch anytime — your library and audit history follow you.\", \"tab_yearly\": \"Yearly · save 30%\", \"tab_monthly\": \"Monthly · save 20%\", \"tab_per_contract\": \"Pay per contract\"}',1,3,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(5,'why_us','{\"body\": \"Most legal tools were built for legal teams. LegalTech was built for the whole company — so the lawyers can focus on the deals that actually need a lawyer.\", \"cards\": [{\"body\": \"Every AI suggestion ships with a citation to the clause it was learned from.\", \"dark\": \"no\", \"icon\": \"ri-shield-keyhole-fill\", \"title\": \"Defensible\"}, {\"body\": \"Trained on 4M+ executed contracts across 38 jurisdictions. Not a wrapper.\", \"dark\": \"yes\", \"icon\": \"ri-sparkling-2-fill\", \"title\": \"Genuinely smart\"}, {\"body\": \"Run one contract a month or ten thousand. Same speed, same audit-grade trail.\", \"dark\": \"yes\", \"icon\": \"ri-flashlight-fill\", \"title\": \"Built to scale\"}, {\"body\": \"Talk to a licensed paralegal in under 4 minutes, any business day.\", \"dark\": \"no\", \"icon\": \"ri-customer-service-2-fill\", \"title\": \"Real humans\"}], \"stats\": [{\"label\": \"faster from brief to executed contract\", \"value\": \"94<span class=\\\"text-ink-400 text-2xl\\\">%</span>\"}, {\"label\": \"average annual savings per legal seat\", \"value\": \"€38k\"}, {\"label\": \"across 1,200+ G2 & Capterra reviews\", \"value\": \"4.9<span class=\\\"text-ink-400 text-2xl\\\">/5</span>\"}, {\"label\": \"contracts lost. Ever. We have receipts.\", \"value\": \"0\"}], \"eyebrow\": \"Why LegalTech\", \"heading\": \"Engineered by lawyers.<br><span class=\\\"italic-display\\\">Loved by everyone else.</span>\"}',1,4,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(6,'testimonials','{\"eyebrow\": \"Customer stories\", \"heading\": \"The receipt drawer of <span class=\\\"italic-display\\\">happy counsel.</span>\", \"rating_text\": \"4.9 · 1,247 reviews on G2\", \"testimonials\": [{\"name\": \"Mara Vukov\", \"role\": \"General Counsel · Northbeam\", \"quote\": \"We replaced a four-person redlining bottleneck with a single LegalTech workspace. Our reseller deals close in days instead of months — and the lawyers finally sleep.\"}, {\"name\": \"Adrien Chen\", \"role\": \"Head of Ops · Helix/Law\", \"quote\": \"The clause library alone paid for itself in three weeks. Our NDAs are out the door in 90 seconds flat.\"}, {\"name\": \"Sara Aoun\", \"role\": \"Solo Practitioner · Beirut\", \"quote\": \"I run a one-woman practice. LegalTech is the only reason I have a weekend.\"}, {\"name\": \"Priya Raman\", \"role\": \"VP Legal · Kairos AI\", \"quote\": \"The audit trail saved us in two due-diligence calls. That alone is worth the seat.\"}, {\"name\": \"Tomás Iglesias\", \"role\": \"COO · Sablecourt\", \"quote\": \"Counsel signs once, the AI handles the next 80%. Our cycle time dropped 71% in a quarter.\"}, {\"name\": \"Florent Ndiaye\", \"role\": \"Partner · Atelier 14\", \"quote\": \"I expected another e-sign tool. What I got was a junior associate that never sleeps.\"}]}',1,5,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(7,'faq','{\"items\": [{\"answer\": \"Yes. Every clause LegalTech drafts is sourced from a curated, lawyer-reviewed library — not free-form model output. You see exactly which clauses were used, what jurisdiction they map to, and you can override any of them before signature.\", \"question\": \"Is the AI safe to use on real, binding contracts?\"}, {\"answer\": \"LegalTech supports eIDAS (EU/UK), ESIGN & UETA (US), ZertES (Switzerland), and 30+ other regulated frameworks. Qualified Electronic Signatures are available on Studio and Firm plans.\", \"question\": \"Are LegalTech signatures legally binding in my country?\"}, {\"answer\": \"Pay-per-contract is ideal if you ship fewer than ~6 contracts per month — you only pay when you generate and send one. Monthly subscriptions unlock unlimited drafts, redlines, and templates, and are billed at a flat rate.\", \"question\": \"What is the difference between pay-per-contract and monthly?\"}, {\"answer\": \"Never, unless you explicitly opt in. Customer data is logically isolated, encrypted with per-tenant keys, and excluded from any training corpus by default.\", \"question\": \"Do you train your AI on my contracts?\"}, {\"answer\": \"Absolutely. Drag in your .docx files, and LegalTech will parse them into structured clauses you can reuse across the workspace. Most teams import their first 20 templates in under an hour.\", \"question\": \"Can I import my existing templates and playbooks?\"}, {\"answer\": \"You can export every contract, audit certificate, and template as PDF + .docx at any time — including for 90 days after cancellation. No lock-in, no goodbye fee.\", \"question\": \"What happens to my data if I cancel?\"}], \"eyebrow\": \"Questions\", \"heading\": \"Things people <span class=\\\"italic-display\\\">actually ask</span> before they buy.\"}',1,6,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(8,'cta','{\"pill_text\": \"4,200+ teams already shipping faster with LegalTech\", \"subheading\": \"Start your 14-day free trial. No credit card. Generate, sign, and send your first contract before lunch.\", \"primary_url\": \"/register\", \"primary_icon\": \"ri-arrow-right-up-line\", \"primary_text\": \"Start free trial\", \"secondary_url\": \"/contact\", \"heading_line_1\": \"Your next contract\", \"heading_line_2\": \"is one sentence away.\", \"secondary_icon\": \"ri-calendar-line\", \"secondary_text\": \"Book a 20-min demo\"}',1,7,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(10,'blog','{\"eyebrow\": \"From the desk\", \"heading\": \"Thinking on <span class=\\\"italic-display\\\">contracts,</span><br>law & <span class=\\\"italic-display\\\">AI.</span>\", \"link_url\": \"/blog\", \"link_text\": \"All articles\"}',1,99,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(11,'custom_html','{\"content\": \"<h2>1. Information We Collect</h2><p>We collect information you provide directly to us when you create an account, use our services, or contact us for support. This includes your name, email address, payment information, and any contract data you upload or create using LegalTech.</p><h2>2. How We Use Your Information</h2><p>We use the information we collect to provide, maintain, and improve our services, process transactions, send transactional and promotional communications, and comply with legal obligations. We do not sell your personal data to third parties.</p><h2>3. Data Storage and Security</h2><p>Your data is stored on servers located in the European Union. We implement industry-standard encryption (AES-256 at rest, TLS 1.3 in transit) and conduct regular security audits. Contract data is isolated per account and never used to train AI models without explicit consent.</p><h2>4. Cookies</h2><p>We use essential cookies to operate the platform and optional analytics cookies to understand how our service is used. You can manage your cookie preferences at any time through the cookie settings panel.</p><h2>5. Data Retention</h2><p>We retain your account data for as long as your account is active. Upon account deletion, personal data is purged within 30 days, except where retention is required by law. Contract data can be exported before deletion.</p><h2>6. Your Rights</h2><p>Depending on your location, you may have the right to access, correct, delete, or export your personal data. To exercise these rights, contact us at privacy@legaltech.app. We will respond within 30 days.</p><h2>7. Third-Party Services</h2><p>We use a limited number of third-party services to operate our platform, including payment processors and email delivery providers. Each is bound by a Data Processing Agreement and may only process your data as instructed by us.</p><h2>8. Changes to This Policy</h2><p>We may update this Privacy Policy from time to time. We will notify you of material changes by email or by displaying a prominent notice in the application. Continued use of the service after changes constitutes acceptance.</p><h2>9. Contact</h2><p>For privacy-related questions, contact our Data Protection Officer at privacy@legaltech.app.</p>\"}',1,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(12,'custom_html','{\"content\": \"<h2>1. Acceptance of Terms</h2><p>By accessing or using LegalTech, you agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree, do not use the service.</p><h2>2. Description of Service</h2><p>LegalTech provides AI-assisted contract drafting, review, and management tools. The service is provided on a subscription basis. We reserve the right to modify, suspend, or discontinue any part of the service with reasonable notice.</p><h2>3. Account Responsibilities</h2><p>You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account. You must notify us immediately of any unauthorised access. You may not share your account with others or use the service for any unlawful purpose.</p><h2>4. Acceptable Use</h2><p>You agree not to use LegalTech to draft documents intended to facilitate fraud, to circumvent legal obligations, or to harm third parties. We reserve the right to suspend accounts that violate this policy without prior notice.</p><h2>5. Intellectual Property</h2><p>All content, features, and functionality of LegalTech are owned by us and protected by copyright, trademark, and other intellectual property laws. You retain full ownership of contracts and documents you create using the service.</p><h2>6. AI-Generated Content Disclaimer</h2><p>LegalTech uses artificial intelligence to assist with legal document drafting. AI-generated output does not constitute legal advice. You are responsible for reviewing all documents before execution.</p><h2>7. Payment and Billing</h2><p>Paid subscriptions are billed in advance on a monthly or annual basis. All fees are non-refundable except as set out in our Refund Policy. We reserve the right to change pricing with 30 days notice to existing subscribers.</p><h2>8. Limitation of Liability</h2><p>To the maximum extent permitted by law, LegalTech shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the service. Our total liability shall not exceed the amount paid by you in the twelve months preceding the claim.</p><h2>9. Governing Law</h2><p>These terms are governed by the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.</p><h2>10. Contact</h2><p>For questions about these terms, contact us at legal@legaltech.app.</p>\"}',1,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(13,'custom_html','{\"content\": \"<h2>Our Refund Commitment</h2><p>We want you to be completely satisfied with LegalTech. If you are not happy with your purchase, we offer a straightforward refund process designed to be fair to all customers.</p><h2>14-Day Money-Back Guarantee</h2><p>All new paid subscriptions are eligible for a full refund within 14 days of the initial payment, no questions asked. To request a refund within this window, contact our support team at billing@legaltech.app with your account email and order reference.</p><h2>Annual Subscriptions</h2><p>For annual subscriptions cancelled after the 14-day window, we will issue a pro-rated refund for the unused months remaining on your subscription, calculated from the date of your cancellation request.</p><h2>Monthly Subscriptions</h2><p>Monthly subscriptions cancelled after the 14-day window are not eligible for a pro-rated refund. Your access will continue until the end of the current billing period, after which it will not renew.</p><h2>Non-Refundable Items</h2><p>Add-on purchases, one-time fees, and payments made more than 90 days before the refund request are not eligible for refund. Accounts suspended for violation of our Terms of Service are not eligible for any refund.</p><h2>How to Request a Refund</h2><p>Email billing@legaltech.app with your name, account email, and reason for the refund. We process all eligible refund requests within 5 business days. Funds are returned to the original payment method and may take 3-10 business days to appear depending on your bank.</p><h2>Contact</h2><p>For billing questions, contact billing@legaltech.app.</p>\"}',1,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(14,'custom_html','{\"content\": \"<h2>What Are Cookies</h2><p>Cookies are small text files placed on your device when you visit a website. They are widely used to make websites work efficiently, remember your preferences, and provide information to site owners about how the site is being used.</p><h2>How We Use Cookies</h2><p>LegalTech uses cookies for three purposes: to operate the platform (essential cookies), to remember your preferences such as language and theme (functional cookies), and to understand how users interact with the service (analytics cookies).</p><h2>Essential Cookies</h2><p>These cookies are strictly necessary for the platform to function and cannot be disabled. They include session management, CSRF protection tokens, and authentication state. Without these cookies, the service cannot operate.</p><table><thead><tr><th>Cookie</th><th>Purpose</th><th>Duration</th></tr></thead><tbody><tr><td>session</td><td>Maintains your login session</td><td>Until browser closes</td></tr><tr><td>XSRF-TOKEN</td><td>Security protection against cross-site request forgery</td><td>2 hours</td></tr><tr><td>locale</td><td>Remembers your language preference</td><td>1 year</td></tr></tbody></table><h2>Analytics Cookies</h2><p>With your consent, we use privacy-respecting analytics to understand which features are most used and where users encounter difficulty. We do not use Google Analytics. Our analytics provider does not build individual user profiles or sell data.</p><h2>Managing Cookies</h2><p>You can control cookies through the cookie preferences panel, accessible via the footer of any page. You can also manage cookies through your browser settings. Note that disabling essential cookies will prevent you from using the service.</p><h2>Contact</h2><p>For cookie-related questions, contact privacy@legaltech.app.</p>\"}',1,0,'2026-05-30 07:36:24','2026-05-30 07:36:24'),(15,'contact','[]',1,0,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `page_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` json NOT NULL,
  `meta_title` json DEFAULT NULL,
  `meta_description` json DEFAULT NULL,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `section_ids` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'home','{\"en\": \"Home\"}',NULL,NULL,NULL,'published',1,'[1, 2, 3, 4, 5, 6, 7, 8, 10]','2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'privacy-policy','{\"en\": \"Privacy Policy\"}',NULL,NULL,NULL,'published',1,'[11]','2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,'terms','{\"en\": \"Terms of Service\"}',NULL,NULL,NULL,'published',1,'[12]','2026-05-30 07:36:24','2026-05-30 07:36:24'),(4,'refund-policy','{\"en\": \"Refund Policy\"}',NULL,NULL,NULL,'published',1,'[13]','2026-05-30 07:36:24','2026-05-30 07:36:24'),(5,'cookie-policy','{\"en\": \"Cookie Policy\"}',NULL,NULL,NULL,'published',1,'[14]','2026-05-30 07:36:24','2026-05-30 07:36:24'),(6,'contact','{\"en\": \"Contact\"}',NULL,NULL,NULL,'published',1,'[15]','2026-05-30 07:36:24','2026-05-30 07:36:24'),(7,'about','{\"en\": \"About Us\"}',NULL,NULL,NULL,'published',1,'[]','2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `gateway_id` bigint unsigned NOT NULL,
  `amount` decimal(28,8) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `fee` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_gateway_id_foreign` (`gateway_id`),
  KEY `payments_transaction_id_foreign` (`transaction_id`),
  KEY `payments_reviewed_by_foreign` (`reviewed_by`),
  KEY `payments_status_index` (`status`),
  KEY `payments_user_id_index` (`user_id`),
  CONSTRAINT `payments_gateway_id_foreign` FOREIGN KEY (`gateway_id`) REFERENCES `gateways` (`id`),
  CONSTRAINT `payments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard.view','dashboard','2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'users.view','users','2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,'users.manage','users','2026-05-30 07:36:24','2026-05-30 07:36:24'),(4,'users.delete','users','2026-05-30 07:36:24','2026-05-30 07:36:24'),(5,'roles.manage','roles','2026-05-30 07:36:24','2026-05-30 07:36:24'),(6,'staff.manage','staff','2026-05-30 07:36:24','2026-05-30 07:36:24'),(7,'payments.view','payments','2026-05-30 07:36:24','2026-05-30 07:36:24'),(8,'payments.manage','payments','2026-05-30 07:36:24','2026-05-30 07:36:24'),(9,'kyc.review','kyc','2026-05-30 07:36:24','2026-05-30 07:36:24'),(10,'plans.manage','plans','2026-05-30 07:36:24','2026-05-30 07:36:24'),(11,'cms.view','cms','2026-05-30 07:36:24','2026-05-30 07:36:24'),(12,'cms.manage','cms','2026-05-30 07:36:24','2026-05-30 07:36:24'),(13,'cms.custom_html.raw','cms','2026-05-30 07:36:24','2026-05-30 07:36:24'),(14,'blog.view','blog','2026-05-30 07:36:24','2026-05-30 07:36:24'),(15,'blog.manage','blog','2026-05-30 07:36:24','2026-05-30 07:36:24'),(16,'notifications.manage','notifications','2026-05-30 07:36:24','2026-05-30 07:36:24'),(17,'support.view','support','2026-05-30 07:36:24','2026-05-30 07:36:24'),(18,'support.manage','support','2026-05-30 07:36:24','2026-05-30 07:36:24'),(19,'reports.view','reports','2026-05-30 07:36:24','2026-05-30 07:36:24'),(20,'settings.manage','settings','2026-05-30 07:36:24','2026-05-30 07:36:24'),(21,'languages.manage','languages','2026-05-30 07:36:24','2026-05-30 07:36:24'),(22,'system.view','system','2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_verification_codes`
--

DROP TABLE IF EXISTS `phone_verification_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_verification_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NOT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `phone_verification_codes_user_id_verified_at_index` (`user_id`,`verified_at`),
  CONSTRAINT `phone_verification_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_verification_codes`
--

LOCK TABLES `phone_verification_codes` WRITE;
/*!40000 ALTER TABLE `phone_verification_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_verification_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(28,8) NOT NULL,
  `billing_cycle` enum('per_contract','monthly','yearly','lifetime') COLLATE utf8mb4_unicode_ci NOT NULL,
  `credits` int unsigned NOT NULL DEFAULT '0',
  `ai_contract_generation` tinyint(1) NOT NULL DEFAULT '0',
  `ai_drafts` int unsigned NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `cta_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES (1,'Pay as you go','No subscription. Pay only for what you use.',9.00000000,'per_contract',1,0,0,'[\"1 contract per purchase\", \"All template categories\", \"PDF export\", \"E-signature requests\"]',0,'Get started','/register',1,0,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(2,'Starter','For individuals trying things out.',0.00000000,'monthly',3,0,0,'[\"3 contract generations\", \"All template categories\", \"PDF export\", \"E-signature requests\"]',0,'Start free','/register',1,1,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(3,'Pro','For freelancers and small teams.',29.00000000,'monthly',50,1,10,'[\"10 AI drafts\", \"50 contract generations\", \"Priority support\", \"Unlimited templates\"]',1,'Subscribe now','/register',1,2,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(4,'Pro Yearly','Pro plan billed annually — save 20%.',279.00000000,'yearly',600,1,120,'[\"120 AI drafts\", \"600 contract generations\", \"Priority support\", \"2 months free\"]',0,'Subscribe now','/register',1,3,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(5,'Business','For growing companies.',99.00000000,'monthly',250,1,50,'[\"50 AI drafts\", \"250 contract generations\", \"Team seats\", \"API access\", \"Dedicated support\"]',0,'Subscribe now','/register',1,4,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(6,'Business Yearly','Business plan billed annually — save 20%.',950.00000000,'yearly',3000,1,600,'[\"600 AI drafts\", \"3,000 contract generations\", \"Team seats\", \"API access\", \"Dedicated support\", \"2 months free\"]',0,'Subscribe now','/register',1,5,'2026-05-30 07:36:28','2026-05-30 07:36:28'),(7,'Enterprise Yearly','Custom pricing for legal teams and large orgs.',0.00000000,'yearly',0,1,0,'[\"Unlimited AI drafts\", \"Unlimited contract generations\", \"SSO & SAML\", \"Dedicated account manager\", \"Custom integrations\", \"SLA\"]',1,'Talk to sales','/contact',1,7,'2026-05-30 07:36:28','2026-05-30 07:36:28');
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permission_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES (1,1),(2,1),(3,1),(1,2),(2,2),(3,2),(1,3),(2,3),(1,4),(1,5),(1,6),(1,7),(2,7),(1,8),(1,9),(2,9),(1,10),(2,10),(1,11),(2,11),(1,12),(2,12),(1,13),(1,14),(2,14),(1,15),(2,15),(1,16),(2,16),(1,17),(2,17),(3,17),(1,18),(2,18),(3,18),(1,19),(2,19),(1,20),(2,20),(1,21),(2,21),(1,22),(2,22);
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super_admin','admin','2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'admin','admin','2026-05-30 07:36:24','2026-05-30 07:36:24'),(3,'staff','admin','2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` json DEFAULT NULL,
  `type` enum('string','boolean','integer','float','array','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `autoload` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `settings_group_autoload_index` (`group`,`autoload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('ai.openai_key','\"\"','string','ai',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('ai.openai_max_tokens','800','integer','ai',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('ai.openai_model','\"gpt-4o-mini\"','string','ai',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('ai.openai_temperature','\"0.3\"','string','ai',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.cookie_consent','\"1\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.email_verification','\"0\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.force_https','\"0\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.kyc_enabled','\"0\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.policy_agreement','\"0\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.registration_enabled','\"1\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('controls.sms_verification','\"0\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.driver','\"log\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.from_address','\"hello@example.com\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.from_name','\"LegalTech\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.mailgun.domain','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.mailgun.endpoint','\"api.mailgun.net\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.mailgun.secret','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.postmark.token','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.resend.key','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.ses.key','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.ses.region','\"us-east-1\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.ses.secret','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.smtp.encryption','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.smtp.host','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.smtp.password','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.smtp.port','\"587\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('mail.smtp.username','\"\"','string','mail',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.custom_css','\"\"','string','general',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.dark_logo','\"brand/group-1000003780-1-P0dJof2i.png\"','string','general',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.favicon','\"brand/frame-1000003740-9rBLtRLP.png\"','string','general',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.font','\"system\"','string','general',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.logo','\"brand/group-1000003780-EULsmmk0.png\"','string','general',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.maintenance_mode','\"0\"','string','controls',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('site.primary_color','\"#D4FF3A\"','string','general',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.facebook.client_id','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.facebook.client_secret','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.facebook.enabled','\"0\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.google.client_id','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.google.client_secret','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.google.enabled','\"0\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.linkedin.client_id','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.linkedin.client_secret','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.linkedin.enabled','\"0\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.twitter.client_id','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.twitter.client_secret','\"\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24'),('social.twitter.enabled','\"0\"','string','social',1,'2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_accounts`
--

DROP TABLE IF EXISTS `social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refresh_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_accounts_provider_provider_id_unique` (`provider`,`provider_id`),
  KEY `social_accounts_user_id_provider_index` (`user_id`,`provider`),
  CONSTRAINT `social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_accounts`
--

LOCK TABLES `social_accounts` WRITE;
/*!40000 ALTER TABLE `social_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','confirmed','unsubscribed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`),
  UNIQUE KEY `subscribers_token_unique` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_attachments`
--

DROP TABLE IF EXISTS `support_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_message_id` bigint unsigned NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_attachments_support_message_id_foreign` (`support_message_id`),
  CONSTRAINT `support_attachments_support_message_id_foreign` FOREIGN KEY (`support_message_id`) REFERENCES `support_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_attachments`
--

LOCK TABLES `support_attachments` WRITE;
/*!40000 ALTER TABLE `support_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint unsigned NOT NULL,
  `author_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_messages_author_type_author_id_index` (`author_type`,`author_id`),
  KEY `support_messages_support_ticket_id_created_at_index` (`support_ticket_id`,`created_at`),
  CONSTRAINT `support_messages_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `guest_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_verify_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_verified` tinyint(1) NOT NULL DEFAULT '0',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_reference_unique` (`reference`),
  KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  KEY `support_tickets_status_created_at_index` (`status`,`created_at`),
  KEY `support_tickets_user_id_status_index` (`user_id`,`status`),
  KEY `support_tickets_guest_verify_token_index` (`guest_verify_token`),
  CONSTRAINT `support_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'laravel','{\"en\": \"Laravel\"}','2026-05-30 07:36:24','2026-05-30 07:36:24'),(2,'tips','{\"en\": \"Tips\"}','2026-05-30 07:36:24','2026-05-30 07:36:24');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(28,8) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `balance_after` decimal(28,8) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `admin_id` bigint unsigned DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `transactions_admin_id_foreign` (`admin_id`),
  KEY `transactions_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `transactions_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  CONSTRAINT `transactions_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translations_locale_group_key_unique` (`locale`,`group`,`key`),
  KEY `translations_locale_index` (`locale`),
  KEY `translations_group_index` (`group`),
  KEY `translations_key_index` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `country_code` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logged_in_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_logins_user_id_index` (`user_id`),
  KEY `user_logins_logged_in_at_index` (`logged_in_at`),
  CONSTRAINT `user_logins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_notifications_user_id_read_at_index` (`user_id`,`read_at`),
  CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_role_role_id_foreign` (`role_id`),
  CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_subscriptions`
--

DROP TABLE IF EXISTS `user_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','past_due','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL,
  `credits_remaining` bigint unsigned NOT NULL DEFAULT '0',
  `ai_drafts_remaining` int unsigned NOT NULL DEFAULT '0',
  `current_period_start` timestamp NULL DEFAULT NULL,
  `current_period_end` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `payment_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_subscriptions_plan_id_foreign` (`plan_id`),
  KEY `user_subscriptions_user_id_status_index` (`user_id`,`status`),
  KEY `user_subscriptions_payment_id_foreign` (`payment_id`),
  CONSTRAINT `user_subscriptions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_subscriptions`
--

LOCK TABLES `user_subscriptions` WRITE;
/*!40000 ALTER TABLE `user_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_country_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_verified_at` timestamp NULL DEFAULT NULL,
  `country_code` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','banned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `kyc_status` enum('none','pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `balance` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhook_events`
--

DROP TABLE IF EXISTS `webhook_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gateway` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `status` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processed',
  `error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webhook_events_event_id_unique` (`event_id`),
  KEY `webhook_events_gateway_status_index` (`gateway`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhook_events`
--

LOCK TABLES `webhook_events` WRITE;
/*!40000 ALTER TABLE `webhook_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `webhook_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'legaltech'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 19:37:09
