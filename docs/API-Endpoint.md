# 🔌 API Endpoints Documentation - Trevio

## Base URL
```
Development: http://localhost:8000
Production: https://trevio.yourdomain.com
```

---

## 🔐 Authentication Endpoints

### Register User
```http
POST /auth/register
Content-Type: application/x-www-form-urlencoded
```

**Request Body:**
```
name: string (required)
email: string (required, unique)
password: string (required, min: 8)
phone: string (required)
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Registration successful",
  "data": {
    "user_id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### Login User
```http
POST /auth/login
Content-Type: application/x-www-form-urlencoded
```

**Request Body:**
```
email: string (required)
password: string (required)
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user_id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

---

### Logout User
```http
GET /auth/logout
```

**Response:**
```json
{
  "status": "success",
  "message": "Logout successful"
}
```

---

## 🏨 Hotel Endpoints

### Search Hotels
```http
GET /hotel/search
```

**Query Parameters:**
```
city: string (required)
check_in: date (required, format: YYYY-MM-DD)
check_out: date (required, format: YYYY-MM-DD)
rooms: integer (optional, default: 1)
guests: integer (optional, default: 2)
min_price: decimal (optional)
max_price: decimal (optional)
star_rating: integer (optional, 1-5)
```

**Example:**
```
GET /hotel/search?city=Jakarta&check_in=2025-12-01&check_out=2025-12-03&rooms=1
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "hotels": [
      {
        "id": 1,
        "name": "Grand Hyatt Jakarta",
        "address": "Jl. M.H. Thamrin No.28-30",
        "city": "Jakarta",
        "star_rating": 5,
        "image_url": "...",
        "min_price": 1500000,
        "available_rooms": true
      }
    ],
    "total": 10
  }
}
```

---

### Get Hotel Detail
```http
GET /hotel/detail/{id}
```

**Parameters:**
```
id: integer (required, hotel_id)
check_in: date (required)
check_out: date (required)
```

**Example:**
```
GET /hotel/detail/1?check_in=2025-12-01&check_out=2025-12-03
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "hotel": {
      "id": 1,
      "name": "Grand Hyatt Jakarta",
      "description": "Luxury 5-star hotel...",
      "address": "...",
      "star_rating": 5,
      "facilities": ["WiFi", "Pool", "Spa"],
      "latitude": -6.195,
      "longitude": 106.822
    },
    "rooms": [
      {
        "id": 1,
        "room_type": "Deluxe Room",
        "description": "...",
        "capacity": 2,
        "bed_type": "King",
        "price_per_night": 1500000,
        "available": true,
        "amenities": ["AC", "TV", "Mini Bar"]
      }
    ]
  }
}
```

---

### Create Hotel Booking
```http
POST /hotel/book
Content-Type: application/x-www-form-urlencoded
Headers: Authorization required (logged in user)
```

**Request Body:**
```
hotel_id: integer (required)
room_id: integer (required)
check_in: date (required)
check_out: date (required)
num_rooms: integer (required)
guest_name: string (required)
guest_email: string (required)
guest_phone: string (required)
special_requests: text (optional)
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Booking created successfully",
  "data": {
    "booking_id": 1,
    "booking_code": "H20251201XXXX",
    "total_price": 3000000,
    "payment_url": "redirect to payment page"
  }
}
```

---

## ✈️ Flight Endpoints

### Search Flights
```http
GET /flight/search
```

**Query Parameters:**
```
from: string (required, city name)
to: string (required, city name)
departure_date: date (required, YYYY-MM-DD)
passengers: integer (optional, default: 1)
class: string (optional, Economy|Business|First)
```

**Example:**
```
GET /flight/search?from=Jakarta&to=Surabaya&departure_date=2025-12-01&passengers=2
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "flights": [
      {
        "id": 1,
        "flight_number": "GA-100",
        "airline": "Garuda Indonesia",
        "departure_city": "Jakarta",
        "arrival_city": "Surabaya",
        "departure_time": "2025-12-01 08:00:00",
        "arrival_time": "2025-12-01 09:30:00",
        "duration": 90,
        "price": 1200000,
        "class": "Economy",
        "available_seats": 180
      }
    ],
    "total": 5
  }
}
```

---

