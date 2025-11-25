<?php
// Helper global untuk fungsi routing antar view.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// ==========================================================================
// LOGIC PENGAMBILAN DATA (INTEGRASI DATABASE)
// ==========================================================================

// Pastikan variabel $hotel tersedia (dikirim dari HotelController)
// Jika file ini diakses langsung tanpa melalui Controller, redirect ke halaman pencarian
if (!isset($hotel) || !is_array($hotel)) {
    $searchUrl = defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php';
    header("Location: " . $searchUrl);
    exit;
}

// --- 1. Normalisasi Data Hotel (DB -> View Structure) ---

// Siapkan array baru untuk view agar tidak mengubah variabel asli sembarangan
$viewHotel = $hotel;

// Lokasi: Gabungkan City dan Province
$city = $hotel['city'] ?? '';
$province = $hotel['province'] ?? '';
$viewHotel['location'] = $city . ($province ? ', ' . $province : '');

// Rating & Reviews
// Gunakan nilai default jika null
$viewHotel['rating'] = isset($hotel['average_rating']) ? (float)$hotel['average_rating'] : 4.5;
$viewHotel['reviews'] = isset($hotel['total_reviews']) ? (int)$hotel['total_reviews'] : 0;

// Amenities / Fasilitas (Mapping dari kolom 'facilities' di DB)
$viewHotel['amenities'] = [];
if (isset($hotel['facilities'])) {
    // Jika formatnya JSON string, decode dulu
    $viewHotel['amenities'] = is_string($hotel['facilities']) 
        ? (json_decode($hotel['facilities'], true) ?? []) 
        : $hotel['facilities'];
}

// Images
// Gunakan $galleryImages dari controller, atau fallback ke main_image
$viewHotel['images'] = isset($galleryImages) ? $galleryImages : [];
if (empty($viewHotel['images']) && !empty($hotel['main_image'])) {
    $viewHotel['images'][] = $hotel['main_image'];
}
// Fallback jika tidak ada gambar sama sekali
if (empty($viewHotel['images'])) {
    $viewHotel['images'][] = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
}

// --- 2. Normalisasi Data Kamar ---

$viewRooms = [];
$minPrice = PHP_INT_MAX; // Untuk mencari harga "Mulai dari"

if (!empty($hotel['rooms']) && is_array($hotel['rooms'])) {
    foreach ($hotel['rooms'] as $r) {
        $price = (float) $r['price_per_night'];
        
        // Cari harga terendah untuk ditampilkan di header
        if ($price < $minPrice) {
            $minPrice = $price;
        }

        // Parse amenities kamar
        $rInc = $r['amenities'] ?? [];
        if (is_string($rInc)) {
            $rInc = json_decode($rInc, true) ?? [];
        }

        // Struktur data kamar untuk view
        $viewRooms[] = [
            'id' => $r['id'], // Penting untuk link booking
            'name' => $r['room_type'],
            'size' => ($r['room_size'] ?? '0') . ' m²',
            'bed' => $r['bed_type'] ?? 'Standard Bed',
            'guests' => ($r['capacity'] ?? 2) . ' dewasa',
            'price_raw' => $price,
            'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.') . ' / malam',
            'inclusive' => array_slice((array)$rInc, 0, 3) // Ambil 3 fasilitas utama
        ];
    }
}

// Set harga hotel (mulai dari)
if ($minPrice === PHP_INT_MAX) $minPrice = 0;
$viewHotel['price'] = 'Rp ' . number_format($minPrice, 0, ',', '.') . ' / malam';

// Highlights untuk slider galeri
$galleryHighlightsData = isset($galleryHighlights) 
    ? $galleryHighlights 
    : array_slice($viewHotel['amenities'], 0, count($viewHotel['images']));

// Override variabel lama dengan data yang sudah dinormalisasi
$hotel = $viewHotel;
$rooms = $viewRooms;
$galleryHighlights = $galleryHighlightsData;

// Judul halaman
$pageTitle = 'Trevio | ' . $hotel['name'];

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

<section class="relative w-full bg-white">
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-0 grid grid-cols-1 md:grid-cols-5 gap-0 md:gap-8">
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
                        <h3 class="text-sm font-semibold text-primary">Fasilitas Lengkap</h3>
                        <p class="mt-2 text-sm text-slate-500">Menyediakan berbagai fasilitas terbaik untuk kenyamanan menginap Anda.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Lokasi Strategis</h3>
                        <p class="mt-2 text-sm text-slate-500">Akses mudah ke berbagai destinasi wisata dan pusat perbelanjaan.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Layanan 24 Jam</h3>
                        <p class="mt-2 text-sm text-slate-500">Staf profesional kami siap membantu kebutuhan Anda kapan saja.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-primary">Kebersihan Terjamin</h3>
                        <p class="mt-2 text-sm text-slate-500">Protokol kebersihan ketat untuk keamanan dan kesehatan tamu.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-5" id="rooms">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Ketersediaan kamar</p>
                        <h2 class="text-2xl font-semibold text-primary">Pilih tipe kamar</h2>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($rooms)): ?>
                        <div class="p-8 text-center text-gray-500 border border-dashed border-gray-300 rounded-xl">
                            Belum ada kamar yang tersedia untuk hotel ini.
                        </div>
                    <?php else: ?>
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
                                        <p class="text-base font-semibold text-primary"><?= htmlspecialchars($room['price_formatted']) ?></p>
                                        <?php
                                        // Link ke booking form dengan parameter ID yang valid
                                        $bookingUrl = defined('BASE_URL') 
                                            ? BASE_URL . '/booking/create?hotel_id=' . urlencode($hotel['id']) . '&room_id=' . urlencode($room['id'])
                                            : trevio_view_route('booking/form.php') . '?hotel_id=' . urlencode($hotel['id']) . '&room_id=' . urlencode($room['id']);
                                        ?>
                                        <a class="mt-3 inline-flex items-center justify-center rounded-full bg-accent px-5 py-2 text-sm font-semibold text-white transition hover:bg-accentLight" href="<?= htmlspecialchars($bookingUrl) ?>">Pesan Sekarang</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                        Lokasi Strategis
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 21h8"></path>
                            <path d="M12 17v4"></path>
                            <rect width="18" height="12" x="3" y="3" rx="2"></rect>
                        </svg>
                        Pembatalan gratis (S&K Berlaku)
                    </li>
                </ul>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary">Lokasi</h3>
                <p class="mt-2 text-sm text-slate-500"><?= htmlspecialchars($hotel['location']) ?></p>
                <div class="mt-4 h-48 overflow-hidden rounded-2xl bg-slate-100">
                    <iframe class="h-full w-full" src="https://maps.google.com/maps?q=<?= urlencode($hotel['location']) ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" loading="lazy" frameborder="0"></iframe>
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