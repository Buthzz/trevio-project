<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Room extends Model {
    protected $table = 'rooms';

    /**
     * Mengambil daftar kamar berdasarkan ID Owner (Untuk Dashboard Owner)
     */
    public function getByOwner($ownerId) {
        // Join dengan tabel hotels untuk memastikan kamar milik hotel si owner
        $query = "SELECT r.*, h.name as hotel_name 
                  FROM {$this->table} r 
                  JOIN hotels h ON r.hotel_id = h.id 
                  WHERE h.owner_id = :owner_id 
                  ORDER BY r.created_at DESC";
        
        $this->query($query);
        $this->bind(':owner_id', $ownerId);
        return $this->resultSet();
    }

    /**
     * Mengambil daftar kamar berdasarkan ID Hotel (Untuk Halaman Detail Hotel)
     */
    public function getByHotelId($hotelId) {
        $query = "SELECT * FROM {$this->table} WHERE hotel_id = :hotel_id ORDER BY price_per_night ASC";
        $this->query($query);
        $this->bind(':hotel_id', $hotelId);
        return $this->resultSet();
    }

    /**
     * Mencari satu kamar berdasarkan ID
     */
    public function find($id) {
        $this->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->bind(':id', $id);
        return $this->single();
    }

    /**
     * [FITUR UTAMA] Cek Ketersediaan Kamar secara Real-time
     * Digunakan di HotelController (Detail) dan BookingController
     */
    public function checkAvailability($roomId, $checkIn, $checkOut) {
        // 1. Hitung jumlah kamar yang SUDAH ter-booking di rentang tanggal tersebut
        // Logic Overlap: (StartA < EndB) and (EndA > StartB)
        // Status yang dihitung: pending_payment, confirmed. (cancelled/rejected/refunded diabaikan)
        
        $query = "SELECT SUM(num_rooms) as booked_count 
                  FROM bookings 
                  WHERE room_id = :room_id 
                  AND booking_status NOT IN ('cancelled', 'rejected', 'refunded')
                  AND (check_in_date < :check_out AND check_out_date > :check_in)";
        
        try {
            $this->query($query);
            $this->bind(':room_id', $roomId);
            $this->bind(':check_in', $checkIn);
            $this->bind(':check_out', $checkOut);
            
            $result = $this->single();
            $bookedCount = (int) ($result['booked_count'] ?? 0);

            // 2. Ambil total stok kamar ini dari database
            $room = $this->find($roomId);
            
            if (!$room) {
                return ['is_available' => false, 'remaining' => 0];
            }

            // Gunakan kolom 'total_slots' (atau 'quantity' sesuaikan dengan DB Anda, di sini pakai total_slots)
            $totalStock = (int) ($room['total_slots'] ?? 0); 
            $remaining = $totalStock - $bookedCount;

            return [
                'is_available' => $remaining > 0,
                'remaining' => max(0, $remaining), // Pastikan tidak negatif
                'booked' => $bookedCount,
                'total' => $totalStock
            ];

        } catch (PDOException $e) {
            error_log("Check Availability Error: " . $e->getMessage());
            // Default fail-safe
            return ['is_available' => false, 'remaining' => 0];
        }
    }

    /**
     * Membuat data kamar baru
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (hotel_id, room_type, description, capacity, price_per_night, total_slots, available_slots, main_image, amenities, is_available) 
                  VALUES 
                  (:hotel_id, :room_type, :description, :capacity, :price_per_night, :total_slots, :available_slots, :main_image, :amenities, 1)";
        
        try {
            $this->query($query);
            
            $this->bind(':hotel_id', $data['hotel_id']);
            $this->bind(':room_type', $data['room_type']);
            $this->bind(':description', $data['description']);
            $this->bind(':capacity', $data['capacity']);
            $this->bind(':price_per_night', $data['price_per_night']);
            $this->bind(':total_slots', $data['total_slots']);
            // Saat create, available = total (belum ada booking)
            $this->bind(':available_slots', $data['total_slots']); 
            $this->bind(':main_image', $data['main_image']);
            $this->bind(':amenities', $data['amenities']); // Pastikan format JSON
            
            if ($this->execute()) {
                return $this->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Room Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update data kamar
     */
    public function update($id, $data) {
        // Ambil data lama untuk menghitung selisih stok
        $oldData = $this->find($id);
        $stockDiff = 0;
        if ($oldData) {
            $stockDiff = (int)$data['total_slots'] - (int)$oldData['total_slots'];
        }

        $query = "UPDATE {$this->table} SET 
                  room_type = :room_type, 
                  price_per_night = :price, 
                  capacity = :capacity, 
                  total_slots = :total_slots,
                  available_slots = available_slots + :stock_diff,
                  description = :description, 
                  amenities = :amenities,
                  main_image = :main_image
                  WHERE id = :id";

        try {
            $this->query($query);
            $this->bind(':room_type', $data['room_type']);
            $this->bind(':price', $data['price_per_night']);
            $this->bind(':capacity', $data['capacity']);
            $this->bind(':total_slots', $data['total_slots']);
            $this->bind(':stock_diff', $stockDiff);
            $this->bind(':description', $data['description']);
            $this->bind(':amenities', $data['amenities']);
            $this->bind(':main_image', $data['main_image']);
            $this->bind(':id', $id);
            
            return $this->execute();
        } catch (PDOException $e) {
            error_log("Room Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus kamar
     */
    public function delete($id) {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $id);
            return $this->execute();
        } catch (PDOException $e) {
            error_log("Room Delete Error: " . $e->getMessage());
            return false;
        }
    }
}