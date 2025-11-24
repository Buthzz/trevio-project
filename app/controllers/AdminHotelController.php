<?php

namespace App\Controllers;

use App\Models\Hotel;
use App\Models\User;

class AdminHotelController extends BaseAdminController {
    private $hotelModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->hotelModel = new Hotel();
        $this->userModel = new User();
    }

    /**
     * Display list of all hotels
     */
    public function index() {
        $data = [
            'title' => 'Manage Hotels - Admin',
            'user' => $_SESSION
        ];
        
        $this->view('admin/hotels/index', $data);
    }

    /**
     * Display hotel details
     */
    public function detail($id) {
        $hotel = $this->hotelModel->getDetailWithRooms($id);
        
        if (!$hotel) {
            $_SESSION['flash_error'] = 'Hotel tidak ditemukan.';
            header('Location: ' . BASE_URL . '/admin/hotels');
            exit;
        }

        $data = [
            'title' => 'Hotel Detail',
            'hotel' => $hotel,
            'user' => $_SESSION
        ];
        
        $this->view('admin/hotels/detail', $data);
    }

    /**
     * Verify hotel (approve/reject)
     */
    public function verify() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/hotels');
            exit;
        }
        
        $hotelId = filter_input(INPUT_POST, 'hotel_id', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $notes = $this->sanitizePost('notes');

        if (!$hotelId || !in_array($status, ['approved', 'rejected'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Data tidak valid.']);
        }

        $updateData = [
            'is_verified' => ($status === 'approved') ? 1 : 0,
            'verification_notes' => $notes,
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => $_SESSION['user_id']
        ];

        $result = $this->hotelModel->update($hotelId, $updateData);

        if ($result) {
            $message = ($status === 'approved') ? 'Hotel berhasil diverifikasi.' : 'Hotel ditolak.';
            $_SESSION['flash_success'] = $message;
            $this->jsonResponse(['success' => true, 'message' => $message]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal memverifikasi hotel.']);
        }
    }

    /**
     * Toggle hotel active status
     */
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/hotels');
            exit;
        }
        
        $hotelId = filter_input(INPUT_POST, 'hotel_id', FILTER_VALIDATE_INT);
        $newStatus = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);

        if (!$hotelId || !in_array($newStatus, [0, 1])) {
            $this->jsonResponse(['success' => false, 'message' => 'Data tidak valid.']);
        }

        $result = $this->hotelModel->update($hotelId, ['is_active' => $newStatus]);

        if ($result) {
            $message = $newStatus ? 'Hotel diaktifkan.' : 'Hotel dinonaktifkan.';
            $_SESSION['flash_success'] = $message;
            $this->jsonResponse(['success' => true, 'message' => $message]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal mengubah status hotel.']);
        }
    }

    /**
     * Delete hotel
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/hotels');
            exit;
        }
        
        $hotelId = filter_input(INPUT_POST, 'hotel_id', FILTER_VALIDATE_INT);

        if (!$hotelId) {
            $this->jsonResponse(['success' => false, 'message' => 'ID hotel tidak valid.']);
        }

        // Check if hotel has active bookings
        // TODO: Add check for active bookings

        $result = $this->hotelModel->delete($hotelId, $_SESSION['user_id']);

        if ($result) {
            $_SESSION['flash_success'] = 'Hotel berhasil dihapus.';
            $this->jsonResponse(['success' => true, 'message' => 'Hotel berhasil dihapus.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menghapus hotel.']);
        }
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
