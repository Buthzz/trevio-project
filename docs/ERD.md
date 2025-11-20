# 📊 ERD - Trevio (Simplified)

## Database Tables: 9

```
┌─────────────────────────────────────────────────────────────┐
│                    TREVIO DATABASE ERD                       │
└─────────────────────────────────────────────────────────────┘

1. users (PK: id)
   ├─ email (UK)
   ├─ google_id (UK)
   ├─ role: customer/owner/admin
   └─ auth_provider: email/google

2. hotels (PK: id)
   ├─ FK: owner_id → users(id)
   ├─ is_verified (admin approval)
   └─ average_rating (calculated)

3. rooms (PK: id) ⭐ SLOT MANAGEMENT
   ├─ FK: hotel_id → hotels(id)
   ├─ total_slots (set by owner)
   └─ available_slots (auto reduce/restore)

4. bookings (PK: id)
   ├─ FK: customer_id → users(id)
   ├─ FK: hotel_id → hotels(id)
   ├─ FK: room_id → rooms(id)
   ├─ booking_code (UK)
   └─ booking_status (lifecycle)

5. payments (PK: id)
   ├─ FK: booking_id → bookings(id)
   ├─ FK: verified_by → users(id) [admin]
   ├─ payment_proof (upload)
   └─ payment_status (pending→verified)

6. refunds (PK: id)
   ├─ FK: booking_id → bookings(id)
   ├─ FK: payment_id → payments(id)
   ├─ FK: customer_id → users(id)
   ├─ FK: processed_by → users(id) [admin]
   └─ refund_status (workflow)

7. reviews (PK: id)
   ├─ FK: booking_id → bookings(id)
   ├─ FK: customer_id → users(id)
   ├─ FK: hotel_id → hotels(id)
   ├─ FK: approved_by → users(id) [admin]
   ├─ rating (1-5)
   └─ is_approved

8. notifications (PK: id)
   ├─ FK: user_id → users(id)
   ├─ FK: booking_id → bookings(id)
   ├─ send_email, send_whatsapp
   └─ email_sent, whatsapp_sent

9. admin_activities (PK: id)
   ├─ FK: admin_id → users(id)
   ├─ activity_type
   └─ audit trail
```

---

## Key Relationships

```
users (1) ──────< (N) hotels
   Owner can have multiple hotels

hotels (1) ──────< (N) rooms
   Hotel has multiple room types

rooms (1) ──────< (N) bookings
   Room can be booked multiple times

users (1) ──────< (N) bookings
   Customer can make multiple bookings

bookings (1) ────── (1) payments
   One booking has one payment

bookings (1) ────── (1) refunds
   One booking can have one refund

bookings (1) ──────< (N) reviews
   Booking can have multiple reviews (but unique per customer)

users (1) ──────< (N) notifications
   User receives multiple notifications

users (1) ──────< (N) admin_activities
   Admin performs multiple activities
```

---

## Slot Management Logic (Visual)

```
┌─────────────────────────────────────────────────┐
│           ROOM SLOT MANAGEMENT                  │
├─────────────────────────────────────────────────┤
│                                                 │
│  Owner Creates Room:                            │
│  ┌───────────────────────┐                      │
│  │ Deluxe Room           │                      │
│  │ total_slots: 10       │  ← Set by owner     │
│  │ available_slots: 10   │  ← Auto = total     │
│  └───────────────────────┘                      │
│                                                 │
│  Ready for ALL dates automatically! ✅          │
│                                                 │
│  Customer Books 2 Rooms:                        │
│  ┌───────────────────────┐                      │
│  │ available_slots: 10   │                      │
│  │         ↓             │                      │
│  │ Booking confirmed     │  ← Trigger fires    │
│  │         ↓             │                      │
│  │ available_slots: 8    │  ← Auto reduced     │
│  └───────────────────────┘                      │
│                                                 │
│  Booking Cancelled:                             │
│  ┌───────────────────────┐                      │
│  │ available_slots: 8    │                      │
│  │         ↓             │                      │
│  │ Booking cancelled     │  ← Trigger fires    │
│  │         ↓             │                      │
│  │ available_slots: 10   │  ← Auto restored    │
│  └───────────────────────┘                      │
│                                                 │
└─────────────────────────────────────────────────┘
```

---

## Transaction Flows in Database

### Transaction 1: Booking & Payment Verification
```
1. Customer creates booking
   → INSERT INTO bookings (status: pending_payment)
   
2. Customer uploads payment proof
   → INSERT INTO payments (status: uploaded)
   → UPDATE bookings SET status = 'pending_verification'
   
3. Admin verifies payment
   → UPDATE payments SET status = 'verified'
   → TRIGGER: UPDATE bookings SET status = 'confirmed'
   → TRIGGER: UPDATE rooms SET available_slots = available_slots - num_rooms
   
4. Send notifications
   → INSERT INTO notifications (email & whatsapp)
```

### Transaction 2: Room Management
```
1. Owner creates room
   → INSERT INTO rooms (total_slots: X, available_slots: X)
   
2. Availability check (customer searches)
   → SELECT * FROM rooms WHERE available_slots >= requested_rooms
   
3. Booking confirmed (automatic)
   → TRIGGER reduces available_slots
   
4. Booking cancelled (automatic)
   → TRIGGER restores available_slots
```

### Transaction 3: Refund Processing
```
1. Customer requests refund
   → INSERT INTO refunds (status: requested)
   
2. Admin approves
   → UPDATE refunds SET status = 'approved'
   
3. Admin transfers & uploads receipt
   → UPDATE refunds SET status = 'completed', receipt uploaded
   → UPDATE bookings SET status = 'refunded'
   → TRIGGER: UPDATE rooms SET available_slots = available_slots + num_rooms
   
4. Send notifications
   → INSERT INTO notifications
```

---

## Indexes for Performance

```sql
-- Users
INDEX idx_email (email)
INDEX idx_google_id (google_id)
INDEX idx_role (role)

-- Hotels
INDEX idx_owner (owner_id)
INDEX idx_city (city)
INDEX idx_active (is_active)

-- Rooms
INDEX idx_hotel (hotel_id)
INDEX idx_slots (available_slots)

-- Bookings
INDEX idx_customer (customer_id)
INDEX idx_hotel (hotel_id)
INDEX idx_status (booking_status)
INDEX idx_dates (check_in_date, check_out_date)

-- Payments
INDEX idx_booking (booking_id)
INDEX idx_status (payment_status)

-- Reviews
INDEX idx_hotel (hotel_id)
INDEX idx_approved (is_approved)
```

---

## Data Flow Summary

```
BOOKING LIFECYCLE:

pending_payment
     ↓ (customer uploads proof)
pending_verification
     ↓ (admin verifies)
confirmed ────────────────┐
     ↓                    │ (slots reduced)
checked_in               │
     ↓                    │
completed                │
                         │
OR:                      │
     ↓                    │
cancelled/refunded ──────┘ (slots restored)
```

---

**Visual ERD:** Create using MySQL Workbench or dbdiagram.io

**Export Command:**
```bash
mysql -u root -p trevio < database/trevio_simplified.sql
```

Then use **MySQL Workbench**: Database → Reverse Engineer → Export as PNG

Or use **dbdiagram.io** for quick visualization.