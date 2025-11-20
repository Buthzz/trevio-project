# 📱 NOTIFICATION SYSTEM - Trevio

## 🎯 Overview

Sistem notifikasi menggunakan **2 channels:**
1. **Email** (SMTP - Gmail/Mailtrap)
2. **WhatsApp** (3rd Party API)

---

## 📧 EMAIL NOTIFICATION

### **Setup (SMTP Gmail):**

**.env Configuration:**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password  # NOT your Gmail password!
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@trevio.com
MAIL_FROM_NAME="Trevio Hotel Booking"
```

### **How to Get Gmail App Password:**

1. Go to Google Account: https://myaccount.google.com/
2. Security → 2-Step Verification (must be enabled)
3. App passwords
4. Select app: Mail
5. Select device: Other (Custom name) → "Trevio"
6. Click Generate
7. Copy 16-character password
8. Paste in `.env` as `MAIL_PASSWORD`

### **Alternative: Mailtrap (For Development):**

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
```

Get credentials from: https://mailtrap.io/

---

## 📲 WHATSAPP NOTIFICATION

### **Recommended 3rd Party APIs:**

#### **Option 1: Fonnte (Recommended - Indonesian)** ✅
- **Website:** https://fonnte.com/
- **Pricing:** Mulai Rp 250.000/bulan
- **Features:** 
  - WhatsApp Business API
  - Easy integration
  - Affordable for students
  - Indonesian support

**Setup:**
```bash
WHATSAPP_PROVIDER=fonnte
WHATSAPP_API_KEY=your_fonnte_api_key
WHATSAPP_DEVICE_ID=your_device_id
```

**API Example:**
```php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.fonnte.com/send',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'target' => '6281234567890',
    'message' => 'Your booking BK20250101001 has been confirmed!',
  ),
  CURLOPT_HTTPHEADER => array(
    'Authorization: ' . WHATSAPP_API_KEY
  ),
));

$response = curl_exec($curl);
curl_close($curl);
```

---

#### **Option 2: Wablas** ✅
- **Website:** https://wablas.com/
- **Pricing:** Free trial, then pay as you go
- **Features:** Similar to Fonnte

**Setup:**
```bash
WHATSAPP_PROVIDER=wablas
WHATSAPP_API_URL=https://YOUR_DOMAIN.wablas.com
WHATSAPP_TOKEN=your_wablas_token
```

---

#### **Option 3: Twilio WhatsApp API** (International)
- **Website:** https://www.twilio.com/whatsapp
- **Pricing:** $0.005 per message (need credit card)
- **Note:** More expensive but very reliable

---

### **For Budget/Testing:** 
Use **Fonnte Free Trial** or just implement email only first, add WhatsApp later!

---

## 📬 NOTIFICATION EVENTS

### **Customer Notifications:**

| Event | Email | WhatsApp | Trigger |
|-------|:-----:|:--------:|---------|
| **Booking Created** | ✅ | ✅ | After booking submitted |
| **Payment Uploaded** | ✅ | ❌ | After proof uploaded |
| **Payment Verified** | ✅ | ✅ | Admin verifies payment |
| **Payment Rejected** | ✅ | ✅ | Admin rejects payment |
| **Booking Confirmed** | ✅ | ✅ | Payment verified |
| **Check-in Reminder** | ✅ | ✅ | 1 day before check-in |
| **Check-out Reminder** | ✅ | ❌ | On check-out date |
| **Review Reminder** | ✅ | ❌ | 1 day after check-out |
| **Refund Requested** | ✅ | ❌ | Refund request submitted |
| **Refund Approved** | ✅ | ✅ | Admin approves refund |
| **Refund Completed** | ✅ | ✅ | Refund transferred |

### **Owner Notifications:**

| Event | Email | WhatsApp | Trigger |
|-------|:-----:|:--------:|---------|
| **New Booking** | ✅ | ✅ | Customer books their hotel |
| **Payment Verified** | ✅ | ❌ | Admin confirms payment |
| **Check-in Today** | ✅ | ✅ | Morning of check-in date |
| **New Review** | ✅ | ❌ | Customer posts review |
| **Refund Request** | ✅ | ✅ | Customer requests refund |

### **Admin Notifications:**

| Event | Email | WhatsApp | Trigger |
|-------|:-----:|:--------:|---------|
| **Payment Uploaded** | ✅ | ✅ | Needs verification |
| **Refund Requested** | ✅ | ✅ | Needs processing |
| **New Hotel Registration** | ✅ | ❌ | Owner registers hotel |
| **New Review Pending** | ✅ | ❌ | Review needs approval |

