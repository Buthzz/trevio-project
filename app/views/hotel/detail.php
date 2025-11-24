<?php
// Gunakan helper routing agar link antar view konsisten.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Data dummy lengkap untuk semua hotel
$hotelsDummy = [
    101 => [
        'id' => 101,
        'name' => 'Padma Hotel Bandung',
        'location' => 'Ciumbuleuit, Bandung',
        'rating' => 4.9,
        'reviews' => 1250,
        'price' => 'Rp 2.100.000 / malam',
        'description' => 'Hotel mewah dengan pemandangan lembah hijau yang menakjubkan. Nikmati udara sejuk Bandung dengan fasilitas kolam renang air hangat infinity dan layanan bintang lima.',
        'amenities' => ['Infinity Pool Air Hangat', 'Kids Club', 'Fitness Center', 'Restoran Pemandangan Alam', 'Wifi Cepat', 'Adventure Park'],
        'images' => [
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1573788708929-dbaf65667488?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3Dauto=format&fit=crop&w=1200&q=80', // Placeholder additional image
            'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80'  // Placeholder additional image
        ],
        'rooms' => [
            [
                'name' => 'Premier Room',
                'size' => '38 m²',
                'bed' => '1 King / 2 Twin',
                'guests' => '2 dewasa',
                'price' => 'Rp 2.100.000 / malam',
                'inclusive' => ['Sarapan Buffet', 'Afternoon Tea', 'Akses Kolam Renang']
            ],
            [
                'name' => 'Hillside Studio',
                'size' => '45 m²',
                'bed' => '1 King Bed',
                'guests' => '2 dewasa + 1 anak',
                'price' => 'Rp 3.500.000 / malam',
                'inclusive' => ['Sarapan', 'Balkon Pribadi', 'Minibar Gratis']
            ]
        ]
    ],
    102 => [
        'id' => 102,
        'name' => 'The Langham Jakarta',
        'location' => 'SCBD, Jakarta Selatan',
        'rating' => 4.8,
        'reviews' => 890,
        'price' => 'Rp 3.500.000 / malam',
        'description' => 'Epitome kemewahan Inggris di jantung Jakarta. Menawarkan pemandangan kota yang spektakuler, restoran kelas dunia, dan akses mudah ke pusat bisnis SCBD.',
        'amenities' => ['Sky Pool', 'Chuan Spa', 'Tom\'s by Tom Aikens', 'Ballroom', 'Concierge 24 Jam', 'Akses Mall'],
        'images' => [
            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
            'https://plus.unsplash.com/premium_photo-1683134297492-cce5fc6dae31?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D?auto=format&fit=crop&w=1200&q=80',
            'https://exquisite-taste-magazine.com/wp-content/uploads/2021/12/tljkt-dining-toms-by-1680-945.jpg?auto=format&fit=crop&w=1200&q=80'
        ],
        'rooms' => [
            [
                'name' => 'Deluxe City View',
                'size' => '45 m²',
                'bed' => '1 King Bed',
                'guests' => '2 dewasa',
                'price' => 'Rp 3.500.000 / malam',
                'inclusive' => ['Akses Gym', 'Wifi High Speed', 'Nespresso Machine']
            ],
            [
                'name' => 'Executive Club',
                'size' => '52 m²',
                'bed' => '1 King Bed',
                'guests' => '2 dewasa',
                'price' => 'Rp 5.200.000 / malam',
                'inclusive' => ['Akses Langham Club', 'Cocktail Hour', 'Meeting Room 2 Jam']
            ]
        ]
    ],
    103 => [
        'id' => 103,
        'name' => 'Amanjiwo Resort',
        'location' => 'Magelang, Yogyakarta',
        'rating' => 5.0,
        'reviews' => 450,
        'price' => 'Rp 8.500.000 / malam',
        'description' => 'Resor yang terinspirasi oleh Candi Borobudur, menawarkan ketenangan spiritual dan pemandangan langsung ke situs warisan dunia UNESCO.',
        'amenities' => ['Private Pool', 'Cultural Tour', 'Spa Tradisional', 'Library', 'Yoga Pavilion', 'Fine Dining'],
        'images' => [
            'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80'
        ],
        'rooms' => [
            [
                'name' => 'Garden Suite',
                'size' => '243 m²',
                'bed' => '1 King Bed',
                'guests' => '2 dewasa',
                'price' => 'Rp 8.500.000 / malam',
                'inclusive' => ['Antar Jemput Bandara', 'Sarapan A la Carte', 'Sesi Yoga']
            ],
            [
                'name' => 'Borobudur Pool Suite',
                'size' => '243 m²',
                'bed' => '1 King Bed',
                'guests' => '2 dewasa',
                'price' => 'Rp 12.000.000 / malam',
                'inclusive' => ['Private Pool', 'Pemandangan Borobudur', 'Butler Service']
            ]
        ]
    ],
    104 => [
        'id' => 104,
        'name' => 'The Apurva Kempinski',
        'location' => 'Nusa Dua, Bali',
        'rating' => 4.9,
        'reviews' => 2100,
        'price' => 'Rp 4.200.000 / malam',
        'description' => 'Teater terbuka yang megah di tebing Nusa Dua. Menawarkan pengalaman Bali yang otentik dengan arsitektur spektakuler dan pemandangan Samudra Hindia.',
        'amenities' => ['Koral Restaurant (Aquarium)', 'Pantai Pribadi', 'Spa Mewah', 'Chapel Pernikahan', 'Kolam Renang Luas', 'Kids Club'],
        'images' => [
            'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80'
        ],
        'rooms' => [
            [
                'name' => 'Grand Deluxe Room',
                'size' => '65 m²',
                'bed' => '1 King / 2 Twin',
                'guests' => '2 dewasa',
                'price' => 'Rp 4.200.000 / malam',
                'inclusive' => ['Sarapan Buffet', 'Akses Gym', 'Welcome Drink']
            ],
            [
                'name' => 'Cliff Private Pool Junior Suite',
                'size' => '100 m²',
                'bed' => '1 King Bed',
                'guests' => '2 dewasa',
                'price' => 'Rp 7.800.000 / malam',
                'inclusive' => ['Private Pool', 'Akses Cliff Lounge', 'Afternoon Tea']
            ]
        ]
    ],
];

