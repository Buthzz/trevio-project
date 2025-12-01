<?php
// Helper global agar fungsi routing tersedia di seluruh header.
require_once __DIR__ . '/../../../helpers/functions.php';

// Pastikan session dimulai
trevio_start_session();

// Backend bisa mengisi variabel ini sebelum require header.
$manualAuthOverrides = [];
if (isset($isAuthenticated)) $manualAuthOverrides['isAuthenticated'] = (bool) $isAuthenticated;
if (isset($profileName)) $manualAuthOverrides['profileName'] = $profileName;
if (isset($profilePhoto)) $manualAuthOverrides['profilePhoto'] = $profilePhoto;
if (isset($profileLink)) $manualAuthOverrides['profileLink'] = $profileLink;

// [HAPUS SIMULASI LOGIN] - Gunakan data real dari session AuthController
// Code simulasi dihapus agar tidak menimpa login asli.

// Ambil Context Auth
$authContext     = trevio_get_auth_context($manualAuthOverrides);
$isAuthenticated = $authContext['isAuthenticated'];
$profileName     = $authContext['profileName'];
$profilePhoto    = $authContext['profilePhoto'];
$profileInitial  = $authContext['profileInitial'];

// [FITUR BARU]: UI Avatars Generator
// Jika tidak ada foto profil, gunakan UI Avatars berdasarkan nama user
if (empty($profilePhoto)) {
    $encodedName = urlencode($profileName);
    $profilePhoto = "https://ui-avatars.com/api/?name={$encodedName}&background=2563eb&color=fff&size=128&bold=true";
}

// Judul default
$pageTitle = $pageTitle ?? 'Trevio';

// [PERBAIKAN ROUTING]: Definisi BASE_URL (jangan hardcode path!)
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

    // Clean up script directory path
    $scriptDir = str_replace('\\', '/', $scriptDir);
    $scriptDir = rtrim($scriptDir, '/');

    define('BASE_URL', $protocol . '://' . $host . $scriptDir);
}

// Link Navigasi (MVC Friendly)
$homeLink = BASE_URL . '/home';
$logoUrl  = BASE_URL . '/images/trevio.svg';
$loginUrl = BASE_URL . '/auth/login';
$registerUrl = BASE_URL . '/auth/register';
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

    <!-- Cache busting for Tailwind CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/tailwind.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/custom.css?v=<?= time() ?>">
    <script src="<?= BASE_URL ?>/js/sweetalert2.min.js"></script>


</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">