---

## 💻 IMPLEMENTATION

### **PHP Email Class:**

```php
<?php
// app/libraries/Mailer.php

class Mailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        $this->host = getenv('MAIL_HOST');
        $this->port = getenv('MAIL_PORT');
        $this->username = getenv('MAIL_USERNAME');
        $this->password = getenv('MAIL_PASSWORD');
        $this->from_email = getenv('MAIL_FROM_ADDRESS');
        $this->from_name = getenv('MAIL_FROM_NAME');
    }
    
    public function send($to, $subject, $body, $attachments = []) {
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->port;
            
            // Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Attachments
            foreach ($attachments as $file) {
                $mail->addAttachment($file);
            }
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
```

### **PHP WhatsApp Class (Fonnte):**

```php
<?php
// app/libraries/WhatsApp.php

class WhatsApp {
    private $api_key;
    private $api_url = 'https://api.fonnte.com/send';
    
    public function __construct() {
        $this->api_key = getenv('WHATSAPP_API_KEY');
    }
    
    public function send($phone, $message) {
        // Format phone number (must start with country code)
        $phone = $this->formatPhone($phone);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $phone,
                'message' => $message,
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->api_key
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            error_log("WhatsApp Error: " . $err);
            return false;
        }
        
        $result = json_decode($response, true);
        return isset($result['status']) && $result['status'] == true;
    }
    
    private function formatPhone($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62 (Indonesia)
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with country code, add 62
        if (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}
```

### **Notification Service:**

```php
<?php
// app/services/NotificationService.php

class NotificationService {
    private $mailer;
    private $whatsapp;
    private $db;
    
    public function __construct() {
        $this->mailer = new Mailer();
        $this->whatsapp = new WhatsApp();
        $this->db = new Database();
    }
    
    public function sendBookingConfirmed($booking_id) {
        // Get booking details
        $booking = $this->getBookingDetails($booking_id);
        
        // Email content
        $email_subject = "Booking Confirmed - {$booking['booking_code']}";
        $email_body = $this->getEmailTemplate('booking_confirmed', $booking);
        
        // WhatsApp message
        $wa_message = "✅ *Booking Confirmed!*\n\n";
        $wa_message .= "Booking Code: *{$booking['booking_code']}*\n";
        $wa_message .= "Hotel: {$booking['hotel_name']}\n";
        $wa_message .= "Check-in: {$booking['check_in_date']}\n";
        $wa_message .= "Check-out: {$booking['check_out_date']}\n\n";
        $wa_message .= "See you soon! 🏨";
        
        // Send notifications
        $email_sent = $this->mailer->send(
            $booking['customer_email'],
            $email_subject,
            $email_body
        );
        
        $wa_sent = false;
        if ($booking['whatsapp_number']) {
            $wa_sent = $this->whatsapp->send(
                $booking['whatsapp_number'],
                $wa_message
            );
        }
        
        // Log notification
        $this->logNotification([
            'user_id' => $booking['customer_id'],
            'notification_type' => 'booking_confirmed',
            'title' => 'Booking Confirmed',
            'message' => $wa_message,
            'send_email' => true,
            'email_sent' => $email_sent,
            'send_whatsapp' => true,
            'whatsapp_sent' => $wa_sent,
            'booking_id' => $booking_id
        ]);
        
        return ['email' => $email_sent, 'whatsapp' => $wa_sent];
    }
    
    public function sendPaymentVerificationPending($booking_id) {
        // Get admin emails
        $admins = $this->getAdminUsers();
        
        $booking = $this->getBookingDetails($booking_id);
        
        $subject = "⚠️ Payment Verification Needed - {$booking['booking_code']}";
        $message = "Customer has uploaded payment proof for booking {$booking['booking_code']}. Please verify.";
        
        foreach ($admins as $admin) {
            // Send email
            $this->mailer->send($admin['email'], $subject, $message);
            
            // Send WhatsApp if available
            if ($admin['whatsapp_number']) {
                $this->whatsapp->send($admin['whatsapp_number'], $message);
            }
            
            // Log
            $this->logNotification([
                'user_id' => $admin['id'],
                'notification_type' => 'payment_uploaded',
                'title' => $subject,
                'message' => $message,
                'send_email' => true,
                'email_sent' => true,
                'send_whatsapp' => !empty($admin['whatsapp_number']),
                'whatsapp_sent' => !empty($admin['whatsapp_number']),
                'booking_id' => $booking_id
            ]);
        }
    }
    
    private function getEmailTemplate($template_name, $data) {
        // Load HTML email template
        ob_start();
        extract($data);
        include __DIR__ . "/../views/emails/{$template_name}.php";
        return ob_get_clean();
    }
    
    private function logNotification($data) {
        $this->db->query("INSERT INTO notifications SET ...");
        // Implementation
    }
}
```

