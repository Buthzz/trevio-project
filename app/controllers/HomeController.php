<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        // Pastikan file view ini ada di: app/views/home/index.php
        $data['title'] = 'Selamat Datang di Trevio';
        
        // Memanggil view landing page
        $this->view('home/index', $data);
    }
}