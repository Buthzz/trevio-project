# 🚀 Deployment Guide - Trevio Production

## 📋 Prerequisites

- Ubuntu 20.04+ / Debian 11+
- Nginx 1.18+
- PHP 8.1+
- MariaDB 10.5+ / MySQL 8.0+
- Git
- Domain sudah pointing ke server IP

---

## 🔧 Step 1: Install Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Nginx
sudo apt install nginx -y

# Install PHP 8.1 and extensions
sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-zip -y

# Install MariaDB
sudo apt install mariadb-server mariadb-client -y

# Secure MariaDB
sudo mysql_secure_installation
```

---

## 📂 Step 2: Clone Repository

```bash
# Navigate to web directory
cd /var/www

# Clone repository
sudo git clone https://github.com/Buthzz/trevio-project.git trevio-dev

# Set ownership
sudo chown -R www-data:www-data /var/www/trevio-dev

# Set permissions
sudo find /var/www/trevio-dev -type d -exec chmod 755 {} \;
sudo find /var/www/trevio-dev -type f -exec chmod 644 {} \;

# Make upload directories writable
sudo chmod -R 775 /var/www/trevio-dev/public/uploads
sudo chmod -R 775 /var/www/trevio-dev/logs
```

---

## 🗄️ Step 3: Setup Database

```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE trevio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'trevio_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';
GRANT ALL PRIVILEGES ON trevio.* TO 'trevio_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database schema
sudo mysql -u root -p trevio < /var/www/trevio-dev/database/trevio_final.sql

# Import seeders (optional - untuk test data)
sudo mysql -u root -p trevio < /var/www/trevio-dev/database/seeders.sql
```

---

## ⚙️ Step 4: Configure Environment

```bash
# Copy .env file
cd /var/www/trevio-dev
sudo cp .env.example .env

# Edit .env
sudo nano .env
```

**Update nilai berikut di `.env`:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://trevio-dev.mfjrxn.eu.org

DB_HOST=localhost
DB_DATABASE=trevio
DB_USERNAME=trevio_user
DB_PASSWORD=your_strong_password_here

SESSION_SECURE=true
SESSION_HTTP_ONLY=true

# Generate strong APP_KEY (32 characters random)
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
```

---

## 🌐 Step 5: Configure Nginx

```bash
# Copy nginx config
sudo cp /var/www/trevio-dev/.nginx.conf /etc/nginx/sites-available/trevio-dev

# Create symlink
sudo ln -s /etc/nginx/sites-available/trevio-dev /etc/nginx/sites-enabled/

# Remove default site (optional)
sudo rm /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## 🔐 Step 6: SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d trevio-dev.mfjrxn.eu.org

# Auto-renewal test
sudo certbot renew --dry-run
```

---

## 🔧 Step 7: PHP-FPM Configuration

```bash
# Edit PHP-FPM pool config
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

**Update settings:**

```ini
user = www-data
group = www-data
listen = /var/run/php/php8.1-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

```bash
# Edit PHP configuration
sudo nano /etc/php/8.1/fpm/php.ini
```

**Update settings:**

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

```bash
# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

---

## 📊 Step 8: Verify Installation

### Check Services Status

```bash
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mariadb
```

### Test Database Connection

```bash
# Run test script
curl https://trevio-dev.mfjrxn.eu.org/test-db.php
```

### Check Logs

```bash
# Nginx logs
sudo tail -f /var/log/nginx/trevio-dev-error.log
sudo tail -f /var/log/nginx/trevio-dev-access.log

# PHP logs
sudo tail -f /var/log/php_errors.log
```

---

## 🧪 Step 9: Test Login

1. **Admin Login:**
   - URL: `https://trevio-dev.mfjrxn.eu.org/auth/login`
   - Email: `admin@trevio.com`
   - Password: `password`

2. **Owner Login:**
   - Email: `owner1@trevio.com`
   - Password: `password`

