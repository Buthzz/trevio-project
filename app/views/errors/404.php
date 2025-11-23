<?php
// Judul halaman untuk header
$pageTitle = '404 Not Found | Trevio';

// Include header
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex min-h-[70vh] flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-8 rounded-full bg-blue-50 p-6">
        <svg class="h-20 w-20 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    
    <h1 class="mb-4 text-4xl font-extrabold text-slate-900 md:text-6xl" style="user-select: none;">404</h1>
    <h2 class="mb-6 text-xl font-bold text-slate-800 md:text-2xl" style="user-select: none;">Halaman Tidak Ditemukan</h2>
    
    <p class="mx-auto mb-8 max-w-lg text-slate-600 leading-relaxed" style="user-select: none;">
        Halaman atau sumber daya yang Anda minta tidak ditemukan oleh server. Ini bisa terjadi jika URL salah ketik, halaman sudah dihapus, atau tautan sudah usang.
    </p>

    <div class="flex flex-col gap-4 sm:flex-row">
        <a href="<?= htmlspecialchars($homeLink ?? '/') ?>" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Kembali ke Beranda
        </a>
        <button onclick="history.back()" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:ring-offset-2">
            Kembali Sebelumnya
        </button>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../layouts/footer.php';
?>
