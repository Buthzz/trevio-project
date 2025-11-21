<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;

class DashboardController extends Controller {
    
    /**
     * Constructor: Proteksi halaman Dashboard
     * Hanya user yang sudah login yang bisa akses
     */
    public function __construct() {
        // Middleware: Pastikan session dimulai (biasanya di index.php)
        if (!isset($_SESSION['user_id'])) {
            // Set flash message jika ada library flash
            if (isset($_SESSION)) {
                $_SESSION['flash_error'] = "Silakan login terlebih dahulu.";
            }
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Method Utama (index)
     * Otomatis redirect ke method spesifik berdasarkan role user
     */
    public function index() {
        // Ambil role dari session (diset saat login di AuthController)
        // Default ke 'customer' jika session role tidak ada (safety)
        $role = $_SESSION['user_role'] ?? 'customer';

        switch ($role) {
            case 'admin':
                $this->adminDashboard();
                break;
            case 'owner':
                $this->ownerDashboard();
                break;
            case 'customer':
                $this->customerDashboard();
                break;
            default:
                // Fallback aman jika role tidak dikenali
                $this->customerDashboard();
                break;
        }
    }

    /**
     * Logic Dashboard untuk ADMIN
     * Menampilkan statistik global sistem
     */
    private function adminDashboard() {
        // Inisialisasi Model yang dibutuhkan
        $userModel = new User();
        $bookingModel = new Booking();
        $hotelModel = new Hotel();

        // Siapkan data statistik untuk View
        // Menggunakan method countAll() dan method statistik lainnya dari Model
        $stats = [
            'total_users'      => $userModel->countAll(),
            'total_hotels'     => $hotelModel->countAll(),
            // Asumsi ada method sumTotalRevenue di BookingModel
            'total_revenue'    => $bookingModel->sumTotalRevenue(), 
            // Menghitung booking yang butuh verifikasi pembayaran
            'pending_payments' => $bookingModel->countByStatus('pending_verification'),
            // Menghitung request refund
            'pending_refunds'  => $bookingModel->countRefundsByStatus('requested')
        ];

        $data = [
            'title'           => 'Admin Dashboard - Trevio',
            'user'            => $_SESSION,
            'stats'           => $stats,
            // Mengambil 5 booking terbaru untuk widget "Recent Activity"
            'recent_bookings' => $bookingModel->getRecentBookings(5), 
            'active_tab'      => 'overview'
        ];

        // Render View: app/views/admin/dashboard.php
        $this->view('admin/dashboard', $data);
    }

    /**
     * Logic Dashboard untuk HOTEL OWNER
     * Menampilkan statistik spesifik untuk hotel milik owner tersebut
     */
    private function ownerDashboard() {
        $ownerId = $_SESSION['user_id'];
        
        $bookingModel = new Booking();
        $hotelModel = new Hotel();

        // Statistik spesifik owner (hanya data miliknya)
        $stats = [
            'my_hotels'       => $hotelModel->countByOwner($ownerId),
            'active_bookings' => $bookingModel->countActiveByOwner($ownerId),
            'checkin_today'   => $bookingModel->countCheckinTodayByOwner($ownerId),
            'revenue_month'   => $bookingModel->calculateRevenueByOwner($ownerId, date('m'), date('Y'))
        ];

        $data = [
            'title'      => 'Owner Dashboard - Trevio',
            'user'       => $_SESSION,
            'stats'      => $stats,
            // Data untuk grafik chart.js (Misal: statistik 7 hari terakhir)
            'chart_data' => $bookingModel->getWeeklyStatsByOwner($ownerId)
        ];

        // Render View: app/views/owner/dashboard.php
        $this->view('owner/dashboard', $data);
    }

    /**
     * Logic Dashboard untuk CUSTOMER
     * Menampilkan riwayat booking dan booking yang sedang aktif
     */
    private function customerDashboard() {
        $customerId = $_SESSION['user_id'];
        $bookingModel = new Booking();

        // Status booking yang dianggap "Aktif"
        $activeStatuses = ['confirmed', 'pending_payment', 'pending_verification', 'checked_in'];
        
        // Status booking yang dianggap "Selesai/Riwayat"
        $pastStatuses = ['completed', 'cancelled', 'refunded', 'rejected'];

        $data = [
            'title'           => 'My Bookings - Trevio',
            'user'            => $_SESSION,
            // Booking yang sedang berjalan/akan datang
            'active_bookings' => $bookingModel->getByCustomer($customerId, $activeStatuses),
            // Riwayat booking masa lalu
            'past_bookings'   => $bookingModel->getByCustomer($customerId, $pastStatuses)
        ];

        // Render View: app/views/customer/dashboard.php
        // Atau bisa juga diarahkan ke 'customer/my_bookings' tergantung struktur view
        $this->view('customer/dashboard', $data);
    }
}