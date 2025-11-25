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
     * Daftar kolom yang diizinkan untuk Mass Assignment.
     * Whitelist ini mencegah SQL Injection pada nama kolom.
     */
    protected $allowedFields = [
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

    /**
     * Mencari user berdasarkan ID.
     * @param int $id User ID
     * @return array|false User data atau false jika tidak ditemukan/error
     */
    public function find(int $id) {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $id);
            return $this->single();
        } catch (PDOException $e) {
            error_log("User Find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari user berdasarkan Email.
     * Digunakan saat Login & Register check.
     * @param string $email Email address
     * @return array|false User data atau false jika tidak ditemukan/error
     */
    public function findByEmail(string $email) {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE email = :email");
            $this->bind(':email', $email);
            return $this->single();
        } catch (PDOException $e) {
            error_log("User FindByEmail Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Membuat user baru.
     * Password akan di-hash otomatis di sini, tapi mencegah double-hash.
     * @param array $data Data user (key => value)
     * @return int|false ID user baru atau false jika gagal
     */
    public function create(array $data) {
        // 1. Filter data hanya untuk kolom yang diizinkan (Whitelist)
        $data = array_intersect_key($data, array_flip($this->allowedFields));

        // 2. Security: Jika field password ada tetapi kosong/spasi, hapus supaya tidak tersimpan kosong
        if (isset($data['password']) && empty(trim((string)$data['password']))) {
            unset($data['password']);
        }

        // 3. Hash Password jika diperlukan (CEK agar tidak double-hash)
        if (isset($data['password'])) {
            $pw = $data['password'];
            // Cek apakah input sudah merupakan hash yang dibuat password_hash
            $info = password_get_info($pw);
            if ($info['algo'] === 0) {
                // bukan hash -> hash dulu
                $data['password'] = password_hash($pw, PASSWORD_BCRYPT);
            } else {
                // sudah hash -> jika perlu rehash sesuai policy, lakukan rehash
                if (password_needs_rehash($pw, PASSWORD_BCRYPT)) {
                    $data['password'] = password_hash($pw, PASSWORD_BCRYPT);
                } // else biarkan hash apa adanya (tidak double-hash)
            }
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
            $this->query($query);

            foreach ($data as $field => $value) {
                $this->bind(":{$field}", $value);
            }

            if ($this->execute()) {
                return (int) $this->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengupdate data user dengan aman.
     * FIX: Mencegah password kosong menimpa password lama.
     * Menghindari double-hashing.
     * @param int $id User ID
     * @param array $data Data update (key => value)
     * @return bool Status keberhasilan
     */
    public function update(int $id, array $data): bool {
        // 1. Jika field password dikirim tapi kosong/spasi saja, 
        // hapus dari array agar password lama di database TIDAK tertimpa hash kosong.
        if (isset($data['password']) && empty(trim((string)$data['password']))) {
            unset($data['password']); 
        }

        // 2. Filter data agar hanya kolom yang diizinkan yang diproses
        $filteredData = array_intersect_key($data, array_flip($this->allowedFields));

        if (empty($filteredData)) {
            // Tidak ada data valid untuk diupdate
            return false; 
        }

        // 3. Hash Password baru (jika user benar-benar menginput password baru)
        if (isset($filteredData['password'])) {
            $pw = $filteredData['password'];
            $info = password_get_info($pw);
            if ($info['algo'] === 0) {
                // plain text -> hash
                $filteredData['password'] = password_hash($pw, PASSWORD_BCRYPT);
            } else {
                // sudah hash -> cek apakah perlu rehash sesuai policy
                if (password_needs_rehash($pw, PASSWORD_BCRYPT)) {
                    $filteredData['password'] = password_hash($pw, PASSWORD_BCRYPT);
                } // else tidak diubah
            }
        }

        // 4. Bangun Query Update Dinamis
        $setPart = [];
        foreach ($filteredData as $key => $value) {
            $setPart[] = "{$key} = :{$key}";
        }
        
        $setString = implode(", ", $setPart);
        $query = "UPDATE {$this->table} SET {$setString} WHERE id = :id";

        try {
            $this->query($query);
            
            // Bind semua value data
            foreach ($filteredData as $key => $value) {
                $this->bind(":{$key}", $value);
            }
            
            // Bind ID untuk WHERE clause
            $this->bind(':id', $id);

            return $this->execute();

        } catch (PDOException $e) {
            error_log("User Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung total user untuk Admin Dashboard.
     * @return int Total user
     */
    public function countAll(): int {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table}");
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("User CountAll Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menghitung user berdasarkan role.
     * @param string $role Role (customer, owner, admin)
     * @return int Jumlah user
     */
    public function countByRole(string $role): int {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role");
            $this->bind(':role', $role);
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("User CountByRole Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all users with optional filters
     * @param string|null $role
     * @param string|null $status
     * @return array
     */
    public function getAll($role = null, $status = null) {
        $query = "SELECT id, name, email, phone, role, is_active, is_verified, auth_provider, created_at 
                  FROM {$this->table} WHERE 1=1";
        
        if ($role) {
            $query .= " AND role = :role";
        }
        
        if ($status !== null) {
            $query .= " AND is_active = :status";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        try {
            $this->query($query);
            
            if ($role) {
                $this->bind(':role', $role);
            }
            
            if ($status !== null) {
                $this->bind(':status', $status);
            }
            
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("User getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count users by status
     * @param int $status
     * @return int
     */
    public function countByStatus($status) {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = :status");
            $this->bind(':status', $status);
            $result = $this->single();
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("User countByStatus Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update user status (activate/deactivate)
     * @param int $userId
     * @param int $status
     * @return bool
     */
    public function updateStatus($userId, $status) {
        try {
            $this->query("UPDATE {$this->table} SET is_active = :status WHERE id = :id");
            $this->bind(':status', $status);
            $this->bind(':id', $userId);
            return $this->execute();
        } catch (PDOException $e) {
            error_log("User updateStatus Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user role
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public function updateRole($userId, $role) {
        try {
            $this->query("UPDATE {$this->table} SET role = :role WHERE id = :id");
            $this->bind(':role', $role);
            $this->bind(':id', $userId);
            return $this->execute();
        } catch (PDOException $e) {
            error_log("User updateRole Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user permanently
     * @param int $userId
     * @return bool
     */
    public function delete($userId) {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id");
            $this->bind(':id', $userId);
            return $this->execute();
        } catch (PDOException $e) {
            error_log("User delete Error: " . $e->getMessage());
            return false;
        }
    }
}
