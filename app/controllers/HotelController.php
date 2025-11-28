<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Hotel;
use App\Models\Room;
use DateTime;

class HotelController extends Controller {
    private $hotelModel;
    private $roomModel;
    
    public function __construct() {
        $this->hotelModel = new Hotel();
        $this->roomModel = new Room();
    }
    
    /**
     * Halaman Pencarian Hotel
     */
    public function search() {
        // 1. Ambil filter dari query string
        $filters = [
            'query' => trim($_GET['q'] ?? ''),
            'city' => $_GET['city'] ?? 'Semua Kota',
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'rating' => $_GET['rating'] ?? 'Semua Rating',
            'facility' => isset($_GET['facility']) ? (array) $_GET['facility'] : [],
            'sort' => $_GET['sort'] ?? 'recommended',
            // Parameter tambahan untuk diteruskan ke detail (Booking Context)
            'check_in' => $_GET['check_in'] ?? '',
            'check_out' => $_GET['check_out'] ?? '',
            'guests' => $_GET['guests'] ?? '',
            'num_rooms' => $_GET['num_rooms'] ?? ''
        ];
        
        // 2. Cari hotel dari database menggunakan Model
        // Pastikan Model Hotel punya method 'search' yang menerima array filters
        $hotels = $this->hotelModel->search($filters);
        
        // 3. Siapkan Opsi Filter untuk View (Sidebar)
        $availableFilters = [
            'city' => $this->hotelModel->getPopularDestinations(10),
            'rating' => ['Semua Rating', '4+', '4.5+', '5'],
            'facility' => ['Kolam Renang', 'Spa', 'Parkir Gratis', 'Wi-Fi', 'Breakfast', 'Gym', 'AC']
        ];
        
        $data = [
            'title' => 'Cari Hotel - Trevio',
            'hotels' => $hotels,
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'total' => count($hotels)
        ];
        
        $this->view('hotel/search', $data);
    }

    /**
     * Halaman Detail Hotel
     * Menampilkan info hotel, kamar, dan ketersediaan
     */
    public function detail($id = null) {
        // Fallback: Cek $_GET['id'] jika parameter routing kosong
        if ($id === null && isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
            header('Location: ' . BASE_URL . '/hotel/search');
            exit;
        }
        
        // 1. Ambil Data Hotel
        $hotel = $this->hotelModel->getDetailWithRooms($id);
        
        if (!$hotel) {
            header('Location: ' . BASE_URL . '/hotel/search');
            exit;
        }

        // 2. Tangkap Parameter Pencarian (Search Context)
        // Default: Check-in hari ini, Check-out besok
        $checkIn = !empty($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
        $checkOut = !empty($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
        $guests = $_GET['guests'] ?? '2 Tamu';
        $numRooms = (int)($_GET['num_rooms'] ?? 1);
        if ($numRooms < 1) $numRooms = 1;
        
        // Validasi Tanggal Dasar
        if (strtotime($checkOut) <= strtotime($checkIn)) {
            $checkOut = date('Y-m-d', strtotime($checkIn . ' +1 day'));
        }

        // Hitung Durasi Malam
        try {
            $d1 = new DateTime($checkIn);
            $d2 = new DateTime($checkOut);
            $diff = $d1->diff($d2);
            $nights = $diff->days;
        } catch (\Exception $e) {
            $nights = 1;
        }
        if ($nights < 1) $nights = 1;

        // 3. Proses Data Kamar (Cek Availability & Hitung Harga Total)
        $processedRooms = [];
        if (!empty($hotel['rooms'])) {
            foreach ($hotel['rooms'] as $room) {
                // Cek ketersediaan real-time ke DB (Room Model)
                // Return: ['is_available' => bool, 'remaining' => int]
                $availability = $this->roomModel->checkAvailability($room['id'], $checkIn, $checkOut);
                
                // Hitung apakah sisa kamar cukup untuk request user
                $isAvailable = $availability['remaining'] >= $numRooms;
                
                // Hitung Total Harga
                $pricePerNight = (float)$room['price_per_night'];
                $totalPrice = $pricePerNight * $nights * $numRooms;

                // Cek Kapasitas (Opsional: Warning jika over capacity)
                // Parsing integer dari string tamu (misal "2 Dewasa" -> 2)
                $guestCount = (int) filter_var($guests, FILTER_SANITIZE_NUMBER_INT) ?: 2;
                $roomCapacity = ($room['capacity'] ?? 2) * $numRooms;
                
                // Append data tambahan ke array room untuk dipakai di View
                $room['search_data'] = [
                    'is_available' => $isAvailable,
                    'remaining_slots' => $availability['remaining'],
                    'total_price' => $totalPrice,
                    'nights' => $nights,
                    'req_rooms' => $numRooms,
                    'capacity_warning' => ($guestCount > $roomCapacity)
                ];
                
                $processedRooms[] = $room;
            }
        }
        
        // Override rooms dengan data yang sudah diproses
        $hotel['rooms'] = $processedRooms;

        // Siapkan Gambar Galeri
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
        // Batasi 5 gambar agar UI rapi
        $galleryImages = array_slice($galleryImages, 0, 5);

        $data = [
            'title' => $hotel['name'] . ' - Trevio',
            'hotel' => $hotel,
            'galleryImages' => $galleryImages,
            // Kirim parameter pencarian kembali ke View untuk pre-fill dan UI logic
            'searchParams' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
                'num_rooms' => $numRooms,
                'guests' => $guests
            ]
        ];
        
        $this->view('hotel/detail', $data);
    }
    
    /**
     * Menangani POST request dari Quick Search Form (misal di Hero Section)
     * Redirect ke halaman search dengan parameter GET yang benar
     */
    public function quickSearch() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Build search URL params
        $params = [];
        
        // Mapping input name dari form ke parameter URL search
        if (!empty($_POST['destination'])) {
            $params['q'] = $_POST['destination']; // 'q' atau 'city' tergantung implementasi search
            $params['city'] = $_POST['destination'];
        }
        
        if (!empty($_POST['check_in'])) $params['check_in'] = $_POST['check_in'];
        if (!empty($_POST['check_out'])) $params['check_out'] = $_POST['check_out'];
        if (!empty($_POST['guests'])) $params['guests'] = $_POST['guests'];
        if (!empty($_POST['num_rooms'])) $params['num_rooms'] = $_POST['num_rooms'];
        
        // Redirect to search page
        $queryString = http_build_query($params);
        header('Location: ' . BASE_URL . '/hotel/search?' . $queryString);
        exit;
    }
}