3. **Customer Login:**
   - Email: `customer@trevio.com`
   - Password: `password`

---

## 🔄 Step 10: Git Pull & Update

```bash
# Navigate to project
cd /var/www/trevio-dev

# Pull latest changes
sudo git pull origin main

# Set permissions again
sudo chown -R www-data:www-data /var/www/trevio-dev
sudo chmod -R 775 /var/www/trevio-dev/public/uploads
sudo chmod -R 775 /var/www/trevio-dev/logs

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
```

---

## 🐛 Troubleshooting

### Problem: 404 Not Found

**Cause:** Nginx routing tidak benar atau document root salah

**Solution:**
```bash
# Pastikan document root di /public
root /var/www/trevio-dev/public;

# Restart Nginx
sudo systemctl restart nginx
```

### Problem: BASE_URL Undefined

**Cause:** `config/app.php` belum di-load

**Solution:**
```bash
# Check app/init.php sudah load config
# Pastikan baris ini ada:
require_once __DIR__ . '/../config/app.php';
```

### Problem: 500 Internal Server Error

**Cause:** PHP errors atau permission issues

**Solution:**
```bash
# Check PHP error log
sudo tail -f /var/log/php_errors.log

# Check Nginx error log
sudo tail -f /var/log/nginx/trevio-dev-error.log

# Fix permissions
sudo chown -R www-data:www-data /var/www/trevio-dev
sudo chmod -R 755 /var/www/trevio-dev
sudo chmod -R 775 /var/www/trevio-dev/public/uploads
sudo chmod -R 775 /var/www/trevio-dev/logs
```

### Problem: Database Connection Failed

**Cause:** Wrong credentials atau database tidak ada

**Solution:**
```bash
# Test manual connection
mysql -u trevio_user -p trevio

# Check .env credentials
sudo cat /var/www/trevio-dev/.env | grep DB_

# Grant privileges again
sudo mysql -u root -p
GRANT ALL PRIVILEGES ON trevio.* TO 'trevio_user'@'localhost';
FLUSH PRIVILEGES;
```

### Problem: Upload Failed

**Cause:** Permission issues

**Solution:**
```bash
# Create upload directories
sudo mkdir -p /var/www/trevio-dev/public/uploads/{hotels,rooms,payments,refunds,reviews}

# Set ownership
sudo chown -R www-data:www-data /var/www/trevio-dev/public/uploads

# Set permissions
sudo chmod -R 775 /var/www/trevio-dev/public/uploads
```

---

## 📝 Maintenance Commands

```bash
# Check disk space
df -h

# Check memory
free -m

# Monitor Nginx connections
sudo netstat -an | grep :80 | wc -l

# Clear PHP-FPM cache
sudo systemctl reload php8.1-fpm

# Restart all services
sudo systemctl restart nginx php8.1-fpm mariadb
```

---

## 🔒 Security Checklist

- [ ] Change default passwords di seeders
- [ ] Update APP_KEY di .env dengan random string
- [ ] Set SESSION_SECURE=true untuk HTTPS
- [ ] Disable directory listing
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Enable firewall (ufw)
- [ ] Regular backups database
- [ ] Update system packages regularly
- [ ] Monitor error logs
- [ ] Disable PHP display_errors di production

---

## 📦 Backup & Restore

### Backup Database

```bash
# Backup database
sudo mysqldump -u root -p trevio > trevio_backup_$(date +%Y%m%d).sql

# Backup files
sudo tar -czf trevio_files_$(date +%Y%m%d).tar.gz /var/www/trevio-dev
```

### Restore Database

```bash
# Restore from backup
sudo mysql -u root -p trevio < trevio_backup_20251123.sql
```

---

## 📞 Support

- Documentation: `/docs`
- GitHub Issues: https://github.com/Buthzz/trevio-project/issues
- Email: support@trevio.com

---

**Date:** November 23, 2025  
**Version:** 1.0.0  
**Environment:** Production
