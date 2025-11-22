<?php

// Mulai session jika belum ada
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Panggil init.php untuk load semua class
require_once '../app/init.php';

// Jalankan Aplikasi Utama
// Ini memicu App::__construct() di core
$app = new App\Core\App;