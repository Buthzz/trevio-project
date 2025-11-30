<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

class NotificationService {

    /**
     * Kirim Notifikasi Lengkap (WA + Email PDF)
     */
    public function sendBookingConfirmation($data) {
        // 1. Kirim WhatsApp
        $this->sendWhatsApp($data);

        // 2. Generate & Kirim Email PDF
        $this->sendEmailWithInvoice($data);
    }

    /**
     * Kirim Pesan WhatsApp (Fonnte API)
     */
    private function sendWhatsApp($data) {
        $target = $data['customer_phone']; // Pastikan format 628xxx atau 08xxx
        
        // Format Pesan
        $message = "*BOOKING DIKONFIRMASI!* ✅\n\n";
        $message .= "Halo *{$data['customer_name']}*,\n";
        $message .= "Pembayaran Anda telah kami terima. Berikut detail pesanan Anda:\n\n";
        $message .= "🏨 Hotel: {$data['hotel_name']}\n";
        $message .= "🔖 Kode Booking: {$data['booking_code']}\n";
        $message .= "🛏️ Tipe Kamar: {$data['room_type']}\n";
        $message .= "📅 Check-in: " . date('d M Y', strtotime($data['check_in_date'])) . "\n";
        $message .= "📅 Check-out: " . date('d M Y', strtotime($data['check_out_date'])) . "\n";
        $message .= "\nTerima kasih telah menggunakan Trevio. Selamat berlibur!";

        $token = "PSb4ar7j6d482Bvphgc1"; // Token Anda

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.fonnte.com/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30, // Timeout aman
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
     * Kirim Email dengan Lampiran PDF Invoice
     */
    private function sendEmailWithInvoice($data) {
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP (Sesuai Data Anda)
            $mail->isSMTP();
            $mail->Host       = 'mail.animenesia.site';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'noreply@animenesia.site';
            $mail->Password   = 'asdffjkl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Keamanan SSL (Bypass jika perlu)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Pengirim & Penerima
            $mail->setFrom('noreply@animenesia.site', 'Trevio Booking System');
            $mail->addAddress($data['customer_email'], $data['customer_name']);

            // Generate PDF Content
            $pdfContent = $this->generateInvoicePDF($data);
            
            // Lampirkan PDF dari string (tanpa simpan ke file fisik)
            $mail->addStringAttachment($pdfContent, 'Invoice-' . $data['booking_code'] . '.pdf');

            // Konten Email HTML
            $mail->isHTML(true);
            $mail->Subject = 'Konfirmasi Booking & Invoice - ' . $data['booking_code'];
            $mail->Body    = "
                <h3>Pembayaran Dikonfirmasi</h3>
                <p>Halo <b>{$data['customer_name']}</b>,</p>
                <p>Terima kasih telah melakukan pembayaran. Booking Anda di <b>{$data['hotel_name']}</b> telah dikonfirmasi.</p>
                <p>Silakan unduh invoice (bukti pembayaran) yang terlampir pada email ini.</p>
                <br>
                <p>Salam,<br>Tim Trevio</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Generate HTML PDF Invoice menggunakan MPDF
     */
    private function generateInvoicePDF($data) {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        
        $html = "
        <style>
            body { font-family: sans-serif; color: #333; }
            .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
            .header h1 { color: #2563eb; margin: 0; }
            .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .details-table th, .details-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
            .total { font-size: 18px; font-weight: bold; color: #2563eb; text-align: right; padding-top: 10px; }
            .footer { text-align: center; font-size: 12px; color: #777; margin-top: 50px; }
        </style>
        
        <div class='header'>
            <h1>INVOICE</h1>
            <p>Kode Booking: <b>{$data['booking_code']}</b></p>
        </div>

        <table class='details-table'>
            <tr>
                <th>Nama Tamu</th>
                <td>{$data['customer_name']}</td>
            </tr>
            <tr>
                <th>Hotel</th>
                <td>{$data['hotel_name']}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{$data['hotel_address']}</td>
            </tr>
            <tr>
                <th>Tipe Kamar</th>
                <td>{$data['room_type']}</td>
            </tr>
            <tr>
                <th>Check-in</th>
                <td>" . date('d F Y', strtotime($data['check_in_date'])) . "</td>
            </tr>
            <tr>
                <th>Check-out</th>
                <td>" . date('d F Y', strtotime($data['check_out_date'])) . "</td>
            </tr>
            <tr>
                <th>Durasi</th>
                <td>{$data['num_nights']} Malam</td>
            </tr>
        </table>

        <div class='total'>
            Total Dibayar: Rp " . number_format($data['total_price'], 0, ',', '.') . "
        </div>

        <div class='footer'>
            <p>Terima kasih telah memesan melalui Trevio.</p>
            <p>Email ini adalah bukti pembayaran yang sah.</p>
        </div>
        ";

        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S'); // Return as string
    }
}