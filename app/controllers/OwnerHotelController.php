<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Models\Hotel;

class HotelController extends Controller {
    private $hotelModel;

    public function __construct() {
        // Cek Sesi Login & Role
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ' . BASE_URL . '/auth/login');
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
        // Generate CSRF Token jika belum ada
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $data = ['title' => 'Tambah Hotel', 'user' => $_SESSION];
        $this->view('owner/hotels/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        $this->validateCsrf();

        // Upload Gambar
        $imagePath = $this->uploadImage($_FILES['hotel_photo']);
        if (!$imagePath) {
            // Set flash message error (implementasi flash message opsional)
            header('Location: ' . BASE_URL . '/owner/hotels/create');
            exit;
        }

        $data = [
            'owner_id' => $_SESSION['user_id'],
            'name' => strip_tags($_POST['hotel_name']),
            'city' => strip_tags($_POST['city']),
            'province' => 'Indonesia', // Default atau tambah input di form
            'address' => strip_tags($_POST['address']),
            'description' => strip_tags($_POST['description']),
            'contact_phone' => strip_tags($_POST['phone']),
            'contact_email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'star_rating' => 3, // Default atau tambah input
            'is_active' => 1,
            'main_image' => $imagePath
        ];

        if ($this->hotelModel->create($data)) {
            header('Location: ' . BASE_URL . '/owner/hotels/index');
        } else {
            header('Location: ' . BASE_URL . '/owner/hotels/create');
        }
    }

    public function edit($id) {
        $hotel = $this->hotelModel->find($id);
        
        // Keamanan: Pastikan hotel milik owner yang sedang login
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/owner/hotels/index');
            exit;
        }

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

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        $this->validateCsrf();

        $id = $_POST['hotel_id'];
        $hotel = $this->hotelModel->find($id);

        // Keamanan
        if (!$hotel || $hotel['owner_id'] != $_SESSION['user_id']) {
            exit("Unauthorized action");
        }

        // Cek apakah ada upload foto baru
        $imagePath = $hotel['main_image']; // Default foto lama
        if (!empty($_FILES['hotel_photo']['name'])) {
            $newImage = $this->uploadImage($_FILES['hotel_photo']);
            if ($newImage) {
                $imagePath = $newImage;
                // Optional: Hapus file lama
            }
        }

        $data = [
            'name' => strip_tags($_POST['hotel_name']),
            'city' => strip_tags($_POST['city']),
            'address' => strip_tags($_POST['address']),
            'description' => strip_tags($_POST['description']),
            'contact_phone' => strip_tags($_POST['phone']),
            'contact_email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'facilities' => json_encode($_POST['facilities'] ?? []),
            'main_image' => $imagePath,
            'owner_id' => $_SESSION['user_id'] // Untuk validasi di model
        ];

        $this->hotelModel->update($id, $data);
        header('Location: ' . BASE_URL . '/owner/hotels/index');
    }

    public function delete($id) {
        // Biasanya request delete via POST/DELETE method, tapi untuk simplifikasi pakai GET dengan verifikasi
        // Di produksi sebaiknya pakai POST
        if ($this->hotelModel->delete($id, $_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/owner/hotels/index');
        } else {
            exit("Gagal menghapus hotel.");
        }
    }

    // --- Helpers ---

    private function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Validation Failed");
        }
    }

    private function uploadImage($file) {
        $targetDir = "../public/uploads/hotels/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . '_' . basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                return '/uploads/hotels/' . $fileName; // Path relatif untuk DB
            }
        }
        return false;
    }
}