<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Hotel extends Model {
    protected $table = 'hotels';

    /**
     * Menghitung total semua hotel untuk Admin Dashboard.
     */
    public function countAll(): int {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table}");
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * [ADMIN] Mengambil semua hotel dengan filter untuk Admin Panel.
     * Termasuk data pemilik (owner).
     */
    public function getForAdmin(array $filters = []) {
        $query = "SELECT h.*, u.name as owner_name, u.email as owner_email,
                  (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id) as total_rooms
                  FROM {$this->table} h
                  JOIN users u ON h.owner_id = u.id
                  WHERE 1=1";
        
        $params = [];

        // Filter status verifikasi
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'pending') {
                $query .= " AND h.is_verified = 0";
            } elseif ($filters['status'] === 'verified') {
                $query .= " AND h.is_verified = 1";
            }
        }

        // Filter pencarian nama/kota
        if (!empty($filters['search'])) {
            $query .= " AND (h.name LIKE :search OR h.city LIKE :search OR u.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $query .= " ORDER BY h.created_at DESC";

        try {
            $this->query($query);
            foreach ($params as $key => $val) {
                $this->bind($key, $val);
            }
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Hotel getForAdmin Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * [ADMIN] Verifikasi Hotel
     */
    public function verify(int $id): bool {
        try {
            $this->query("UPDATE {$this->table} SET is_verified = 1, is_active = 1 WHERE id = :id");
            $this->bind(':id', $id);
            return $this->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * [ADMIN] Hapus Hotel (Bypass owner check)
     */
    public function deleteByAdmin(int $id): bool {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $id);
            return $this->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // --- Method Owner & Public (EXISTING) ---

    public function getByOwner($ownerId) {
        $this->query("SELECT * FROM {$this->table} WHERE owner_id = :owner_id ORDER BY created_at DESC");
        $this->bind(':owner_id', $ownerId);
        return $this->resultSet();
    }

    public function find($id) {
        $this->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->bind(':id', $id);
        return $this->single();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (owner_id, name, description, address, city, province, star_rating, main_image, facilities, contact_phone, contact_email, is_active) 
                  VALUES 
                  (:owner_id, :name, :description, :address, :city, :province, :star_rating, :main_image, :facilities, :contact_phone, :contact_email, :is_active)";
        try {
            $this->query($query);
            foreach ($data as $key => $value) {
                $this->bind(":{$key}", $value);
            }
            $this->execute();
            return $this->lastInsertId();
        } catch (PDOException $e) { return false; }
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  name = :name, description = :description, address = :address, 
                  city = :city, contact_phone = :contact_phone, contact_email = :contact_email, 
                  facilities = :facilities, main_image = :main_image 
                  WHERE id = :id AND owner_id = :owner_id"; // Membatasi update hanya oleh owner
        try {
            $this->query($query);
            foreach ($data as $key => $value) {
                $this->bind(":{$key}", $value);
            }
            $this->bind(':id', $id);
            return $this->execute();
        } catch (PDOException $e) { return false; }
    }

    public function delete($id, $ownerId) {
        $this->query("DELETE FROM {$this->table} WHERE id = :id AND owner_id = :owner_id");
        $this->bind(':id', $id);
        $this->bind(':owner_id', $ownerId);
        return $this->execute();
    }
    
    public function countByOwner($ownerId) {
        $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE owner_id = :owner_id");
        $this->bind(':owner_id', $ownerId);
        $result = $this->single();
        return $result['total'] ?? 0;
    }
    
    public function getFeatured($limit = 8) {
        $query = "SELECT h.*, MIN(r.price_per_night) as min_price FROM {$this->table} h LEFT JOIN rooms r ON h.id = r.hotel_id WHERE h.is_active = 1 AND h.is_verified = 1 GROUP BY h.id ORDER BY h.average_rating DESC LIMIT " . (int)$limit;
        $this->query($query);
        return $this->resultSet();
    }
    
    public function search($filters = []) {
        $query = "SELECT h.*, MIN(r.price_per_night) as min_price FROM {$this->table} h LEFT JOIN rooms r ON h.id = r.hotel_id WHERE h.is_active = 1 AND h.is_verified = 1";
        $bindings = [];
        if (!empty($filters['city']) && $filters['city'] !== 'Semua Kota') {
            $query .= " AND h.city = :city"; $bindings[':city'] = $filters['city'];
        }
        if (!empty($filters['query'])) {
            $query .= " AND (h.name LIKE :query OR h.city LIKE :query)";
            $bindings[':query'] = '%' . $filters['query'] . '%';
        }
        $query .= " GROUP BY h.id ORDER BY h.average_rating DESC";
        $this->query($query);
        foreach ($bindings as $key => $value) $this->bind($key, $value);
        return $this->resultSet();
    }
    
    public function getPopularDestinations($limit = 6) {
        $this->query("SELECT city, COUNT(*) as hotel_count FROM {$this->table} WHERE is_active = 1 AND is_verified = 1 GROUP BY city ORDER BY hotel_count DESC LIMIT " . (int)$limit);
        $results = $this->resultSet();
        $destinations = ['🔥 Semua'];
        foreach ($results as $row) $destinations[] = $row['city'];
        return $destinations;
    }
    
    public function getDetailWithRooms($id) {
        $hotel = $this->find($id);
        if (!$hotel) return false;
        $this->query("SELECT * FROM rooms WHERE hotel_id = :hotel_id AND is_available = 1 ORDER BY price_per_night ASC");
        $this->bind(':hotel_id', $id);
        $hotel['rooms'] = $this->resultSet();
        if (!empty($hotel['facilities'])) $hotel['facilities'] = json_decode($hotel['facilities'], true) ?: [];
        return $hotel;
    }
    
    public function getFeaturedReviews($limit = 3, $minRating = 4.8) {
        $this->query("SELECT r.*, u.name as customer_name FROM reviews r JOIN users u ON r.customer_id = u.id WHERE r.rating >= :min LIMIT " . (int)$limit);
        $this->bind(':min', $minRating);
        return $this->resultSet();
    }
}