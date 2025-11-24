<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Payment extends Model {
    protected $table = 'payments';

    /**
     * Mendapatkan semua payment yang menunggu verifikasi
     * @return array
     */
    public function getPendingPayments() {
        $query = "SELECT p.*, b.booking_code, b.total_price, 
                  u.name as customer_name, u.email as customer_email,
                  h.name as hotel_name
                  FROM {$this->table} p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE p.payment_status = 'pending_verification'
                  ORDER BY p.uploaded_at DESC";
        
        try {
            $this->query($query);
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Payment getPendingPayments Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mendapatkan semua payment dengan filter status
     * @param string|null $status
     * @return array
     */
    public function getAll($status = null) {
        $query = "SELECT p.*, b.booking_code, b.total_price,
                  u.name as customer_name, h.name as hotel_name
                  FROM {$this->table} p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id";
        
        if ($status) {
            $query .= " WHERE p.payment_status = :status";
        }
        
        $query .= " ORDER BY p.uploaded_at DESC";
        
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
     * Mendapatkan detail payment berdasarkan ID
     * @param int $id
     * @return array|false
     */
    public function find($id) {
        $query = "SELECT p.*, b.booking_code, b.total_price, b.customer_id,
                  u.name as customer_name, u.email as customer_email,
                  h.name as hotel_name, r.room_type
                  FROM {$this->table} p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE p.id = :id";
        
        try {
            $this->query($query);
            $this->bind(':id', $id);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Payment find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendapatkan payment berdasarkan booking_id
     * @param int $bookingId
     * @return array|false
     */
    public function findByBookingId($bookingId) {
        $query = "SELECT * FROM {$this->table} WHERE booking_id = :booking_id";
        
        try {
            $this->query($query);
            $this->bind(':booking_id', $bookingId);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Payment findByBookingId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifikasi payment oleh admin (ATOMIC TRANSACTION)
     * @param int $paymentId
     * @param int $adminId
     * @param string $notes
     * @return bool
     */
    public function verify($paymentId, $adminId, $notes = '') {
        try {
            $this->beginTransaction();

            // 1. Update payment status
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'verified',
                          verified_by = :admin_id,
                          verified_at = NOW(),
                          verification_notes = :notes
                      WHERE id = :id";
            
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':notes', $notes);
            $this->execute();

            // 2. Get booking_id and num_rooms
            $payment = $this->findByPaymentId($paymentId);
            if (!$payment) {
                throw new \Exception("Payment not found");
            }

            // 3. Update booking status to 'confirmed'
            $this->query("UPDATE bookings 
                             SET booking_status = 'confirmed',
                                 confirmed_at = NOW()
                             WHERE id = :booking_id");
            $this->bind(':booking_id', $payment['booking_id']);
            $this->execute();

            // 4. Get booking details for room slot reduction
            $this->query("SELECT room_id, num_rooms FROM bookings WHERE id = :booking_id");
            $this->bind(':booking_id', $payment['booking_id']);
            $booking = $this->single();

            // 5. Reduce room slots (ATOMIC)
            $this->query("UPDATE rooms 
                             SET available_slots = available_slots - :num_rooms
                             WHERE id = :room_id 
                             AND available_slots >= :num_rooms");
            $this->bind(':room_id', $booking['room_id']);
            $this->bind(':num_rooms', $booking['num_rooms']);
            
            if (!$this->execute()) {
                throw new \Exception("Failed to reduce room slots - insufficient availability");
            }

            $this->commit();
            return true;

        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Payment verify Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject payment oleh admin
     * @param int $paymentId
     * @param int $adminId
     * @param string $reason
     * @return bool
     */
    public function reject($paymentId, $adminId, $reason = '') {
        try {
            $this->beginTransaction();

            // 1. Update payment status
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

            // 2. Get booking_id
            $payment = $this->findByPaymentId($paymentId);
            if (!$payment) {
                throw new \Exception("Payment not found");
            }

            // 3. Update booking status back to 'pending_payment'
            $this->query("UPDATE bookings 
                             SET booking_status = 'pending_payment'
                             WHERE id = :booking_id");
            $this->bind(':booking_id', $payment['booking_id']);
            $this->execute();

            $this->commit();
            return true;

        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Payment reject Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper: Get payment by ID (internal use)
     * @param int $paymentId
     * @return array|false
     */
    private function findByPaymentId($paymentId) {
        $this->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->bind(':id', $paymentId);
        return $this->single();
    }

    /**
     * Count pending payments for dashboard stats
     * @return int
     */
    public function countPending() {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE payment_status = 'pending_verification'");
            $result = $this->single();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Payment countPending Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total verified payment amount (revenue)
     * @return float
     */
    public function getTotalRevenue() {
        try {
            $query = "SELECT SUM(b.total_price) as revenue 
                      FROM {$this->table} p
                      JOIN bookings b ON p.booking_id = b.id
                      WHERE p.payment_status = 'verified'";
            
            $this->query($query);
            $result = $this->single();
            return (float)($result['revenue'] ?? 0);
        } catch (PDOException $e) {
            error_log("Payment getTotalRevenue Error: " . $e->getMessage());
            return 0;
        }
    }
}
