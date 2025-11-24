<?php
// Helper global untuk fungsi routing antar view.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Judul halaman utama landing.
$pageTitle = 'Trevio | Temukan Hotel Favoritmu';

// Data dummy hotel populer untuk kartu inspirasi (Hanya Indonesia).
$hotels = $hotels ?? [
    [
        'id' => 101,
        'name' => 'Padma Hotel Bandung',
        'city' => 'Bandung',
        'start_price' => 2100000,
        'thumbnail' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'rating' => 4.9,
    ],
    [
        'id' => 102,
        'name' => 'The Langham Jakarta',
        'city' => 'Jakarta',
        'start_price' => 3500000,
        'thumbnail' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
        'rating' => 4.8,
    ],
    [
        'id' => 103,
        'name' => 'Amanjiwo Resort',
        'city' => 'Yogyakarta',
        'start_price' => 8500000,
        'thumbnail' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=1200&q=80',
        'rating' => 5.0,
    ],
    [
        'id' => 104,
        'name' => 'The Apurva Kempinski',
        'city' => 'Bali',
        'start_price' => 4200000,
        'thumbnail' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80',
        'rating' => 4.9,
    ],
];

// Keunggulan utama Trevio untuk ditampilkan di fitur highlight.
$benefits = [
    [
        'icon' => '<span class="text-xl font-bold" style="user-select: none;">$</span>',
        'title' => 'Harga Jujur',
        'description' => 'Tidak ada biaya tersembunyi saat checkout.'
    ],
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="user-select: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'title' => 'Konfirmasi Instan',
        'description' => 'E-voucher terbit otomatis setelah pembayaran.'
    ],
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="user-select: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'title' => 'Fleksibilitas',
        'description' => 'Reschedule mudah dan opsi refund tersedia.'
    ],
];

// Testimoni pelanggan untuk meningkatkan kepercayaan.
$testimonials = [
    [
        'name' => 'Budi Prameswari',
        'trip' => 'Staycation di Bandung',
        'rating' => '5.0',
        'quote' => 'Booking lewat Trevio gampang banget, konfirmasi langsung masuk email dan WhatsApp.',
        'avatar' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=300&q=80',
    ],
    [
        'name' => 'M. Hendrik Purwanto',
        'trip' => 'Perjalanan Comifuro ke Jakarta',
        'rating' => '4.9',
        'quote' => 'Harga sesuai yang tampil, fasilitas hotel juga sesuai deskripsi. Sangat rekomendasi.',
        'avatar' => 'https://tugas.animenesia.site/uploads/1762854705_0ffb45e7b7.jpg?auto=format&fit=crop&w=300&q=80',
    ],
    [
        'name' => 'Rina & Hendra',
        'trip' => 'Liburan keluarga di Bali',
        'rating' => '4.8',
        'quote' => 'Proses check-in mulus dan ada pengingat otomatis sebelum keberangkatan.',
        'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80',
    ],
];

// Hanya tampilkan testimoni dengan rating >= 4.8 sesuai kebutuhan hero trust signal.
$featuredTestimonials = array_values(array_filter($testimonials, static function ($testimonial) {
    return (float) ($testimonial['rating'] ?? 0) >= 4.8;
}));

// Daftar destinasi filter cepat pada hero.
$destinations = ['🔥 Semua', 'Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Bali', 'Lombok'];

// Mulai sesi lebih awal agar pengecekan login dapat berjalan aman.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tandai status login untuk dipakai di form search maupun CTA lain.
$isAuthenticated = !empty($_SESSION['user_id']);

// Beritahu header mengenai context user yang sedang aktif.
trevio_share_auth_context([
    'isAuthenticated' => $isAuthenticated,
    'profileName' => $_SESSION['user_name'] ?? 'Traveler Trevio',
    'profilePhoto' => $_SESSION['user_avatar'] ?? null,
]);

// Helper sederhana supaya nilai input bersih sebelum dipakai dalam redirect.
if (!function_exists('trevio_clean_query')) {
    function trevio_clean_query(string $value): string
    {
        return trim($value);
    }
}

