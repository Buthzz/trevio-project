<?php
// Helper routing untuk memastikan link antar view konsisten.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Judul halaman pencarian hotel.
$pageTitle = 'Trevio | Cari & Filter Hotel';

// --- LOGIC DATA ---

// 1. Ambil data dari Controller
//    Controller harus mengirim: $hotels (array hasil), $filters (state filter saat ini), $availableFilters (opsi)
//    Jika tidak ada, gunakan default kosong/standar.

$hotels = isset($data['hotels']) ? $data['hotels'] : []; 
$totalResults = isset($data['total']) ? $data['total'] : count($hotels);

// 2. State Filter (untuk mengisi ulang form)
$filters = isset($data['filters']) ? $data['filters'] : [
    'query' => trim($_GET['q'] ?? ''),
    'city' => $_GET['city'] ?? 'Semua Kota',
    'price' => $_GET['price'] ?? 'Semua Harga',
    'rating' => $_GET['rating'] ?? 'Semua Rating',
    'facility' => isset($_GET['facility']) ? (array) $_GET['facility'] : [],
    'sort' => $_GET['sort'] ?? 'recommended',
];

// 3. Opsi Filter (Bisa dari DB atau Hardcoded)
$availableFilters = [
    'city' => ['Semua Kota', 'Jakarta', 'Bali', 'Bandung', 'Yogyakarta', 'Surabaya', 'Semarang', 'Malang'],
    'price' => ['Semua Harga', '< 1 juta', '1 - 2 juta', '2 - 3 juta', '> 3 juta'],
    'rating' => ['Semua Rating', '4+', '4.5+', '5'],
    'facility' => ['Kolam Renang', 'Spa', 'Parkir Gratis', 'Wi-Fi', 'Sarapan', 'Gym', 'AC']
];

// Header khusus halaman hotel.
require __DIR__ . '/../layouts/header.php';
?>

