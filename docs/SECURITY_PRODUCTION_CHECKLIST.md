# 🔒 Security Production Checklist

## ⚠️ CRITICAL - Must Fix Before Production

### 1. Authentication System
**Status:** ❌ Development Only  
**Location:** `app/views/auth/login.php`, `app/views/auth/register.php`

**Current Implementation:**
```php
// SIMULATION CODE - NOT FOR PRODUCTION
$validEmail = 'user@gmail.com';
$validPassword = 'password123';
if ($email === $validEmail && $password === $validPassword) {
    // Plain text comparison
}
```

**Required Changes:**
- [ ] Remove all simulation code
- [ ] Implement database-backed authentication via `AuthController`
- [ ] Use `password_verify()` with bcrypt hashes from database
- [ ] Integrate with production User model

**Reference:** Real implementation exists in `app/controllers/AuthController.php` (already secure)

---

### 2. Session Security Configuration
**Status:** ✅ Fixed (PR #71)

**Implemented:**
```php
// Session security flags now active in helpers/functions.php
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', '1'); // Auto-enabled on HTTPS
ini_set('session.use_strict_mode', '1');
session_regenerate_id(true); // Every 5 minutes
```

---

### 3. Booking Code Generation
**Status:** ✅ Fixed (PR #71)

**Before:**
```php
// Only 65,536 possibilities per day (2 bytes)
$bookingCode = 'TRV-' . date('ymd') . '-' . bin2hex(random_bytes(2));
```

**After:**
```php
// 16,777,216 possibilities per day (4 bytes)
$bookingCode = 'TRV-' . date('ymd') . '-' . bin2hex(random_bytes(4));
```

---

### 4. Environment Configuration
**Status:** ⚠️ Partially Done

**Current `.env` Requirements:**
```env
# Production mode
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE=true

# Strong credentials
DB_PASSWORD="strong_password_here"
APP_KEY=base64:GENERATE_32_CHAR_RANDOM_KEY

# Disable simulation
TREVIO_SIMULATE_LOGIN=false
```

**Action Items:**
- [ ] Generate strong `APP_KEY` with: `php -r "echo 'base64:'.base64_encode(random_bytes(32));"`
- [ ] Set `SESSION_SECURE=true` for HTTPS
- [ ] Change all default passwords
- [ ] Verify `TREVIO_SIMULATE_LOGIN=false`

---

### 5. Input Validation Enhancement
**Status:** ⚠️ Good, Can Be Better

**Current (Adequate):**
```php
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
```

**Recommended Additions:**
```php
// Add validation rules
if (strlen($password) < 8) {
    throw new ValidationException('Password too short');
}

if (!preg_match('/^[a-zA-Z0-9\s]+$/', $fullName)) {
    throw new ValidationException('Invalid name format');
}

// Rate limiting for login attempts (already implemented in AuthController)
```

---

### 6. CSRF Protection
**Status:** ✅ Excellent

**Verified Implementation:**
```php
// Token generation
trevio_csrf_token(); // helpers/functions.php

// Token validation
trevio_verify_csrf(); // Uses hash_equals() - timing safe

// Form inclusion
<?= trevio_csrf_field() ?>
```

---

### 7. XSS Prevention
**Status:** ✅ Good

**Verified:**
```php
// Consistent use of htmlspecialchars()
<?= htmlspecialchars($userInput) ?>

// Output escaping in all views
<?= htmlspecialchars($hotel['name']) ?>
```

---

### 8. CDN Dependencies
**Status:** ⚠️ Performance Issue

**Current:**
```html
<script src="https://cdn.tailwindcss.com"></script>
```

**Production Recommendation:**
- [ ] Install Tailwind CLI: `npm install -D tailwindcss`
- [ ] Build production CSS: `npx tailwindcss -i input.css -o public/css/tailwind.min.css --minify`
- [ ] Replace CDN with local: `<link href="/public/css/tailwind.min.css">`
- [ ] Reduces load time from ~200ms to ~20ms

---

### 9. Database Query Safety
**Status:** ✅ Excellent

**Verified in Models:**
```php
// All using prepared statements
$this->db->query("SELECT * FROM users WHERE email = :email");
$this->db->bind(':email', $email);
```

---

### 10. File Upload Security
**Status:** ✅ Good (with recommendations)

**Current Protection:**
```php
// File type validation
$allowTypes = array('jpg', 'png', 'jpeg', 'gif');
if (in_array($fileType, $allowTypes)) {
    // Validate is actually image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        move_uploaded_file(...);
    }
}
```

**Recommendations:**
- [ ] Add file size limits (already defined: 5MB)
- [ ] Store uploads outside webroot or block PHP execution
- [ ] Scan with antivirus in critical environments

---

## ✅ Security Strengths (Keep Doing)

1. **CSRF Protection:** Implemented consistently across all forms
2. **Output Escaping:** `htmlspecialchars()` used properly
3. **Prepared Statements:** No raw SQL concatenation found
4. **Timing-Safe Comparison:** `hash_equals()` for tokens
5. **Password Hashing:** `password_hash()` with bcrypt
6. **Session Regeneration:** Prevents session fixation
7. **Input Sanitization:** `filter_input()` used appropriately

---

## 📋 Pre-Deployment Checklist

### Code Review
- [x] Remove duplicate code blocks
- [x] Remove duplicate footer includes
- [x] Strengthen booking code generation
- [x] Add session security flags
- [x] Externalize simulation flags
- [ ] Replace simulation auth with real database calls

### Configuration
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Enable `SESSION_SECURE=true`
- [ ] Set strong database password
- [ ] Disable `TREVIO_SIMULATE_LOGIN`

### Infrastructure
- [ ] Enable HTTPS with valid SSL certificate
- [ ] Configure firewall rules
- [ ] Set up automated backups
- [ ] Enable error logging (not display)
- [ ] Configure rate limiting
- [ ] Set up monitoring/alerting

### Testing
- [ ] Run security scan (OWASP ZAP, Burp Suite)
- [ ] Test session timeout behavior
- [ ] Verify CSRF protection works
- [ ] Test file upload restrictions
- [ ] Penetration testing for SQL injection
- [ ] XSS vulnerability testing

---

## 📞 Contact

For security issues, contact: security@trevio.com

**Last Updated:** November 24, 2025  
**Version:** 1.0.0  
**Status:** Development → Pre-Production
