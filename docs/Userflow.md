# 📊 ERD & User Flow - Visual Implementation Guide

## 🗄️ PART 1: DATABASE ERD (Entity Relationship Diagram)

### **Relationships Summary:**

```
┌─────────┐
│  users  │
└────┬────┘
     │
     │ 1:N
     ↓
┌──────────┐       ┌─────────┐
│ bookings │──────→│ hotels  │
└────┬─────┘  N:1  └────┬────┘
     │                   │
     │ 1:1               │ 1:N
     ↓                   ↓
┌──────────┐       ┌─────────┐
│ payments │       │  rooms  │
└──────────┘       └─────────┘
     
┌──────────┐
│ bookings │──────→┌─────────┐
└──────────┘  N:1  │ flights │
                   └─────────┘

┌──────────┐
│ reviews  │──────→┌─────────┐
└──────────┘  N:1  │ hotels  │
                   └─────────┘
```

---

### **Detailed ERD Structure:**

#### **TABLE: users**
```
┌─────────────────────────────────────┐
│             USERS                   │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│     name (VARCHAR)                  │
│ UK  email (VARCHAR)                 │
│     password (VARCHAR)              │
│     phone (VARCHAR)                 │
│     role (ENUM: guest/user/admin)   │
│     profile_image (VARCHAR)         │
│     is_verified (BOOLEAN)           │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

#### **TABLE: hotels**
```
┌─────────────────────────────────────┐
│             HOTELS                  │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│     name (VARCHAR)                  │
│     description (TEXT)              │
│     address (TEXT)                  │
│     city (VARCHAR)                  │
│     province (VARCHAR)              │
│     country (VARCHAR)               │
│     postal_code (VARCHAR)           │
│     latitude (DECIMAL)              │
│     longitude (DECIMAL)             │
│     star_rating (TINYINT 1-5)       │
│     image_url (VARCHAR)             │
│     facilities (JSON)               │
│     is_active (BOOLEAN)             │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

#### **TABLE: rooms**
```
┌─────────────────────────────────────┐
│             ROOMS                   │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│ FK  hotel_id → hotels(id)           │
│     room_type (VARCHAR)             │
│     description (TEXT)              │
│     capacity (INT)                  │
│     bed_type (VARCHAR)              │
│     price_per_night (DECIMAL)       │
│     total_rooms (INT)               │
│     available_rooms (INT)           │
│     room_size (INT)                 │
│     amenities (JSON)                │
│     image_url (VARCHAR)             │
│     is_available (BOOLEAN)          │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

#### **TABLE: flights**
```
┌─────────────────────────────────────┐
│            FLIGHTS                  │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│ UK  flight_number (VARCHAR)         │
│     airline (VARCHAR)               │
│     departure_airport (VARCHAR)     │
│     arrival_airport (VARCHAR)       │
│     departure_city (VARCHAR)        │
│     arrival_city (VARCHAR)          │
│     departure_time (DATETIME)       │
│     arrival_time (DATETIME)         │
│     duration (INT minutes)          │
│     price (DECIMAL)                 │
│     class (ENUM)                    │
│     total_seats (INT)               │
│     available_seats (INT)           │
│     baggage_allowance (INT)         │
│     aircraft_type (VARCHAR)         │
│     is_active (BOOLEAN)             │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

#### **TABLE: bookings** ⭐ MAIN TRANSACTION
```
┌─────────────────────────────────────┐
│           BOOKINGS                  │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│ UK  booking_code (VARCHAR)          │
│ FK  user_id → users(id)             │
│     booking_type (ENUM: hotel/flight)│
│                                     │
│     -- Hotel fields --              │
│ FK  hotel_id → hotels(id)           │
│ FK  room_id → rooms(id)             │
│     check_in_date (DATE)            │
│     check_out_date (DATE)           │
│     num_rooms (INT)                 │
│                                     │
│     -- Flight fields --             │
│ FK  flight_id → flights(id)         │
│     num_passengers (INT)            │
│                                     │
│     -- Common fields --             │
│     total_price (DECIMAL)           │
│     booking_status (ENUM)           │
│     guest_name (VARCHAR)            │
│     guest_email (VARCHAR)           │
│     guest_phone (VARCHAR)           │
│     special_requests (TEXT)         │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

#### **TABLE: payments** ⭐ MAIN TRANSACTION
```
┌─────────────────────────────────────┐
│           PAYMENTS                  │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│ FK  booking_id → bookings(id)       │
│     payment_method (VARCHAR)        │
│     payment_provider (VARCHAR)      │
│     amount (DECIMAL)                │
│                                     │
│     -- Xendit fields --             │
│ UK  xendit_invoice_id (VARCHAR)     │
│     xendit_payment_url (TEXT)       │
│     xendit_external_id (VARCHAR)    │
│                                     │
│     payment_status (ENUM)           │
│     paid_at (TIMESTAMP)             │
│     expired_at (TIMESTAMP)          │
│     transaction_data (JSON)         │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