// [BACKEND NOTE]: Logic untuk mengambil detail hotel berdasarkan ID dari URL
// Parameter 'id' dikirim dari home page atau search page melalui query string
// Contoh: hotel/detail.php?id=101
// Jika ID tidak ditemukan atau tidak valid, gunakan default hotel ID 101
$hotelId = isset($_GET['id']) ? intval($_GET['id']) : 101;

// [BACKEND NOTE]: Cek apakah hotel dengan ID tersebut ada di array dummy
// Jika tidak ada, redirect ke halaman 404 (untuk production, bisa redirect ke search page)
if (!isset($hotelsDummy[$hotelId])) {
    // TODO Backend: Ganti dengan redirect ke 404.php atau search.php
    // header('Location: ../errors/404.php');
    // exit;
    // Sementara gunakan default hotel
    $hotelId = 101;
}

// [BACKEND NOTE]: Ambil data hotel lengkap dari array dummy berdasarkan ID
// Untuk production: query ke database SELECT * FROM hotels WHERE id = $hotelId
$hotel = $hotelsDummy[$hotelId];

// [BACKEND NOTE]: Ambil daftar room/kamar yang tersedia di hotel ini
// Untuk production: query ke database SELECT * FROM rooms WHERE hotel_id = $hotelId
$rooms = $hotel['rooms'] ?? [];

// Judul halaman detail hotel untuk ditampilkan di browser tab.
$pageTitle = 'Trevio | ' . $hotel['name'];

// Ambil highlight singkat untuk ditampilkan di indikator slider galeri.
$galleryHighlights = array_slice($hotel['amenities'], 0, count($hotel['images']));


