<?php
// Helper routing untuk memastikan link antar view konsisten.
require_once __DIR__ . '/../../../helpers/functions.php';

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
$searchResults = $searchResults ?? [
    [
        'name' => 'The Langham Jakarta',
        'city' => 'Jakarta',
        'rating' => 4.9,
        'reviews' => 412,
        'price' => 'IDR 2.850.000',
        'image' => 'https://images.unsplash.com/photo-1551776235-dde6d4829808?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Infinity pool', 'Sky bar', 'City view']
    ],
    [
        'name' => 'Padma Resort Ubud',
        'city' => 'Bali',
        'rating' => 4.8,
        'reviews' => 289,
        'price' => 'IDR 3.450.000',
        'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Trekking', 'Wellness spa', 'Jungle view']
    ],
    [
        'name' => 'GAIA Hotel Bandung',
        'city' => 'Bandung',
        'rating' => 4.7,
        'reviews' => 198,
        'price' => 'IDR 2.150.000',
        'image' => 'https://images.unsplash.com/photo-1505691723518-36a5ac3be353?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Heated pool', 'Kids club', 'Scenic deck']
    ],
    [
        'name' => 'Hotel Tentrem Yogyakarta',
        'city' => 'Yogyakarta',
        'rating' => 4.9,
        'reviews' => 354,
        'price' => 'IDR 1.980.000',
        'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
        'highlights' => ['Spa', 'Cultural tour', 'Fine dining']
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
            <form class="flex w-full flex-col gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:px-6" method="get" action="<?= htmlspecialchars(trevio_view_path('search.php')) ?>">
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
                    <form class="mt-4 space-y-5" method="get" action="<?= htmlspecialchars(trevio_view_path('search.php')) ?>">
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
                        <button class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" type="reset">Atur Ulang</button>
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
                    /**
                     * Halaman pencarian/filter hotel
                     */
            </aside>
            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Ditemukan <span class="font-semibold text-primary"><?= $resultCount ?></span> hotel sesuai pencarian.</p>
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                        <span class="text-slate-500">Urutkan:</span>
                        <form method="get" action="<?= htmlspecialchars(trevio_view_path('search.php')) ?>">
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
                        <article class="flex flex-col gap-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg md:flex-row">
                            <div class="h-48 w-full flex-shrink-0 overflow-hidden rounded-2xl bg-cover bg-center md:h-40 md:w-64" style="background-image: url('<?= htmlspecialchars($hotel['image']) ?>');"></div>
                            <div class="flex flex-1 flex-col justify-between gap-4">
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-lg font-semibold text-primary"><?= htmlspecialchars($hotel['name']) ?></h3>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 .587l3.668 7.431 8.2 1.194-5.934 5.78 1.402 8.174L12 18.896l-7.336 3.87 1.402-8.174L.132 9.212l8.2-1.194z"></path>
                                            </svg>
                                            <?= htmlspecialchars(number_format($hotel['rating'], 1)) ?>
                                        </span>
                                        <span class="text-xs text-slate-500"><?= htmlspecialchars($hotel['reviews']) ?> ulasan</span>
                                    </div>
                                    <p class="text-sm text-slate-500"><?= htmlspecialchars($hotel['city']) ?></p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($hotel['highlights'] as $highlight): ?>
                                            <span class="badge-soft"><?= htmlspecialchars($highlight) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400">Harga per malam</p>
                                        <p class="text-base font-semibold text-primary"><?= htmlspecialchars($hotel['price']) ?></p>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars(trevio_view_path('../booking/history.php')) ?>">Riwayat</a>
                                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars(trevio_view_path('../booking/detail.php')) ?>?hotel=<?= urlencode($hotel['name']) ?>">Detail Booking</a>
                                        <a class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-700" href="<?= htmlspecialchars(trevio_view_path('detail.php')) ?>?hotel=<?= urlencode($hotel['name']) ?>">Lihat Detail Hotel</a>
                                        <a class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" href="<?= htmlspecialchars(trevio_view_path('../booking/form.php')) ?>?hotel=<?= urlencode($hotel['name']) ?>&city=<?= urlencode($hotel['city']) ?>">Booking Sekarang</a>
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
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars(trevio_view_path('../booking/form.php')) ?>">Form Pemesanan</a>
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars(trevio_view_path('../booking/confirm.php')) ?>">Konfirmasi Pembayaran</a>
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars(trevio_view_path('../booking/history.php')) ?>">Riwayat Booking</a>
                        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars(trevio_view_path('detail.php')) ?>">Detail Hotel Contoh</a>
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
