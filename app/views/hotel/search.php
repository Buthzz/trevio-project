<?php
// Helper routing untuk memastikan link antar view konsisten.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Judul halaman pencarian hotel.
$pageTitle = 'Trevio | Cari & Filter Hotel';

// --- LOGIC DATA ---

$hotels = isset($data['hotels']) ? $data['hotels'] : []; 
$totalResults = isset($data['total']) ? $data['total'] : count($hotels);

// 2. State Filter (untuk mengisi ulang form)
$filters = isset($data['filters']) ? $data['filters'] : [];
$query = $filters['query'] ?? '';
$city = $filters['city'] ?? 'Semua Kota';
$minPrice = $filters['min_price'] ?? '';
$maxPrice = $filters['max_price'] ?? '';
$rating = $filters['rating'] ?? '';
$sort = $filters['sort'] ?? 'recommended';

// 3. Opsi Filter
$availableCities = ['Semua Kota', 'Jakarta', 'Bali', 'Bandung', 'Yogyakarta', 'Surabaya', 'Semarang', 'Malang'];
$availableFacilities = ['Kolam Renang', 'Spa', 'Parkir Gratis', 'Wi-Fi', 'Sarapan', 'Gym', 'AC'];

// [FIX FLOW]: Simpan parameter pencarian (tanggal, tamu, dll) untuk diteruskan ke link detail
// Kita ambil langsung dari $_GET agar data realtime dari URL terambil
$forwardParams = [
    'check_in' => $_GET['check_in'] ?? date('Y-m-d'),
    'check_out' => $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day')),
    'num_rooms' => $_GET['num_rooms'] ?? '1',
    'guest_adults' => $_GET['guest_adults'] ?? '2',
    'guest_children' => $_GET['guest_children'] ?? '0'
];

require __DIR__ . '/../layouts/header.php';
?>

