<?php
// Helper global
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Validasi Data
if (!isset($data['hotel'])) {
    header("Location: " . BASE_URL . "/hotel/search");
    exit;
}

$hotel = $data['hotel'];
$searchParams = $data['searchParams'] ?? [
    'check_in' => date('Y-m-d'),
    'check_out' => date('Y-m-d', strtotime('+1 day')),
    'nights' => 1,
    'num_rooms' => 1,
    'guests' => '2 Tamu'
];

// Format Data untuk Tampilan
$duration = $searchParams['nights'];
$roomCount = $searchParams['num_rooms'];
$checkInStr = date('d M', strtotime($searchParams['check_in']));
$checkOutStr = date('d M Y', strtotime($searchParams['check_out']));

// Normalisasi Data Hotel
$city = $hotel['city'] ?? '';
$province = $hotel['province'] ?? '';
$locationStr = $city . ($province ? ', ' . $province : '');
$rating = number_format($hotel['average_rating'] ?? 0, 1);
$reviews = $hotel['total_reviews'] ?? 0;
$amenities = is_string($hotel['facilities'] ?? '') ? json_decode($hotel['facilities'], true) : ($hotel['facilities'] ?? []);
if (!is_array($amenities)) $amenities = [];

// Gambar Galeri
$galleryImages = $data['galleryImages'] ?? [];
if (empty($galleryImages)) $galleryImages[] = BASE_URL . '/images/placeholder.jpg';

// Highlights (untuk dot slider)
$galleryHighlights = array_slice($amenities, 0, count($galleryImages));

