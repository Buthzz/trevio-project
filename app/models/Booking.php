<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Booking extends Model {
    
    protected $table = 'bookings';

    /**
     * Membuat booking baru
     * @param array $data Booking data including customer_id, hotel_id, room_id, dates, pricing
     * @return int|false Booking ID if successful, false otherwise
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

        if (empty($params)) {
            error_log("Booking Create Error: No valid fields provided");
            return false;
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
     * @param string $code Booking code
     * @return array|false Booking details with hotel and room info, or false if not found
     */
    public function findByCode($code) {
        $query = "SELECT b.*, h.name as hotel_name, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.booking_code = :code";
        
        try {
            $this->db->query($query);
            $this->db->bind(':code', $code);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("Booking FindByCode Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Submit Pembayaran (Customer Upload Bukti)
     * - Insert ke tabel payments
     * - Update status booking jadi 'pending_verification'
     * @param int $bookingId
     * @param string $proofFile
     * @param string $bankName
     * @param string $accountName
     * @return bool
     */
    public function submitPayment($bookingId, $proofFile, $bankName, $accountName) {
        try {
            $this->db->beginTransaction();

            // 0. Get booking amount
            $this->db->query("SELECT total_price FROM {$this->table} WHERE id = :id");
            $this->db->bind(':id', $bookingId);
            $booking = $this->db->single();
            
            if (!$booking) {
                throw new PDOException("Booking not found");
            }

            // 1. Insert Payment
            $queryPayment = "INSERT INTO payments (booking_id, payment_method, transfer_amount, transfer_from_bank, payment_proof, payment_status, created_at) 
                             VALUES (:booking_id, 'bank_transfer', :amount, :bank_name, :proof, 'uploaded', NOW())";
            
            $this->db->query($queryPayment);
            $this->db->bind(':booking_id', $bookingId);
            $this->db->bind(':amount', $booking['total_price']);
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

    /**
     * Menghitung total revenue dari semua booking yang sudah confirmed/completed
     * @return float Total revenue amount
     */
    public function sumTotalRevenue() {
        try {
            // Menghitung total dari booking yang sudah confirmed/completed
            $this->db->query("SELECT SUM(total_price) as total FROM {$this->table} WHERE booking_status IN ('confirmed', 'completed', 'checked_in')");
            $result = $this->db->single();
            return $result ? (float)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Booking SumTotalRevenue Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menghitung jumlah booking berdasarkan status
     * @param string $status Booking status to count
     * @return int Number of bookings with specified status
     */
    public function countByStatus($status) {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE booking_status = :status");
            $this->db->bind(':status', $status);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Booking CountByStatus Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menghitung jumlah refund berdasarkan status
     * @param string $status Refund status to count
     * @return int Number of refunds with specified status
     */
    public function countRefundsByStatus($status) {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM refunds WHERE refund_status = :status");
            $this->db->bind(':status', $status);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Booking CountRefundsByStatus Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mendapatkan booking terbaru untuk dashboard
     * @param int $limit Number of recent bookings to retrieve
     * @return array List of recent bookings with customer and hotel information
     */
    public function getRecentBookings($limit = 5) {
        try {
            $this->db->query("SELECT b.*, u.name as customer_name, h.name as hotel_name 
                              FROM {$this->table} b
                              JOIN users u ON b.customer_id = u.id
                              JOIN hotels h ON b.hotel_id = h.id
                              ORDER BY b.created_at DESC LIMIT :limit");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Booking GetRecentBookings Error: " . $e->getMessage());
            return [];
        }
    }

    // =================================================================
    // OWNER DASHBOARD METHODS
    // =================================================================

    /**
     * Menghitung booking aktif untuk hotel owner tertentu
     * @param int $ownerId Owner user ID
     * @return int Number of active bookings for this owner
     */
    public function countActiveByOwner($ownerId) {
        $query = "SELECT COUNT(b.id) as total 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id 
                  AND b.booking_status IN ('confirmed', 'checked_in', 'pending_verification')";
        
        try {
            $this->db->query($query);
            $this->db->bind(':owner_id', $ownerId);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Booking CountActiveByOwner Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menghitung check-in hari ini untuk hotel owner
     * @param int $ownerId Owner user ID
     * @return int Number of check-ins scheduled for today
     */
    public function countCheckinTodayByOwner($ownerId) {
        $today = date('Y-m-d');
        $query = "SELECT COUNT(b.id) as total 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id 
                  AND b.check_in_date = :today 
                  AND b.booking_status = 'confirmed'";
        
        try {
            $this->db->query($query);
            $this->db->bind(':owner_id', $ownerId);
            $this->db->bind(':today', $today);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Booking CountCheckinTodayByOwner Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menghitung revenue untuk owner berdasarkan bulan dan tahun
     * @param int $ownerId Owner user ID
     * @param int $month Month (1-12)
     * @param int $year Year (e.g., 2025)
     * @return float Total revenue for specified month/year
     */
    public function calculateRevenueByOwner($ownerId, $month, $year) {
        $query = "SELECT SUM(b.total_price) as total 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id 
                  AND MONTH(b.created_at) = :month 
                  AND YEAR(b.created_at) = :year
                  AND b.booking_status IN ('confirmed', 'completed', 'checked_in')";
        
        try {
            $this->db->query($query);
            $this->db->bind(':owner_id', $ownerId);
            $this->db->bind(':month', $month);
            $this->db->bind(':year', $year);
            $result = $this->db->single();
            return $result ? (float)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Booking CalculateRevenueByOwner Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mendapatkan statistik mingguan untuk owner (7 hari terakhir)
     * @param int $ownerId Owner user ID
     * @return array Weekly booking statistics with dates, counts, and revenue
     */
    public function getWeeklyStatsByOwner($ownerId) {
        // Mengambil data booking 7 hari terakhir untuk chart
        $query = "SELECT DATE(b.created_at) as date, COUNT(*) as count, SUM(b.total_price) as revenue
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE h.owner_id = :owner_id
                  AND b.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY DATE(b.created_at)
                  ORDER BY date ASC";
        
        try {
            $this->db->query($query);
            $this->db->bind(':owner_id', $ownerId);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Booking GetWeeklyStatsByOwner Error: " . $e->getMessage());
            return [];
        }
    }

    // =================================================================
    // CUSTOMER DASHBOARD METHODS
    // =================================================================

    /**
     * Mendapatkan booking customer berdasarkan status
     * @param int $customerId Customer user ID
     * @param array $statusArray Array of booking statuses to filter (e.g., ['confirmed', 'completed'])
     * @return array List of customer bookings with hotel and room information
     */
    public function getByCustomer($customerId, $statusArray) {
        // Security: Validate and sanitize status array to prevent SQL injection
        $validStatuses = ['pending_payment', 'pending_verification', 'confirmed', 'checked_in', 'completed', 'cancelled', 'refunded'];
        $statusArray = array_filter($statusArray, function($status) use ($validStatuses) {
            return in_array($status, $validStatuses);
        });
        
        if (empty($statusArray)) {
            return []; // Return empty if no valid statuses
        }
        
        // Create placeholders for prepared statement
        $placeholders = implode(',', array_fill(0, count($statusArray), '?'));
        
        $query = "SELECT b.*, h.name as hotel_name, h.city, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.customer_id = ? 
                  AND b.booking_status IN ($placeholders)
                  ORDER BY b.created_at DESC";

        try {
            $this->db->query($query);
            // Bind customer ID first
            $this->db->bind(1, $customerId);
            // Bind each status value
            $position = 2;
            foreach ($statusArray as $status) {
                $this->db->bind($position++, $status);
            }
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Get Customer Bookings Error: " . $e->getMessage());
            return [];
        }
    }
}