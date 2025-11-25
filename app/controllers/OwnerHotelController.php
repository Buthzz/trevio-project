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
    
    // --- Helpers ---

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function uploadImage($file) {
        $targetDir = "../public/uploads/hotels/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file["name"]));
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'png', 'jpeg', 'webp'])) {
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                return '/uploads/hotels/' . $fileName; 
            }
        }
        return false;
    }
}