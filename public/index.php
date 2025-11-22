<?php

// Mulai session di awal aplikasi
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Muat inisialisasi (Bootstrapper)
require_once '../app/init.php';

// Jalankan Aplikasi (Router)
$app = new App\Core\App;