<?php

// IMPORT PENTING: Mengimpor class Model dari namespace App\Core
use App\Core\Model;

class Payment extends Model {
    private $table = 'payments';

    public function getAllPayments()
    {
        // PERBAIKAN PENTING:
        // Menggunakan LEFT JOIN (bukan INNER JOIN) agar data pembayaran tetap muncul
        // meskipun data user atau booking terkait sudah dihapus/bermasalah.
        // Kita juga mengurutkan dari yang terbaru (DESC).
        
        $query = "SELECT p.*, 
                         u.name as customer_name, 
                         u.email as customer_email,
                         b.booking_code,
                         b.check_in,
                         b.check_out,
                         b.total_price as bill_amount
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  ORDER BY CASE 
                    WHEN p.status = 'waiting_confirmation' THEN 1 
                    WHEN p.status = 'pending' THEN 2
                    ELSE 3 
                  END ASC, p.created_at DESC";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function getPaymentById($id)
    {
        $query = "SELECT p.*, 
                         u.name as customer_name,
                         b.booking_code
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  WHERE p.id = :id";

        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function confirmPayment($id)
    {
        // Update status pembayaran jadi paid
        $query = "UPDATE " . $this->table . " SET status = 'paid', updated_at = NOW() WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        
        if ($this->db->execute()) {
            // Jika berhasil, update juga status booking jadi confirmed
            // Ambil booking_id dulu
            $payment = $this->getPaymentById($id);
            if ($payment) {
                $qBooking = "UPDATE bookings SET status = 'confirmed' WHERE id = :booking_id";
                $this->db->query($qBooking);
                $this->db->bind('booking_id', $payment->booking_id);
                $this->db->execute();
            }
            return true;
        }
        return false;
    }

    public function rejectPayment($id)
    {
        $query = "UPDATE " . $this->table . " SET status = 'failed', updated_at = NOW() WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        
        if ($this->db->execute()) {
             // Jika ditolak, status booking kembali ke cancelled atau pending
            $payment = $this->getPaymentById($id);
            if ($payment) {
                $qBooking = "UPDATE bookings SET status = 'cancelled' WHERE id = :booking_id";
                $this->db->query($qBooking);
                $this->db->bind('booking_id', $payment->booking_id);
                $this->db->execute();
            }
            return true;
        }
        return false;
    }
}