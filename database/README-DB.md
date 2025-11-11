# Entity Relationship Diagram - Travel Booking Platform (Updated with Role Management)

## Core Entities Structure

### 1. USER MANAGEMENT & ROLES

**roles**
- id (PK, TINYINT UNSIGNED, AUTO_INCREMENT)
- name (VARCHAR(50), UNIQUE, NOT NULL) # 'guest', 'user', 'admin'
- slug (VARCHAR(50), UNIQUE, NOT NULL) # 'guest', 'user', 'admin'
- description (TEXT, NULLABLE)
- level (TINYINT UNSIGNED, NOT NULL) # 1=guest, 2=user, 3=admin (hierarchy)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

**permissions**
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- name (VARCHAR(100), UNIQUE, NOT NULL) # e.g., 'booking.create', 'user.update', 'admin.access'
- slug (VARCHAR(100), UNIQUE, NOT NULL)
- category (VARCHAR(50), NOT NULL) # 'booking', 'user', 'payment', 'admin'
- description (TEXT, NULLABLE)
- created_at (TIMESTAMP)

**role_permissions** (Many-to-Many)
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- role_id (FK -> roles.id, ON DELETE CASCADE)
- permission_id (FK -> permissions.id, ON DELETE CASCADE)
- created_at (TIMESTAMP)
- UNIQUE KEY unique_role_permission (role_id, permission_id)

**users**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- role_id (FK -> roles.id, DEFAULT 2, NOT NULL) # Default: user role
- email (VARCHAR(255), UNIQUE, NOT NULL)
- phone (VARCHAR(20), UNIQUE, NULLABLE)
- password_hash (VARCHAR(255), NOT NULL)
- full_name (VARCHAR(100), NOT NULL)
- date_of_birth (DATE, NULLABLE)
- gender (ENUM: 'male', 'female', 'other', NULLABLE)
- profile_image (VARCHAR(255), NULLABLE)
- email_verified_at (TIMESTAMP, NULLABLE)
- phone_verified_at (TIMESTAMP, NULLABLE)
- status (ENUM: 'active', 'suspended', 'inactive', DEFAULT 'active')
- last_login_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_role (role_id, status)
- INDEX idx_email (email, status)

**user_sessions** (Track active sessions per user)
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- user_id (FK -> users.id, ON DELETE CASCADE)
- token_hash (VARCHAR(255), UNIQUE, NOT NULL) # Hashed session token
- ip_address (VARCHAR(45), NOT NULL)
- user_agent (TEXT, NOT NULL)
- last_activity (TIMESTAMP, NOT NULL)
- expires_at (TIMESTAMP, NOT NULL)
- created_at (TIMESTAMP)
- INDEX idx_user_active (user_id, expires_at)
- INDEX idx_token (token_hash)

**guest_sessions** (For anonymous browsing/cart)
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- session_id (VARCHAR(100), UNIQUE, NOT NULL) # UUID
- ip_address (VARCHAR(45), NOT NULL)
- user_agent (TEXT, NOT NULL)
- cart_data (JSON, NULLABLE) # Store temporary booking selections
- last_activity (TIMESTAMP, NOT NULL)
- expires_at (TIMESTAMP, NOT NULL)
- created_at (TIMESTAMP)
- INDEX idx_session (session_id, expires_at)

**user_addresses**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- user_id (FK -> users.id, ON DELETE CASCADE)
- label (VARCHAR(50), e.g., 'Home', 'Office')
- address_line (TEXT, NOT NULL)
- city (VARCHAR(100), NOT NULL)
- province (VARCHAR(100), NOT NULL)
- postal_code (VARCHAR(10), NULLABLE)
- country (VARCHAR(2), DEFAULT 'ID')
- is_primary (BOOLEAN, DEFAULT FALSE)
- created_at (TIMESTAMP)

