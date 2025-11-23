
<?php
// [BACKEND NOTE]: Mulai session untuk menyimpan data booking sementara
// Session digunakan untuk membawa data dari form -> confirm -> history
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Judul halaman form agar tab browser jelas.
$pageTitle = 'Trevio | Form Pemesanan';

// [BACKEND NOTE]: Ambil data hotel dari URL parameters (dikirim dari hotel/detail.php)
// Parameter yang diterima: hotel_id, room_name, room_price
$hotelId = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : null;
$roomName = isset($_GET['room_name']) ? $_GET['room_name'] : null;
$roomPrice = isset($_GET['room_price']) ? $_GET['room_price'] : null;

// [BACKEND NOTE]: Jika ada hotel_id, load data hotel dari dummy array
// Untuk production: query ke database untuk ambil data hotel
$hotelName = 'Aurora Peaks Resort'; // Default
$hotelCity = 'Tokyo'; // Default

if ($hotelId) {
    // Dummy hotel data - sesuaikan dengan data di hotel/detail.php
    $hotelsDummy = [
        101 => ['name' => 'Padma Hotel Bandung', 'city' => 'Bandung'],
        102 => ['name' => 'The Langham Jakarta', 'city' => 'Jakarta'],
        103 => ['name' => 'Amanjiwo Resort', 'city' => 'Yogyakarta'],
        104 => ['name' => 'The Apurva Kempinski', 'city' => 'Bali'],
    ];
    
    if (isset($hotelsDummy[$hotelId])) {
        $hotelName = $hotelsDummy[$hotelId]['name'];
        $hotelCity = $hotelsDummy[$hotelId]['city'];
    }
}

// [BACKEND NOTE]: Extract harga numerik dari room_price (format: "Rp 2.100.000 / malam")
// Untuk production: simpan harga sebagai integer di database
$pricePerNight = 520000; // Default
if ($roomPrice) {
    // Extract angka dari format "Rp 2.100.000 / malam"
    preg_match('/[\d.]+/', str_replace(',', '', $roomPrice), $matches);
    if (!empty($matches[0])) {
        $pricePerNight = intval(str_replace('.', '', $matches[0]));
    }
}