// Siapkan URL penting dengan helper agar path konsisten.
if (defined('BASE_URL')) {
    $loginUrl = BASE_URL . '/auth/login';
    $registerUrl = BASE_URL . '/auth/register';
    $searchBaseUrl = BASE_URL . '/hotel/search';
    $hotelDetailUrl = BASE_URL . '/hotel/detail';
} else {
    // Fallback manual jika BASE_URL tidak tersedia
    $loginUrl = trevio_view_route('auth/login.php');
    $registerUrl = trevio_view_route('auth/register.php');
    $searchBaseUrl = trevio_view_route('hotel/search.php');
    $hotelDetailUrl = trevio_view_route('hotel/detail.php');
}

// Normalisasi nilai default agar form tetap terisi saat reload.
$prefillValues = [
    'query' => trevio_clean_query($_GET['q'] ?? ''),
    'city' => trevio_clean_query($_GET['city'] ?? 'Semua Kota'),
    'check_in' => trevio_clean_query($_GET['check_in'] ?? ''),
    'check_out' => trevio_clean_query($_GET['check_out'] ?? ''),
    'guests' => trevio_clean_query($_GET['guests'] ?? ''),
];

// Tangani submit form search dari hero section.
// [BACKEND NOTE]: Logika ini menangani pengiriman form pencarian dari halaman utama.
// Jika user mengklik tombol "Cari", parameter akan dikumpulkan dan user akan diarahkan ke halaman hasil pencarian (hotel/search.php).
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['home_search'])) {
    // Susun parameter pencarian yang akan dilempar ke halaman hotel/search.
    $searchPayload = [
        'q' => $prefillValues['query'],
        'city' => $prefillValues['city'] !== '' ? $prefillValues['city'] : 'Semua Kota',
        'check_in' => $prefillValues['check_in'],
        'check_out' => $prefillValues['check_out'],
        'guests' => $prefillValues['guests'],
    ];

    // Buat query string hanya dari nilai yang tidak kosong supaya URL lebih rapi.
    $searchQueryString = http_build_query(array_filter($searchPayload, static function ($value) {
        return $value !== '';
    }));
    $searchUrl = $searchBaseUrl . ($searchQueryString !== '' ? '?' . $searchQueryString : '');

    if (!$isAuthenticated) {
        // Wajib login sebelum diarahkan ke hasil pencarian.
        // [BACKEND NOTE]: Ubah logika ini jika pencarian diperbolehkan untuk guest (tanpa login).
        $loginRedirect = $loginUrl . '?redirect=' . urlencode($searchUrl);
        header('Location: ' . $loginRedirect);
        exit;
    }

    header('Location: ' . $searchUrl);
    exit;
}
// Render header umum agar nav dan asset konsisten.
require __DIR__ . '/../layouts/header.php';
?>

<!-- Hero landing + form pencarian cepat -->
<div class="relative h-[60vh] min-h-[400px] w-full overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[1200ms] hover:scale-105" style="background-image: url('https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=2070&auto=format&fit=crop');"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/70 via-black/40 to-transparent"></div>

    <div class="absolute top-1/4 left-1/4 h-32 w-32 rounded-full bg-white/5 blur-xl animate-pulse-slow"></div>
    <div class="absolute bottom-1/3 right-1/4 h-40 w-40 rounded-full bg-blue-400/10 blur-xl animate-pulse-slow animation-delay-500"></div>
    <div class="absolute top-1/2 left-1/2 h-24 w-24 rounded-full bg-white/5 blur-xl animate-pulse-slow animation-delay-1000"></div>

    <div class="relative z-10 flex h-full flex-col items-center justify-center px-4 pb-12 text-center text-white"   >
        <h1 class="mb-5 text-4xl font-extrabold leading-tight tracking-tight drop-shadow-lg md:text-6xl" style="user-select: none;">
            Temukan Petualangan <br> Penginapan Impianmu
        </h1>
        <p class="max-w-2xl text-lg font-medium opacity-90 drop-shadow md:text-xl" style="user-select: none;">
            Cari dan pesan hotel terbaik dengan harga jujur, fasilitas lengkap, dan tanpa biaya tersembunyi.
        </p>
    </div>

    <style>
        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); opacity: 0.1; }
            50% { transform: scale(1.25); opacity: 0.25; }
        }
        .animate-pulse-slow { animation: pulse-slow 8s infinite ease-in-out; }
        .animation-delay-500 { animation-delay: 0.5s; }
        .animation-delay-1000 { animation-delay: 1s; }
    </style>
