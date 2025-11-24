<?php
// Helper routing untuk memastikan link antar view konsisten.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Judul halaman pencarian hotel.
$pageTitle = 'Trevio | Cari & Filter Hotel';
// Parameter filter aktif baik dari controller maupun query string.
$filters = $filters ?? [
    'query' => trim($_GET['q'] ?? ''),
    'city' => $_GET['city'] ?? 'Semua Kota',
    'price' => $_GET['price'] ?? 'Semua Harga',
    'rating' => $_GET['rating'] ?? 'Semua Rating',
    'facility' => isset($_GET['facility']) ? (array) $_GET['facility'] : [],
    'sort' => $_GET['sort'] ?? 'recommended',
];
// Pilihan filter yang ditampilkan di sidebar.
$availableFilters = [
    'city' => ['Semua Kota', 'Jakarta', 'Bali', 'Bandung', 'Yogyakarta', 'Surabaya'],
    'price' => ['Semua Harga', '< 1 juta', '1 - 2 juta', '2 - 3 juta', '> 3 juta'],
    'rating' => ['Semua Rating', '4+', '4.5+', '5'],
    'facility' => ['Kolam Renang', 'Spa', 'Parkir Gratis', 'Wi-Fi', 'Breakfast']
];
// Data dummy hasil pencarian saat backend belum mengirim data.
// [BACKEND NOTE]: Ini adalah struktur data yang diharapkan oleh view untuk menampilkan hasil pencarian.
// Pastikan API atau Controller mengirimkan array dengan key: id, name, city, rating, reviews, price, image, highlights.
// ID digunakan untuk routing ke detail page, bukan nama hotel.
$searchResults = $searchResults ?? [
    [
        'id' => 101,
        'name' => 'Padma Hotel Bandung',
        'city' => 'Bandung',
        'rating' => 4.9,
        'reviews' => 1250,
        'price' => 'IDR 2.100.000',
        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Infinity Pool', 'Pemandangan Alam', 'Kids Club']
    ],
    [
        'id' => 102,
        'name' => 'The Langham Jakarta',
        'city' => 'Jakarta',
        'rating' => 4.8,
        'reviews' => 890,
        'price' => 'IDR 3.500.000',
        'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Sky Pool', 'Luxury Spa', 'City View']
    ],
    [
        'id' => 103,
        'name' => 'Amanjiwo Resort',
        'city' => 'Yogyakarta',
        'rating' => 5.0,
        'reviews' => 450,
        'price' => 'IDR 8.500.000',
        'image' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Private Pool', 'Dekat Borobudur', 'Cultural Tour']
    ],
    [
        'id' => 104,
        'name' => 'The Apurva Kempinski',
        'city' => 'Bali',
        'rating' => 4.9,
        'reviews' => 2100,
        'price' => 'IDR 4.200.000',
        'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Aquarium Restaurant', 'Beachfront', 'Luxury Chapel']
    ],
];

/**
 * Mengubah harga berbentuk string menjadi bilangan bulat agar mudah dibandingkan.
 */
function trevio_parse_price_to_int(string $price): int
{
    return (int) preg_replace('/[^\d]/', '', $price);
}

/**
 * Memastikan hotel masuk dalam rentang harga yang dipilih pengguna.
 */
function trevio_match_price_filter(array $hotel, string $selectedPrice): bool
{
    $price = trevio_parse_price_to_int($hotel['price']);

    switch ($selectedPrice) {
        case '< 1 juta':
            return $price <= 1_000_000;
        case '1 - 2 juta':
            return $price >= 1_000_000 && $price <= 2_000_000;
        case '2 - 3 juta':
            return $price >= 2_000_000 && $price <= 3_000_000;
        case '> 3 juta':
            return $price > 3_000_000;
        default:
            return true;
    }
}

/**
 * Mengecek apakah semua fasilitas yang diminta tersedia pada highlight hotel.
 */
