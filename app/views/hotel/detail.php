<?php
// Helper routing & Session
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// Validasi Data dari Controller
if (!isset($data['hotel'])) {
    // Redirect jika diakses langsung tanpa lewat Controller
    header("Location: " . (defined('BASE_URL') ? BASE_URL : '/trevio-project/public') . "/hotel/search");
    exit;
}

$hotel = $data['hotel'];
// Ambil parameter pencarian dari controller (default values handled di controller)
$searchParams = $data['searchParams'] ?? [
    'check_in' => date('Y-m-d'),
    'check_out' => date('Y-m-d', strtotime('+1 day')),
    'nights' => 1,
    'num_rooms' => 1,
    'guests' => '2 Tamu'
];

// Format Tampilan Tanggal
$checkInDisplay = date('d M Y', strtotime($searchParams['check_in']));
$checkOutDisplay = date('d M Y', strtotime($searchParams['check_out']));
$duration = $searchParams['nights'];
$roomCount = $searchParams['num_rooms'];

// Normalisasi Data Hotel (Handling null values)
$city = $hotel['city'] ?? 'Indonesia';
$hotelRating = number_format((float)($hotel['average_rating'] ?? 0), 1);
$hotelReviews = $hotel['total_reviews'] ?? 0;
$description = $hotel['description'] ?? 'Deskripsi belum tersedia.';

// Normalisasi Fasilitas (JSON to Array)
$amenities = is_string($hotel['facilities'] ?? '') 
    ? json_decode($hotel['facilities'], true) 
    : ($hotel['facilities'] ?? []);
if (!is_array($amenities)) $amenities = ['Wifi', 'Parkir', 'Resepsionis 24 Jam'];

// Gambar Galeri
$galleryImages = $data['galleryImages'] ?? [];
if (empty($galleryImages)) {
    $galleryImages[] = BASE_URL . '/images/placeholder.jpg';
}

$pageTitle = 'Trevio | ' . ($hotel['name'] ?? 'Detail Hotel');

