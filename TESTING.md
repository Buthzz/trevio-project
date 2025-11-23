# 🔍 Testing Guide - Trevio Database & Login

## ✅ Checklist Sebelum Test

### 1. **Database Setup**
- [ ] Database `trevio` sudah dibuat
- [ ] Import file `database/trevio_final.sql` 
- [ ] Tabel `users` ada dan terisi

### 2. **Environment Configuration**
- [ ] File `.env` sudah ada di root project
- [ ] Konfigurasi database sudah benar:
  ```env
  DB_HOST=localhost
  DB_PORT=3306
  DB_DATABASE=trevio
  DB_USERNAME=root
  DB_PASSWORD=
  ```

### 3. **File Permissions**
- [ ] Folder `public/uploads` writable
- [ ] Folder `logs` writable

---

## 🧪 Cara Testing

### **Step 1: Test Koneksi Database**

Akses: `http://localhost:8000/test-db.php`

**Apa yang dicek:**
- ✅ Environment variables loaded
- ✅ Database connection berhasil
- ✅ Tabel users exists
- ✅ Admin account exists
- ✅ Owner account exists
- ✅ Security configuration
- ✅ File permissions

**Expected Result:**
```
✓ Connected
✓ Users Table Exists
✓ 1 admin(s) found
✓ 1 owner(s) found
```

---

### **Step 2: Create Test Accounts (jika belum ada)**

Jika test menunjukkan "No admin found" atau "No owner found", jalankan SQL berikut:

#### **Admin Account**
```sql
INSERT INTO users (name, email, password, role, is_active, is_verified, auth_provider, created_at)
VALUES ('Admin Trevio', 'admin@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 1, 'email', NOW());
```

**Login Credentials:**
- Email: `admin@trevio.com`
- Password: `password`

#### **Owner Account**
```sql
INSERT INTO users (name, email, password, role, is_active, is_verified, auth_provider, created_at)
VALUES ('Owner Test', 'owner@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner', 1, 1, 'email', NOW());
```

**Login Credentials:**
- Email: `owner@trevio.com`
- Password: `password`

#### **Customer Account (Optional)**
```sql
INSERT INTO users (name, email, password, role, is_active, is_verified, auth_provider, created_at)
VALUES ('Customer Test', 'customer@trevio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 1, 1, 'email', NOW());
```

**Login Credentials:**
- Email: `customer@trevio.com`
- Password: `password`

---

### **Step 3: Test Login Admin**

1. Akses: `http://localhost:8000/auth/login`
2. Login dengan:
   - Email: `admin@trevio.com`
   - Password: `password`
3. **Expected Result:**
   - Redirect ke `/admin/dashboard`
   - Session `$_SESSION['user_role']` = 'admin'
   - Dapat akses halaman admin

**Test Cases:**
```php
✓ Login berhasil dengan credentials valid
✓ Redirect ke admin dashboard
✓ Session user_id, user_email, user_role tersimpan
✓ CSRF token valid
✓ Login attempts counter reset
```

---

### **Step 4: Test Login Owner**

1. Logout dari admin (akses `/auth/logout`)
2. Login dengan:
   - Email: `owner@trevio.com`
   - Password: `password`
3. **Expected Result:**
   - Redirect ke `/owner` atau `/owner/dashboard`
   - Session `$_SESSION['user_role']` = 'owner'
   - Dapat akses halaman owner

**Test Cases:**
```php
✓ Login berhasil dengan credentials valid
✓ Redirect ke owner dashboard
✓ Tidak dapat akses halaman admin (403)
✓ Session regenerate untuk keamanan
```

---

## 🔒 Security Features yang Sudah Diimplementasi

### **1. Authentication Security**
- ✅ Password hashing dengan `bcrypt` (PASSWORD_BCRYPT)
- ✅ Session regeneration setelah login (prevent session fixation)
- ✅ Login attempt limiting (max 5 attempts, block 15 menit)
- ✅ Role-based access control (admin/owner/customer)

### **2. Database Security**
- ✅ Prepared statements (PDO) - SQL injection prevention
- ✅ Parameter binding dengan type detection
- ✅ Real prepared statements (`ATTR_EMULATE_PREPARES = false`)
- ✅ Connection timeout 10 seconds
- ✅ Error logging tanpa expose credentials