### Get Flight Detail
```http
GET /flight/detail/{id}
```

**Parameters:**
```
id: integer (required, flight_id)
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "flight_number": "GA-100",
    "airline": "Garuda Indonesia",
    "departure_airport": "CGK",
    "arrival_airport": "SUB",
    "departure_city": "Jakarta",
    "arrival_city": "Surabaya",
    "departure_time": "2025-12-01 08:00:00",
    "arrival_time": "2025-12-01 09:30:00",
    "duration": 90,
    "price": 1200000,
    "class": "Economy",
    "total_seats": 180,
    "available_seats": 180,
    "baggage_allowance": 20,
    "aircraft_type": "Boeing 737"
  }
}
```

---

### Create Flight Booking
```http
POST /flight/book
Content-Type: application/x-www-form-urlencoded
Headers: Authorization required
```

**Request Body:**
```
flight_id: integer (required)
num_passengers: integer (required)
guest_name: string (required)
guest_email: string (required)
guest_phone: string (required)
passengers: array (required)
  - name: string
  - id_number: string
  - dob: date
```

**Response:**
```json
{
  "status": "success",
  "message": "Flight booking created",
  "data": {
    "booking_id": 2,
    "booking_code": "F20251201XXXX",
    "total_price": 2400000,
    "payment_url": "redirect to payment"
  }
}
```

---

## 💳 Payment Endpoints

### Create Payment Invoice
```http
POST /payment/create
Content-Type: application/json
Headers: Authorization required
```

**Request Body:**
```json
{
  "booking_id": 1,
  "amount": 3000000,
  "payment_method": "credit_card"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "payment_id": 1,
    "xendit_invoice_id": "xxx-xxx-xxx",
    "xendit_payment_url": "https://checkout.xendit.co/web/xxx",
    "expired_at": "2025-12-01 10:00:00"
  }
}
```

---

### Payment Webhook (Xendit Callback)
```http
POST /payment/webhook
Content-Type: application/json
Headers: X-Callback-Token: [xendit_token]
```

**Request Body (from Xendit):**
```json
{
  "id": "xxx-xxx-xxx",
  "external_id": "booking_1",
  "status": "PAID",
  "amount": 3000000,
  "paid_at": "2025-12-01 09:45:00",
  "payment_method": "CREDIT_CARD"
}
```

**Response:**
```json
{
  "status": "success"
}
```

---

### Payment Success Page
```http
GET /payment/success/{booking_id}
```

**Response:** HTML page with booking details

---

### Payment Failed Page
```http
GET /payment/failed/{booking_id}
```

**Response:** HTML page with error details and retry option

---

## 📋 Booking Endpoints

### Get User Bookings
```http
GET /booking/history
Headers: Authorization required
```

**Query Parameters:**
```
type: string (optional, hotel|flight)
status: string (optional, pending|confirmed|cancelled|completed)
page: integer (optional, default: 1)
limit: integer (optional, default: 10)
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "bookings": [
      {
        "id": 1,
        "booking_code": "H20251201XXXX",
        "booking_type": "hotel",
        "service_name": "Grand Hyatt Jakarta",
        "check_in": "2025-12-01",
        "check_out": "2025-12-03",
        "total_price": 3000000,
        "booking_status": "confirmed",
        "payment_status": "paid",
        "created_at": "2025-11-28 10:00:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 3,
      "total_items": 25
    }
  }
}
```

---

### Get Booking Detail
```http
GET /booking/detail/{booking_code}
Headers: Authorization required
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "booking": {
      "id": 1,
      "booking_code": "H20251201XXXX",
      "booking_type": "hotel",
      "hotel": {
        "name": "Grand Hyatt Jakarta",
        "address": "..."
      },
      "room": {
        "room_type": "Deluxe Room"
      },
      "check_in": "2025-12-01",
      "check_out": "2025-12-03",
      "num_rooms": 1,
      "guest_name": "John Doe",
      "guest_email": "john@example.com",
      "guest_phone": "08123456789",
      "total_price": 3000000,
      "booking_status": "confirmed",
      "created_at": "2025-11-28 10:00:00"
    },
    "payment": {
      "payment_method": "credit_card",
      "payment_status": "paid",
      "paid_at": "2025-11-28 10:15:00"
    }
  }
}
```