function trevio_match_facility_filter(array $hotel, array $selectedFacilities): bool
{
    if (empty($selectedFacilities)) {
        return true;
    }

    $available = array_map(static function ($item) {
        return mb_strtolower($item);
    }, $hotel['highlights'] ?? []);

    foreach ($selectedFacilities as $facility) {
        $facility = mb_strtolower($facility);
        $matched = false;
        foreach ($available as $highlight) {
            if (strpos($highlight, $facility) !== false) {
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            return false;
        }
    }

    return true;
}

/**
 * Menyaring daftar hotel berdasarkan kata kunci, pilihan kota, harga, rating, dan fasilitas.
 */
function trevio_filter_hotels(array $hotels, array $filters): array
{
    $query = mb_strtolower($filters['query']);
    $selectedCity = $filters['city'];
    $selectedPrice = $filters['price'];
    $selectedRating = $filters['rating'];
    $selectedFacilities = $filters['facility'];

    return array_values(array_filter($hotels, static function ($hotel) use ($query, $selectedCity, $selectedPrice, $selectedRating, $selectedFacilities) {
        $matchesQuery = $query === ''
            || strpos(mb_strtolower($hotel['name']), $query) !== false
            || strpos(mb_strtolower($hotel['city']), $query) !== false;

        $matchesCity = $selectedCity === 'Semua Kota'
            || strcasecmp($hotel['city'], $selectedCity) === 0;

        $matchesPrice = trevio_match_price_filter($hotel, $selectedPrice);

        $matchesRating = true;
        if ($selectedRating !== 'Semua Rating') {
            if ($selectedRating === '5') {
                $matchesRating = $hotel['rating'] >= 5.0;
            } else {
                $threshold = (float) rtrim($selectedRating, '+');
                $matchesRating = $hotel['rating'] >= $threshold;
            }
        }

        $matchesFacility = trevio_match_facility_filter($hotel, $selectedFacilities);

        return $matchesQuery && $matchesCity && $matchesPrice && $matchesRating && $matchesFacility;
    }));
}

/**
 * Mengurutkan hotel sesuai opsi dropdown tanpa mengubah hasil filter.
 */
function trevio_sort_hotels(array $hotels, string $sortOption): array
{
    if ($sortOption === 'lowest-price' || $sortOption === 'highest-price') {
        usort($hotels, static function ($a, $b) use ($sortOption) {
            $parsedA = trevio_parse_price_to_int($a['price']);
            $parsedB = trevio_parse_price_to_int($b['price']);
            return $sortOption === 'lowest-price' ? $parsedA <=> $parsedB : $parsedB <=> $parsedA;
        });
    } elseif ($sortOption === 'highest-rating') {
        usort($hotels, static function ($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
    }

    return $hotels;
}

// [BACKEND NOTE]: ===== FILTERING SUDAH AKTIF DAN BERFUNGSI =====
// Filter di sidebar dan sorting sudah terhubung dan berfungsi penuh
// Tidak perlu konfigurasi tambahan - semua sudah otomatis
// 
// Cara kerja:
// 1. User memilih filter di sidebar (city, price, rating, facility)
// 2. Form submit otomatis dan kirim parameter via GET
// 3. Function trevio_filter_hotels() memproses semua filter
// 4. Function trevio_sort_hotels() mengurutkan hasil
// 5. Hasil final ditampilkan di $searchResults
//
// Untuk production: Ganti $searchResults dengan query ke database

// Terapkan filter berdasarkan input pengguna.
$filteredHotels = trevio_filter_hotels($searchResults, $filters);
// Urutkan hasil sesuai preferensi sort.
$searchResults = trevio_sort_hotels($filteredHotels, $filters['sort']);
// Hitung jumlah hotel untuk ditampilkan pada heading.
$resultCount = count($searchResults);

/**
 * Membuat URL relatif menuju view lain berdasarkan struktur direktori sekarang.
 */
function trevio_view_path(string $relative): string
{
    return trevio_view_route($relative);
}

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
                <?php foreach ($filters['facility'] as $facility): ?>
                    <input type="hidden" name="facility[]" value="<?= htmlspecialchars($facility) ?>" />
                <?php endforeach; ?>
                <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort']) ?>" />
                <button class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-accentLight" type="submit">Cari</button>
            </form>
        </div>
        <div class="grid gap-8 lg:grid-cols-[280px,1fr]">
            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold text-primary">Filter</h2>
                    <!-- [BACKEND NOTE]: Form filter SUDAH BERFUNGSI - tidak hanya tampilan -->
                    <!-- Setiap perubahan filter akan submit form dan reload page dengan hasil filter -->
                    <!-- Filter aktif: City, Price Range, Rating, Facilities -->
                    <form class="mt-4 space-y-5" method="get" action="">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($filters['query']) ?>" />
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort']) ?>" />
                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-400">Kota</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" name="city">
                                <?php foreach ($availableFilters['city'] as $city): ?>
                                    <option value="<?= htmlspecialchars($city) ?>" <?= $filters['city'] === $city ? 'selected' : '' ?>><?= htmlspecialchars($city) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-400">Harga</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" name="price">
                                <?php foreach ($availableFilters['price'] as $price): ?>
                                    <option value="<?= htmlspecialchars($price) ?>" <?= $filters['price'] === $price ? 'selected' : '' ?>><?= htmlspecialchars($price) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase text-slate-400">Rating</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" name="rating">
                                <?php foreach ($availableFilters['rating'] as $rating): ?>
                                    <option value="<?= htmlspecialchars($rating) ?>" <?= $filters['rating'] === $rating ? 'selected' : '' ?>><?= htmlspecialchars($rating) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <fieldset>
                            <legend class="text-xs font-semibold uppercase text-slate-400">Fasilitas</legend>
                            <div class="mt-3 space-y-2">
                                <?php foreach ($availableFilters['facility'] as $facility): ?>
                                    <label class="flex items-center gap-3 text-sm text-slate-600">
                                        <input class="size-4 rounded border-slate-300 text-accent focus:ring-accent" type="checkbox" name="facility[]" value="<?= htmlspecialchars($facility) ?>" <?= in_array($facility, $filters['facility'], true) ? 'checked' : '' ?> />
                                        <?= htmlspecialchars($facility) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                        <button class="inline-flex w-full items-center justify-center rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900" type="submit">Terapkan Filter</button>
                        <a class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="search.php">Atur Ulang</a>
                    </form>
                </div>
                <div class="rounded-3xl border border-blue-200 bg-blue-50 p-6 text-sm text-blue-800">
                    <h3 class="text-sm font-semibold text-blue-900">Tips mencari hotel</h3>
                    <ul class="mt-3 space-y-2">
                        <li>• Manfaatkan filter fasilitas untuk hasil yang lebih relevan.</li>
                        <li>• Bandingkan harga antar tanggal untuk melihat promo tersembunyi.</li>
                        <li>• Gunakan fitur simpan hotel favorit untuk dibooking nanti.</li>
                    </ul>
                </div>
                    <!-- /*** Halaman pencarian/filter hotel */ -->
            </aside>
            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Ditemukan <span class="font-semibold text-primary"><?= $resultCount ?></span> hotel sesuai pencarian.</p>
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
                            <select class="border-0 bg-transparent text-sm font-semibold text-primary focus:outline-none" name="sort" onchange="this.form.submit()">
                                <option value="recommended" <?= $filters['sort'] === 'recommended' ? 'selected' : '' ?>>Rekomendasi</option>
                                <option value="lowest-price" <?= $filters['sort'] === 'lowest-price' ? 'selected' : '' ?>>Harga Terendah</option>
                                <option value="highest-price" <?= $filters['sort'] === 'highest-price' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                <option value="highest-rating" <?= $filters['sort'] === 'highest-rating' ? 'selected' : '' ?>>Rating Tertinggi</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="space-y-4">
                    <?php if ($resultCount === 0): ?>
                        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                            Tidak ada hotel yang cocok dengan pencarianmu. Coba ubah kata kunci atau filter yang dipilih.
                        </div>
                    <?php else: ?>
                    <?php foreach ($searchResults as $hotel): ?>
                        <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                            <div class="flex flex-col md:flex-row">
                                <!-- Hotel Image - Larger and more prominent -->
                                <div class="relative h-64 w-full flex-shrink-0 overflow-hidden bg-slate-100 md:h-72 md:w-80">
                                    <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>" loading="lazy" />
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-sm font-semibold text-yellow-500 shadow-lg backdrop-blur-sm">
                                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 .587l3.668 7.431 8.2 1.194-5.934 5.78 1.402 8.174L12 18.896l-7.336 3.87 1.402-8.174L.132 9.212l8.2-1.194z"></path>
                                            </svg>
                                            <?= htmlspecialchars(number_format($hotel['rating'], 1)) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Hotel Details -->
                                <div class="flex flex-1 flex-col p-6">
                                    <div class="flex-1 space-y-3">
                                        <!-- Title and Location -->
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($hotel['name']) ?></h3>
                                            <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars($hotel['city']) ?></p>
                                            <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                                <span><?= htmlspecialchars($hotel['reviews']) ?> ulasan</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Facilities/Highlights -->
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($hotel['highlights'] as $highlight): ?>
                                                <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"><?= htmlspecialchars($highlight) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Price and Actions -->
                                    <div class="mt-6 flex flex-col gap-4 border-t border-slate-100 pt-4 md:flex-row md:items-end md:justify-between">
                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Harga per Malam</p>
                                            <p class="mt-1 text-1xl font-bold text-primary"><?= htmlspecialchars($hotel['price']) ?></p>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-2">
                                            <!-- Link ke detail page menggunakan hotel ID -->
                                            <a class="inline-flex items-center justify-center rounded-full border-2 border-slate-900 bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white transition-all hover:border-slate-700 hover:bg-slate-700" href="detail.php?id=<?= urlencode($hotel['id']) ?>">
                                                Lihat Detail Hotel
                                            </a>
                                            
                                            <!-- Link ke booking form dengan hotel_id -->
                                            <a class="inline-flex items-center justify-center rounded-full border-2 border-accent bg-accent px-6 py-2.5 text-sm font-semibold text-white transition-all hover:border-accentLight hover:bg-accentLight" href="../booking/form.php?hotel_id=<?= urlencode($hotel['id']) ?>">
                                                Booking Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-600">
                    <h3 class="text-base font-semibold text-primary">Akses cepat ke halaman lain</h3>
                    <p class="mt-2">Gunakan tautan berikut untuk berpindah ke alur booking Trevio:</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="../booking/form.php">Form Pemesanan</a>
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="../booking/confirm.php">Konfirmasi Pembayaran</a>
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="../booking/history.php">Riwayat Booking</a>
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="detail.php">Detail Hotel Contoh</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
// Footer hotel menutup tampilan pencarian.
require __DIR__ . '/../layouts/footer.php';
?>