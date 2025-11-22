<?php
// Judul halaman memastikan konteks konfirmasi terbaca jelas di tab browser.
$pageTitle = 'Trevio | Konfirmasi Pembayaran';
// Gunakan invoice dari query string, jika kosong buat default berbasis tanggal hari ini.
$invoiceCode = $_GET['invoice'] ?? 'INV-' . date('Ymd') . '-001';
// Nama hotel utama yang sedang dikonfirmasi pembayarannya.
$hotelName = $_GET['hotel'] ?? 'Aurora Peaks Resort';
// Nama tamu utama yang menerima konfirmasi.
$guestName = $_GET['guest'] ?? 'Amelia Pratama';
// Total pembayaran yang ditampilkan pada ringkasan invoice.
$totalAmount = $_GET['total'] ?? 'IDR 7.820.000';
// Daftar tahapan proses pembayaran untuk progress list.
$timeline = [
	['label' => 'Pemesanan dibuat', 'time' => '10:21', 'status' => 'Selesai'],
	['label' => 'Pembayaran diterima', 'time' => '10:23', 'status' => 'Selesai'],
	['label' => 'Voucher dikirim', 'time' => '10:24', 'status' => 'Selesai'],
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

			<aside class="space-y-4">
				<!-- Bagian kanan bisa diisi rekomendasi itinerary atau upsell -->
				<div class="rounded-3xl border border-slate-200 bg-white p-5 text-sm text-slate-600 shadow-sm">
					<p class="text-base font-semibold text-primary">Langkah selanjutnya</p>
					<ul class="mt-4 space-y-2 text-sm">
						<li>• Simpan invoice ini sebagai bukti pembayaran.</li>
						<li>• Tunjukkan e-voucher saat check-in di hotel.</li>
						<li>• Hubungi support bila jadwal perlu diubah.</li>
					</ul>
				</div>
				<div class="flex flex-col gap-3 text-sm font-semibold">
					<a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-5 py-2 text-slate-600 transition hover:border-accent hover:text-accent" href="form.php">Kembali ke Form Pemesanan</a>
					<a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-5 py-2 text-slate-600 transition hover:border-accent hover:text-accent" href="history.php">Lihat Riwayat Booking</a>
					<a class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-2 text-white transition hover:bg-accentLight" href="../hotel/search.php">Cari hotel lainnya</a>
				</div>
			</aside>
		</div>
	</div>
</section>
<?php
// Tutup halaman dengan footer global.
require __DIR__ . '/../layouts/footer.php';
?>
