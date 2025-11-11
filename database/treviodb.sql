-- ============================================
-- Trevio Database Schema
-- Similar to Traveloka with Xendit Integration
-- Author: @hexfjr
-- Date: 2025-11-11
-- Database: MariaDB 10.6+
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- ============================================
-- 1. ROLE & PERMISSION MANAGEMENT
-- ============================================

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `level` tinyint(3) unsigned NOT NULL COMMENT '1=guest, 2=user, 3=admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`),
  UNIQUE KEY `unique_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL COMMENT 'booking, user, payment, admin',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`),
  UNIQUE KEY `unique_slug` (`slug`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  KEY `fk_role_permissions_permission` (`permission_id`),
  CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. USER MANAGEMENT
-- ============================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT 2 COMMENT 'Default: user role',
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','suspended','inactive') NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_phone` (`phone`),
  KEY `idx_role_status` (`role_id`,`status`),
  KEY `idx_email_status` (`email`,`status`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token` (`token_hash`),
  KEY `idx_user_active` (`user_id`,`expires_at`),
  CONSTRAINT `fk_user_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `guest_sessions`;
CREATE TABLE `guest_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `cart_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cart_data`)),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session` (`session_id`),
  KEY `idx_session_expiry` (`session_id`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `label` varchar(50) NOT NULL,
  `address_line` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'ID',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_user_addresses_user` (`user_id`),
  CONSTRAINT `fk_user_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_passengers`;
CREATE TABLE `user_passengers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` enum('Mr','Mrs','Ms') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `id_type` enum('ktp','passport','sim') NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `nationality` varchar(2) NOT NULL DEFAULT 'ID',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_user_passengers_user` (`user_id`),
  CONSTRAINT `fk_user_passengers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. ADMIN MANAGEMENT
-- ============================================

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT 3 COMMENT 'Admin role',
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `admin_level` enum('super_admin','admin','support','finance','marketing') NOT NULL,
  `permissions_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions_override`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`),
  KEY `idx_role_active` (`role_id`,`is_active`),
  CONSTRAINT `fk_admins_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_sessions`;
CREATE TABLE `admin_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token` (`token_hash`),
  KEY `idx_admin_active` (`admin_id`,`expires_at`),
  CONSTRAINT `fk_admin_sessions_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. PRODUCT MANAGEMENT - FLIGHTS
-- ============================================

DROP TABLE IF EXISTS `airlines`;
CREATE TABLE `airlines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `country` varchar(2) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by_admin_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`code`),
  KEY `fk_airlines_admin` (`created_by_admin_id`),
  CONSTRAINT `fk_airlines_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `airports`;
CREATE TABLE `airports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL COMMENT 'IATA code',
  `name` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(2) NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `flights`;
CREATE TABLE `flights` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `airline_id` int(10) unsigned NOT NULL,
  `flight_number` varchar(10) NOT NULL,
  `departure_airport_id` int(10) unsigned NOT NULL,
  `arrival_airport_id` int(10) unsigned NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `duration_minutes` int(10) unsigned NOT NULL,
  `aircraft_type` varchar(50) DEFAULT NULL,
  `available_seats` int(10) unsigned NOT NULL,
  `total_seats` int(10) unsigned NOT NULL,
  `base_price` decimal(12,2) NOT NULL,
  `class` enum('economy','premium_economy','business','first') NOT NULL,
  `status` enum('scheduled','delayed','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `created_by_admin_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_search` (`departure_airport_id`,`arrival_airport_id`,`departure_time`,`status`),
  KEY `idx_flight_number` (`airline_id`,`flight_number`),
  KEY `fk_flights_arrival_airport` (`arrival_airport_id`),
  KEY `fk_flights_admin` (`created_by_admin_id`),
  CONSTRAINT `fk_flights_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_flights_airline` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_flights_arrival_airport` FOREIGN KEY (`arrival_airport_id`) REFERENCES `airports` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_flights_departure_airport` FOREIGN KEY (`departure_airport_id`) REFERENCES `airports` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. PRODUCT MANAGEMENT - HOTELS
-- ============================================

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'ID',
  `postal_code` varchar(10) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `star_rating` tinyint(3) unsigned DEFAULT NULL COMMENT '1-5 stars',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `check_in_time` time NOT NULL DEFAULT '14:00:00',
  `check_out_time` time NOT NULL DEFAULT '12:00:00',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `facilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`facilities`)),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by_admin_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug` (`slug`),
  KEY `idx_location` (`city`,`status`),
  KEY `fk_hotels_admin` (`created_by_admin_id`),
  CONSTRAINT `fk_hotels_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hotel_rooms`;
CREATE TABLE `hotel_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_id` bigint(20) unsigned NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `max_occupancy` tinyint(3) unsigned NOT NULL,
  `total_rooms` int(10) unsigned NOT NULL,
  `bed_type` varchar(50) DEFAULT NULL,
  `size_sqm` int(10) unsigned DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `facilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`facilities`)),
  `base_price_per_night` decimal(12,2) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_hotel_rooms_hotel` (`hotel_id`),
  CONSTRAINT `fk_hotel_rooms_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hotel_room_availability`;
CREATE TABLE `hotel_room_availability` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `available_rooms` int(10) unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL COMMENT 'Dynamic pricing',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_room_date` (`room_id`,`date`),
  KEY `idx_date` (`date`),
  CONSTRAINT `fk_hotel_room_availability_room` FOREIGN KEY (`room_id`) REFERENCES `hotel_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. BOOKING MANAGEMENT
-- ============================================

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(20) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Nullable for guest checkout',
  `guest_session_id` bigint(20) unsigned DEFAULT NULL,
  `booking_type` enum('flight','hotel','train','car','activity') NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `status` enum('pending','confirmed','cancelled','completed','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded','partial_refund') NOT NULL DEFAULT 'unpaid',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` timestamp NOT NULL COMMENT 'Payment deadline',
  `notes` text DEFAULT NULL,
  `cancelled_by` enum('user','admin','system') DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_booking_code` (`booking_code`),
  KEY `idx_user_status` (`user_id`,`status`),
  KEY `idx_status` (`status`,`payment_status`),
  KEY `fk_bookings_guest_session` (`guest_session_id`),
  CONSTRAINT `fk_bookings_guest_session` FOREIGN KEY (`guest_session_id`) REFERENCES `guest_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `flight_bookings`;
CREATE TABLE `flight_bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `flight_id` bigint(20) unsigned NOT NULL,
  `trip_type` enum('one_way','round_trip') NOT NULL,
  `return_flight_id` bigint(20) unsigned DEFAULT NULL,
  `passenger_count` tinyint(3) unsigned NOT NULL,
  `baggage_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`baggage_info`)),
  `seat_selection` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`seat_selection`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_flight_bookings_booking` (`booking_id`),
  KEY `fk_flight_bookings_flight` (`flight_id`),
  KEY `fk_flight_bookings_return_flight` (`return_flight_id`),
  CONSTRAINT `fk_flight_bookings_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_flight_bookings_flight` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_flight_bookings_return_flight` FOREIGN KEY (`return_flight_id`) REFERENCES `flights` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `flight_passengers`;
CREATE TABLE `flight_passengers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flight_booking_id` bigint(20) unsigned NOT NULL,
  `title` enum('Mr','Mrs','Ms') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `id_type` enum('ktp','passport','sim') NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `nationality` varchar(2) NOT NULL,
  `seat_number` varchar(10) DEFAULT NULL,
  `baggage_weight` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'in KG',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_flight_passengers_flight_booking` (`flight_booking_id`),
  CONSTRAINT `fk_flight_passengers_flight_booking` FOREIGN KEY (`flight_booking_id`) REFERENCES `flight_bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hotel_bookings`;
CREATE TABLE `hotel_bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `hotel_id` bigint(20) unsigned NOT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `nights` tinyint(3) unsigned NOT NULL,
  `rooms_count` tinyint(3) unsigned NOT NULL,
  `guest_count` tinyint(3) unsigned NOT NULL,
  `special_request` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dates` (`check_in_date`,`check_out_date`),
  KEY `fk_hotel_bookings_booking` (`booking_id`),
  KEY `fk_hotel_bookings_hotel` (`hotel_id`),
  KEY `fk_hotel_bookings_room` (`room_id`),
  CONSTRAINT `fk_hotel_bookings_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hotel_bookings_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_hotel_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `hotel_rooms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hotel_booking_guests`;
CREATE TABLE `hotel_booking_guests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_booking_id` bigint(20) unsigned NOT NULL,
  `room_number` tinyint(3) unsigned NOT NULL COMMENT 'Which room (1,2,3 if multiple)',
  `title` enum('Mr','Mrs','Ms') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_hotel_booking_guests_hotel_booking` (`hotel_booking_id`),
  CONSTRAINT `fk_hotel_booking_guests_hotel_booking` FOREIGN KEY (`hotel_booking_id`) REFERENCES `hotel_bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. PAYMENT & TRANSACTIONS (XENDIT)
-- ============================================

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `payment_code` varchar(50) NOT NULL,
  `payment_method` enum('credit_card','bank_transfer','e_wallet','qris','retail') NOT NULL,
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'e.g. BCA, Gopay, OVO',
  `amount` decimal(12,2) NOT NULL,
  `admin_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `status` enum('pending','processing','completed','failed','expired') NOT NULL DEFAULT 'pending',
  `xendit_invoice_id` varchar(100) DEFAULT NULL,
  `xendit_external_id` varchar(100) NOT NULL,
  `xendit_payment_url` text DEFAULT NULL,
  `xendit_callback_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`xendit_callback_data`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_payment_code` (`payment_code`),
  UNIQUE KEY `unique_xendit_invoice` (`xendit_invoice_id`),
  UNIQUE KEY `unique_xendit_external` (`xendit_external_id`),
  KEY `idx_booking_status` (`booking_id`,`status`),
  CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payment_channels`;
CREATE TABLE `payment_channels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('bank_transfer','e_wallet','credit_card','qris','retail') NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `admin_fee_type` enum('fixed','percentage') NOT NULL,
  `admin_fee_value` decimal(12,2) NOT NULL,
  `min_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_amount` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `refunds`;
CREATE TABLE `refunds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `payment_id` bigint(20) unsigned NOT NULL,
  `refund_code` varchar(50) NOT NULL,
  `refund_amount` decimal(12,2) NOT NULL,
  `refund_reason` enum('cancellation','overpayment','service_issue','other') NOT NULL,
  `reason_detail` text DEFAULT NULL,
  `admin_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_refund` decimal(12,2) NOT NULL,
  `status` enum('pending','approved','processing','completed','rejected') NOT NULL DEFAULT 'pending',
  `xendit_refund_id` varchar(100) DEFAULT NULL,
  `approved_by_admin_id` int(10) unsigned DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_refund_code` (`refund_code`),
  UNIQUE KEY `unique_xendit_refund` (`xendit_refund_id`),
  KEY `fk_refunds_booking` (`booking_id`),
  KEY `fk_refunds_payment` (`payment_id`),
  KEY `fk_refunds_admin` (`approved_by_admin_id`),
  CONSTRAINT `fk_refunds_admin` FOREIGN KEY (`approved_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_refunds_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_refunds_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. PROMO & VOUCHER
-- ============================================

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE `vouchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percentage','fixed_amount') NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `max_discount` decimal(12,2) DEFAULT NULL COMMENT 'For percentage type',
  `min_transaction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `applicable_to` enum('all','flight','hotel','train','car','activity') NOT NULL DEFAULT 'all',
  `user_specific` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'For targeted promos',
  `usage_limit` int(10) unsigned DEFAULT NULL COMMENT 'Total usage allowed',
  `usage_per_user` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `valid_from` timestamp NOT NULL,
  `valid_until` timestamp NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_admin_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`code`),
  KEY `idx_code_active` (`code`,`is_active`),
  KEY `idx_validity` (`valid_from`,`valid_until`,`is_active`),
  KEY `fk_vouchers_admin` (`created_by_admin_id`),
  CONSTRAINT `fk_vouchers_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `voucher_usage`;
CREATE TABLE `voucher_usage` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `voucher_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Nullable for guest usage',
  `booking_id` bigint(20) unsigned NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_voucher` (`user_id`,`voucher_id`),
  KEY `fk_voucher_usage_voucher` (`voucher_id`),
  KEY `fk_voucher_usage_booking` (`booking_id`),
  CONSTRAINT `fk_voucher_usage_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_voucher_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_voucher_usage_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. REVIEWS & RATINGS
-- ============================================

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `booking_id` bigint(20) unsigned NOT NULL,
  `reviewable_type` enum('flight','hotel','train','car','activity') NOT NULL,
  `reviewable_id` bigint(20) unsigned NOT NULL COMMENT 'Polymorphic relation',
  `rating` tinyint(3) unsigned NOT NULL COMMENT '1-5',
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `pros` text DEFAULT NULL,
  `cons` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_verified` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'From actual booking',
  `helpful_count` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `moderated_by_admin_id` int(10) unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reviewable` (`reviewable_type`,`reviewable_id`,`status`),
  KEY `idx_user` (`user_id`),
  KEY `fk_reviews_booking` (`booking_id`),
  KEY `fk_reviews_admin` (`moderated_by_admin_id`),
  CONSTRAINT `fk_reviews_admin` FOREIGN KEY (`moderated_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `review_responses`;
CREATE TABLE `review_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint(20) unsigned NOT NULL,
  `responder_type` enum('vendor','admin') NOT NULL,
  `responder_id` bigint(20) unsigned NOT NULL COMMENT 'FK to admins.id if type=admin',
  `response` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_review_responses_review` (`review_id`),
  CONSTRAINT `fk_review_responses_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. NOTIFICATION SYSTEM
-- ============================================

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('booking_confirmation','payment_reminder','payment_success','booking_cancelled','promo','review_request') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `channel` enum('in_app','email','sms','push') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`,`is_read`,`created_at`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `email_logs`;
CREATE TABLE `email_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `template` varchar(100) NOT NULL,
  `status` enum('queued','sent','failed') NOT NULL DEFAULT 'queued',
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`,`created_at`),
  KEY `fk_email_logs_user` (`user_id`),
  CONSTRAINT `fk_email_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. CONFIGURATION & AUDIT
-- ============================================

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `type` enum('string','number','boolean','json') NOT NULL,
  `description` text DEFAULT NULL,
  `updated_by_admin_id` int(10) unsigned DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`key`),
  KEY `fk_system_settings_admin` (`updated_by_admin_id`),
  CONSTRAINT `fk_system_settings_admin` FOREIGN KEY (`updated_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_type` enum('guest','user','admin') NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL COMMENT 'NULL for guest',
  `session_id` varchar(100) DEFAULT NULL COMMENT 'guest_session_id or user_session token',
  `action` varchar(100) NOT NULL COMMENT 'e.g. booking.created, payment.completed',
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_actor` (`actor_type`,`actor_id`,`created_at`),
  KEY `idx_action` (`action`,`created_at`),
  KEY `idx_session` (`session_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. SEED DATA - ROLES & PERMISSIONS
-- ============================================

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `level`) VALUES
(1, 'Guest', 'guest', 'Anonymous user with limited access', 1),
(2, 'User', 'user', 'Registered user with full booking capabilities', 2),
(3, 'Admin', 'admin', 'Administrator with system management access', 3);

INSERT INTO `permissions` (`name`, `slug`, `category`, `description`) VALUES
-- Booking permissions
('Create Booking', 'booking.create', 'booking', 'Create new booking'),
('View Own Bookings', 'booking.view_own', 'booking', 'View own bookings'),
('View All Bookings', 'booking.view_all', 'booking', 'View all bookings'),
('Cancel Own Booking', 'booking.cancel_own', 'booking', 'Cancel own booking'),
('Cancel Any Booking', 'booking.cancel_any', 'booking', 'Cancel any booking'),

-- User permissions
('Update Own Profile', 'user.update_own', 'user', 'Update own profile'),
('View All Users', 'user.view_all', 'user', 'View all users'),
('Manage Users', 'user.manage', 'user', 'Full user management'),

-- Payment permissions
('View Own Payments', 'payment.view_own', 'payment', 'View own payments'),
('View All Payments', 'payment.view_all', 'payment', 'View all payments'),
('Process Refund', 'payment.process_refund', 'payment', 'Process refunds'),

-- Admin permissions
('Access Dashboard', 'admin.access_dashboard', 'admin', 'Access admin panel'),
('Manage Products', 'admin.manage_products', 'admin', 'Manage flights, hotels, etc'),
('System Configuration', 'admin.system_config', 'admin', 'System configuration'),
('View Activity Logs', 'admin.view_logs', 'admin', 'View activity logs');

-- Guest role permissions (minimal)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT 1, id FROM `permissions` WHERE `slug` = 'booking.create';

-- User role permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT 2, id FROM `permissions` WHERE `slug` IN (
  'booking.create',
  'booking.view_own',
  'booking.cancel_own',
  'user.update_own',
  'payment.view_own'
);

-- Admin role permissions (all)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT 3, id FROM `permissions`;

-- ============================================
-- 13. SEED DATA - SAMPLE PAYMENT CHANNELS
-- ============================================

INSERT INTO `payment_channels` (`code`, `name`, `type`, `admin_fee_type`, `admin_fee_value`, `min_amount`, `max_amount`, `is_active`, `display_order`) VALUES
('BCA_VA', 'BCA Virtual Account', 'bank_transfer', 'fixed', 4000.00, 10000.00, 50000000.00, 1, 1),
('MANDIRI_VA', 'Mandiri Virtual Account', 'bank_transfer', 'fixed', 4000.00, 10000.00, 50000000.00, 1, 2),
('BNI_VA', 'BNI Virtual Account', 'bank_transfer', 'fixed', 4000.00, 10000.00, 50000000.00, 1, 3),
('BRI_VA', 'BRI Virtual Account', 'bank_transfer', 'fixed', 4000.00, 10000.00, 50000000.00, 1, 4),
('PERMATA_VA', 'Permata Virtual Account', 'bank_transfer', 'fixed', 4000.00, 10000.00, 50000000.00, 1, 5),
('GOPAY', 'GoPay', 'e_wallet', 'percentage', 2.00, 1000.00, 2000000.00, 1, 6),
('OVO', 'OVO', 'e_wallet', 'percentage', 2.00, 10000.00, 10000000.00, 1, 7),
('DANA', 'DANA', 'e_wallet', 'percentage', 2.00, 1000.00, 10000000.00, 1, 8),
('LINKAJA', 'LinkAja', 'e_wallet', 'percentage', 2.00, 10000.00, 10000000.00, 1, 9),
('SHOPEEPAY', 'ShopeePay', 'e_wallet', 'percentage', 2.00, 1000.00, 10000000.00, 1, 10),
('QRIS', 'QRIS', 'qris', 'percentage', 0.70, 1000.00, 10000000.00, 1, 11),
('CREDIT_CARD', 'Credit Card', 'credit_card', 'percentage', 2.90, 10000.00, NULL, 1, 12),
('ALFAMART', 'Alfamart', 'retail', 'fixed', 5000.00, 10000.00, 5000000.00, 1, 13),
('INDOMARET', 'Indomaret', 'retail', 'fixed', 5000.00, 10000.00, 5000000.00, 1, 14);

-- ============================================
-- 14. SEED DATA - SAMPLE AIRPORTS
-- ============================================

INSERT INTO `airports` (`code`, `name`, `city`, `country`, `timezone`, `latitude`, `longitude`) VALUES
('CGK', 'Soekarno-Hatta International Airport', 'Jakarta', 'ID', 'Asia/Jakarta', -6.12555600, 106.65596600),
('SUB', 'Juanda International Airport', 'Surabaya', 'ID', 'Asia/Jakarta', -7.37983300, 112.78711100),
('DPS', 'Ngurah Rai International Airport', 'Denpasar', 'ID', 'Asia/Makassar', -8.74817200, 115.16717200),
('JOG', 'Adisucipto International Airport', 'Yogyakarta', 'ID', 'Asia/Jakarta', -7.78823800, 110.43169000),
('UPG', 'Sultan Hasanuddin International Airport', 'Makassar', 'ID', 'Asia/Makassar', -5.06162500, 119.55412500),
('KNO', 'Kualanamu International Airport', 'Medan', 'ID', 'Asia/Jakarta', 3.64221900, 98.88517600),
('BDO', 'Husein Sastranegara International Airport', 'Bandung', 'ID', 'Asia/Jakarta', -6.90062800, 107.57645800),
('PLM', 'Sultan Mahmud Badaruddin II International Airport', 'Palembang', 'ID', 'Asia/Jakarta', -2.89822500, 104.69989700),
('BTH', 'Hang Nadim International Airport', 'Batam', 'ID', 'Asia/Jakarta', 1.12103800, 104.11892200),
('SOC', 'Adisumarmo International Airport', 'Solo', 'ID', 'Asia/Jakarta', -7.51608300, 110.75690800);

-- ============================================
-- 15. SEED DATA - SAMPLE AIRLINES
-- ============================================

INSERT INTO `airlines` (`code`, `name`, `country`, `status`) VALUES
('GA', 'Garuda Indonesia', 'ID', 'active'),
('QZ', 'AirAsia Indonesia', 'ID', 'active'),
('ID', 'Batik Air', 'ID', 'active'),
('JT', 'Lion Air', 'ID', 'active'),
('IU', 'Super Air Jet', 'ID', 'active'),
('QG', 'Citilink', 'ID', 'active'),
('SJ', 'Sriwijaya Air', 'ID', 'active'),
('IN', 'Nam Air', 'ID', 'active');

-- ============================================
-- 16. SAMPLE ADMIN USER
-- ============================================

-- Password: Admin123!@# (hashed with bcrypt cost 12)
INSERT INTO `admins` (`role_id`, `username`, `email`, `password_hash`, `full_name`, `admin_level`, `is_active`) VALUES
(3, 'superadmin', 'admin@traveloka-clone.com', '$2y$12$LQv3c1yYqC.Qj5WZ5qgZ8.EqXq4K9xN4fVGK7LhH.Pz2K9xN4fVGK7', 'Super Administrator', 'super_admin', 1);

-- ============================================
-- FINISH
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- NOTES FOR DEPLOYMENT
-- ============================================
-- 1. Update admin password immediately after first login
-- 2. Configure Xendit API keys in system_settings table
-- 3. Set up cron jobs for:
--    - Expired bookings cleanup
--    - Guest sessions cleanup
--    - Email queue processing
--    - Payment status sync with Xendit
-- 4. Enable slow query log for performance monitoring
-- 5. Set up daily backups with retention policy
-- 6. Configure Redis for session management (production)
-- 7. Set up monitoring for failed payments and refunds
-- ============================================