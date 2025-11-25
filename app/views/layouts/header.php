<?php
// Helper global agar fungsi routing tersedia di seluruh header.
require_once __DIR__ . '/../../../helpers/functions.php';

// Pastikan session dimulai untuk akses data user
trevio_start_session();

// Backend bisa mengisi variabel ini sebelum require header.
$manualAuthOverrides = [];
if (isset($isAuthenticated)) {
    $manualAuthOverrides['isAuthenticated'] = (bool) $isAuthenticated;
}
if (isset($profileName)) {
    $manualAuthOverrides['profileName'] = $profileName;
}
if (isset($profilePhoto)) {
    $manualAuthOverrides['profilePhoto'] = $profilePhoto;
}
if (isset($profileLink)) {
    $manualAuthOverrides['profileLink'] = $profileLink;
}

// [PERBAIKAN]: Hapus/Komentari Simulasi Login agar menggunakan AuthController asli
/*
$SIMULATE_LOGIN = filter_var(getenv('TREVIO_SIMULATE_LOGIN'), FILTER_VALIDATE_BOOLEAN);
if ($SIMULATE_LOGIN) {
    // ... code simulasi ...
}
*/

// Pakai helper agar header selalu memiliki context terbaru (override + sesi).
$authContext     = trevio_get_auth_context($manualAuthOverrides);
$isAuthenticated = $authContext['isAuthenticated'];
$profileName     = $authContext['profileName'];
$profilePhoto    = $authContext['profilePhoto'];
$profileInitial  = $authContext['profileInitial'];
$profileLink     = $authContext['profileLink'];

// Judul default ketika view tidak memberikan $pageTitle.
$pageTitle  = $pageTitle ?? 'Trevio';

// [PERBAIKAN ROUTING]: Gunakan BASE_URL untuk link bersih (MVC Friendly)
// Pastikan BASE_URL didefinisikan di app/config/app.php
if (!defined('BASE_URL')) {
    // Fallback basic jika config belum terload sempurna
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/trevio-project/public'); 
}

$homeLink = BASE_URL . '/home'; // Ke HomeController@index
$logoUrl  = BASE_URL . '/images/trevio.svg'; // Asset publik
$loginUrl = BASE_URL . '/auth/login'; // Ke AuthController@login
$registerUrl = BASE_URL . '/auth/register'; // Ke AuthController@register
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        primary: '#111827',
                        accent: '#2563eb',
                        accentLight: '#1d4ed8',
                        slateSoft: '#64748b'
                    }
                }
            }
        };
    </script>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/custom.css">

    <script src="<?= BASE_URL ?>/js/sweetalert2.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">

