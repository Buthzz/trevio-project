<?php
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// [SECURITY]: Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Redirect ke login jika belum login
    $loginUrl = trevio_view_route('auth/login.php') . '?return_url=' . urlencode($_SERVER['REQUEST_URI']);
    header("Location: $loginUrl");
    exit;
}

// Ambil data dari URL
$hotelId = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;
$roomName = isset($_GET['room_name']) ? $_GET['room_name'] : '';
$roomPrice = isset($_GET['room_price']) ? $_GET['room_price'] : '';

// Dummy hotel data - sesuaikan dengan data di hotel/detail.php
$hotelsDummy = [
    101 => ['name' => 'Padma Hotel Bandung', 'city' => 'Bandung'],
    102 => ['name' => 'The Langham Jakarta', 'city' => 'Jakarta'],
    103 => ['name' => 'Amanjiwo Resort', 'city' => 'Yogyakarta'],
    104 => ['name' => 'The Apurva Kempinski', 'city' => 'Bali'],
];

$hotelName = 'Hotel Tidak Dikenal';
$hotelCity = 'Indonesia';

if ($hotelId && isset($hotelsDummy[$hotelId])) {
    $hotelName = $hotelsDummy[$hotelId]['name'];
    $hotelCity = $hotelsDummy[$hotelId]['city'];
}

