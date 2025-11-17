# 🏨✈️ Trevio - Travel Booking Platform

> Platform pemesanan hotel dan tiket pesawat berbasis web - Final Project Web Application Programming

[![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.0+-06B6D4.svg)](https://tailwindcss.com)

---

## 📋 Project Overview

**Trevio** adalah aplikasi web pemesanan hotel dan tiket pesawat yang terinspirasi dari Traveloka. Sistem ini memungkinkan pengguna untuk mencari, membandingkan, dan memesan akomodasi hotel serta tiket penerbangan dengan mudah dan aman.

### 🎯 Main Features

#### **3 Main Transactions (Required):**
1. **Hotel Booking** - Pemesanan kamar hotel dengan berbagai tipe
2. **Flight Booking** - Pemesanan tiket pesawat (one-way/round-trip)
3. **Payment Processing** - Pembayaran menggunakan Xendit Payment Gateway (Sandbox)

#### **Additional Features:**
- User authentication & authorization (Guest, User, Admin)
- Search & filter (hotel by location, flight by route & date)
- Booking history & management
- Admin dashboard (manage hotels, flights, bookings)
- Review & rating system (optional bonus)

---

## 👥 Team Members & Responsibilities

| Name | Role | Responsibilities |
|------|------|------------------|
| **Hendrik** | Project Manager & Full Stack Dev | Project coordination, backend core features, code review |
| **Fajar** | Full Stack Dev & DevOps | Backend development, deployment, server setup, database optimization |
| **Syadat** | User Flow & QA | data integrity, testing & quality assurance |
| **Zek** | UI/UX Designer | Interface design, user experience, prototyping |
| **Reno** | Frontend Developer | Frontend implementation, Tailwind CSS integration, responsive design |

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.0+ (Native MVC Pattern)
- **Frontend:** HTML5, Tailwind CSS 3.0+, Vanilla JavaScript
- **Database:** MySQL 8.0+
- **Payment Gateway:** Xendit (Sandbox Mode)
- **Version Control:** Git & GitHub
- **Deployment:** Shared Hosting / VPS (TBA)

---

## 🗂️ Project Structure (MVC)

```
trevio/
├── app/
│   ├── controllers/          # Controller layer
│   │   ├── AuthController.php
│   │   ├── HotelController.php
│   │   ├── FlightController.php
│   │   ├── BookingController.php
│   │   ├── PaymentController.php
│   │   └── AdminController.php
│   │
│   ├── models/               # Model layer (database interaction)
│   │   ├── User.php
│   │   ├── Hotel.php
│   │   ├── Room.php
│   │   ├── Flight.php
│   │   ├── Booking.php
│   │   ├── Payment.php
│   │   └── Review.php
│   │
│   └── views/                # View layer (UI templates)
│       ├── layouts/
│       │   ├── header.php
│       │   ├── footer.php
│       │   └── navbar.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── home/
│       │   └── index.php
│       ├── hotel/
│       │   ├── search.php
│       │   ├── detail.php
│       │   └── booking.php
│       ├── flight/
│       │   ├── search.php
│       │   ├── detail.php
│       │   └── booking.php
│       ├── booking/
│       │   ├── history.php
│       │   └── detail.php
│       ├── payment/
│       │   ├── checkout.php
│       │   └── success.php
│       └── admin/
│           └── dashboard.php
│
├── config/                   # Configuration files
│   ├── database.php
│   ├── xendit.php
│   └── app.php
│
├── core/                     # Core MVC framework classes
│   ├── App.php              # Router & App initialization
│   ├── Controller.php       # Base controller
│   ├── Database.php         # Database connection handler
│   └── Model.php            # Base model
│
├── public/                   # Public accessible files
│   ├── css/
│   │   └── style.css        # Custom CSS (complement Tailwind)
│   ├── js/
│   │   └── main.js          # Main JavaScript
│   ├── images/
│   └── index.php            # Entry point
│
├── database/
│   ├── trevio.sql           # Database schema
│   └── seeders.sql          # Sample data
│
├── docs/                     # Documentation
│   ├── ERD.png
│   ├── User_Flow.png
│   └── API_Endpoints.md
│
├── .gitignore
├── .htaccess                 # URL rewriting
└── README.md
```

---

## 🗄️ Database Schema (ERD)

**5+ Tables Required:**

1. **users** - User accounts (guest, user, admin)
2. **hotels** - Hotel master data
3. **rooms** - Hotel room types & availability
4. **flights** - Flight schedules & routes
5. **bookings** - All booking records (hotel + flight)
6. **payments** - Payment transactions
7. **reviews** - (Optional) User reviews & ratings

*See detailed ERD in `/docs/ERD.png`*

---

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.0
- MySQL >= 8.0
- Composer (optional)
- Git

### Installation

1. **Clone Repository**
   ```bash
   git clone https://github.com/your-team/trevio.git
   cd trevio
   ```

2. **Database Setup**
   ```bash
   # Import database
   mysql -u root -p < database/trevio.sql
   
   # Import sample data (optional)
   mysql -u root -p trevio < database/seeders.sql
   ```

3. **Configuration**
   ```bash
   # Copy and edit config files
   cp config/database.example.php config/database.php
   cp config/xendit.example.php config/xendit.php
   
   # Edit with your credentials
   nano config/database.php
   nano config/xendit.php
   ```

4. **Run Development Server**
   ```bash
   cd public
   php -S localhost:8000
   ```

5. **Access Application**
   - URL: `http://localhost:8000`
   - Admin: `admin@trevio.com` / `admin123`
   - User: `user@trevio.com` / `user123`

---

## 📅 Development Timeline

### **Week P13** - Project Planning ✅
- [x] System overview & requirements
- [x] Database design (ERD)
- [x] User flow mapping
- [x] Git repository setup
- [x] Project structure initialization

### **Week P14** - Module Interconnection (In Progress)
- [ ] Authentication system
- [ ] Hotel booking module
- [ ] Flight booking module
- [ ] Payment gateway integration
- [ ] Module integration testing

### **Week P15** - Final Testing & Deployment
- [ ] Full system testing (QA)
- [ ] Bug fixing & optimization
- [ ] Deployment to hosting
- [ ] Final documentation
- [ ] Presentation preparation

---

## 🔐 Environment Variables

Create `config/database.php`:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'trevio');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Create `config/xendit.php`:
```php
<?php
define('XENDIT_API_KEY', 'your-sandbox-api-key');
define('XENDIT_MODE', 'sandbox'); // sandbox or production
```