<header class="bg-white border-b border-slate-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:gap-6 sm:px-6 sm:py-4">
        <a class="inline-flex items-center gap-3" href="<?= htmlspecialchars($homeLink) ?>">
            <span class="sr-only">Beranda Trevio</span>
            <img class="h-12 w-auto sm:h-16 md:h-20" src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Trevio" style="user-select: none;">
        </a>

        <div class="hidden items-center gap-3 sm:gap-4 md:flex">
            <?php // Jika sudah login, tampilkan tombol profil saja ?>
            <?php if ($isAuthenticated): ?>
                <?php
                // [PERBAIKAN ROUTING]: Hapus .php dari URL dashboard
                $dashboardLink = '#';
                $dashboardLabel = '';
                $userRole = $authContext['userRole'] ?? 'guest';

                if ($userRole === 'admin') {
                    // Ke AdminController@index (atau dashboard)
                    $dashboardLink = BASE_URL . '/admin'; 
                    $dashboardLabel = 'Dashboard Admin';
                } elseif ($userRole === 'owner') { // Sesuai database role 'owner' (bukan host)
                    // Ke OwnerController@index
                    $dashboardLink = BASE_URL . '/owner'; 
                    $dashboardLabel = 'Dashboard Owner';
                } elseif ($userRole === 'customer') {
                    // Ke DashboardController@index (Customer Dashboard)
                    $dashboardLink = BASE_URL . '/dashboard';
                    $dashboardLabel = 'Dashboard Saya';
                }
                
                $logoutLink = BASE_URL . '/auth/logout?csrf_token=' . trevio_csrf_token();
                ?>
                
                <div class="relative" data-profile-dropdown>
                    <button type="button"
                       class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-accent hover:text-accent focus:outline-none"
                       onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <?php if ($profilePhoto): ?>
                            <img class="h-8 w-8 rounded-full object-cover" src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto profil">
                        <?php else: ?>
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-sm font-bold text-white">
                                <?= htmlspecialchars($profileInitial) ?>
                            </span>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($profileName) ?></span>
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div class="hidden absolute right-0 mt-2 w-48 origin-top-right rounded-xl border border-slate-100 bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <?php if ($dashboardLabel): ?>
                            <a href="<?= htmlspecialchars($dashboardLink) ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <?= htmlspecialchars($dashboardLabel) ?>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= htmlspecialchars($logoutLink) ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            Keluar
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php // Jika belum login, tunjukkan tombol Masuk & Daftar ?>
                <a href="<?= htmlspecialchars($loginUrl) ?>"
                   class="text-sm font-medium text-slate-700 hover:text-primary">
                    Masuk
                </a>
                <a href="<?= htmlspecialchars($registerUrl) ?>"
                   class="inline-flex items-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white shadow-lg hover:bg-accentLight">
                    Daftar
                </a>
            <?php endif; ?>
        </div>

        <button class="inline-flex items-center justify-center rounded-full border border-slate-200 p-2 text-slateSoft transition hover:border-accent hover:text-accent focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 md:hidden"
                type="button"
                data-mobile-toggle>
            <span class="sr-only">Buka menu</span>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="mobile-nav hidden border-t border-slate-200 bg-white px-4 pb-6 pt-4 shadow-lg md:hidden"
         data-mobile-panel>
        <div class="flex flex-col gap-4">
            <div>
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200">
                    <span class="relative inline-flex h-5 w-5 overflow-hidden rounded-full border border-slate-300 shadow-sm">
                        <span class="absolute inset-x-0 top-0 h-1/2 bg-red-600"></span>
                        <span class="absolute inset-x-0 bottom-0 h-1/2 bg-white"></span>
                    </span>
                    <span>Indonesia</span>
                </button>
            </div>

            <?php if ($isAuthenticated): ?>
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-3 rounded-xl p-2">
                        <?php if ($profilePhoto): ?>
                            <img class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100" src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto profil">
                        <?php else: ?>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-accent text-lg font-bold text-white shadow-sm">
                                <?= htmlspecialchars($profileInitial) ?>
                            </span>
                        <?php endif; ?>
                        <div class="flex flex-col">
                            <span class="font-semibold text-slate-900"><?= htmlspecialchars($profileName) ?></span>
                            <span class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($userRole) ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-2 space-y-1 pl-2">
                        <?php if ($dashboardLabel): ?>
                            <a href="<?= htmlspecialchars($dashboardLink) ?>" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                <?= htmlspecialchars($dashboardLabel) ?>
                            </a>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($logoutLink) ?>" class="block rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                            Keluar
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4">
                    <a href="<?= htmlspecialchars($loginUrl) ?>"
                       class="flex w-full items-center justify-center rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary">
                        Masuk
                    </a>
                    <a href="<?= htmlspecialchars($registerUrl) ?>"
                       class="flex w-full items-center justify-center rounded-xl bg-accent py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-accentLight hover:shadow-lg">
                        Daftar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="relative">
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('[data-mobile-toggle]');
        const panel  = document.querySelector('[data-mobile-panel]');
        
        // Mobile menu toggle
        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                panel.classList.toggle('hidden');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.querySelector('[data-profile-dropdown]');
            if (dropdown && !dropdown.contains(event.target)) {
                const menu = dropdown.querySelector('div[class*="absolute"]');
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            }
        });
    });
</script>