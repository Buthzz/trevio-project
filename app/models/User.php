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
     * Mencari user berdasarkan ID
     * * @param int $id
     * @return array|false
     */
    public function find($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Mencari user berdasarkan Email
     * Digunakan saat Login & Register check
     * * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        $this->db->query("SELECT * FROM {$this->table} WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Membuat user baru (Register / Google Login)
     * Menangani field nullable secara otomatis
     * * @param array $data Array asosiatif field database
     * @return int|false ID user yang baru dibuat atau false jika gagal
     */
    public function create($data) {
        // Mapping data agar sesuai dengan kolom database
        $fields = [
            'name', 
            'email', 
            'password', 
            'phone', 
            'whatsapp_number',
            'auth_provider', 
            'google_id', 
            'role', 
            'is_verified', 
            'is_active',
            'profile_image'
        ];

        $params = [];
        $values = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $params[] = $field;
                $values[] = ":{$field}";
            }
        }

        $columns = implode(", ", $params);
        $placeholders = implode(", ", $values);

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
            $this->db->query($query);

            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $this->db->bind(":{$field}", $data[$field]);
                }
            }

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            // Log error jika diperlukan, jangan tampilkan ke user di production
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengupdate data user
     * Digunakan untuk update profil atau menyimpan Google ID pada akun lama
     * * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $setPart = [];
        foreach ($data as $key => $value) {
            $setPart[] = "{$key} = :{$key}";
        }
        
        $setString = implode(", ", $setPart);
        $query = "UPDATE {$this->table} SET {$setString} WHERE id = :id";

        try {
            $this->db->query($query);
            
            // Bind data yang akan diupdate
            foreach ($data as $key => $value) {
                $this->db->bind(":{$key}", $value);
            }
            
            // Bind ID
            $this->db->bind(':id', $id);

            return $this->db->execute();

        } catch (PDOException $e) {
            error_log("User Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung total user untuk Admin Dashboard
     * * @return int
     */
    public function countAll() {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Menghitung user berdasarkan role (opsional untuk statistik detail)
     * * @param string $role
     * @return int
     */
    public function countByRole($role) {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role");
        $this->db->bind(':role', $role);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }
}