---

### Cancel Booking
```http
POST /booking/cancel/{booking_id}
Headers: Authorization required
```

**Response:**
```json
{
  "status": "success",
  "message": "Booking cancelled successfully"
}
```

---

## 👨‍💼 Admin Endpoints

### Admin Dashboard Stats
```http
GET /admin/dashboard
Headers: Authorization required (admin only)
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "total_bookings": 150,
    "total_revenue": 450000000,
    "pending_bookings": 10,
    "completed_bookings": 120,
    "total_users": 500,
    "today_bookings": 5
  }
}
```

---

### Manage Hotels (CRUD)
```http
GET    /admin/hotels              # List all hotels
GET    /admin/hotels/{id}         # Get hotel detail
POST   /admin/hotels/create       # Create new hotel
POST   /admin/hotels/update/{id}  # Update hotel
POST   /admin/hotels/delete/{id}  # Delete hotel
```

---

### Manage Rooms (CRUD)
```http
GET    /admin/rooms              # List all rooms
POST   /admin/rooms/create       # Create new room
POST   /admin/rooms/update/{id}  # Update room
POST   /admin/rooms/delete/{id}  # Delete room
```

---

### Manage Flights (CRUD)
```http
GET    /admin/flights              # List all flights
POST   /admin/flights/create       # Create new flight
POST   /admin/flights/update/{id}  # Update flight
POST   /admin/flights/delete/{id}  # Delete flight
```

---

### View All Bookings
```http
GET /admin/bookings
```

**Query Parameters:**
```
type: string (optional)
status: string (optional)
date_from: date (optional)
date_to: date (optional)
```

---

### View Payment Reports
```http
GET /admin/payments
```

**Query Parameters:**
```
status: string (optional)
date_from: date (optional)
date_to: date (optional)
```

---

## 📝 Reviews Endpoints (Optional)

### Create Review
```http
POST /review/create
Headers: Authorization required
```

**Request Body:**
```
booking_id: integer (required)
reviewable_type: string (required, hotel|flight)
reviewable_id: integer (required)
rating: integer (required, 1-5)
review_text: text (optional)
```

---

### Get Reviews
```http
GET /review/{type}/{id}
```

**Parameters:**
```
type: string (hotel|flight)
id: integer (hotel_id or flight_id)
```

---

## 🚫 Error Responses

### Standard Error Format:
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### Common HTTP Status Codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation error)
- `401` - Unauthorized (not logged in)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Unprocessable Entity (validation failed)
- `500` - Internal Server Error

---

## 🔒 Authentication

### Session-based Authentication:
```php
// After login, session is created
$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = $role;

// Check authentication
if (!isset($_SESSION['user_id'])) {
    // Redirect to login
}

// Check admin authorization
if ($_SESSION['user_role'] !== 'admin') {
    // Access denied
}
```

---

## 📌 Notes

1. **All endpoints** return JSON except pages (HTML views)
2. **Authentication required** endpoints need active session
3. **Admin endpoints** require admin role
4. **Dates** format: `YYYY-MM-DD`
5. **Times** format: `YYYY-MM-DD HH:MM:SS`
6. **Prices** in IDR (Indonesian Rupiah)
7. **Pagination** default: 10 items per page

---

## 🧪 Testing with cURL

### Example: Search Hotels
```bash
curl -X GET "http://localhost:8000/hotel/search?city=Jakarta&check_in=2025-12-01&check_out=2025-12-03"
```

### Example: Login
```bash
curl -X POST http://localhost:8000/auth/login \
  -d "email=user@trevio.com" \
  -d "password=user123"
```

### Example: Create Booking (with session)
```bash
curl -X POST http://localhost:8000/hotel/book \
  -H "Cookie: PHPSESSID=xxx" \
  -d "hotel_id=1" \
  -d "room_id=1" \
  -d "check_in=2025-12-01" \
  -d "check_out=2025-12-03" \
  -d "num_rooms=1" \
  -d "guest_name=John Doe" \
  -d "guest_email=john@example.com" \
  -d "guest_phone=08123456789"
```

---

**Last Updated:** November 2025  
**Version:** 1.0  
**Maintained by:** Trevio Development Team