</div>

<!-- Kartu pencarian + destinasi populer -->
<div class="relative z-20 -mt-20 mb-16 mx-auto max-w-6xl px-4 md:-mt-24 md:mb-24 md:px-6">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-xl md:p-8">
        <div class="mb-6 flex items-center gap-2 border-b border-gray-100 pb-4 text-blue-600">
            <div class="rounded-lg bg-blue-50 p-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <span class="text-lg font-bold">Cari Hotel</span>
        </div>

        <form action="" method="get" class="grid grid-cols-1 items-end gap-4 md:grid-cols-12" data-search-form>
            <input type="hidden" name="home_search" value="1">
            <input type="hidden" name="city" value="<?= htmlspecialchars($prefillValues['city']) ?>" data-city-input>
            <div class="group relative md:col-span-4">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-gray-400">Destinasi</label>
                <div class="flex h-[50px] items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 p-3 transition group-hover:bg-white group-hover:border-blue-500">
                    <svg class="h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path></svg>
                    <input class="w-full bg-transparent text-sm font-bold text-gray-800 placeholder-gray-400 outline-none" name="q" placeholder="Mau nginep dimana?" type="text" value="<?= htmlspecialchars($prefillValues['query']) ?>" data-query-input>
                </div>
            </div>

            <div class="group relative md:col-span-2">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-gray-400">Check In</label>
                <div class="h-[50px] rounded-xl border border-gray-200 bg-gray-50 p-3 transition group-hover:bg-white group-hover:border-blue-500">
                    <input class="w-full cursor-pointer bg-transparent text-sm font-bold text-gray-800 placeholder-gray-400 outline-none" name="check_in" placeholder="Tanggal" type="<?= empty($prefillValues['check_in']) ? 'text' : 'date' ?>" onfocus="this.type='date'" value="<?= htmlspecialchars($prefillValues['check_in']) ?>">
                </div>
            </div>

            <div class="group relative md:col-span-2">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-gray-400">Check Out</label>
                <div class="h-[50px] rounded-xl border border-gray-200 bg-gray-50 p-3 transition group-hover:bg-white group-hover:border-blue-500">
                    <input class="w-full cursor-pointer bg-transparent text-sm font-bold text-gray-800 placeholder-gray-400 outline-none" name="check_out" placeholder="Tanggal" type="<?= empty($prefillValues['check_out']) ? 'text' : 'date' ?>" onfocus="this.type='date'" value="<?= htmlspecialchars($prefillValues['check_out']) ?>">
                </div>
            </div>

            <div class="group relative md:col-span-2">
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-gray-400">Tamu</label>
                <div class="flex h-[50px] items-center rounded-xl border border-gray-200 bg-gray-50 p-3 transition group-hover:bg-white group-hover:border-blue-500">
                    <div class="flex items-center gap-2 truncate text-sm font-bold text-gray-800">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <input class="w-full truncate bg-transparent text-sm font-bold text-gray-800 placeholder-gray-400 outline-none" name="guests" placeholder="1 Kamar, 2 Tamu" type="text" value="<?= htmlspecialchars($prefillValues['guests']) ?>">
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="hidden select-none text-[10px] font-bold text-transparent md:block">Cari</label>
                <button class="flex h-[50px] w-full items-center justify-center gap-2 rounded-xl bg-blue-600 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-blue-700 active:scale-95" type="submit" data-search-button>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Cari
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Section benefit Trevio -->
<div class="mx-auto mb-20 text-center md:mb-28">
    <div class="mx-auto max-w-5xl px-6">
        <h2 class="mb-12 text-2xl font-bold text-gray-800 md:mb-16 md:text-3xl" style="user-select: none;">Kenapa Booking di Trevio?</h2>
        <div class="relative grid grid-cols-1 gap-8 md:grid-cols-3 md:gap-12">
            <svg class="pointer-events-none absolute top-8 left-[16%] hidden h-20 w-[68%] text-gray-200 md:block" fill="none" stroke="currentColor" stroke-dasharray="6 6" stroke-width="2">
                <path d="M0,10 C50,50 150,50 200,10 S350,-30 400,10 S550,50 600,10" vector-effect="non-scaling-stroke"></path>
            </svg>
            <?php foreach ($benefits as $benefit): ?>
                <div class="relative z-10 flex flex-col items-center rounded-xl border border-gray-50 bg-white p-6 text-center shadow-sm transition md:border-none md:bg-transparent md:p-4 md:shadow-none">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600 shadow-sm">
                        <?= $benefit['icon'] ?>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800" style="user-select: none;"><?= htmlspecialchars($benefit['title']) ?></h3>
                    <p class="mt-2 px-2 text-sm text-gray-500" style="user-select: none;"><?= htmlspecialchars($benefit['description']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Listing hotel populer (dummy) -->
<div id="popular-destinations" class="mx-auto mb-24 max-w-7xl px-4 md:px-6">
    <div class="mb-8 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 md:text-3xl">Destinasi Populer</h2>
            <p class="mt-1 text-gray-500">Pilihan favorit wisatawan minggu ini</p>
        </div>
        <div class="no-scrollbar flex w-full gap-3 overflow-x-auto pb-2 md:w-auto">
            <?php foreach ($destinations as $index => $label): ?>
                <?php $isActive = $index === 0; ?>
                <button class="whitespace-nowrap rounded-full px-5 py-2 text-sm font-medium transition <?= $isActive ? 'bg-gray-900 text-white shadow-lg shadow-gray-900/20' : 'bg-white border border-gray-200 text-gray-600 hover:border-gray-800 hover:text-gray-900' ?>" type="button" data-destination="<?= htmlspecialchars($label) ?>">
                    <?= htmlspecialchars($label) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($hotels)): ?>
        <div class="rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50 p-12 text-center">
            <svg class="mx-auto mb-4 h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <h3 class="text-lg font-bold text-gray-600">Belum ada data hotel</h3>
            <p class="text-gray-400">Silakan login sebagai Owner untuk menambah hotel.</p>
        </div>
    <?php else: ?>
        <div id="hotel-grid" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($hotels as $hotel): ?>
                <?php $thumbnail = $hotel['thumbnail'] ?? ''; ?>
                <?php $ratingValue = number_format((float)($hotel['rating'] ?? 0), 1); ?>
                <a class="group relative block h-[320px] cursor-pointer overflow-hidden rounded-2xl border border-gray-100 bg-slate-900/5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl md:h-[360px]" href="<?= htmlspecialchars($hotelDetailUrl) ?>?id=<?= urlencode($hotel['id'] ?? '') ?>" data-city="<?= htmlspecialchars($hotel['city']) ?>">
                    <!-- Rating Badge di Pojok Kanan Atas -->
                    <div class="absolute top-4 right-4 z-20 inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-1 text-xs font-bold text-slate-800 shadow-sm backdrop-blur-sm">
                        <svg class="h-3 w-3 text-yellow-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .8 15 8l7 .9-5.2 4.9 1.3 7.2L12 17.8 5 21l1.3-7.2L1 8.9 8 8z"/></svg>
                        <?= htmlspecialchars($ratingValue) ?>
                    </div>
                    
                    <img class="h-full w-full object-cover transition duration-700 group-hover:scale-105" src="<?= htmlspecialchars($thumbnail) ?>" alt="Foto <?= htmlspecialchars($hotel['name']) ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/30 to-transparent opacity-90 transition group-hover:opacity-100"></div>
                    <div class="absolute bottom-0 left-0 w-full p-5 text-white">
                        <div class="mb-2">
                            <h3 class="text-lg font-semibold leading-tight md:text-xl"><?= htmlspecialchars($hotel['name']) ?></h3>
                        </div>
                        <p class="mb-4 flex items-center gap-1 text-sm text-gray-300">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            <?= htmlspecialchars($hotel['city']) ?>
                        </p>
                        <div class="flex items-end justify-between border-t border-white/20 pt-3 text-sm">
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-400">Mulai dari</p>
                                <p class="text-base font-semibold text-yellow-300">Rp <?= number_format((float)($hotel['start_price'] ?? 0), 0, ',', '.') ?></p>
                            </div>
                            <span class="rounded-full bg-blue-600/90 px-3 py-1 text-xs font-semibold">Lihat Detail</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Pesan jika tidak ada hasil filter -->
        <div id="no-results-message" class="hidden rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50 p-12 text-center">
            <svg class="mx-auto mb-4 h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <h3 class="text-lg font-bold text-gray-600">Slot Hotel Tidak Tersedia di lokasi ini</h3>
            <p class="text-gray-400">Coba pilih destinasi lain atau lihat semua hotel.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Testimoni rating tinggi -->
