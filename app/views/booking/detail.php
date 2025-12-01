<?php
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// [SECURITY]: Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $loginUrl = trevio_view_route('auth/login.php') . '?return_url=' . urlencode($_SERVER['REQUEST_URI']);
    header("Location: $loginUrl");
    exit;
}

// [BACKEND NOTE]: Ambil booking code dari URL
$bookingCode = $_GET['code'] ?? '';

if (!$bookingCode) {
    $_SESSION['flash_error'] = 'Kode booking tidak valid.';
    header('Location: ' . trevio_view_route('booking/history.php'));
    exit;
}

// [BACKEND NOTE]: Untuk production, query dari database
// SELECT * FROM bookings WHERE booking_code = ? AND user_id = ?
// Sementara gunakan dummy data untuk development
$booking = [
    'id' => 1,
    'booking_code' => $bookingCode,
    'booking_status' => 'pending_payment',
    'hotel_name' => 'Merlynn Park Hotel',
    'room_type' => 'Single Room',
    'check_in_date' => '2025-12-01',
    'check_out_date' => '2025-12-02',
    'num_nights' => 1,
    'num_rooms' => 1,
    'guest_name' => $_SESSION['user_name'] ?? 'Guest',
    'guest_email' => $_SESSION['user_email'] ?? 'guest@example.com',
    'guest_phone' => '08123456789',
    'price_per_night' => 300000,
    'subtotal' => 300000,
    'tax_amount' => 30000,
    'service_charge' => 15000,
    'total_price' => 345000
];

// Include header setelah logic
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?= BASE_URL ?>" class="text-gray-700 hover:text-blue-600">Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <a href="<?= BASE_URL ?>/booking/history" class="text-gray-700 hover:text-blue-600">Riwayat</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-gray-500">Detail #<?= htmlspecialchars($booking['booking_code']) ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['flash_success']; ?></span>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['flash_error']; ?></span>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Informasi Booking -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-white">Invoice Booking</h2>
                    <span class="bg-white text-blue-600 px-3 py-1 rounded-full text-sm font-bold">
                        <?= htmlspecialchars($booking['booking_status']) ?>
                    </span>
                </div>
                
                <div class="p-6">
                    <!-- Hotel Info -->
                    <div class="flex items-start mb-6 pb-6 border-b border-gray-100">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($booking['hotel_name']) ?></h3>
                            <p class="text-gray-600"><?= htmlspecialchars($booking['room_type']) ?></p>
                        </div>
                    </div>

                    <!-- Detail Tanggal & Kamar -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Check-in</p>
                            <p class="font-semibold text-gray-800"><?= date('d M Y', strtotime($booking['check_in_date'])) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Check-out</p>
                            <p class="font-semibold text-gray-800"><?= date('d M Y', strtotime($booking['check_out_date'])) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Durasi</p>
                            <p class="font-semibold text-gray-800"><?= $booking['num_nights'] ?> Malam</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jumlah Kamar</p>
                            <p class="font-semibold text-gray-800"><?= $booking['num_rooms'] ?> Unit</p>
                        </div>
                    </div>

                    <!-- Detail Tamu -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Data Tamu</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="flex justify-between mb-2">
                                <span class="text-gray-600">Nama:</span>
                                <span class="font-medium"><?= htmlspecialchars($booking['guest_name']) ?></span>
                            </p>
                            <p class="flex justify-between mb-2">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium"><?= htmlspecialchars($booking['guest_email']) ?></span>
                            </p>
                            <p class="flex justify-between">
                                <span class="text-gray-600">Telepon:</span>
                                <span class="font-medium"><?= htmlspecialchars($booking['guest_phone']) ?></span>
                            </p>
                        </div>
                    </div>

                    <!-- Rincian Harga -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Rincian Pembayaran</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Harga per malam (x<?= $booking['num_rooms'] ?>)</span>
                                <span>Rp <?= number_format($booking['price_per_night'] * $booking['num_rooms'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Total <?= $booking['num_nights'] ?> malam</span>
                                <span>Rp <?= number_format($booking['subtotal'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Pajak (10%)</span>
                                <span>Rp <?= number_format($booking['tax_amount'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Biaya Layanan (5%)</span>
                                <span>Rp <?= number_format($booking['service_charge'], 0, ',', '.') ?></span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between items-center">
                                <span class="font-bold text-lg text-gray-800">Total Pembayaran</span>
                                <span class="font-bold text-xl text-blue-600">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Upload Pembayaran -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Pembayaran</h3>
                
                <?php if ($booking['booking_status'] == 'pending_payment'): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <p class="text-sm text-yellow-700">Silakan transfer ke rekening berikut dan upload bukti pembayaran Anda.</p>
                    </div>

                    <div class="mb-6 text-center border-b border-gray-100 pb-6">
                        <p class="text-gray-500 text-sm mb-1">Bank BCA</p>
                        <p class="text-2xl font-mono font-bold text-gray-800 tracking-wider">8293-2910-2212</p>
                        <p class="text-gray-500 text-sm mt-1">a.n PT Trevio Indonesia</p>
                    </div>

                    <form action="<?= BASE_URL ?>/booking/uploadPayment" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank Pengirim</label>
                            <input type="text" name="bank_name" required placeholder="Contoh: BCA / Mandiri" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                            <input type="text" name="account_name" required placeholder="Nama sesuai buku tabungan" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                            <input type="number" name="account_number" placeholder="Nomor rekening pengirim" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer</label>
                            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Max 5MB.</p>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                            Konfirmasi Pembayaran
                        </button>
                    </form>

                <?php elseif ($booking['booking_status'] == 'pending_verification'): ?>
                    <div class="text-center py-8">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Menunggu Verifikasi</h3>
                        <p class="mt-2 text-sm text-gray-500">Kami sedang mengecek pembayaran Anda. Mohon tunggu maksimal 1x24 jam.</p>
                    </div>

                <?php elseif ($booking['booking_status'] == 'confirmed'): ?>
                    <div class="text-center py-8">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Pembayaran Lunas</h3>
                        <p class="mt-2 text-sm text-gray-500">E-Ticket telah dikirim ke email Anda. Silakan tunjukkan saat check-in.</p>
                        <a href="<?= BASE_URL ?>/booking/ticket/<?= $booking['booking_code'] ?>" class="mt-4 inline-block text-blue-600 font-medium hover:underline">Download E-Ticket</a>
                    </div>

                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">Status booking: <strong><?= $booking['booking_status'] ?></strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>