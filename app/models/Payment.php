<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Payment extends Model {
    protected $table = 'payments';

    /**
     * Get all payments with optional status filter
     * Menggunakan JOIN ke bookings dan users untuk info lengkap
     */
    public function getAll($status = null) {
        $query = "SELECT p.*, 
                         b.booking_code, b.total_price as booking_total,
                         u.name as customer_name, u.email as customer_email,
                         h.name as hotel_name
                  FROM {$this->table} p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id";
        
        if ($status) {
            $query .= " WHERE p.payment_status = :status";
        }
        
        // Urutkan dari yang terbaru
        $query .= " ORDER BY p.payment_date DESC";
        
        try {
            $this->query($query);
            if ($status) {
                $this->bind(':status', $status);
            }
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Payment getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Hitung jumlah pembayaran yang pending (butuh verifikasi)
     */
    public function countPending() {
        try {
            // Asumsikan status di DB adalah 'pending' untuk yang menunggu verifikasi
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE payment_status = 'pending'");
            $result = $this->single();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Find payment detail by ID
     */
    public function find($id) {
        $query = "SELECT p.*, 
                         b.booking_code, b.total_price, b.check_in, b.check_out, b.num_rooms,
                         u.name as customer_name, u.email as customer_email,
                         h.name as hotel_name
                  FROM {$this->table} p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE p.id = :id";
        
        try {
            $this->query($query);
            $this->bind(':id', $id);
            return $this->single();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Confirm payment (Admin Action)
     * Mengupdate status payment jadi 'paid' DAN booking jadi 'confirmed'
     */
    public function confirm($paymentId, $adminId) {
        try {
            $this->beginTransaction();

            // 1. Update Payment Status
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'paid', 
                          confirmed_by = :admin_id, 
                          confirmed_at = NOW() 
                      WHERE id = :id";
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->execute();

            // 2. Ambil Booking ID dari payment ini
            $this->query("SELECT booking_id FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $paymentId);
            $payment = $this->single();

            if ($payment) {
                // 3. Update Booking Status jadi 'confirmed'
                $this->query("UPDATE bookings SET booking_status = 'confirmed' WHERE id = :booking_id");
                $this->bind(':booking_id', $payment['booking_id']);
                $this->execute();
            }

            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollBack();
            error_log("Payment Confirm Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject payment
     */
    public function reject($paymentId, $adminId, $reason) {
        try {
            $this->beginTransaction();

            // Update Payment jadi 'failed'
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'failed', 
                          confirmed_by = :admin_id, 
                          confirmed_at = NOW() 
                      WHERE id = :id"; // Catatan: Anda mungkin perlu menambah kolom 'rejection_reason' di tabel payments jika ingin menyimpan alasan
            
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->execute();

            // Update Booking jadi 'cancelled' atau 'waiting_payment' (tergantung logika bisnis)
            // Di sini kita kembalikan ke cancelled agar user book ulang atau upload ulang (opsional)
            $this->query("SELECT booking_id FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $paymentId);
            $payment = $this->single();

            if ($payment) {
                $this->query("UPDATE bookings SET booking_status = 'cancelled' WHERE id = :booking_id");
                $this->bind(':booking_id', $payment['booking_id']);
                $this->execute();
            }

            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollBack();
            return false;
        }
    }
}