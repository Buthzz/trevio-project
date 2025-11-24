<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Hotel;

class OwnerController extends Controller {
    private $bookingModel;
    private $hotelModel;

    public function __construct() {
        // 1. Cek apakah user sudah login
        // 2. Cek apakah role user adalah 'owner'
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            // Jika bukan owner, lempar ke halaman login atau dashboard user biasa
            // Use relative path atau cek jika BASE_URL sudah defined
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/auth/login');
            exit;
        }

        // Inisialisasi model yang dibutuhkan untuk dashboard
        $this->bookingModel = new Booking();
        $this->hotelModel = new Hotel();
    }

    /**
     * Menampilkan Halaman Dashboard Owner (Statistics & Charts)
     * URL: /owner atau /owner/index
     */
    public function index() {
        $ownerId = $_SESSION['user_id'];

        // Ambil Statistik Utama (Cards)
        // Data diambil dari method yang sudah ada di Booking.php & Hotel.php
        $stats = [
            'my_hotels'       => $this->hotelModel->countByOwner($ownerId),
            'active_bookings' => $this->bookingModel->countActiveByOwner($ownerId),
            'checkin_today'   => $this->bookingModel->countCheckinTodayByOwner($ownerId),
            'revenue_month'   => $this->bookingModel->calculateRevenueByOwner($ownerId, date('m'), date('Y'))
        ];

        // Ambil Data Grafik Mingguan (Chart)
        $chartData = $this->bookingModel->getWeeklyStatsByOwner($ownerId);

        $data = [
            'title'      => 'Owner Dashboard',
            'user'       => $_SESSION,
            'stats'      => $stats,
            'chart_data' => $chartData
        ];

        // Render View Dashboard Owner
        $this->view('owner/dashboard', $data);
    }
}