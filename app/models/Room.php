<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Room extends Model {
    protected $table = 'rooms';

    /**
     * =========================================================
     * METHODS UNTUK FRONTEND / CUSTOMER
     * =========================================================
     */

    /**
     * Mencari satu data kamar berdasarkan ID
     */
    public function find(int $id) {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $id);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Room Find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil daftar kamar aktif berdasarkan ID Hotel.
     * [PENTING] Method ini diperlukan oleh HotelController::detail()
     */
    public function getByHotel(int $hotelId) {
        try {
            // Ambil kamar yang tersedia (is_available = 1) untuk ditampilkan ke customer
            $query = "SELECT * FROM {$this->table} WHERE hotel_id = :hotel_id AND is_available = 1";
            $this->query($query);
            $this->bind(':hotel_id', $hotelId);
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Room getByHotel Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update stok (inventory) kamar secara manual.
     * Helper tambahan jika diperlukan di luar transaksi booking otomatis.
     */
    public function updateStock(int $roomId, int $qty, string $operator = '-') {
        try {
            $op = ($operator === '+') ? '+' : '-';
            $query = "UPDATE {$this->table} SET available_slots = available_slots $op :qty WHERE id = :id";
            $this->query($query);
            $this->bind(':qty', $qty);
            $this->bind(':id', $roomId);
            return $this->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * =========================================================
     * METHODS UNTUK OWNER / ADMIN (CRUD)
     * =========================================================
     */

    public function getByOwner($ownerId) {
        // Join dengan tabel hotels untuk memastikan kamar milik hotel si owner
        $query = "SELECT r.*, h.name as hotel_name 
                  FROM {$this->table} r 
                  JOIN hotels h ON r.hotel_id = h.id 
                  WHERE h.owner_id = :owner_id 
                  ORDER BY r.created_at DESC";
        
        try {
            $this->query($query);
            $this->bind(':owner_id', $ownerId);
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Room getByOwner Error: " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        // PERBAIKAN: Menggunakan kolom 'amenities' (bukan facilities) sesuai database
        // PERBAIKAN: Menggunakan total_slots untuk available_slots awal
        $query = "INSERT INTO {$this->table} 
                  (hotel_id, room_type, description, capacity, price_per_night, total_slots, available_slots, main_image, amenities, is_available) 
                  VALUES 
                  (:hotel_id, :room_type, :description, :capacity, :price_per_night, :total_slots, :available_slots, :main_image, :amenities, 1)";
        
        try {
            $this->query($query);
            
            $this->bind(':hotel_id', $data['hotel_id']);
            $this->bind(':room_type', $data['room_type']); // Disimpan dari input 'room_name'
            $this->bind(':description', $data['description']);
            $this->bind(':capacity', $data['capacity']);
            $this->bind(':price_per_night', $data['price_per_night']);
            $this->bind(':total_slots', $data['total_slots']);
            $this->bind(':available_slots', $data['total_slots']); // Available = Total saat baru
            $this->bind(':main_image', $data['main_image']);
            $this->bind(':amenities', $data['amenities']); // JSON amenities
            
            if ($this->execute()) {
                return $this->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Room Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        // PERBAIKAN: Menggunakan kolom 'amenities'
        // Jika total_slots diubah, idealnya available_slots disesuaikan (logika ini bisa ditambahkan nanti)
        // Saat ini kita update data dasar saja.
        $query = "UPDATE {$this->table} SET 
                  room_type = :room_type, 
                  price_per_night = :price, 
                  capacity = :capacity, 
                  total_slots = :total_slots,
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