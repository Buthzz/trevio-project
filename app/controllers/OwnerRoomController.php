<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Room;
use App\Models\Hotel;

class OwnerRoomController extends Controller {
    private $roomModel;
    private $hotelModel;

    public function __construct() {
        // Cek session login & role
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/auth/login');
            exit;
        }
        $this->roomModel = new Room();
        $this->hotelModel = new Hotel();
    }

    public function index() {
        $hotelId = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : null;
        $allRooms = $this->roomModel->getByOwner($_SESSION['user_id']);
        
        $rooms = $allRooms;
        if ($hotelId) {
            $rooms = array_filter($allRooms, function($room) use ($hotelId) {
                return $room['hotel_id'] == $hotelId;
            });
        }

        $data = [
            'title' => 'Manajemen Kamar',
            'rooms' => $rooms,
            'hotels' => $this->hotelModel->getByOwner($_SESSION['user_id']),
            'selected_hotel' => $hotelId,
            'user' => $_SESSION
        ];
        
        $this->view('owner/rooms/index', $data);
    }

    public function create() {
        $hotels = $this->hotelModel->getByOwner($_SESSION['user_id']);
        
        if (empty($hotels)) {
            $_SESSION['flash_error'] = "Anda harus membuat hotel terlebih dahulu.";
            header('Location: ' . BASE_URL . '/owner/hotels/create');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $selectedHotelId = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : null;

        $data = [
            'title' => 'Tambah Kamar',
            'hotels' => $hotels,
            'selected_hotel' => $selectedHotelId,
            'user' => $_SESSION
        ];
        $this->view('owner/rooms/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        $this->validateCsrf();

        // Validasi Input Wajib
        if (empty($_POST['hotel_id']) || empty($_POST['room_name']) || empty($_POST['price']) || empty($_POST['capacity'])) {
            $_SESSION['flash_error'] = "Nama Kamar, Harga, dan Kapasitas wajib diisi.";
            header('Location: ' . BASE_URL . '/owner/rooms/create');
            exit;
        }

        // Validasi kepemilikan hotel
        $hotel = $this->hotelModel->find($_POST['hotel_id']);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized: Hotel ini bukan milik Anda.");
        }

        // Upload Image
        $imagePath = null;
        if (!empty($_FILES['room_photo']['name'])) {
            $imagePath = $this->uploadImage($_FILES['room_photo']);
        }
        $imagePath = $imagePath ?: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=300&fit=crop';

        // Format Data
        // PERBAIKAN: Menggunakan 'room_name' sebagai 'room_type' di DB agar nama spesifik (misal "Deluxe 101") tersimpan
        // 'room_type' dari dropdown (Single/Double) bisa digabung ke deskripsi atau diabaikan jika DB cuma punya 1 kolom
        $roomName = strip_tags($_POST['room_name']);
        $category = strip_tags($_POST['room_type'] ?? '');
        
        // Opsional: Gabungkan kategori ke deskripsi jika perlu
        $description = strip_tags($_POST['description'] ?? '');
        if ($category) {
            $description = "Tipe: $category. " . $description;
        }

        $data = [
            'hotel_id' => (int)$_POST['hotel_id'],
            'room_type' => $roomName, // Simpan nama kamar di kolom room_type
            'price_per_night' => max(0, (float)$_POST['price']),
            'capacity' => max(1, (int)$_POST['capacity']),
            'total_slots' => max(1, (int)($_POST['total_rooms'] ?? 1)),
            'description' => $description,
            'amenities' => json_encode($_POST['facilities'] ?? []), // Map form 'facilities' ke 'amenities'
            'main_image' => $imagePath
        ];

        if ($this->roomModel->create($data)) {
            $_SESSION['flash_success'] = "Kamar berhasil ditambahkan!";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
        } else {
            $_SESSION['flash_error'] = "Gagal menyimpan data kamar. Silakan coba lagi.";
            header('Location: ' . BASE_URL . '/owner/rooms/create');
        }
        exit;
    }

    public function edit($id) {
        $room = $this->roomModel->find($id);
        
        if (!$room) {
            $_SESSION['flash_error'] = "Kamar tidak ditemukan.";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
            exit;
        }

        $hotel = $this->hotelModel->find($room['hotel_id']);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Akses ditolak.";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Mapping amenities (DB) ke facilities (View)
        if (isset($room['amenities']) && is_string($room['amenities'])) {
            $room['facilities'] = json_decode($room['amenities'], true) ?? [];
        } else {
             $room['facilities'] = [];
        }

        // Agar view edit.php bisa menampilkan nama kamar dengan benar
        $room['room_name'] = $room['room_type']; 

        $data = [
            'title' => 'Edit Kamar',
            'room' => $room,
            'hotels' => $this->hotelModel->getByOwner($_SESSION['user_id']),
            'user' => $_SESSION
        ];
        $this->view('owner/rooms/edit', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        $this->validateCsrf();

        $id = (int)$_POST['room_id'];
        $room = $this->roomModel->find($id);
        
        if (!$room) {
            $_SESSION['flash_error'] = "Kamar tidak ditemukan.";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
            exit;
        }

        // Verifikasi kepemilikan
        $hotel = $this->hotelModel->find($room['hotel_id']);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized");
        }

        $imagePath = $room['main_image'];
        if (!empty($_FILES['room_photo']['name'])) {
            $newImage = $this->uploadImage($_FILES['room_photo']);
            if ($newImage) $imagePath = $newImage;
        }

        // Logic Nama Kamar vs Tipe
        $roomName = strip_tags($_POST['room_name']);
        $category = strip_tags($_POST['room_type'] ?? '');
        
        $description = strip_tags($_POST['description'] ?? '');
        
        $data = [
            'room_type' => $roomName, // Update nama kamar
            'price_per_night' => max(0, (float)$_POST['price']),
            'capacity' => max(1, (int)$_POST['capacity']),
            'total_slots' => max(1, (int)$_POST['total_rooms']),
            'description' => $description,
            'amenities' => json_encode($_POST['facilities'] ?? []), // Facilities -> Amenities
            'main_image' => $imagePath
        ];

        if ($this->roomModel->update($id, $data)) {
            $_SESSION['flash_success'] = "Kamar berhasil diperbarui!";
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui kamar.";
        }

        header('Location: ' . BASE_URL . '/owner/rooms/index');
        exit;
    }

    public function delete($id) {
        $room = $this->roomModel->find($id);
        if ($room) {
            $hotel = $this->hotelModel->find($room['hotel_id']);
            if ($hotel && $hotel['owner_id'] == $_SESSION['user_id']) {
                $this->roomModel->delete($id);
                $_SESSION['flash_success'] = "Kamar berhasil dihapus.";
            } else {
                $_SESSION['flash_error'] = "Gagal menghapus: Akses ditolak.";
            }
        }
        header('Location: ' . BASE_URL . '/owner/rooms/index');
    }

    // --- Helpers ---

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function uploadImage($file) {
        $targetDir = "../public/uploads/rooms/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $originalName = basename($file["name"]);
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $fileName = time() . '_' . $originalName;
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = array('jpg', 'png', 'jpeg', 'webp');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                return '/uploads/rooms/' . $fileName;
            }
        }
        return false;
    }
}