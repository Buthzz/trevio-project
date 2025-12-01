<?php 
require_once '../app/views/layouts/header.php'; 

// [LOGIC DATA]: Normalisasi Gambar & Fasilitas
$roomImage = !empty($room['main_image']) ? $room['main_image'] : ($hotel['main_image'] ?? BASE_URL . '/public/images/placeholder.jpg');

// Parsing fasilitas kamar
$amenities = is_string($room['amenities'] ?? '') ? json_decode($room['amenities'], true) : ($room['amenities'] ?? []);
if (!is_array($amenities)) $amenities = [];

// [DATA SEARCH]: Pastikan data dari controller ada
$checkIn = $search_params['check_in'] ?? date('Y-m-d');
$checkOut = $search_params['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
$numRooms = (int)($search_params['num_rooms'] ?? 1);

// Hitung durasi malam untuk display
$d1 = new DateTime($checkIn);
$d2 = new DateTime($checkOut);
$nights = $d1->diff($d2)->days;
if ($nights < 1) $nights = 1;
?>

<div class="min-h-screen bg-slate-50 py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <nav class="flex text-sm font-medium text-slate-500 mb-2">
                <a href="<?= BASE_URL ?>" class="hover:text-blue-600 transition">Beranda</a>
                <span class="mx-2">/</span>
                <a href="<?= BASE_URL ?>/hotel/detail/<?= $hotel['id'] ?>" class="hover:text-blue-600 transition"><?= htmlspecialchars($hotel['name']) ?></a>
                <span class="mx-2">/</span>
                <span class="text-slate-800">Booking</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900">Konfirmasi Pesanan</h1>
            <p class="text-slate-500 mt-1">Selesaikan satu langkah lagi untuk liburan impianmu.</p>
        </div>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl shadow-sm animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-bold">Terjadi Kesalahan</p>
                        <p class="text-sm text-red-600"><?= $_SESSION['flash_error']; ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/booking/store" method="POST" id="bookingForm" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
            
            <input type="hidden" name="check_in" id="check_in" value="<?= htmlspecialchars($checkIn) ?>">
            <input type="hidden" name="check_out" id="check_out" value="<?= htmlspecialchars($checkOut) ?>">
            <input type="hidden" name="num_rooms" id="num_rooms" value="<?= htmlspecialchars($numRooms) ?>">
            
            <input type="hidden" id="price_per_night" value="<?= $room['price_per_night'] ?>">

            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold shadow-sm">1</span>
                            <h2 class="text-lg font-bold text-slate-800">Detail Perjalanan</h2>
                        </div>
                        <a href="<?= BASE_URL ?>/hotel/detail/<?= $hotel['id'] ?>?check_in=<?= $checkIn ?>&check_out=<?= $checkOut ?>&num_rooms=<?= $numRooms ?>#rooms" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline">
                            Ubah
                        </a>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Check-In</p>
                                <p class="font-bold text-slate-800 text-sm md:text-base"><?= date('d M Y', strtotime($checkIn)) ?></p>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Check-Out</p>
                                <p class="font-bold text-slate-800 text-sm md:text-base"><?= date('d M Y', strtotime($checkOut)) ?></p>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Durasi</p>
                                <p class="font-bold text-slate-800 text-sm md:text-base"><?= $nights ?> Malam</p>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Kamar</p>
                                <p class="font-bold text-slate-800 text-sm md:text-base"><?= $numRooms ?> Unit</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold shadow-sm">2</span>
                        <h2 class="text-lg font-bold text-slate-800">Data Pemesan</h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input type="text" name="guest_name" required
                                    value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                    placeholder="Nama sesuai KTP/Paspor"
                                    class="pl-10 w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-2.5 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="email" name="guest_email" required
                                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                        placeholder="contoh@email.com"
                                        class="pl-10 w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-2.5 transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nomor WhatsApp</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <input type="tel" name="guest_phone" required
                                        placeholder="08123456789"
                                        class="pl-10 w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-2.5 transition">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 transform active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Lanjut ke Pembayaran</span>
                    </button>
                    <p class="text-xs text-slate-400 mt-3 text-center">
                        Transaksi aman dan terenkripsi. Dengan melanjutkan, Anda menyetujui S&K Trevio.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-white shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-200 overflow-hidden">
                        
                        <div class="relative h-48 bg-slate-200">
                            <img src="<?= htmlspecialchars($roomImage) ?>" alt="<?= htmlspecialchars($room['room_type']) ?>" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="font-bold text-lg leading-tight mb-1"><?= htmlspecialchars($hotel['name']) ?></h3>
                                <p class="text-sm text-slate-200 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?= htmlspecialchars($hotel['city'] ?? 'Indonesia') ?>
                                </p>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="mb-4">
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase tracking-wide">Tipe Kamar</span>
                                <h4 class="font-bold text-slate-800 text-lg mt-1"><?= htmlspecialchars($room['room_type']) ?></h4>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <?php foreach(array_slice($amenities, 0, 3) as $am): ?>
                                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200"><?= htmlspecialchars($am) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <hr class="border-dashed border-slate-200 my-4">

                            <div id="price_summary" class="space-y-3 text-sm text-slate-600">
                                <div class="flex justify-between">
                                    <span>Harga per malam</span>
                                    <span class="font-medium text-slate-900">Rp <?= number_format($room['price_per_night'], 0, ',', '.') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Durasi menginap</span>
                                    <span class="font-medium text-slate-900"><span id="summary_nights"><?= $nights ?></span> Malam</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah kamar</span>
                                    <span class="font-medium text-slate-900">x <span id="summary_rooms"><?= $numRooms ?></span></span>
                                </div>
                                
                                <div class="bg-slate-50 p-3 rounded-lg space-y-2 mt-2 border border-slate-100">
                                    <div class="flex justify-between text-xs">
                                        <span>Subtotal</span>
                                        <span id="summary_subtotal">Rp -</span>
                                    </div>
                                    <div class="flex justify-between text-xs text-slate-500">
                                        <span>Pajak & Layanan (15%)</span>
                                        <span id="summary_tax">Rp -</span>
                                    </div>
                                </div>

                                <div class="border-t border-slate-100 pt-3 mt-2">
                                    <div class="flex justify-between items-end">
                                        <span class="font-bold text-slate-800">Total Pembayaran</span>
                                        <span id="summary_total" class="font-extrabold text-xl text-blue-600">Rp -</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil data dari hidden inputs
    const checkInVal = document.getElementById('check_in').value;
    const checkOutVal = document.getElementById('check_out').value;
    const roomsVal = document.getElementById('num_rooms').value;
    const pricePerNight = parseFloat(document.getElementById('price_per_night').value);
    
    // UI Elements Summary
    const summarySubtotal = document.getElementById('summary_subtotal');
    const summaryTax = document.getElementById('summary_tax');
    const summaryTotal = document.getElementById('summary_total');

    const formatRp = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

    function calculateTotal() {
        const checkInDate = new Date(checkInVal);
        const checkOutDate = new Date(checkOutVal);
        const rooms = parseInt(roomsVal) || 1;

        if (checkInVal && checkOutVal && checkOutDate > checkInDate) {
            const timeDiff = checkOutDate - checkInDate;
            const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            // Perhitungan
            const subtotal = pricePerNight * nights * rooms;
            const taxService = subtotal * 0.15; // 15% (10% Tax + 5% Service)
            const total = subtotal + taxService;

            summarySubtotal.textContent = formatRp(subtotal);
            summaryTax.textContent = formatRp(taxService);
            summaryTotal.textContent = formatRp(total);
        } else {
            summarySubtotal.textContent = 'Rp -';
            summaryTax.textContent = 'Rp -';
            summaryTotal.textContent = 'Rp -';
        }
    }

    // Jalankan sekali saat load halaman
    calculateTotal();
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>