**user_passengers** (Saved passengers for quick booking)
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- user_id (FK -> users.id, ON DELETE CASCADE)
- title (ENUM: 'Mr', 'Mrs', 'Ms', NOT NULL)
- full_name (VARCHAR(100), NOT NULL)
- id_type (ENUM: 'ktp', 'passport', 'sim')
- id_number (VARCHAR(50), NOT NULL)
- date_of_birth (DATE, NOT NULL)
- nationality (VARCHAR(2), DEFAULT 'ID')
- is_primary (BOOLEAN, DEFAULT FALSE)
- created_at (TIMESTAMP)

---

### 2. ADMIN MANAGEMENT

**admins**
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- role_id (FK -> roles.id, DEFAULT 3, NOT NULL) # Admin role
- username (VARCHAR(50), UNIQUE, NOT NULL)
- email (VARCHAR(255), UNIQUE, NOT NULL)
- password_hash (VARCHAR(255), NOT NULL)
- full_name (VARCHAR(100), NOT NULL)
- admin_level (ENUM: 'super_admin', 'admin', 'support', 'finance', 'marketing')
- permissions_override (JSON, NULLABLE) # Custom permissions jika beda dari role
- is_active (BOOLEAN, DEFAULT TRUE)
- last_login_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_role (role_id, is_active)

**admin_sessions**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- admin_id (FK -> admins.id, ON DELETE CASCADE)
- token_hash (VARCHAR(255), UNIQUE, NOT NULL)
- ip_address (VARCHAR(45), NOT NULL)
- user_agent (TEXT, NOT NULL)
- last_activity (TIMESTAMP, NOT NULL)
- expires_at (TIMESTAMP, NOT NULL)
- created_at (TIMESTAMP)
- INDEX idx_admin_active (admin_id, expires_at)

---

### 3. PRODUCT MANAGEMENT

**airlines**
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- code (VARCHAR(10), UNIQUE, NOT NULL) # e.g., GA, QZ
- name (VARCHAR(100), NOT NULL)
- logo (VARCHAR(255), NULLABLE)
- country (VARCHAR(2), NOT NULL)
- status (ENUM: 'active', 'inactive', DEFAULT 'active')
- created_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

**airports**
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- code (VARCHAR(3), UNIQUE, NOT NULL) # IATA code
- name (VARCHAR(255), NOT NULL)
- city (VARCHAR(100), NOT NULL)
- country (VARCHAR(2), NOT NULL)
- timezone (VARCHAR(50), NOT NULL)
- latitude (DECIMAL(10,8), NULLABLE)
- longitude (DECIMAL(11,8), NULLABLE)

**flights**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- airline_id (FK -> airlines.id, ON DELETE RESTRICT)
- flight_number (VARCHAR(10), NOT NULL)
- departure_airport_id (FK -> airports.id, ON DELETE RESTRICT)
- arrival_airport_id (FK -> airports.id, ON DELETE RESTRICT)
- departure_time (DATETIME, NOT NULL)
- arrival_time (DATETIME, NOT NULL)
- duration_minutes (INT UNSIGNED, NOT NULL)
- aircraft_type (VARCHAR(50), NULLABLE)
- available_seats (INT UNSIGNED, NOT NULL)
- total_seats (INT UNSIGNED, NOT NULL)
- base_price (DECIMAL(12,2), NOT NULL)
- class (ENUM: 'economy', 'premium_economy', 'business', 'first')
- status (ENUM: 'scheduled', 'delayed', 'cancelled', 'completed')
- created_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_search (departure_airport_id, arrival_airport_id, departure_time, status)
- INDEX idx_flight_number (airline_id, flight_number)

