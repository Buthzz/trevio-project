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
        
        $this->query($query);
        $this->bind(':owner_id', $ownerId);
        return $this->resultSet();
    }

    public function find($id) {
        $this->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->bind(':id', $id);
        return $this->single();
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