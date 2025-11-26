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

<section class="bg-slate-50 py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-accent">Eksplorasi</p>
                <h1 class="text-3xl font-bold text-slate-900 md:text-4xl">Temukan Penginapan Ideal</h1>
                <p class="mt-2 text-slate-500">Sesuaikan pilihan dengan preferensi dan budget liburanmu.</p>
            </div>
            
            <!-- Top Search Bar (Optional, simplified) -->
            <form class="flex w-full flex-col gap-2 md:w-auto md:flex-row md:items-center" method="get" action="">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="q" value="<?= htmlspecialchars($filters['query']) ?>" class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm font-medium placeholder-slate-400 focus:border-accent focus:ring-accent" placeholder="Cari nama hotel atau kota...">
                </div>
                <!-- Hidden inputs to keep filters -->
                <input type="hidden" name="city" value="<?= htmlspecialchars($filters['city']) ?>" />
                <input type="hidden" name="price" value="<?= htmlspecialchars($filters['price']) ?>" />
                <input type="hidden" name="rating" value="<?= htmlspecialchars($filters['rating']) ?>" />
                <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort']) ?>" />
                <?php foreach ($filters['facility'] as $facility): ?>
                    <input type="hidden" name="facility[]" value="<?= htmlspecialchars($facility) ?>" />
                <?php endforeach; ?>
                <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">Cari</button>
            </form>
        </div>

        <?php
        // --- DUMMY DATA INJECTION (If empty) ---
        if (empty($hotels)) {
            $hotels = [
                [
                    'id' => 1,
                    'name' => 'Grand Luxury Hotel Jakarta',
                    'city' => 'Jakarta Pusat',
                    'average_rating' => 4.8,
                    'total_reviews' => 1250,
                    'min_price' => 1500000,
                    'main_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                    'facilities' => ['Kolam Renang', 'Spa', 'Gym', 'Wi-Fi']
                ],
                [
                    'id' => 2,
                    'name' => 'Sunset Beach Resort Bali',
                    'city' => 'Kuta, Bali',
                    'average_rating' => 4.5,
                    'total_reviews' => 850,
                    'min_price' => 2500000,
                    'main_image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                    'facilities' => ['Pantai Pribadi', 'Sarapan', 'Bar', 'Wi-Fi']
                ],
                [
                    'id' => 3,
                    'name' => 'Mountain View Villa',
                    'city' => 'Bandung',
                    'average_rating' => 4.7,
                    'total_reviews' => 420,
                    'min_price' => 950000,
                    'main_image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                    'facilities' => ['Parkir Gratis', 'Dapur', 'Wi-Fi', 'Taman']
                ],
                [
                    'id' => 4,
                    'name' => 'City Center Inn',
                    'city' => 'Surabaya',
                    'average_rating' => 4.2,
                    'total_reviews' => 300,
                    'min_price' => 450000,
                    'main_image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=800&q=80',
                    'facilities' => ['AC', 'Wi-Fi', 'Resepsionis 24 Jam']
                ],
                [
                    'id' => 5,
                    'name' => 'Heritage Boutique Hotel',
                    'city' => 'Yogyakarta',
                    'average_rating' => 4.9,
                    'total_reviews' => 2100,
                    'min_price' => 1200000,
                    'main_image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80',
                    'facilities' => ['Kolam Renang', 'Restoran', 'Spa', 'Wi-Fi']
                ]
            ];
            $totalResults = count($hotels);
        }
        ?>

        <div class="grid gap-8 md:grid-cols-[240px_1fr] xl:gap-12">
            
            <!-- Sidebar Filter -->
            <aside class="relative">
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Filter</h2>
                            <a href="<?= defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php' ?>" class="text-xs font-semibold text-accent hover:text-accentLight">Reset</a>
                        </div>
                        
                        <form class="space-y-6" method="get" action="">
                            <input type="hidden" name="q" value="<?= htmlspecialchars($filters['query']) ?>" />
                            
                            <!-- Urutkan (Moved to Sidebar) -->
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase text-slate-400">Urutkan</label>
                                <div class="relative">
                                    <select name="sort" onchange="this.form.submit()" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-700 focus:border-accent focus:bg-white focus:ring-accent cursor-pointer">
                                        <option value="recommended" <?= $filters['sort'] === 'recommended' ? 'selected' : '' ?>>Rekomendasi</option>
                                        <option value="lowest-price" <?= $filters['sort'] === 'lowest-price' ? 'selected' : '' ?>>Harga Terendah</option>
                                        <option value="highest-price" <?= $filters['sort'] === 'highest-price' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                        <option value="highest-rating" <?= $filters['sort'] === 'highest-rating' ? 'selected' : '' ?>>Rating Tertinggi</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Harga (Min/Max Inputs) -->
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase text-slate-400">Budget per Malam</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-1 block text-[10px] text-slate-500">Min</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-slate-400">Rp</span>
                                            <input type="number" name="min_price" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>" class="w-full rounded-lg border border-slate-200 py-1.5 pl-7 pr-2 text-sm focus:border-accent focus:ring-accent" placeholder="0">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] text-slate-500">Max</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-slate-400">Rp</span>
                                            <input type="number" name="max_price" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>" class="w-full rounded-lg border border-slate-200 py-1.5 pl-7 pr-2 text-sm focus:border-accent focus:ring-accent" placeholder="Jutaan">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="mt-3 w-full rounded-lg bg-slate-900 py-1.5 text-xs font-bold text-white transition hover:bg-slate-800">Terapkan Harga</button>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Rating (Checkboxes) -->
                            <div>
                                <label class="mb-3 block text-xs font-bold uppercase text-slate-400">Rating Bintang</label>
                                <div class="space-y-2">
                                    <?php 
                                    $ratings = ['5' => '5 Bintang', '4' => '4 Bintang', '3' => '3 Bintang'];
                                    $selectedRatings = isset($_GET['rating_check']) ? (array) $_GET['rating_check'] : [];
                                    foreach ($ratings as $val => $label): 
                                    ?>
                                        <label class="flex cursor-pointer items-center gap-3">
                                            <input type="checkbox" name="rating_check[]" value="<?= $val ?>" <?= in_array($val, $selectedRatings) ? 'checked' : '' ?> onchange="this.form.submit()" class="h-4 w-4 cursor-pointer rounded border-slate-300 text-accent focus:ring-accent" />
                                            <span class="text-sm text-slate-600"><?= $label ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Fasilitas -->
                            <div>
                                <label class="mb-3 block text-xs font-bold uppercase text-slate-400">Fasilitas</label>
                                <div class="space-y-2.5">
                                    <?php foreach ($availableFilters['facility'] as $facility): ?>
                                        <label class="group flex cursor-pointer items-center gap-3">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" name="facility[]" value="<?= htmlspecialchars($facility) ?>" <?= in_array($facility, $filters['facility'], true) ? 'checked' : '' ?> onchange="this.form.submit()" class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-slate-300 bg-white transition-all checked:border-accent checked:bg-accent hover:border-accent" />
                                                <svg class="pointer-events-none absolute left-1/2 top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 transition-opacity peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-slate-600 transition-colors group-hover:text-slate-900"><?= htmlspecialchars($facility) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tips Card -->
                    <div class="rounded-2xl bg-blue-50 p-5">
                        <div class="mb-2 flex items-center gap-2 text-blue-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold text-sm">Tips Hemat</span>
                        </div>
                        <p class="text-xs leading-relaxed text-blue-600">
                            Login untuk akses harga member spesial dan promo tersembunyi di tanggal tertentu.
                        </p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="space-y-6">
                
                <!-- Sort & Count (Simplified) -->
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-600">Menampilkan <span class="font-bold text-slate-900"><?= $totalResults ?></span> properti</p>
                </div>

                <!-- Hotel List -->
                <div class="grid gap-5">
                    <?php if (empty($hotels)): ?>
                        <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50 py-20 text-center">
                            <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">Tidak ada hasil ditemukan</h3>
                            <p class="text-slate-500">Coba ubah kata kunci atau kurangi filter pencarianmu.</p>
                            <a href="<?= defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php' ?>" class="mt-6 rounded-full bg-white px-6 py-2 text-sm font-bold text-accent shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-accentLight">
                                Reset Filter
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($hotels as $hotel): ?>
                            <?php 
                                // Data Preparation
                                $hotelId = $hotel['id'] ?? 0;
                                $hotelName = $hotel['name'] ?? 'Nama Hotel';
                                $hotelCity = $hotel['city'] ?? 'Indonesia';
                                $hotelRating = isset($hotel['average_rating']) ? number_format($hotel['average_rating'], 1) : '4.5';
                                $hotelReviews = $hotel['total_reviews'] ?? 0;
                                $priceRaw = $hotel['min_price'] ?? 0;
                                $hotelPrice = 'IDR ' . number_format($priceRaw, 0, ',', '.');
                                $hotelImage = !empty($hotel['main_image']) ? $hotel['main_image'] : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
                                
                                $highlights = [];
                                if (isset($hotel['facilities'])) {
                                    $highlightsRaw = is_string($hotel['facilities']) ? json_decode($hotel['facilities'], true) : $hotel['facilities'];
                                    if(is_array($highlightsRaw)) {
                                        $highlights = array_slice($highlightsRaw, 0, 3);
                                    }
                                }
                                if (empty($highlights)) $highlights = ['Wifi Gratis', 'AC', 'Layanan 24 Jam'];
                                
                                $detailUrl = defined('BASE_URL') ? BASE_URL . '/hotel/detail?id=' . urlencode($hotelId) : 'detail.php?id=' . urlencode($hotelId);
                                $bookingUrl = defined('BASE_URL') ? BASE_URL . '/booking/create?hotel_id=' . urlencode($hotelId) : '../booking/form.php?hotel_id=' . urlencode($hotelId);
                            ?>
                            
                            <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:border-accent/30 hover:shadow-lg md:flex-row">
                                <!-- Image -->
                                <div class="relative h-64 w-full shrink-0 overflow-hidden bg-slate-100 md:h-auto md:w-72 lg:w-80">
                                    <img src="<?= htmlspecialchars($hotelImage) ?>" alt="<?= htmlspecialchars($hotelName) ?>" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                                    <div class="absolute left-3 top-3">
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-white/90 px-2.5 py-1 text-xs font-bold text-slate-900 backdrop-blur-sm">
                                            <svg class="h-3.5 w-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <?= htmlspecialchars($hotelRating) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900 transition-colors group-hover:text-accent">
                                                    <a href="<?= htmlspecialchars($detailUrl) ?>">
                                                        <?= htmlspecialchars($hotelName) ?>
                                                    </a>
                                                </h3>
                                                <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <?= htmlspecialchars($hotelCity) ?>
                                                </p>
                                            </div>
                                            <!-- Wishlist Button (Visual Only) -->
                                            <button class="rounded-full p-2 text-slate-300 transition hover:bg-slate-50 hover:text-red-500">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <?php foreach ($highlights as $highlight): ?>
                                                <span class="inline-flex items-center rounded-md bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    <?= htmlspecialchars($highlight) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4">
                                        <div>
                                            <p class="text-xs font-medium text-slate-400">Mulai dari</p>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-bold text-slate-900"><?= htmlspecialchars($hotelPrice) ?></span>
                                                <span class="text-xs text-slate-500">/malam</span>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="<?= htmlspecialchars($detailUrl) ?>" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                                Detail
                                            </a>
                                            <a href="<?= htmlspecialchars($bookingUrl) ?>" class="rounded-xl bg-accent px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-accentLight hover:shadow-md">
                                                Pilih Kamar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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