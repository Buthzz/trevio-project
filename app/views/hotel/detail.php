<?php
// Helper global untuk fungsi routing antar view.
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Pastikan variabel $hotel tersedia
if (!isset($hotel) || !is_array($hotel)) {
    $searchUrl = defined('BASE_URL') ? BASE_URL . '/hotel/search' : 'search.php';
    header("Location: " . $searchUrl);
    exit;
}

// [FIX FLOW]: Ambil parameter pencarian dari URL (agar tanggal & tamu terbawa ke booking)
$queryParams = $_GET;
$bookingParams = [
    'check_in' => $queryParams['check_in'] ?? '',
    'check_out' => $queryParams['check_out'] ?? '',
    'guests' => $queryParams['guests'] ?? '',
    'num_rooms' => $queryParams['num_rooms'] ?? ''
];
$bookingQueryString = http_build_query(array_filter($bookingParams));

// --- 1. Normalisasi Data Hotel ---
$viewHotel = $hotel;
$city = $hotel['city'] ?? '';
$province = $hotel['province'] ?? '';
$viewHotel['location'] = $city . ($province ? ', ' . $province : '');
$viewHotel['rating'] = isset($hotel['average_rating']) ? (float)$hotel['average_rating'] : 4.5;
$viewHotel['reviews'] = isset($hotel['total_reviews']) ? (int)$hotel['total_reviews'] : 0;

$viewHotel['amenities'] = [];
if (isset($hotel['facilities'])) {
    $viewHotel['amenities'] = is_string($hotel['facilities']) 
        ? (json_decode($hotel['facilities'], true) ?? []) 
        : $hotel['facilities'];
}

$viewHotel['images'] = isset($galleryImages) ? $galleryImages : [];
if (empty($viewHotel['images']) && !empty($hotel['main_image'])) {
    $viewHotel['images'][] = $hotel['main_image'];
}
if (empty($viewHotel['images'])) {
    $viewHotel['images'][] = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
}

// --- 2. Normalisasi Data Kamar ---
$viewRooms = [];
$minPrice = PHP_INT_MAX; 

if (!empty($hotel['rooms']) && is_array($hotel['rooms'])) {
    foreach ($hotel['rooms'] as $r) {
        $price = (float) $r['price_per_night'];
        if ($price < $minPrice) {
            $minPrice = $price;
        }

        $rInc = $r['amenities'] ?? [];
        if (is_string($rInc)) {
            $rInc = json_decode($rInc, true) ?? [];
        }

        $viewRooms[] = [
            'id' => $r['id'],
            'name' => $r['room_type'],
            'size' => ($r['room_size'] ?? '0') . ' m²',
            'bed' => $r['bed_type'] ?? 'Standard Bed',
            'guests' => ($r['capacity'] ?? 2) . ' dewasa',
            'price_raw' => $price,
            'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.') . ' / malam',
            'inclusive' => array_slice((array)$rInc, 0, 3)
        ];
    }
}

if ($minPrice === PHP_INT_MAX) $minPrice = 0;
$viewHotel['price'] = 'Rp ' . number_format($minPrice, 0, ',', '.') . ' / malam';

$galleryHighlightsData = isset($galleryHighlights) 
    ? $galleryHighlights 
    : array_slice($viewHotel['amenities'], 0, count($viewHotel['images']));

$hotel = $viewHotel;
$rooms = $viewRooms;
$galleryHighlights = $galleryHighlightsData;
$pageTitle = 'Trevio | ' . $hotel['name'];

require __DIR__ . '/../layouts/header.php';
?>

<style>
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
        bottom: 32px;
        left: 50%;
        transform: translateX(-50%);
        display: inline-flex;
        align-items: center;
        gap: 16px;
        padding: 10px 16px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(12px);
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        z-index: 10;
    }
    .detail-hero__dot {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .detail-hero__dot span {
        font-size: 16px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .detail-hero__dot small {
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        max-width: 90%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        opacity: 0.9;
    }
    .detail-hero__dot:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.5);
        color: #fff;
        transform: translateY(-2px);
    }
    .detail-hero__dot.is-active {
        background: #ffffff;
        color: #0f172a;
        border-color: #ffffff;
        transform: scale(1.15);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        z-index: 1;
    }
    @media (max-width: 640px) {
        .detail-hero {
            height: 400px;
            border-radius: 0 0 24px 24px;
        }
        .detail-hero__indicator {
            bottom: 20px;
            padding: 6px 10px;
            gap: 8px;
            width: auto;
            max-width: 95%;
        }
        .detail-hero__dot {
            width: 52px;
            height: 52px;
        }
        .detail-hero__dot span {
            font-size: 13px;
            margin-bottom: 0;
        }
        .detail-hero__dot small {
            font-size: 7px;
            max-width: 100%;
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
    <div class="mx-auto grid max-w-6xl gap-10 px-6 md:grid-cols-[300px_1fr]">
        <aside class="relative">
            <div class="sticky top-24 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-primary">Ringkasan singkat</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center gap-2">Check-in 15:00 • Check-out 12:00</li>
                        <li class="flex items-center gap-2">Lokasi Strategis</li>
                        <li class="flex items-center gap-2">Pembatalan gratis (S&K Berlaku)</li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-primary">Lokasi</h3>
                    <p class="mt-2 text-sm text-slate-500"><?= htmlspecialchars($hotel['location']) ?></p>
                    <div class="mt-4 h-48 overflow-hidden rounded-2xl bg-slate-100">
                        <iframe class="h-full w-full" src="https://maps.google.com/maps?q=<?= urlencode($hotel['location']) ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" loading="lazy" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </aside>

        <article class="space-y-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="text-lg font-semibold text-primary">Tentang hotel</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600"><?= htmlspecialchars($hotel['description']) ?></p>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($hotel['amenities'] as $amenity): ?>
                        <span class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="h-4 w-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"></path></svg>
                            <?= htmlspecialchars($amenity) ?>
                        </span>
                    <?php endforeach; ?>
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
                                        // [FIX FLOW]: Tambahkan parameter pencarian ke link Booking
                                        $bookingBase = defined('BASE_URL') 
                                            ? BASE_URL . '/booking/create'
                                            : trevio_view_route('booking/form.php');
                                        
                                        $bookingUrl = $bookingBase . '?hotel_id=' . urlencode($hotel['id']) . '&room_id=' . urlencode($room['id']);
                                        if ($bookingQueryString) {
                                            $bookingUrl .= '&' . $bookingQueryString;
                                        }
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
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const gallery = document.querySelector('[data-detail-gallery]');
    if (!gallery) return;
    
    const slides = Array.from(gallery.querySelectorAll('[data-gallery-slide]'));
    const dots = Array.from(gallery.querySelectorAll('[data-gallery-target]'));
    let activeIndex = 0;
    let autoTimer = null;

    const setActive = function (index) {
        activeIndex = index;
        slides.forEach((slide, idx) => slide.classList.toggle('is-active', idx === index));
        dots.forEach((dot, idx) => dot.classList.toggle('is-active', idx === index));
    };

    const startAutoRotate = function () {
        stopAutoRotate();
        autoTimer = setInterval(() => {
            setActive((activeIndex + 1) % slides.length);
        }, 8000);
    };

    const stopAutoRotate = function () {
        if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    };

    dots.forEach(dot => {
        dot.addEventListener('click', function () {
            setActive(parseInt(dot.getAttribute('data-gallery-target'), 10));
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
require __DIR__ . '/../layouts/footer.php';
?>