<div class="bg-gray-50 py-20">
    <div class="mx-auto max-w-6xl px-6">
        <div class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-gray-400">Testimoni Tamu</p>
            <h2 class="mt-2 text-3xl font-semibold text-gray-900">Apa kata pengguna Trevio?</h2>
            <p class="mt-3 text-sm text-gray-500">Cerita singkat dari traveler Indonesia yang sudah mencoba Trevio.</p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php if (!empty($featuredTestimonials)): ?>
                <?php foreach ($featuredTestimonials as $testimonial): ?>
                    <article class="flex h-full flex-col rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <img class="h-10 w-10 rounded-full object-cover" src="<?= htmlspecialchars($testimonial['avatar']) ?>" alt="Foto <?= htmlspecialchars($testimonial['name']) ?>">
                            <div>
                                <p class="text-sm font-semibold text-gray-900" style="user-select: none;"><?= htmlspecialchars($testimonial['name']) ?></p>
                                <p class="text-xs text-gray-500" style="user-select: none;"><?= htmlspecialchars($testimonial['trip']) ?></p>
                            </div>
                            <div class="ml-auto inline-flex items-center gap-1 text-xs font-semibold text-yellow-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 .5 15.7 8l8.3 1.2-6 5.8 1.4 8.2L12 19l-7.4 3.8 1.4-8.2-6-5.8L8.3 8z"></path>
                                </svg>
                                <span style="user-select: none;"><?= htmlspecialchars($testimonial['rating']) ?></span>
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-gray-600" style="user-select: none;">"<?= htmlspecialchars($testimonial['quote']) ?>"</p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="md:col-span-3 rounded-3xl border border-dashed border-gray-200 bg-white/70 p-8 text-center text-sm text-gray-500">
                    Belum ada testimoni dengan rating tinggi. Ajak pengguna mengirim review mereka melalui formulir di bawah ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Banner CTA floating (ganti copywriting via array) -->