**hotels**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- name (VARCHAR(255), NOT NULL)
- slug (VARCHAR(255), UNIQUE, NOT NULL)
- description (TEXT, NULLABLE)
- address (TEXT, NOT NULL)
- city (VARCHAR(100), NOT NULL)
- province (VARCHAR(100), NOT NULL)
- country (VARCHAR(2), DEFAULT 'ID')
- postal_code (VARCHAR(10), NULLABLE)
- latitude (DECIMAL(10,8), NOT NULL)
- longitude (DECIMAL(11,8), NOT NULL)
- star_rating (TINYINT UNSIGNED, NULLABLE) # 1-5
- phone (VARCHAR(20), NULLABLE)
- email (VARCHAR(255), NULLABLE)
- check_in_time (TIME, DEFAULT '14:00:00')
- check_out_time (TIME, DEFAULT '12:00:00')
- images (JSON, NULLABLE)
- facilities (JSON, NULLABLE)
- status (ENUM: 'active', 'inactive', DEFAULT 'active')
- created_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_location (city, status)

**hotel_rooms**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- hotel_id (FK -> hotels.id, ON DELETE CASCADE)
- room_type (VARCHAR(100), NOT NULL)
- description (TEXT, NULLABLE)
- max_occupancy (TINYINT UNSIGNED, NOT NULL)
- total_rooms (INT UNSIGNED, NOT NULL)
- bed_type (VARCHAR(50), NULLABLE)
- size_sqm (INT UNSIGNED, NULLABLE)
- images (JSON, NULLABLE)
- facilities (JSON, NULLABLE)
- base_price_per_night (DECIMAL(12,2), NOT NULL)
- status (ENUM: 'available', 'unavailable', DEFAULT 'available')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

**hotel_room_availability**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- room_id (FK -> hotel_rooms.id, ON DELETE CASCADE)
- date (DATE, NOT NULL)
- available_rooms (INT UNSIGNED, NOT NULL)
- price (DECIMAL(12,2), NOT NULL)
- UNIQUE KEY unique_room_date (room_id, date)
- INDEX idx_date (date)

---

### 4. BOOKING MANAGEMENT

