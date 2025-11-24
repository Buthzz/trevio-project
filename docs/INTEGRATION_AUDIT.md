# 🔍 Frontend-Backend Integration Audit

**Date:** November 24, 2025  
**Status:** ⚠️ CRITICAL ISSUES FOUND

---

## 🚨 Critical Issues

### 1. **Routing Conflict** (BLOCKER)
**Severity:** 🔴 Critical

**Problem:**
- Frontend views di `app/views/` akses langsung via file path
- Backend controllers di `app/controllers/` pakai MVC routing
- **KONFLIK:** Dua sistem routing berjalan bersamaan

**Evidence:**
```php
// Frontend (Wrong):
app/views/home/index.php  → Accessed directly
app/views/hotel/search.php → Accessed directly

// Backend (Correct):
/home → HomeController@index
/hotel/search → HotelController@search
```

**Impact:**
- Data dummy tetap di view, tidak dari database
- Controller tidak terpakai
- Security risk (direct file access)

---

### 2. **Data Dummy in Views** (BLOCKER)
**Severity:** 🔴 Critical

**Problem:**
- Semua data hardcoded di view files
- Database & models tidak terpakai
- Tidak ada data persistence

**Evidence:**
```php
// app/views/home/index.php (Line 12-45)
$hotels = [
    ['name' => 'Padma Hotel', ...], // DUMMY!
    ['name' => 'The Langham', ...], // DUMMY!
];

// Should be:
$hotels = $this->hotelModel->getAll(); // From DB!
```

**Files Affected:**
- `app/views/home/index.php` - Hotel dummy
- `app/views/hotel/search.php` - Search dummy
- `app/views/hotel/detail.php` - Detail dummy
- `app/views/booking/form.php` - Booking dummy
- `app/views/auth/login.php` - Auth simulation

---

### 3. **URL Structure Issues** (HIGH)
**Severity:** 🟠 High

**Current URLs (Inconsistent):**
```
❌ /app/views/home/index.php
❌ /app/views/hotel/search.php
❌ /app/views/auth/login.php
```

**Should Be (MVC Standard):**
```
✅ /home atau /
✅ /hotel/search
✅ /auth/login
```

---

### 4. **Controller-View Disconnection** (HIGH)
**Severity:** 🟠 High

**Problem:**
- Controllers exist but not used:
  - `HomeController.php` ✅ Exists
  - `HotelController.php` ✅ Exists
  - `AuthController.php` ✅ Exists
  - `BookingController.php` ✅ Exists
  
- Views render independently with dummy data

**Example:**
```php
// HomeController@index EXISTS but NOT CALLED
public function index() {
    $hotels = $this->hotelModel->getAll();
    $this->view('home/index', ['hotels' => $hotels]);
}

// Instead, view accessed directly:
http://localhost/app/views/home/index.php ❌
```

---

## 📋 Required Fixes (Priority Order)

### Phase 1: Routing Integration (DAY 1 - CRITICAL)

#### 1.1 Remove Direct View Access
```apache
# .htaccess - Block direct view access
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Block direct access to app/views
    RewriteRule ^app/views/ - [F,L]
    
    # Route all to index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

#### 1.2 Update All Internal Links
**Files to Update:**
- `app/views/layouts/header.php` (navigation)
- `app/views/layouts/footer.php` (footer links)
- All view files with hardcoded paths

**Before:**
```php
<a href="app/views/hotel/search.php">Cari Hotel</a>
```

**After:**
```php
<a href="<?= BASE_URL ?>/hotel/search">Cari Hotel</a>
```

---

### Phase 2: Data Integration (DAY 1-2)

#### 2.1 Remove Dummy Data from Views
**Pattern to Follow:**

**Before (app/views/home/index.php):**
```php
<?php
$hotels = [
    ['name' => 'Padma Hotel', ...],
    ['name' => 'The Langham', ...],
];
?>
```

**After (HomeController.php → view):**
```php
// Controller:
public function index() {
    $data = [
        'hotels' => $this->hotelModel->getAllActive(),
        'title' => 'Trevio - Find Your Perfect Stay'
    ];
    $this->view('home/index', $data);
}

// View:
<?php foreach ($data['hotels'] as $hotel): ?>
    <h3><?= htmlspecialchars($hotel['name']) ?></h3>