// Header khusus halaman hotel agar style tetap seragam.
require __DIR__ . '/../layouts/header.php';
?>
<style>
    /* Fallback styling untuk hero detail supaya foto dummy tampil rapi */
    .detail-hero {
        position: relative;
        height: 520px;
        overflow: hidden;
        border-bottom-left-radius: 32px;
        border-bottom-right-radius: 32px;
        background: #0f172a;
    }
    .detail-hero__slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.6s ease;
    }
    .detail-hero__slide.is-active {
        opacity: 1;
    }
    .detail-hero__badge {
        position: absolute;
        top: 24px;
        left: 24px;
        padding: 0.35rem 0.85rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #0f172a;
        background: rgba(255,255,255,0.9);
        border-radius: 999px;
    }
    .detail-hero__indicator {
        position: absolute;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: center;
        width: calc(100% - 64px);
    }
    .detail-hero__dot {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.15rem;
        padding: 0.5rem 0.75rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.4);
        background: rgba(15,23,42,0.55);
        color: rgba(255,255,255,0.8);
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .detail-hero__dot span {
        font-size: 0.75rem;
        font-weight: 600;
    }
    .detail-hero__dot.is-active {
        background: rgba(255,255,255,0.95);
        color: #0f172a;
        border-color: transparent;
    }
    @media (max-width: 640px) {
        .detail-hero {
            height: 420px;
            border-radius: 0 0 24px 24px;
        }
        .detail-hero__indicator {
            bottom: 16px;
            width: calc(100% - 32px);
        }
        .detail-hero__dot {
            padding: 0.4rem 0.6rem;
        }
    }
</style>
<!-- Hero detail hotel + slider foto -->

<!-- Hero foto dan info hotel, layout lebih modern dan responsif -->
<section class="relative w-full bg-white">
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-0 grid grid-cols-1 md:grid-cols-5 gap-0 md:gap-8">
        <!-- Foto slider -->
        <div class="md:col-span-3 flex flex-col justify-center">
            <div class="detail-hero" data-detail-gallery>
                <?php foreach ($hotel['images'] as $index => $image): ?>
                    <div class="detail-hero__slide <?= $index === 0 ? 'is-active' : '' ?>" data-gallery-slide="<?= $index ?>" style="background-image: url('<?= htmlspecialchars($image) ?>');">
                        <div class="detail-hero__badge">Foto <?= $index + 1 ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="detail-hero__indicator" data-gallery-dots>
                    <?php foreach ($hotel['images'] as $index => $image): ?>
                        <?php $dotLabel = $galleryHighlights[$index] ?? 'Preview ' . ($index + 1); ?>
                        <button type="button" class="detail-hero__dot <?= $index === 0 ? 'is-active' : '' ?>" data-gallery-target="<?= $index ?>">
                            <span><?= sprintf('%02d', $index + 1) ?></span>
                            <small><?= htmlspecialchars($dotLabel) ?></small>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Info utama hotel -->
        <div class="md:col-span-2 flex flex-col justify-center items-start md:items-start pt-8 md:pt-0">
            <nav class="mb-2 text-xs text-slate-400">
                <a class="hover:text-primary" href="<?= htmlspecialchars(trevio_view_route('home/index.php')) ?>">Beranda</a>
                <span class="mx-1">/</span>
                <a class="hover:text-primary" href="<?= htmlspecialchars(trevio_view_route('hotel/search.php')) ?>">Hotel</a>
                <span class="mx-1">/</span>
                <span class="font-semibold text-primary"><?= htmlspecialchars($hotel['name']) ?></span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold text-primary leading-tight mb-2"><?= htmlspecialchars($hotel['name']) ?></h1>
            <p class="text-base text-slate-500 mb-3 flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500 inline-block" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 .587 15.668 8l8.2 1.193-5.934 5.781 1.402 8.174L12 18.896l-7.336 3.869 1.402-8.174L.132 9.193 8.332 8z"></path>
                </svg>
                <span class="font-semibold text-emerald-600"> <?= number_format($hotel['rating'], 1) ?> </span>
                <span class="text-slate-400">/ <?= htmlspecialchars($hotel['reviews']) ?> ulasan</span>
            </p>
            <p class="text-sm text-slate-400 mb-4"><span class="font-medium">Lokasi:</span> <?= htmlspecialchars($hotel['location']) ?></p>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 mb-2 w-full">
                <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Harga mulai</p>
                <p class="text-xl font-bold text-primary"><?= htmlspecialchars($hotel['price']) ?></p>
                <a class="mt-3 inline-flex items-center justify-center rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-600 w-full" href="#rooms">Cek ketersediaan</a>
            </div>
        </div>
    </div>
