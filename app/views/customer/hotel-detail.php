<?php
$pageTitle = 'Trevio | Detail Hotel';
$hotel = $hotel ?? [
    'name' => 'Aurora Peaks Resort',
    'location' => 'Sapporo, Jepang',
    'rating' => 4.9,
    'reviews' => 428,
    'price' => 'JPY 32.000 / malam',
    'description' => 'Resor premium dengan panorama pegunungan Hokkaido, lounge observasi, onsen pribadi, dan pengalaman kuliner musiman. Trevio menjamin dukungan concierge 24 jam untuk setiap tamu.',
    'amenities' => ['Onsen pribadi', 'Sky lounge', 'Shuttle bandara', 'Spa terapi', 'Restoran Michelin', 'Wifi super cepat'],
    'images' => [
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1500&q=80',
        'https://images.unsplash.com/photo-1519821983755-6f5bbfc62a86?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=1200&q=80'
    ]
];
$rooms = $rooms ?? [
    [
        'name' => 'Premier Onsen Suite',
        'size' => '78 m²',
        'bed' => '1 King bed',
        'guests' => '2 dewasa',
        'price' => 'JPY 52.000 / malam',
        'inclusive' => ['Sarapan untuk 2 tamu', 'Akses onsen privat', 'Layanan butler 24 jam']
    ],
    [
        'name' => 'Skyline Panorama Room',
        'size' => '54 m²',
        'bed' => '1 King bed / 2 Twin',
        'guests' => '2 dewasa + 1 anak',
        'price' => 'JPY 38.500 / malam',
        'inclusive' => ['Sarapan buffet', 'Akses lounge', 'Diskon spa 20%']
    ],
    [
        'name' => 'Family Alpine Loft',
        'size' => '90 m²',
        'bed' => '2 King bed',
        'guests' => '4 dewasa + 2 anak',
        'price' => 'JPY 68.000 / malam',
        'inclusive' => ['Sarapan keluarga', 'Kids club', 'Guide ski privat']
    ],
];
require __DIR__ . '/partials/header.php';
?>
<section class="relative overflow-hidden">
    <div class="detail-gallery">
        <?php foreach ($hotel['images'] as $index => $image): ?>
            <div class="detail-image detail-image--<?= $index === 0 ? 'main' : 'side' ?>" style="background-image: url('<?= htmlspecialchars($image) ?>');"></div>
        <?php endforeach; ?>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 to-transparent"></div>
    <div class="relative mx-auto flex max-w-6xl flex-col gap-6 px-6 pb-16 pt-64 text-white">
        <nav class="text-xs text-white/70">
            <a class="hover:text-white" href="/">Beranda</a>
            <span class="mx-1">/</span>
            <a class="hover:text-white" href="/customer/search">Hotel</a>
            <span class="mx-1">/</span>
            <span><?= htmlspecialchars($hotel['name']) ?></span>
        </nav>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="space-y-2">
                <h1 class="text-4xl font-semibold"><?= htmlspecialchars($hotel['name']) ?></h1>
                <p class="text-sm text-white/80"><?= htmlspecialchars($hotel['location']) ?></p>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-3 py-1 font-semibold text-emerald-200">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 .587 15.668 8l8.2 1.193-5.934 5.781 1.402 8.174L12 18.896l-7.336 3.869 1.402-8.174L.132 9.193 8.332 8z"></path>
                        </svg>
                        <?= number_format($hotel['rating'], 1) ?>
                    </span>
                    <span class="text-white/80"><?= htmlspecialchars($hotel['reviews']) ?> ulasan</span>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs">Best pick Trevio</span>
                </div>
            </div>
            <div class="rounded-3xl border border-white/20 bg-white/10 px-6 py-4 text-right backdrop-blur">
                <p class="text-xs uppercase tracking-wide text-white/70">Harga mulai</p>
                <p class="text-lg font-semibold"><?= htmlspecialchars($hotel['price']) ?></p>
                <a class="mt-2 inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-primary transition hover:bg-blue-100" href="#rooms">Cek ketersediaan</a>
            </div>
        </div>
    </div>