// Data default reservasi agar komponen samping memiliki nilai awal.
$reservation = [
    'hotel' => $hotelName,
    'hotel_id' => $hotelId,
    'hotel_city' => $hotelCity,
    'room' => $roomName ?: 'Premier Onsen Suite',
    'check_in' => '2025-12-18',
    'check_out' => '2025-12-21',
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
// Hitung biaya layanan tambahan.
$totalService = $totalBase * $reservation['service'];
// Total keseluruhan ditampilkan pada ringkasan harga.
$totalAmount = $totalBase + $totalTax + $totalService;

// [BACKEND NOTE]: Tangani POST request dari form saat tombol "Bayar Sekarang" diklik
// Data form akan disimpan ke session $_SESSION['trevio_booking_current']
// Kemudian redirect ke halaman konfirmasi pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    // [BACKEND NOTE]: Ambil data tamu dari form
    // Untuk production: validasi semua input dan sanitize data
    $guestName = $_POST['guest_name'] ?? 'Guest';
    $guestEmail = $_POST['guest_email'] ?? '';
    $guestPhone = $_POST['guest_phone'] ?? '';
    $guestNationality = $_POST['guest_nationality'] ?? 'Indonesia';
    $specialRequest = $_POST['special_request'] ?? '';
    
    // [BACKEND NOTE]: Generate booking code dan invoice code yang unik
    // Format: TRV-YYMMDD-XXX (XXX = random 3 digit)
    // Untuk production: gunakan auto-increment ID dari database
    $bookingCode = 'TRV-' . date('ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $invoiceCode = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // [BACKEND NOTE]: Simpan data booking lengkap ke session
    // Session ini akan dibaca oleh halaman confirm.php
    $_SESSION['trevio_booking_current'] = [
        'booking_code' => $bookingCode,
        'invoice_code' => $invoiceCode,
        'hotel_id' => $reservation['hotel_id'],
        'hotel_name' => $reservation['hotel'],
        'hotel_city' => $reservation['hotel_city'],
        'room_name' => $reservation['room'],
        'check_in' => $reservation['check_in'],
        'check_out' => $reservation['check_out'],
        'nights' => $reservation['nights'],
        'guests' => $reservation['guests'],
        'price_per_night' => $reservation['price_per_night'],
        'total_base' => $totalBase,
        'total_tax' => $totalTax,
        'total_service' => $totalService,
        'total_amount' => $totalAmount,
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
                <span class="size-2 rounded-full bg-emerald-500"></span>
                Terhubung aman dengan Trevio Secure Pay
            </div>
        </div>
        <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
            <!-- Kolom kiri: step-by-step pengisian data tamu -->
            <!-- [BACKEND NOTE]: Form wrapper untuk mengirim data booking via POST -->
            <form method="POST" action="" class="space-y-8">
                <input type="hidden" name="confirm_booking" value="1">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                        <span class="step-badge">1</span>
                        Data Tamu
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <label class="form-group">
                            <span>Nama Lengkap</span>
                            <input class="input-control" name="guest_name" type="text" placeholder="Nama sesuai KTP / Paspor" required />
                        </label>
                        <label class="form-group">
                            <span>Email</span>
                            <input class="input-control" name="guest_email" type="email" placeholder="nama@email.com" required />
                        </label>
                        <label class="form-group">
                            <span>No. Telepon</span>
                            <input class="input-control" name="guest_phone" type="tel" placeholder="08xxxxxxxxxx" required />
                        </label>
                        <label class="form-group">
                            <span>Kebangsaan</span>
                            <input class="input-control" name="guest_nationality" type="text" placeholder="Indonesia" value="Indonesia" />
                        </label>
                        <label class="form-group sm:col-span-2">
                            <span>Permintaan Khusus</span>
                           <textarea class="input-control h-28" name="special_request" placeholder="Contoh: high floor, late check-in"></textarea>
                        </label>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                        <span class="step-badge">2</span>
                        Rincian Pembayaran
                    </div>
                    <div class="mt-6 grid gap-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="form-group">
                                <span>Metode Pembayaran</span>
                                <select class="input-control">
                                    <option value="credit">Kartu Kredit / Debit</option>
                                    <option value="bank">Transfer Bank</option>
                                    <option value="ewallet">E-Wallet</option>
                                </select>
                            </label>
                            <label class="form-group">
                                <span>Kode Promo</span>
                                <div class="flex gap-3">
                                    <input class="input-control" type="text" placeholder="TREVIOHEMAT" />
                                    <button class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" type="button">Terapkan</button>
                                </div>
                            </label>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="form-group">
                                <span>Nama Pemegang Kartu</span>
                                <input class="input-control" type="text" placeholder="Nama di kartu" />
                            </label>
                            <label class="form-group">
                                <span>Nomor Kartu</span>
                                <input class="input-control" type="text" placeholder="XXXX XXXX XXXX XXXX" />
                            </label>
                        </div>
                        <div class="grid gap-4 md:grid-cols-[1fr,120px,120px]">
                            <label class="form-group">
                                <span>Alamat Penagihan</span>
                                <input class="input-control" type="text" placeholder="Masukkan alamat" />
                            </label>
                            <label class="form-group">
                                <span>Exp</span>
                                <input class="input-control" type="text" placeholder="MM/YY" />
                            </label>
                            <label class="form-group">
                                <span>CVV</span>
                                <input class="input-control" type="password" placeholder="***" />
                            </label>
                        </div>
                        <label class="flex items-center gap-3 text-sm text-slate-600">
                            <input class="size-4 rounded border-slate-300 text-accent focus:ring-accent" type="checkbox" />
                            Saya telah membaca dan menyetujui syarat & ketentuan Trevio.
                        </label>
                        <div class="rounded-2xl bg-slate-50 p-4 text-xs text-slate-500">
                            Pembayaran diproses oleh Trevio Secure Pay dengan enkripsi 256-bit. Detail kartu tidak disimpan di server kami.
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                        <span class="step-badge">3</span>
                        Konfirmasi
                    </div>
                    <div class="mt-6 space-y-4 text-sm text-slate-600">
                        <p>Pastikan seluruh data sudah benar. Setelah menekan tombol "Bayar Sekarang", e-ticket akan terkirim ke email dan WhatsApp Anda.</p>
                        <!-- [BACKEND NOTE]: Tombol submit untuk kirim data ke server -->
                        <button class="inline-flex items-center gap-2 rounded-full bg-accent px-6 py-3 text-sm font-semibold text-white transition hover:bg-accentLight" type="submit">Bayar Sekarang</button>
                        <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" type="button" data-save-button>Simpan untuk nanti</button>
                    </div>
                </div>
                </div>
            </form>
            <!-- Kolom kanan: ringkasan pesanan + add-on -->
            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-primary">Ringkasan Pesanan</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Hotel</span>
                            <span class="font-semibold text-primary"><?= htmlspecialchars($reservation['hotel']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Kamar</span>
                            <span><?= htmlspecialchars($reservation['room']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Check-in</span>
                            <span><?= htmlspecialchars($reservation['check_in']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Check-out</span>
                            <span><?= htmlspecialchars($reservation['check_out']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Durasi</span>
                            <span><?= htmlspecialchars($reservation['nights']) ?> malam</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Tamu</span>
                            <span><?= htmlspecialchars($reservation['guests']) ?></span>
                        </div>
                    </div>
                    <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span><?= $reservation['nights'] ?> malam x IDR <?= number_format($reservation['price_per_night'], 0, ',', '.') ?></span>
                            <span>IDR <?= number_format($totalBase, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Pajak (<?= $reservation['tax'] * 100 ?>%)</span>
                            <span>IDR <?= number_format($totalTax, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Layanan (<?= $reservation['service'] * 100 ?>%)</span>
                            <span>IDR <?= number_format($totalService, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-3 font-semibold text-primary">
                            <span>Total</span>
                            <span>IDR <?= number_format($totalAmount, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500">
                        Harga sudah termasuk pajak dan biaya layanan. Pembayaran fleksibel tersedia untuk metode kartu kredit tertentu.
                    </div>
                </div>
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-700">
                    <h3 class="text-sm font-semibold text-emerald-900">Perlindungan perjalanan Trevio</h3>
                    <p class="mt-2">Tambah Travel Safe+ hanya IDR 180.000 untuk perlindungan pembatalan mendadak dan bantuan medis darurat.</p>
                    <button class="mt-4 inline-flex items-center justify-center rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100" type="button">Tambah perlindungan</button>
                </div>
            </aside>
        </div>
    </div>
</section>
<script>
// [BACKEND NOTE]: JavaScript untuk handle tombol "Simpan untuk nanti"
// Form submit untuk "Bayar Sekarang" sudah ditangani oleh POST handler di PHP
document.addEventListener('DOMContentLoaded', function () {
    // Tombol untuk menyimpan data reservasi sementara.
    const saveButton = document.querySelector('[data-save-button]');

    if (saveButton) {
        // Beri feedback ketika reservasi hanya disimpan ke riwayat.
        saveButton.addEventListener('click', function () {
            Swal.fire({
                title: 'Disimpan',
                text: 'Reservasi kamu tersimpan di riwayat booking.',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });

            setTimeout(function () {
                // TODO backend: ganti redirect sesuai route riwayat resmi
                window.location.href = 'history.php?saved=1';
            }, 2100);
        });
    }
});
</script>
<?php
// Footer global untuk menutup layout halaman.
require __DIR__ . '/../layouts/footer.php';
?>