<section class="bg-slate-50 py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-blue-600">Eksplorasi</p>
                <h1 class="text-3xl font-bold text-slate-900 md:text-4xl">Temukan Penginapan Ideal</h1>
                <p class="mt-2 text-slate-500">Sesuaikan pilihan dengan preferensi dan budget liburanmu.</p>
            </div>
            
            <form class="flex w-full flex-col gap-2 md:w-auto md:flex-row md:items-center" method="get" action="">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm font-medium placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Cari nama hotel atau kota...">
                </div>
                
                <input type="hidden" name="city" value="<?= htmlspecialchars($city) ?>" />
                <input type="hidden" name="min_price" value="<?= htmlspecialchars($minPrice) ?>" />
                <input type="hidden" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>" />
                <input type="hidden" name="rating" value="<?= htmlspecialchars($rating) ?>" />
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>" />
                
                <input type="hidden" name="check_in" value="<?= htmlspecialchars($forwardParams['check_in']) ?>" />
                <input type="hidden" name="check_out" value="<?= htmlspecialchars($forwardParams['check_out']) ?>" />
                <input type="hidden" name="num_rooms" value="<?= htmlspecialchars($forwardParams['num_rooms']) ?>" />
                <input type="hidden" name="guest_adults" value="<?= htmlspecialchars($forwardParams['guest_adults']) ?>" />
                <input type="hidden" name="guest_children" value="<?= htmlspecialchars($forwardParams['guest_children']) ?>" />

                <?php 
                // Pertahankan array fasilitas
                if(isset($filters['facility']) && is_array($filters['facility'])) {
                    foreach ($filters['facility'] as $f) {
                        echo '<input type="hidden" name="facility[]" value="'.htmlspecialchars($f).'" />';
                    }
                }
                ?>
                
                <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">Cari</button>
            </form>
        </div>

        <?php if (empty($hotels)): ?>
            <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50 py-20 text-center">
                <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
                    <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Tidak ada hasil ditemukan</h3>
                <p class="text-slate-500">Coba ubah kata kunci atau kurangi filter pencarianmu.</p>
                <a href="<?= defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php' ?>" class="mt-6 rounded-full bg-white px-6 py-2 text-sm font-bold text-blue-600 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50">
                    Reset Filter
                </a>
            </div>
        <?php else: ?>
        
        <div class="grid gap-8 md:grid-cols-[240px_1fr] xl:gap-12">
            
            <aside class="relative">
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Filter</h2>
                            <a href="<?= defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php' ?>" class="text-xs font-semibold text-blue-600 hover:underline">Reset</a>
                        </div>
                        
                        <form id="filterForm" class="space-y-6" method="get" action="">
                            
                            <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>" />
                            <input type="hidden" name="check_in" value="<?= htmlspecialchars($forwardParams['check_in']) ?>" />
                            <input type="hidden" name="check_out" value="<?= htmlspecialchars($forwardParams['check_out']) ?>" />
                            <input type="hidden" name="num_rooms" value="<?= htmlspecialchars($forwardParams['num_rooms']) ?>" />
                            <input type="hidden" name="guest_adults" value="<?= htmlspecialchars($forwardParams['guest_adults']) ?>" />
                            <input type="hidden" name="guest_children" value="<?= htmlspecialchars($forwardParams['guest_children']) ?>" />
                            
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase text-slate-400">Urutkan</label>
                                <div class="relative">
                                    <select name="sort" onchange="document.getElementById('filterForm').submit()" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-700 focus:border-blue-500 focus:bg-white focus:ring-blue-500 cursor-pointer">
                                        <option value="recommended" <?= $sort === 'recommended' ? 'selected' : '' ?>>Rekomendasi</option>
                                        <option value="lowest-price" <?= $sort === 'lowest-price' ? 'selected' : '' ?>>Harga Terendah</option>
                                        <option value="highest-price" <?= $sort === 'highest-price' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                        <option value="highest-rating" <?= $sort === 'highest-rating' ? 'selected' : '' ?>>Rating Tertinggi</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase text-slate-400">Kota</label>
                                <select name="city" onchange="document.getElementById('filterForm').submit()" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <?php foreach($availableCities as $c): ?>
                                        <option value="<?= $c ?>" <?= $city === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase text-slate-400">Budget per Malam</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-1 block text-[10px] text-slate-500">Min</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-slate-400">Rp</span>
                                            <input type="number" name="min_price" value="<?= htmlspecialchars($minPrice) ?>" class="w-full rounded-lg border border-slate-200 py-1.5 pl-7 pr-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] text-slate-500">Max</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-slate-400">Rp</span>
                                            <input type="number" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>" class="w-full rounded-lg border border-slate-200 py-1.5 pl-7 pr-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Jutaan">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="mt-3 w-full rounded-lg bg-slate-100 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-200 transition">Terapkan Harga</button>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <label class="mb-3 block text-xs font-bold uppercase text-slate-400">Rating Bintang</label>
                                <div class="space-y-2">
                                    <?php 
                                    $ratings = ['5' => '5 Bintang', '4.5' => '4.5+', '4' => '4+'];
                                    foreach ($ratings as $val => $label): 
                                        $isChecked = ($rating === (string)$val);
                                    ?>
                                        <label class="flex cursor-pointer items-center gap-3">
                                            <input type="radio" name="rating" value="<?= $val ?>" <?= $isChecked ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()" class="h-4 w-4 cursor-pointer text-blue-600 focus:ring-blue-500" />
                                            <span class="text-sm text-slate-600"><?= $label ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                    
                                    <label class="flex cursor-pointer items-center gap-3">
                                        <input type="radio" name="rating" value="Semua Rating" <?= empty($rating) || $rating === 'Semua Rating' ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()" class="h-4 w-4 cursor-pointer text-slate-400 focus:ring-slate-500" />
                                        <span class="text-sm text-slate-400">Semua Rating</span>
                                    </label>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <label class="mb-3 block text-xs font-bold uppercase text-slate-400">Fasilitas</label>
                                <div class="space-y-2.5">
                                    <?php 
                                    $currentFacilities = $filters['facility'] ?? [];
                                    if (!is_array($currentFacilities)) $currentFacilities = [];
                                    
                                    foreach ($availableFacilities as $facility): 
                                    ?>
                                        <label class="group flex cursor-pointer items-center gap-3">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" name="facility[]" value="<?= htmlspecialchars($facility) ?>" <?= in_array($facility, $currentFacilities) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()" class="peer h-4 w-4 cursor-pointer appearance-none rounded border border-slate-300 bg-white transition-all checked:border-blue-600 checked:bg-blue-600 hover:border-blue-600" />
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
                </div>
            </aside>

            <div class="space-y-6">
                
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-600">Menampilkan <span class="font-bold text-slate-900"><?= $totalResults ?></span> properti</p>
                </div>

                <div class="grid gap-5">
                    <?php foreach ($hotels as $hotel): ?>
                        <?php 
                            $hotelId = $hotel['id'] ?? 0;
                            $hotelName = $hotel['name'] ?? 'Nama Hotel';
                            $hotelCity = $hotel['city'] ?? 'Indonesia';
                            $hotelRating = isset($hotel['average_rating']) ? number_format($hotel['average_rating'], 1) : '4.5';
                            $priceRaw = $hotel['min_price'] ?? 0;
                            
                            if ($priceRaw === null || $priceRaw == 0) {
                                $hotelPrice = "Cek Detail";
                                $perMalam = "";
                            } else {
                                $hotelPrice = 'IDR ' . number_format((float)$priceRaw, 0, ',', '.');
                                $perMalam = "/malam";
                            }

                            $hotelImage = !empty($hotel['main_image']) ? $hotel['main_image'] : BASE_URL . '/images/placeholder.jpg';
                            
                            $detailBase = defined('BASE_URL') ? BASE_URL . '/hotel/detail' : 'detail.php';
                            
                            $queryParams = [
                                'id' => $hotelId,
                                'check_in' => $forwardParams['check_in'],
                                'check_out' => $forwardParams['check_out'],
                                'num_rooms' => $forwardParams['num_rooms'],
                                'guest_adults' => $forwardParams['guest_adults'],
                                'guest_children' => $forwardParams['guest_children']
                            ];
                            $detailUrl = $detailBase . '?' . http_build_query($queryParams);
                        ?>
                        
                        <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:border-blue-200 hover:shadow-lg md:flex-row">
                            <div class="relative h-48 w-full shrink-0 overflow-hidden bg-slate-100 md:h-auto md:w-72 lg:w-80">
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
                            
                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 transition-colors group-hover:text-blue-600">
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
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Wifi Gratis</span>
                                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">AC</span>
                                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Layanan 24 Jam</span>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4">
                                    <div>
                                        <p class="text-xs font-medium text-slate-400">Mulai dari</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-xl font-bold text-slate-900"><?= htmlspecialchars($hotelPrice) ?></span>
                                            <span class="text-xs text-slate-500"><?= $perMalam ?></span>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <a href="<?= htmlspecialchars($detailUrl) ?>" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                            Detail
                                        </a>
                                        <a href="<?= htmlspecialchars($detailUrl) ?>#rooms" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md">
                                            Pilih Kamar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (isset($data['pagination'])): ?>
                    <div class="flex justify-center pt-8"></div>
                <?php endif; ?>
                
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
require __DIR__ . '/../layouts/footer.php';
?>