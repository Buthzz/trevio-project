# Pembagian Tugas Tim Trevio (Anti Konflik!)

## 📁 Struktur Folder Project
```
trevio-project/
├── app/
│   ├── controllers/          # Logic handlers
│   │   ├── admin/           # Admin controllers
│   │   ├── owner/           # Owner controllers
│   │   └── customer/        # Customer controllers
│   ├── models/              # Database models
│   └── views/               # HTML pages
│       ├── admin/
│       ├── owner/
│       └── customer/
├── config/                  # Configuration files
├── libraries/               # Helper libraries
├── database/                # SQL files
└── public/                  # Assets (CSS, JS, images)
    ├── css/
    ├── js/
    └── uploads/
```

---

## 👨‍💻 Pembagian Tugas Detail

### **1️⃣ Hendrik (Project Manager + Full Stack)**
**Folder:** `app/controllers/` + `config/` + `libraries/`

```
app/controllers/
├── AuthController.php           ✅ Login, register, logout, Google OAuth
├── BookingController.php        ✅ Customer booking logic
└── DashboardController.php      ✅ Dashboard semua role

app/models/
├── User.php                     ✅ User CRUD
└── Booking.php                  ✅ Booking CRUD

config/
├── database.php                 ✅ DB connection
├── google-oauth.php             ✅ Google OAuth config
└── (semua file di folder config)

libraries/
├── Mailer.php                   ✅ Email notifications (PHPMailer)
├── WhatsApp.php                 ✅ WhatsApp API wrapper
└── (semua file di folder libraries)
```

**Tugas:**
- Setup MVC structure & routing
- Authentication system
- Booking transaction core
- Email & WhatsApp libraries
- Koordinasi merge PR

---

### **2️⃣ Fajar (Backend + Database + DevOps)**
**Folder:** `app/controllers/admin/` + `database/` + `docs/`

```
app/controllers/admin/
├── PaymentController.php        ✅ Verify/reject payment
├── RefundController.php         ✅ Process refund
└── UserController.php           ✅ Manage users

app/models/
├── Payment.php                  ✅ Payment CRUD
└── Refund.php                   ✅ Refund CRUD

database/
├── trevio_final.sql             ✅ Database schema
└── seeders.sql                  ✅ Sample data

docs/
└── Deployment_Guide.md          ✅ VPS deployment steps
```

**Tugas:**
- Admin payment & refund logic
- Database design & migration
- Sample data (seeders)
- VPS deployment documentation

---

### **3️⃣ Syadat (QA + User Flow + Backend Ringan)**
**Folder:** `app/controllers/owner/` + `tests/` + `docs/`

```
app/controllers/owner/
├── HotelController.php          ✅ CRUD hotels (INSERT, UPDATE, DELETE)
└── RoomController.php           ✅ CRUD rooms (INSERT, UPDATE, DELETE, slot logic)

app/models/
├── Hotel.php                    ✅ Hotel CRUD
└── Room.php                     ✅ Room CRUD (available_slots logic)

tests/
├── booking-flow-test.md         ✅ Test booking scenario
├── payment-flow-test.md         ✅ Test payment scenario
└── refund-flow-test.md          ✅ Test refund scenario

docs/
└── User_Flow.pdf                ✅ User flow diagram
```

**Tugas:**
- Owner hotel & room CRUD (backend ringan)
- User flow documentation
- Manual testing checklist
- Bug reporting

---

### **4️⃣ Zakaria (UI/UX + Frontend + Backend Ringan)**
**Folder:** `app/views/customer/` + `app/controllers/customer/` + `docs/`

```
app/views/customer/
├── home.php                     ✅ Landing page
├── search.php                   ✅ Search & filter hotels
├── hotel-detail.php             ✅ Hotel detail page
└── booking-form.php             ✅ Booking form

app/controllers/customer/
└── SearchController.php         ✅ Search logic (SELECT query ~15 baris)

public/css/
└── custom.css                   ✅ Custom styles (jika perlu)

docs/
└── Design_System.md             ✅ UI/UX guidelines
```

**Tugas:**
- Design mockup (optional Figma)
- Customer UI dengan Tailwind
- Search hotel controller (backend ringan)
- Responsive mobile-first

---

### **5️⃣ Reno (Frontend + Backend Ringan)**
**Folder:** `app/views/owner/` + `app/views/admin/` + `public/js/`

