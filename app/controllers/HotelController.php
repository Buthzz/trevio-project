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

    public function index() {
        // Redirect ke home atau halaman search
        header('Location: ' . BASE_URL . '/hotel/search');
        exit;
    }

    public function search() {
        // Logika pencarian (bisa dikembangkan nanti)
        $data = ['title' => 'Cari Hotel'];
        $this->view('hotel/search', $data);
    }

    /**
     * Menampilkan detail hotel.
     * PERBAIKAN: Mendukung parameter $id = null agar bisa menangkap ?id=1 dari URL.
     */
    public function detail($id = null) {
        // 1. Jika $id tidak ada di URL segment (misal: /hotel/detail/1),
        //    maka ambil dari Query String (misal: /hotel/detail?id=1)
        if ($id === null) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        } else {
            $id = filter_var($id, FILTER_VALIDATE_INT);
        }
        
        // 2. Validasi Akhir: Jika ID tetap kosong, redirect ke Home
        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $hotel = $this->hotelModel->find($id);
        if (!$hotel) {
            header('Location: ' . BASE_URL); // Hotel tidak ditemukan
            exit;
        }

        $rooms = $this->roomModel->getByHotel($id);

        // --- PENTING: Tangkap Parameter Pencarian dari URL ---
        // Ini menjaga agar tanggal yang dipilih user di Home tidak hilang
        // Parameter ini akan diteruskan ke tombol "Pilih Kamar" di view
        $searchParams = [
            'check_in'  => $_GET['check_in'] ?? '',
            'check_out' => $_GET['check_out'] ?? '',
            'num_rooms' => $_GET['num_rooms'] ?? 1,
            'guests'    => $_GET['guests'] ?? ''
        ];

        $data = [
            'title' => $hotel['name'],
            'hotel' => $hotel,
            'rooms' => $rooms,
            'search_params' => $searchParams // Kirim ke View
        ];

        $this->view('hotel/detail', $data);
    }
}