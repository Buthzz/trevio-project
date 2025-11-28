<?php

namespace App\Controllers;

use App\Models\Hotel;

class AdminHotelController extends BaseAdminController {
    private $hotelModel;

    public function __construct() {
        parent::__construct(); // Cek login admin & session
        $this->hotelModel = new Hotel();
    }

    /**
     * Menampilkan daftar hotel dengan filter
     */
    public function index() {
        $filters = [
            'search' => filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS),
            'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) // pending/verified
        ];

        $hotels = $this->hotelModel->getForAdmin($filters);

        $data = [
            'title' => 'Manajemen Hotel - Admin',
            'hotels' => $hotels,
            'filters' => $filters,
            'user' => $_SESSION
        ];

        $this->view('admin/hotels/index', $data);
    }

    /**
     * Verifikasi Hotel
     */
    public function verify($id) {
        $this->ensureCsrfToken(); // Security check

        if ($this->hotelModel->verify($id)) {
            $_SESSION['flash_success'] = "Hotel berhasil diverifikasi dan diaktifkan.";
        } else {
            $_SESSION['flash_error'] = "Gagal memverifikasi hotel.";
        }

        header('Location: ' . BASE_URL . '/admin/hotels');
        exit;
    }

    /**
     * Hapus Hotel
     */
    public function delete($id) {
        $this->ensureCsrfToken();

        if ($this->hotelModel->deleteByAdmin($id)) {
            $_SESSION['flash_success'] = "Hotel berhasil dihapus permanen.";
        } else {
            $_SESSION['flash_error'] = "Gagal menghapus hotel.";
        }

        header('Location: ' . BASE_URL . '/admin/hotels');
        exit;
    }
}