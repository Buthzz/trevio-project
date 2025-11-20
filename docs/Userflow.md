# 🔄 REVISED USER FLOW - Hotel Booking Management System

## 🎯 System Overview

**Business Model:** Hotel Booking Management Platform (bukan aggregator)
- Multiple hotels managed by different owners
- Direct booking system with payment & refund
- Complete inventory management

---

## 📊 MAIN TRANSACTIONS (3)

### ✅ Transaction 1: **Hotel Booking & Payment**
Customer → Browse Hotels → Select Room → Book → Pay (Xendit) → Get Receipt

### ✅ Transaction 2: **Room Inventory Management**
Owner → Set Room Availability per Date → Update Pricing → Manage Bookings

### ✅ Transaction 3: **Refund Processing**
Customer → Request Refund → Admin Review → Approve → Transfer Back

---

## 👥 USER FLOWS BY ROLE

development
### 🛍️ **CUSTOMER FLOW**

### **FLOW 1: Hotel Booking (Main Transaction)**
#### [User Flow Hotel Booking](docs/UserFlow_HotelBooking.png)
main
```
┌─────────────────────────────────────────────────────────────┐
│ CUSTOMER: BOOKING FLOW (Main Transaction 1)                 │
└─────────────────────────────────────────────────────────────┘

START
  ↓
┌──────────────────────┐
│ Landing Page         │
│ - Browse Hotels      │
│ - Search by City     │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ Hotel List           │
│ Filter by:           │
│ - Location           │
│ - Price Range        │
│ - Star Rating        │
│ - Facilities         │
└──────┬───────────────┘
       ↓ Click Hotel
┌──────────────────────┐
│ Hotel Detail Page    │
│ - Hotel Info         │
│ - Facilities         │
│ - Reviews            │
│ - Available Rooms    │
└──────┬───────────────┘
       ↓ Select Room & Dates
┌──────────────────────┐
│ Check Availability   │
│ Input:               │
│ - Check-in Date      │
│ - Check-out Date     │
│ - Number of Rooms    │
│ - Number of Guests   │
└──────┬───────────────┘
       ↓ Check if available?
       ├─── NO → Show "Not Available" → Back to Hotel Detail
       ↓ YES
┌──────────────────────┐
│ Booking Form         │
│ - Guest Details      │
│ - Special Requests   │
│ - Price Breakdown:   │
│   * Room Price       │
│   * Tax (10%)        │
│   * Service (5%)     │
│   * Total Price      │
└──────┬───────────────┘
       ↓ Check if Logged In?
       ├─── NO → Redirect to Login → After Login, Back to Form
       ↓ YES
┌──────────────────────┐
│ Confirm Booking      │
│ Backend:             │
│ - Create booking     │
│ - Status: pending    │
│ - Generate code      │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ Payment Page         │
│ (Xendit Integration) │
│ - Credit Card        │
│ - Bank Transfer      │
│ - E-Wallet           │
└──────┬───────────────┘
       ↓ Payment Success?
       ├─── NO → Show Error → Retry or Cancel
       ↓ YES
┌──────────────────────┐
│ Success Page         │
│ - Booking Code       │
│ - Booking Details    │
│ - Download Receipt   │
│ - Email Confirmation │
│                      │
│ Backend:             │
│ - Update status:     │
│   confirmed          │
│ - Reduce availability│
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ My Bookings          │
│ - View History       │
│ - Download Receipt   │
│ - Request Refund     │
└──────────────────────┘
       ↓
     END
```

---

development
### 🛍️ **CUSTOMER: REFUND REQUEST FLOW**

=======
### **FLOW 2: Flight Booking (Main Transaction)**
#### [User Flow Flight Booking](docs/UserFlow_FlightBooking.png)
main
```
┌─────────────────────────────────────────────────────────────┐
│ CUSTOMER: REFUND REQUEST FLOW                               │
└─────────────────────────────────────────────────────────────┘

START (from My Bookings)
  ↓
┌──────────────────────┐
│ View Booking Detail  │
│ Status: confirmed    │
└──────┬───────────────┘
       ↓ Click "Request Refund"
┌──────────────────────┐
│ Check Refund Policy  │
│ - Before check-in?   │
│ - Cancellation fee?  │
└──────┬───────────────┘
       ↓ Confirm Request
┌──────────────────────┐
│ Refund Request Form  │
│ - Reason             │
│ - Bank Account Info: │
│   * Bank Name        │
│   * Account Number   │
│   * Account Name     │
└──────┬───────────────┘
       ↓ Submit
┌──────────────────────┐
│ Backend:             │
│ - Create refund      │
│ - Status: requested  │
│ - Notify admin       │
│ - Notify hotel owner │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ Confirmation Page    │
│ "Refund requested"   │
│ "Wait for admin      │
│  approval"           │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ Track Refund Status  │
│ - Requested          │
│ - Under Review       │
│ - Approved           │
│ - Processing         │
│ - Completed          │
└──────────────────────┘
       ↓
     END
```

