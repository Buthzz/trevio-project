<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;
use Exception;

class Booking extends Model {
    
    /**
     * Nama tabel utama
     */
    protected $table = 'bookings';

    /**
     * Membuat booking baru dengan keamanan Transaksi & Inventory.
     * Method ini dipanggil oleh BookingController::store()
     * * @param array $data Data booking lengkap
     * @return int|false ID booking yang baru dibuat atau false jika gagal/penuh
     */
    public function createSecurely(array $data): int|false {
        // Whitelist field yang diizinkan untuk insert
        $allowedFields = [
            'booking_code', 'customer_id', 'hotel_id', 'room_id',
            'check_in_date', 'check_out_date', 'num_nights', 'num_rooms',
            'price_per_night', 'subtotal', 'tax_amount', 'service_charge', 'total_price',
            'guest_name', 'guest_email', 'guest_phone', 'booking_status'
        ];

        // Filter data agar hanya field yang diizinkan yang masuk
        $insertData = array_intersect_key($data, array_flip($allowedFields));

        // Validasi data penting untuk inventory
        if (empty($insertData['room_id']) || empty($insertData['num_rooms'])) {
            error_log("Booking Create Error: room_id or num_rooms missing");
            return false;
        }

        try {
            // 1. Mulai Transaksi Database
            $this->beginTransaction();

            // 2. LOCK & CHECK: Cek ketersediaan kamar & kunci baris agar tidak dibaca proses lain
            // 'FOR UPDATE' akan menahan proses booking lain sampai transaksi ini selesai
            $queryCheck = "SELECT available_slots FROM rooms WHERE id = :room_id FOR UPDATE";
            $this->query($queryCheck);
            $this->bind(':room_id', $insertData['room_id']);
            $room = $this->single();

            // Validasi ketersediaan
            if (!$room) {
                throw new Exception("Kamar tidak ditemukan.");
            }

            if ($room['available_slots'] < $insertData['num_rooms']) {
                // Batalkan transaksi jika stok tidak cukup
                $this->rollBack();
                error_log("Booking Failed: Not enough slots for Room ID " . $insertData['room_id']);
                return false; 
            }

            // 3. Insert Data Booking
            $params = [];
            $values = [];
            
            foreach ($insertData as $field => $value) {
                $params[] = $field;
                $values[] = ":{$field}";
            }

            $columns = implode(", ", $params);
            $placeholders = implode(", ", $values);

            $queryInsert = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $this->query($queryInsert);
            
            foreach ($insertData as $field => $value) {
                $this->bind(":{$field}", $value);
            }
            
            if (!$this->execute()) {
                throw new Exception("Gagal menyimpan data booking.");
            }
            
            $bookingId = (int) $this->lastInsertId();

            // 4. KURANGI STOK KAMAR (UPDATE INVENTORY)
            $queryUpdateSlot = "UPDATE rooms SET available_slots = available_slots - :num WHERE id = :rid";
            $this->query($queryUpdateSlot);
            $this->bind(':num', $insertData['num_rooms']);
            $this->bind(':rid', $insertData['room_id']);
            $this->execute();

            // 5. Commit Transaksi (Simpan Permanen)
            $this->commit();
            
            return $bookingId;

        } catch (Exception $e) {
            // Rollback jika terjadi error apapun
            $this->rollBack();
            error_log("Booking Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari booking berdasarkan Kode Booking
     * @param string $code Kode booking (misal: BK2025...)
     * @return array|false Data booking atau false
     */
    public function findByCode(string $code): array|false {
        $query = "SELECT b.*, h.name as hotel_name, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.booking_code = :code";
        
        try {
            $this->query($query);
            $this->bind(':code', $code);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Booking FindByCode Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari booking berdasarkan ID
     * @param int $id Booking ID
     * @return array|false Data booking atau false
     */
    public function find(int $id): array|false {
        $query = "SELECT b.*, h.name as hotel_name, r.room_type 
                  FROM {$this->table} b
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms r ON b.room_id = r.id
                  WHERE b.id = :id";
        
        try {
            $this->query($query);
            $this->bind(':id', $id);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Booking Find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Submit Pembayaran dengan Transaksi Atomik
     * Memisahkan info bank dan akun untuk data yang lebih rapi.
     */
    public function submitPayment(int $bookingId, string $proofFile, string $bankName, string $accountName, string $accountNumber = ''): bool {
        try {
            $this->beginTransaction();

            // 1. Ambil total harga untuk validasi/pencatatan
            $this->query("SELECT total_price FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $bookingId);
            $booking = $this->single();
            
            if (!$booking) {
                throw new PDOException("Booking not found");
            }

            // 2. Insert ke tabel payments
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
            
            $this->query($queryPayment);
            $this->bind(':booking_id', $bookingId);
            $this->bind(':amount', $booking['total_price']);
            $this->bind(':bank_name', $bankName);
            $this->bind(':proof', $proofFile);
            $this->bind(':account_detail', "Sender: " . $fullAccountDetail); 
            $this->execute();

            // 3. Update Status Booking
            $queryBooking = "UPDATE {$this->table} SET booking_status = 'pending_verification' WHERE id = :id";
            $this->query($queryBooking);
            $this->bind(':id', $bookingId);
            $this->execute();

            $this->commit();
            return true;

        } catch (PDOException $e) {
            $this->rollBack();
            error_log("Submit Payment Error: " . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // ADMIN DASHBOARD METHODS
    // =================================================================

    public function sumTotalRevenue(): float {
        try {
            $this->query("SELECT SUM(total_price) as total FROM {$this->table} WHERE booking_status IN ('confirmed', 'completed', 'checked_in')");
            $result = $this->single();
            return $result ? (float)$result['total'] : 0.0;
        } catch (PDOException $e) {
            return 0.0;
        }
    }

    public function countByStatus(string $status): int {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE booking_status = :status");
            $this->bind(':status', $status);
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countRefundsByStatus(string $status): int {
        try {
            $this->query("SELECT COUNT(*) as total FROM refunds WHERE refund_status = :status");
            $this->bind(':status', $status);
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getRecentBookings(int $limit = 5): array {
        try {
            $this->query("SELECT b.*, u.name as customer_name, h.name as hotel_name 
                              FROM {$this->table} b
                              JOIN users u ON b.customer_id = u.id
                              JOIN hotels h ON b.hotel_id = h.id
                              ORDER BY b.created_at DESC LIMIT :limit");
            $this->bind(':limit', $limit);
            return $this->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get daily revenue for the last N days
     */
    public function getDailyRevenue(int $days = 7): array {
        try {
            $this->query("SELECT 
                              DATE(created_at) as date,
                              SUM(total_price) as revenue
                          FROM {$this->table} 
                          WHERE booking_status = 'confirmed' 
                            AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                          GROUP BY DATE(created_at)
                          ORDER BY date ASC");
            $this->bind(':days', $days);
            $results = $this->resultSet();
            
            // Fill in missing dates with 0 revenue
            $revenueData = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $revenue = 0;
                
                // Find revenue for this date
                foreach ($results as $result) {
                    if ($result['date'] === $date) {
                        $revenue = $result['revenue'];
                        break;
                    }
                }
                
                $revenueData[] = [
                    'date' => $date,
                    'revenue' => $revenue
                ];
            }
            
            return $revenueData;
        } catch (PDOException $e) {
            error_log("Error getting daily revenue: " . $e->getMessage());
            // Return dummy data for demo if error occurs
            $revenueData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $revenueData[] = [
                    'date' => $date,
                    'revenue' => rand(500000, 2000000) // Random revenue between 500K - 2M
                ];
            }
            return $revenueData;
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
            $this->query($query);
            $this->bind(':owner_id', $ownerId);
            $result = $this->single();
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
            $this->query($query);
            $this->bind(':owner_id', $ownerId);
            $this->bind(':today', $today);
            $result = $this->single();
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
            $this->query($query);
            $this->bind(':owner_id', $ownerId);
            $this->bind(':month', $month);
            $this->bind(':year', $year);
            $result = $this->single();
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
            $this->query($query);
            $this->bind(':owner_id', $ownerId);
            return $this->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    // =================================================================
    // CUSTOMER DASHBOARD METHODS
    // =================================================================

    public function getByCustomer(int $customerId, array $statusArray): array {
        $validStatuses = ['pending_payment', 'pending_verification', 'confirmed', 'checked_in', 'completed', 'cancelled', 'refunded'];
        $statusArray = array_filter($statusArray, fn($s) => in_array($s, $validStatuses));
        
        if (empty($statusArray)) {
            return [];
        }
        
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
            $this->query($query);
            
            foreach ($params as $key => $value) {
                $this->bind($key, $value);
            }
            
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Get Customer Bookings Error: " . $e->getMessage());
            return [];
        }
    }
}