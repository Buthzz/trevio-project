# 🏨 Trevio - Hotel Booking Management System

> Final Project - Web Application Programming | Ganjil 2025

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://php.net)
[![MariaDB](https://img.shields.io/badge/MariaDB-10.6+-003545?logo=mariadb&logoColor=white)](https://mariadb.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.0+-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

---

## 📋 Project Overview

**Trevio** adalah sistem manajemen pemesanan hotel yang memungkinkan:
- Multiple hotels dengan multiple owners
- Manual payment verification oleh admin
- Room slot management (automatic availability tracking)
- Multi-channel notifications (Email & WhatsApp)
- Reviews & rating system
- Complete refund workflow

---

## 🎯 Main Features

### **3 Main Transactions:**

#### 1️⃣ **Booking & Manual Payment Verification**
- Customer memilih hotel dan kamar
- Upload bukti transfer pembayaran
- Admin verifikasi payment secara manual
- Email invoice dikirim setelah konfirmasi
- WhatsApp notification ke owner

#### 2️⃣ **Room Slot Management** 
- Owner set total slot kamar saat create/edit room
- Slot otomatis ready untuk semua hari
- Slot berkurang otomatis saat booking confirmed
- Slot kembali saat booking cancelled/refunded
- **No calendar per-date management needed!**

#### 3️⃣ **Refund Processing**
- Customer request refund dengan bank info
- Admin review dan approve/reject
- Transfer manual oleh admin
- Upload bukti transfer refund

---

## 👥 User Roles

### **Customer**
- Register/Login (Email + Password)
- Browse & search hotels
- Check room availability (real-time slot check)
- Book multiple rooms
- Upload payment proof
- Receive email invoice
- View booking history
- Request refund
- Write reviews & ratings

### **Hotel Owner**
- Register/Login (Email + Password)
- Add/manage hotels
- Add/manage rooms (set default slot count)
- View bookings
- Check-in guests
- View reports (Chart.js)
- Receive WhatsApp notification for new bookings

### **Admin**
- Login (Email + Password)
- Verify/reject payments (view proof, add notes)
- Process refunds
- Manage users (activate/deactivate)
- Manage hotels (approve/reject)
- View global statistics & reports
- Moderate reviews

---

## 🛠️ Tech Stack

### **Backend:**
- PHP 8.2 (Native MVC Pattern)
- MariaDB 10.6+
- PHPMailer (email notifications)
- WhatsApp API (Fonnte)
- mPDF (PDF generation)

### **Frontend:**
- Tailwind CSS 3.0+ (styling)
- Chart.js (reports & statistics)
- Vanilla JavaScript
- Google Fonts

### **Deployment:**
- VPS Server (Ubuntu/CentOS)
- Nginx Web Server
- SSL Certificate (Let's Encrypt)

---

## 📊 Database Structure

**Main Tables:**

1. **users** - All users (customer, owner, admin)
2. **hotels** - Hotels (owned by owners)
3. **rooms** - Room types with slot count
4. **bookings** - All booking transactions
5. **payments** - Payment proofs & verification
6. **refunds** - Refund requests & processing
7. **reviews** - Customer reviews & ratings
8. **notifications** - Notification logs (email & WhatsApp)

See full schema: [database/trevio_final.sql](database/trevio_final.sql)

See ERD: [docs/ERD.png](docs/ERD.png) | [docs/ERD.md](docs/ERD.md)

---

## 🚀 Installation

### **Prerequisites:**
- PHP >= 8.2
- MariaDB >= 10.6
- Composer (optional, for dependencies)
- VPS Server with SSH access
- Nginx Web Server

### **1. Clone Repository**
```bash
git clone https://github.com/Buthzz/trevio-project.git
cd trevio-project
```

### **2. Configure Environment**
```bash
# Copy environment template
cp .env.example .env

# Edit configuration
nano .env
```

**Required configurations:**
```env
# Database
DB_HOST=localhost
DB_NAME=trevio
DB_USER=root
DB_PASS=your_password

# App
APP_URL=https://trevio.yourdomain.com
APP_ENV=production

# Email (Gmail SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM=noreply@trevio.com

# WhatsApp (Fonnte)
WHATSAPP_API_KEY=your_fonnte_api_key
WHATSAPP_ENABLED=false
WHATSAPP_PROVIDER=fonnte
WHATSAPP_API_URL=https://api.fonnte.com/send

# Google Maps (Optional)
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

### **3. Database Setup**
```bash
# Create database
mariadb -u root -p -e "CREATE DATABASE trevio"

# Import schema
mariadb -u root -p trevio < database/trevio_final.sql
```

### **4. Set Permissions**
```bash
# Set upload directory permissions
chmod -R 755 public/uploads
chmod -R 755 logs

# Set ownership (if on VPS)
chown -R www-data:www-data public/uploads
chown -R www-data:www-data logs
```

### **5. Configure Web Server**

**Nginx:**
```nginx
server {
    listen 80;
    server_name trevio.yourdomain.com;
    root /var/www/trevio/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### **6. Access Application**
```
http://your-domain.com
or
http://your-vps-ip
```

---

## 🔑 Default Login Credentials

**Admin:**
```
Email: admin@trevio.com
Password: password123
```

**Owner:**
```
Email: owner@trevio.com
Password: password123
```

**Customer:**
```
Email: customer@trevio.com
Password: password123
```

⚠️ **IMPORTANT:** Change these passwords after first login!

---

## 💡 Room Slot Management Logic

### **How It Works:**

#### **When Owner Creates Room:**
```php
// Example: Owner creates "Deluxe Room" with 5 slots
Room:
- room_type: "Deluxe Room"
- total_slots: 5        // Set by owner
- available_slots: 5    // Default = total_slots

// Slot is READY for ALL dates automatically!
// No need to set availability per date
```

#### **When Customer Books:**
```php
// Customer books 2 rooms
// Before booking:
available_slots = 5

// After booking confirmed:
available_slots = 5 - 2 = 3

// System automatically reduces slots
```

#### **When Booking Cancelled/Refunded:**
```php
// Restore slots
available_slots = 3 + 2 = 5

// Back to original
```

#### **Availability Check:**
```php
// When customer searches hotel for any date:
SELECT * FROM rooms 
WHERE hotel_id = ? 
AND available_slots >= num_rooms_requested
```

**Result:** Simple, no per-date calendar needed! ✅

---

## 📧 Notification System

### **Email Notifications (PHPMailer):**
Sent for:
- ✉️ Booking created (invoice attached)
- ✉️ Payment verified
- ✉️ Payment rejected
- ✉️ Refund completed

### **WhatsApp Notifications (Fonnte):**
Sent to **OWNER** only for:
- 📱 New booking confirmed
- 📱 Guest check-in today reminder

### **Setup:**

**Email (Gmail):**
1. Enable 2-Step Verification in Google Account
2. Generate App Password
3. Use in `.env` as `MAIL_PASSWORD`

**WhatsApp (Fonnte):**
1. Register at https://fonnte.com
2. Get API key
3. Use in `.env` as `WHATSAPP_API_KEY`

---

---

## 📊 Reports & Statistics

### **Owner Reports (Chart.js):**
- Revenue trend (line chart)
- Booking trend (bar chart)
- Occupancy rate (pie chart)
- Room type performance

### **Admin Reports:**
- Total users (by role)
- Total hotels
- Total bookings (by status)
- Revenue summary
- Top performing hotels

---

## 🧪 Testing

### **Test Accounts:**
```
Admin:
- Email: admin@trevio.com
- Password: password123

Owner:
- Email: owner@trevio.com
- Password: password123

Customer:
- Email: customer@trevio.com
- Password: password123
```

### **Test Scenarios:**

**Customer Flow:**
1. Register/Login (email or Google)
2. Search hotel
3. Check room availability
4. Book room
5. Upload payment proof
6. Wait admin verification
7. Receive invoice email
8. Write review after stay

**Owner Flow:**
1. Login
2. Add hotel
3. Add rooms (set slots)
4. View bookings
5. Check-in guest
6. View reports

**Admin Flow:**
1. Login
2. Verify payment
3. Reject payment (test)
4. Process refund
5. Manage users
6. View statistics

---

## 📂 Key Files

```
trevio/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php          # Login + Google OAuth
│   │   ├── BookingController.php       # Booking logic
│   │   └── OwnerRoomController.php     # Room slot management
│   ├── models/
│   │   ├── Room.php                    # Room model (slot logic)
│   │   └── Booking.php                 # Booking model
│   └── views/
│       └── ...
├── config/
│   ├── database.php                    # DB config
│   └── google-oauth.php                # Google OAuth config
├── libraries/
│   ├── PHPMailer/                      # Email library
│   ├── Mailer.php                      # Email wrapper
│   └── WhatsApp.php                    # WhatsApp wrapper
└── public/
    ├── index.php                       # Entry point
    └── uploads/                        # Upload directory
```

---

## 🚀 Deployment to VPS

### **Quick Deployment Guide:**

**1. Connect to VPS:**
```bash
ssh root@your-vps-ip
```

**2. Install LEMP Stack:**
```bash
# Update system
apt update && apt upgrade -y

# Install Nginx, MariaDB, PHP
apt install nginx mariadb-server php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml -y
```

**3. Clone & Setup:**
```bash
cd /var/www
git clone https://github.com/your-team/trevio.git
cd trevio
cp .env.example .env
nano .env  # Configure
```

**4. Database:**
```bash
mariadb -u root -p < database/trevio_final.sql
```

**5. Permissions:**
```bash
chown -R www-data:www-data /var/www/trevio
chmod -R 755 /var/www/trevio/public/uploads
```

**6. Nginx Config:**
```bash
nano /etc/nginx/sites-available/trevio
```

```nginx
server {
    listen 80;
    server_name trevio.yourdomain.com;
    root /var/www/trevio/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    error_log /var/log/nginx/trevio-error.log;
    access_log /var/log/nginx/trevio-access.log;
}
```

```bash
ln -s /etc/nginx/sites-available/trevio /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
systemctl restart php8.2-fpm
```

**7. SSL (Let's Encrypt):**
```bash
apt install certbot python3-certbot-apache -y
certbot --apache -d trevio.yourdomain.com
```

**Full guide:** See [docs/Deployment_Guide.md](docs/Deployment_Guide.md) for detailed VPS deployment steps.

---

## 🎨 UI/UX Features

- ✨ Responsive design (mobile-first)
- 🎨 Modern Tailwind CSS
- 📊 Interactive charts (Chart.js)
- 🖼️ Image galleries
- ⭐ Star rating system
- 📱 Mobile-friendly forms
- 🔍 Real-time search
- 🎯 Loading indicators

---

## 📝 Documentation

**📚 Documentation Hub:** [docs/INDEX.md](docs/INDEX.md)

### Core Documentation:
- [ERD Diagram (PNG)](docs/ERD.png) | [ERD (Markdown)](docs/ERD.md)
- [User Flow Documentation](docs/Userflow.md)
- [Deployment Guide](docs/Deployment_Guide.md)
- [Notification System](docs/notification-system.md)
- [Git Workflow](docs/git-workflow.md)
- [Task Division](docs/pembagian-tugas.md)
- [Security Checklist](docs/SECURITY_PRODUCTION_CHECKLIST.md)
- [Nginx Error Pages](docs/nginx-error-pages.md)

---

## 👨‍💻 Team Members

| Name | Role | Responsibilities |
|------|------|------------------|
| **Hendrik** | Project Manager & Full Stack | Backend core, MVC structure, coordination |
| **Fajar** | Backend & DevOps | Payment/Refund logic, database, deployment |
| **Syadat** | Backend & QA | Owner controllers, hotel/room CRUD, testing |
| **Zakaria** | UI/UX & Frontend | Customer views, search, Tailwind design |
| **Reno** | Frontend Developer | Owner/Admin dashboards, Chart.js, alerts |

See detailed task division: [docs/pembagian-tugas.md](docs/pembagian-tugas.md)

---

## 📊 Project Statistics

- **Total Files:** 100+
- **Lines of Code:** 10,000+
- **Database Tables:** 8+
- **Main Transactions:** 3 (Booking, Payment, Refund)
- **User Roles:** 3 (Customer, Owner, Admin)
- **Notification Channels:** 2 (Email + WhatsApp)

---

## 🐛 Known Issues & Solutions

### **Issue: Upload size limit**
```ini
# php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

### **Issue: Email not sending**
- Check Gmail app password
- Check SMTP settings
- Check firewall (port 587)

### **Issue: Permission denied on uploads**
```bash
chmod -R 755 public/uploads
chown -R www-data:www-data public/uploads
```

---

## 🔄 Version History

### **v1.0.0 (Current)**
- Initial release
- Manual payment verification system
- Automatic room slot management
- Email & WhatsApp notifications
- Reviews & rating system
- Multi-role dashboard (Admin, Owner, Customer)
- Refund processing workflow

---

## 📞 Support

For questions or issues:
- **Project Manager:** Hendrik - hendrik@email.com
- **Lecturer:** Moh. Kautsar Sophan , S.Kom., M.MT

---

## 📜 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

This project was created for educational purposes as part of the Web Application Programming final project at Universitas Nahdlatul Ulama Surabaya.

**© 2025 Trevio Development Team. All Rights Reserved.**

---

## 🙏 Acknowledgments

- Moh. Kautsar Sophan , S.Kom., M.MT (Lecturer)
- PHP Community
- Tailwind CSS Team
- Chart.js Contributors
- PHPMailer & mPDF Libraries
- Fonnte WhatsApp API

---

**Built with ❤️ by Trevio Team**