---

development
### 🏨 **HOTEL OWNER FLOW**

=======
### **FLOW 3: Payment Processing (Main Transaction)**
#### [User Flow Payment Processing](docs/UserFlow_PaymentProcessing.png)
main
```
┌─────────────────────────────────────────────────────────────┐
│ HOTEL OWNER: INVENTORY MANAGEMENT (Main Transaction 2)      │
└─────────────────────────────────────────────────────────────┘

START
  ↓
┌──────────────────────┐
│ Owner Dashboard      │
│ - Total Bookings     │
│ - Rooms Sold         │
│ - Revenue            │
│ - Pending Check-ins  │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ Manage Hotels        │
│ (if owner has        │
│  multiple hotels)    │
└──────┬───────────────┘
       ↓ Select Hotel
┌──────────────────────┐
│ Hotel Management     │
│ Options:             │
│ 1. Room Availability │
│ 2. Bookings          │
│ 3. Check-in          │
│ 4. Reports           │
└──────┬───────────────┘
       │
       ├─── OPTION 1: ROOM AVAILABILITY ───┐
       │                                    ↓
       │                         ┌──────────────────────┐
       │                         │ Room Availability    │
       │                         │ Management           │
       │                         │                      │
       │                         │ View Calendar:       │
       │                         │ - Select Month       │
       │                         │ - View Availability  │
       │                         │   per Date           │
       │                         └──────┬───────────────┘
       │                                ↓ Click Date
       │                         ┌──────────────────────┐
       │                         │ Set Availability     │
       │                         │ For: [Date]          │
       │                         │                      │
       │                         │ Room Type: [Select]  │
       │                         │ Available: [Number]  │
       │                         │ Price: [Override]    │
       │                         │ Notes: [Text]        │
       │                         └──────┬───────────────┘
       │                                ↓ Save
       │                         ┌──────────────────────┐
       │                         │ Backend:             │
       │                         │ - Update/Create      │
       │                         │   room_availability  │
       │                         │ - Set available_count│
       │                         │ - Set price_override │
       │                         └──────────────────────┘
       │                                ↓
       │                         [Back to Calendar]
       │
       ├─── OPTION 2: VIEW BOOKINGS ───────┐
       │                                    ↓
       │                         ┌──────────────────────┐
       │                         │ Booking List         │
       │                         │ Filter:              │
       │                         │ - Date Range         │
       │                         │ - Status             │
       │                         │ - Room Type          │
       │                         │                      │
       │                         │ Show:                │
       │                         │ - Booking Code       │
       │                         │ - Guest Name         │
       │                         │ - Check-in/out       │
       │                         │ - Room Type          │
       │                         │ - Total Price        │
       │                         │ - Status             │
       │                         └──────┬───────────────┘
       │                                ↓ Click Booking
       │                         ┌──────────────────────┐
       │                         │ Booking Detail       │
       │                         │ - Full Info          │
       │                         │ - Guest Details      │
       │                         │ - Payment Status     │
       │                         │ - Actions:           │
       │                         │   * Check-in         │
       │                         │   * Contact Guest    │
       │                         └──────────────────────┘
       │
       ├─── OPTION 3: CHECK-IN GUESTS ─────┐
       │                                    ↓
       │                         ┌──────────────────────┐
       │                         │ Check-in Page        │
       │                         │                      │
       │                         │ Input Booking Code:  │
       │                         │ [Text Field]         │
       │                         │                      │
       │                         │ OR Scan QR Code      │
       │                         └──────┬───────────────┘
       │                                ↓ Submit
       │                         ┌──────────────────────┐
       │                         │ Verify Booking       │
       │                         │ - Check if exists    │
       │                         │ - Check if confirmed │
       │                         │ - Check date valid   │
       │                         └──────┬───────────────┘
       │                                ↓ Valid?
       │                                ├─── NO → Show Error
       │                                ↓ YES
       │                         ┌──────────────────────┐
       │                         │ Show Guest Info      │
       │                         │ - Name               │
       │                         │ - Room Type          │
       │                         │ - Nights             │
       │                         │ - Verify ID          │
       │                         └──────┬───────────────┘
       │                                ↓ Confirm Check-in
       │                         ┌──────────────────────┐
       │                         │ Backend:             │
       │                         │ - Update status:     │
       │                         │   checked_in         │
       │                         │ - Set checked_in_at  │
       │                         │ - Set checked_in_by  │
       │                         │   (owner_id)         │
       │                         └──────┬───────────────┘
       │                                ↓
       │                         ┌──────────────────────┐
       │                         │ Success!             │
       │                         │ "Guest checked in"   │
       │                         └──────────────────────┘
       │
       └─── OPTION 4: REPORTS ─────────────┐
                                            ↓
                                 ┌──────────────────────┐
                                 │ Owner Reports        │
                                 │                      │
                                 │ Date Range: [Select] │
                                 │                      │
                                 │ Metrics:             │
                                 │ - Rooms Sold         │
                                 │ - Total Revenue      │
                                 │ - Total Refunds      │
                                 │ - Net Revenue        │
                                 │ - Occupancy Rate     │
                                 │ - Avg. Price/Night   │
                                 │                      │
                                 │ Charts:              │
                                 │ - Revenue Trend      │
                                 │ - Booking Trend      │
                                 │ - Room Type Performance│
                                 │                      │
                                 │ Export: [PDF/Excel]  │
                                 └──────────────────────┘
                                            ↓
                                          END
```

