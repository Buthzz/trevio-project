<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Payment extends Model {
    protected $table = 'payments';

    /**
     * Get all payments with smart status filter
     * FIX: Menggunakan LEFT JOIN agar data tetap muncul meski data booking/user terhapus (orphan data)
     */
    public function getAll($status = null) {
        // Menggunakan LEFT JOIN untuk keamanan data
        // Menambahkan COALESCE untuk menangani nilai NULL jika data relasi hilang
        $query = "SELECT p.*, 
                         p.transfer_amount,
                         COALESCE(b.booking_code, 'DATA BOOKING HILANG') as booking_code, 
                         COALESCE(b.total_price, 0) as booking_total,
                         COALESCE(u.name, 'Unknown User') as customer_name, 
                         COALESCE(u.email, '-') as customer_email,
                         COALESCE(h.name, 'Unknown Hotel') as hotel_name
                  FROM {$this->table} p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  LEFT JOIN users u ON b.customer_id = u.id
                  LEFT JOIN hotels h ON b.hotel_id = h.id";
        
        // Logika Filter Status
        if ($status) {
            if ($status === 'pending') {
                // Tampilkan yang statusnya 'pending' ATAU 'uploaded' (Menunggu Verifikasi)
                $query .= " WHERE p.payment_status IN ('pending', 'uploaded')";
            } else {
                $query .= " WHERE p.payment_status = :status";
            }
        }
        
        // Urutkan: Uploaded (prioritas) -> Pending -> Lainnya, lalu berdasarkan tanggal terbaru
        if ($status === 'pending') {
            $query .= " ORDER BY FIELD(p.payment_status, 'uploaded', 'pending'), p.created_at DESC";
        } else {
            $query .= " ORDER BY p.created_at DESC";
        }
        
        try {
            $this->query($query);
            
            // Bind parameter jika status bukan 'pending' (karena pending pakai IN query langsung)
            if ($status && $status !== 'pending') {
                $this->bind(':status', $status);
            }
            
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Payment getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Hitung jumlah pembayaran yang pending/uploaded (butuh verifikasi)
     */
    public function countPending() {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE payment_status IN ('pending', 'uploaded')");
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
                         b.booking_code, b.total_price, b.check_in_date, b.check_out_date, b.num_rooms,
                         u.name as customer_name, u.email as customer_email,
                         h.name as hotel_name
                  FROM {$this->table} p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  LEFT JOIN users u ON b.customer_id = u.id
                  LEFT JOIN hotels h ON b.hotel_id = h.id
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
     * Mengupdate status payment jadi 'verified' DAN booking jadi 'confirmed'
     */
    public function confirm($paymentId, $adminId) {
        try {
            $this->beginTransaction();

            // 1. Update Payment Status jadi 'verified'
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'verified', 
                          verified_by = :admin_id, 
                          verified_at = NOW() 
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

            // Update Payment jadi 'rejected'
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'rejected', 
                          verified_by = :admin_id, 
                          verified_at = NOW(),
                          rejection_reason = :reason
                      WHERE id = :id";
            
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':reason', $reason);
            $this->execute();

            // Update Booking jadi 'cancelled'
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