require __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Styles Khusus Halaman Detail */
    .detail-hero {
        position: relative;
        height: 480px;
        border-radius: 0 0 32px 32px;
        overflow: hidden;
        background: #0f172a;
    }
    .detail-hero__slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.7s ease;
    }
    .detail-hero__slide.is-active {
        opacity: 1;
    }
    .text-shadow {
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
</style>

<section class="relative w-full bg-slate-50 pb-12">
    <div class="mx-auto max-w-7xl px-4 lg:px-6 pt-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 relative h-[400px] lg:h-[500px] rounded-3xl overflow-hidden shadow-xl group">
                <div class="detail-hero" data-detail-gallery>
                    <?php foreach ($galleryImages as $index => $img): ?>
                        <div class="detail-hero__slide <?= $index === 0 ? 'is-active' : '' ?>" 
                             data-slide="<?= $index ?>" 
                             style="background-image: url('<?= htmlspecialchars($img) ?>');">
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($galleryImages) > 1): ?>
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 px-4 py-2 bg-black/30 backdrop-blur-md rounded-full z-10">
                    <?php foreach ($galleryImages as $index => $img): ?>
                        <button type="button" 
                                class="w-2.5 h-2.5 rounded-full transition-all bg-white/50 hover:bg-white <?= $index === 0 ? 'bg-white scale-125' : '' ?>" 
                                onclick="changeSlide(<?= $index ?>)">
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="absolute top-6 left-6 z-20">
                    <a href="<?= BASE_URL ?>/hotel/search?q=<?= urlencode($city) ?>" 
                       class="flex items-center gap-2 px-4 py-2 bg-white/90 backdrop-blur rounded-full text-sm font-bold text-slate-800 hover:bg-white transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="flex flex-col justify-center lg:py-4">
                <div class="mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-3">
                        <?= htmlspecialchars($city) ?>
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight mb-3">
                        <?= htmlspecialchars($hotel['name']) ?>
                    </h1>
                    
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-6">
                        <div class="flex items-center gap-1 text-yellow-500 font-bold">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span><?= $hotelRating ?></span>
                        </div>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span><?= $hotelReviews ?> Ulasan</span>
                    </div>

                    <div class="bg-white border border-blue-100 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-bl-full -mr-4 -mt-4"></div>
                        
                        <div class="flex justify-between items-center mb-4 relative z-10">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Detail Perjalananmu</h3>
                            <a href="<?= BASE_URL ?>/hotel/search?q=<?= urlencode($city) ?>" class="text-xs font-bold text-blue-600 hover:text-blue-700">Ubah</a>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm relative z-10">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Check-In</p>
                                <p class="font-bold text-slate-800"><?= $checkInDisplay ?></p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Check-Out</p>
                                <p class="font-bold text-slate-800"><?= $checkOutDisplay ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-slate-600 relative z-10">
                            <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg font-bold">
                                <?= $duration ?> Malam
                            </span>
                            <span class="bg-slate-100 px-3 py-1.5 rounded-lg">
                                <?= $roomCount ?> Kamar
                            </span>
                            <span class="bg-slate-100 px-3 py-1.5 rounded-lg">
                                <?= htmlspecialchars($searchParams['guests']) ?>
                            </span>
                        </div>
                    </div>
                    </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12" id="rooms">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-12">
            
            <div class="space-y-10">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-100 pb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Pilihan Kamar</h2>
                        <p class="text-slate-500 text-sm mt-1">
                            Menampilkan harga total untuk 
                            <span class="font-bold text-slate-800"><?= $duration ?> malam</span>, 
                            <span class="font-bold text-slate-800"><?= $roomCount ?> kamar</span>
                        </p>
                    </div>
                </div>

                <?php if (empty($hotel['rooms'])): ?>
                    <div class="text-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <p class="text-slate-500 font-medium">Belum ada kamar yang tersedia saat ini.</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6">
                        <?php foreach ($hotel['rooms'] as $room): ?>
                            <?php 
                                // Logic Data dari Controller
                                $searchData = $room['search_data'] ?? [
                                    'is_available' => true,
                                    'total_price' => $room['price_per_night'],
                                    'remaining_slots' => 5,
                                    'capacity_warning' => false
                                ];
                                
                                $isAvailable = $searchData['is_available'];
                                $totalPrice = $searchData['total_price'];
                                $perNightPrice = $room['price_per_night'];
                                $remainingSlots = $searchData['remaining_slots'];

                                // Amenities
                                $roomAmenities = is_string($room['amenities'] ?? '') 
                                    ? json_decode($room['amenities'], true) 
                                    : ($room['amenities'] ?? []);
                                if (!is_array($roomAmenities)) $roomAmenities = [];

                                // Gambar Kamar
                                $roomImage = !empty($room['main_image']) 
                                    ? htmlspecialchars($room['main_image']) 
                                    : BASE_URL . '/images/placeholder.jpg';
                                
                                // URL Booking (Membawa parameter tanggal)
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

                            <div class="group relative flex flex-col md:flex-row bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:border-blue-200 transition-all duration-300 <?= !$isAvailable ? 'opacity-60 bg-slate-50' : '' ?>">
                                
                                <div class="md:w-72 h-64 md:h-auto bg-slate-200 relative shrink-0 overflow-hidden">
                                    <img src="<?= $roomImage ?>" alt="<?= htmlspecialchars($room['room_type']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    
                                    <?php if (!$isAvailable): ?>
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-[2px]">
                                            <span class="bg-red-600 text-white px-4 py-1.5 rounded-lg font-bold text-sm uppercase tracking-widest shadow-lg transform -rotate-3">Habis Terjual</span>
                                        </div>
                                    <?php elseif ($remainingSlots <= 3): ?>
                                        <div class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm animate-pulse">
                                            Sisa <?= $remainingSlots ?> kamar!
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1 p-5 md:p-6 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($room['room_type']) ?></h3>
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mb-4">
                                            <span class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1.5 rounded-md border border-slate-100">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <?= htmlspecialchars($room['description'] ?? 'Standard Room') ?>
                                            </span>
                                            <span class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1.5 rounded-md border border-slate-100">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Max <?= $room['capacity'] ?? 2 ?> Org
                                            </span>
                                            <?php if ($searchData['capacity_warning']): ?>
                                                <span class="text-orange-600 bg-orange-50 px-2 py-1 rounded border border-orange-100 font-bold">
                                                    ⚠ Kapasitas Kurang
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <?php foreach(array_slice($roomAmenities, 0, 4) as $am): ?>
                                                <span class="text-[11px] font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded"><?= htmlspecialchars($am) ?></span>
                                            <?php endforeach; ?>
                                            <?php if(count($roomAmenities) > 4): ?>
                                                <span class="text-[11px] text-slate-400 px-1">+<?= count($roomAmenities)-4 ?> lainnya</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="border-t border-slate-100 pt-4 flex items-end justify-between">
                                        <div>
                                            <?php if ($duration > 1 || $roomCount > 1): ?>
                                                <p class="text-xs text-slate-400 mb-0.5">Total Harga</p>
                                                <p class="text-xl font-bold text-blue-600">Rp <?= number_format($totalPrice, 0, ',', '.') ?></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Rp <?= number_format($perNightPrice, 0, ',', '.') ?> /malam per kamar</p>
                                            <?php else: ?>
                                                <p class="text-xs text-slate-400 mb-0.5">Harga per malam</p>
                                                <p class="text-xl font-bold text-blue-600">Rp <?= number_format($perNightPrice, 0, ',', '.') ?></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Termasuk pajak & biaya</p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($isAvailable): ?>
                                            <a href="<?= $bookingUrl ?>" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 hover:bg-blue-700 hover:shadow-lg transition-all active:scale-95">
                                                Pilih Kamar
                                            </a>
                                        <?php else: ?>
                                            <button disabled class="bg-slate-100 text-slate-400 px-6 py-2.5 rounded-xl font-bold text-sm cursor-not-allowed border border-slate-200">
                                                Tidak Tersedia
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sticky top-24">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Tentang Hotel</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        <?= nl2br(htmlspecialchars($description)) ?>
                    </p>
                    
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Fasilitas Populer</h4>
                    <ul class="space-y-2.5">
                        <?php foreach (array_slice($amenities, 0, 5) as $facility): ?>
                            <li class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-blue-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <?= htmlspecialchars($facility) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-2">Lokasi</h4>
                        <p class="text-sm text-slate-500 mb-3"><?= htmlspecialchars($city) ?></p>
                        <div class="h-32 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 text-xs">
                            Map Preview (<?= htmlspecialchars($city) ?>)
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</section>

<script>
    const slides = document.querySelectorAll('.detail-hero__slide');
    const dots = document.querySelectorAll('.detail-hero button'); // Dot buttons
    let activeIndex = 0;
    
    function changeSlide(index) {
        // Hide all
        slides.forEach(s => s.classList.remove('is-active'));
        dots.forEach(d => {
            d.classList.remove('bg-white', 'scale-125');
            d.classList.add('bg-white/50');
        });

        // Show active
        slides[index].classList.add('is-active');
        if(dots[index]) {
            dots[index].classList.remove('bg-white/50');
            dots[index].classList.add('bg-white', 'scale-125');
        }
        
        activeIndex = index;
    }

    // Auto rotate every 5s
    setInterval(() => {
        const nextIndex = (activeIndex + 1) % slides.length;
        changeSlide(nextIndex);
    }, 5000);
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>