<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Room;
use App\Models\Hotel;

// PERBAIKAN: Nama class harus sama dengan nama file (OwnerRoomController)
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
        // Ambil filter hotel_id dari URL jika ada
        $hotelId = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : null;
        
        // Jika hotel_id ada, ambil rooms khusus hotel itu. Jika tidak, ambil semua milik owner.
        // Note: Anda mungkin perlu menyesuaikan method getByOwner di Model Room jika ingin support filter hotel spesifik
        // Atau filter array hasilnya di sini.
        $allRooms = $this->roomModel->getByOwner($_SESSION['user_id']);
        
        // Filter manual jika model belum support filter by hotel_id
        $rooms = $allRooms;
        if ($hotelId) {
            $rooms = array_filter($allRooms, function($room) use ($hotelId) {
                return $room['hotel_id'] == $hotelId;
            });
        }

        $data = [
            'title' => 'Manajemen Kamar',
            'rooms' => $rooms,
            'hotels' => $this->hotelModel->getByOwner($_SESSION['user_id']), // Untuk dropdown filter
            'selected_hotel' => $hotelId,
            'user' => $_SESSION
        ];
        
        $this->view('owner/rooms/index', $data);
    }

    public function create() {
        // Kirim data hotel agar bisa dipilih di dropdown
        $hotels = $this->hotelModel->getByOwner($_SESSION['user_id']);
        
        if (empty($hotels)) {
            // Jika belum punya hotel, arahkan buat hotel dulu
            header('Location: ' . BASE_URL . '/owner/hotels/create');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Pre-select hotel jika ada parameter hotel_id di URL
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

        // Validate required fields
        if (empty($_POST['hotel_id']) || empty($_POST['room_type']) || empty($_POST['price']) || empty($_POST['capacity'])) {
            $_SESSION['flash_error'] = "Semua kolom bertanda * wajib diisi.";
            header('Location: ' . BASE_URL . '/owner/rooms/create');
            exit;
        }

        // Validasi kepemilikan hotel
        $hotel = $this->hotelModel->find($_POST['hotel_id']);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized: Hotel ini bukan milik Anda.");
        }

        // Upload image logic
        $imagePath = null;
        if (!empty($_FILES['room_photo']['name'])) {
            $imagePath = $this->uploadImage($_FILES['room_photo']);
        }
        // Fallback placeholder jika gagal/kosong
        $imagePath = $imagePath ?: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=300&fit=crop';

        // Validate numeric inputs
        $price = max(0, (float)$_POST['price']);
        $capacity = max(1, (int)$_POST['capacity']);
        $totalRooms = max(1, (int)($_POST['total_rooms'] ?? 1));

        $data = [
            'hotel_id' => (int)$_POST['hotel_id'],
            'room_name' => strip_tags($_POST['room_name'] ?? ''), // Opsional
            'room_type' => strip_tags($_POST['room_type']),
            'price_per_night' => $price,
            'capacity' => $capacity,
            'total_slots' => $totalRooms, 
            'available_slots' => $totalRooms, // Default available = total saat create
            'description' => strip_tags($_POST['description'] ?? ''),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'main_image' => $imagePath,
            'is_available' => 1
        ];

        if ($this->roomModel->create($data)) {
            $_SESSION['flash_success'] = "Kamar berhasil ditambahkan!";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
        } else {
            $_SESSION['flash_error'] = "Gagal menyimpan kamar.";
            header('Location: ' . BASE_URL . '/owner/rooms/create');
        }
    }

    public function edit($id) {
        $room = $this->roomModel->find($id);
        
        if (!$room) {
            $_SESSION['flash_error'] = "Kamar tidak ditemukan.";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
            exit;
        }

        // Validasi room milik hotel owner
        $hotel = $this->hotelModel->find($room['hotel_id']);
        
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Akses ditolak.";
            header('Location: ' . BASE_URL . '/owner/rooms/index');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Decode facilities jika masih string JSON
        if (is_string($room['facilities'])) {
            $room['facilities'] = json_decode($room['facilities'], true) ?? [];
        }

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
            die("Room not found");
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

        // Validate numeric inputs
        $price = max(0, (float)$_POST['price']);
        $capacity = max(1, (int)$_POST['capacity']);
        $totalRooms = max(1, (int)$_POST['total_rooms']);

        $data = [
            'hotel_id' => (int)$_POST['hotel_id'],
            'room_name' => strip_tags($_POST['room_name'] ?? ''),
            'room_type' => strip_tags($_POST['room_type']),
            'price_per_night' => $price,
            'capacity' => $capacity,
            'total_slots' => $totalRooms,
            'description' => strip_tags($_POST['description'] ?? ''),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'main_image' => $imagePath
        ];

        if ($this->roomModel->update($id, $data)) {
            $_SESSION['flash_success'] = "Kamar berhasil diperbarui!";
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui kamar.";
        }

        header('Location: ' . BASE_URL . '/owner/rooms/index');
    }

    public function delete($id) {
        // Cek kepemilikan sebelum hapus
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
            $check = getimagesize($file["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                    // Return path relative to public
                    return '/uploads/rooms/' . $fileName;
                }
            }
        }
        return false;
    }
}