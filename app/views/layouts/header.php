<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$assetBase  = $assetBase ?? rtrim(dirname($scriptName), '/');
$assetBase  = ($assetBase === '' || $assetBase === '/') ? '.' : $assetBase;
$pageTitle  = $pageTitle ?? 'Trevio';
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

<header class="sticky top-0 z-40 bg-white border-b border-slate-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-6 py-4">
        <a class="inline-flex items-center gap-3" href="/">
            <span class="sr-only">Beranda Trevio</span>
            <img class="h-10 w-auto" src="/public/images/trevio.svg" alt="Logo Trevio">
        </a>

        <div class="hidden items-center gap-6 md:flex">
            <button type="button"
                    class="rounded-2xl bg-slate-100 px-4 py-1.5 text-xs font-semibold text-slate-600">
                IDR
            </button>
            <a href="/login"
               class="text-sm font-medium text-slate-700 hover:text-primary">
                Masuk
            </a>
            <a href="/register"
               class="inline-flex items-center rounded-full bg-accent px-6 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-accentLight">
                Daftar
            </a>
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
    <div class="mobile-nav hidden border-t border-slate-200 bg-white px-6 pb-6 pt-3 shadow-md md:hidden"
         data-mobile-panel>
        <div class="flex flex-col items-start gap-3">
            <button type="button"
                    class="rounded-2xl bg-slate-100 px-4 py-1.5 text-xs font-semibold text-slate-600">
                IDR
            </button>
            <a href="/login"
               class="text-sm font-medium text-slate-700 hover:text-primary">
                Masuk
            </a>
            <a href="/register"
               class="inline-flex items-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-accentLight">
                Daftar
            </a>
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
