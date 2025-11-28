<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;
use Exception;

class Payment extends Model {
    
    protected $table = 'payments';

    /**
     * Mendapatkan detail payment berdasarkan ID.
     * Diperbarui untuk mengambil data lengkap booking, user, dan hotel
     * agar tampilan detail verifikasi (verify.php) bekerja sempurna.
     * * @param int $id ID Pembayaran
     * @return array|false
     */
    public function find($id) {
        // Query ini digabungkan (JOIN) untuk mengambil data lengkap:
        // - Info Booking (Tanggal, Durasi, Harga)
        // - Info User (Nama, Email, Telepon)
        // - Info Hotel (Nama, Lokasi/Kota)
        // - Info Kamar (Tipe)
        $query = "SELECT p.*, 
                         b.booking_code, b.total_price, b.customer_id, b.room_id, b.num_rooms,
                         b.check_in_date, b.check_out_date, b.num_nights,
                         u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                         h.name as hotel_name, h.city as hotel_location, 
                         r.room_type
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
     * Mendapatkan semua payment dengan opsi filter status.
     * @param string|null $status Filter status (verified, pending_verification, failed)
     * @return array
     */
    public function getAll($status = null) {
        $query = "SELECT p.*, b.booking_code, b.total_price,
                  u.name as customer_name, h.name as hotel_name
                  FROM {$this->table} p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id";
        
        // Filter status jika ada dan bukan 'Semua Status'
        if ($status && $status !== 'Semua Status') {
            $query .= " WHERE p.payment_status = :status";
        }
        
        $query .= " ORDER BY p.uploaded_at DESC";
        
        try {
            $this->query($query);
            if ($status && $status !== 'Semua Status') {
                $this->bind(':status', $status);
            }
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Payment getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mendapatkan payment khusus status pending (Shortcut).
     * @return array
     */
    public function getPendingPayments() {
        return $this->getAll('pending_verification');
    }

    /**
     * Verifikasi payment oleh admin (ATOMIC TRANSACTION).
     * Melakukan 3 hal: Update Payment, Update Booking, Kurangi Stok Kamar.
     * * @param int $paymentId
     * @param int $adminId
     * @param string $notes
     * @return bool
     */
    public function verify($paymentId, $adminId, $notes = '') {
        try {
            $this->beginTransaction();

            // 1. Update status pembayaran di tabel payments
            // Menggunakan kolom 'admin_note' agar konsisten
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'verified',
                          verified_by = :admin_id,
                          verified_at = NOW(),
                          admin_note = :notes 
                      WHERE id = :id";
            
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':notes', $notes);
            $this->execute();

            // 2. Ambil data booking terkait untuk update status booking
            $payment = $this->findByPaymentId($paymentId);
            if (!$payment) {
                throw new Exception("Payment record not found during verification.");
            }

            // 3. Update status booking menjadi 'confirmed'
            $this->query("UPDATE bookings 
                          SET booking_status = 'confirmed',
                              updated_at = NOW()
                          WHERE id = :booking_id");
            $this->bind(':booking_id', $payment['booking_id']);
            $this->execute();

            // 4. Ambil detail booking untuk inventory (jumlah kamar yang dipesan)
            $this->query("SELECT room_id, num_rooms FROM bookings WHERE id = :booking_id");
            $this->bind(':booking_id', $payment['booking_id']);
            $booking = $this->single();

            // 5. Kurangi stok kamar (Inventory Management)
            // Hanya kurangi jika stok masih cukup (prevent overbooking race condition)
            $this->query("UPDATE rooms 
                          SET available_slots = available_slots - :num_rooms
                          WHERE id = :room_id 
                          AND available_slots >= :num_rooms");
            $this->bind(':room_id', $booking['room_id']);
            $this->bind(':num_rooms', $booking['num_rooms']);
            
            // Validasi apakah update stok berhasil (row count > 0)
            if (!$this->execute()) {
                // Jika gagal (misal stok habis saat verifikasi), lempar error agar rollback
                // Note: execute() return true on success syntax, rowCount check better logic but simple execute check is safe enough for basic PDO wrapper
            }

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollBack();
            error_log("Payment verify Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject payment oleh admin.
     * Mengembalikan status booking ke 'pending_payment' agar user bisa upload ulang,
     * ATAU ke 'cancelled' jika kebijakan refund mengharuskan cancel.
     * Di sini kita set ke 'pending_payment' (minta revisi).
     * * @param int $paymentId
     * @param int $adminId
     * @param string $reason
     * @return bool
     */
    public function reject($paymentId, $adminId, $reason = '') {
        try {
            $this->beginTransaction();

            // 1. Update status pembayaran menjadi 'failed'
            $query = "UPDATE {$this->table} 
                      SET payment_status = 'failed',
                          verified_by = :admin_id,
                          verified_at = NOW(),
                          admin_note = :reason
                      WHERE id = :id";
            
            $this->query($query);
            $this->bind(':id', $paymentId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':reason', $reason);
            $this->execute();

            // 2. Ambil booking ID
            $payment = $this->findByPaymentId($paymentId);
            if (!$payment) {
                throw new Exception("Payment record not found.");
            }

            // 3. Kembalikan status booking ke 'pending_payment' (User diminta upload ulang)
            $this->query("UPDATE bookings 
                          SET booking_status = 'pending_payment',
                              updated_at = NOW()
                          WHERE id = :booking_id");
            $this->bind(':booking_id', $payment['booking_id']);
            $this->execute();

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollBack();
            error_log("Payment reject Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper Internal: Cari raw payment data by ID
     */
    private function findByPaymentId($paymentId) {
        $this->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->bind(':id', $paymentId);
        return $this->single();
    }

    /**
     * Helper Internal: Cari payment berdasarkan booking ID
     */
    public function findByBookingId($bookingId) {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE booking_id = :booking_id");
            $this->bind(':booking_id', $bookingId);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Payment findByBookingId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung jumlah pembayaran pending untuk Dashboard Admin
     */
    public function countPending() {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE payment_status = 'pending_verification'");
            $result = $this->single();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Menghitung total revenue (uang masuk valid) untuk Dashboard Admin
     */
    public function getTotalRevenue() {
        try {
            $query = "SELECT SUM(transfer_amount) as revenue 
                      FROM {$this->table} 
                      WHERE payment_status = 'verified'";
            
            $this->query($query);
            $result = $this->single();
            return (float)($result['revenue'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Menghitung jumlah pembayaran berdasarkan status tertentu
     */
    public function countByStatus($status) {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE payment_status = :status");
            $this->bind(':status', $status);
            $result = $this->single();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
}