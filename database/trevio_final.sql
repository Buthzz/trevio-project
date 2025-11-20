-- =====================================================
-- TREVIO - HOTEL BOOKING MANAGEMENT SYSTEM
-- Simplified Schema with Slot Management
-- =====================================================

CREATE DATABASE IF NOT EXISTS trevio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE trevio;

-- =====================================================
-- TABLE 1: users
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255), -- Nullable for Google OAuth users
    phone VARCHAR(20),
    whatsapp_number VARCHAR(20),
    
    -- Authentication
    auth_provider ENUM('email', 'google') DEFAULT 'email',
    google_id VARCHAR(100) UNIQUE NULL,
    
    -- Role & Status
    role ENUM('customer', 'owner', 'admin') DEFAULT 'customer',
    is_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    profile_image VARCHAR(255) DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_google_id (google_id),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 2: hotels
-- =====================================================
CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    
    name VARCHAR(200) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    
    star_rating TINYINT CHECK (star_rating BETWEEN 1 AND 5),
    
    main_image VARCHAR(255),
    facilities JSON,
    
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE, -- Admin approval
    
    -- Stats (denormalized for performance)
    average_rating DECIMAL(3, 2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_owner (owner_id),
    INDEX idx_city (city),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 3: rooms (WITH SLOT MANAGEMENT)
-- =====================================================
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    
    room_type VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT NOT NULL DEFAULT 2,
    bed_type VARCHAR(50),
    
    price_per_night DECIMAL(10, 2) NOT NULL,
    
    -- SLOT MANAGEMENT (KEY FEATURE!)
    total_slots INT NOT NULL DEFAULT 10,        -- Set by owner
    available_slots INT NOT NULL DEFAULT 10,    -- Reduced on booking
    
    room_size INT,
    amenities JSON,
    main_image VARCHAR(255),
    
    is_available BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    INDEX idx_hotel (hotel_id),
    INDEX idx_available (is_available),
    INDEX idx_slots (available_slots)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 4: bookings (Main Transaction 1)
-- =====================================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    
    customer_id INT NOT NULL,
    hotel_id INT NOT NULL,
    room_id INT NOT NULL,
    
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    num_nights INT NOT NULL,
    num_rooms INT NOT NULL DEFAULT 1,
    
    -- Pricing
    price_per_night DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0.00,
    service_charge DECIMAL(10, 2) DEFAULT 0.00,
    total_price DECIMAL(10, 2) NOT NULL,
    
    -- Guest info
    guest_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(100) NOT NULL,
    guest_phone VARCHAR(20) NOT NULL,
    special_requests TEXT,
    
    -- Status
    booking_status ENUM(
        'pending_payment',
        'pending_verification',
        'confirmed',
        'checked_in',
        'completed',
        'cancelled',
        'refunded'
    ) DEFAULT 'pending_payment',
    
    -- Check-in
    checked_in_at TIMESTAMP NULL,
    checked_in_by INT NULL,
    
    -- Cancellation
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE RESTRICT,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT,
    FOREIGN KEY (checked_in_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_customer (customer_id),
    INDEX idx_hotel (hotel_id),
    INDEX idx_status (booking_status),
    INDEX idx_dates (check_in_date, check_out_date)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 5: payments (Manual Verification)
-- =====================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    
    payment_method ENUM('bank_transfer', 'cash') DEFAULT 'bank_transfer',
    
    -- Transfer details
    transfer_amount DECIMAL(10, 2) NOT NULL,
    transfer_to_bank VARCHAR(100),
    transfer_from_bank VARCHAR(100),
    transfer_date DATE,
    
    -- Payment proof
    payment_proof VARCHAR(255),
    payment_notes TEXT,
    
    -- Status
    payment_status ENUM('pending', 'uploaded', 'verified', 'rejected') DEFAULT 'pending',
    
    -- Admin verification
    verified_by INT NULL,
    verified_at TIMESTAMP NULL,
    admin_notes TEXT,
    rejection_reason TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_booking (booking_id),
    INDEX idx_status (payment_status)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 6: refunds (Main Transaction 3)
-- =====================================================
CREATE TABLE refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_id INT NOT NULL,
    customer_id INT NOT NULL,
    
    refund_amount DECIMAL(10, 2) NOT NULL,
    refund_reason TEXT NOT NULL,
    
    -- Customer bank account
    customer_bank_name VARCHAR(100) NOT NULL,
    customer_bank_account VARCHAR(50) NOT NULL,
    customer_bank_holder VARCHAR(100) NOT NULL,
    
    -- Status
    refund_status ENUM('requested', 'approved', 'processing', 'completed', 'rejected') DEFAULT 'requested',
    
    -- Admin processing
    processed_by INT NULL,
    processed_at TIMESTAMP NULL,
    admin_notes TEXT,
    
    refund_receipt VARCHAR(255),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_booking (booking_id),
    INDEX idx_status (refund_status)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 7: reviews
-- =====================================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    customer_id INT NOT NULL,
    hotel_id INT NOT NULL,
    
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    review_images JSON,
    
    -- Moderation
    is_approved BOOLEAN DEFAULT FALSE,
    approved_by INT NULL,
    
    -- Owner response
    owner_response TEXT NULL,
    owner_response_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_review (customer_id, booking_id),
    INDEX idx_hotel (hotel_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 8: notifications
-- =====================================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    
    notification_type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    
    -- Channels
    send_email BOOLEAN DEFAULT TRUE,
    send_whatsapp BOOLEAN DEFAULT FALSE,
    
    -- Email status
    email_sent BOOLEAN DEFAULT FALSE,
    email_sent_at TIMESTAMP NULL,
    
    -- WhatsApp status
    whatsapp_sent BOOLEAN DEFAULT FALSE,
    whatsapp_sent_at TIMESTAMP NULL,
    
    -- Related
    booking_id INT NULL,
    
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 9: admin_activities
-- =====================================================
CREATE TABLE admin_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    
    target_id INT NULL,
    target_type VARCHAR(50) NULL,
    
    ip_address VARCHAR(45),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin (admin_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Auto-generate booking code
DELIMITER //
CREATE TRIGGER before_booking_insert 
BEFORE INSERT ON bookings
FOR EACH ROW
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
END//
DELIMITER ;

-- Reduce slots when booking confirmed
DELIMITER //
CREATE TRIGGER after_booking_confirmed 
AFTER UPDATE ON bookings
FOR EACH ROW
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
END//
DELIMITER ;

-- Update payment status triggers booking status
DELIMITER //
CREATE TRIGGER after_payment_verified 
AFTER UPDATE ON payments
FOR EACH ROW
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
END//
DELIMITER ;

-- Update hotel rating when review approved
DELIMITER //
CREATE TRIGGER after_review_approved 
AFTER UPDATE ON reviews
FOR EACH ROW
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
END//
DELIMITER ;

-- =====================================================
-- INITIAL DATA
-- =====================================================

INSERT INTO users (name, email, password, phone, whatsapp_number, role, is_verified) VALUES
('Admin Trevio', 'admin@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', '6281234567890', 'admin', TRUE),
('Owner Hotel', 'owner@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', '6281234567891', 'owner', TRUE),
('Customer Test', 'customer@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', '6281234567892', 'customer', TRUE);
-- Password: password123

-- =====================================================
-- ROOM SLOT MANAGEMENT EXAMPLE
-- =====================================================

/*
HOW SLOT MANAGEMENT WORKS:

1. Owner creates room:
   INSERT INTO rooms (hotel_id, room_type, total_slots, available_slots, ...)
   VALUES (1, 'Deluxe Room', 10, 10, ...);
   
   Result: 10 slots ready for ALL dates automatically!

2. Customer books 2 rooms:
   - System checks: available_slots >= 2? YES
   - Create booking (status: pending_payment)
   - Customer uploads payment proof
   - Admin verifies payment
   - Trigger reduces: available_slots = 10 - 2 = 8

3. Customer cancels:
   - Admin processes cancellation
   - Trigger restores: available_slots = 8 + 2 = 10

4. Availability check (ANY date):
   SELECT * FROM rooms 
   WHERE hotel_id = ? 
   AND available_slots >= ?
   AND is_available = TRUE
   
   Simple! No per-date calendar needed! ✅
*/

-- =====================================================
-- END OF SCHEMA
-- =====================================================