---

### 👨‍💼 **ADMIN FLOW**

```
┌─────────────────────────────────────────────────────────────┐
│ ADMIN: REFUND PROCESSING (Main Transaction 3)               │
└─────────────────────────────────────────────────────────────┘

START
  ↓
┌──────────────────────┐
│ Admin Dashboard      │
│ - Total Users        │
│ - Total Hotels       │
│ - Total Bookings     │
│ - Total Revenue      │
│ - Pending Refunds    │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ Admin Menu:          │
│ 1. Manage Users      │
│ 2. Manage Hotels     │
│ 3. View All Bookings │
│ 4. Process Refunds   │
│ 5. Global Reports    │
└──────┬───────────────┘
       │
       ├─── OPTION 4: PROCESS REFUNDS ─────┐
       │                                    ↓
       │                         ┌──────────────────────┐
       │                         │ Refund Requests List │
       │                         │                      │
       │                         │ Filter by Status:    │
       │                         │ - Requested          │
       │                         │ - Under Review       │
       │                         │ - Approved           │
       │                         │ - Completed          │
       │                         │ - Rejected           │
       │                         │                      │
       │                         │ Show:                │
       │                         │ - Booking Code       │
       │                         │ - Customer Name      │
       │                         │ - Hotel Name         │
       │                         │ - Refund Amount      │
       │                         │ - Reason             │
       │                         │ - Requested Date     │
       │                         │ - Status             │
       │                         └──────┬───────────────┘
       │                                ↓ Click Refund
       │                         ┌──────────────────────┐
       │                         │ Refund Detail        │
       │                         │                      │
       │                         │ Booking Info:        │
       │                         │ - Code               │
       │                         │ - Hotel              │
       │                         │ - Customer           │
       │                         │ - Amount Paid        │
       │                         │                      │
       │                         │ Refund Info:         │
       │                         │ - Requested Amount   │
       │                         │ - Reason             │
       │                         │ - Bank Account Info  │
       │                         │                      │
       │                         │ Actions:             │
       │                         │ [Approve] [Reject]   │
       │                         └──────┬───────────────┘
       │                                ↓ Select Action
       │                                │
       │               ┌────────────────┴─────────────────┐
       │               ↓                                  ↓
       │       [APPROVE REFUND]                   [REJECT REFUND]
       │               ↓                                  ↓
       │    ┌──────────────────────┐         ┌──────────────────────┐
       │    │ Process Refund       │         │ Rejection Form       │
       │    │                      │         │                      │
       │    │ Confirm Details:     │         │ Rejection Reason:    │
       │    │ - Bank: [Show]       │         │ [Text Area]          │
       │    │ - Account: [Show]    │         │                      │
       │    │ - Amount: [Show]     │         │ [Submit]             │
       │    │                      │         └──────┬───────────────┘
       │    │ Admin Notes:         │                ↓
       │    │ [Text]               │         ┌──────────────────────┐
       │    │                      │         │ Backend:             │
       │    │ [Confirm Transfer]   │         │ - Update refund:     │
       │    └──────┬───────────────┘         │   status = rejected  │
       │           ↓                         │ - Add admin_notes    │
       │    ┌──────────────────────┐         │ - Notify customer    │
       │    │ Backend:             │         └──────────────────────┘
       │    │ - Update refund:     │                ↓
       │    │   status = processing│              [END]
       │    │ - Process transfer   │
       │    │   (manual/API)       │
       │    │ - Update booking:    │
       │    │   status = refunded  │
       │    │ - Set refund_amount  │
       │    │ - Restore room       │
       │    │   availability       │
       │    │ - Generate receipt   │
       │    │ - Notify customer    │
       │    │ - Notify hotel owner │
       │    └──────┬───────────────┘
       │           ↓
       │    ┌──────────────────────┐
       │    │ Upload Receipt       │
       │    │ [File Upload]        │
       │    └──────┬───────────────┘
       │           ↓
       │    ┌──────────────────────┐
       │    │ Mark as Completed    │
       │    │ - status = completed │
       │    └──────┬───────────────┘
       │           ↓
       │    ┌──────────────────────┐
       │    │ Success!             │
       │    │ "Refund processed"   │
       │    └──────────────────────┘
       │           ↓
       │         [END]
       │
       └─── OPTION 5: GLOBAL REPORTS ──────┐
                                            ↓
                                 ┌──────────────────────┐
                                 │ Admin Global Reports │
                                 │                      │
                                 │ Date Range: [Select] │
                                 │                      │
                                 │ Summary:             │
                                 │ - Total Customers    │
                                 │ - Total Owners       │
                                 │ - Total Hotels       │
                                 │ - Total Bookings     │
                                 │ - Total Rooms Sold   │
                                 │ - Total Revenue      │
                                 │ - Total Refunds      │
                                 │ - Net Revenue        │
                                 │                      │
                                 │ By Hotel:            │
                                 │ - Top Performers     │
                                 │ - Revenue by Hotel   │
                                 │ - Occupancy Rates    │
                                 │                      │
                                 │ By Location:         │
                                 │ - Revenue by City    │
                                 │ - Bookings by City   │
                                 │                      │
                                 │ Trends:              │
                                 │ - Daily Revenue      │
                                 │ - Monthly Bookings   │
                                 │ - Refund Rate        │
                                 │                      │
                                 │ Export: [PDF/Excel]  │
                                 └──────────────────────┘
                                            ↓
                                          END
```

