-- ============================================================
-- WhatsAppBizAI — MySQL Schema Dump
-- Version : 1.0 | Compatible MySQL 5.7+ / MariaDB 10.3+
-- Usage   : Importer dans phpMyAdmin APRÈS avoir créé la DB
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- 1. personal_access_tokens (Laravel Sanctum)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id`             bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id`   bigint(20) UNSIGNED NOT NULL,
  `name`           varchar(255) NOT NULL,
  `token`          varchar(64) NOT NULL,
  `abilities`      text DEFAULT NULL,
  `last_used_at`   timestamp NULL DEFAULT NULL,
  `expires_at`     timestamp NULL DEFAULT NULL,
  `created_at`     timestamp NULL DEFAULT NULL,
  `updated_at`     timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. businesses
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `businesses` (
  `id`                           bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                         varchar(255) NOT NULL,
  `owner_name`                   varchar(255) NOT NULL,
  `email`                        varchar(255) NOT NULL,
  `phone`                        varchar(255) DEFAULT NULL,
  `whatsapp_phone_number_id`     varchar(255) DEFAULT NULL,
  `whatsapp_access_token`        text DEFAULT NULL,
  `whatsapp_business_account_id` varchar(255) DEFAULT NULL,
  `gemini_system_prompt`         text DEFAULT NULL,
  `address`                      varchar(255) DEFAULT NULL,
  `city`                         varchar(255) DEFAULT NULL,
  `country`                      varchar(10) NOT NULL DEFAULT 'CM',
  `currency`                     varchar(10) NOT NULL DEFAULT 'XAF',
  `logo_path`                    varchar(255) DEFAULT NULL,
  `invoice_prefix`               varchar(20) NOT NULL DEFAULT 'FAC',
  `quote_prefix`                 varchar(20) NOT NULL DEFAULT 'DEV',
  `is_active`                    tinyint(1) NOT NULL DEFAULT 1,
  `plan`                         enum('free','starter','business','pro') NOT NULL DEFAULT 'free',
  `plan_expires_at`              timestamp NULL DEFAULT NULL,
  `timezone`                     varchar(255) NOT NULL DEFAULT 'Africa/Douala',
  `created_at`                   timestamp NULL DEFAULT NULL,
  `updated_at`                   timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `businesses_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`       bigint(20) UNSIGNED DEFAULT NULL,
  `name`              varchar(255) NOT NULL,
  `email`             varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password`          varchar(255) NOT NULL,
  `role`              enum('admin','agent') NOT NULL DEFAULT 'admin',
  `remember_token`    varchar(100) DEFAULT NULL,
  `created_at`        timestamp NULL DEFAULT NULL,
  `updated_at`        timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_business_id_foreign` (`business_id`),
  CONSTRAINT `users_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. password_reset_tokens
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email`      varchar(255) NOT NULL,
  `token`      varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. sessions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`            varchar(255) NOT NULL,
  `user_id`       bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address`    varchar(45) DEFAULT NULL,
  `user_agent`    text DEFAULT NULL,
  `payload`       longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. contacts
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
  `id`              bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`     bigint(20) UNSIGNED NOT NULL,
  `whatsapp_number` varchar(30) NOT NULL,
  `name`            varchar(255) DEFAULT NULL,
  `email`           varchar(255) DEFAULT NULL,
  `company`         varchar(255) DEFAULT NULL,
  `notes`           text DEFAULT NULL,
  `tags`            json DEFAULT NULL,
  `status`          enum('prospect','client','inactif') NOT NULL DEFAULT 'prospect',
  `last_seen_at`    timestamp NULL DEFAULT NULL,
  `total_invoiced`  decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_paid`      decimal(12,2) NOT NULL DEFAULT 0.00,
  `portal_token`    varchar(64) DEFAULT NULL,
  `created_at`      timestamp NULL DEFAULT NULL,
  `updated_at`      timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_business_id_whatsapp_number_unique` (`business_id`,`whatsapp_number`),
  UNIQUE KEY `contacts_portal_token_unique` (`portal_token`),
  KEY `contacts_whatsapp_number_index` (`whatsapp_number`),
  CONSTRAINT `contacts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. conversations
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `conversations` (
  `id`                  bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`         bigint(20) UNSIGNED NOT NULL,
  `contact_id`          bigint(20) UNSIGNED NOT NULL,
  `whatsapp_thread_id`  varchar(255) DEFAULT NULL,
  `status`              enum('open','closed','waiting') NOT NULL DEFAULT 'open',
  `channel`             varchar(20) NOT NULL DEFAULT 'whatsapp',
  `ai_enabled`          tinyint(1) NOT NULL DEFAULT 1,
  `summary`             text DEFAULT NULL,
  `last_message_at`     timestamp NULL DEFAULT NULL,
  `closed_at`           timestamp NULL DEFAULT NULL,
  `created_at`          timestamp NULL DEFAULT NULL,
  `updated_at`          timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_business_id_foreign` (`business_id`),
  KEY `conversations_contact_id_foreign` (`contact_id`),
  KEY `conversations_whatsapp_thread_id_index` (`whatsapp_thread_id`),
  KEY `conversations_status_index` (`status`),
  CONSTRAINT `conversations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. messages
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id`                   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id`      bigint(20) UNSIGNED NOT NULL,
  `whatsapp_message_id`  varchar(255) DEFAULT NULL,
  `direction`            enum('inbound','outbound') NOT NULL,
  `type`                 enum('text','image','document','template','audio','video') NOT NULL DEFAULT 'text',
  `content`              text DEFAULT NULL,
  `media_url`            varchar(255) DEFAULT NULL,
  `media_mime`           varchar(255) DEFAULT NULL,
  `status`               enum('pending','sent','delivered','read','failed') NOT NULL DEFAULT 'pending',
  `is_ai`                tinyint(1) NOT NULL DEFAULT 0,
  `tokens_used`          int(11) DEFAULT NULL,
  `error_message`        text DEFAULT NULL,
  `sent_at`              timestamp NULL DEFAULT NULL,
  `created_at`           timestamp NULL DEFAULT NULL,
  `updated_at`           timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `messages_whatsapp_message_id_unique` (`whatsapp_message_id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_direction_index` (`direction`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. services
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id`          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `name`        varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `unit_price`  decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency`    varchar(10) NOT NULL DEFAULT 'XAF',
  `unit`        varchar(30) NOT NULL DEFAULT 'forfait',
  `is_active`   tinyint(1) NOT NULL DEFAULT 1,
  `created_at`  timestamp NULL DEFAULT NULL,
  `updated_at`  timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_business_id_foreign` (`business_id`),
  CONSTRAINT `services_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. invoices
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `invoices` (
  `id`             bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`    bigint(20) UNSIGNED NOT NULL,
  `contact_id`     bigint(20) UNSIGNED NOT NULL,
  `number`         varchar(50) NOT NULL,
  `status`         enum('draft','sent','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `issue_date`     date NOT NULL,
  `due_date`       date NOT NULL,
  `subtotal`       decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_rate`       decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount`     decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount`       decimal(12,2) NOT NULL DEFAULT 0.00,
  `total`          decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount`    decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency`       varchar(10) NOT NULL DEFAULT 'XAF',
  `notes`          text DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `paid_at`        timestamp NULL DEFAULT NULL,
  `sent_at`        timestamp NULL DEFAULT NULL,
  `pdf_path`       varchar(255) DEFAULT NULL,
  `whatsapp_sent`  tinyint(1) NOT NULL DEFAULT 0,
  `created_at`     timestamp NULL DEFAULT NULL,
  `updated_at`     timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_number_unique` (`number`),
  KEY `invoices_business_id_foreign` (`business_id`),
  KEY `invoices_contact_id_foreign` (`contact_id`),
  KEY `invoices_status_index` (`status`),
  CONSTRAINT `invoices_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. invoice_items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id`          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`  bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity`    decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price`  decimal(12,2) NOT NULL DEFAULT 0.00,
  `total`       decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at`  timestamp NULL DEFAULT NULL,
  `updated_at`  timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 12. quotes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quotes` (
  `id`                      bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`             bigint(20) UNSIGNED NOT NULL,
  `contact_id`              bigint(20) UNSIGNED NOT NULL,
  `number`                  varchar(50) NOT NULL,
  `status`                  enum('draft','sent','accepted','declined','expired') NOT NULL DEFAULT 'draft',
  `issue_date`              date NOT NULL,
  `valid_until`             date NOT NULL,
  `subtotal`                decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_rate`                decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount`              decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount`                decimal(12,2) NOT NULL DEFAULT 0.00,
  `total`                   decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency`                varchar(10) NOT NULL DEFAULT 'XAF',
  `notes`                   text DEFAULT NULL,
  `pdf_path`                varchar(255) DEFAULT NULL,
  `whatsapp_sent`           tinyint(1) NOT NULL DEFAULT 0,
  `converted_to_invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at`              timestamp NULL DEFAULT NULL,
  `updated_at`              timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotes_number_unique` (`number`),
  KEY `quotes_business_id_foreign` (`business_id`),
  KEY `quotes_contact_id_foreign` (`contact_id`),
  KEY `quotes_status_index` (`status`),
  CONSTRAINT `quotes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotes_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 13. quote_items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quote_items` (
  `id`          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`    bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity`    decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price`  decimal(12,2) NOT NULL DEFAULT 0.00,
  `total`       decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at`  timestamp NULL DEFAULT NULL,
  `updated_at`  timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_items_quote_id_foreign` (`quote_id`),
  CONSTRAINT `quote_items_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 14. jobs (Laravel Queue)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`           bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        varchar(255) NOT NULL,
  `payload`      longtext NOT NULL,
  `attempts`     tinyint(3) UNSIGNED NOT NULL,
  `reserved_at`  int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at`   int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id`             varchar(255) NOT NULL,
  `name`           varchar(255) NOT NULL,
  `total_jobs`     int(11) NOT NULL,
  `pending_jobs`   int(11) NOT NULL,
  `failed_jobs`    int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options`        mediumtext DEFAULT NULL,
  `cancelled_at`   int(11) DEFAULT NULL,
  `created_at`     int(11) NOT NULL,
  `finished_at`    int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue`      text NOT NULL,
  `payload`    longtext NOT NULL,
  `exception`  longtext NOT NULL,
  `failed_at`  timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 15. activity_log (Spatie)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_name`      varchar(255) DEFAULT NULL,
  `description`   text NOT NULL,
  `subject_type`  varchar(255) DEFAULT NULL,
  `event`         varchar(255) DEFAULT NULL,
  `subject_id`    bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type`   varchar(255) DEFAULT NULL,
  `causer_id`     bigint(20) UNSIGNED DEFAULT NULL,
  `properties`    json DEFAULT NULL,
  `batch_uuid`    char(36) DEFAULT NULL,
  `created_at`    timestamp NULL DEFAULT NULL,
  `updated_at`    timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer`  (`causer_type`,`causer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 16. subscriptions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id`                   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`          bigint(20) UNSIGNED NOT NULL,
  `plan`                 enum('free','starter','business','pro') NOT NULL DEFAULT 'free',
  `status`               enum('active','expired','cancelled','pending') NOT NULL DEFAULT 'active',
  `billing_cycle`        enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `starts_at`            timestamp NULL DEFAULT NULL,
  `ends_at`              timestamp NULL DEFAULT NULL,
  `flutterwave_tx_ref`   varchar(255) DEFAULT NULL,
  `flutterwave_tx_id`    varchar(255) DEFAULT NULL,
  `amount_paid`          decimal(12,2) DEFAULT NULL,
  `currency`             varchar(10) NOT NULL DEFAULT 'XAF',
  `features`             json DEFAULT NULL,
  `created_at`           timestamp NULL DEFAULT NULL,
  `updated_at`           timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_business_id_foreign` (`business_id`),
  KEY `subscriptions_flutterwave_tx_ref_index` (`flutterwave_tx_ref`),
  CONSTRAINT `subscriptions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 17. payments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id`              bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id`     bigint(20) UNSIGNED NOT NULL,
  `method`          enum('flutterwave','mtn_momo','orange_money','wave','bank_transfer','other') NOT NULL,
  `status`          enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `plan`            enum('starter','business','pro') NOT NULL DEFAULT 'starter',
  `billing_cycle`   enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `amount`          decimal(12,2) NOT NULL,
  `currency`        varchar(10) NOT NULL DEFAULT 'XAF',
  `reference`       varchar(255) DEFAULT NULL,
  `phone_number`    varchar(255) DEFAULT NULL,
  `screenshot_path` varchar(255) DEFAULT NULL,
  `notes`           text DEFAULT NULL,
  `admin_notes`     text DEFAULT NULL,
  `verified_by`     bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at`     timestamp NULL DEFAULT NULL,
  `subscription_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at`      timestamp NULL DEFAULT NULL,
  `updated_at`      timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_business_id_foreign` (`business_id`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `payments_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `payments_business_id_foreign`     FOREIGN KEY (`business_id`)     REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_verified_by_foreign`     FOREIGN KEY (`verified_by`)     REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 18. cache + cache_locks
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key`        varchar(255) NOT NULL,
  `value`      mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key`        varchar(255) NOT NULL,
  `owner`      varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 19. migrations (table Laravel interne)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch`     int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Marquer les migrations comme exécutées
INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2024_01_01_000000_create_personal_access_tokens_table', 1),
  ('2024_01_01_000001_create_users_table', 1),
  ('2024_01_01_000002_create_businesses_table', 1),
  ('2024_01_01_000003_create_contacts_table', 1),
  ('2024_01_01_000004_create_conversations_messages_table', 1),
  ('2024_01_01_000005_create_services_quotes_table', 1),
  ('2024_01_01_000006_create_invoices_table', 1),
  ('2024_01_01_000007_create_jobs_table', 1),
  ('2024_01_01_000008_create_activity_log_table', 1),
  ('2024_01_01_000009_create_subscriptions_payments_table', 1),
  ('2024_01_01_000010_add_portal_token_and_plan_columns', 1);

-- ============================================================
-- FIN DU SCHEMA — NE PAS insérer de données ici.
-- Lancez : php artisan db:seed  (depuis SSH) pour les données démo.
-- ============================================================
