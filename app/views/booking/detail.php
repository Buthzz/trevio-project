<?php
// [BACKEND NOTE]: Mulai session untuk ambil booking detail dari history
// Booking detail dicari berdasarkan booking code dari URL parameter
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Judul halaman detail untuk memudahkan identifikasi di tab browser.
$pageTitle = 'Trevio | Detail Pemesanan';

// [BACKEND NOTE]: Ambil booking code dari URL parameter
// Contoh: detail.php?code=TRV-251123-001
$bookingCode = $_GET['code'] ?? null;

// [BACKEND NOTE]: Cari booking di session history berdasarkan code
// Untuk production: query ke database SELECT * FROM bookings WHERE code = ?
$booking = null;
if ($bookingCode && isset($_SESSION['trevio_booking_history'])) {
    foreach ($_SESSION['trevio_booking_history'] as $item) {
        if ($item['code'] === $bookingCode) {
            $booking = $item;
            break;
        }
    }
}

// [BACKEND NOTE]: Jika booking tidak ditemukan, gunakan data dummy atau redirect
// Untuk production: redirect ke history.php jika tidak ditemukan
if (!$booking) {
    // Fallback ke data dummy untuk testing
    $booking = [
        'code' => $bookingCode ?: 'TRV-' . date('ymd') . '-882',
        'hotel' => $_GET['hotel'] ?? 'Aurora Peaks Resort',
        'city' => 'Tokyo',
        'status' => $_GET['status'] ?? 'Menunggu Pembayaran',
        'check_in' => $_GET['check_in'] ?? '18 Des 2025',
        'check_out' => $_GET['check_out'] ?? '21 Des 2025',
        'nights' => $_GET['nights'] ?? 3,
        'guest_name' => $_GET['guest'] ?? 'Amelia Pratama',
        'room_name' => $_GET['room'] ?? 'Premier Onsen Suite',
    ];
}

// Ekstrak data untuk tampilan
$hotelName = $booking['hotel'];
$status = $booking['status'];
$checkIn = $booking['check_in'];
$checkOut = $booking['check_out'];
$nights = $booking['nights'];
$guestName = $booking['guest_name'];
$roomType = $booking['room_name'] ?? $booking['room'] ?? 'Premier Onsen Suite';
// Daftar aksi utama yang bisa dilakukan pengguna.
$actions = [
	['label' => 'Lanjutkan Pembayaran', 'href' => 'confirm.php?invoice=' . urlencode('INV-' . date('Ymd') . '-001'), 'variant' => 'primary'],
	['label' => 'Ubah Jadwal', 'href' => 'form.php?hotel=' . urlencode($hotelName), 'variant' => 'outline'],
	['label' => 'Lihat Riwayat', 'href' => 'history.php', 'variant' => 'ghost'],
];

// Header umum untuk konsistensi navigasi admin/booking.
require __DIR__ . '/../layouts/header.php';
?>
<!-- Detail booking untuk user, tinggal sambungkan data controller -->
<section class="bg-slate-100/70 py-16">
	<div class="mx-auto max-w-5xl space-y-8 px-6">
		<!-- Breadcrumb bantu user kembali ke riwayat/cari hotel -->
		<nav class="text-xs text-slate-500">
			<a class="text-accent" href="../hotel/search.php">Cari Hotel</a>
			<span class="mx-1">/</span>
			<a class="text-accent" href="history.php">Riwayat</a>
			<span class="mx-1">/</span>
			<span class="text-primary">Detail Booking</span>
		</nav>

		<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
			<!-- Header kartu memuat status + kode booking -->
			<div class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
				<div>
					<p class="text-xs font-semibold text-slate-500">Kode Booking</p>
					<h1 class="text-3xl font-semibold text-primary">#<?= htmlspecialchars($bookingCode) ?></h1>
					<p class="text-sm text-slate-500">Status: <span class="font-semibold text-accent"><?= htmlspecialchars($status) ?></span></p>
				</div>
				<div class="text-sm text-slate-500">
					<p class="text-xs font-semibold uppercase tracking-[0.2em]">Hotel</p>
					<p class="text-base font-semibold text-primary"><?= htmlspecialchars($hotelName) ?></p>
				</div>
			</div>

			<div class="mt-6 grid gap-6 md:grid-cols-2">
				<!-- Kolom kiri: info tamu & jadwal, kanan: rincian pembayaran -->
				<div class="space-y-4 text-sm text-slate-600">
					<p class="text-xs font-semibold text-slate-500">Informasi Tamu</p>
					<dl class="space-y-2">
						<div class="flex items-center justify-between">
							<dt>Nama</dt>
							<dd class="font-semibold text-primary"><?= htmlspecialchars($guestName) ?></dd>
						</div>
						<div class="flex items-center justify-between">
							<dt>Tamu</dt>
							<dd>2 Dewasa</dd>
						</div>
						<div class="flex items-center justify-between">
							<dt>Kontak</dt>
							<dd>+62 811-8899-221</dd>
						</div>
					</dl>
				</div>
				<div class="space-y-4 text-sm text-slate-600">
					<p class="text-xs font-semibold text-slate-500">Detail Menginap</p>
					<dl class="space-y-2">
						<div class="flex items-center justify-between">
							<dt>Check-in</dt>
							<dd><?= htmlspecialchars($checkIn) ?></dd>
						</div>
						<div class="flex items-center justify-between">
							<dt>Check-out</dt>
							<dd><?= htmlspecialchars($checkOut) ?></dd>
						</div>
						<div class="flex items-center justify-between">
							<dt>Durasi</dt>
							<dd><?= htmlspecialchars($nights) ?> malam</dd>
						</div>
						<div class="flex items-center justify-between">
							<dt>Tipe Kamar</dt>
							<dd><?= htmlspecialchars($roomType) ?></dd>
						</div>
					</dl>
				</div>
			</div>

			<div class="mt-8 rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">
				<!-- Catatan opsional dari CS atau sistem -->
				<p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Catatan</p>
				<ul class="mt-3 space-y-2">
					<li>• Cek-in lebih awal dapat diajukan maksimal H-1.</li>
					<li>• Pembatalan fleksibel tersedia sampai 48 jam sebelum check-in.</li>
					<li>• Hubungi concierge Trevio untuk kebutuhan transportasi.</li>
				</ul>
			</div>

			<div class="mt-6 flex flex-col gap-3 text-sm font-semibold sm:flex-row">
				<!-- Action list didefinisikan array $actions supaya mudah dirubah -->
				<?php foreach ($actions as $action): ?>
					<?php
					$classes = 'inline-flex flex-1 items-center justify-center rounded-full px-5 py-2 transition';
					if ($action['variant'] === 'primary') {
						$classes .= ' bg-accent text-white hover:bg-accentLight';
					} elseif ($action['variant'] === 'outline') {
						$classes .= ' border border-slate-200 text-slate-600 hover:border-accent hover:text-accent';
					} else {
						$classes .= ' text-slate-500 hover:text-primary';
					}
					?>
					<a class="<?= $classes ?>" href="<?= htmlspecialchars($action['href']) ?>"><?= htmlspecialchars($action['label']) ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
<?php
// Footer global menutup seluruh layout.
require __DIR__ . '/../layouts/footer.php';
?>