**bookings**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- booking_code (VARCHAR(20), UNIQUE, NOT NULL)
- user_id (FK -> users.id, NULLABLE, ON DELETE RESTRICT) # Nullable for guest checkout
- guest_session_id (FK -> guest_sessions.id, NULLABLE, ON DELETE SET NULL)
- booking_type (ENUM: 'flight', 'hotel', 'train', 'car', 'activity')
- contact_name (VARCHAR(100), NOT NULL)
- contact_email (VARCHAR(255), NOT NULL)
- contact_phone (VARCHAR(20), NOT NULL)
- total_amount (DECIMAL(12,2), NOT NULL)
- discount_amount (DECIMAL(12,2), DEFAULT 0)
- tax_amount (DECIMAL(12,2), DEFAULT 0)
- service_fee (DECIMAL(12,2), DEFAULT 0)
- grand_total (DECIMAL(12,2), NOT NULL)
- currency (VARCHAR(3), DEFAULT 'IDR')
- status (ENUM: 'pending', 'confirmed', 'cancelled', 'completed', 'refunded')
- payment_status (ENUM: 'unpaid', 'paid', 'refunded', 'partial_refund')
- booking_date (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- expiry_date (TIMESTAMP, NOT NULL)
- notes (TEXT, NULLABLE)
- cancelled_by (ENUM: 'user', 'admin', 'system', NULLABLE)
- cancelled_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_user (user_id, status)
- INDEX idx_booking_code (booking_code)
- INDEX idx_status (status, payment_status)

**flight_bookings**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- booking_id (FK -> bookings.id, ON DELETE CASCADE)
- flight_id (FK -> flights.id, ON DELETE RESTRICT)
- trip_type (ENUM: 'one_way', 'round_trip')
- return_flight_id (FK -> flights.id, NULLABLE, ON DELETE RESTRICT)
- passenger_count (TINYINT UNSIGNED, NOT NULL)
- baggage_info (JSON, NULLABLE)
- seat_selection (JSON, NULLABLE)
- created_at (TIMESTAMP)

**flight_passengers**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- flight_booking_id (FK -> flight_bookings.id, ON DELETE CASCADE)
- title (ENUM: 'Mr', 'Mrs', 'Ms', NOT NULL)
- full_name (VARCHAR(100), NOT NULL)
- id_type (ENUM: 'ktp', 'passport', 'sim')
- id_number (VARCHAR(50), NOT NULL)
- date_of_birth (DATE, NOT NULL)
- nationality (VARCHAR(2), NOT NULL)
- seat_number (VARCHAR(10), NULLABLE)
- baggage_weight (INT UNSIGNED, DEFAULT 0)
- created_at (TIMESTAMP)

**hotel_bookings**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- booking_id (FK -> bookings.id, ON DELETE CASCADE)
- hotel_id (FK -> hotels.id, ON DELETE RESTRICT)
- room_id (FK -> hotel_rooms.id, ON DELETE RESTRICT)
- check_in_date (DATE, NOT NULL)
- check_out_date (DATE, NOT NULL)
- nights (TINYINT UNSIGNED, NOT NULL)
- rooms_count (TINYINT UNSIGNED, NOT NULL)
- guest_count (TINYINT UNSIGNED, NOT NULL)
- special_request (TEXT, NULLABLE)
- created_at (TIMESTAMP)
- INDEX idx_dates (check_in_date, check_out_date)

**hotel_booking_guests**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- hotel_booking_id (FK -> hotel_bookings.id, ON DELETE CASCADE)
- room_number (TINYINT UNSIGNED, NOT NULL)
- title (ENUM: 'Mr', 'Mrs', 'Ms', NOT NULL)
- full_name (VARCHAR(100), NOT NULL)
- is_primary (BOOLEAN, DEFAULT FALSE)
- created_at (TIMESTAMP)

---

### 5. PAYMENT & TRANSACTIONS

**payments**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- booking_id (FK -> bookings.id, ON DELETE RESTRICT)
- payment_code (VARCHAR(50), UNIQUE, NOT NULL)
- payment_method (ENUM: 'credit_card', 'bank_transfer', 'e_wallet', 'qris', 'retail')
- payment_channel (VARCHAR(50), NULLABLE)
- amount (DECIMAL(12,2), NOT NULL)
- admin_fee (DECIMAL(12,2), DEFAULT 0)
- total_amount (DECIMAL(12,2), NOT NULL)
- currency (VARCHAR(3), DEFAULT 'IDR')
- status (ENUM: 'pending', 'processing', 'completed', 'failed', 'expired')
- xendit_invoice_id (VARCHAR(100), UNIQUE, NULLABLE)
- xendit_external_id (VARCHAR(100), UNIQUE, NOT NULL)
- xendit_payment_url (TEXT, NULLABLE)
- xendit_callback_data (JSON, NULLABLE)
- paid_at (TIMESTAMP, NULLABLE)
- expired_at (TIMESTAMP, NOT NULL)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_booking (booking_id, status)
- INDEX idx_xendit (xendit_invoice_id)

**payment_channels**
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- code (VARCHAR(50), UNIQUE, NOT NULL)
- name (VARCHAR(100), NOT NULL)
- type (ENUM: 'bank_transfer', 'e_wallet', 'credit_card', 'qris', 'retail')
- icon (VARCHAR(255), NULLABLE)
- admin_fee_type (ENUM: 'fixed', 'percentage')
- admin_fee_value (DECIMAL(12,2), NOT NULL)
- min_amount (DECIMAL(12,2), DEFAULT 0)
- max_amount (DECIMAL(12,2), NULLABLE)
- is_active (BOOLEAN, DEFAULT TRUE)
- display_order (INT, DEFAULT 0)

**refunds**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- booking_id (FK -> bookings.id, ON DELETE RESTRICT)
- payment_id (FK -> payments.id, ON DELETE RESTRICT)
- refund_code (VARCHAR(50), UNIQUE, NOT NULL)
- refund_amount (DECIMAL(12,2), NOT NULL)
- refund_reason (ENUM: 'cancellation', 'overpayment', 'service_issue', 'other')
- reason_detail (TEXT, NULLABLE)
- admin_fee (DECIMAL(12,2), DEFAULT 0)
- net_refund (DECIMAL(12,2), NOT NULL)
- status (ENUM: 'pending', 'approved', 'processing', 'completed', 'rejected')
- xendit_refund_id (VARCHAR(100), UNIQUE, NULLABLE)
- approved_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- processed_at (TIMESTAMP, NULLABLE)
- completed_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

---

### 6. PROMO & VOUCHER

**vouchers**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- code (VARCHAR(50), UNIQUE, NOT NULL)
- name (VARCHAR(255), NOT NULL)
- description (TEXT, NULLABLE)
- type (ENUM: 'percentage', 'fixed_amount')
- value (DECIMAL(12,2), NOT NULL)
- max_discount (DECIMAL(12,2), NULLABLE)
- min_transaction (DECIMAL(12,2), DEFAULT 0)
- applicable_to (ENUM: 'all', 'flight', 'hotel', 'train', 'car', 'activity')
- user_specific (BOOLEAN, DEFAULT FALSE) # For targeted promos
- usage_limit (INT UNSIGNED, NULLABLE)
- usage_per_user (TINYINT UNSIGNED, DEFAULT 1)
- valid_from (TIMESTAMP, NOT NULL)
- valid_until (TIMESTAMP, NOT NULL)
- is_active (BOOLEAN, DEFAULT TRUE)
- created_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_code (code, is_active)
- INDEX idx_validity (valid_from, valid_until, is_active)

**voucher_usage**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- voucher_id (FK -> vouchers.id, ON DELETE CASCADE)
- user_id (FK -> users.id, NULLABLE, ON DELETE CASCADE) # Nullable for guest usage
- booking_id (FK -> bookings.id, ON DELETE CASCADE)
- discount_amount (DECIMAL(12,2), NOT NULL)
- used_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- INDEX idx_user_voucher (user_id, voucher_id)

---

### 7. REVIEWS & RATINGS

**reviews**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- user_id (FK -> users.id, ON DELETE CASCADE)
- booking_id (FK -> bookings.id, ON DELETE CASCADE)
- reviewable_type (ENUM: 'flight', 'hotel', 'train', 'car', 'activity')
- reviewable_id (BIGINT UNSIGNED, NOT NULL)
- rating (TINYINT UNSIGNED, NOT NULL) # 1-5
- title (VARCHAR(255), NULLABLE)
- comment (TEXT, NULLABLE)
- pros (TEXT, NULLABLE)
- cons (TEXT, NULLABLE)
- images (JSON, NULLABLE)
- is_verified (BOOLEAN, DEFAULT TRUE)
- helpful_count (INT UNSIGNED, DEFAULT 0)
- status (ENUM: 'pending', 'approved', 'rejected', DEFAULT 'pending')
- moderated_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- moderated_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- INDEX idx_reviewable (reviewable_type, reviewable_id, status)
- INDEX idx_user (user_id)

**review_responses**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- review_id (FK -> reviews.id, ON DELETE CASCADE)
- responder_type (ENUM: 'vendor', 'admin')
- responder_id (BIGINT UNSIGNED, NOT NULL) # FK to admins.id if type=admin
- response (TEXT, NOT NULL)
- created_at (TIMESTAMP)

---

### 8. NOTIFICATION SYSTEM

**notifications**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- user_id (FK -> users.id, ON DELETE CASCADE)
- type (ENUM: 'booking_confirmation', 'payment_reminder', 'payment_success', 'booking_cancelled', 'promo', 'review_request')
- title (VARCHAR(255), NOT NULL)
- message (TEXT, NOT NULL)
- data (JSON, NULLABLE)
- channel (ENUM: 'in_app', 'email', 'sms', 'push')
- is_read (BOOLEAN, DEFAULT FALSE)
- read_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- INDEX idx_user_read (user_id, is_read, created_at)

**email_logs**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- user_id (FK -> users.id, NULLABLE, ON DELETE SET NULL)
- to_email (VARCHAR(255), NOT NULL)
- subject (VARCHAR(255), NOT NULL)
- template (VARCHAR(100), NOT NULL)
- status (ENUM: 'queued', 'sent', 'failed')
- sent_at (TIMESTAMP, NULLABLE)
- error_message (TEXT, NULLABLE)
- created_at (TIMESTAMP)
- INDEX idx_status (status, created_at)

---

### 9. CONFIGURATION & AUDIT

**system_settings**
- id (PK, INT UNSIGNED, AUTO_INCREMENT)
- key (VARCHAR(100), UNIQUE, NOT NULL)
- value (TEXT, NOT NULL)
- type (ENUM: 'string', 'number', 'boolean', 'json')
- description (TEXT, NULLABLE)
- updated_by_admin_id (FK -> admins.id, NULLABLE, ON DELETE SET NULL)
- updated_at (TIMESTAMP)

**activity_logs**
- id (PK, BIGINT UNSIGNED, AUTO_INCREMENT)
- actor_type (ENUM: 'guest', 'user', 'admin') # Updated untuk 3 roles
- actor_id (BIGINT UNSIGNED, NULLABLE) # NULL untuk guest
- session_id (VARCHAR(100), NULLABLE) # guest_session_id atau user_session token
- action (VARCHAR(100), NOT NULL)
- entity_type (VARCHAR(50), NULLABLE)
- entity_id (BIGINT UNSIGNED, NULLABLE)
- ip_address (VARCHAR(45), NULLABLE)
- user_agent (TEXT, NULLABLE)
- metadata (JSON, NULLABLE)
- created_at (TIMESTAMP)
- INDEX idx_actor (actor_type, actor_id, created_at)
- INDEX idx_action (action, created_at)
- INDEX idx_session (session_id, created_at)

---

## Role Permission Matrix

### Guest (role_id: 1)
**Allowed Actions:**
- Browse products (flights, hotels)
- Search & filter
- View product details
- Add to cart (session-based)
- View voucher info
- Guest checkout (with email confirmation)

**Denied Actions:**
- Save passenger data
- View booking history
- Write reviews
- Loyalty points
- Profile management

---

### User (role_id: 2)
**Inherits Guest permissions + Additional:**
- Create bookings (all types)
- Save passenger data
- Manage profile & addresses
- View booking history
- Cancel bookings (with policy check)
- Write & manage reviews
- Apply vouchers
- Earn loyalty points (future)
- Save payment methods (tokenized)

**Denied Actions:**
- Access admin panel
- Manage products
- Approve refunds
- System configuration

---

### Admin (role_id: 3)
**Full Access Including:**
- Manage flights, hotels, products
- View all bookings & users
- Process refunds
- Moderate reviews
- Create/manage vouchers
- Access analytics dashboard
- System configuration
- User management (suspend/activate)
- View all activity logs

**Sub-levels (via admin_level):**
- **super_admin**: Full system access
- **admin**: Product & booking management
- **support**: Customer support, view-only bookings
- **finance**: Payment & refund management
- **marketing**: Voucher & promo management

---

## Implementation Notes

### Authentication Flow

**Guest:**
```php
// Create guest session on first visit
$sessionId = generateUUID();
$_SESSION['guest_id'] = $sessionId;
// Store in guest_sessions table
```

**User Registration:**
```php
// Auto-assign user role (role_id: 2)
INSERT INTO users (role_id, email, ...) VALUES (2, 'user@example.com', ...)
// Migrate guest cart if session_id exists
```

**Admin Login:**
```php
// Separate login endpoint: /admin/login
// Validate admin credentials from admins table
// Create admin_session
// Check role_id = 3 and admin_level permissions
```

### Authorization Middleware

```php
// Check role-based access
function requireRole($requiredRole) {
    $userRole = $_SESSION['user_role']; // From DB
    $roleHierarchy = ['guest' => 1, 'user' => 2, 'admin' => 3];
    
    if ($roleHierarchy[$userRole] < $roleHierarchy[$requiredRole]) {
        throw new UnauthorizedException();
    }
}

// Check specific permission
function requirePermission($permission) {
    $roleId = $_SESSION['role_id'];
    // Query role_permissions table
    $hasPermission = checkPermission($roleId, $permission);
    if (!$hasPermission) {
        throw new ForbiddenException();
    }
}
```

### Guest Checkout Flow

1. Guest browse & pilih produk → simpan di `guest_sessions.cart_data`
2. Checkout → create `bookings` dengan `user_id = NULL`, `guest_session_id = {id}`
3. Email konfirmasi dengan link untuk claim booking
4. Jika guest register nanti → link booking ke `user_id`

### Security Considerations

1. **Session Management:**
   - Guest sessions expire after 24h inactivity
   - User sessions expire after 7 days inactivity
   - Admin sessions expire after 2h inactivity (security)

2. **Password Policy:**
   - Users: Min 8 chars, 1 uppercase, 1 number
   - Admins: Min 12 chars, 1 uppercase, 1 number, 1 special char
   - Hash: bcrypt cost 12

3. **Rate Limiting:**
   - Guest: 100 req/hour per IP
   - User: 1000 req/hour
   - Admin: Unlimited (with monitoring)

4. **Audit Trail:**
   - All admin actions logged in `activity_logs`
   - Sensitive operations (refund approval) require 2FA
   - Track failed login attempts (lock after 5 attempts)

---

## Database Indexes for Role-Based Queries

```sql
-- Fast role checking
CREATE INDEX idx_users_role_status ON users(role_id, status);

-- Permission lookup
CREATE INDEX idx_role_permissions_lookup ON role_permissions(role_id, permission_id);

-- Guest session cleanup
CREATE INDEX idx_guest_sessions_expiry ON guest_sessions(expires_at, last_activity);

-- Admin activity monitoring
CREATE INDEX idx_activity_admin ON activity_logs(actor_type, actor_id, created_at) 
WHERE actor_type = 'admin';
```

---

## Sample Permissions Data

```sql
-- Booking permissions
('booking.create', 'Create new booking', 'booking'),
('booking.view_own', 'View own bookings', 'booking'),
('booking.view_all', 'View all bookings', 'booking'),
('booking.cancel_own', 'Cancel own booking', 'booking'),
('booking.cancel_any', 'Cancel any booking', 'booking'),

-- User permissions
('user.update_own', 'Update own profile', 'user'),
('user.view_all', 'View all users', 'user'),
('user.manage', 'Manage users', 'user'),

-- Admin permissions
('admin.access_dashboard', 'Access admin panel', 'admin'),
('admin.manage_products', 'Manage products', 'admin'),
('admin.process_refunds', 'Process refunds', 'admin'),
('admin.system_config', 'System configuration', 'admin'),

-- Payment permissions
('payment.view_own', 'View own payments', 'payment'),
('payment.view_all', 'View all payments', 'payment'),
('payment.process_refund', 'Process refunds', 'payment'),
```

---

## Role Assignment

```sql
-- Guest role (minimal permissions)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(1, (SELECT id FROM permissions WHERE slug = 'booking.create'));

-- User role
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, (SELECT id FROM permissions WHERE slug = 'booking.create')),
(2, (SELECT id FROM permissions WHERE slug = 'booking.view_own')),
(2, (SELECT id FROM permissions WHERE slug = 'booking.cancel_own')),
(2, (SELECT id FROM permissions WHERE slug = 'user.update_own')),
(2, (SELECT id FROM permissions WHERE slug = 'payment.view_own'));

-- Admin role (all permissions)
-- Assign all permissions to role_id 3
```
