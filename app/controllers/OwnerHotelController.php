<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Hotel;

class OwnerHotelController extends Controller {
    private $hotelModel;

    public function __construct() {
        // Cek Sesi Login & Role
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/auth/login');
            exit;
        }
        $this->hotelModel = new Hotel();
    }

    public function index() {
        $data = [
            'title' => 'Kelola Hotel',
            'hotels' => $this->hotelModel->getByOwner($_SESSION['user_id']),
            'user' => $_SESSION
        ];
        $this->view('owner/hotels/index', $data);
    }

    public function create() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $data = ['title' => 'Tambah Hotel', 'user' => $_SESSION];
        $this->view('owner/hotels/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        $this->validateCsrf();

        // Validasi Input
        if (empty($_POST['hotel_name']) || empty($_POST['city']) || empty($_POST['address'])) {
            $_SESSION['flash_error'] = "Nama, Kota, dan Alamat wajib diisi.";
            header('Location: ' . BASE_URL . '/owner/hotels/create');
            exit;
        }

        // Upload Gambar
        $imagePath = null;
        if (!empty($_FILES['hotel_photo']['name'])) {
            $imagePath = $this->uploadImage($_FILES['hotel_photo']);
        }
        
        // Fallback jika upload gagal/kosong
        if (!$imagePath) {
            $imagePath = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'; 
        }

        $data = [
            'owner_id' => $_SESSION['user_id'],
            'name' => strip_tags($_POST['hotel_name']),
            'city' => strip_tags($_POST['city']),
            'province' => strip_tags($_POST['province'] ?? 'Indonesia'),
            'address' => strip_tags($_POST['address']),
            'description' => strip_tags($_POST['description']),
            'contact_phone' => strip_tags($_POST['phone']),
            'contact_email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'star_rating' => 4, // Default sementara
            'is_active' => 1,
            'main_image' => $imagePath
        ];

        if ($this->hotelModel->create($data)) {
            $_SESSION['flash_success'] = "Hotel berhasil ditambahkan!";
            header('Location: ' . BASE_URL . '/owner/hotels/index');
        } else {
            $_SESSION['flash_error'] = "Gagal menyimpan data hotel.";
            header('Location: ' . BASE_URL . '/owner/hotels/create');
        }
    }

    /**
     * Menampilkan halaman edit hotel
     * URL: /owner/hotels/edit/{id}
     */
    public function edit($id) {
        // 1. Ambil data hotel berdasarkan ID
        $hotel = $this->hotelModel->find($id);

        // 2. Validasi: Pastikan hotel ada dan milik owner yang sedang login
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Hotel tidak ditemukan atau Anda tidak memiliki akses.";
            header('Location: ' . BASE_URL . '/owner/hotels/index');
            exit;
        }

        // 3. Decode fasilitas jika formatnya JSON string
        if (isset($hotel['facilities']) && is_string($hotel['facilities'])) {
            $hotel['facilities'] = json_decode($hotel['facilities'], true) ?? [];
        }

        // 4. Generate CSRF token baru
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $data = [
            'title' => 'Edit Hotel',
            'hotel' => $hotel,
            'user' => $_SESSION
        ];
        
        $this->view('owner/hotels/edit', $data);
    }

    /**
     * Memproses update data hotel
     * Method: POST
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/owner/hotels/index');
            exit;
        }

        $this->validateCsrf();

        $id = filter_input(INPUT_POST, 'hotel_id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['flash_error'] = "ID Hotel tidak valid.";
            header('Location: ' . BASE_URL . '/owner/hotels/index');
            exit;
        }

        // 1. Validasi Kepemilikan sebelum update
        $hotel = $this->hotelModel->find($id);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized Action");
        }

        // 2. Handle Upload Gambar (Jika ada gambar baru)
        $imagePath = $hotel['main_image']; // Default pakai gambar lama
        if (!empty($_FILES['hotel_photo']['name'])) {
            $uploaded = $this->uploadImage($_FILES['hotel_photo']);
            if ($uploaded) {
                $imagePath = $uploaded;
                // Opsional: Hapus gambar lama jika bukan placeholder default
            }
        }

        // 3. Persiapkan Data Update
        $data = [
            'name' => strip_tags($_POST['hotel_name']),
            'city' => strip_tags($_POST['city']),
            // Province bisa diambil dari input hidden atau hardcode sementara
            'province' => 'Indonesia', 
            'address' => strip_tags($_POST['address']),
            'description' => strip_tags($_POST['description']),
            'contact_phone' => strip_tags($_POST['phone']),
            'contact_email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'main_image' => $imagePath,
            // Mapping status dropdown ke boolean is_active (atau kolom status jika ada)
            // Asumsi di DB ada kolom is_active (boolean) dan mungkin status (enum)
            // Kita update is_active based on dropdown
            'is_active' => ($_POST['status'] === 'active') ? 1 : 0
        ];

        // 4. Eksekusi Update di Model
        // Pastikan Model Hotel punya method update yang menerima parameter ini
        if ($this->hotelModel->update($id, $data)) {
            $_SESSION['flash_success'] = "Informasi hotel berhasil diperbarui.";
            header('Location: ' . BASE_URL . '/owner/hotels/index');
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui hotel.";
            header('Location: ' . BASE_URL . '/owner/hotels/edit/' . $id);
        }
        exit;
    }

    /**
     * Menghapus hotel
     */
    public function delete($id) {
        $hotel = $this->hotelModel->find($id);
        
        if ($hotel && $hotel['owner_id'] == $_SESSION['user_id']) {
            if ($this->hotelModel->delete($id, $_SESSION['user_id'])) {
                $_SESSION['flash_success'] = "Hotel berhasil dihapus.";
            } else {
                $_SESSION['flash_error'] = "Gagal menghapus hotel.";
            }
        } else {
            $_SESSION['flash_error'] = "Akses ditolak atau hotel tidak ditemukan.";
        }
        
        header('Location: ' . BASE_URL . '/owner/hotels/index');
        exit;
    }
    
    // --- Helpers ---

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function uploadImage($file) {
        $targetDir = "../public/uploads/hotels/";
        // Pastikan folder ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Sanitasi nama file
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file["name"]));
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Cek ekstensi
        if (in_array($fileType, ['jpg', 'png', 'jpeg', 'webp'])) {
            // Pindahkan file
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                // Kembalikan path relatif untuk disimpan di DB
                return '/uploads/hotels/' . $fileName; 
            }
        }
        return false;
    }
}