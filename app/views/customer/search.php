<?php
$pageTitle = 'Trevio | Cari & Filter Hotel';
$filters = $filters ?? [
    'query' => $_GET['q'] ?? '',
    'city' => $_GET['city'] ?? 'Semua Kota',
    'price' => $_GET['price'] ?? 'Semua Harga',
    'rating' => $_GET['rating'] ?? '4+',
];
$availableFilters = [
    'city' => ['Semua Kota', 'Jakarta', 'Bali', 'Bandung', 'Yogyakarta', 'Surabaya'],
    'price' => ['Semua Harga', '< 1 juta', '1 - 2 juta', '2 - 3 juta', '> 3 juta'],
    'rating' => ['Semua Rating', '4+', '4.5+', '5'],
    'facility' => ['Kolam Renang', 'Spa', 'Parkir Gratis', 'Wi-Fi', 'Breakfast']
];
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
require __DIR__ . '/partials/header.php';
?>
<section class="bg-slate-100/70 py-16">
    <div class="mx-auto max-w-6xl space-y-8 px-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-xl space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Cari Hotel</p>
                <h1 class="text-3xl font-semibold text-primary">Temukan hotel terbaik sesuai preferensi kamu</h1>
                <p class="text-sm text-slate-500">Gunakan filter fasilitas, rating, dan rentang harga untuk menemukan hotel yang paling cocok.</p>
            </div>
            <form class="flex w-full flex-col gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:px-6" method="get">
                <div class="flex flex-1 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input class="h-11 w-full border-0 bg-transparent text-sm focus:outline-none" name="q" value="<?= htmlspecialchars($filters['query']) ?>" placeholder="Cari kota atau hotel" />
                </div>
                <button class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-accentLight" type="submit">Cari</button>
            </form>
        </div>
        <div class="grid gap-8 lg:grid-cols-[280px,1fr]">
            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold text-primary">Filter</h2>
                    <form class="mt-4 space-y-5" method="get">
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
                                        <input class="size-4 rounded border-slate-300 text-accent focus:ring-accent" type="checkbox" value="<?= htmlspecialchars($facility) ?>" />
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
            </aside>
            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Ditemukan <span class="font-semibold text-primary"><?= count($searchResults) ?></span> hotel sesuai pencarian.</p>
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                        <span class="text-slate-500">Urutkan:</span>
                        <select class="border-0 bg-transparent text-sm font-semibold text-primary focus:outline-none">
                            <option value="recommended">Rekomendasi</option>
                            <option value="lowest-price">Harga Terendah</option>
                            <option value="highest-rating">Rating Tertinggi</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-4">
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
                                    <div class="flex gap-3">
                                        <button class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" type="button">Simpan</button>
                                        <a class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" href="/customer/hotel-detail">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
