<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class User extends Model {
    
    /**
     * Nama tabel database
     */
    protected $table = 'users';

    /**
     * Daftar kolom yang diizinkan untuk Mass Assignment dan Update.
     * Berfungsi sebagai whitelist untuk mencegah SQL Injection pada nama kolom.
     */
    protected $allowedFields = [
        'name', 'email', 'password', 'phone', 'whatsapp_number',
        'auth_provider', 'google_id', 'role', 'is_verified', 
        'is_active', 'profile_image'
    ];

    /**
     * Mencari user berdasarkan ID.
     * * @param int $id User ID
     * @return array|false User data atau false jika tidak ditemukan/error
     */
    public function find(int $id) {
        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("User Find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari user berdasarkan Email.
     * Digunakan saat Login & Register check.
     * * @param string $email Email address
     * @return array|false User data atau false jika tidak ditemukan/error
     */
    public function findByEmail(string $email) {
        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE email = :email");
            $this->db->bind(':email', $email);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("User FindByEmail Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Membuat user baru dengan keamanan Password Hashing.
     * Hanya memproses kolom yang terdaftar di $allowedFields.
     * * @param array $data Data user (key => value)
     * @return int|false ID user baru atau false jika gagal
     */
    public function create(array $data) {
        // 1. Filter data hanya untuk kolom yang diizinkan (Whitelist)
        $data = array_intersect_key($data, array_flip($this->allowedFields));

        // 2. Security: Hash Password jika ada input password
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $params = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $params[] = $field;
            $values[] = ":{$field}";
        }

        // Cek jika tidak ada data valid yang tersisa setelah filter
        if (empty($params)) {
            error_log("User Create Error: No valid fields provided");
            return false;
        }

        $columns = implode(", ", $params);
        $placeholders = implode(", ", $values);

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
            $this->db->query($query);

            foreach ($data as $field => $value) {
                $this->db->bind(":{$field}", $value);
            }

            if ($this->db->execute()) {
                return (int) $this->db->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengupdate data user dengan aman.
     * Mencegah SQL Injection dengan whitelist kolom.
     * * @param int $id User ID
     * @param array $data Data update (key => value)
     * @return bool Status keberhasilan
     */
    public function update(int $id, array $data): bool {
        // 1. Filter data agar hanya kolom yang diizinkan yang diproses
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));

        if (empty($filteredData)) {
            error_log("User Update Error: No valid data provided or fields not allowed");
            return false;
        }

        $setPart = [];
        foreach ($filteredData as $key => $value) {
            // Nama kolom aman karena diambil dari hasil filter $allowedFields
            $setPart[] = "{$key} = :{$key}";
        }
        
        $setString = implode(", ", $setPart);
        $query = "UPDATE {$this->table} SET {$setString} WHERE id = :id";

        try {
            $this->db->query($query);
            
            foreach ($filteredData as $key => $value) {
                // Hash password otomatis jika field password diupdate
                if ($key === 'password') {
                    $value = password_hash($value, PASSWORD_BCRYPT);
                }
                $this->db->bind(":{$key}", $value);
            }
            
            $this->db->bind(':id', $id);

            return $this->db->execute();

        } catch (PDOException $e) {
            error_log("User Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung total user untuk Admin Dashboard.
     * * @return int Total user
     */
    public function countAll(): int {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("User CountAll Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menghitung user berdasarkan role.
     * * @param string $role Role (customer, owner, admin)
     * @return int Jumlah user
     */
    public function countByRole(string $role): int {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role");
            $this->db->bind(':role', $role);
            $result = $this->db->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("User CountByRole Error: " . $e->getMessage());
            return 0;
        }
    }
}