#### **TABLE: reviews** (Optional)
```
┌─────────────────────────────────────┐
│           REVIEWS                   │
├─────────────────────────────────────┤
│ PK  id (INT)                        │
│ FK  user_id → users(id)             │
│ FK  booking_id → bookings(id)       │
│     reviewable_type (ENUM)          │
│     reviewable_id (INT)             │
│     rating (TINYINT 1-5)            │
│     review_text (TEXT)              │
│     is_approved (BOOLEAN)           │
│     created_at (TIMESTAMP)          │
│     updated_at (TIMESTAMP)          │
└─────────────────────────────────────┘
```

---

### **CARA BUAT ERD VISUAL:**

#### **Option 1: MySQL Workbench (RECOMMENDED)**

1. **Import SQL:**
```bash
mysql -u root -p < trevio.sql
```

2. **Open MySQL Workbench:**
   - Database → Reverse Engineer
   - Select "trevio" database
   - Select all tables
   - Execute

3. **Export Diagram:**
   - File → Export → Export as PNG (for presentation)
   - File → Export → Export as PDF (for documentation)

#### **Option 2: dbdiagram.io (Online)**

Visit: https://dbdiagram.io/

Copy-paste this code:

```sql
Table users {
  id int [pk, increment]
  name varchar
  email varchar [unique]
  password varchar
  phone varchar
  role enum
  created_at timestamp
}

Table hotels {
  id int [pk, increment]
  name varchar
  city varchar
  star_rating int
  is_active boolean
  created_at timestamp
}

Table rooms {
  id int [pk, increment]
  hotel_id int [ref: > hotels.id]
  room_type varchar
  price_per_night decimal
  available_rooms int
  created_at timestamp
}

Table flights {
  id int [pk, increment]
  flight_number varchar [unique]
  airline varchar
  departure_city varchar
  arrival_city varchar
  departure_time datetime
  price decimal
  available_seats int
  created_at timestamp
}

Table bookings {
  id int [pk, increment]
  booking_code varchar [unique]
  user_id int [ref: > users.id]
  booking_type enum
  hotel_id int [ref: > hotels.id]
  room_id int [ref: > rooms.id]
  flight_id int [ref: > flights.id]
  total_price decimal
  booking_status enum
  created_at timestamp
}

Table payments {
  id int [pk, increment]
  booking_id int [ref: - bookings.id]
  payment_method varchar
  amount decimal
  xendit_invoice_id varchar [unique]
  payment_status enum
  paid_at timestamp
  created_at timestamp
}

Table reviews {
  id int [pk, increment]
  user_id int [ref: > users.id]
  booking_id int [ref: > bookings.id]
  reviewable_type enum
  reviewable_id int
  rating int
  created_at timestamp
}
```

Then: Export → Download as PNG/PDF

---

## 🔄 PART 2: USER FLOW DIAGRAMS

