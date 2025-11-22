<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Home - Trevio'
        ];
        
        // Pastikan file app/views/home/index.php ada
        $this->view('home/index', $data);
    }
}