<?php endforeach; ?>
```

#### 2.2 Database Integration Checklist
- [ ] **Home:** Hotel list dari `hotels` table
- [ ] **Search:** Filter dari database dengan WHERE clause
- [ ] **Detail:** Hotel detail dari `hotels` JOIN `rooms`
- [ ] **Booking:** Create booking di `bookings` table
- [ ] **Auth:** Login query `users` table

---

### Phase 3: URL Standardization (DAY 2)

#### 3.1 URL Mapping

| Old URL (Wrong) | New URL (Correct) | Controller | Method |
|----------------|-------------------|------------|---------|
| `/app/views/home/index.php` | `/` atau `/home` | `HomeController` | `index()` |
| `/app/views/hotel/search.php` | `/hotel/search` | `HotelController` | `search()` |
| `/app/views/hotel/detail.php?id=1` | `/hotel/detail/1` | `HotelController` | `detail($id)` |
| `/app/views/booking/form.php` | `/booking/create` | `BookingController` | `create()` |
| `/app/views/auth/login.php` | `/auth/login` | `AuthController` | `login()` |
| `/app/views/auth/register.php` | `/auth/register` | `AuthController` | `register()` |

---

### Phase 4: Security Hardening (DAY 3)

#### 4.1 Input Validation
```php
// Booking form - validate all inputs
$roomId = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);
if (!$roomId) {
    throw new ValidationException('Invalid room ID');
}
```

#### 4.2 CSRF Protection
```php
// All forms must have:
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
```

#### 4.3 SQL Injection Prevention
```php
// Use prepared statements in all models
$this->db->query("SELECT * FROM hotels WHERE id = :id");
$this->db->bind(':id', $id, PDO::PARAM_INT);
```

---

## 🔧 Implementation Plan

### Step 1: Block Direct Access (10 min)
```bash
# Create/update .htaccess in public/
RewriteRule ^app/views/ - [F,L]
```

### Step 2: Fix Navigation Links (30 min)
```bash
# Update header.php and footer.php
# Replace all direct file paths with BASE_URL routes
```

### Step 3: Integrate HomeController (1 hour)
```bash
# Remove dummy data from app/views/home/index.php
# Fetch from HomeController → Hotel model → Database
```

### Step 4: Integrate Other Controllers (3 hours)
```bash
# Hotel: search, detail
# Auth: login, register
# Booking: create, confirm
```

### Step 5: Test All Routes (1 hour)
```bash
# Test matrix:
# - Home page loads with real data
# - Search works with filters
# - Detail shows correct hotel
# - Booking creates DB record
# - Auth validates credentials
```

---

## ✅ Success Criteria

**Phase 1 Complete When:**
- [ ] Direct view access returns 403 Forbidden
- [ ] All URLs follow `/controller/method/param` pattern
- [ ] Navigation links use BASE_URL

**Phase 2 Complete When:**
- [ ] No dummy data in any view file
- [ ] All data from database via models
- [ ] Models use prepared statements

**Phase 3 Complete When:**
- [ ] URL structure consistent (no .php extensions)
- [ ] SEO-friendly URLs (no query strings except filters)
- [ ] Clean routing logs in browser dev tools

**Phase 4 Complete When:**
- [ ] CSRF on all forms
- [ ] Input validation on all user inputs
- [ ] XSS protection with htmlspecialchars()
- [ ] SQL injection impossible (prepared statements)

---

## 📊 Current Status

| Component | Status | Integration | Database | Security |
|-----------|--------|-------------|----------|----------|
| Home | 🟡 Partial | ❌ Dummy data | ❌ Not connected | ⚠️ Direct access |
| Hotel Search | 🟡 Partial | ❌ Dummy data | ❌ Not connected | ⚠️ Direct access |
| Hotel Detail | 🟡 Partial | ❌ Dummy data | ❌ Not connected | ⚠️ Direct access |
| Booking | 🟡 Partial | ❌ Dummy data | ❌ Not connected | ⚠️ Direct access |
| Auth | 🟢 Good | ✅ Controller | ✅ Database | ✅ Secure |
| Admin | 🟢 Good | ✅ Controller | ✅ Database | ✅ Secure |
| Owner | 🟢 Good | ✅ Controller | ✅ Database | ✅ Secure |

**Overall:** 🔴 **30% Integrated** (Only admin/auth working properly)

---

## 🎯 Next Actions

### Immediate (Today):
1. Create `.htaccess` to block direct view access
2. Update `header.php` navigation to use MVC routes
3. Integrate `HomeController` with `Hotel` model

### Tomorrow:
4. Integrate `HotelController` (search, detail)
5. Integrate `BookingController`
6. Remove all dummy data from views

### Day 3:
7. Full security audit
8. End-to-end testing
9. Deploy to staging

---

## 📞 Questions to Answer

1. **Keep old view files?** → NO, migrate to MVC or archive
2. **Backward compatibility?** → NO, clean break (redirect old URLs)
3. **API endpoints needed?** → FUTURE (for mobile app)

---

**Prepared by:** AI Code Review  
**For:** Trevio Project Team  
**Branch:** `feature/homepage2` → `fix/mvc-integration`
