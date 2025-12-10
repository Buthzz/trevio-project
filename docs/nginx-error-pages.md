# Nginx Error Pages Configuration

## Overview

Konfigurasi Nginx untuk Trevio telah disesuaikan agar error pages (403, 404, 500) diarahkan ke file error yang ada di proyek melalui MVC routing.

---

## Custom Error Pages

Proyek Trevio memiliki 3 custom error pages:

1. **403 Forbidden** - `app/views/errors/403.php`
2. **404 Not Found** - `app/views/errors/404.php`
3. **500 Server Error** - `app/views/errors/500.php`

---

## Nginx Configuration

### Error Page Directives

```nginx
# Custom Error Pages
error_page 403 /errors/403;
error_page 404 /errors/404;
error_page 500 502 503 504 /errors/500;

# Route error pages through MVC
location ~ ^/errors/(403|404|500)$ {
    rewrite ^/errors/(.*)$ /index.php?url=errors/$1 last;
}
```

### PHP-FPM Configuration

**PENTING:** Tambahkan `fastcgi_intercept_errors on;` di location block PHP:

```nginx
location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;

    # Enable error page interception
    fastcgi_intercept_errors on;  # <-- WAJIB!

    # Other settings...
}
```

---

## How It Works

### Flow Diagram

```
User Request → Nginx → Error Occurs
                ↓
         error_page directive
                ↓
         /errors/403 (internal redirect)
                ↓
         location ~ ^/errors/
                ↓
         rewrite to /index.php?url=errors/403
                ↓
         MVC Router (public/index.php)
                ↓
         ErrorsController
                ↓
         app/views/errors/403.php
                ↓
         Custom Error Page Displayed
```

### Example Scenarios

#### 1. 404 Not Found
```
User visits: https://trevio.mfjrxn.eu.org/nonexistent-page
↓
Nginx: try_files fails
↓
Nginx: error_page 404 /errors/404
↓
Rewrite: /index.php?url=errors/404
↓
ErrorsController::error404()
↓
Display: app/views/errors/404.php
```

#### 2. 403 Forbidden
```
User tries: https://trevio.mfjrxn.eu.org/app/controllers/
↓
Nginx: location ~ ^/app/ { deny all; }
↓
Nginx: error_page 403 /errors/403
↓
Rewrite: /index.php?url=errors/403
↓
ErrorsController::error403()
↓
Display: app/views/errors/403.php
```

#### 3. 500 Server Error
```
PHP Fatal Error occurs
↓
PHP-FPM returns 500 status
↓
Nginx: fastcgi_intercept_errors on (catches it)
↓
Nginx: error_page 500 /errors/500
↓
Rewrite: /index.php?url=errors/500
↓
ErrorsController::error500()
↓
Display: app/views/errors/500.php
```

---

## Configuration Files

### 1. `.nginx.conf` (Production Template)

File ini untuk server production dengan template placeholders:
- `{{ssl_certificate_key}}`
- `{{ssl_certificate}}`
- `{{root}}`
- `{{php_fpm_port}}`
- dll.

**Lokasi:** `f:\trevio-project\.nginx.conf`

### 2. `nginx.conf` (Full Configuration)

File ini berisi konfigurasi lengkap untuk:
- **Production** (trevio.mfjrxn.eu.org)
- **Development** (trevio-dev.mfjrxn.eu.org)

**Lokasi:** `f:\trevio-project\nginx.conf`

---

## Implementation Steps

### Step 1: Update Nginx Config

Copy salah satu config file ke server:

```bash
# Untuk production dengan template
sudo cp .nginx.conf /etc/nginx/sites-available/trevio

# Atau untuk full config
sudo cp nginx.conf /etc/nginx/sites-available/trevio
```

### Step 2: Enable Site

```bash
sudo ln -s /etc/nginx/sites-available/trevio /etc/nginx/sites-enabled/
```

### Step 3: Test Configuration

```bash
sudo nginx -t
```

Expected output:
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### Step 4: Reload Nginx

```bash
sudo systemctl reload nginx
```

---

## Testing Error Pages

### Test 404 Error

```bash
curl -I https://trevio.mfjrxn.eu.org/nonexistent-page
```

Expected:
- Status: `404 Not Found`
- Custom error page displayed

### Test 403 Error

```bash
curl -I https://trevio.mfjrxn.eu.org/app/controllers/
```

Expected:
- Status: `403 Forbidden`
- Custom error page displayed

### Test 500 Error

Create a test PHP error:
```php
// public/test-500.php
<?php
trigger_error("Test 500 error", E_USER_ERROR);
```

Visit: `https://trevio.mfjrxn.eu.org/test-500.php`

Expected:
- Status: `500 Internal Server Error`
- Custom error page displayed

---

## Troubleshooting

### Problem: Error pages not showing

**Cause:** `fastcgi_intercept_errors` not enabled

**Solution:**
```nginx
location ~ \.php$ {
    # Add this line
    fastcgi_intercept_errors on;
}
```

### Problem: Default Nginx error page still showing

**Cause:** Error page routing not configured

**Solution:**
```nginx
# Add these lines
error_page 403 /errors/403;
error_page 404 /errors/404;
error_page 500 502 503 504 /errors/500;

location ~ ^/errors/(403|404|500)$ {
    rewrite ^/errors/(.*)$ /index.php?url=errors/$1 last;
}
```

### Problem: Infinite redirect loop

**Cause:** Error in error page itself

**Solution:**
- Check error page PHP files for errors
- Temporarily disable error_page directive to debug
- Check Nginx error log: `sudo tail -f /var/log/nginx/error.log`

---

## Key Differences: Production vs Development

| Setting | Production | Development |
|---------|-----------|-------------|
| **display_errors** | Off | On |
| **error_reporting** | 0 | E_ALL |
| **autoindex** | Off | On |
| **Custom Error Pages** | ✅ Enabled | ✅ Enabled |
| **fastcgi_intercept_errors** | ✅ On | ✅ On |

---

## Security Notes

1. **Error pages tidak expose sensitive info** - Custom error pages tidak menampilkan stack trace atau path
2. **PHP errors hidden di production** - `display_errors=Off` mencegah leak informasi
3. **Error logging tetap aktif** - Errors tetap dicatat di log file untuk debugging

---

## References

- Nginx error_page: http://nginx.org/en/docs/http/ngx_http_core_module.html#error_page
- fastcgi_intercept_errors: http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_intercept_errors

---

**Last Updated:** December 10, 2025  
**Version:** 1.0.0  
**Maintainer:** DevOps Team
