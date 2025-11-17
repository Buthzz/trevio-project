-- =====================================================
-- TREVIO DATABASE SCHEMA
-- Travel Booking Platform (Hotel & Flight)
-- 
-- Requirements:
-- - MySQL 8.0+
-- - 7 Tables (exceeds minimum 5 requirement)
-- - 3 Main Transactions: Hotel Booking, Flight Booking, Payment
-- =====================================================

CREATE DATABASE IF NOT EXISTS trevio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE trevio;

-- =====================================================
-- TABLE 1: users
-- Menyimpan data user (Guest, User, Admin)
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('guest', 'user', 'admin') DEFAULT 'user',
    profile_image VARCHAR(255) DEFAULT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 2: hotels
-- Menyimpan data master hotel
-- =====================================================
CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    country VARCHAR(100) DEFAULT 'Indonesia',
    postal_code VARCHAR(10),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    star_rating TINYINT CHECK (star_rating BETWEEN 1 AND 5),
    image_url VARCHAR(255),
    facilities JSON, -- ["WiFi", "Pool", "Restaurant", "Gym"]
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_city (city),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 3: rooms
-- Menyimpan data tipe kamar hotel
-- =====================================================
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    room_type VARCHAR(100) NOT NULL, -- Standard, Deluxe, Suite
    description TEXT,
    capacity INT NOT NULL DEFAULT 2,
    bed_type VARCHAR(50), -- Single, Double, Twin, King
    price_per_night DECIMAL(10, 2) NOT NULL,
    total_rooms INT NOT NULL DEFAULT 10,
    available_rooms INT NOT NULL DEFAULT 10,
    room_size INT, -- in square meters
    amenities JSON, -- ["AC", "TV", "Mini Bar", "Balcony"]
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    INDEX idx_hotel (hotel_id),
    INDEX idx_available (is_available),
    INDEX idx_price (price_per_night)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 4: flights
-- Menyimpan data jadwal penerbangan
-- =====================================================
CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(20) UNIQUE NOT NULL,
    airline VARCHAR(100) NOT NULL, -- Garuda, Lion Air, AirAsia
    departure_airport VARCHAR(100) NOT NULL, -- CGK, SUB, DPS
    arrival_airport VARCHAR(100) NOT NULL,
    departure_city VARCHAR(100) NOT NULL,
    arrival_city VARCHAR(100) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    duration INT NOT NULL, -- in minutes
    price DECIMAL(10, 2) NOT NULL,
    class ENUM('Economy', 'Business', 'First') DEFAULT 'Economy',
    total_seats INT NOT NULL DEFAULT 180,
    available_seats INT NOT NULL DEFAULT 180,
    baggage_allowance INT DEFAULT 20, -- in kg
    aircraft_type VARCHAR(50), -- Boeing 737, Airbus A320
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_route (departure_city, arrival_city),
    INDEX idx_departure (departure_time),
    INDEX idx_active (is_active),
    CONSTRAINT chk_airports CHECK (departure_airport != arrival_airport)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 5: bookings
-- Menyimpan semua transaksi booking (hotel & flight)
-- MAIN TRANSACTION TABLE
-- =====================================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    booking_type ENUM('hotel', 'flight') NOT NULL,
    
    -- Hotel booking fields
    hotel_id INT NULL,
    room_id INT NULL,
    check_in_date DATE NULL,
    check_out_date DATE NULL,
    num_rooms INT DEFAULT 1,
    
    -- Flight booking fields
    flight_id INT NULL,
    num_passengers INT DEFAULT 1,
    
    -- Common fields
    total_price DECIMAL(10, 2) NOT NULL,
    booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    guest_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(100) NOT NULL,
    guest_phone VARCHAR(20) NOT NULL,
    special_requests TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (flight_id) REFERENCES flights(id) ON DELETE SET NULL,
    
    INDEX idx_user (user_id),
    INDEX idx_status (booking_status),
    INDEX idx_type (booking_type),
    INDEX idx_code (booking_code)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 6: payments
