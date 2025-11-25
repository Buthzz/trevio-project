<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Hotel;
use App\Models\Room;

class HotelController extends Controller {
    private $hotelModel;
    private $roomModel;
    
    public function __construct() {
        $this->hotelModel = new Hotel();
        $this->roomModel = new Room();
    }
    
    // ... method search() biarkan tetap sama ...
    public function search() {
        // Get filters from query string
        $filters = [
            'query' => trim($_GET['q'] ?? ''),
            'city' => $_GET['city'] ?? 'Semua Kota',
            'price' => $_GET['price'] ?? 'Semua Harga',
            'rating' => $_GET['rating'] ?? 'Semua Rating',
            'facility' => isset($_GET['facility']) ? (array) $_GET['facility'] : [],
            'sort' => $_GET['sort'] ?? 'recommended',
        ];
        
        // Search hotels from database
        $hotels = $this->hotelModel->search($filters);
        
        // Get available filter options
        $availableFilters = [
            'city' => $this->hotelModel->getPopularDestinations(10),
            'price' => ['Semua Harga', '< 1 juta', '1 - 2 juta', '2 - 3 juta', '> 3 juta'],
            'rating' => ['Semua Rating', '4+', '4.5+', '5'],
            'facility' => ['Kolam Renang', 'Spa', 'Parkir Gratis', 'Wi-Fi', 'Breakfast']
        ];
        
        $data = [
            'title' => 'Cari Hotel - Trevio',
            'hotels' => $hotels,
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'resultCount' => count($hotels)
        ];
        
        $this->view('hotel/search', $data);
    }

    /**
     * Show hotel detail with rooms
     * Supports: /hotel/detail/1 OR /hotel/detail?id=1
     */
    public function detail($id = null) {
        // PERBAIKAN: Cek $_GET['id'] jika parameter $id dari routing kosong
        if ($id === null && isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        // Validate hotel ID
        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
            // Opsional: Tambahkan logging atau session flash message di sini
            // $_SESSION['flash_error'] = 'Hotel ID tidak valid.';
            header('Location: ' . BASE_URL . '/hotel/search');
            exit;
        }
        
        // Get hotel with rooms from database
        $hotel = $this->hotelModel->getDetailWithRooms($id);
        
        if (!$hotel) {
            // $_SESSION['flash_error'] = 'Hotel tidak ditemukan.';
            header('Location: ' . BASE_URL . '/hotel/search');
            exit;
        }
        
        // Get gallery images logic (tetap sama)
        $galleryImages = [];
        if (!empty($hotel['main_image'])) {
            $galleryImages[] = $hotel['main_image'];
        }
        
        if (!empty($hotel['rooms'])) {
            foreach ($hotel['rooms'] as $room) {
                if (!empty($room['main_image']) && !in_array($room['main_image'], $galleryImages)) {
                    $galleryImages[] = $room['main_image'];
                }
            }
        }
        
        $galleryImages = array_slice($galleryImages, 0, 5);
        
        // Pastikan facilities berbentuk array sebelum di-slice
        $facilities = is_string($hotel['facilities']) ? json_decode($hotel['facilities'], true) : ($hotel['facilities'] ?? []);
        if (!is_array($facilities)) $facilities = [];

        $data = [
            'title' => $hotel['name'] . ' - Trevio',
            'hotel' => $hotel,
            'galleryImages' => $galleryImages,
            'galleryHighlights' => array_slice($facilities, 0, count($galleryImages))
        ];
        
        $this->view('hotel/detail', $data);
    }
    
    // ... method quickSearch() biarkan tetap sama ...
    public function quickSearch() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Build search URL with filters
        $params = [];
        
        if (!empty($_POST['destination'])) {
            $params['city'] = $_POST['destination'];
        }
        
        if (!empty($_POST['check_in']) && !empty($_POST['check_out'])) {
            $params['check_in'] = $_POST['check_in'];
            $params['check_out'] = $_POST['check_out'];
        }
        
        if (!empty($_POST['guests'])) {
            $params['guests'] = $_POST['guests'];
        }
        
        // Redirect to search page with parameters
        $queryString = http_build_query($params);
        header('Location: ' . BASE_URL . '/hotel/search?' . $queryString);
        exit;
    }
}