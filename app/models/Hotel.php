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
    
    /**
     * Get featured hotels for homepage (verified & active, sorted by rating)
     * @param int $limit Number of hotels to return
     * @return array
     */
    public function getFeatured($limit = 8) {
        $query = "SELECT h.*, 
                  (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id AND is_available = 1) as available_rooms
                  FROM {$this->table} h
                  WHERE h.is_active = 1 AND h.is_verified = 1
                  ORDER BY h.average_rating DESC, h.total_reviews DESC
                  LIMIT :limit";
        
        $this->db->query($query);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * Get all active hotels with filtering
     * @param array $filters Search filters (city, rating, price, facilities)
     * @return array
     */
    public function search($filters = []) {
        $query = "SELECT h.*, 
                  MIN(r.price_per_night) as min_price,
                  (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id AND is_available = 1) as available_rooms
                  FROM {$this->table} h
                  LEFT JOIN rooms r ON h.id = r.hotel_id
                  WHERE h.is_active = 1 AND h.is_verified = 1";
        
        $bindings = [];
        
        // Filter by city
        if (!empty($filters['city']) && $filters['city'] !== 'Semua Kota') {
            $query .= " AND h.city = :city";
            $bindings[':city'] = $filters['city'];
        }
        
        // Filter by search query (name or description)
        if (!empty($filters['query'])) {
            $query .= " AND (h.name LIKE :query OR h.description LIKE :query OR h.city LIKE :query)";
            $bindings[':query'] = '%' . $filters['query'] . '%';
        }
        
        // Filter by rating
        if (!empty($filters['rating']) && $filters['rating'] !== 'Semua Rating') {
            $ratingFilter = floatval(str_replace('+', '', $filters['rating']));
            $query .= " AND h.average_rating >= :rating";
            $bindings[':rating'] = $ratingFilter;
        }
        
        $query .= " GROUP BY h.id";
        
        // Filter by price (after GROUP BY)
        if (!empty($filters['price']) && $filters['price'] !== 'Semua Harga') {
            switch ($filters['price']) {
                case '< 1 juta':
                    $query .= " HAVING min_price < 1000000";
                    break;
                case '1 - 2 juta':
                    $query .= " HAVING min_price BETWEEN 1000000 AND 2000000";
                    break;
                case '2 - 3 juta':
                    $query .= " HAVING min_price BETWEEN 2000000 AND 3000000";
                    break;
                case '> 3 juta':
                    $query .= " HAVING min_price > 3000000";
                    break;
            }
        }
        
        // Sorting
        $sortBy = $filters['sort'] ?? 'recommended';
        switch ($sortBy) {
            case 'lowest-price':
                $query .= " ORDER BY min_price ASC";
                break;
            case 'highest-price':
                $query .= " ORDER BY min_price DESC";
                break;
            case 'highest-rating':
                $query .= " ORDER BY h.average_rating DESC";
                break;
            default:
                $query .= " ORDER BY h.average_rating DESC, h.total_reviews DESC";
        }
        
        $this->db->query($query);
        foreach ($bindings as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Get popular destinations (cities with most hotels)
     * @param int $limit Number of cities to return
     * @return array
     */
    public function getPopularDestinations($limit = 6) {
        $query = "SELECT city, COUNT(*) as hotel_count
                  FROM {$this->table}
                  WHERE is_active = 1 AND is_verified = 1
                  GROUP BY city
                  ORDER BY hotel_count DESC
                  LIMIT :limit";
        
        $this->db->query($query);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $results = $this->db->resultSet();
        
        // Format for frontend
        $destinations = ['🔥 Semua']; // Default "All" option
        foreach ($results as $row) {
            $destinations[] = $row['city'];
        }
        
        return $destinations;
    }
    
    /**
     * Get hotel detail with rooms
     * @param int $id Hotel ID
     * @return array|false
     */
    public function getDetailWithRooms($id) {
        // Get hotel info
        $hotel = $this->find($id);
        if (!$hotel) {
            return false;
        }
        
        // Get available rooms
        $this->db->query("SELECT * FROM rooms WHERE hotel_id = :hotel_id AND is_available = 1 ORDER BY price_per_night ASC");
        $this->db->bind(':hotel_id', $id);
        $hotel['rooms'] = $this->db->resultSet();
        
        // Decode JSON fields
        if (!empty($hotel['facilities'])) {
            $hotel['facilities'] = json_decode($hotel['facilities'], true) ?: [];
        }
        
        return $hotel;
    }
}