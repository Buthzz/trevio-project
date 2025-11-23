<?php
// Judul halaman untuk header
$pageTitle = '500 Server Error | Trevio';

// Include header
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex min-h-[70vh] flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-8 rounded-full bg-yellow-50 p-6">
        <svg class="h-20 w-20 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
        </svg>
    </div>
    
    <h1 class="mb-4 text-4xl font-extrabold text-slate-900 md:text-6xl" style="user-select: none;">500</h1>
    <h2 class="mb-6 text-xl font-bold text-slate-800 md:text-2xl" style="user-select: none;">Terjadi Kesalahan Server</h2>
    
    <p class="mx-auto mb-8 max-w-lg text-slate-600 leading-relaxed" style="user-select: none;">
        Terjadi masalah di sisi server web yang mencegahnya untuk memproses permintaan. Ini adalah pesan kesalahan umum yang tidak menunjukkan penyebab spesifiknya.
    </p>

    <div class="flex flex-col gap-4 sm:flex-row">
        <a href="<?= htmlspecialchars($homeLink ?? '/') ?>" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Coba Lagi Nanti
        </a>
        <button onclick="location.reload()" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:ring-offset-2">
            Refresh Halaman
        </button>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../layouts/footer.php';
?>