</section>
<!-- Section informasi kamar dan sidebar booking -->
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
                                    <?php
                                    // [BACKEND NOTE]: Link ke booking form dengan membawa data hotel dan room
                                    // Parameter yang dikirim: hotel_id, room_name, room_price
                                    // Data ini akan digunakan untuk pre-fill form pemesanan
                                    $bookingUrl = trevio_view_route('booking/form.php') . 
                                                  '?hotel_id=' . urlencode($hotel['id']) . 
                                                  '&room_name=' . urlencode($room['name']) .
                                                  '&room_price=' . urlencode($room['price']);
                                    ?>
                                    <a class="mt-3 inline-flex items-center justify-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" href="<?= htmlspecialchars($bookingUrl) ?>">Pesan Sekarang</a>
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
                        20 menit dari Bandara • Shuttle gratis
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
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary">Lokasi</h3>
                <p class="mt-2 text-sm text-slate-500"><?= htmlspecialchars($hotel['location']) ?></p>
                <div class="mt-4 h-48 overflow-hidden rounded-2xl bg-slate-100">
                    <iframe class="h-full w-full" src="https://maps.google.com/maps?q=<?= urlencode($hotel['location']) ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" loading="lazy"></iframe>
                </div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary">Kebijakan penting</h3>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-slate-500">
                    <li>Deposit keamanan dibutuhkan saat check-in.</li>
                    <li>Tidak diperbolehkan merokok di kamar. Area merokok tersedia di lounge.</li>
                    <li>Hewan peliharaan diperbolehkan di kamar tertentu (hubungi concierge).</li>
                </ul>
            </div>
        </aside>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Kendalikan rotasi foto hero agar pengguna bisa melihat ringkasan dari tiga gambar.
    const gallery = document.querySelector('[data-detail-gallery]');
    if (!gallery) {
        return;
    }
    const slides = Array.from(gallery.querySelectorAll('[data-gallery-slide]'));
    const dots = Array.from(gallery.querySelectorAll('[data-gallery-target]'));
    let activeIndex = 0;
    let autoTimer = null;

    const setActive = function (index) {
        activeIndex = index;
        slides.forEach(function (slide, idx) {
            slide.classList.toggle('is-active', idx === index);
        });
        dots.forEach(function (dot, idx) {
            dot.classList.toggle('is-active', idx === index);
        });
    };

    const startAutoRotate = function () {
        stopAutoRotate();
        autoTimer = setInterval(function () {
            const nextIndex = (activeIndex + 1) % slides.length;
            setActive(nextIndex);
        }, 8000);
    };

    const stopAutoRotate = function () {
        if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
    };

    dots.forEach(function (dot) {
        const targetIndex = parseInt(dot.getAttribute('data-gallery-target'), 10);
        dot.addEventListener('click', function () {
            setActive(targetIndex);
            startAutoRotate();
        });
    });

    gallery.addEventListener('mouseenter', stopAutoRotate);
    gallery.addEventListener('mouseleave', startAutoRotate);

    setActive(activeIndex);
    startAutoRotate();
});
</script>
<?php
// Footer khusus hotel untuk menutup konten.
require __DIR__ . '/../layouts/footer.php';
?>