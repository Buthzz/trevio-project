<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;

class DashboardController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Silakan login terlebih dahulu.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $role = $_SESSION['user_role'] ?? 'customer';

        // Fixed: Strict comparison (Switch uses loose, but good practice to be explicit if using if/else)
        if ($role === 'admin') {
            $this->adminDashboard();
        } elseif ($role === 'owner') {
            $this->ownerDashboard();
        } else {
            $this->customerDashboard();
        }
    }

    private function adminDashboard() {
        $userModel = new User();
        $bookingModel = new Booking();
        $hotelModel = new Hotel();

        // Safety: Ensure methods exist before calling (Addressing Reviewer Note)
        $revenue = method_exists($bookingModel, 'sumTotalRevenue') ? $bookingModel->sumTotalRevenue() : 0;
        $pending = method_exists($bookingModel, 'countByStatus') ? $bookingModel->countByStatus('pending_verification') : 0;

        $stats = [
            'total_users'      => method_exists($userModel, 'countAll') ? $userModel->countAll() : 0,
            'total_hotels'     => method_exists($hotelModel, 'countAll') ? $hotelModel->countAll() : 0,
            'total_revenue'    => $revenue,
            'pending_payments' => $pending,
            'pending_refunds'  => method_exists($bookingModel, 'countRefundsByStatus') ? $bookingModel->countRefundsByStatus('requested') : 0
        ];

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'user' => $_SESSION,
            'stats' => $stats,
            'recent_bookings' => $bookingModel->getRecentBookings(5)
        ]);
    }

    private function ownerDashboard() {
        $ownerId = $_SESSION['user_id'];
        $bookingModel = new Booking();
        $hotelModel = new Hotel();

        $stats = [
            'my_hotels'       => $hotelModel->countByOwner($ownerId),
            'active_bookings' => $bookingModel->countActiveByOwner($ownerId),
            'checkin_today'   => $bookingModel->countCheckinTodayByOwner($ownerId),
            'revenue_month'   => $bookingModel->calculateRevenueByOwner($ownerId, date('m'), date('Y'))
        ];

        $this->view('owner/dashboard', [
            'title' => 'Owner Dashboard',
            'user' => $_SESSION,
            'stats' => $stats,
            'chart_data' => $bookingModel->getWeeklyStatsByOwner($ownerId)
        ]);
    }

    private function customerDashboard() {
        $bookingModel = new Booking();
        $customerId = $_SESSION['user_id'];

        $this->view('customer/dashboard', [
            'title' => 'My Bookings',
            'user' => $_SESSION,
            'active_bookings' => $bookingModel->getByCustomer($customerId, ['confirmed', 'pending_payment', 'pending_verification']),
            'past_bookings'   => $bookingModel->getByCustomer($customerId, ['completed', 'cancelled', 'refunded'])
        ]);
    }
}