-- Menyimpan transaksi pembayaran via Xendit
-- MAIN TRANSACTION TABLE
-- =====================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL, -- credit_card, bank_transfer, ewallet
    payment_provider VARCHAR(50) DEFAULT 'Xendit',
    amount DECIMAL(10, 2) NOT NULL,
    
    -- Xendit specific fields
    xendit_invoice_id VARCHAR(100) UNIQUE,
    xendit_payment_url TEXT,
    xendit_external_id VARCHAR(100),
    
    payment_status ENUM('pending', 'paid', 'failed', 'expired', 'refunded') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    expired_at TIMESTAMP NULL,
    
    transaction_data JSON, -- Store full Xendit response
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    
    INDEX idx_booking (booking_id),
    INDEX idx_status (payment_status),
    INDEX idx_xendit_invoice (xendit_invoice_id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE 7: reviews (OPTIONAL - BONUS FEATURE)
-- Menyimpan review & rating dari user
-- =====================================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    reviewable_type ENUM('hotel', 'flight') NOT NULL,
    reviewable_id INT NOT NULL, -- hotel_id or flight_id
    
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    
    is_approved BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_review (user_id, booking_id),
    INDEX idx_reviewable (reviewable_type, reviewable_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB;

-- =====================================================
-- INITIAL DATA - Admin Account
-- =====================================================
INSERT INTO users (name, email, password, phone, role, is_verified) VALUES
('Admin Trevio', 'admin@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin', TRUE),
-- Password: admin123
('John Doe', 'user@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'user', TRUE);
-- Password: user123

-- =====================================================
-- SAMPLE DATA - Hotels
-- =====================================================
INSERT INTO hotels (name, description, address, city, province, star_rating, facilities, image_url) VALUES
('Grand Hyatt Jakarta', 'Luxury 5-star hotel in the heart of Jakarta', 'Jl. M.H. Thamrin No.28-30', 'Jakarta', 'DKI Jakarta', 5, '["WiFi", "Pool", "Spa", "Gym", "Restaurant"]', 'https://via.placeholder.com/400x300'),
('Sheraton Surabaya', 'Premium business hotel with modern amenities', 'Jl. Embong Malang No.25-31', 'Surabaya', 'Jawa Timur', 4, '["WiFi", "Pool", "Restaurant", "Meeting Room"]', 'https://via.placeholder.com/400x300'),
('The Stones Hotel Bali', 'Beachfront resort in Legian Beach', 'Jl. Pantai Kuta', 'Denpasar', 'Bali', 5, '["WiFi", "Beach Access", "Pool", "Spa", "Bar"]', 'https://via.placeholder.com/400x300');

-- =====================================================
-- SAMPLE DATA - Rooms
-- =====================================================
INSERT INTO rooms (hotel_id, room_type, description, capacity, bed_type, price_per_night, total_rooms, available_rooms, amenities) VALUES
(1, 'Deluxe Room', 'Spacious room with city view', 2, 'King', 1500000.00, 20, 20, '["AC", "TV", "Mini Bar", "WiFi", "Safe"]'),
(1, 'Executive Suite', 'Luxurious suite with living room', 4, 'King', 3500000.00, 5, 5, '["AC", "TV", "Mini Bar", "WiFi", "Safe", "Balcony"]'),
(2, 'Superior Room', 'Comfortable room for business travelers', 2, 'Double', 850000.00, 30, 30, '["AC", "TV", "WiFi", "Work Desk"]'),
(3, 'Ocean View Room', 'Room with stunning ocean view', 2, 'King', 2200000.00, 15, 15, '["AC", "TV", "Mini Bar", "Balcony", "Beach Access"]');

-- =====================================================
-- SAMPLE DATA - Flights
-- =====================================================
INSERT INTO flights (flight_number, airline, departure_airport, arrival_airport, departure_city, arrival_city, departure_time, arrival_time, duration, price, class, total_seats, available_seats) VALUES
('GA-100', 'Garuda Indonesia', 'CGK', 'SUB', 'Jakarta', 'Surabaya', '2025-12-01 08:00:00', '2025-12-01 09:30:00', 90, 1200000.00, 'Economy', 180, 180),
('GA-101', 'Garuda Indonesia', 'SUB', 'CGK', 'Surabaya', 'Jakarta', '2025-12-01 14:00:00', '2025-12-01 15:30:00', 90, 1200000.00, 'Economy', 180, 180),
('QZ-200', 'AirAsia', 'CGK', 'DPS', 'Jakarta', 'Denpasar', '2025-12-01 10:00:00', '2025-12-01 12:00:00', 120, 850000.00, 'Economy', 180, 180),
('JT-300', 'Lion Air', 'DPS', 'CGK', 'Denpasar', 'Jakarta', '2025-12-01 16:00:00', '2025-12-01 18:00:00', 120, 900000.00, 'Economy', 180, 180);

-- =====================================================
-- TRIGGERS - Auto generate booking code
-- =====================================================
DELIMITER //

CREATE TRIGGER before_booking_insert 
BEFORE INSERT ON bookings
FOR EACH ROW
BEGIN
    IF NEW.booking_code IS NULL OR NEW.booking_code = '' THEN
        SET NEW.booking_code = CONCAT(
            UPPER(LEFT(NEW.booking_type, 1)),
            DATE_FORMAT(NOW(), '%Y%m%d'),
            LPAD(FLOOR(RAND() * 9999), 4, '0')
        );
    END IF;
END//

DELIMITER ;

-- =====================================================
-- VIEWS - Useful queries
-- =====================================================

-- View untuk booking summary
CREATE VIEW booking_summary AS
SELECT 
    b.id,
    b.booking_code,
    b.booking_type,
    u.name as user_name,
    u.email as user_email,
    CASE 
        WHEN b.booking_type = 'hotel' THEN h.name
        WHEN b.booking_type = 'flight' THEN f.airline
    END as service_name,
    b.total_price,
    b.booking_status,
    p.payment_status,
    b.created_at
FROM bookings b
JOIN users u ON b.user_id = u.id
LEFT JOIN hotels h ON b.hotel_id = h.id
LEFT JOIN flights f ON b.flight_id = f.id
LEFT JOIN payments p ON p.booking_id = b.id;

-- =====================================================
-- END OF SCHEMA
-- =====================================================