</section>
<section class="bg-white py-16">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 lg:grid-cols-[2fr,1fr]">
        <article class="space-y-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="text-lg font-semibold text-primary">Tentang hotel</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600"><?= htmlspecialchars($hotel['description']) ?></p>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($hotel['amenities'] as $amenity): ?>
                        <span class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m5 12 5 5L20 7"></path>
                            </svg>
                            <?= htmlspecialchars($amenity) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="text-lg font-semibold text-primary">Highlight pengalaman</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Aurora Lounge</h3>
                        <p class="mt-2 text-sm text-slate-500">Nikmati panorama langit malam melalui dome kaca dengan live music setiap malam.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Private Onsen</h3>
                        <p class="mt-2 text-sm text-slate-500">Onsen outdoor hangat dengan view pegunungan, tersedia untuk pasangan maupun keluarga.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Culinary Journey</h3>
                        <p class="mt-2 text-sm text-slate-500">Chef bintang Michelin menghadirkan menu lokal musiman dan pairing sake curated.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Concierge aktif</h3>
                        <p class="mt-2 text-sm text-slate-500">Tim Trevio membantu itinerary ski, transportasi, hingga rekomendasi hidden gem.</p>
                    </div>
                </div>
            </div>
            <div class="space-y-5" id="rooms">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Ketersediaan kamar</p>
                        <h2 class="text-2xl font-semibold text-primary">Pilih tipe kamar</h2>
                    </div>
                    <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" type="button">Filter tanggal</button>
                </div>
                <div class="space-y-4">
                    <?php foreach ($rooms as $room): ?>
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-primary"><?= htmlspecialchars($room['name']) ?></h3>
                                    <p class="text-sm text-slate-500">Ukuran <?= htmlspecialchars($room['size']) ?> • <?= htmlspecialchars($room['bed']) ?> • Kapasitas <?= htmlspecialchars($room['guests']) ?></p>
                                    <ul class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
                                        <?php foreach ($room['inclusive'] as $include): ?>
                                            <li class="rounded-full bg-slate-100 px-3 py-1"><?= htmlspecialchars($include) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase tracking-wide text-slate-400">Harga per malam</p>
                                    <p class="text-base font-semibold text-primary"><?= htmlspecialchars($room['price']) ?></p>
                                    <a class="mt-3 inline-flex items-center justify-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" href="/customer/booking-form">Pesan Sekarang</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>
        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary">Ringkasan singkat</h3>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m12 6 4 8H8z"></path>
                        </svg>
                        Check-in 15:00 • Check-out 12:00
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2c3.5 0 6 2.5 6 7 0 6-6 13-6 13S6 15 6 9c0-4.5 2.5-7 6-7z"></path>
                            <circle cx="12" cy="9" r="2"></circle>
                        </svg>
                        20 menit dari Bandara Sapporo • Shuttle gratis
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 21h8"></path>
                            <path d="M12 17v4"></path>
                            <rect width="18" height="12" x="3" y="3" rx="2"></rect>
                        </svg>
                        Pembatalan gratis hingga 48 jam sebelum check-in
                    </li>
                </ul>
                <button class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full border border-accent px-4 py-2 text-sm font-semibold text-accent transition hover:bg-accent hover:text-white" type="button">
                    Chat dengan concierge
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21 15-5-5"></path>
                        <path d="M21 9v6h-6"></path>
                        <path d="M9 21 4 16"></path>
                        <path d="M9 21v-6H3"></path>
                    </svg>
                </button>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary">Lokasi</h3>
                <p class="mt-2 text-sm text-slate-500">Minami-ku, Sapporo, Hokkaido, Jepang</p>
                <div class="mt-4 h-48 overflow-hidden rounded-2xl bg-slate-100">
                    <iframe class="h-full w-full" src="https://maps.google.com/maps?q=Sapporo%20Japan&t=&z=13&ie=UTF8&iwloc=&output=embed" loading="lazy"></iframe>
                </div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary">Kebijakan penting</h3>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-slate-500">
                    <li>Deposit keamanan sebesar 10.000 JPY dibutuhkan saat check-in.</li>
                    <li>Tidak diperbolehkan merokok di kamar. Area merokok tersedia di lounge.</li>
                    <li>Hewan peliharaan diperbolehkan di kamar tertentu (hubungi concierge).</li>
                </ul>
            </div>
        </aside>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
