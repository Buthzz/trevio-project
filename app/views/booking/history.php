<?php
// Judul halaman riwayat agar tab browser mudah dikenali.
$pageTitle = 'Trevio | Riwayat Booking';
// Koleksi dummy riwayat booking untuk dirender pada kartu.
$history = [
	[
		'code' => 'TRV-0924-882',
		'hotel' => 'The Langham Jakarta',
		'city' => 'Jakarta',
		'date' => '24 Sep 2025',
		'status' => 'Selesai',
		'total' => 'IDR 5.450.000',
		'link' => 'detail.php?code=TRV-0924-882&hotel=The+Langham+Jakarta&status=Selesai'
	],
	[
		'code' => 'TRV-1012-441',
		'hotel' => 'Padma Resort Ubud',
		'city' => 'Bali',
		'date' => '12 Okt 2025',
		'status' => 'Berjalan',
		'total' => 'IDR 6.120.000',
		'link' => 'detail.php?code=TRV-1012-441&hotel=Padma+Resort+Ubud&status=Berjalan'
	],
	[
		'code' => 'TRV-1105-778',
		'hotel' => 'GAIA Hotel Bandung',
		'city' => 'Bandung',
		'date' => '05 Nov 2025',
		'status' => 'Menunggu Pembayaran',
		'total' => 'IDR 3.980.000',
		'link' => 'detail.php?code=TRV-1105-778&hotel=GAIA+Hotel+Bandung&status=Menunggu+Pembayaran'
	],
];

// Sertakan header global supaya navigasi konsisten.
require __DIR__ . '/../layouts/header.php';
?>
<!-- Halaman riwayat booking untuk user -->
<section class="bg-slate-100/70 py-16">
	<div class="mx-auto max-w-6xl space-y-8 px-6">
		<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
			<!-- Header tetap statis, bisa diisi data nyata dari controller -->
			<div>
				<p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Riwayat Booking</p>
				<h1 class="text-3xl font-semibold text-primary">Kelola seluruh perjalananmu</h1>
				<p class="text-sm text-slate-500">Pantau status pemesanan, simpan untuk nanti, dan lanjutkan pembayaran dari satu tempat.</p>
			</div>
			<div class="flex gap-3 text-sm font-semibold">
				<a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-slate-600 transition hover:border-accent hover:text-accent" href="form.php">Buat Reservasi Baru</a>
				<a class="inline-flex items-center justify-center rounded-full bg-accent px-4 py-2 text-white transition hover:bg-accentLight" href="../hotel/search.php">Cari Hotel</a>
			</div>
		</div>

		<div class="space-y-4">
			<!-- Loop history dummy, tinggal ganti dengan data DB -->
			<?php foreach ($history as $item): ?>
				<article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
						<div>
							<p class="text-xs font-semibold text-slate-500">Kode</p>
							<p class="text-lg font-semibold text-primary">#<?= htmlspecialchars($item['code']) ?></p>
						</div>
						<div class="text-sm text-slate-500">
							<p class="text-base font-semibold text-primary"><?= htmlspecialchars($item['hotel']) ?></p>
							<p><?= htmlspecialchars($item['city']) ?></p>
						</div>
						<div class="text-sm text-slate-500">
							<p class="text-xs font-semibold text-slate-500">Tanggal</p>
							<p><?= htmlspecialchars($item['date']) ?></p>
						</div>
						<div class="text-right text-sm text-slate-500">
							<p class="text-xs font-semibold text-slate-500">Total</p>
							<p class="text-base font-semibold text-primary"><?= htmlspecialchars($item['total']) ?></p>
						</div>
					</div>
					<div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
						<span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold <?= $item['status'] === 'Selesai' ? 'bg-emerald-50 text-emerald-700' : ($item['status'] === 'Berjalan' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600') ?>">
							<?= htmlspecialchars($item['status']) ?>
						</span>
						<div class="flex flex-wrap gap-3 text-sm font-semibold">
							<a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-slate-600 transition hover:border-accent hover:text-accent" href="<?= htmlspecialchars($item['link']) ?>">Lihat Detail</a>
							<a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-slate-600 transition hover:border-accent hover:text-accent" href="form.php?hotel=<?= urlencode($item['hotel']) ?>">Pesan Lagi</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
// Footer umum menutup struktur halaman.
require __DIR__ . '/../layouts/footer.php';
?>