---

## 🔄 **KEY INTERACTIONS BETWEEN ROLES**

### Customer ↔ Hotel Owner:
1. Customer books → Owner sees in dashboard
2. Customer arrives → Owner checks in
3. Customer requests refund → Owner notified

### Customer ↔ Admin:
1. Customer requests refund → Admin reviews
2. Admin approves → Customer receives money back
3. Admin rejects → Customer notified with reason

### Hotel Owner ↔ Admin:
1. Owner sets availability → Visible to customers
2. Owner sees refund requests → Admin processes
3. Admin monitors hotel performance → Owner sees own data

---

## 📋 **BUSINESS RULES**

### Booking Rules:
- Check-in must be today or future date
- Check-out must be after check-in
- Minimum 1 night stay
- Maximum 30 nights per booking
- Room availability checked in real-time

### Payment Rules:
- Full payment upfront
- Tax: 10% of subtotal
- Service charge: 5% of subtotal
- Payment expires in 24 hours

### Refund Rules:
- Must request before check-in date
- Cancellation fee: 10% of total (optional policy)
- Refund processed within 7 business days
- Refund to original payment method or bank account

### Check-in Rules:
- Can check-in on scheduled date only
- Must have confirmed booking
- Valid booking code required
- ID verification required

---

## 📊 **DATA FLOW SUMMARY**

```
CUSTOMER BOOKS
     ↓
PAYMENT PROCESSED (Xendit)
     ↓
BOOKING CONFIRMED
     ↓
ROOM AVAILABILITY REDUCED
     ↓
OWNER NOTIFIED
     ↓
CUSTOMER ARRIVES
     ↓
OWNER CHECKS IN
     ↓
BOOKING STATUS: CHECKED_IN
     ↓
AFTER CHECK-OUT
     ↓
BOOKING STATUS: COMPLETED
     ↓
CUSTOMER CAN REVIEW

-- OR --

CUSTOMER REQUESTS REFUND
     ↓
ADMIN REVIEWS REQUEST
     ↓
ADMIN APPROVES
     ↓
TRANSFER PROCESSED
     ↓
ROOM AVAILABILITY RESTORED
     ↓
BOOKING STATUS: REFUNDED
     ↓
CUSTOMER & OWNER NOTIFIED
```

---

**This revised flow eliminates the need for external APIs and creates a complete, manageable business system!** ✅