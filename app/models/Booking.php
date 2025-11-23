<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Booking extends Model {
    
    /**
     * Nama tabel utama
     */
    protected $table = 'bookings';

    /**
     * Membuat booking baru
     * * @param array $data Data booking lengkap
     * @return int|false ID booking yang baru dibuat atau false jika gagal
     */
    public function create(array $data): int|false {
        // Whitelist field yang diizinkan untuk insert
        $allowedFields = [
            'booking_code', 'customer_id', 'hotel_id', 'room_id',
            'check_in_date', 'check_out_date', 'num_nights', 'num_rooms',
            'price_per_night', 'subtotal', 'tax_amount', 'service_charge', 'total_price',
            'guest_name', 'guest_email', 'guest_phone', 'booking_status'
        ];

        // Filter data
        $data = array_intersect_key($data, array_flip($allowedFields));

        $params = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $params[] = $field;
            $values[] = ":{$field}";
        }

        if (empty($params)) {
            error_log("Booking Create Error: No valid fields provided");
            return false;
        }

        $columns = implode(", ", $params);
        $placeholders = implode(", ", $values);

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
            $this->db->query($query);
            
            foreach ($data as $field => $value) {
                $this->db->bind(":{$field}", $value);
            }
            
            if ($this->db->execute()) {
                return (int) $this->db->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            error_log("Booking Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari booking berdasarkan Kode Booking
     * * @param string $code Kode booking (misal: BK2025...)
     * @return array|false Data booking atau false
     */
    public function findByCode(string $code): array|false {
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
     * Mencari booking berdasarkan ID
     * * @param int $id Booking ID
     * @return array|false Data booking atau false
     */
    public function find(int $id): array|false {
        $query = "SELECT b.*, h.name as hotel_name, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.id = :id";
        
        try {
            $this->db->query($query);
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("Booking Find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Submit Pembayaran dengan Transaksi Atomik
     * Memisahkan info bank dan akun untuk data yang lebih rapi.
     * * @param int $bookingId
     * @param string $proofFile Nama file bukti transfer
     * @param string $bankName Nama Bank Pengirim
     * @param string $accountName Nama Pemilik Rekening
     * @param string $accountNumber Nomor Rekening (Opsional)
     * @return bool Status keberhasilan
     */
    public function submitPayment(int $bookingId, string $proofFile, string $bankName, string $accountName, string $accountNumber = ''): bool {
        try {
            $this->db->beginTransaction();

            // 1. Ambil total harga untuk validasi/pencatatan
            $this->db->query("SELECT total_price FROM {$this->table} WHERE id = :id");
            $this->db->bind(':id', $bookingId);
            $booking = $this->db->single();
            
            if (!$booking) {
                throw new PDOException("Booking not found");
            }

            // 2. Insert ke tabel payments
            // Catatan: Idealnya tabel payments memiliki kolom 'transfer_from_account' dan 'transfer_from_number'.
            // Jika schema database belum update, kita simpan detail akun di 'payment_notes' atau kolom relevan.
            
            $fullAccountDetail = $accountName . ($accountNumber ? " ({$accountNumber})" : "");

            $queryPayment = "INSERT INTO payments (
                booking_id, payment_method, transfer_amount, 
                transfer_from_bank, payment_proof, payment_notes,
                payment_status, created_at
            ) VALUES (
                :booking_id, 'bank_transfer', :amount, 
                :bank_name, :proof, :account_detail,
                'uploaded', NOW()
            )";
            
            $this->db->query($queryPayment);
            $this->db->bind(':booking_id', $bookingId);
            $this->db->bind(':amount', $booking['total_price']);
            $this->db->bind(':bank_name', $bankName);
            $this->db->bind(':proof', $proofFile);
            $this->db->bind(':account_detail', "Sender: " . $fullAccountDetail); // Simpan detail pengirim
            $this->db->execute();

            // 3. Update Status Booking
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

    public function sumTotalRevenue(): float {
        try {
            $this->db->query("SELECT SUM(total_price) as total FROM {$this->table} WHERE booking_status IN ('confirmed', 'completed', 'checked_in')");
            $result = $this->db->single();
            return $result ? (float)$result['total'] : 0.0;
        } catch (PDOException $e) {
            return 0.0;
        }
    }

    public function countByStatus(string $status): int {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE booking_status = :status");
            $this->db->bind(':status', $status);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countRefundsByStatus(string $status): int {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM refunds WHERE refund_status = :status");
            $this->db->bind(':status', $status);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getRecentBookings(int $limit = 5): array {
        try {
            $this->db->query("SELECT b.*, u.name as customer_name, h.name as hotel_name 
                              FROM {$this->table} b
                              JOIN users u ON b.customer_id = u.id
                              JOIN hotels h ON b.hotel_id = h.id
                              ORDER BY b.created_at DESC LIMIT :limit");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // =================================================================
    // OWNER DASHBOARD METHODS
    // =================================================================

    public function countActiveByOwner(int $ownerId): int {
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
            return 0;
        }
    }

    public function countCheckinTodayByOwner(int $ownerId): int {
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
            return 0;
        }
    }

    public function calculateRevenueByOwner(int $ownerId, int $month, int $year): float {
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
            return $result ? (float)$result['total'] : 0.0;
        } catch (PDOException $e) {
            return 0.0;
        }
    }

    public function getWeeklyStatsByOwner(int $ownerId): array {
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
            return [];
        }
    }

    // =================================================================
    // CUSTOMER DASHBOARD METHODS
    // =================================================================

    /**
     * Mengambil data booking customer (Perbaikan Binding Parameter)
     * * @param int $customerId
     * @param array $statusArray
     * @return array
     */
    public function getByCustomer(int $customerId, array $statusArray): array {
        $validStatuses = ['pending_payment', 'pending_verification', 'confirmed', 'checked_in', 'completed', 'cancelled', 'refunded'];
        $statusArray = array_filter($statusArray, fn($s) => in_array($s, $validStatuses));
        
        if (empty($statusArray)) {
            return [];
        }
        
        // Generate Named Placeholders untuk binding yang aman (:status_0, :status_1, ...)
        $placeholders = [];
        $params = [':customer_id' => $customerId];
        
        foreach ($statusArray as $index => $status) {
            $key = ":status_{$index}";
            $placeholders[] = $key;
            $params[$key] = $status;
        }
        
        $inClause = implode(',', $placeholders);
        
        $query = "SELECT b.*, h.name as hotel_name, h.city, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.customer_id = :customer_id 
                  AND b.booking_status IN ($inClause)
                  ORDER BY b.created_at DESC";

        try {
            $this->db->query($query);
            
            // Bind parameter dinamis
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }
            
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Get Customer Bookings Error: " . $e->getMessage());
            return [];
        }
    }
}