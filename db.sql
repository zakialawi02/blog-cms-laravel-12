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

-- Dumping data for table blog-cms-laravel.cache: ~2 rows (approximately)
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('blog_laravel_12_cache_nav_menus', 'a:4:{s:6:"header";a:6:{s:2:"id";i:1;s:4:"name";s:11:"Main Header";s:8:"location";s:6:"header";s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:5:"items";a:3:{i:0;a:11:{s:2:"id";i:1;s:5:"label";s:4:"Home";s:4:"link";s:1:"/";s:6:"parent";N;s:4:"sort";i:1;s:5:"class";N;s:4:"menu";i:1;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}i:1;a:11:{s:2:"id";i:2;s:5:"label";s:11:"Programming";s:4:"link";s:28:"/blog/categories/programming";s:6:"parent";N;s:4:"sort";i:2;s:5:"class";N;s:4:"menu";i:1;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}i:2;a:11:{s:2:"id";i:3;s:5:"label";s:10:"Technology";s:4:"link";s:27:"/blog/categories/technology";s:6:"parent";N;s:4:"sort";i:3;s:5:"class";N;s:4:"menu";i:1;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}}}s:10:"header-top";a:6:{s:2:"id";i:2;s:4:"name";s:10:"Header Top";s:8:"location";s:10:"header-top";s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:5:"items";a:3:{i:0;a:11:{s:2:"id";i:4;s:5:"label";s:4:"Home";s:4:"link";s:1:"/";s:6:"parent";N;s:4:"sort";i:1;s:5:"class";N;s:4:"menu";i:2;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}i:1;a:11:{s:2:"id";i:5;s:5:"label";s:5:"About";s:4:"link";s:6:"/about";s:6:"parent";N;s:4:"sort";i:2;s:5:"class";N;s:4:"menu";i:2;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}i:2;a:11:{s:2:"id";i:6;s:5:"label";s:7:"Contact";s:4:"link";s:8:"/contact";s:6:"parent";N;s:4:"sort";i:3;s:5:"class";N;s:4:"menu";i:2;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}}}s:8:"footer-a";a:6:{s:2:"id";i:3;s:4:"name";s:13:"Footer Menu 1";s:8:"location";s:8:"footer-a";s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:5:"items";a:3:{i:0;a:11:{s:2:"id";i:7;s:5:"label";s:4:"Home";s:4:"link";s:1:"/";s:6:"parent";N;s:4:"sort";i:1;s:5:"class";N;s:4:"menu";i:3;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}i:1;a:11:{s:2:"id";i:8;s:5:"label";s:6:"Menu 1";s:4:"link";s:1:"#";s:6:"parent";N;s:4:"sort";i:2;s:5:"class";N;s:4:"menu";i:3;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}i:2;a:11:{s:2:"id";i:9;s:5:"label";s:6:"Menu 2";s:4:"link";s:1:"#";s:6:"parent";N;s:4:"sort";i:3;s:5:"class";N;s:4:"menu";i:3;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}}}s:8:"footer-b";a:6:{s:2:"id";i:4;s:4:"name";s:13:"Footer Menu 2";s:8:"location";s:8:"footer-b";s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:5:"items";a:1:{i:0;a:11:{s:2:"id";i:10;s:5:"label";s:6:"Menu 1";s:4:"link";s:1:"#";s:6:"parent";N;s:4:"sort";i:1;s:5:"class";N;s:4:"menu";i:4;s:5:"depth";i:0;s:10:"created_at";s:27:"2025-06-03T09:06:18.000000Z";s:10:"updated_at";s:27:"2025-06-03T09:06:18.000000Z";s:8:"children";a:0:{}}}}}', 1751533819),
	('blog_laravel_12_cache_web_setting', 'a:23:{s:8:"web_name";s:7:"My Blog";s:16:"web_name_variant";i:3;s:7:"tagline";s:20:"My Blog Tagline Here";s:11:"description";s:32:"My Blog Description Here for SEO";s:8:"keywords";s:75:"My Blog, keywords, Laravel, blog, zakialawi, zakialawi.my.id, zakialawi.com";s:8:"app_logo";s:12:"app_logo.png";s:7:"favicon";s:11:"favicon.png";s:5:"email";s:21:"hallo@zakialawi.my.id";s:7:"link_fb";s:0:"";s:11:"link_tiktok";s:0:"";s:7:"link_ig";s:0:"";s:12:"link_twitter";s:0:"";s:12:"link_youtube";s:0:"";s:13:"link_linkedin";s:0:"";s:11:"link_github";s:0:"";s:20:"can_join_contributor";b:1;s:20:"home_feature_section";a:4:{s:5:"label";s:14:"Featured Posts";s:10:"is_visible";s:1:"1";s:5:"total";i:6;s:5:"items";s:12:"random-posts";}s:14:"home_section_1";a:4:{s:5:"label";s:12:"Recent Posts";s:10:"is_visible";s:1:"1";s:5:"total";i:6;s:5:"items";s:12:"recent-posts";}s:14:"home_section_2";a:4:{s:5:"label";s:0:"";s:10:"is_visible";b:0;s:5:"total";i:6;s:5:"items";s:0:"";}s:14:"home_section_3";a:4:{s:5:"label";s:0:"";s:10:"is_visible";b:0;s:5:"total";i:3;s:5:"items";s:0:"";}s:14:"home_sidebar_1";a:4:{s:5:"label";s:13:"Popular Posts";s:10:"is_visible";s:1:"1";s:5:"total";i:4;s:5:"items";s:13:"popular-posts";}s:14:"home_sidebar_2";a:4:{s:5:"label";s:4:"Tags";s:10:"is_visible";s:1:"1";s:5:"total";i:10;s:5:"items";s:15:"all-tags-widget";}s:21:"home_bottom_section_1";a:4:{s:5:"label";s:10:"You Missed";s:10:"is_visible";s:1:"1";s:5:"total";i:4;s:5:"items";s:12:"random-posts";}}', 2064301957);

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
	(1, 'Technology', 'technology', '2025-06-03 09:06:17', '2025-06-03 09:06:17'),
	(2, 'Book', 'book', '2025-06-03 09:06:17', '2025-06-03 09:06:17'),
	(3, 'Diary', 'diary', '2025-06-03 09:06:17', '2025-06-03 09:06:17'),
	(4, 'Geo', 'geography-geodesy', '2025-06-03 09:06:17', '2025-06-03 09:06:17'),
	(5, 'Tutorial', 'tutorial', '2025-06-03 09:06:17', '2025-06-03 09:06:17');

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
	(1, 'Main Header', 'header', '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(2, 'Header Top', 'header-top', '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(3, 'Footer Menu 1', 'footer-a', '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(4, 'Footer Menu 2', 'footer-b', '2025-06-03 09:06:18', '2025-06-03 09:06:18');

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
	(1, 'Home', '/', NULL, 1, NULL, 1, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(2, 'Programming', '/blog/categories/programming', NULL, 2, NULL, 1, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(3, 'Technology', '/blog/categories/technology', NULL, 3, NULL, 1, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(4, 'Home', '/', NULL, 1, NULL, 2, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(5, 'About', '/about', NULL, 2, NULL, 2, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(6, 'Contact', '/contact', NULL, 3, NULL, 2, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(7, 'Home', '/', NULL, 1, NULL, 3, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(8, 'Menu 1', '#', NULL, 2, NULL, 3, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(9, 'Menu 2', '#', NULL, 3, NULL, 3, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18'),
	(10, 'Menu 1', '#', NULL, 1, NULL, 4, 0, '2025-06-03 09:06:18', '2025-06-03 09:06:18');

-- Dumping structure for table blog-cms-laravel.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.migrations: ~0 rows (approximately)
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
	(16, '2025_04_17_170059_menu_items', 1);

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

-- Dumping data for table blog-cms-laravel.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('UNwAPMPEsGuV8fpCd2ohYFnjZvVqtExDqlEZMCK0', 'cd2fd383-0a14-4849-9e72-d5aad02bd125', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM1liV210V2ZCWHRDNDRnczBBWDRBU0xTbUVSWXlReWdwZDF4UHFOVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvcGFnZXMvbGF5b3V0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MzY6ImNkMmZkMzgzLTBhMTQtNDg0OS05ZTcyLWQ1YWFkMDJiZDEyNSI7fQ==', 1748941957);

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
	('0efd3276-423b-4b5b-aca3-b3b0790765e3', 'Writer User', 'writer', 'writer@mail.com', '2025-06-03 09:06:17', '$2y$12$n.VrJ1wtkrFGJGHL0nRm7uSK43wKcEHgbqzaAWlkWGt7L.ZPznUyi', NULL, '/assets/img/profile/writer.png', 'writer', '2025-06-03 09:06:17', '2025-06-03 09:06:17', NULL),
	('cd2fd383-0a14-4849-9e72-d5aad02bd125', 'SuperAdmin User', 'superadmin', 'superadmin@mail.com', '2025-06-03 09:06:16', '$2y$12$eEAa38LNw48UV5MY6s3NluQTq8HgzKJAGW9NWb.v2avI5qSR6XGMW', NULL, '/assets/img/profile/admin.png', 'superadmin', '2025-06-03 09:06:16', '2025-06-03 09:06:16', NULL),
	('eabcbeb7-d0d2-4adb-affa-950f6c2393a0', 'Regular User', 'user', 'user@mail.com', '2025-06-03 09:06:17', '$2y$12$PBo.UzXG7pW/8FqADVhjD.SsoKsQs/aghd69ZszePY/zIFU/6lJe.', NULL, '/assets/img/profile/user.png', 'user', '2025-06-03 09:06:17', '2025-06-03 09:06:17', NULL),
	('f2c02a2f-9970-43d8-942f-1fecf34e9722', 'Admin User', 'admin', 'admin@mail.com', '2025-06-03 09:06:17', '$2y$12$V71S939h5AXCTRG4Spy6eeU8F/1Bz0bP9uO5MD07kK/iCr8M.A4Ea', NULL, '/assets/img/profile/admin.png', 'admin', '2025-06-03 09:06:17', '2025-06-03 09:06:17', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table blog-cms-laravel.web_settings: ~23 rows (approximately)
INSERT INTO `web_settings` (`id`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
	(1, 'web_name', 'My Blog', 'string', '2025-06-03 09:06:13', '2025-06-03 09:06:13'),
	(2, 'web_name_variant', '3', 'integer', '2025-06-03 09:06:13', '2025-06-03 09:06:13'),
	(3, 'tagline', 'My Blog Tagline Here', 'string', '2025-06-03 09:06:13', '2025-06-03 09:06:13'),
	(4, 'description', 'My Blog Description Here for SEO', 'string', '2025-06-03 09:06:13', '2025-06-03 09:06:13'),
	(5, 'keywords', 'My Blog, keywords, Laravel, blog, zakialawi, zakialawi.my.id, zakialawi.com', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(6, 'app_logo', 'app_logo.png', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(7, 'favicon', 'favicon.png', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(8, 'email', 'hallo@zakialawi.my.id', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(9, 'link_fb', '', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(10, 'link_tiktok', '', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(11, 'link_ig', '', 'string', '2025-06-03 09:06:14', '2025-06-03 09:06:14'),
	(12, 'link_twitter', '', 'string', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(13, 'link_youtube', '', 'string', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(14, 'link_linkedin', '', 'string', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(15, 'link_github', '', 'string', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(16, 'can_join_contributor', '1', 'boolean', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(17, 'home_feature_section', '{"label":"Featured Posts","is_visible":"1","total":6,"items":"random-posts"}', 'json', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(18, 'home_section_1', '{"label":"Recent Posts","is_visible":"1","total":6,"items":"recent-posts"}', 'json', '2025-06-03 09:06:15', '2025-06-03 09:06:15'),
	(19, 'home_section_2', '{"label":"","is_visible":false,"total":6,"items":""}', 'json', '2025-06-03 09:06:15', '2025-06-03 09:12:37'),
	(20, 'home_section_3', '{"label":"","is_visible":false,"total":3,"items":""}', 'json', '2025-06-03 09:06:16', '2025-06-03 09:12:37'),
	(21, 'home_sidebar_1', '{"label":"Popular Posts","is_visible":"1","total":4,"items":"popular-posts"}', 'json', '2025-06-03 09:06:16', '2025-06-03 09:06:16'),
	(22, 'home_sidebar_2', '{"label":"Tags","is_visible":"1","total":10,"items":"all-tags-widget"}', 'json', '2025-06-03 09:06:16', '2025-06-03 09:06:16'),
	(23, 'home_bottom_section_1', '{"label":"You Missed","is_visible":"1","total":4,"items":"random-posts"}', 'json', '2025-06-03 09:06:16', '2025-06-03 09:06:16');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
