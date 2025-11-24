<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Room extends Model {
    protected $table = 'rooms';

    public function getByOwner($ownerId) {
        // Join dengan tabel hotels untuk memastikan kamar milik hotel si owner
        $query = "SELECT r.*, h.name as hotel_name 
                  FROM {$this->table} r 
                  JOIN hotels h ON r.hotel_id = h.id 
                  WHERE h.owner_id = :owner_id 
                  ORDER BY r.created_at DESC";
        
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultSet();
    }

    public function find($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create($data) {
        // SLOT LOGIC: available_slots di-set sama dengan total_slots saat pembuatan
        $query = "INSERT INTO {$this->table} 
                  (hotel_id, room_type, description, capacity, price_per_night, total_slots, available_slots, main_image, facilities, is_available) 
                  VALUES 
                  (:hotel_id, :room_type, :description, :capacity, :price_per_night, :total_slots, :total_slots, :main_image, :facilities, 1)";
        
        try {
            $this->db->query($query);
            // Bind parameter manual karena kita menggunakan total_slots dua kali
            $this->db->bind(':hotel_id', $data['hotel_id']);
            $this->db->bind(':room_type', $data['room_type']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':capacity', $data['capacity']);
            $this->db->bind(':price_per_night', $data['price_per_night']);
            $this->db->bind(':total_slots', $data['total_slots']);
            $this->db->bind(':main_image', $data['main_image']);
            $this->db->bind(':facilities', $data['facilities']);
            
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        // Logic update slot bisa lebih kompleks (misal ada booking aktif), 
        // tapi untuk MVP kita update slot dan sesuaikan availability
        $query = "UPDATE {$this->table} SET 
                  room_type = :room_type, price_per_night = :price, 
                  capacity = :capacity, total_slots = :total_slots,
                  description = :description, facilities = :facilities,
                  main_image = :main_image
                  WHERE id = :id";

        try {
            $this->db->query($query);
            $this->db->bind(':room_type', $data['room_type']);
            $this->db->bind(':price', $data['price_per_night']);
            $this->db->bind(':capacity', $data['capacity']);
            $this->db->bind(':total_slots', $data['total_slots']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':facilities', $data['facilities']);
            $this->db->bind(':main_image', $data['main_image']);
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        // Pastikan hanya menghapus jika tidak ada booking aktif (opsional validation)
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}