### **3. CSRF Protection**
- ✅ CSRF token generation dengan `random_bytes(32)`
- ✅ Token expiration (3600 detik / 1 jam)
- ✅ Timing-safe comparison dengan `hash_equals()`
- ✅ Token regeneration setelah validasi

### **4. XSS Prevention**
- ✅ Input sanitization dengan `htmlspecialchars()`
- ✅ GET parameter sanitization
- ✅ `strip_tags()` untuk input text
- ✅ Security headers (X-XSS-Protection, X-Frame-Options)

### **5. Session Security**
- ✅ `session.cookie_httponly = 1` (prevent XSS cookie theft)
- ✅ Session lifetime 120 menit
- ✅ Secure cookies untuk HTTPS (production)
- ✅ Session regeneration on privilege escalation

---

## 🐛 Troubleshooting

### **Problem: "Database connection failed"**
**Solution:**
1. Cek MySQL/MariaDB service running
2. Verify `.env` DB credentials
3. Test manual connection: `mysql -u root -p trevio`

### **Problem: "BASE_URL not defined"**
**Solution:**
1. Pastikan `config/app.php` di-load di `app/init.php`
2. Check `public/index.php` memanggil `require '../app/init.php'`

### **Problem: "CSRF Validation Failed"**
**Solution:**
1. Clear session: Logout dan clear browser cookies
2. Check `$_SESSION['csrf_token']` exists
3. Verify form memiliki input hidden csrf_token

### **Problem: "Too many login attempts"**
**Solution:**
1. Wait 15 menit atau clear session manual
2. Delete session file di `C:\Windows\Temp\` (Windows)
3. Restart browser

### **Problem: "Class not found"**
**Solution:**
1. Check namespace: `namespace App\Controllers;`
2. Verify autoloader di `app/init.php`
3. Check case-sensitive path (Linux VPS)

---

## 📊 Test Checklist

### **Database Connection**
- [ ] PDO connection berhasil
- [ ] Query SELECT VERSION() berhasil
- [ ] Tabel users ditemukan
- [ ] Admin account exists
- [ ] Owner account exists

### **Login Flow - Admin**
- [ ] Form login tampil dengan CSRF token
- [ ] Login dengan valid credentials berhasil
- [ ] Redirect ke `/admin/dashboard`
- [ ] Session variables tersimpan correct
- [ ] Logout berhasil dan session cleared

### **Login Flow - Owner**
- [ ] Login dengan owner credentials berhasil
- [ ] Redirect ke `/owner`
- [ ] Dashboard owner menampilkan statistics
- [ ] Tidak bisa akses URL admin
- [ ] Logout berhasil

### **Security Testing**
- [ ] Login dengan password salah ditolak
- [ ] Login attempts increment correct
- [ ] CSRF token invalid ditolak
- [ ] SQL injection di email field prevented
- [ ] XSS di input fields prevented

---

## 🚀 Next Steps After Testing

1. ✅ Database connection OK
2. ✅ Login admin berhasil
3. ✅ Login owner berhasil
4. 🔄 Register customer (waiting for friend fix)
5. ⏭️ Test booking flow
6. ⏭️ Test payment verification
7. ⏭️ Test refund process

---

## 📝 Notes

- **Register flow** sementara di-skip, akan diperbaiki teman
- Test accounts menggunakan password: `password` (bcrypt hash)
- Semua security features sudah aktif di development mode
- Production deployment perlu update:
  - `APP_ENV=production` 
  - `SESSION_SECURE=true` (HTTPS)
  - Strong password di `.env`

---

## 🎯 Success Criteria

✅ **PASS** jika:
- Database connection successful
- Admin login → redirect to admin dashboard
- Owner login → redirect to owner dashboard
- Role-based access working
- CSRF protection active
- No SQL injection vulnerability
- Session security implemented

❌ **FAIL** jika:
- Database connection error
- Login redirect ke wrong page
- Session variables not set
- CSRF token bypass possible
- SQL injection possible

---

**Date:** November 23, 2025  
**Deadline:** November 24, 2025 @ 22:00  
**Sprint:** Day 1 - Foundation Layer Testing