### **FLOW 1: Hotel Booking (Main Transaction)**
#### [User Flow Hotel Booking](docs/UserFlow_HotelBooking.png)
```
┌─────────────────────────────────────────────────────────────┐
│                   HOTEL BOOKING FLOW                        │
└─────────────────────────────────────────────────────────────┘

    START
      │
      ↓
┌──────────────┐
│  Home Page   │
└──────┬───────┘
       │
       ↓
┌──────────────────────┐
│  Enter Search Data:  │
│  - City              │
│  - Check-in Date     │
│  - Check-out Date    │
│  - Rooms             │
└──────┬───────────────┘
       │
       ↓ Click "Search"
       │
┌──────────────────────┐
│  Hotel List Page     │
│  (Search Results)    │
└──────┬───────────────┘
       │
       ↓ Click Hotel
       │
┌──────────────────────┐
│  Hotel Detail Page   │
│  - Info & Facilities │
│  - Available Rooms   │
│  - Reviews           │
└──────┬───────────────┘
       │
       ↓ Select Room & Click "Book"
       │
       ├─────→ Check Login? ───→ NO → Redirect to Login ──┐
       │                                                    │
       ↓ YES                                               │
       │                                         After Login ↓
┌──────────────────────┐                                   │
│  Booking Form Page   │←──────────────────────────────────┘
│  - Guest Details     │
│  - Special Requests  │
│  - Price Summary     │
└──────┬───────────────┘
       │
       ↓ Click "Continue to Payment"
       │
       ├─→ Backend: Create Booking (status: pending)
       │            Generate booking_code
       │            Reduce available_rooms
       │
       ↓
┌──────────────────────┐
│  Payment Gateway     │
│  (Xendit)            │
│  - Select Method     │
│  - Enter Payment     │
└──────┬───────────────┘
       │
       ↓ Payment Process
       │
       ├────────┬────────┐
       │        │        │
    SUCCESS  FAILED   EXPIRED
       │        │        │
       ↓        ↓        ↓
   Confirmed Cancelled Cancelled
       │
       ↓
┌──────────────────────┐
│  Success Page        │
│  - Booking Code      │
│  - E-voucher         │
│  - Booking Details   │
└──────────────────────┘
       │
       ↓
     END
```

---

### **FLOW 2: Flight Booking (Main Transaction)**
#### [User Flow Flight Booking](docs/UserFlow_FlightBooking.png)
```
┌─────────────────────────────────────────────────────────────┐
│                  FLIGHT BOOKING FLOW                        │
└─────────────────────────────────────────────────────────────┘

    START
      │
      ↓
┌──────────────┐
│  Home Page   │
└──────┬───────┘
       │
       ↓
┌──────────────────────┐
│  Enter Search Data:  │
│  - From City         │
│  - To City           │
│  - Departure Date    │
│  - Passengers        │
│  - Class             │
└──────┬───────────────┘
       │
       ↓ Click "Search Flights"
       │
┌──────────────────────┐
│  Flight List Page    │
│  (Search Results)    │
└──────┬───────────────┘
       │
       ↓ Click Flight
       │
┌──────────────────────┐
│  Flight Detail Page  │
│  - Flight Info       │
│  - Baggage           │
│  - Facilities        │
└──────┬───────────────┘
       │
       ↓ Click "Book This Flight"
       │
       ├─────→ Check Login? ───→ NO → Redirect to Login ──┐
       │                                                    │
       ↓ YES                                               │
       │                                         After Login ↓
┌──────────────────────┐                                   │
│  Passenger Form      │←──────────────────────────────────┘
│  For each passenger: │
│  - Name              │
│  - ID Number         │
│  - Date of Birth     │
│                      │
│  Contact Person:     │
│  - Email             │
│  - Phone             │
└──────┬───────────────┘
       │
       ↓ Click "Continue to Payment"
       │
       ├─→ Backend: Create Booking (status: pending)
       │            Generate booking_code
       │            Reduce available_seats
       │
       ↓
┌──────────────────────┐
│  Payment Gateway     │
│  (Xendit)            │
└──────┬───────────────┘
       │
       ↓ Payment Process
       │
       ├────────┬────────┐
       │        │        │
    SUCCESS  FAILED   EXPIRED
       │        │        │
       ↓        ↓        ↓
   Confirmed Cancelled Cancelled
       │
       ↓
┌──────────────────────┐
│  Success Page        │
│  - Booking Code      │
│  - E-ticket          │
│  - Flight Details    │
└──────────────────────┘
       │
       ↓
     END
```

---

