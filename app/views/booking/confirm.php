<?php
require_once __DIR__ . '/../../../helpers/functions.php';
trevio_start_session();

// [SECURITY]: Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $loginUrl = trevio_view_route('auth/login.php') . '?return_url=' . urlencode($_SERVER['REQUEST_URI']);
    header("Location: $loginUrl");
    exit;
}

// [BACKEND NOTE]: Ambil data booking dari session
// Data ini diset di booking/form.php sebelum redirect
$booking = $_SESSION['trevio_booking_current'] ?? null;

// Jika tidak ada data booking, redirect ke home atau history
if (!$booking) {
    // Fallback: Cek apakah ada invoice di URL, mungkin user refresh halaman
    $invoiceCode = $_GET['invoice'] ?? '';
    if ($invoiceCode) {
        // Coba cari di history
        $history = $_SESSION['trevio_booking_history'] ?? [];
        foreach ($history as $item) {
            if ($item['invoice'] === $invoiceCode) {
                // Found in history, restore minimal data for display
                $booking = [
                    'booking_code' => $item['code'],
                    'invoice_code' => $item['invoice'],
                    'hotel_name' => $item['hotel'],
                    'hotel_city' => $item['city'],
                    'created_at' => $item['created_at'],
                    'status' => $item['status'],
                    'total_amount' => $item['total'],
                    'guest_name' => $item['guest_name'] ?? $_SESSION['user_name'],
                    'check_in' => $item['check_in'],
                    'check_out' => $item['check_out'],
                    'nights' => $item['nights']
                ];
                break;
            }
        }
    }
    
    if (!$booking) {
        // Jika masih tidak ketemu, redirect ke home
        header('Location: ' . trevio_view_route('home/index.php'));
        exit;
    }
}

// Extract variables for easier usage in view
$invoiceCode = $booking['invoice_code'];
$bookingCode = $booking['booking_code'];
$hotelName = $booking['hotel_name'];
$guestName = $booking['guest_name'];
$totalAmount = $booking['total_amount'];
$createdAt = $booking['created_at'];

// [BACKEND NOTE]: Simpan booking ke history session untuk ditampilkan di history.php
// Untuk production: simpan ke database table bookings
if (!isset($_SESSION['trevio_booking_history'])) {
    $_SESSION['trevio_booking_history'] = [];
}

// Tambahkan booking saat ini ke history (jika belum ada)
$bookingExists = false;
foreach ($_SESSION['trevio_booking_history'] as $item) {
    if ($item['code'] === $bookingCode) {
        $bookingExists = true;
        break;
    }
}

if (!$bookingExists) {
    $_SESSION['trevio_booking_history'][] = [
        'code' => $bookingCode,
        'invoice' => $invoiceCode,
        'hotel' => $hotelName,
        'city' => $booking['hotel_city'] ?? 'Jakarta',
        'date' => date('d M Y', strtotime($createdAt)),
        'check_in' => $booking['check_in'] ?? date('Y-m-d'),
        'check_out' => $booking['check_out'] ?? date('Y-m-d', strtotime('+3 days')),
        'nights' => $booking['nights'] ?? 3,
        'status' => $booking['status'],
        'total' => $totalAmount,
        'guest_name' => $guestName,
        'created_at' => $createdAt,
        'link' => 'detail.php?code=' . urlencode($bookingCode)
    ];
}

// Daftar tahapan proses pembayaran untuk progress list.
$timeline = [
	['label' => 'Pemesanan dibuat', 'time' => date('H:i', strtotime($createdAt)), 'status' => 'Selesai'],
	['label' => 'Pembayaran diterima', 'time' => date('H:i', strtotime('+2 minutes', strtotime($createdAt))), 'status' => 'Selesai'],
	['label' => 'Voucher dikirim', 'time' => date('H:i', strtotime('+3 minutes', strtotime($createdAt))), 'status' => 'Selesai'],
];

// Sertakan header umum agar layout dan asset konsisten.
require __DIR__ . '/../layouts/header.php';
?>
<!-- Halaman konfirmasi pembayaran statis; mudah dihubungkan dengan controller pembayaran -->
<section class="bg-slate-100/70 py-16">
	<div class="mx-auto max-w-5xl space-y-8 px-6">
		<!-- Hero copy yang muncul setelah gateway sukses -->
		<div class="flex flex-col gap-3 text-center">
			<p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Konfirmasi Pembayaran</p>
			<h1 class="text-3xl font-semibold text-primary">Pembayaran kamu sudah kami terima ✅</h1>
			<p class="text-sm text-slate-500">Voucher dan invoice resmi telah dikirim ke email <?= htmlspecialchars($guestName) ?>.</p>
		</div>

		<!-- Grid utama: kiri = detail invoice, kanan = CTA lanjutan -->
		<div class="grid gap-6 lg:grid-cols-[2fr,1fr]">
			<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
				<!-- Blok invoice: nilai diambil dari query string/controller -->
				<div class="flex flex-col gap-1 border-b border-slate-100 pb-4">
					<p class="text-xs font-semibold text-slate-500">Kode Invoice</p>
					<p class="text-lg font-semibold text-primary">#<?= htmlspecialchars($invoiceCode) ?></p>
				</div>
				<dl class="mt-6 grid gap-4 text-sm text-slate-600 sm:grid-cols-2">
					<div>
						<dt class="text-xs font-semibold text-slate-500">Hotel</dt>
						<dd class="text-base font-semibold text-primary"><?= htmlspecialchars($hotelName) ?></dd>
					</div>
					<div>
						<dt class="text-xs font-semibold text-slate-500">Tamu Utama</dt>
						<dd><?= htmlspecialchars($guestName) ?></dd>
					</div>
					<div>
						<dt class="text-xs font-semibold text-slate-500">Metode Pembayaran</dt>
						<dd>Kartu Kredit (Trevio Secure Pay)</dd>
					</div>
					<div>
						<dt class="text-xs font-semibold text-slate-500">Status</dt>
						<dd class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Sukses</dd>
					</div>
				</dl>

				<div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
					<div class="flex items-center justify-between">
						<span>Total Dibayar</span>
						<span class="text-lg font-semibold text-primary"><?= htmlspecialchars($totalAmount) ?></span>
					</div>
				</div>

				<div class="mt-8 space-y-4">
					<p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status Proses</p>
					<ol class="space-y-3">
						<?php foreach ($timeline as $item): ?>
							<li class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3 text-sm text-slate-600">
								<div class="flex flex-col">
									<span class="font-semibold text-primary"><?= htmlspecialchars($item['label']) ?></span>
									<span class="text-xs text-slate-400"><?= htmlspecialchars($item['time']) ?> WIB</span>
								</div>
								<span class="text-xs font-semibold text-emerald-600"><?= htmlspecialchars($item['status']) ?></span>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>

			<!-- Sidebar CTA -->
			<div class="space-y-4">
				<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
					<h3 class="font-semibold text-primary">Langkah Selanjutnya</h3>
					<p class="mt-2 text-sm text-slate-500">Cek email kamu untuk e-voucher atau lihat detail pesanan di halaman history.</p>
					<div class="mt-6 flex flex-col gap-3">
						<a href="history.php" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
							Lihat Riwayat Pesanan
						</a>
						<a href="../home/index.php" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
							Kembali ke Beranda
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
require __DIR__ . '/../layouts/footer.php';
?>