<header id="main-header" class="relative z-50 w-full bg-white border-b border-slate-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:gap-6 sm:px-6 sm:py-4">
        
        <a class="inline-flex items-center gap-3" href="<?= htmlspecialchars($homeLink) ?>">
            <img class="h-10 w-auto sm:h-12 md:h-14" src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Trevio" style="user-select: none;">
        </a>

        <div class="hidden items-center gap-3 sm:gap-4 md:flex">
            
            <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors">
                <span class="relative inline-flex h-5 w-5 overflow-hidden rounded-full border border-slate-300 shadow-sm">
                    <span class="absolute inset-x-0 top-0 h-1/2 bg-red-600"></span>
                    <span class="absolute inset-x-0 bottom-0 h-1/2 bg-white"></span>
                </span>
                IDN
            </button>

            <?php if ($isAuthenticated): ?>
                <?php
                // Logic URL Dashboard berdasarkan Role
                $dashboardLink = '#';
                $dashboardLabel = '';
                $userRole = $authContext['userRole'] ?? 'guest';

                if ($userRole === 'admin') {
                    $dashboardLink = BASE_URL . '/admin';
                    $dashboardLabel = 'Admin Dashboard';
                } elseif ($userRole === 'owner') {
                    $dashboardLink = BASE_URL . '/owner';
                    $dashboardLabel = 'Owner Dashboard';
                } elseif ($userRole === 'customer') {
                    $dashboardLink = BASE_URL . '/dashboard';
                    $dashboardLabel = 'Dashboard Saya';
                }
                
                $logoutLink = BASE_URL . '/auth/logout?csrf_token=' . trevio_csrf_token();
                ?>
                
                <div class="relative" data-profile-dropdown>
                    <button type="button"
                       class="flex items-center gap-3 rounded-full border border-slate-200 bg-white pl-1 pr-4 py-1 text-sm font-semibold text-slate-700 transition hover:border-blue-500 hover:text-blue-600 hover:shadow-sm focus:outline-none group"
                       onclick="this.nextElementSibling.classList.toggle('hidden')">
                        
                        <img class="h-9 w-9 rounded-full object-cover border border-slate-200 group-hover:border-blue-300" 
                             src="<?= htmlspecialchars($profilePhoto) ?>" 
                             alt="Foto profil">
                             
                        <span><?= htmlspecialchars($profileName) ?></span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div class="hidden absolute right-0 mt-2 w-56 origin-top-right rounded-xl border border-slate-100 bg-white py-2 shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-xs text-slate-500">Masuk sebagai</p>
                            <p class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars($profileName) ?></p>
                        </div>

                        <?php if ($dashboardLabel): ?>
                            <a href="<?= htmlspecialchars($dashboardLink) ?>" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                    <?= htmlspecialchars($dashboardLabel) ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= htmlspecialchars($logoutLink) ?>" class="block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors rounded-b-xl">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar
                            </div>
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <a href="<?= htmlspecialchars($loginUrl) ?>"
                   class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors px-2">
                    Masuk
                </a>
                <a href="<?= htmlspecialchars($registerUrl) ?>"
                   class="inline-flex items-center rounded-full bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md transition-all hover:bg-blue-700 hover:shadow-lg active:scale-95">
                    Daftar
                </a>
            <?php endif; ?>
        </div>

        <button class="inline-flex items-center justify-center rounded-full border border-slate-200 p-2.5 text-slate-600 transition hover:border-blue-500 hover:text-blue-600 focus:outline-none md:hidden"
                type="button"
                data-mobile-toggle>
            <span class="sr-only">Buka menu</span>
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="mobile-nav hidden border-t border-slate-200 bg-white px-5 pb-6 pt-4 shadow-xl md:hidden animate-in slide-in-from-top-4 duration-300"
         data-mobile-panel>
        <div class="flex flex-col gap-4">
            <div>
                <button type="button" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">
                    <span class="relative inline-flex h-5 w-5 overflow-hidden rounded-full border border-slate-300 shadow-sm">
                        <span class="absolute inset-x-0 top-0 h-1/2 bg-red-600"></span>
                        <span class="absolute inset-x-0 bottom-0 h-1/2 bg-white"></span>
                    </span>
                    <span>Indonesia (IDN)</span>
                </button>
            </div>

            <?php if ($isAuthenticated): ?>
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                        <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm" src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto profil">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900"><?= htmlspecialchars($profileName) ?></span>
                            <span class="text-xs text-slate-500 font-medium capitalize"><?= htmlspecialchars($userRole) ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-3 grid gap-2">
                        <?php if ($dashboardLabel): ?>
                            <a href="<?= htmlspecialchars($dashboardLink) ?>" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                <?= htmlspecialchars($dashboardLabel) ?>
                            </a>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($logoutLink) ?>" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
                    <a href="<?= htmlspecialchars($loginUrl) ?>"
                       class="flex w-full items-center justify-center rounded-xl border border-slate-200 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                        Masuk
                    </a>
                    <a href="<?= htmlspecialchars($registerUrl) ?>"
                       class="flex w-full items-center justify-center rounded-xl bg-blue-600 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-blue-700">
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
        // -- Mobile Menu Logic --
        const toggle = document.querySelector('[data-mobile-toggle]');
        const panel  = document.querySelector('[data-mobile-panel]');
        
        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                panel.classList.toggle('hidden');
            });
        }

        // -- Profile Dropdown Logic --
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