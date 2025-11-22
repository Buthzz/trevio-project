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
$assetBase  = $assetBase ?? rtrim(dirname($scriptName), '/');
// Pastikan base path tidak kosong supaya link CSS tetap valid.
$assetBase  = ($assetBase === '' || $assetBase === '/') ? '.' : $assetBase;
// Judul default ketika view tidak memberikan $pageTitle.
$pageTitle  = $pageTitle ?? 'Trevio';

if (preg_match('#^(.*)/app/#', $scriptName, $matches)) {
    $projectBaseUrl = $matches[1];
} else {
    $projectBaseUrl = '';
}

// Default tautan navigasi utama yang bisa dioverride dari view.
$homeLink = $homeLink ?? trevio_view_route('home/index.php');
$logoUrl  = $logoUrl ?? trevio_view_route('../../public/images/trevio.svg');
$loginUrl = trevio_view_route('auth/login.php');
$registerUrl = trevio_view_route('auth/register.php');
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
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link rel="stylesheet" href="<?= $assetBase ?>/css/custom.css">

    <!-- SweetAlert (kalau mau dipakai di page) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">

<!-- Header global + nav utama -->
<header class="sticky top-0 z-40 bg-white border-b border-slate-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:gap-6 sm:px-6 sm:py-4">
        <a class="inline-flex items-center gap-3" href="<?= htmlspecialchars($homeLink) ?>">
            <span class="sr-only">Beranda Trevio</span>
            <img class="h-12 w-auto sm:h-16 md:h-20" src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Trevio">
        </a>

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
                <a href="<?= htmlspecialchars($profileLink) ?>"
                   class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-accent hover:text-accent">
                    <?php if ($profilePhoto): ?>
                        <img class="h-8 w-8 rounded-full object-cover" src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto profil">
                    <?php else: ?>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-sm font-bold text-white">
                            <?= htmlspecialchars($profileInitial) ?>
                        </span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($profileName) ?></span>
                </a>
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
    <div class="mobile-nav hidden border-t border-slate-200 bg-white px-4 pb-6 pt-3 shadow-md sm:px-6 md:hidden"
         data-mobile-panel>
        <div class="flex flex-col items-start gap-3">
            <button type="button"
                    class="flex items-center gap-2 rounded-2xl bg-slate-100 px-4 py-1.5 text-xs font-semibold text-slate-600">
                <span class="relative inline-flex h-5 w-5 overflow-hidden rounded-full border border-slate-300 bg-white">
                    <span class="absolute inset-x-0 top-0 h-1/2 bg-red-600"></span>
                    <span class="absolute inset-x-0 bottom-0 h-1/2 bg-white"></span>
                </span>
                Indonesia
            </button>
            <?php // Versi mobile meniru logika desktop ?>
            <?php if ($isAuthenticated): ?>
                <a href="<?= htmlspecialchars($profileLink) ?>"
                   class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
                    <?php if ($profilePhoto): ?>
                        <img class="h-9 w-9 rounded-full object-cover" src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto profil">
                    <?php else: ?>
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-accent text-base font-bold text-white">
                            <?= htmlspecialchars($profileInitial) ?>
                        </span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($profileName) ?></span>
                </a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($loginUrl) ?>"
                   class="text-sm font-medium text-slate-700 hover:text-primary">
                    Masuk
                </a>
                <a href="<?= htmlspecialchars($registerUrl) ?>"
                   class="inline-flex items-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-accentLight">
                    Daftar
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="relative">
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('[data-mobile-toggle]');
        const panel  = document.querySelector('[data-mobile-panel]');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', function () {
            panel.classList.toggle('hidden');
        });
    });
</script>