<div class="fixed bottom-4 left-4 right-4 z-40 md:left-1/2 md:right-auto md:w-auto md:-translate-x-1/2">
    <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-gray-900/90 px-4 py-3 text-white backdrop-blur-md shadow-2xl md:px-6">
        <div class="hidden text-sm font-medium md:block">Dapatkan diskon pengguna baru hingga 50%</div>
        <div class="flex w-full items-center gap-3 md:w-auto">
            <a class="flex-1 rounded-xl bg-blue-600 px-4 py-2 text-center text-sm font-bold text-white transition hover:bg-blue-500 md:flex-none" href="<?= htmlspecialchars($registerUrl) ?>">Daftar Sekarang</a>
            <button class="rounded-lg p-2 transition hover:bg-white/10" type="button" onclick="this.closest('.fixed').style.display='none'">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
</div>

<!-- Scroll to Top Button -->
<button id="scrollToTopBtn" class="fixed bottom-24 right-4 z-50 hidden rounded-full bg-blue-600 p-3 text-white shadow-lg transition hover:bg-blue-700 hover:-translate-y-1 focus:outline-none md:bottom-8 md:right-8" aria-label="Scroll to top">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
</button>

<div class="pb-12"></div>

<?php
// Footer global menutup halaman landing.
require __DIR__ . '/../layouts/footer.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // [BACKEND NOTE]: Inisialisasi variabel untuk fitur pencarian, filter, dan scroll.
    const destinationButtons = document.querySelectorAll('[data-destination]');
    const queryInput = document.querySelector('[data-query-input]');
    const cityInput = document.querySelector('[data-city-input]');
    const hotelGrid = document.getElementById('hotel-grid');
    const noResultsMessage = document.getElementById('no-results-message');
    const hotelCards = hotelGrid ? hotelGrid.querySelectorAll('a[data-city]') : [];
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');

    // [BACKEND NOTE]: Cek parameter URL untuk notifikasi login sukses.
    // Jika ada parameter 'login_success', tampilkan notifikasi toast kepada user.
    const urlParams = new URLSearchParams(window.location.search);
    const loginSuccess = urlParams.get('login_success');
    
    if (loginSuccess) {
        let message = 'Selamat datang kembali!';
        if (loginSuccess === 'register') {
            message = 'Akun berhasil dibuat. Selamat datang di Trevio!';
        } else if (loginSuccess === 'google') {
            message = 'Login Google berhasil. Selamat datang!';
        }

        Swal.fire({
            icon: 'success',
            title: 'Berhasil Masuk!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        // Bersihkan URL tanpa reload
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: newUrl}, '', newUrl);
    }
    
    // [BACKEND NOTE]: Logic Scroll to Top
    // Tombol ini sekarang selalu muncul sesuai permintaan user untuk memudahkan navigasi.
    if (scrollToTopBtn) {
        scrollToTopBtn.classList.remove('hidden');

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // [BACKEND NOTE]: Logic filter destinasi
    // Ketika user mengklik tombol destinasi, filter hotel berdasarkan kota yang dipilih.
    destinationButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedCity = button.getAttribute('data-destination') || '';
            
            // Update UI tombol aktif
            destinationButtons.forEach(btn => {
                btn.classList.remove('bg-gray-900', 'text-white', 'shadow-lg');
                btn.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
            });
            button.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
            button.classList.add('bg-gray-900', 'text-white', 'shadow-lg');

            // Update form search (opsional, agar sinkron)
            if (queryInput && cityInput) {
                if (selectedCity === '🔥 Semua') {
                    queryInput.value = '';
                    cityInput.value = 'Semua Kota';
                } else {
                    queryInput.value = selectedCity;
                    cityInput.value = selectedCity;
                }
            }

            // Filter kartu hotel secara client-side
            let visibleCount = 0;
            hotelCards.forEach(card => {
                const cardCity = card.getAttribute('data-city');
                // Logika filter: Tampilkan jika 'Semua' atau jika kota cocok (case insensitive/partial match)
                if (selectedCity === '🔥 Semua' || cardCity.toLowerCase().includes(selectedCity.toLowerCase())) {
                    card.style.display = ''; // Reset display (block/flex)
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            if (visibleCount === 0) {
                if (noResultsMessage) noResultsMessage.classList.remove('hidden');
                if (hotelGrid) hotelGrid.classList.add('hidden');
            } else {
                if (noResultsMessage) noResultsMessage.classList.add('hidden');
                if (hotelGrid) hotelGrid.classList.remove('hidden');
            }
        });
    });
});
</script>