<section class="bg-slate-100/70 py-16">
    <div class="mx-auto max-w-6xl space-y-8 px-6">
        
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-xl space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Cari Hotel</p>
                <h1 class="text-3xl font-semibold text-primary">Temukan hotel terbaik sesuai preferensi kamu</h1>
                <p class="text-sm text-slate-500">Gunakan filter fasilitas, rating, dan rentang harga untuk menemukan hotel yang paling cocok.</p>
            </div>
            
            <form class="flex w-full flex-col gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:px-6" method="get" action="">
                <div class="flex flex-1 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input class="h-11 w-full border-0 bg-transparent text-sm focus:outline-none" name="q" value="<?= htmlspecialchars($filters['query']) ?>" placeholder="Cari kota atau hotel" />
                </div>
                <input type="hidden" name="city" value="<?= htmlspecialchars($filters['city']) ?>" />
                <input type="hidden" name="price" value="<?= htmlspecialchars($filters['price']) ?>" />
                <input type="hidden" name="rating" value="<?= htmlspecialchars($filters['rating']) ?>" />
                <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort']) ?>" />
                <?php foreach ($filters['facility'] as $facility): ?>
                    <input type="hidden" name="facility[]" value="<?= htmlspecialchars($facility) ?>" />
                <?php endforeach; ?>
                
                <button class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-accentLight" type="submit">Cari</button>
            </form>
        </div>

        <div class="grid gap-8 lg:grid-cols-[280px,1fr]">
            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold text-primary">Filter</h2>
                    <form class="mt-4 space-y-5" method="get" action="">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($filters['query']) ?>" />
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort']) ?>" />
                        
                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-400">Kota</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm bg-white" name="city" onchange="this.form.submit()">
                                <?php foreach ($availableFilters['city'] as $city): ?>
                                    <option value="<?= htmlspecialchars($city) ?>" <?= $filters['city'] === $city ? 'selected' : '' ?>><?= htmlspecialchars($city) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-400">Harga</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm bg-white" name="price" onchange="this.form.submit()">
                                <?php foreach ($availableFilters['price'] as $price): ?>
                                    <option value="<?= htmlspecialchars($price) ?>" <?= $filters['price'] === $price ? 'selected' : '' ?>><?= htmlspecialchars($price) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-400">Rating</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm bg-white" name="rating" onchange="this.form.submit()">
                                <?php foreach ($availableFilters['rating'] as $rating): ?>
                                    <option value="<?= htmlspecialchars($rating) ?>" <?= $filters['rating'] === $rating ? 'selected' : '' ?>><?= htmlspecialchars($rating) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <fieldset>
                            <legend class="text-xs font-semibold uppercase text-slate-400">Fasilitas</legend>
                            <div class="mt-3 space-y-2">
                                <?php foreach ($availableFilters['facility'] as $facility): ?>
                                    <label class="flex items-center gap-3 text-sm text-slate-600 cursor-pointer">
                                        <input class="size-4 rounded border-slate-300 text-accent focus:ring-accent" type="checkbox" name="facility[]" value="<?= htmlspecialchars($facility) ?>" <?= in_array($facility, $filters['facility'], true) ? 'checked' : '' ?> onchange="this.form.submit()" />
                                        <?= htmlspecialchars($facility) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>

                        <div class="flex flex-col gap-2 pt-2">
                            <button class="inline-flex w-full items-center justify-center rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900" type="submit">Terapkan Filter</button>
                            <a class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php' ?>">Atur Ulang</a>
                        </div>
                    </form>
                </div>

                <div class="rounded-3xl border border-blue-200 bg-blue-50 p-6 text-sm text-blue-800 hidden lg:block">
                    <h3 class="text-sm font-semibold text-blue-900">Tips mencari hotel</h3>
                    <ul class="mt-3 space-y-2">
                        <li>• Manfaatkan filter fasilitas untuk hasil yang lebih relevan.</li>
                        <li>• Bandingkan harga antar tanggal untuk melihat promo tersembunyi.</li>
                        <li>• Gunakan fitur simpan hotel favorit untuk dibooking nanti.</li>
                    </ul>
                </div>
            </aside>

            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Ditemukan <span class="font-semibold text-primary"><?= $totalResults ?></span> hotel sesuai pencarian.</p>
                    
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                        <span class="text-slate-500">Urutkan:</span>
                        <form method="get" action="">
                            <input type="hidden" name="q" value="<?= htmlspecialchars($filters['query']) ?>" />
                            <input type="hidden" name="city" value="<?= htmlspecialchars($filters['city']) ?>" />
                            <input type="hidden" name="price" value="<?= htmlspecialchars($filters['price']) ?>" />
                            <input type="hidden" name="rating" value="<?= htmlspecialchars($filters['rating']) ?>" />
                            <?php foreach ($filters['facility'] as $facility): ?>
                                <input type="hidden" name="facility[]" value="<?= htmlspecialchars($facility) ?>" />
                            <?php endforeach; ?>
                            
                            <select class="border-0 bg-transparent text-sm font-semibold text-primary focus:outline-none cursor-pointer" name="sort" onchange="this.form.submit()">
                                <option value="recommended" <?= $filters['sort'] === 'recommended' ? 'selected' : '' ?>>Rekomendasi</option>
                                <option value="lowest-price" <?= $filters['sort'] === 'lowest-price' ? 'selected' : '' ?>>Harga Terendah</option>
                                <option value="highest-price" <?= $filters['sort'] === 'highest-price' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                <option value="highest-rating" <?= $filters['sort'] === 'highest-rating' ? 'selected' : '' ?>>Rating Tertinggi</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php if (empty($hotels)): ?>
                        <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4 text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700 mb-2">Tidak ada hasil ditemukan</h3>
                            <p class="text-sm text-slate-500">Coba ubah kata kunci, kurangi filter, atau cari di kota lain.</p>
                            <a href="<?= defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php' ?>" class="mt-4 inline-flex items-center text-sm font-semibold text-accent hover:text-accentLight">
                                Hapus Semua Filter
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($hotels as $hotel): ?>
                            <?php 
                                // Normalisasi Data (DB -> View)
                                $hotelId = $hotel['id'] ?? 0;
                                $hotelName = $hotel['name'] ?? 'Nama Hotel';
                                $hotelCity = $hotel['city'] ?? 'Indonesia';
                                $hotelRating = isset($hotel['average_rating']) ? number_format($hotel['average_rating'], 1) : '4.5';
                                $hotelReviews = $hotel['total_reviews'] ?? 0;
                                
                                // Harga (ambil min_price dari query, atau fallback)
                                $priceRaw = $hotel['min_price'] ?? 0;
                                $hotelPrice = 'IDR ' . number_format($priceRaw, 0, ',', '.');
                                
                                // Gambar
                                $hotelImage = !empty($hotel['main_image']) ? $hotel['main_image'] : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
                                
                                // Highlights (facilities dari DB)
                                $highlights = [];
                                if (isset($hotel['facilities'])) {
                                    $highlightsRaw = is_string($hotel['facilities']) ? json_decode($hotel['facilities'], true) : $hotel['facilities'];
                                    if(is_array($highlightsRaw)) {
                                        $highlights = array_slice($highlightsRaw, 0, 3);
                                    }
                                }
                                if (empty($highlights)) $highlights = ['Wifi Gratis', 'AC', 'Layanan 24 Jam'];
                                
                                // URL Detail
                                $detailUrl = defined('BASE_URL') 
                                    ? BASE_URL . '/hotel/detail?id=' . urlencode($hotelId)
                                    : 'detail.php?id=' . urlencode($hotelId);
                                    
                                // URL Booking (langsung ke form booking hotel tsb)
                                $bookingUrl = defined('BASE_URL')
                                    ? BASE_URL . '/booking/create?hotel_id=' . urlencode($hotelId)
                                    : '../booking/form.php?hotel_id=' . urlencode($hotelId);
                            ?>
                            
                            <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                                <div class="flex flex-col md:flex-row">
                                    <div class="relative h-64 w-full flex-shrink-0 overflow-hidden bg-slate-100 md:h-72 md:w-80">
                                        <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                             src="<?= htmlspecialchars($hotelImage) ?>" 
                                             alt="<?= htmlspecialchars($hotelName) ?>" 
                                             loading="lazy" 
                                             onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'"/>
                                        
                                        <div class="absolute top-4 left-4">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-sm font-semibold text-yellow-500 shadow-lg backdrop-blur-sm">
                                                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 .587l3.668 7.431 8.2 1.194-5.934 5.78 1.402 8.174L12 18.896l-7.336 3.87 1.402-8.174L.132 9.212l8.2-1.194z"></path>
                                                </svg>
                                                <?= htmlspecialchars($hotelRating) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-1 flex-col p-6">
                                        <div class="flex-1 space-y-3">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">
                                                    <a href="<?= htmlspecialchars($detailUrl) ?>" class="hover:text-accent transition-colors">
                                                        <?= htmlspecialchars($hotelName) ?>
                                                    </a>
                                                </h3>
                                                <p class="mt-1 text-sm text-slate-500 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    <?= htmlspecialchars($hotelCity) ?>
                                                </p>
                                                <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                                    <span><?= htmlspecialchars($hotelReviews) ?> ulasan</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach ($highlights as $highlight): ?>
                                                    <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"><?= htmlspecialchars($highlight) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6 flex flex-col gap-4 border-t border-slate-100 pt-4 md:flex-row md:items-end md:justify-between">
                                            <div>
                                                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Mulai dari</p>
                                                <p class="mt-1 text-xl font-bold text-primary"><?= htmlspecialchars($hotelPrice) ?></p>
                                                <p class="text-[10px] text-slate-400">per kamar / malam</p>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2">
                                                <a class="inline-flex items-center justify-center rounded-full border-2 border-slate-900 bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white transition-all hover:border-slate-700 hover:bg-slate-700" href="<?= htmlspecialchars($detailUrl) ?>">
                                                    Lihat Detail
                                                </a>
                                                <a class="inline-flex items-center justify-center rounded-full border-2 border-accent bg-accent px-6 py-2.5 text-sm font-semibold text-white transition-all hover:border-accentLight hover:bg-accentLight" href="<?= htmlspecialchars($bookingUrl) ?>">
                                                    Booking
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($data['pagination'])): ?>
                    <div class="flex justify-center pt-8">
                        </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</section>

<?php
// Footer hotel menutup tampilan pencarian.
require __DIR__ . '/../layouts/footer.php';
?>