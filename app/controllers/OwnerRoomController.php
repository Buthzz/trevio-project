<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Models\Room;
use App\Models\Hotel;

class RoomController extends Controller {
    private $roomModel;
    private $hotelModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ' . BASE_URL . '/auth/login');
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

        // Validasi kepemilikan hotel
        $hotel = $this->hotelModel->find($_POST['hotel_id']);
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized: Hotel ini bukan milik Anda.");
        }

        $imagePath = $this->uploadImage($_FILES['room_photo']);
        $imagePath = $imagePath ?: '/images/placeholder.jpg';

        $data = [
            'hotel_id' => $_POST['hotel_id'],
            'room_name' => strip_tags($_POST['room_name']), // Opsional jika skema DB ada
            'room_type' => strip_tags($_POST['room_type']),
            'price_per_night' => (int) $_POST['price'],
            'capacity' => (int) $_POST['capacity'],
            'total_slots' => (int) $_POST['total_rooms'], // Slot logic: set total
            'description' => strip_tags($_POST['description']),
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

        $id = $_POST['room_id'];
        $room = $this->roomModel->find($id);
        
        // Verifikasi kepemilikan via hotel relation
        $hotel = $this->hotelModel->find($room['hotel_id']);
        if ($hotel['owner_id'] != $_SESSION['user_id']) {
            die("Unauthorized");
        }

        $imagePath = $room['main_image'];
        if (!empty($_FILES['room_photo']['name'])) {
            $newImage = $this->uploadImage($_FILES['room_photo']);
            if ($newImage) $imagePath = $newImage;
        }

        $data = [
            'hotel_id' => $_POST['hotel_id'], // Jika ingin memindahkan kamar ke hotel lain
            'room_type' => strip_tags($_POST['room_type']),
            'price_per_night' => (int) $_POST['price'],
            'capacity' => (int) $_POST['capacity'],
            'total_slots' => (int) $_POST['total_rooms'],
            // Logic available_slots update ada di Model atau biarkan manual via UI lain
            // Untuk simplifikasi, jika total berubah, available bisa disesuaikan proporsional nanti
            'description' => strip_tags($_POST['description']),
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
    }

    private function uploadImage($file) {
        $targetDir = "../public/uploads/rooms/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . '_' . basename($file["name"]);
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