---

## 🧪 Testing Credentials (Sandbox)

### Xendit Test Cards:
- **Success:** `4000000000000002`
- **Failed:** `4000000000000010`

### Admin Access:
- Email: `admin@trevio.com`
- Password: `admin123`

---

## 📖 Documentation

- [Database Schema & ERD](docs/ERD.png)
- [User Flow Diagram](docs/User_Flow.png)
- [API Endpoints](docs/API_Endpoints.md)
- [Git Workflow Guide](docs/Git_Workflow.md)

---

## 🤝 Git Workflow

```bash
# Create feature branch
git checkout -b feature/hotel-booking

# Make changes and commit
git add .
git commit -m "feat: add hotel search functionality"

# Push to remote
git push origin feature/hotel-booking

# Create Pull Request on GitHub
# After review, merge to main
```

**Commit Message Convention:**
- `feat:` new feature
- `fix:` bug fix
- `docs:` documentation
- `style:` formatting, CSS
- `refactor:` code restructuring
- `test:` adding tests
- `chore:` maintenance

---

## 📞 Contact

**Lecturer:** Moch Kautsar Sophan  
**Course:** Web Application Programming - Ganjil 2025

For questions or issues, please contact the project manager:
- **Hendrik** - [hendrik@email.com]

---

## 📄 License

This project is created for educational purposes as part of the Web Application Programming final project.

**© 2025 Trevio Team. All Rights Reserved.**
```