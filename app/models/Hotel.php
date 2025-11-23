<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Hotel extends Model {
    protected $table = 'hotels';

    public function getByOwner($ownerId) {
        $this->db->query("SELECT * FROM {$this->table} WHERE owner_id = :owner_id ORDER BY created_at DESC");
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultSet();
    }

    public function find($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (owner_id, name, description, address, city, province, star_rating, main_image, facilities, contact_phone, contact_email, is_active) 
                  VALUES 
                  (:owner_id, :name, :description, :address, :city, :province, :star_rating, :main_image, :facilities, :contact_phone, :contact_email, :is_active)";
        
        try {
            $this->db->query($query);
            foreach ($data as $key => $value) {
                $this->db->bind(":{$key}", $value);
            }
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  name = :name, description = :description, address = :address, 
                  city = :city, contact_phone = :contact_phone, contact_email = :contact_email, 
                  facilities = :facilities, main_image = :main_image 
                  WHERE id = :id AND owner_id = :owner_id";

        try {
            $this->db->query($query);
            foreach ($data as $key => $value) {
                $this->db->bind(":{$key}", $value);
            }
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($id, $ownerId) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND owner_id = :owner_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->execute();
    }
    
    public function countByOwner($ownerId) {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE owner_id = :owner_id");
        $this->db->bind(':owner_id', $ownerId);
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }
}