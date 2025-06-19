-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table blog-cms-laravel.articles
CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('post','page') COLLATE utf8mb4_unicode_ci DEFAULT 'post',
  `category_id` bigint unsigned DEFAULT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'cover thumbnail image',
  `cover_large` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_desc` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_category_id_foreign` (`category_id`),
  KEY `articles_user_id_foreign` (`user_id`),
  KEY `articles_title_index` (`title`),
  CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.articles: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.article_tags
CREATE TABLE IF NOT EXISTS `article_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_tags_article_id_foreign` (`article_id`),
  KEY `article_tags_tag_id_foreign` (`tag_id`),
  CONSTRAINT `article_tags_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `article_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.article_tags: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.article_views
CREATE TABLE IF NOT EXISTS `article_views` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `operating_system` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_views_article_id_foreign` (`article_id`),
  CONSTRAINT `article_views_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.article_views: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.cache: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.cache_locks: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.categories: ~5 rows (approximately)
INSERT INTO `categories` (`id`, `category`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Technology', 'technology', '2025-06-19 09:24:46', '2025-06-19 09:24:46'),
	(2, 'Book', 'book', '2025-06-19 09:24:46', '2025-06-19 09:24:46'),
	(3, 'Diary', 'diary', '2025-06-19 09:24:46', '2025-06-19 09:24:46'),
	(4, 'Geo', 'geography-geodesy', '2025-06-19 09:24:46', '2025-06-19 09:24:46'),
	(5, 'Tutorial', 'tutorial', '2025-06-19 09:24:46', '2025-06-19 09:24:46');

-- Dumping structure for table blog-cms-laravel.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `article_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comment content',
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_article_id_foreign` (`article_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `comments_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.comments: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
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

-- Dumping data for table blog-cms-laravel.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.jobs: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
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

-- Dumping data for table blog-cms-laravel.job_batches: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.menus
CREATE TABLE IF NOT EXISTS `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'lokasi menu, eg: header, footer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.menus: ~4 rows (approximately)
INSERT INTO `menus` (`id`, `name`, `location`, `created_at`, `updated_at`) VALUES
	(1, 'Main Header', 'header', NULL, NULL),
	(2, 'Header Top', 'header-top', NULL, NULL),
	(3, 'Footer Menu 1', 'footer-a', NULL, NULL),
	(4, 'Footer Menu 2', 'footer-b', NULL, NULL);

-- Dumping structure for table blog-cms-laravel.menu_items
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` bigint unsigned DEFAULT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu` bigint unsigned NOT NULL DEFAULT '1',
  `depth` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_foreign` (`menu`),
  KEY `menu_items_parent_foreign` (`parent`),
  CONSTRAINT `menu_items_menu_foreign` FOREIGN KEY (`menu`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_items_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.menu_items: ~10 rows (approximately)
INSERT INTO `menu_items` (`id`, `label`, `link`, `parent`, `sort`, `class`, `menu`, `depth`, `created_at`, `updated_at`) VALUES
	(1, 'Home', '/', NULL, 0, NULL, 1, 0, NULL, NULL),
	(2, 'Programming', '/blog/categories/programming', NULL, 1, NULL, 1, 0, NULL, NULL),
	(3, 'Technology', '/blog/categories/technology', NULL, 2, NULL, 1, 0, NULL, NULL),
	(4, 'Home', '/', NULL, 0, NULL, 2, 0, NULL, NULL),
	(5, 'About', '/about', NULL, 1, NULL, 2, 0, NULL, NULL),
	(6, 'Contact', '/contact', NULL, 2, NULL, 2, 0, NULL, NULL),
	(7, 'Home', '/', NULL, 1, NULL, 3, 0, NULL, NULL),
	(8, 'Menu 1', '#', NULL, 2, NULL, 3, 0, NULL, NULL),
	(9, 'Menu 2', '#', NULL, 3, NULL, 3, 0, NULL, NULL),
	(10, 'Menu 1', '#', NULL, 0, NULL, 4, 0, NULL, NULL);

-- Dumping structure for table blog-cms-laravel.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.migrations: ~17 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_03_22_144958_create_personal_access_tokens_table', 1),
	(5, '2025_03_26_022548_modify_personal_access_tokens_table', 1),
	(6, '2025_03_28_025259_create_categories_table', 1),
	(7, '2025_03_28_025542_create_articles_table', 1),
	(8, '2025_03_28_071215_create_tags_table', 1),
	(9, '2025_03_28_071416_create_article_tags_table', 1),
	(10, '2025_03_28_071922_create_article_views_table', 1),
	(11, '2025_03_30_011253_web_setting', 1),
	(12, '2025_04_03_065931_comments', 1),
	(13, '2025_04_06_144336_request_contributor', 1),
	(14, '2025_04_06_193132_newsletter', 1),
	(15, '2025_04_17_170052_menus', 1),
	(16, '2025_04_17_170059_menu_items', 1),
	(17, '2025_06_03_211903_page', 1);

-- Dumping structure for table blog-cms-laravel.newsletters
CREATE TABLE IF NOT EXISTS `newsletters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_subscribed` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.newsletters: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.pages
CREATE TABLE IF NOT EXISTS `pages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isFullWidth` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.pages: ~3 rows (approximately)
INSERT INTO `pages` (`id`, `title`, `description`, `content`, `slug`, `isFullWidth`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('01jy3r2nd25x8f1nm6ew1s46f7', 'Contact', 'Page of Contact', '{ "assets": [], "styles": [ { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "display": "flex", "justify-content": "flex-start", "align-items": "stretch", "flex-wrap": "nowrap", "padding-top": "10px", "padding-right": "10px", "padding-bottom": "10px", "padding-left": "10px" } }, { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "flex-wrap": "wrap" }, "mediaText": "(max-width: 768px)", "atRuleType": "media" }, { "selectors": [{ "name": "gjs-cell", "private": 1 }], "style": { "min-height": "75px", "flex-grow": "1", "flex-basis": "100%" } } ], "pages": [ { "frames": [ { "component": { "type": "wrapper", "stylable": [ "background", "background-color", "background-image", "background-repeat", "background-attachment", "background-position", "background-size" ], "attributes": { "id": "iq2q" } }, "id": "QPjKujkHrih5afuF" } ], "id": "MtEglcsgDUdI00Ua" } ] }', 'contact', 1, '2025-06-19 09:24:49', '2025-06-19 09:24:49', NULL),
	('01jy3r2nh9r95rvrykpppr9yt3', 'Privacy Policy', 'Page of Privacy', '{ "assets": [], "styles": [ { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "display": "flex", "justify-content": "flex-start", "align-items": "stretch", "flex-wrap": "nowrap", "padding-top": "10px", "padding-right": "10px", "padding-bottom": "10px", "padding-left": "10px" } }, { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "flex-wrap": "wrap" }, "mediaText": "(max-width: 768px)", "atRuleType": "media" }, { "selectors": [{ "name": "gjs-cell", "private": 1 }], "style": { "min-height": "75px", "flex-grow": "1", "flex-basis": "100%" } } ], "pages": [ { "frames": [ { "component": { "type": "wrapper", "stylable": [ "background", "background-color", "background-image", "background-repeat", "background-attachment", "background-position", "background-size" ], "attributes": { "id": "iq2q" } }, "id": "QPjKujkHrih5afuF" } ], "id": "MtEglcsgDUdI00Ua" } ] }', 'privacy', 1, '2025-06-19 09:24:49', '2025-06-19 09:24:49', NULL),
	('01jy3r2nmxse0trs13edp5766x', 'Term of Conditions', 'Page of Terms', '{ "assets": [], "styles": [ { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "display": "flex", "justify-content": "flex-start", "align-items": "stretch", "flex-wrap": "nowrap", "padding-top": "10px", "padding-right": "10px", "padding-bottom": "10px", "padding-left": "10px" } }, { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "flex-wrap": "wrap" }, "mediaText": "(max-width: 768px)", "atRuleType": "media" }, { "selectors": [{ "name": "gjs-cell", "private": 1 }], "style": { "min-height": "75px", "flex-grow": "1", "flex-basis": "100%" } } ], "pages": [ { "frames": [ { "component": { "type": "wrapper", "stylable": [ "background", "background-color", "background-image", "background-repeat", "background-attachment", "background-position", "background-size" ], "attributes": { "id": "iq2q" } }, "id": "QPjKujkHrih5afuF" } ], "id": "MtEglcsgDUdI00Ua" } ] }', 'terms', 1, '2025-06-19 09:24:49', '2025-06-19 09:24:49', NULL);

-- Dumping structure for table blog-cms-laravel.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.request_contributors
CREATE TABLE IF NOT EXISTS `request_contributors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_code_until` timestamp NOT NULL,
  `is_confirmed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `request_contributors_user_id_foreign` (`user_id`),
  CONSTRAINT `request_contributors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.request_contributors: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.sessions: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.tags
CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_tag_name_unique` (`tag_name`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.tags: ~0 rows (approximately)

-- Dumping structure for table blog-cms-laravel.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/assets/img/profile/user.png',
  `role` enum('superadmin','admin','writer','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `remember_token`, `profile_photo_path`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('02e5d95a-18d4-4dcc-bcc7-005cac5707c2', 'Writer User', 'writer', 'writer@mail.com', '2025-06-19 09:24:46', '$2y$12$Em9/kyJLS8jpJC4YFUZdL.s.0/IAjImrMETVo0.xYknWfSgZQ/UDq', NULL, '/assets/img/profile/writer.png', 'writer', '2025-06-19 09:24:46', '2025-06-19 09:24:46', NULL),
	('0e471f88-6f0b-4c7d-a1a1-e70d1b4bf31f', 'Regular User', 'user', 'user@mail.com', '2025-06-19 09:24:46', '$2y$12$z1IG86RNlYOY57YDA7aLkumPvORO5QjQHopTIZ2hXKN/tQoBDURhS', NULL, '/assets/img/profile/user.png', 'user', '2025-06-19 09:24:46', '2025-06-19 09:24:46', NULL),
	('7408268a-5d78-407d-97fc-a6dad6703036', 'Admin User', 'admin', 'admin@mail.com', '2025-06-19 09:24:46', '$2y$12$YL0wjUfbaSPdnMxzhhK23uufANEZmOUnqijngBxWRNsL.xlM3sQ3K', NULL, '/assets/img/profile/admin.png', 'admin', '2025-06-19 09:24:46', '2025-06-19 09:24:46', NULL),
	('fcd09914-388c-41c1-b83c-84a18c6d5fdb', 'SuperAdmin User', 'superadmin', 'superadmin@mail.com', '2025-06-19 09:24:45', '$2y$12$DJufhOqAyrF8o0klklI8jO6TTi9cJ16rd4GOKSFjbRgK6FIy72ajS', NULL, '/assets/img/profile/admin.png', 'superadmin', '2025-06-19 09:24:45', '2025-06-19 09:24:45', NULL);

-- Dumping structure for table blog-cms-laravel.web_settings
CREATE TABLE IF NOT EXISTS `web_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.web_settings: ~36 rows (approximately)
INSERT INTO `web_settings` (`id`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
	(1, 'web_name', 'My Blog', 'string', '2025-06-19 09:24:39', '2025-06-19 09:24:39'),
	(2, 'web_name_variant', '3', 'string', '2025-06-19 09:24:39', '2025-06-19 09:24:39'),
	(3, 'tagline', 'My Blog Tagline Here', 'string', '2025-06-19 09:24:39', '2025-06-19 09:24:39'),
	(4, 'description', 'My Blog Description Here for SEO', 'string', '2025-06-19 09:24:39', '2025-06-19 09:24:39'),
	(5, 'keywords', 'My Blog, keywords, Laravel, blog, zakialawi, zakialawi.my.id, zakialawi.com', 'string', '2025-06-19 09:24:39', '2025-06-19 09:24:39'),
	(6, 'app_logo', 'app_logo.png', 'string', '2025-06-19 09:24:39', '2025-06-19 09:24:39'),
	(7, 'favicon', 'favicon.png', 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(8, 'email', 'hallo@zakialawi.my.id', 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(9, 'link_fb', NULL, 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(10, 'link_tiktok', NULL, 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(11, 'link_ig', NULL, 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(12, 'link_twitter', NULL, 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(13, 'link_youtube', NULL, 'string', '2025-06-19 09:24:40', '2025-06-19 09:24:40'),
	(14, 'link_linkedin', NULL, 'string', '2025-06-19 09:24:41', '2025-06-19 09:24:41'),
	(15, 'link_github', NULL, 'string', '2025-06-19 09:24:41', '2025-06-19 09:24:41'),
	(16, 'can_join_contributor', '1', 'boolean', '2025-06-19 09:24:41', '2025-06-19 09:24:41'),
	(17, 'google_adsense', NULL, 'string', '2025-06-19 09:24:41', '2025-06-19 09:24:41'),
	(18, 'google_analytics', NULL, 'string', '2025-06-19 09:24:41', '2025-06-19 09:24:41'),
	(19, 'home_feature_section', '{"label":"Featured Posts","is_visible":true,"total":6,"items":"random-posts"}', 'json', '2025-06-19 09:24:41', '2025-06-19 09:24:41'),
	(20, 'ads_featured', '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'json', '2025-06-19 09:24:42', '2025-06-19 09:24:42'),
	(21, 'home_section_1', '{"label":"Recent Posts","is_visible":true,"total":6,"items":"recent-posts"}', 'json', '2025-06-19 09:24:42', '2025-06-19 09:24:42'),
	(22, 'home_section_2', '{"label":"Default label","is_visible":false,"total":"6","items":""}', 'json', '2025-06-19 09:24:42', '2025-06-19 09:24:42'),
	(23, 'home_section_3', '{"label":"Default label","is_visible":false,"total":"3","items":""}', 'json', '2025-06-19 09:24:42', '2025-06-19 09:24:42'),
	(24, 'home_section_4', '{"label":"Default label","is_visible":false,"total":"3","items":""}', 'json', '2025-06-19 09:24:42', '2025-06-19 09:24:42'),
	(25, 'home_section_5', '{"label":"Default label","is_visible":false,"total":"3","items":""}', 'json', '2025-06-19 09:24:42', '2025-06-19 09:24:42'),
	(26, 'home_sidebar_1', '{"label":"Popular Posts","is_visible":true,"total":4,"items":"popular-posts"}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(27, 'home_sidebar_2', '{"label":"Tags","is_visible":true,"total":10,"items":"all-tags-widget"}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(28, 'home_sidebar_3', '{"label":"Default label","is_visible":false,"total":"5","items":""}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(29, 'home_sidebar_4', '{"label":"Default label","is_visible":false,"total":"5","items":""}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(30, 'ads_sidebar_1', '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(31, 'ads_sidebar_2', '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(32, 'ads_bottom_1', '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'json', '2025-06-19 09:24:43', '2025-06-19 09:24:43'),
	(33, 'home_bottom_section_1', '{"label":"You Missed","is_visible":true,"total":4,"items":"random-posts"}', 'json', '2025-06-19 09:24:44', '2025-06-19 09:24:44'),
	(34, 'ads_bottom_2', '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'json', '2025-06-19 09:24:45', '2025-06-19 09:24:45'),
	(35, 'before_close_head', NULL, 'string', '2025-06-19 09:24:45', '2025-06-19 09:24:45'),
	(36, 'before_close_body', NULL, 'string', '2025-06-19 09:24:45', '2025-06-19 09:24:45');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