// [BACKEND NOTE]: Extract harga numerik dari room_price (format: "Rp 2.100.000 / malam")
// Untuk production: simpan harga sebagai integer di database
$pricePerNight = 520000; // Default fallback
if ($roomPrice) {
    // Extract angka dari format "Rp 2.100.000 / malam"
    preg_match('/[\d.]+/', str_replace(',', '', $roomPrice), $matches);
    if (!empty($matches[0])) {
        $pricePerNight = intval(str_replace('.', '', $matches[0]));
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // [SECURITY]: Verifikasi CSRF Token
    if (!trevio_verify_csrf()) {
        die('Akses ditolak: Token CSRF tidak valid. Silakan refresh halaman.');
    }

    // Ambil data dari form
    $guestName = filter_input(INPUT_POST, 'guest_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $guestEmail = filter_input(INPUT_POST, 'guest_email', FILTER_SANITIZE_EMAIL);
    $guestPhone = filter_input(INPUT_POST, 'guest_phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $guestNationality = filter_input(INPUT_POST, 'guest_nationality', FILTER_SANITIZE_SPECIAL_CHARS);
    $specialRequest = filter_input(INPUT_POST, 'special_request', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Generate kode booking dan invoice
    // [SECURITY]: Gunakan random_bytes untuk keamanan yang lebih baik
    $bookingCode = 'TRV-' . date('ymd') . '-' . bin2hex(random_bytes(2));
    $invoiceCode = 'INV-' . date('Ymd') . '-' . bin2hex(random_bytes(2));

    // Hitung total (simulasi)
    $nights = 3; // Default 3 malam sesuai tampilan
    $totalBase = $pricePerNight * $nights;
    $totalTax = $totalBase * 0.10;
    $totalService = $totalBase * 0.05;
    $totalAmount = $totalBase + $totalTax + $totalService;

    // [BACKEND NOTE]: Simpan data booking ke session untuk halaman konfirmasi
    $_SESSION['trevio_booking_current'] = [
        'booking_code' => $bookingCode,
        'invoice_code' => $invoiceCode,
        'hotel_id' => $hotelId,
        'hotel_name' => $hotelName,
        'hotel_city' => $hotelCity,
        'room_name' => $roomName ?: 'Premier Onsen Suite',
        'check_in' => date('Y-m-d', strtotime('+1 day')), // Simulasi besok
        'check_out' => date('Y-m-d', strtotime('+4 days')), // Simulasi 3 malam
        'nights' => $nights,
        'guests' => '2 dewasa',
        'price_per_night' => $pricePerNight,
        'total_base' => $totalBase,
        'total_tax' => $totalTax,
        'total_service' => $totalService,
        'total_amount' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
        'guest_name' => $guestName,
        'guest_email' => $guestEmail,
        'guest_phone' => $guestPhone,
        'guest_nationality' => $guestNationality,
        'special_request' => $specialRequest,
        'status' => 'Menunggu Pembayaran',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    
    // [BACKEND NOTE]: Redirect ke halaman konfirmasi
    // Untuk production: proses payment gateway integration di sini
    header('Location: confirm.php?invoice=' . urlencode($invoiceCode));
    exit;
}

// Data default reservasi agar komponen samping memiliki nilai awal.
$reservation = [
    'hotel' => $hotelName,
    'hotel_id' => $hotelId,
    'hotel_city' => $hotelCity,
    'room' => $roomName ?: 'Premier Onsen Suite',
    'check_in' => date('d M Y', strtotime('+1 day')),
    'check_out' => date('d M Y', strtotime('+4 days')),
    'nights' => 3,
    'guests' => '2 dewasa',
    'price_per_night' => $pricePerNight,
    'tax' => 0.1,
    'service' => 0.05,
];

// Hitung total tarif dasar dari harga per malam x durasi.
$totalBase = $reservation['price_per_night'] * $reservation['nights'];
// Hitung nominal pajak berdasarkan tarif yang ditentukan.
$totalTax = $totalBase * $reservation['tax'];
$totalService = $totalBase * $reservation['service'];
$grandTotal = $totalBase + $totalTax + $totalService;

// Sertakan header global agar tampilan konsisten.
require __DIR__ . '/../layouts/header.php';
?>
<!-- Halaman form pemesanan: hubungkan ke controller booking/create -->
<section class="bg-slate-100/70 py-16">
    <div class="mx-auto max-w-6xl space-y-10 px-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Form Pemesanan</p>
                <h1 class="text-3xl font-semibold text-primary">Lengkapi detail reservasi kamu</h1>
                <p class="mt-2 text-sm text-slate-500">Pastikan informasi tamu dan metode pembayaran sudah benar sebelum melanjutkan.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-500 shadow">
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                Sisa waktu: 14:59
            </div>
        </div>

        <div class="grid gap-12 lg:grid-cols-[1.5fr,1fr]">
            <!-- Kolom Kiri: Form Input Data Tamu -->
            <div class="space-y-8">
                <!-- Card 1: Informasi Tamu -->
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-primary">Informasi Tamu</h2>
                    </div>
                    
                    <form method="POST" action="" class="space-y-6">
                        <?= trevio_csrf_field() ?>
                        
                        <div class="grid gap-6 sm:grid-cols-2">
                            <label class="form-group">
                                <span class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</span>
                                <input class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" name="guest_name" type="text" placeholder="Nama sesuai KTP / Paspor" required value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" />
                            </label>
                            <label class="form-group">
                                <span class="block text-sm font-medium text-slate-700 mb-1">Email</span>
                                <input class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" name="guest_email" type="email" placeholder="nama@email.com" required value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" />
                            </label>
                            <label class="form-group">
                                <span class="block text-sm font-medium text-slate-700 mb-1">No. Telepon</span>
                                <input class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" name="guest_phone" type="tel" placeholder="08xxxxxxxxxx" required />
                            </label>
                            <label class="form-group">
                                <span class="block text-sm font-medium text-slate-700 mb-1">Kebangsaan</span>
                                <input class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" name="guest_nationality" type="text" placeholder="Indonesia" value="Indonesia" />
                            </label>
                            <label class="form-group sm:col-span-2">
                                <span class="block text-sm font-medium text-slate-700 mb-1">Permintaan Khusus (Opsional)</span>
                                <textarea class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" name="special_request" rows="3" placeholder="Contoh: Check-in lebih awal, bantal tambahan..."></textarea>
                            </label>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full rounded-xl bg-primary px-6 py-3.5 text-center font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:bg-blue-700 hover:shadow-blue-900/30 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                                Lanjutkan ke Pembayaran
                            </button>
                            <p class="mt-3 text-center text-xs text-slate-400">
                                Dengan melanjutkan, Anda menyetujui <a href="#" class="text-blue-600 hover:underline">Syarat & Ketentuan</a> Trevio.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Pesanan (Sticky) -->
            <div class="relative">
                <div class="sticky top-8 space-y-6">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-primary">Ringkasan Pesanan</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Hotel Info -->
                            <div class="flex gap-4">
                                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-slate-200">
                                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=300&q=80" alt="Hotel thumbnail" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <h4 class="font-bold text-primary line-clamp-2"><?= htmlspecialchars($reservation['hotel']) ?></h4>
                                    <p class="text-sm text-slate-500"><?= htmlspecialchars($reservation['hotel_city']) ?></p>
                                    <div class="mt-1 flex items-center gap-1 text-xs text-amber-500">
                                        <span>★★★★★</span>
                                        <span class="text-slate-400">(4.9)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="rounded-xl bg-slate-50 p-4 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Check-in</span>
                                    <span class="font-medium text-slate-700"><?= $reservation['check_in'] ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Check-out</span>
                                    <span class="font-medium text-slate-700"><?= $reservation['check_out'] ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Durasi</span>
                                    <span class="font-medium text-slate-700"><?= $reservation['nights'] ?> Malam</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Tipe Kamar</span>
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($reservation['room']) ?></span>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 border-t border-slate-100 pt-4 text-sm">
                                <div class="flex justify-between text-slate-600">
                                    <span>Harga Kamar (x<?= $reservation['nights'] ?>)</span>
                                    <span>Rp <?= number_format($totalBase, 0, ',', '.') ?></span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Pajak (10%)</span>
                                    <span>Rp <?= number_format($totalTax, 0, ',', '.') ?></span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Layanan (5%)</span>
                                    <span>Rp <?= number_format($totalService, 0, ',', '.') ?></span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3 text-base font-bold text-primary">
                                    <span>Total Akhir</span>
                                    <span class="text-blue-600">Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Save for later button -->
                    <button type="button" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Simpan untuk nanti
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require __DIR__ . '/../layouts/footer.php';
?>