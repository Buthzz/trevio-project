<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Booking extends Model {
    
    protected $table = 'bookings';

    /**
     * Membuat booking baru
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $fields = [
            'booking_code', 'customer_id', 'hotel_id', 'room_id',
            'check_in_date', 'check_out_date', 'num_nights', 'num_rooms',
            'price_per_night', 'subtotal', 'tax_amount', 'service_charge', 'total_price',
            'guest_name', 'guest_email', 'guest_phone', 'booking_status'
        ];

        $params = [];
        $values = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $params[] = $field;
                $values[] = ":{$field}";
            }
        }

        $query = "INSERT INTO {$this->table} (" . implode(", ", $params) . ") VALUES (" . implode(", ", $values) . ")";

        try {
            $this->db->query($query);
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $this->db->bind(":{$field}", $data[$field]);
                }
            }
            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Booking Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari booking berdasarkan Kode Booking (untuk validasi & detail)
     */
    public function findByCode($code) {
        $query = "SELECT b.*, h.name as hotel_name, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.booking_code = :code";
        
        $this->db->query($query);
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    /**
     * Submit Pembayaran (Customer Upload Bukti)
     * - Insert ke tabel payments
     * - Update status booking jadi 'pending_verification'
     */
    public function submitPayment($bookingId, $proofFile, $bankName, $accountName) {
        try {
            $this->db->beginTransaction();

            // 1. Insert Payment
            $queryPayment = "INSERT INTO payments (booking_id, payment_method, transfer_from_bank, payment_proof, payment_status, created_at) 
                             VALUES (:booking_id, 'bank_transfer', :bank_name, :proof, 'uploaded', NOW())";
            
            $this->db->query($queryPayment);
            $this->db->bind(':booking_id', $bookingId);
            $this->db->bind(':bank_name', $bankName . ' - ' . $accountName); // Gabung info bank
            $this->db->bind(':proof', $proofFile);
            $this->db->execute();

            // 2. Update Booking Status
            $queryBooking = "UPDATE {$this->table} SET booking_status = 'pending_verification' WHERE id = :id";
            $this->db->query($queryBooking);
            $this->db->bind(':id', $bookingId);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Submit Payment Error: " . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // ADMIN DASHBOARD METHODS
    // =================================================================

    public function sumTotalRevenue() {
        // Menghitung total dari booking yang sudah confirmed/completed
        $this->db->query("SELECT SUM(total_price) as total FROM {$this->table} WHERE booking_status IN ('confirmed', 'completed', 'checked_in')");
        $result = $this->db->single();
        return $result ? (float)$result['total'] : 0;
    }

    public function countByStatus($status) {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE booking_status = :status");
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function countRefundsByStatus($status) {
        $this->db->query("SELECT COUNT(*) as total FROM refunds WHERE refund_status = :status");
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function getRecentBookings($limit = 5) {
        $this->db->query("SELECT b.*, u.name as customer_name, h.name as hotel_name 
                          FROM {$this->table} b
                          JOIN users u ON b.customer_id = u.id
                          JOIN hotels h ON b.hotel_id = h.id
                          ORDER BY b.created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // =================================================================
    // OWNER DASHBOARD METHODS
    // =================================================================

    public function countActiveByOwner($ownerId) {
        $query = "SELECT COUNT(b.id) as total 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id 
                  AND b.booking_status IN ('confirmed', 'checked_in', 'pending_verification')";
        
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function countCheckinTodayByOwner($ownerId) {
        $today = date('Y-m-d');
        $query = "SELECT COUNT(b.id) as total 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id 
                  AND b.check_in_date = :today 
                  AND b.booking_status = 'confirmed'";
        
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        $this->db->bind(':today', $today);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function calculateRevenueByOwner($ownerId, $month, $year) {
        $query = "SELECT SUM(b.total_price) as total 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id 
                  AND MONTH(b.created_at) = :month 
                  AND YEAR(b.created_at) = :year
                  AND b.booking_status IN ('confirmed', 'completed', 'checked_in')";
        
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        $this->db->bind(':month', $month);
        $this->db->bind(':year', $year);
        $result = $this->db->single();
        return $result ? (float)$result['total'] : 0;
    }

    public function getWeeklyStatsByOwner($ownerId) {
        // Mengambil data booking 7 hari terakhir untuk chart
        $query = "SELECT DATE(b.created_at) as date, COUNT(*) as count, SUM(b.total_price) as revenue
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id
                  AND b.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY DATE(b.created_at)
                  ORDER BY date ASC";
        
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultSet();
    }

    // =================================================================
    // CUSTOMER DASHBOARD METHODS
    // =================================================================

    public function getByCustomer($customerId, $statusArray) {
        // Mengubah array status menjadi string placeholder, misal: 'confirmed','completed'
        $placeholders = implode(',', array_map(function($s) { return "'$s'"; }, $statusArray));
        
        // Query raw karena IN clause dengan array binding di PDO agak trik
        // (Demi keamanan, pastikan statusArray hanya berisi string valid dari controller)
        $query = "SELECT b.*, h.name as hotel_name, h.city, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.customer_id = :customer_id 
                  AND b.booking_status IN ($placeholders)
                  ORDER BY b.created_at DESC";

        $this->db->query($query);
        $this->db->bind(':customer_id', $customerId);
        return $this->db->resultSet();
    }
}