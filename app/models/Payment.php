<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Payment extends Model {
    protected $table = 'payments';

    /**
     * Get all payments with smart status filter
     * Fix: Menggabungkan status 'pending' dan 'uploaded' agar muncul di tab Verifikasi
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
        
        // Logika Filter Status yang Diperbaiki
        if ($status) {
            if ($status === 'pending') {
                // Tampilkan yang statusnya 'pending' ATAU 'uploaded'
                $query .= " WHERE p.payment_status IN ('pending', 'uploaded')";
            } else {
                $query .= " WHERE p.payment_status = :status";
            }
        }
        
        // Urutkan dari yang terbaru (uploaded duluan, lalu pending)
        // FIELD function memastikan 'uploaded' muncul paling atas jika status pending
        if ($status === 'pending') {
            $query .= " ORDER BY FIELD(p.payment_status, 'uploaded', 'pending'), p.payment_date DESC";
        } else {
            $query .= " ORDER BY p.payment_date DESC";
        }
        
        try {
            $this->query($query);
            
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
     * Fix: Update status ke 'verified' (sesuai ENUM DB) bukan 'paid'
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

            // 2. Ambil Booking ID
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
     * Fix: Update status ke 'rejected' (sesuai ENUM DB) bukan 'failed'
     */
    public function reject($paymentId, $adminId, $reason) {
        try {
            $this->beginTransaction();

            // Update Payment jadi 'rejected' dan simpan alasan
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