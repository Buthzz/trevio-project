<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Hotel;

/**
 * Admin Controller - Main Dashboard
 */
class AdminController extends BaseAdminController {
    private $userModel;
    private $bookingModel;
    private $hotelModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->bookingModel = new Booking();
        $this->hotelModel = new Hotel();
    }

    /**
     * Admin Dashboard - Statistics & Overview
     */
    public function dashboard() {
        // Ensure CSRF token
        $this->ensureCsrfToken();
        
        // Gather statistics
        $revenue = method_exists($this->bookingModel, 'sumTotalRevenue') 
            ? $this->bookingModel->sumTotalRevenue() 
            : 0;
            
        $pending = method_exists($this->bookingModel, 'countByStatus') 
            ? $this->bookingModel->countByStatus('pending_verification') 
            : 0;

        // Get daily revenue for the last 7 days
        $dailyRevenue = method_exists($this->bookingModel, 'getDailyRevenue') 
            ? $this->bookingModel->getDailyRevenue(7) 
            : [];

        $stats = [
            'total_users'      => $this->userModel->countAll(),
            'total_hotels'     => method_exists($this->hotelModel, 'countAll') 
                ? $this->hotelModel->countAll() 
                : 0,
            'total_revenue'    => $revenue,
            'pending_payments' => $pending,
            'pending_refunds'  => method_exists($this->bookingModel, 'countRefundsByStatus') 
                ? $this->bookingModel->countRefundsByStatus('requested') 
                : 0
        ];

        $data = [
            'title' => 'Admin Dashboard',
            'user' => $_SESSION,
            'stats' => $stats,
            'daily_revenue' => $dailyRevenue,
            'recent_bookings' => method_exists($this->bookingModel, 'getRecentBookings')
                ? $this->bookingModel->getRecentBookings(5)
                : [],
            'csrf_token' => $_SESSION['csrf_token']
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * Alias untuk dashboard
     */
    public function index() {
        $this->dashboard();
    }
}