$pageTitle = 'Trevio | ' . $hotel['name'];
require __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Style Original */
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
                <?php foreach ($galleryImages as $index => $image): ?>
                    <div class="detail-hero__slide <?= $index === 0 ? 'is-active' : '' ?>" data-gallery-slide="<?= $index ?>" style="background-image: url('<?= htmlspecialchars($image) ?>');">
                        <div class="detail-hero__badge">Foto <?= $index + 1 ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="detail-hero__indicator" data-gallery-dots>
                    <?php foreach ($galleryImages as $index => $image): ?>
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
                <a class="hover:text-blue-600" href="<?= BASE_URL ?>">Beranda</a>
                <span class="mx-1">/</span>
                <a class="hover:text-blue-600" href="<?= BASE_URL ?>/hotel/search">Hotel</a>
                <span class="mx-1">/</span>
                <span class="font-semibold text-blue-600"><?= htmlspecialchars($hotel['name']) ?></span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 leading-tight mb-2"><?= htmlspecialchars($hotel['name']) ?></h1>
            <p class="text-base text-slate-500 mb-3 flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500 inline-block" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 .587 15.668 8l8.2 1.193-5.934 5.781 1.402 8.174L12 18.896l-7.336 3.869 1.402-8.174L.132 9.193 8.332 8z"></path>
                </svg>
                <span class="font-semibold text-emerald-600"><?= $rating ?></span>
                <span class="text-slate-400">/ <?= $reviews ?> ulasan</span>
            </p>
            <p class="text-sm text-slate-400 mb-4"><span class="font-medium">Lokasi:</span> <?= htmlspecialchars($locationStr) ?></p>
            
            <div class="rounded-2xl border border-blue-100 bg-blue-50/50 px-5 py-4 mb-2 w-full">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs uppercase tracking-wide text-blue-500 font-bold">Pencarianmu</p>
                    <a href="<?= BASE_URL ?>/hotel/search?q=<?= urlencode($city) ?>" class="text-xs font-bold text-blue-600 underline">Ubah</a>
                </div>
                <p class="text-sm font-semibold text-slate-700">
                    <?= $checkInStr ?> - <?= $checkOutStr ?> 
                    <span class="font-normal text-slate-500">(<?= $duration ?> Malam)</span>
                </p>
                <p class="text-sm text-slate-500 mt-1"><?= $searchParams['guests'] ?>, <?= $roomCount ?> Kamar</p>
                
                <a class="mt-4 inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 w-full shadow-lg shadow-blue-500/30" href="#rooms">
                    Lihat Ketersediaan
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 md:grid-cols-[300px_1fr]">
        
        <aside class="relative">
            <div class="sticky top-24 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Ringkasan singkat</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Check-in 14:00 • Check-out 12:00
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            Lokasi Strategis
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pembatalan gratis (S&K)
                        </li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Lokasi</h3>
                    <p class="mt-2 text-sm text-slate-500"><?= htmlspecialchars($locationStr) ?></p>
                    <div class="mt-4 h-48 overflow-hidden rounded-2xl bg-slate-100">
                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">Peta Google Maps</div>
                    </div>
                </div>
            </div>
        </aside>

        <article class="space-y-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Tentang hotel</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600"><?= nl2br(htmlspecialchars($hotel['description'] ?? 'Deskripsi hotel belum tersedia.')) ?></p>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($amenities as $amenity): ?>
                        <span class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="h-4 w-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"></path></svg>
                            <?= htmlspecialchars($amenity) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="space-y-5" id="rooms">
                <div class="flex flex-col gap-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Pilihan Kamar</p>
                    <h2 class="text-2xl font-semibold text-slate-900">Ketersediaan untuk <?= $duration ?> malam</h2>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($hotel['rooms'])): ?>
                        <div class="p-8 text-center text-gray-500 border-2 border-dashed border-gray-200 rounded-3xl bg-slate-50">
                            Belum ada kamar yang tersedia untuk tanggal ini.
                        </div>
                    <?php else: ?>
                        <?php foreach ($hotel['rooms'] as $room): ?>
                            <?php 
                                // LOGIC BARU: Ambil data hasil kalkulasi dari Controller
                                $searchData = $room['search_data'] ?? [
                                    'is_available' => true,
                                    'total_price' => $room['price_per_night'],
                                    'remaining_slots' => 5,
                                    'capacity_warning' => false
                                ];
                                
                                $isAvailable = $searchData['is_available'];
                                $totalPrice = $searchData['total_price'];
                                $perNightPrice = $room['price_per_night'];
                                $remaining = $searchData['remaining_slots'];

                                // Amenities
                                $rInc = is_string($room['amenities'] ?? '') ? json_decode($room['amenities'], true) : ($room['amenities'] ?? []);
                                if (!is_array($rInc)) $rInc = ['Wifi', 'AC'];

                                // Link Booking
                                $bookParams = [
                                    'hotel_id' => $hotel['id'],
                                    'room_id' => $room['id'],
                                    'check_in' => $searchParams['check_in'],
                                    'check_out' => $searchParams['check_out'],
                                    'num_rooms' => $searchParams['num_rooms'],
                                    'guests' => $searchParams['guests']
                                ];
                                $bookingUrl = BASE_URL . '/booking/create?' . http_build_query($bookParams);
                            ?>

                            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md <?= !$isAvailable ? 'opacity-75 bg-slate-50' : '' ?>">
                                <div class="flex flex-col gap-6 md:flex-row md:items-center">
                                    <div class="w-full md:w-48 h-32 bg-slate-200 rounded-2xl overflow-hidden shrink-0 relative">
                                        <img src="<?= !empty($room['main_image']) ? htmlspecialchars($room['main_image']) : BASE_URL.'/images/placeholder.jpg' ?>" class="w-full h-full object-cover">
                                        <?php if (!$isAvailable): ?>
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">HABIS</span>
                                            </div>
                                        <?php elseif ($remaining <= 3): ?>
                                            <div class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded">Sisa <?= $remaining ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($room['room_type']) ?></h3>
                                        <p class="text-sm text-slate-500 mt-1">
                                            Ukuran <?= htmlspecialchars($room['room_size'] ?? '-') ?> m² • 
                                            Max <?= htmlspecialchars($room['capacity'] ?? 2) ?> Org
                                            <?php if ($searchData['capacity_warning']): ?>
                                                <span class="text-orange-500 text-xs font-bold ml-1">(Kapasitas Pas-pasan)</span>
                                            <?php endif; ?>
                                        </p>
                                        <ul class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
                                            <?php foreach (array_slice($rInc, 0, 3) as $inc): ?>
                                                <li class="rounded-full bg-slate-100 px-3 py-1"><?= htmlspecialchars($inc) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <div class="text-left md:text-right shrink-0">
                                        <?php if ($duration > 1 || $roomCount > 1): ?>
                                            <p class="text-xs uppercase tracking-wide text-slate-400">Total Harga</p>
                                            <p class="text-xl font-bold text-blue-600">Rp <?= number_format($totalPrice, 0, ',', '.') ?></p>
                                            <p class="text-[10px] text-slate-400">Rp <?= number_format($perNightPrice, 0, ',', '.') ?> /malam</p>
                                        <?php else: ?>
                                            <p class="text-xs uppercase tracking-wide text-slate-400">Harga per malam</p>
                                            <p class="text-xl font-bold text-blue-600">Rp <?= number_format($perNightPrice, 0, ',', '.') ?></p>
                                        <?php endif; ?>

                                        <?php if ($isAvailable): ?>
                                            <a class="mt-3 inline-flex w-full md:w-auto items-center justify-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 shadow-lg shadow-blue-500/20" href="<?= $bookingUrl ?>">
                                                Pilih
                                            </a>
                                        <?php else: ?>
                                            <button disabled class="mt-3 inline-flex w-full md:w-auto items-center justify-center rounded-xl bg-slate-200 px-6 py-2.5 text-sm font-bold text-slate-400 cursor-not-allowed">
                                                Penuh
                                            </button>
                                        <?php endif; ?>
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
    // Slider Logic Original
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
            const nextIndex = (activeIndex + 1) % slides.length;
            setActive(nextIndex);
        }, 5000);
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>