---

## 📄 EMAIL TEMPLATES

### **Example: booking_confirmed.php**

```html
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .button { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Booking Confirmed!</h1>
        </div>
        <div class="content">
            <p>Dear <?= $guest_name ?>,</p>
            
            <p>Your booking has been confirmed!</p>
            
            <h3>Booking Details:</h3>
            <ul>
                <li><strong>Booking Code:</strong> <?= $booking_code ?></li>
                <li><strong>Hotel:</strong> <?= $hotel_name ?></li>
                <li><strong>Room Type:</strong> <?= $room_type ?></li>
                <li><strong>Check-in:</strong> <?= date('d M Y', strtotime($check_in_date)) ?></li>
                <li><strong>Check-out:</strong> <?= date('d M Y', strtotime($check_out_date)) ?></li>
                <li><strong>Total Price:</strong> Rp <?= number_format($total_price, 0, ',', '.') ?></li>
            </ul>
            
            <p>
                <a href="<?= BASE_URL ?>/booking/detail/<?= $booking_code ?>" class="button">
                    View Booking Details
                </a>
            </p>
            
            <p>See you soon!</p>
            <p>Trevio Team</p>
        </div>
    </div>
</body>
</html>
```

---

## 🔔 NOTIFICATION TIMING

### **Immediate (Real-time):**
- Booking created
- Payment uploaded
- Payment verified/rejected
- Booking confirmed
- Refund requested

### **Scheduled (Cron Job):**
- Check-in reminder (1 day before, sent at 9 AM)
- Check-out reminder (on check-out date, sent at 9 AM)
- Review reminder (1 day after check-out, sent at 10 AM)

### **Cron Job Example:**

```bash
# /etc/crontab or crontab -e

# Check-in reminders (daily at 9 AM)
0 9 * * * php /path/to/trevio/cron/send_checkin_reminders.php

# Check-out reminders (daily at 9 AM)
0 9 * * * php /path/to/trevio/cron/send_checkout_reminders.php

# Review reminders (daily at 10 AM)
0 10 * * * php /path/to/trevio/cron/send_review_reminders.php
```

---

## 📊 NOTIFICATION DASHBOARD

### **For Users:**
- Notification bell icon with badge count
- Dropdown list of recent notifications
- Mark as read functionality
- Notification preferences (enable/disable per type)

### **For Admin:**
- Monitor notification delivery status
- View failed notifications
- Resend failed notifications
- Notification statistics

---

## ⚡ QUICK START (Minimal Setup)

### **For P13-P14 (MVP):**

1. **Email Only (No WhatsApp yet)**
   ```bash
   # Use Mailtrap for testing
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_user
   MAIL_PASSWORD=your_mailtrap_pass
   ```

2. **Essential Notifications:**
   - Booking created
   - Payment verified
   - Booking confirmed
   
3. **WhatsApp Integration (P15 or later):**
   - Sign up for Fonnte free trial
   - Add WhatsApp to critical notifications only

---

## 💰 COST ESTIMATION

### **Email (SMTP):**
- Gmail: **FREE** (with app password)
- Mailtrap: **FREE** (for testing)

### **WhatsApp:**
- Fonnte: **Rp 250.000/month** (unlimited messages)
- Wablas: **Pay as you go** (~Rp 300-500/message)
- For demo: **Use free trial** ✅

**Recommendation:** Start with email only, add WhatsApp before final presentation!

---

## 📱 TESTING

### **Test Email:**
```php
$mailer = new Mailer();
$result = $mailer->send(
    'test@example.com',
    'Test Email',
    '<h1>Test from Trevio</h1><p>Email is working!</p>'
);

var_dump($result); // Should return true
```

### **Test WhatsApp:**
```php
$whatsapp = new WhatsApp();
$result = $whatsapp->send(
    '081234567890',
    'Test message from Trevio!'
);

var_dump($result); // Should return true
```

---

**Note:** Notification system is a BONUS feature. Implement core transactions first, then add notifications for extra points! 🎯