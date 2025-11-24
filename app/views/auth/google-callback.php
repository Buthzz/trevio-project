<?php
// Mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions path
require_once __DIR__ . '/../../../helpers/functions.php';

// Simulasi login Google (Mock)
// Di lingkungan produksi, Anda akan menggunakan library client Google API
// untuk menukar kode otorisasi dengan token akses dan mengambil data user.

// Cek apakah ini request login Google (mock)
if (isset($_GET['login_type']) && $_GET['login_type'] === 'google') {
    
    // [SECURITY]: Verifikasi State Token (CSRF Protection)
    if (!isset($_GET['state']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['state'])) {
        die('Akses ditolak: Token State tidak valid. Silakan coba login kembali.');
    }

    // Data dummy user dari Google
    $mockGoogleUser = [
        'id' => '1098237465', // ID unik dari Google
        'name' => 'Trevio User', // Nama user
        'email' => 'user@gmail.com',
        'avatar' => 'https://ui-avatars.com/api/?name=Trevio+User&background=0EA5E9&color=fff', // Avatar placeholder
        'role' => 'guest' // Default role
    ];

    // Simpan data ke sesi
    $_SESSION['user_id'] = $mockGoogleUser['id'];
    $_SESSION['user_name'] = $mockGoogleUser['name'];
    $_SESSION['user_email'] = $mockGoogleUser['email'];
    $_SESSION['user_avatar'] = $mockGoogleUser['avatar'];
    $_SESSION['user_role'] = $mockGoogleUser['role'];
    $_SESSION['is_logged_in'] = true;
    $_SESSION['login_provider'] = 'google';

    // Redirect ke halaman home atau dashboard
    // Gunakan helper trevio_view_route jika tersedia, atau fallback ke path relatif
    if (function_exists('trevio_view_route')) {
        $redirectUrl = trevio_view_route('home/index.php');
    } else {
        $redirectUrl = '../home/index.php';
    }
    
    // Tambahkan parameter sukses untuk notifikasi di frontend
    $redirectUrl .= '?login_success=google';

    header("Location: " . $redirectUrl);
    exit;
} else {
    // Jika diakses langsung tanpa parameter, redirect ke login
    header("Location: login.php");
    exit;
}
?>