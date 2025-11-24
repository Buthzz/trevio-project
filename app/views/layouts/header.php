<?php
// Helper global agar fungsi routing tersedia di seluruh header.
require_once __DIR__ . '/../../../helpers/functions.php';

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

// [SIMULASI LOGIN - UNTUK TESTING TAMPILAN HEADER]
// Ubah nilai-nilai di bawah untuk melihat tampilan header sesuai role:
// - 'admin' untuk Dashboard Admin
// - 'host' untuk Dashboard Owner
// - 'guest' untuk customer biasa (hanya logout)
// Set environment variable TREVIO_SIMULATE_LOGIN=true untuk enable simulasi
$SIMULATE_LOGIN = filter_var(getenv('TREVIO_SIMULATE_LOGIN'), FILTER_VALIDATE_BOOLEAN);
$SIMULATE_ROLE = getenv('TREVIO_SIMULATE_ROLE') ?: 'guest'; // 'admin', 'host', atau 'guest'
$SIMULATE_NAME = getenv('TREVIO_SIMULATE_NAME') ?: 'M. Hendrik Purwanto';
$SIMULATE_AVATAR = getenv('TREVIO_SIMULATE_AVATAR') ?: 'https://tugas.animenesia.site/uploads/1762854705_0ffb45e7b7.jpg';

// Terapkan simulasi jika diaktifkan
if ($SIMULATE_LOGIN) {
    $_SESSION['user_id'] = 999;
    $_SESSION['user_name'] = $SIMULATE_NAME;
    $_SESSION['user_role'] = $SIMULATE_ROLE;
    $_SESSION['user_avatar'] = $SIMULATE_AVATAR;
}

// Pakai helper agar header selalu memiliki context terbaru (override + sesi).
$authContext    = trevio_get_auth_context($manualAuthOverrides);
$isAuthenticated = $authContext['isAuthenticated'];
$profileName    = $authContext['profileName'];
$profilePhoto   = $authContext['profilePhoto'];
$profileInitial = $authContext['profileInitial'];
$profileLink    = $authContext['profileLink'];

// Nama script aktif dipakai untuk menentukan base asset.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
// Basis path asset bisa dioverride dari view, fallback ke lokasi script.
// Basis path asset bisa dioverride dari view, fallback ke lokasi script.
$assetBase  = $assetBase ?? rtrim(dirname($scriptName), '/');
// Pastikan base path tidak kosong supaya link CSS tetap valid.
// Pastikan base path tidak kosong supaya link CSS tetap valid.
$assetBase  = ($assetBase === '' || $assetBase === '/') ? '.' : $assetBase;
// Judul default ketika view tidak memberikan $pageTitle.
$pageTitle  = $pageTitle ?? 'Trevio';

// Deteksi project base URL untuk routing
if (preg_match('#^(.*)/app/#', $scriptName, $matches)) {
    $projectBaseUrl = $matches[1];
} else {
    $projectBaseUrl = '';
}

// Default tautan navigasi utama yang bisa dioverride dari view.
$homeLink = defined('BASE_URL') ? BASE_URL : trevio_view_route('.');
$logoUrl  = defined('BASE_URL') ? BASE_URL . '/images/trevio.svg' : trevio_view_route('../../public/images/trevio.svg');
$loginUrl = defined('BASE_URL') ? BASE_URL . '/auth/login' : trevio_view_route('auth/login');
$registerUrl = defined('BASE_URL') ? BASE_URL . '/auth/register' : trevio_view_route('auth/register');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind via CDN -->
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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../public/css/custom.css">

    <!-- SweetAlert (kalau mau dipakai di page) -->
    <script src="../../public/js/sweetalert2.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">

<!-- Header global + nav utama -->
<header class="bg-white border-b border-slate-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:gap-6 sm:px-6 sm:py-4">
        <a class="inline-flex items-center gap-3" href="<?= htmlspecialchars($homeLink) ?>">
            <span class="sr-only">Beranda Trevio</span>
            <img class="h-12 w-auto sm:h-16 md:h-20" src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Trevio" style="user-select: none;">
        </a>

        <div class="hidden items-center gap-3 sm:gap-4 md:flex">
        <div class="hidden items-center gap-3 sm:gap-4 md:flex">
            <button type="button"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 sm:px-4 sm:py-1.5 sm:text-xs">
                <span class="relative inline-flex h-5 w-5 overflow-hidden rounded-full border border-slate-300 bg-white">
                    <span class="absolute inset-x-0 top-0 h-1/2 bg-red-600"></span>
                    <span class="absolute inset-x-0 bottom-0 h-1/2 bg-white"></span>
                </span>
                ID
            </button>
            <?php // Jika sudah login, tampilkan tombol profil saja ?>
            <?php if ($isAuthenticated): ?>
                <?php
                // Tentukan dashboard link berdasarkan role
                $dashboardLink = '#';
                $dashboardLabel = '';
                $userRole = $authContext['userRole'] ?? 'guest';

                if ($userRole === 'admin') {
                    $dashboardLink = trevio_view_route('admin/dashboard.php');
                    $dashboardLabel = 'Dashboard Admin';
                } elseif ($userRole === 'host') { // Asumsi 'host' adalah owner
                    $dashboardLink = trevio_view_route('owner/dashboard.php');
                    $dashboardLabel = 'Dashboard Owner';
                }
                $logoutLink = trevio_view_route('auth/logout.php') . '?csrf_token=' . trevio_csrf_token();
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

                    <!-- Dropdown Menu -->
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

    <!-- Nav mobile: cuma IDR, Masuk, Daftar -->
    <div class="mobile-nav hidden border-t border-slate-200 bg-white px-4 pb-6 pt-4 shadow-lg md:hidden"
         data-mobile-panel>
        <div class="flex flex-col gap-4">
            <!-- Language/Region Selector -->
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
                <!-- Authenticated User Mobile -->
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
                <!-- Guest Mobile -->
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