<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Payment extends Model {
    protected $table = 'payments';

    /**
     * Get all payments with smart status filter
     */
    public function getAll($status = null) {
        $query = "SELECT p.*, 
                         p.transfer_amount,
                         COALESCE(b.booking_code, 'DATA HILANG') as booking_code, 
                         COALESCE(b.total_price, 0) as booking_total,
                         COALESCE(u.name, 'Unknown User') as customer_name, 
                         COALESCE(u.email, '-') as customer_email,
                         COALESCE(h.name, 'Unknown Hotel') as hotel_name
                  FROM {$this->table} p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  LEFT JOIN users u ON b.customer_id = u.id
                  LEFT JOIN hotels h ON b.hotel_id = h.id";
        
        if ($status) {
            if ($status === 'pending') {
                $query .= " WHERE p.payment_status IN ('pending', 'uploaded')";
            } else {
                $query .= " WHERE p.payment_status = :status";
            }
        }
        
        if ($status === 'pending') {
            $query .= " ORDER BY FIELD(p.payment_status, 'uploaded', 'pending'), p.created_at DESC";
        } else {
            $query .= " ORDER BY p.created_at DESC";
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
     * Find payment detail by ID (Updated for Invoice Data)
     * Mengambil data lengkap: Booking, User, Hotel, Room
     */
    public function find($id) {
        $query = "SELECT p.*, 
                         b.booking_code, b.total_price, b.check_in_date, b.check_out_date, b.num_rooms, b.num_nights,
                         u.name as customer_name, u.email as customer_email, 
                         COALESCE(u.whatsapp_number, u.phone) as customer_phone,
                         h.name as hotel_name, h.address as hotel_address, h.city as hotel_city,
                         r.room_type, r.price_per_night
                  FROM {$this->table} p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  LEFT JOIN users u ON b.customer_id = u.id
                  LEFT JOIN hotels h ON b.hotel_id = h.id
                  LEFT JOIN rooms r ON b.room_id = r.id
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
     * Confirm payment
     */
    public function confirm($paymentId, $adminId) {
        try {
            $this->beginTransaction();

            $query = "UPDATE {$this->table} SET payment_status = 'verified', verified_by = :admin_id, verified_at = NOW() WHERE id = :id";
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->execute();

            $this->query("SELECT booking_id FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $paymentId);
            $payment = $this->single();

            if ($payment) {
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

    public function reject($paymentId, $adminId, $reason) {
        try {
            $this->beginTransaction();
            $query = "UPDATE {$this->table} SET payment_status = 'rejected', verified_by = :admin_id, verified_at = NOW(), rejection_reason = :reason WHERE id = :id";
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':reason', $reason);
            $this->execute();

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