```
app/views/owner/
├── dashboard.php                ✅ Owner dashboard
├── hotels.php                   ✅ List hotels
├── rooms.php                    ✅ List rooms
└── reports.php                  ✅ Reports Chart.js

app/views/admin/
├── dashboard.php                ✅ Admin dashboard
├── payments.php                 ✅ Payment verification list
├── refunds.php                  ✅ Refund processing list
└── statistics.php               ✅ Global statistics

app/controllers/
└── ProfileController.php        ✅ Edit profile (UPDATE query ~15 baris)

public/js/
├── charts.js                    ✅ Chart.js init
└── alerts.js                    ✅ SweetAlert2 wrapper
```

**Tugas:**
- Owner & Admin dashboard UI
- Chart.js implementation
- Profile controller (backend ringan)
- SweetAlert2 alerts

---

## 🚦 Aturan Git (Simpel!)

### **Branch Strategy:**
```
main           # ❌ JANGAN push langsung!
└── dev        # ✅ Push kesini lewat PR
    ├── hendrik-auth
    ├── fajar-payment
    ├── syadat-owner
    ├── zek-customer
    └── reno-dashboard
```

### **Git Command:**

**Setup awal:**
```bash
git clone https://github.com/your-team/trevio.git
cd trevio
git checkout -b nama-kamu-fitur  # contoh: hendrik-auth
```

**Setiap hari sebelum coding:**
```bash
git pull origin dev              # ⚠️ WAJIB! Ambil update terbaru
```

**Setelah selesai coding:**
```bash
git add .
git commit -m "feat: deskripsi"  # contoh: "feat: add login page"
git push origin nama-kamu-fitur
```

**Di GitHub:**
- Buat Pull Request ke branch `dev`
- Tag Hendrik untuk review
- Tunggu approval

### **Commit Message:**
```
feat: fitur baru
fix: perbaiki bug
style: ubah tampilan
docs: update dokumentasi
```

---

## 🚫 Aturan Anti Konflik

| ❌ JANGAN | ✅ LAKUKAN |
|-----------|-----------|
| Push ke `main` | Push ke `dev` lewat PR |
| Edit file orang lain | Koordinasi di grup dulu |
| Commit file `.env` | Tambahkan ke `.gitignore` |
| Commit folder `uploads/` | Tambahkan ke `.gitignore` |
| Numpuk banyak perubahan | Commit sering (tiap fitur kecil) |

---

## 📁 File Ownership (Siapa Pegang Apa?)

| File/Folder | Owner | Boleh Edit? |
|-------------|-------|-------------|
| `AuthController.php` | Hendrik | ❌ Tanya dulu |
| `config/*` | Hendrik | ❌ Tanya dulu |
| `libraries/*` | Hendrik | ❌ Tanya dulu |
| `PaymentController.php` | Fajar | ❌ Tanya dulu |
| `database/*` | Fajar | ❌ Tanya dulu |
| `HotelController.php` | Syadat | ❌ Tanya dulu |
| `RoomController.php` | Syadat | ❌ Tanya dulu |
| `SearchController.php` | Zek | ❌ Tanya dulu |
| `ProfileController.php` | Reno | ❌ Tanya dulu |
| `views/customer/*` | Zek | ✅ Style boleh |
| `views/owner/*` | Reno | ✅ Style boleh |
| `views/admin/*` | Reno | ✅ Style boleh |
| `public/css/*` | Zek/Reno | ✅ Bebas |
| `public/js/*` | Reno | ✅ Bebas |

---

## ✅ Checklist Progress

### **Hendrik:**
- [ ] AuthController (login, register, Google OAuth)
- [ ] BookingController (create booking)
- [ ] User & Booking model
- [ ] Database connection config
- [ ] Mailer & WhatsApp library

### **Fajar:**
- [ ] PaymentController (verify payment)
- [ ] RefundController (process refund)
- [ ] Payment & Refund model
- [ ] Database schema & seeders
- [ ] VPS deployment guide

### **Syadat:**
- [ ] HotelController (CRUD)
- [ ] RoomController (CRUD + slot)
- [ ] Hotel & Room model
- [ ] User flow documentation
- [ ] Testing checklist

### **Zakaria:**
- [ ] Customer landing page
- [ ] Search hotel page
- [ ] Hotel detail page
- [ ] SearchController (backend ringan)
- [ ] Responsive design

### **Reno:**
- [ ] Owner dashboard UI
- [ ] Admin dashboard UI
- [ ] Chart.js reports
- [ ] ProfileController (backend ringan)
- [ ] SweetAlert2 integration

---

## 💡 Tips Kolaborasi

1. **Komunikasi di grup** sebelum edit file shared
2. **Pull dulu** setiap mau mulai coding
3. **Commit sering** jangan tunggu banyak
4. **Testing** sebelum push
5. **Review PR teman** saling bantu
6. **Tanya Hendrik** kalau bingung merge conflict

---

**🎯 Fokus:** Setiap orang punya folder sendiri = minimal konflik! ✅
