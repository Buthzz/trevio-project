<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Hotel;

class HomeController extends Controller {
    private $hotelModel;
    
    public function __construct() {
        $this->hotelModel = new Hotel();
    }
    
    public function index() {
        // Get featured/active hotels from database
        $hotels = $this->hotelModel->getFeatured(8); // Top 8 hotels
        
        // Get popular destinations for quick filters
        $destinations = $this->hotelModel->getPopularDestinations();
        
        // Get featured reviews (rating >= 4.8)
        $testimonials = $this->hotelModel->getFeaturedReviews(3, 4.8);
        
        $data = [
            'title' => 'Trevio - Find Your Perfect Stay',
            'hotels' => $hotels,
            'destinations' => $destinations,
            'testimonials' => $testimonials,
            'benefits' => [
                [
                    'icon' => '💰',
                    'title' => 'Harga Transparan',
                    'description' => 'Tidak ada biaya tersembunyi saat checkout.'
                ],
                [
                    'icon' => '⚡',
                    'title' => 'Konfirmasi Instan',
                    'description' => 'E-voucher terbit otomatis setelah pembayaran.'
                ],
                [
                    'icon' => '🔄',
                    'title' => 'Fleksibel',
                    'description' => 'Reschedule mudah dan opsi refund tersedia.'
                ],
            ]
        ];
        
        // Pastikan file app/views/home/index.php ada
        $this->view('home/index', $data);
    }
}