### **FLOW 3: Payment Processing (Main Transaction)**
#### [User Flow Payment Processing](docs/UserFlow_PaymentProcessing.png)
```
┌─────────────────────────────────────────────────────────────┐
│                 PAYMENT PROCESSING FLOW                     │
└─────────────────────────────────────────────────────────────┘

    START (from booking page)
      │
      ↓
┌──────────────────────────┐
│  Backend Process:        │
│  1. Create payment record│
│  2. Call Xendit API      │
│  3. Create invoice       │
│  4. Get payment URL      │
└──────┬───────────────────┘
       │
       ↓
┌──────────────────────────┐
│  Redirect to Xendit      │
│  Payment Page            │
└──────┬───────────────────┘
       │
       ↓
┌──────────────────────────┐
│  User in Xendit Page:    │
│  1. Select payment method│
│     □ Credit Card        │
│     □ Bank Transfer      │
│     □ E-Wallet           │
│     □ Retail Outlet      │
│  2. Enter payment details│
│  3. Confirm payment      │
└──────┬───────────────────┘
       │
       ↓ Xendit processes payment
       │
       ├─────────────┬─────────────┬─────────────┐
       │             │             │             │
    SUCCESS       FAILED       EXPIRED      PENDING
       │             │             │             │
       ↓             ↓             ↓             ↓
       │             │             │        (waiting)
       │             │             │
┌──────┴────────┐   │             │
│ Xendit sends  │   │             │
│ webhook to    │   │             │
│ our server    │   │             │
└──────┬────────┘   │             │
       │             │             │
       ↓             ↓             ↓
┌──────────────────────────────────────────┐
│  Backend Webhook Handler:                │
│  1. Verify signature                     │
│  2. Update payment status                │
│  3. Update booking status                │
│  4. Send confirmation email              │
│                                          │
│  IF SUCCESS:                             │
│    payment_status = 'paid'               │
│    booking_status = 'confirmed'          │
│                                          │
│  IF FAILED/EXPIRED:                      │
│    payment_status = 'failed'/'expired'   │
│    booking_status = 'cancelled'          │
│    Restore room/seat availability        │
└──────┬───────────────────────────────────┘
       │
       ↓
┌──────────────────────────┐
│  Redirect User to:       │
│                          │
│  IF SUCCESS:             │
│    → Success Page        │
│    → Show booking details│
│    → Download e-ticket   │
│                          │
│  IF FAILED:              │
│    → Failed Page         │
│    → Show error          │
│    → Retry option        │
└──────────────────────────┘
       │
       ↓
     END
```

---

### **CARA BUAT USER FLOW VISUAL:**

#### **Tool Recommendations:**

1. **draw.io** (Free, Recommended)
   - Visit: https://app.diagrams.net/
   - Template: Flowchart
   - Export as PNG/PDF

2. **Figma** (Free for students)
   - Visit: https://figma.com
   - Use FigJam for flowcharts
   - Great for collaboration

3. **Lucidchart** (Free with limits)
   - Visit: https://lucidchart.com
   - Professional templates

4. **Miro** (Free for students)
   - Visit: https://miro.com
   - Good for brainstorming

---

### **SYMBOLS TO USE:**

```
┌─────┐
│START│  = Start/End (Rounded rectangle)
└─────┘

┌─────────┐
│ Process │  = Process/Action (Rectangle)
└─────────┘

   ╱ ╲
  ╱   ╲
 ╱ ? ? ╲  = Decision (Diamond)
╲       ╱
 ╲     ╱
  ╲   ╱

    │
    ↓      = Flow direction (Arrow)
    │

┌─────────┐
│ Input/  │  = User Input (Parallelogram)
│ Output  │
└─────────┘
```

---

### **Quick Tips for Presentation:**

1. **ERD**: Print in A3 size, easy to read
2. **User Flow**: Animate in PowerPoint for step-by-step
3. **Color Code**: 
   - Blue = User actions
   - Green = Success
   - Red = Error
   - Yellow = System process

---

**Files to Create:**
- `ERD_Trevio.png` (from MySQL Workbench)
- `UserFlow_Hotel_Booking.png` (from draw.io)
- `UserFlow_Flight_Booking.png` (from draw.io)
- `UserFlow_Payment.png` (from draw.io)

Save all to `/docs` folder in GitHub repo! 📁