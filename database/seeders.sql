-- =====================================================
-- TREVIO DATABASE SEEDERS
-- =====================================================
-- Purpose: Populate database with test data
-- Date: November 23, 2025
-- Password for all test accounts: "password"
-- Bcrypt hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. USERS (Admin, Owners, Customers)
-- =====================================================

-- Admin Account
INSERT INTO users (name, email, password, phone, role, auth_provider, is_verified, is_active, created_at, updated_at) VALUES
('Admin Trevio', 'admin@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin', 'email', 1, 1, NOW(), NOW());

-- Owner Accounts
INSERT INTO users (name, email, password, phone, whatsapp_number, role, auth_provider, is_verified, is_active, created_at, updated_at) VALUES
('Hotel Owner 1', 'owner1@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', '6281234567891', 'owner', 'email', 1, 1, NOW(), NOW()),
('Hotel Owner 2', 'owner2@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', '6281234567892', 'owner', 'email', 1, 1, NOW(), NOW()),
('Budi Santoso', 'budi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', '6281234567893', 'owner', 'email', 1, 1, NOW(), NOW());

-- Customer Accounts
INSERT INTO users (name, email, password, phone, whatsapp_number, role, auth_provider, is_verified, is_active, created_at, updated_at) VALUES
('Customer Test', 'customer@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567894', '6281234567894', 'customer', 'email', 1, 1, NOW(), NOW()),
('Siti Nurhaliza', 'siti@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082234567890', '6282234567890', 'customer', 'email', 1, 1, NOW(), NOW()),
('Andi Wijaya', 'andi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '083234567890', '6283234567890', 'customer', 'email', 1, 1, NOW(), NOW()),
('Dewi Lestari', 'dewi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '084234567890', '6284234567890', 'customer', 'email', 1, 1, NOW(), NOW()),
('Rudi Hartono', 'rudi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '085234567890', '6285234567890', 'customer', 'email', 1, 1, NOW(), NOW());

-- =====================================================
-- 2. HOTELS
-- =====================================================

INSERT INTO hotels (owner_id, name, address, city, province, latitude, longitude, description, facilities, star_rating, main_image, is_active, created_at, updated_at) VALUES
-- Hotel Owner 1
(2, 'Grand Trevio Hotel Jakarta', 'Jl. Thamrin No. 1', 'Jakarta Pusat', 'DKI Jakarta', -6.1951, 106.8229, 
'Hotel bintang 5 di pusat Jakarta dengan fasilitas lengkap dan pemandangan kota yang menakjubkan. Dekat dengan pusat perbelanjaan dan kawasan bisnis.', 
'WiFi Gratis,Kolam Renang,Gym,Restaurant,Bar,Spa,Laundry,Room Service,Parking,Airport Shuttle', 
5, '/uploads/hotels/grand_trevio_jakarta.jpg', 1, NOW(), NOW()),

(2, 'Trevio Beach Resort Bali', 'Jl. Pantai Kuta No. 99', 'Badung', 'Bali', -8.7185, 115.1680,
'Resort tepi pantai dengan private beach, cocok untuk liburan keluarga dan bulan madu. Pemandangan sunset yang indah.', 
'WiFi Gratis,Kolam Renang,Beach Access,Restaurant,Bar,Spa,Kids Club,Water Sports,Parking', 
5, '15:00:00', '11:00:00', '/uploads/hotels/trevio_beach_bali.jpg', 1, NOW(), NOW()),

-- Hotel Owner 2
(3, 'Trevio Mountain View Bandung', 'Jl. Raya Lembang No. 234', 'Bandung', 'Jawa Barat', '40391', -6.8116, 107.6156,
'Hotel dengan pemandangan pegunungan yang sejuk. Cocok untuk family gathering dan corporate event.', 
'WiFi Gratis,Restaurant,Cafe,Meeting Room,Outdoor Activities,Parking,Garden', 
4, '14:00:00', '12:00:00', '/uploads/hotels/mountain_bandung.jpg', 1, NOW(), NOW()),

(3, 'Trevio City Inn Surabaya', 'Jl. Pemuda No. 45', 'Surabaya', 'Jawa Timur', '60271', -7.2575, 112.7521,
'Hotel budget di pusat kota Surabaya. Dekat dengan stasiun dan pusat perbelanjaan. Harga terjangkau dengan fasilitas memadai.', 
'WiFi Gratis,Restaurant,Laundry,24h Front Desk,Parking', 
3, '14:00:00', '12:00:00', '/uploads/hotels/city_inn_surabaya.jpg', 1, NOW(), NOW()),

-- Budi Santoso
(4, 'Trevio Heritage Hotel Yogyakarta', 'Jl. Malioboro No. 56', 'Yogyakarta', 'DI Yogyakarta', '55213', -7.7956, 110.3695,
'Hotel heritage dengan sentuhan budaya Jawa. Lokasi strategis di Malioboro. Walking distance ke Keraton dan Taman Sari.', 
'WiFi Gratis,Restaurant,Cafe,Traditional Spa,Cultural Tours,Parking,Bicycle Rental', 
4, '14:00:00', '12:00:00', '/uploads/hotels/heritage_jogja.jpg', 1, NOW(), NOW());

-- =====================================================
-- 3. ROOMS
-- =====================================================

-- Grand Trevio Hotel Jakarta (Hotel ID 1)
INSERT INTO rooms (hotel_id, room_type, price_per_night, total_slots, available_slots, capacity, room_size, bed_type, description, amenities, main_image, is_available, created_at, updated_at) VALUES
(1, 'Deluxe Room', 850000, 20, 20, 2, 28, 'King Bed', 'Kamar deluxe dengan pemandangan kota, dilengkapi AC, TV LED, minibar, dan kamar mandi dengan bathtub.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Bathtub', '/uploads/rooms/deluxe_jakarta_1.jpg', 1, NOW(), NOW()),
(1, 'Executive Suite', 1500000, 10, 10, 3, 45, 'King Bed + Sofa Bed', 'Suite mewah dengan ruang tamu terpisah, pantry kecil, dan balkon pribadi dengan pemandangan Jakarta.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Bathtub,Living Room,Balcony,Coffee Maker', '/uploads/rooms/executive_jakarta_1.jpg', 1, NOW(), NOW()),
(1, 'Presidential Suite', 3500000, 2, 2, 4, 85, '2 King Beds', 'Suite presidensial dengan 2 kamar tidur, ruang makan, ruang kerja, dan jacuzzi pribadi.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Jacuzzi,Living Room,Dining Room,Kitchen,Balcony', '/uploads/rooms/presidential_jakarta_1.jpg', 1, NOW(), NOW()),

-- Trevio Beach Resort Bali (Hotel ID 2)
INSERT INTO rooms (hotel_id, room_type, price_per_night, total_slots, available_slots, capacity, room_size, bed_type, description, amenities, main_image, is_available, created_at, updated_at) VALUES
(2, 'Garden View Room', 950000, 25, 25, 2, 30, 'Queen Bed', 'Kamar dengan pemandangan taman tropis. Cocok untuk pasangan dan family.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Bathtub,Balcony', '/uploads/rooms/garden_bali_1.jpg', 1, NOW(), NOW()),
(2, 'Ocean View Room', 1350000, 15, 15, 2, 32, 'King Bed', 'Kamar dengan pemandangan laut lepas. Private balcony untuk menikmati sunset.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Bathtub,Balcony,Ocean View', '/uploads/rooms/ocean_bali_1.jpg', 1, NOW(), NOW()),
(2, 'Beach Front Villa', 2800000, 8, 8, 4, 75, 'King Bed + 2 Single Beds', 'Villa pribadi di tepi pantai dengan akses langsung ke beach. Private pool dan gazebo.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Outdoor Shower,Private Pool,Beach Access,Living Room', '["/uploads/rooms/villa_bali_1.jpg"]', 1, NOW(), NOW()),

-- Trevio Mountain View Bandung (Hotel ID 3)
INSERT INTO rooms (hotel_id, room_type, price_per_night, total_rooms, available_slots, capacity, size_sqm, bed_type, description, amenities, room_images, is_available, created_at, updated_at) VALUES
(3, 'Standard Room', 450000, 30, 30, 2, 24, 'Twin Beds', 'Kamar standard dengan pemandangan pegunungan. Udara sejuk dan nyaman.', 'AC,TV,WiFi,Hair Dryer', '["/uploads/rooms/standard_bandung_1.jpg"]', 1, NOW(), NOW()),
(3, 'Family Room', 750000, 12, 12, 4, 40, '1 King + 2 Single', 'Kamar keluarga luas dengan 2 kamar tidur. Cocok untuk keluarga dengan anak.', 'AC,TV,WiFi,Minibar,Hair Dryer,Bathtub,Living Area', '["/uploads/rooms/family_bandung_1.jpg"]', 1, NOW(), NOW()),

-- Trevio City Inn Surabaya (Hotel ID 4)
INSERT INTO rooms (hotel_id, room_type, price_per_night, total_rooms, available_slots, capacity, size_sqm, bed_type, description, amenities, room_images, is_available, created_at, updated_at) VALUES
(4, 'Economy Room', 250000, 40, 40, 2, 18, 'Double Bed', 'Kamar ekonomis bersih dan nyaman. Cocok untuk budget traveler.', 'AC,TV,WiFi', '["/uploads/rooms/economy_surabaya_1.jpg"]', 1, NOW(), NOW()),
(4, 'Superior Room', 400000, 20, 20, 2, 22, 'Queen Bed', 'Kamar superior dengan fasilitas lebih lengkap. Breakfast included.', 'AC,TV,WiFi,Minibar,Hair Dryer', '["/uploads/rooms/superior_surabaya_1.jpg"]', 1, NOW(), NOW()),

-- Trevio Heritage Hotel Yogyakarta (Hotel ID 5)
INSERT INTO rooms (hotel_id, room_type, price_per_night, total_rooms, available_slots, capacity, size_sqm, bed_type, description, amenities, room_images, is_available, created_at, updated_at) VALUES
(5, 'Joglo Room', 600000, 15, 15, 2, 28, 'King Bed', 'Kamar dengan desain tradisional Joglo. Nuansa budaya Jawa yang kental.', 'AC,TV,WiFi,Minibar,Safe Box,Traditional Decor', '["/uploads/rooms/joglo_jogja_1.jpg"]', 1, NOW(), NOW()),
(5, 'Heritage Suite', 1100000, 6, 6, 3, 50, 'King Bed + Sofa Bed', 'Suite dengan furniture antik dan dekorasi tradisional. Balcony dengan pemandangan Merapi.', 'AC,TV,WiFi,Minibar,Safe Box,Hair Dryer,Bathtub,Living Room,Balcony,Traditional Decor', '["/uploads/rooms/heritage_suite_jogja_1.jpg"]', 1, NOW(), NOW());

-- =====================================================
-- 4. BOOKINGS (Sample Data)
-- =====================================================

-- Booking Confirmed (Customer 1 - Siti)
INSERT INTO bookings (customer_id, room_id, booking_code, check_in_date, check_out_date, num_rooms, num_nights, guest_name, guest_email, guest_phone, price_per_night, subtotal, tax_amount, service_charge, total_price, booking_status, special_requests, created_at, updated_at) VALUES
(6, 1, 'TRV-20251120-A001', '2025-11-25', '2025-11-27', 1, 2, 'Siti Nurhaliza', 'siti@gmail.com', '082234567890', 850000, 1700000, 170000, 85000, 1955000, 'confirmed', 'Early check-in jika tersedia', NOW(), NOW());

-- Update room availability for confirmed booking
UPDATE rooms SET available_slots = available_slots - 1 WHERE id = 1;

-- Booking Pending Payment (Customer 2 - Andi)
INSERT INTO bookings (customer_id, room_id, booking_code, check_in_date, check_out_date, num_rooms, num_nights, guest_name, guest_email, guest_phone, price_per_night, subtotal, tax_amount, service_charge, total_price, booking_status, created_at, updated_at) VALUES
(7, 4, 'TRV-20251121-B002', '2025-11-28', '2025-11-30', 2, 2, 'Andi Wijaya', 'andi@gmail.com', '083234567890', 950000, 3800000, 380000, 190000, 4370000, 'pending_payment', NOW(), NOW());

-- Booking Pending Verification (Customer 3 - Dewi)
INSERT INTO bookings (customer_id, room_id, booking_code, check_in_date, check_out_date, num_rooms, num_nights, guest_name, guest_email, guest_phone, price_per_night, subtotal, tax_amount, service_charge, total_price, booking_status, created_at, updated_at) VALUES
(8, 9, 'TRV-20251122-C003', '2025-12-01', '2025-12-03', 1, 2, 'Dewi Lestari', 'dewi@gmail.com', '084234567890', 600000, 1200000, 120000, 60000, 1380000, 'pending_verification', NOW(), NOW());

-- =====================================================
-- 5. PAYMENTS
-- =====================================================

-- Payment Verified (for Booking 1)
INSERT INTO payments (booking_id, payment_proof, payment_status, bank_name, account_name, account_number, payment_date, verified_by, verified_at, admin_notes, created_at, updated_at) VALUES
(1, '/uploads/payments/payment_1_proof.jpg', 'verified', 'BCA', 'Siti Nurhaliza', '1234567890', '2025-11-21 10:30:00', 1, '2025-11-21 11:00:00', 'Payment verified successfully', NOW(), NOW());

-- Payment Pending Verification (for Booking 3)
INSERT INTO payments (booking_id, payment_proof, payment_status, bank_name, account_name, account_number, payment_date, created_at, updated_at) VALUES
(3, '/uploads/payments/payment_3_proof.jpg', 'pending', 'Mandiri', 'Dewi Lestari', '9876543210', '2025-11-22 14:20:00', NOW(), NOW());

-- =====================================================
-- 6. REVIEWS
-- =====================================================

-- Review for Grand Trevio Hotel Jakarta
INSERT INTO reviews (booking_id, customer_id, hotel_id, rating, review_text, review_images, owner_response, owner_response_at, is_approved, created_at, updated_at) VALUES
(1, 6, 1, 5, 'Hotel yang sangat bagus! Pelayanan ramah, kamar bersih dan nyaman. Lokasi strategis di pusat Jakarta. Breakfast juga enak. Highly recommended!', 
'["/uploads/reviews/review_1_img1.jpg"]', 
'Terima kasih atas review positifnya! Kami senang Anda menikmati menginap di hotel kami. Sampai jumpa lagi!', 
'2025-11-22 09:00:00', 1, NOW(), NOW());

-- =====================================================
-- 7. NOTIFICATIONS (Sample)
-- =====================================================

INSERT INTO notifications (
  user_id,
  notification_type,
  title,
  message,
  booking_id,
  is_read,
  send_email,
  send_whatsapp,
  email_sent,
  whatsapp_sent,
  created_at
) VALUES
-- For Siti (Customer - Booking Confirmed)
(6, 'booking_confirmed', 'Booking Dikonfirmasi', 'Booking Anda dengan kode TRV-20251120-A001 telah dikonfirmasi. Selamat menikmati penginapan!', 1, 1, 1, 0, 0, 0, NOW()),

-- For Hotel Owner 1 (New Booking)
(2, 'new_booking', 'Booking Baru', 'Anda mendapat booking baru dengan kode TRV-20251120-A001 untuk Grand Trevio Hotel Jakarta.', 1, 0, 1, 1, 0, 0, NOW()),

-- For Andi (Customer - Payment Uploaded)
(7, 'payment_uploaded', 'Bukti Pembayaran Diupload', 'Bukti pembayaran untuk booking TRV-20251121-B002 berhasil diupload. Menunggu verifikasi admin.', 2, 0, 1, 0, 0, 0, NOW()),

-- For Admin (Payment Needs Verification)
(1, 'payment_verification', 'Pembayaran Perlu Verifikasi', 'Pembayaran untuk booking TRV-20251122-C003 menunggu verifikasi Anda.', 3, 0, 1, 1, 0, 0, NOW());

-- =====================================================
-- 8. ADDITIONAL BOOKINGS FOR TESTING
-- =====================================================

-- Completed Booking
INSERT INTO bookings (customer_id, room_id, booking_code, check_in_date, check_out_date, num_rooms, num_nights, guest_name, guest_email, guest_phone, price_per_night, subtotal, tax_amount, service_charge, total_price, booking_status, created_at, updated_at) VALUES
(9, 11, 'TRV-20251101-D004', '2025-11-05', '2025-11-07', 1, 2, 'Rudi Hartono', 'rudi@gmail.com', '085234567890', 450000, 900000, 90000, 45000, 1035000, 'completed', NOW(), NOW());

-- Payment for completed booking
INSERT INTO payments (booking_id, payment_proof, payment_status, bank_name, account_name, account_number, payment_date, verified_by, verified_at, admin_notes, created_at, updated_at) VALUES
(4, '/uploads/payments/payment_4_proof.jpg', 'verified', 'BNI', 'Rudi Hartono', '5555666777', '2025-11-02 09:00:00', 1, '2025-11-02 10:00:00', 'Verified', NOW(), NOW());

-- Cancelled Booking
INSERT INTO bookings (customer_id, room_id, booking_code, check_in_date, check_out_date, num_rooms, num_nights, guest_name, guest_email, guest_phone, price_per_night, subtotal, tax_amount, service_charge, total_price, booking_status, cancellation_reason, created_at, updated_at) VALUES
(7, 5, 'TRV-20251115-E005', '2025-11-20', '2025-11-22', 1, 2, 'Andi Wijaya', 'andi@gmail.com', '083234567890', 1350000, 2700000, 270000, 135000, 3105000, 'cancelled', 'Perubahan jadwal mendadak', NOW(), NOW());

-- =====================================================
-- SUMMARY OF SEEDED DATA
-- =====================================================
-- Users:
--   1 Admin (admin@trevio.com)
--   3 Owners (owner1@trevio.com, owner2@trevio.com, budi@gmail.com)
--   5 Customers (customer@trevio.com, siti@gmail.com, andi@gmail.com, dewi@gmail.com, rudi@gmail.com)
--
-- Hotels: 5 hotels across different cities
-- Rooms: 16 room types with various capacities and prices
-- Bookings: 5 bookings with different statuses (confirmed, pending_payment, pending_verification, completed, cancelled)
-- Payments: 3 payments (2 verified, 1 pending)
-- Reviews: 1 review with owner response
-- Notifications: 4 notifications for different user types
-- =====================================================

-- Password for all accounts: "password"
-- Test with: admin@trevio.com, owner1@trevio.com, customer@trevio.com

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- VERIFICATION QUERIES (Run these to check data)
-- =====================================================
-- SELECT * FROM users WHERE role = 'admin';
-- SELECT * FROM hotels;
-- SELECT h.name as hotel, r.room_type, r.price_per_night, r.available_slots 
--   FROM rooms r JOIN hotels h ON r.hotel_id = h.id;
-- SELECT b.booking_code, b.booking_status, u.name as customer, h.name as hotel
--   FROM bookings b 
--   JOIN users u ON b.customer_id = u.id
--   JOIN rooms r ON b.room_id = r.id
--   JOIN hotels h ON r.hotel_id = h.id;
-- =====================================================
