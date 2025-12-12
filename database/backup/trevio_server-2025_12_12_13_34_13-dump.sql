/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.5-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: trevio
-- ------------------------------------------------------
-- Server version	11.8.5-MariaDB-deb13-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `admin_activities`
--

DROP TABLE IF EXISTS `admin_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `admin_activities_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_activities`
--

LOCK TABLES `admin_activities` WRITE;
/*!40000 ALTER TABLE `admin_activities` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `admin_activities` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `num_nights` int(11) NOT NULL,
  `num_rooms` int(11) NOT NULL DEFAULT 1,
  `price_per_night` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `service_charge` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_phone` varchar(20) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `booking_status` enum('pending_payment','pending_verification','confirmed','checked_in','completed','cancelled','refunded') DEFAULT 'pending_payment',
  `checked_in_at` timestamp NULL DEFAULT NULL,
  `checked_in_by` int(11) DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_code` (`booking_code`),
  KEY `room_id` (`room_id`),
  KEY `checked_in_by` (`checked_in_by`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_hotel` (`hotel_id`),
  KEY `idx_status` (`booking_status`),
  KEY `idx_dates` (`check_in_date`,`check_out_date`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`),
  CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`checked_in_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `bookings` VALUES
(1,'BK2025112829648',17,2,3,'2025-11-28','2025-11-29',1,1,540000.00,540000.00,54000.00,27000.00,621000.00,'Hendrik','hen@gmail.com','083166666666',NULL,'cancelled',NULL,NULL,NULL,NULL,'2025-11-28 03:06:03','2025-11-29 20:54:07'),
(2,'BK2025112805049',3,2,3,'2025-11-28','2025-11-29',1,1,540000.00,540000.00,54000.00,27000.00,621000.00,'Customer Test','customer@trevio.com','083166666666',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-11-28 05:00:41','2025-11-29 20:38:43'),
(3,'BK2025112900866',3,2,4,'2025-11-29','2025-11-30',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Customer Test','customer@trevio.com','123',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-11-29 08:38:04','2025-11-29 20:38:28'),
(4,'BK2025113065440',3,2,4,'2025-11-30','2025-12-01',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Customer Test','astirasil@gmail.com','083139682650',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-11-29 21:15:32','2025-11-29 21:17:04'),
(5,'BK2025113023463',18,2,5,'2025-11-30','2025-12-01',1,1,1000000.00,1000000.00,100000.00,50000.00,1150000.00,'Hendrik Purwanto','hendrikpurwanto880@gmail.com','083139682650',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-11-29 21:48:30','2025-11-29 21:49:43'),
(6,'BK2025113042125',18,2,5,'2025-11-30','2025-12-01',1,1,1000000.00,1000000.00,100000.00,50000.00,1150000.00,'Hendrik Purwanto','hendrikpurwanto880@gmail.com','083139682650',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-11-29 23:00:27','2025-11-29 23:07:55'),
(7,'BK2025120146032',2,2,4,'2025-12-01','2025-12-02',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Owner Hotel','owner@trevio.com','085156064912',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-01 02:51:19','2025-12-01 02:51:19'),
(8,'BK2025120103739',14,2,4,'2025-12-01','2025-12-02',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Kylo','renotugascmt@gmail.com','085156064912',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-01 03:35:00','2025-12-01 03:35:00'),
(9,'BK2025120113184',14,2,4,'2025-12-01','2025-12-02',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Kylo','renotugascmt@gmail.com','085156064912',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-01 04:09:58','2025-12-01 04:09:58'),
(10,'BK2025120131535',14,2,4,'2025-12-01','2025-12-02',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Kylo','renotugascmt@gmail.com','085156064912',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-01 04:13:43','2025-12-01 04:13:43'),
(11,'BK2025120169435',18,3,6,'2025-12-01','2025-12-02',1,1,1536000.00,1536000.00,153600.00,76800.00,1766400.00,'Hendrik Purwanto','hendrikpurwanto880@gmail.com','083139682650',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-12-01 04:44:05','2025-12-01 04:44:57'),
(12,'BK2025120161239',18,3,7,'2025-12-01','2025-12-02',1,1,567000.00,567000.00,56700.00,28350.00,652050.00,'Hendrik Purwanto','hendrikpurwanto880@gmail.com','083139682650',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-12-01 09:48:31','2025-12-01 09:55:29'),
(13,'BK2025120295113',13,2,4,'2025-12-02','2025-12-03',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Zakaria','zakaria123@gmail.com','0881081772005',NULL,'pending_verification',NULL,NULL,NULL,NULL,'2025-12-01 10:18:12','2025-12-01 10:20:17'),
(14,'BK2025120217278',14,2,4,'2025-12-02','2025-12-03',1,1,300000.00,300000.00,30000.00,15000.00,345000.00,'Kylo','renotugascmt@gmail.com','085156064912',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-12-01 22:27:34','2025-12-01 22:28:46'),
(15,'BK2025120244665',14,3,6,'2025-12-09','2025-12-18',9,1,1536000.00,13824000.00,1382400.00,691200.00,15897600.00,'Kylo','renotugascmt@gmail.com','085156064912',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-12-01 22:30:09','2025-12-01 22:30:34'),
(16,'BK2025120272674',3,6,11,'2025-12-02','2025-12-03',1,1,1200000.00,1200000.00,120000.00,60000.00,1380000.00,'Customer Test','customer@trevio.com','0821398123',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-02 00:20:14','2025-12-02 00:20:14'),
(17,'BK2025120248907',13,6,11,'2025-12-02','2025-12-03',1,1,1200000.00,1200000.00,120000.00,60000.00,1380000.00,'Zakaria','zakaria123@gmail.com','0881081772005',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-02 05:00:16','2025-12-02 05:00:16'),
(18,'BK2025120619703',21,6,11,'2025-12-06','2025-12-07',1,1,1200000.00,1200000.00,120000.00,60000.00,1380000.00,'rezanur','reza@gmail.com','081453997071',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-06 04:36:43','2025-12-06 04:36:43'),
(19,'BK2025121025201',22,6,11,'2025-12-10','2025-12-11',1,1,1200000.00,1200000.00,120000.00,60000.00,1380000.00,'Sahrul','sahrul51@yahoo.co.id','081453997071',NULL,'pending_payment',NULL,NULL,NULL,NULL,'2025-12-10 12:38:39','2025-12-10 12:38:39'),
(20,'BK2025121112438',3,6,13,'2025-12-11','2025-12-12',1,1,100000.00,100000.00,10000.00,5000.00,115000.00,'Customer Test','customer@trevio.com','0191992983',NULL,'confirmed',NULL,NULL,NULL,NULL,'2025-12-11 09:42:48','2025-12-11 09:43:49');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50032 DROP TRIGGER IF EXISTS before_booking_insert */;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`trevio`@`%`*/ /*!50003 TRIGGER `before_booking_insert` BEFORE INSERT ON `bookings` FOR EACH ROW
BEGIN
    IF NEW.booking_code IS NULL OR NEW.booking_code = '' THEN
        SET NEW.booking_code = CONCAT(
            'BK',
            DATE_FORMAT(NOW(), '%Y%m%d'),
            LPAD(FLOOR(RAND() * 99999), 5, '0')
        );
    END IF;

    SET NEW.num_nights = DATEDIFF(NEW.check_out_date, NEW.check_in_date);
    SET NEW.subtotal = NEW.price_per_night * NEW.num_nights * NEW.num_rooms;
    SET NEW.tax_amount = NEW.subtotal * 0.10;
    SET NEW.service_charge = NEW.subtotal * 0.05;
    SET NEW.total_price = NEW.subtotal + NEW.tax_amount + NEW.service_charge;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50032 DROP TRIGGER IF EXISTS after_booking_confirmed */;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`trevio`@`%`*/ /*!50003 TRIGGER `after_booking_confirmed` AFTER UPDATE ON `bookings` FOR EACH ROW
BEGIN
    IF NEW.booking_status = 'confirmed' AND OLD.booking_status = 'pending_verification' THEN
        UPDATE rooms
        SET available_slots = available_slots - NEW.num_rooms
        WHERE id = NEW.room_id;
    END IF;

    -- Restore slots when cancelled/refunded
    IF (NEW.booking_status IN ('cancelled', 'refunded'))
       AND OLD.booking_status = 'confirmed' THEN
        UPDATE rooms
        SET available_slots = available_slots + NEW.num_rooms
        WHERE id = NEW.room_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `hotels`
--

DROP TABLE IF EXISTS `hotels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `star_rating` tinyint(4) DEFAULT NULL CHECK (`star_rating` between 1 and 5),
  `main_image` varchar(255) DEFAULT NULL,
  `facilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`facilities`)),
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_verified` tinyint(1) DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_owner` (`owner_id`),
  KEY `idx_city` (`city`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotels`
--

LOCK TABLES `hotels` WRITE;
/*!40000 ALTER TABLE `hotels` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `hotels` VALUES
(2,2,'Merlynn Park Hotel','At Merlynn Park Hotel Jakarta...','K.H. Hasyim Ashari 29-31, 10130 Jakarta','Jakarta','Indonesia',4,'/uploads/hotels/1764065262_553697302.jpg','[\"wifi\",\"pool\",\"gym\"]','+6283139682650','admin@hendprw.me',1,1,5.00,0,'2025-11-25 03:07:42','2025-12-01 04:29:07'),
(3,2,'Four Points by Sheraton Surabaya','Comfortable Accommodations...','Pakuwon Mall, Jalan Puncak Indah Lontar No 2','Surabaya','Indonesia',4,'/uploads/hotels/1764588131_465570701.jpg','[\"wifi\",\"pool\",\"parking\",\"ac\"]','08361612626','hotel@gmail.com',1,1,5.00,0,'2025-12-01 04:22:11','2025-12-01 04:29:07'),
(4,2,'Crowne Plaza Bandung','Strategically located...','Jl. Lembong No. 19','Bandung','Indonesia',4,'/uploads/hotels/1764588837_bdng.jpg','[\"wifi\",\"parking\",\"ac\"]','08361612626','hotel@gmail.com',1,1,5.00,0,'2025-12-01 04:33:57','2025-12-02 00:17:38'),
(5,2,'The Kemilau Ubud','Comfortable Accommodations...','Jalan Raya Pengosekan','Denpasar','Indonesia',4,'/uploads/hotels/1764589179_689387093.jpg','[\"wifi\",\"ac\"]','08361612626','hotel@gmail.com',1,1,6.00,0,'2025-12-01 04:39:39','2025-12-02 00:17:37'),
(6,2,'The Phoenix Hotel Yogyakarta','Located in the heart...','Jalan Jend. Sudirman No.9','Yogyakarta','Indonesia',4,'/uploads/hotels/1764589816_jogja.jpg','[\"wifi\",\"pool\",\"parking\",\"ac\"]','08361612626','hotel@gmail.com',1,1,7.00,0,'2025-12-01 04:50:16','2025-12-02 00:17:37'),
(7,2,'Wyndham Casablanca Jakarta','The Wyndham Casablanca...','Jalan Casablanca Kav 18','Jakarta','Indonesia',4,'/uploads/hotels/1764590054_760889753.jpg','[\"wifi\",\"pool\",\"gym\",\"bar\",\"parking\",\"ac\"]','08361612626','hotel@gmail.com',1,1,5.31,0,'2025-12-01 04:54:14','2025-12-02 00:19:19');
/*!40000 ALTER TABLE `hotels` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `send_email` tinyint(1) DEFAULT 1,
  `send_whatsapp` tinyint(1) DEFAULT 0,
  `email_sent` tinyint(1) DEFAULT 0,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `whatsapp_sent` tinyint(1) DEFAULT 0,
  `whatsapp_sent_at` timestamp NULL DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_read` (`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `payment_method` enum('bank_transfer','cash') DEFAULT 'bank_transfer',
  `transfer_amount` decimal(10,2) NOT NULL,
  `transfer_to_bank` varchar(100) DEFAULT NULL,
  `transfer_from_bank` varchar(100) DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `payment_status` enum('pending','uploaded','verified','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `verified_by` (`verified_by`),
  KEY `idx_booking` (`booking_id`),
  KEY `idx_status` (`payment_status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `payments` VALUES
(1,1,'bank_transfer',621000.00,NULL,'hai',NULL,'payment_1_1764324420_f702d752e9adb7ca.png','Sender: hau (08383838383)','rejected',1,'2025-11-29 20:54:07',NULL,'Bukti tidak valid/buram','2025-11-28 03:07:00','2025-11-29 20:54:07'),
(2,2,'bank_transfer',621000.00,NULL,'bni',NULL,'payment_2_1764331283_2efe447f30d3dfc7.jpg','Sender: hau (333333)','verified',1,'2025-11-29 20:38:43',NULL,NULL,'2025-11-28 05:01:23','2025-11-29 20:38:43'),
(3,3,'bank_transfer',345000.00,NULL,'jsjsj',NULL,'payment_3_1764430745_2486b80fb164c2ca.jpg','Sender: yanto (939330983)','verified',1,'2025-11-29 20:38:28',NULL,NULL,'2025-11-29 08:39:05','2025-11-29 20:38:28'),
(4,4,'bank_transfer',345000.00,NULL,'BNI',NULL,'payment_4_1764476154_6e6ae2c2d20c5662.png','Sender: Hendrik (19299272)','verified',1,'2025-11-29 21:17:04',NULL,NULL,'2025-11-29 21:15:54','2025-11-29 21:17:04'),
(5,5,'bank_transfer',1150000.00,NULL,'BNI',NULL,'payment_5_1764478128_585bb16263bbd385.png','Sender: Hendrik (000000000000)','verified',1,'2025-11-29 21:49:43',NULL,NULL,'2025-11-29 21:48:48','2025-11-29 21:49:43'),
(6,6,'bank_transfer',1150000.00,NULL,'BNI',NULL,'payment_6_1764482446_284d0cb91583f2b7.png','Sender: Hendrik (00000000)','verified',1,'2025-11-29 23:07:55',NULL,NULL,'2025-11-29 23:00:46','2025-11-29 23:07:55'),
(7,11,'bank_transfer',1766400.00,NULL,'BNI',NULL,'payment_11_1764589467.jpg','Sender: Hendrik (1254321432443)','verified',1,'2025-12-01 04:44:57',NULL,NULL,'2025-12-01 04:44:27','2025-12-01 04:44:57'),
(8,12,'bank_transfer',652050.00,NULL,'BNI',NULL,'payment_12_1764607955.png','Sender: Hen (083139682650)','verified',1,'2025-12-01 09:55:29',NULL,NULL,'2025-12-01 09:52:35','2025-12-01 09:55:29'),
(9,13,'bank_transfer',345000.00,NULL,'Bradley Burke',NULL,'payment_13_1764609617.png','Sender: Stella Davis (580)','uploaded',NULL,NULL,NULL,NULL,'2025-12-01 10:20:17','2025-12-01 10:20:17'),
(10,14,'bank_transfer',345000.00,NULL,'9235728743567245',NULL,'payment_14_1764653277.png','Sender: Reno','verified',1,'2025-12-01 22:28:46',NULL,NULL,'2025-12-01 22:27:57','2025-12-01 22:28:46'),
(11,15,'bank_transfer',15897600.00,NULL,'9235728743567245',NULL,'payment_15_1764653418.png','Sender: Reno','verified',1,'2025-12-01 22:30:34',NULL,NULL,'2025-12-01 22:30:18','2025-12-01 22:30:34'),
(12,20,'bank_transfer',115000.00,NULL,'djjd',NULL,'payment_20_1765446193.jpg','Sender: hdd','verified',1,'2025-12-11 09:43:49',NULL,NULL,'2025-12-11 09:43:13','2025-12-11 09:43:49');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50032 DROP TRIGGER IF EXISTS after_payment_verified */;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`trevio`@`%`*/ /*!50003 TRIGGER `after_payment_verified` AFTER UPDATE ON `payments` FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'verified' AND OLD.payment_status != 'verified' THEN
        UPDATE bookings
        SET booking_status = 'confirmed'
        WHERE id = NEW.booking_id;
    END IF;

    IF NEW.payment_status = 'rejected' AND OLD.payment_status != 'rejected' THEN
        UPDATE bookings
        SET booking_status = 'cancelled'
        WHERE id = NEW.booking_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` text NOT NULL,
  `customer_bank_name` varchar(100) NOT NULL,
  `customer_bank_account` varchar(50) NOT NULL,
  `customer_bank_holder` varchar(100) NOT NULL,
  `refund_status` enum('requested','approved','processing','completed','rejected') DEFAULT 'requested',
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `refund_receipt` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `customer_id` (`customer_id`),
  KEY `processed_by` (`processed_by`),
  KEY `idx_booking` (`booking_id`),
  KEY `idx_status` (`refund_status`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_4` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `review_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`review_images`)),
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `owner_response` text DEFAULT NULL,
  `owner_response_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_review` (`customer_id`,`booking_id`),
  KEY `booking_id` (`booking_id`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_hotel` (`hotel_id`),
  KEY `idx_approved` (`is_approved`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50032 DROP TRIGGER IF EXISTS after_review_approved */;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`trevio`@`%`*/ /*!50003 TRIGGER `after_review_approved` AFTER UPDATE ON `reviews` FOR EACH ROW
BEGIN
    DECLARE avg_rating DECIMAL(3, 2);
    DECLARE review_count INT;

    IF NEW.is_approved = TRUE AND OLD.is_approved = FALSE THEN
        SELECT AVG(rating), COUNT(*)
        INTO avg_rating, review_count
        FROM reviews
        WHERE hotel_id = NEW.hotel_id AND is_approved = TRUE;

        UPDATE hotels
        SET average_rating = avg_rating,
            total_reviews = review_count
        WHERE id = NEW.hotel_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 2,
  `bed_type` varchar(50) DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `total_slots` int(11) NOT NULL DEFAULT 10,
  `available_slots` int(11) NOT NULL DEFAULT 10,
  `room_size` int(11) DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `main_image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_hotel` (`hotel_id`),
  KEY `idx_available` (`is_available`),
  KEY `idx_slots` (`available_slots`),
  CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `rooms` VALUES
(3,2,'Deluxe','Tipe: deluxe. Ngetest',5,NULL,540000.00,105,100,NULL,'[\"ac\",\"wifi\"]','/uploads/rooms/1764320469_106201478.jpg',1,'2025-11-28 02:01:09','2025-12-01 02:50:28'),
(4,2,'Single room','Tipe: single. ',2,NULL,300000.00,1500,1363,NULL,'[\"ac\",\"tv\"]','/uploads/rooms/1764326888_553697302.jpg',1,'2025-11-28 03:48:08','2025-12-01 22:28:46'),
(5,2,'Twin Room','Tipe: twin. twin',4,NULL,1000000.00,10000,99996,NULL,'[\"wifi\",\"tv\",\"bathroom\"]','/uploads/rooms/1764478083_77676532.jpg',1,'2025-11-29 21:48:03','2025-12-01 02:29:19'),
(6,3,'Deluxe Room','Tipe: deluxe.',5,NULL,1536000.00,1000,996,NULL,'[\"ac\",\"wifi\",\"tv\",\"bathroom\",\"shower\"]','/uploads/rooms/1764588361_602803672.jpg',1,'2025-12-01 04:26:01','2025-12-01 22:30:34'),
(7,3,'SIngle Room','Tipe: single.',3,NULL,567000.00,2000,1998,NULL,'[\"ac\",\"wifi\",\"tv\",\"bathroom\"]','/uploads/rooms/1764588443_483862259.jpg',1,'2025-12-01 04:27:23','2025-12-01 09:55:29'),
(8,4,'Standard Twin Room','Tipe: twin.',2,NULL,139000.00,1000,1000,NULL,'[\"ac\",\"wifi\",\"tv\"]','/uploads/rooms/1764588915_659547989.jpg',1,'2025-12-01 04:35:15','2025-12-01 04:35:15'),
(9,4,'Premium Room','Tipe: deluxe.',2,NULL,2155000.00,1000,1000,NULL,'[\"ac\",\"wifi\",\"tv\",\"bathroom\"]','/uploads/rooms/1764589023_74567774.jpg',1,'2025-12-01 04:37:03','2025-12-01 04:37:03'),
(10,5,'Twin Room','Tipe: twin. 2 twin beds ',2,NULL,2000000.00,100,100,NULL,'[\"ac\",\"wifi\",\"tv\",\"bathroom\"]','/uploads/rooms/1764589266_689387093.jpg',1,'2025-12-01 04:41:06','2025-12-01 04:41:06'),
(11,6,'Standard Twin Room','Tipe: twin.',2,NULL,1200000.00,100,96,NULL,'[\"ac\",\"wifi\",\"tv\",\"bathroom\",\"shower\"]','/uploads/rooms/1764658302_1764589919_776698212.jpg',1,'2025-12-01 04:51:59','2025-12-10 12:38:39'),
(12,7,'Standard Family Roo','Tipe: family.',2,NULL,1250000.00,300,300,NULL,'[\"ac\",\"wifi\",\"tv\",\"bathroom\",\"shower\",\"balcony\",\"minibar\",\"safe\"]','/uploads/rooms/1764655323_1764654734_1764590139_760889753.jpg',1,'2025-12-01 04:55:39','2025-12-10 07:20:10'),
(13,6,'Suite','Tipe: suite. ',1,NULL,100000.00,10,8,NULL,'[\"ac\",\"wifi\",\"tv\",\"shower\"]','/uploads/rooms/1764655355_1764654850_1764607492_553697302.jpg',1,'2025-12-01 09:44:52','2025-12-11 09:43:49');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `auth_provider` enum('email','google') DEFAULT 'email',
  `google_id` varchar(100) DEFAULT NULL,
  `role` enum('customer','owner','admin') DEFAULT 'customer',
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `idx_email` (`email`),
  KEY `idx_google_id` (`google_id`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(1,'Admin Trevio','admin@trevio.com','$2a$12$tGDX2na2rKWKnvHYzI0jwu7FaeyPh4VERazzBQA.4aG.LVP4IqxsO','081234567890','6281234567890','email',NULL,'admin',1,1,NULL,'2025-11-24 06:13:01','2025-12-11 13:10:22'),
(2,'Owner Hotel','owner@trevio.com','$2a$12$tGDX2na2rKWKnvHYzI0jwu7FaeyPh4VERazzBQA.4aG.LVP4IqxsO','081234567891','6281234567891','email',NULL,'owner',1,1,NULL,'2025-11-24 06:13:01','2025-12-11 13:10:22'),
(3,'Customer Test','customer@trevio.com','$2a$12$tGDX2na2rKWKnvHYzI0jwu7FaeyPh4VERazzBQA.4aG.LVP4IqxsO','081234567892','6281234567892','email',NULL,'customer',1,1,NULL,'2025-11-24 06:13:01','2025-12-11 13:10:22'),
(4,'Hendrik','hendrik@gmail.com','$2a$10$iIG7xlsL37rT9R9Jih8Z.uoW/FNtHh3E9rJZwlazSOKe9Zvmawu36',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-24 06:20:55','2025-11-24 06:31:06'),
(5,'hendrik2','uwu@gmail.com','$2y$10$XJMGfDAXOFjX8CDCYlj.8OPtTzFgb/GDhqSDI64hMXhYJsobfFMwe',NULL,NULL,'email',NULL,'owner',1,1,NULL,'2025-11-24 07:23:40','2025-11-24 07:23:40'),
(11,'fajar','fajar@gmail.com','$2y$10$ZrixUtD7rMDFXv2/kzv3zu.VS55C5nHnpHt7gtN4zgP4jfCPobRui',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-24 07:49:20','2025-11-24 07:49:20'),
(12,'Syadat','maulanaaslih2@gmail.com','$2y$10$Ubt92zvQjVg41QCs6l1q4uTzt3qzOBQAob6k8UUwJcfwirGPXYKtG',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-24 07:55:05','2025-11-24 07:55:05'),
(13,'Zakaria','zakaria123@gmail.com','$2y$10$XYPaGKbxPDVRjOpUkqEM6e65X/fE0mXP3QZRnFgrzGVB6OoXdysTm',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-24 07:55:16','2025-11-24 07:55:16'),
(14,'Kylo','renotugascmt@gmail.com','$2y$10$j8CH70jsrR.SmXN3QkyUNOGSfwuyoFSL2mNfOPLVNkKn06mDJLeYC',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-24 07:55:18','2025-11-24 07:55:18'),
(15,'Kilo','kilo@gmail.com','$2y$10$/zPAnrOqvkLzmcUynWwdH.a/gHRlgLmcYs07OQqV.clUhJ38XE7UC',NULL,NULL,'email',NULL,'admin',1,1,NULL,'2025-11-24 07:57:12','2025-11-29 22:15:28'),
(16,'syadat','dat@gmail.com','$2y$10$s6i5j/0u0WlZ03InepAJVeJilAk8vzQ8aDPjJhqXVzPX8Qw6uEsOm',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-25 01:28:01','2025-11-25 01:28:01'),
(17,'Hendrik','hen@gmail.com','$2y$10$hL/mYHBaf3MUvGW7VWm1xe6pAduR9.QLB6KVwrzCJHI8mS9NPwu4m',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-28 02:05:45','2025-11-28 02:05:45'),
(18,'Hendrik Purwanto','hendrikpurwanto880@gmail.com','$2y$10$KbhAhAzgjSbOp.0pK.4HAuNYXa/1ZtNtsrTIuBdR5GaSJXA2qkGyq',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-29 21:36:58','2025-11-29 21:36:58'),
(19,'Surya','suryamaulana7572@gmail.com','$2y$10$KB8bKkggbZRH.E85Je328OIAMBuoW8WRlu7bZrMDNZNFsZLbwz.Wu',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-11-30 06:48:22','2025-12-02 06:41:05'),
(20,'YOOY','jonikumar@gmail.com','$2y$10$PO.x6Wru3jJG5NfiUnGG1eRmoXeuCMf4x2P7YmvacAfCeemQS5Hsa',NULL,NULL,'email',NULL,'owner',1,1,NULL,'2025-12-02 01:34:06','2025-12-02 01:34:06'),
(21,'rezanur','reza@gmail.com','$2y$10$Uiyg56nQKXbFJimMSnkF.O0mm50xmwNPDBHBGdxyapdh8lIVNBele',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-12-06 04:35:41','2025-12-06 04:35:41'),
(22,'Sahrul','sahrul51@yahoo.co.id','$2y$10$zZtZ076HimvekWV0qgVHx.0JG3TfOaBHn20p5VBPu2ScIhtng4UV6',NULL,NULL,'email',NULL,'customer',1,1,NULL,'2025-12-10 12:35:51','2025-12-10 12:35:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-12-12 13:34:18
