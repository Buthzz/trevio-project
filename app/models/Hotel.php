<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Hotel extends Model {
    protected $table = 'hotels';

    /**
     * Mencari hotel dengan filter lengkap (Harga, Rating, Fasilitas, Sort)
     * [FIXED]: Menambahkan logika filter yang sebelumnya hilang.
     */
    public function search($filters = []) {
        // Query Dasar: Join ke tabel rooms untuk mendapatkan harga terendah (min_price) per hotel
        // Menggunakan LEFT JOIN agar hotel yang belum punya kamar tetap bisa dicek (meski min_price null)
        $query = "SELECT h.*, MIN(r.price_per_night) as min_price 
                  FROM {$this->table} h 
                  LEFT JOIN rooms r ON h.id = r.hotel_id 
                  WHERE h.is_active = 1 AND h.is_verified = 1";
        
        $bindings = [];

        // 1. Filter Kota
        if (!empty($filters['city']) && $filters['city'] !== 'Semua Kota') {
            $query .= " AND h.city = :city";
            $bindings[':city'] = $filters['city'];
        }

        // 2. Filter Search Query (Nama Hotel atau Kota)
        if (!empty($filters['query'])) {
            $query .= " AND (h.name LIKE :query OR h.city LIKE :query)";
            $bindings[':query'] = '%' . $filters['query'] . '%';
        }

        // 3. Filter Rating (Review Score)
        // Menangani input seperti '4.5' (artinya >= 4.5) atau '5' (artinya >= 4.8 atau 5)
        if (!empty($filters['rating']) && $filters['rating'] !== 'Semua Rating') {
            // Hapus karakter '+' jika ada
            $ratingVal = (float) rtrim($filters['rating'], '+');
            
            // Jika memilih 5 bintang, kita cari yang mendekati sempurna
            if ($ratingVal >= 5) {
                $query .= " AND h.average_rating >= 4.8";
            } else {
                $query .= " AND h.average_rating >= :rating";
                $bindings[':rating'] = $ratingVal;
            }
        }

        // 4. Filter Fasilitas
        // Asumsi kolom 'facilities' menyimpan JSON string seperti ["Wifi", "AC", "Kolam Renang"]
        if (!empty($filters['facility']) && is_array($filters['facility'])) {
            foreach ($filters['facility'] as $idx => $fac) {
                $key = ":facility_{$idx}";
                // Menggunakan LIKE %"Nama"% untuk mencari di dalam string JSON
                $query .= " AND h.facilities LIKE $key";
                $bindings[$key] = '%"' . $fac . '"%'; 
            }
        }

        // Grouping agar 1 hotel muncul 1 kali (karena join dengan rooms)
        $query .= " GROUP BY h.id";

        // 5. Filter Range Harga (HAVING)
        // Kita pakai HAVING karena 'min_price' adalah hasil agregasi (MIN)
        $havingClauses = [];
        
        if (!empty($filters['min_price'])) {
            $havingClauses[] = "min_price >= :min_price";
            $bindings[':min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $havingClauses[] = "min_price <= :max_price";
            $bindings[':max_price'] = $filters['max_price'];
        }

        if (!empty($havingClauses)) {
            $query .= " HAVING " . implode(' AND ', $havingClauses);
        }

        // 6. Sorting (Urutkan)
        $sort = $filters['sort'] ?? 'recommended';
        switch ($sort) {
            case 'lowest-price':
                // Urutkan dari harga termurah, hotel tanpa harga (null) ditaruh di akhir
                $query .= " ORDER BY CASE WHEN min_price IS NULL THEN 1 ELSE 0 END, min_price ASC";
                break;
            case 'highest-price':
                $query .= " ORDER BY min_price DESC";
                break;
            case 'highest-rating':
                $query .= " ORDER BY h.average_rating DESC";
                break;
            case 'recommended':
            default:
                // Rekomendasi: Kombinasi rating tinggi dan harga kompetitif
                $query .= " ORDER BY h.average_rating DESC, min_price ASC";
                break;
        }

        // Eksekusi Query
        try {
            $this->query($query);
            foreach ($bindings as $key => $value) {
                $this->bind($key, $value);
            }
            return $this->resultSet();
        } catch (PDOException $e) {
            // Log error jika diperlukan
            return [];
        }
    }

    // --- Method Lain Tetap Sama (Tidak Diubah) ---

    public function countAll(): int {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table}");
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) { return 0; }
    }

    public function getForAdmin(array $filters = []) {
        $query = "SELECT h.*, u.name as owner_name, u.email as owner_email,
                  (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id) as total_rooms
                  FROM {$this->table} h
                  JOIN users u ON h.owner_id = u.id
                  WHERE 1=1";
        $params = [];
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'pending') {
                $query .= " AND h.is_verified = 0";
            } elseif ($filters['status'] === 'verified') {
                $query .= " AND h.is_verified = 1";
            }
        }
        if (!empty($filters['search'])) {
            $query .= " AND (h.name LIKE :search OR h.city LIKE :search OR u.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        $query .= " ORDER BY h.created_at DESC";
        try {
            $this->query($query);
            foreach ($params as $key => $val) { $this->bind($key, $val); }
            return $this->resultSet();
        } catch (PDOException $e) { return []; }
    }

    public function verify(int $id): bool {
        try {
            $this->query("UPDATE {$this->table} SET is_verified = 1, is_active = 1 WHERE id = :id");
            $this->bind(':id', $id);
            return $this->execute();
        } catch (PDOException $e) { return false; }
    }

    public function deleteByAdmin(int $id): bool {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $id);
            return $this->execute();
        } catch (PDOException $e) { return false; }
    }

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
            foreach ($data as $key => $value) { $this->bind(":{$key}", $value); }
            $this->execute();
            return $this->lastInsertId();
        } catch (PDOException $e) { return false; }
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  name = :name, description = :description, address = :address, 
                  city = :city, contact_phone = :contact_phone, contact_email = :contact_email, 
                  facilities = :facilities, main_image = :main_image 
                  WHERE id = :id AND owner_id = :owner_id"; 
        try {
            $this->query($query);
            foreach ($data as $key => $value) { $this->bind(":{$key}", $value); }
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
    
    public function getPopularDestinations($limit = 6) {
        $this->query("SELECT city, COUNT(*) as hotel_count FROM {$this->table} WHERE is_active = 1 AND is_verified = 1 GROUP BY city ORDER BY hotel_count DESC LIMIT " . (int)$limit);
        $results = $this->resultSet();
        $destinations = ['Semua Kota'];
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