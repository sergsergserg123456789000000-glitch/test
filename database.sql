-- ============================================
-- PROFESSIONAL SOFTWARE - Database Schema
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";

-- --------------------------------------------
-- USERS TABLE (Пользователи)
-- --------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `company` VARCHAR(255) DEFAULT NULL,
  `email_verified` TINYINT(1) DEFAULT 0,
  `verification_code` VARCHAR(6) DEFAULT NULL,
  `verification_code_expires` DATETIME DEFAULT NULL,
  `social_provider` ENUM('local', 'vk', 'ok', 'yandex', 'mail') DEFAULT 'local',
  `social_id` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_verification_code` (`verification_code`),
  KEY `idx_social` (`social_provider`, `social_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- ADMIN_USERS TABLE (Администраторы)
-- --------------------------------------------
CREATE TABLE `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `role` ENUM('super_admin', 'admin', 'editor', 'support') DEFAULT 'admin',
  `two_factor_secret` VARCHAR(255) DEFAULT NULL,
  `two_factor_enabled` TINYINT(1) DEFAULT 0,
  `last_login` DATETIME DEFAULT NULL,
  `last_ip` VARCHAR(45) DEFAULT NULL,
  `failed_login_attempts` INT DEFAULT 0,
  `locked_until` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- PRODUCTS TABLE (Продукты)
-- --------------------------------------------
CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `tagline` VARCHAR(500) DEFAULT NULL,
  `category` ENUM('Безопасность', 'Утилиты', 'Облако', 'Бизнес') NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `old_price` DECIMAL(10,2) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `features` JSON DEFAULT NULL,
  `os_support` JSON DEFAULT NULL,
  `requirements` JSON DEFAULT NULL,
  `rating` DECIMAL(2,1) DEFAULT 5.0,
  `reviews_count` INT DEFAULT 0,
  `badge` ENUM('Хит', 'Новинка', 'Скидка') DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_category` (`category`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- PRODUCT_VERSIONS TABLE (Версии продуктов)
-- --------------------------------------------
CREATE TABLE `product_versions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `version` VARCHAR(20) NOT NULL,
  `release_date` DATE NOT NULL,
  `file_size` VARCHAR(20) DEFAULT NULL,
  `download_url` VARCHAR(500) DEFAULT NULL,
  `file_hash` VARCHAR(64) DEFAULT NULL,
  `is_current` TINYINT(1) DEFAULT 0,
  `changelog` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_current` (`is_current`),
  CONSTRAINT `fk_pv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- LICENSES TABLE (Лицензии)
-- --------------------------------------------
CREATE TABLE `licenses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `license_key` VARCHAR(50) NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('active', 'expired', 'blocked', 'trial') DEFAULT 'active',
  `seats` INT DEFAULT 1,
  `activated_at` DATETIME DEFAULT NULL,
  `expires_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_license_key` (`license_key`),
  KEY `idx_user` (`user_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_licenses_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_licenses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- ORDERS TABLE (Заказы)
-- --------------------------------------------
CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(20) NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `customer_company` VARCHAR(255) DEFAULT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'paid', 'cancelled', 'refunded') DEFAULT 'pending',
  `payment_method` ENUM('card', 'sbp', 'invoice', 'crypto') DEFAULT NULL,
  `payment_id` VARCHAR(100) DEFAULT NULL,
  `paid_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_number` (`order_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- ORDER_ITEMS TABLE (Элементы заказа)
-- --------------------------------------------
CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `license_id` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_oi_license` FOREIGN KEY (`license_id`) REFERENCES `licenses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- BLOG_POSTS TABLE (Статьи блога)
-- --------------------------------------------
CREATE TABLE `blog_posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(150) NOT NULL,
  `title` VARCHAR(500) NOT NULL,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `category` VARCHAR(50) DEFAULT NULL,
  `author_id` INT UNSIGNED DEFAULT NULL,
  `author_name` VARCHAR(100) DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` VARCHAR(500) DEFAULT NULL,
  `tags` JSON DEFAULT NULL,
  `is_published` TINYINT(1) DEFAULT 0,
  `published_at` DATETIME DEFAULT NULL,
  `views_count` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_published` (`is_published`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- SUPPORT_TICKETS TABLE (Тикеты поддержки)
-- --------------------------------------------
CREATE TABLE `support_tickets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` VARCHAR(20) NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `subject` VARCHAR(500) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('open', 'in_progress', 'closed', 'pending') DEFAULT 'open',
  `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
  `assigned_to` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `closed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ticket_number` (`ticket_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- CONTACT_FORMS TABLE (Форма контактов)
-- --------------------------------------------
CREATE TABLE `contact_forms` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `company` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(500) NOT NULL,
  `message` TEXT NOT NULL,
  `is_processed` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_processed` (`is_processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- CALLBACK_REQUESTS TABLE (Обратный звонок)
-- --------------------------------------------
CREATE TABLE `callback_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `is_called` TINYINT(1) DEFAULT 0,
  `called_at` DATETIME DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_called` (`is_called`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- EMAIL_LOGS TABLE (Логи email)
-- --------------------------------------------
CREATE TABLE `email_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipient` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(500) NOT NULL,
  `template` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
  `error_message` TEXT DEFAULT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipient` (`recipient`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- ADMIN_ACTIVITY_LOG TABLE (Лог действий админов)
-- --------------------------------------------
CREATE TABLE `admin_activity_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `table_name` VARCHAR(50) DEFAULT NULL,
  `record_id` INT UNSIGNED DEFAULT NULL,
  `old_values` JSON DEFAULT NULL,
  `new_values` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- SITE_SETTINGS TABLE (Настройки сайта)
-- --------------------------------------------
CREATE TABLE `site_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `setting_type` ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
  `description` VARCHAR(255) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- DEFAULT DATA
-- --------------------------------------------

-- Default admin user (password: admin123)
INSERT INTO `admin_users` (`email`, `password_hash`, `name`, `role`) VALUES
('admin@mastersoftware.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', 'super_admin');

-- Default site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'PROFESSIONAL SOFTWARE', 'text', 'Название сайта'),
('site_email', 'info@mastersoftware.ru', 'text', 'Email сайта'),
('site_phone', '+7 (812) 945-31-43', 'text', 'Телефон'),
('site_logo_path', 'images/Logo-Master-Software.ico', 'text', 'Логотип сайта'),
('site_favicon_path', 'images/Logo-Master-Software.ico', 'text', 'Фавикон сайта'),
('oauth_vk_client_id', '', 'text', 'VK Client ID'),
('oauth_vk_client_secret', '', 'text', 'VK Client Secret'),
('oauth_ok_client_id', '', 'text', 'OK Client ID'),
('oauth_ok_client_secret', '', 'text', 'OK Client Secret'),
('oauth_yandex_client_id', '', 'text', 'Yandex Client ID'),
('oauth_yandex_client_secret', '', 'text', 'Yandex Client Secret'),
('oauth_mail_client_id', '', 'text', 'Mail.ru Client ID'),
('oauth_mail_client_secret', '', 'text', 'Mail.ru Client Secret'),
('verification_code_expiry', '30', 'number', 'Время жизни кода подтверждения (минут)'),
('max_login_attempts', '5', 'number', 'Максимум попыток входа'),
('lockout_duration', '30', 'number', 'Длительность блокировки (минут)');

-- Sample products
INSERT INTO `products` (`slug`, `name`, `tagline`, `category`, `price`, `description`, `os_support`, `rating`, `badge`) VALUES
('nimbus-guard-pro', 'NimbusGuard Pro', 'Антивирус нового поколения с AI-движком', 'Безопасность', 2490.00, 'Комплексное решение для защиты рабочих станций и серверов.', '["Windows", "macOS", "Linux"]', 4.9, 'Хит'),
('nimbus-clean-utility', 'NimbusClean Utility', 'Оптимизация и очистка системы', 'Утилиты', 1290.00, 'Безопасная очистка системного мусора и оптимизация.', '["Windows", "macOS"]', 4.7, NULL),
('nimbus-vault-cloud', 'NimbusVault Cloud', 'Защищённое облачное хранилище', 'Облако', 990.00, 'Корпоративное облако с end-to-end шифрованием.', '["Windows", "macOS", "Linux", "Android", "iOS"]', 4.8, 'Новинка');

COMMIT;
