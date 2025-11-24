<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Room;
use App\Models\Hotel;

class RoomController extends Controller {
    private $roomModel;
    private $hotelModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/auth/login');
            exit;
        }
        $this->roomModel = new Room();
        $this->hotelModel = new Hotel();
    }

    public function index() {
        $data = [
            'title' => 'Manajemen Kamar',
            'rooms' => $this->roomModel->getByOwner($_SESSION['user_id']),
            'hotels' => $this->hotelModel->getByOwner($_SESSION['user_id']), // Untuk filter hotel
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

        $data = [
            'title' => 'Tambah Kamar',
            'hotels' => $hotels,
            'user' => $_SESSION
        ];
        $this->view('owner/rooms/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        $this->validateCsrf();

        // Validate required fields
        if (empty($_POST['hotel_id']) || empty($_POST['room_type']) || empty($_POST['price']) || empty($_POST['capacity'])) {
            header('Location: ' . BASE_URL . '/owner/rooms/create');
            exit;
        }

        // Validasi kepemilikan hotel
        $hotel = $this->hotelModel->find($_POST['hotel_id']);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized: Hotel ini bukan milik Anda.");
        }

        $imagePath = $this->uploadImage($_FILES['room_photo']);
        $imagePath = $imagePath ?: '/images/placeholder.jpg';

        // Validate numeric inputs
        $price = max(0, (float)$_POST['price']);
        $capacity = max(1, (int)$_POST['capacity']);
        $totalRooms = max(1, (int)($_POST['total_rooms'] ?? 1));

        $data = [
            'hotel_id' => (int)$_POST['hotel_id'],
            'room_name' => strip_tags($_POST['room_name'] ?? ''), // Opsional jika skema DB ada
            'room_type' => strip_tags($_POST['room_type']),
            'price_per_night' => $price,
            'capacity' => $capacity,
            'total_slots' => $totalRooms, // Slot logic: set total
            'description' => strip_tags($_POST['description'] ?? ''),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'main_image' => $imagePath
        ];

        if ($this->roomModel->create($data)) {
            header('Location: ' . BASE_URL . '/owner/rooms/index');
        } else {
            // Handle error
            header('Location: ' . BASE_URL . '/owner/rooms/create');
        }
    }

    public function edit($id) {
        $room = $this->roomModel->find($id);
        // Validasi room milik hotel owner
        $hotel = $this->hotelModel->find($room['hotel_id']);
        
        if (!$room || !$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/owner/rooms/index');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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

        // Verifikasi kepemilikan via hotel relation
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
            'hotel_id' => (int)$_POST['hotel_id'], // Jika ingin memindahkan kamar ke hotel lain
            'room_type' => strip_tags($_POST['room_type']),
            'price_per_night' => $price,
            'capacity' => $capacity,
            'total_slots' => $totalRooms,
            // Logic available_slots update ada di Model atau biarkan manual via UI lain
            // Untuk simplifikasi, jika total berubah, available bisa disesuaikan proporsional nanti
            'description' => strip_tags($_POST['description'] ?? ''),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'main_image' => $imagePath
        ];

        $this->roomModel->update($id, $data);
        header('Location: ' . BASE_URL . '/owner/rooms/index');
    }

    public function delete($id) {
        // Cek kepemilikan sebelum hapus
        $room = $this->roomModel->find($id);
        $hotel = $this->hotelModel->find($room['hotel_id']);
        
        if ($hotel['owner_id'] == $_SESSION['user_id']) {
            $this->roomModel->delete($id);
        }
        header('Location: ' . BASE_URL . '/owner/rooms/index');
    }

    // --- Helpers ---

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
        // Regenerate token after validation to prevent replay attacks
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function uploadImage($file) {
        $targetDir = "../public/uploads/rooms/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        // Sanitize filename to prevent path traversal
        $originalName = basename($file["name"]);
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        $fileName = time() . '_' . $originalName;
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = array('jpg', 'png', 'jpeg', 'webp');
        if (in_array($fileType, $allowTypes)) {
            // Validate file is actually an image
            $check = getimagesize($file["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                    return '/uploads/rooms/' . $fileName;
                }
            }
        }
        return false;
    }
}