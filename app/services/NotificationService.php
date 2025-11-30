<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

class NotificationService {

    // --- FITUR BARU: Notifikasi ke Admin (Saat ada bukti bayar) ---
    public function notifyAdminsNewPayment($booking, $deeplink) {
        // Daftar Nomor Admin (Hardcoded sesuai request)
        $admins = ['083139682650', '085855731048'];

        $message = "*TREVIO - PEMBAYARAN BARU* 💰\n\n";
        $message .= "Kode: *{$booking['booking_code']}*\n";
        $message .= "Tamu: {$booking['guest_name']}\n";
        $message .= "Total: Rp " . number_format($booking['total_price'], 0, ',', '.') . "\n\n";
        $message .= "Bukti pembayaran telah diupload.\n";
        $message .= "Klik link di bawah untuk VERIFIKASI LANGSUNG (Tanpa Login):\n";
        $message .= "👉 " . $deeplink . "\n\n";
        $message .= "Mohon segera dicek.";

        foreach ($admins as $phone) {
            $this->sendWhatsAppMessage($phone, $message);
        }
    }

    // --- FITUR BARU: Notifikasi ke Owner (Saat kamar terjual/confirm) ---
    public function notifyOwnerBookingSold($booking, $ownerPhone, $hotelName) {
        $message = "*KAMAR TERJUAL!* 🎉\n\n";
        $message .= "Halo Owner *{$hotelName}*,\n";
        $message .= "Booking baru saja dikonfirmasi:\n\n";
        $message .= "🏨 Hotel: {$hotelName}\n";
        $message .= "🔖 Kode: {$booking['booking_code']}\n";
        $message .= "👤 Tamu: {$booking['guest_name']}\n";
        $message .= "📅 Check-in: " . date('d M Y', strtotime($booking['check_in_date'])) . "\n";
        $message .= "🌙 Durasi: {$booking['num_nights']} Malam\n";
        $message .= "💰 Pendapatan: Rp " . number_format($booking['total_price'], 0, ',', '.') . "\n\n";
        $message .= "Cek dashboard untuk detail lengkap.";

        $this->sendWhatsAppMessage($ownerPhone, $message);
    }

    /**
     * Kirim Notifikasi Customer (Existing)
     */
    public function sendBookingConfirmation($data) {
        // 1. Kirim WhatsApp ke Customer
        $msgCustomer = "*BOOKING DIKONFIRMASI!* ✅\n\n";
        $msgCustomer .= "Halo *{$data['customer_name']}*,\n";
        $msgCustomer .= "Pembayaran Anda telah kami terima.\n\n";
        $msgCustomer .= "🏨 Hotel: {$data['hotel_name']}\n";
        $msgCustomer .= "🔖 Kode: {$data['booking_code']}\n";
        $msgCustomer .= "📅 Check-in: " . date('d M Y', strtotime($data['check_in_date'])) . "\n\n";
        $msgCustomer .= "Terima kasih telah menggunakan Trevio.";
        
        $this->sendWhatsAppMessage($data['customer_phone'], $msgCustomer);

        // 2. Generate & Kirim Email PDF
        $this->sendEmailWithInvoice($data);
    }

    /**
     * [REFACTORED] Fungsi Generic Kirim WA
     */
    private function sendWhatsAppMessage($target, $message) {
        // Pastikan format nomor 628xxx (Konversi 08 ke 628)
        if (substr($target, 0, 1) == '0') {
            $target = '62' . substr($target, 1);
        }

        $token = "PSb4ar7j6d482Bvphgc1"; // Token Fonnte Anda

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.fonnte.com/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', 
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $token
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            error_log('Fonnte Error: ' . curl_error($curl));
        }
        curl_close($curl);
    }

    /**
     * Kirim Email dengan Lampiran PDF Invoice (Existing - Tidak Diubah)
     */
    private function sendEmailWithInvoice($data) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'mail.animenesia.site';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'noreply@animenesia.site';
            $mail->Password   = 'asdffjkl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];

            $mail->setFrom('noreply@animenesia.site', 'Trevio Booking System');
            $mail->addAddress($data['customer_email'], $data['customer_name']);

            $pdfContent = $this->generateInvoicePDF($data);
            $mail->addStringAttachment($pdfContent, 'Invoice-' . $data['booking_code'] . '.pdf');

            $mail->isHTML(true);
            $mail->Subject = 'Konfirmasi Booking & Invoice - ' . $data['booking_code'];
            $mail->Body    = "<h3>Pembayaran Dikonfirmasi</h3><p>Booking Anda di <b>{$data['hotel_name']}</b> telah dikonfirmasi.</p>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function generateInvoicePDF($data) {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $html = "<h1>INVOICE {$data['booking_code']}</h1><p>Total: Rp " . number_format($data['total_price']) . "</p>";
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S');
    }
}