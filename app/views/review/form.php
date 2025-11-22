<?php
require_once __DIR__ . '/../../../helpers/functions.php';

// Ketika file ini diakses langsung, layout perlu dipanggil agar CSS terload.
$includeLayout = $includeLayout ?? true;
if ($includeLayout) {
	$pageTitle = $pageTitle ?? 'Tulis Review | Trevio';
	require __DIR__ . '/../layouts/header.php';
}

// Komponen form ulasan mandiri agar bisa disisipkan di berbagai halaman.
// Backend bisa mengganti action ataupun method sesuai endpoint penyimpanan.
$reviewFormAction = $reviewFormAction ?? trevio_view_route('review/store.php');
$defaultRating    = $defaultRating ?? '5.0';
?>

<!-- Komponen form review mandiri -->
<section class="mx-auto mt-12 max-w-4xl rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
	<div class="mb-6 text-center">
		<p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Bagikan Pengalaman</p>
		<h2 class="mt-2 text-2xl font-bold text-slate-900">Tulis Review untuk Traveler Lain</h2>
		<p class="mt-2 text-sm text-slate-500">Review dengan rating tinggi (4.8 ke atas) berpeluang tampil di bagian Testimoni Tamu.</p>
	</div>

	<form action="<?= htmlspecialchars($reviewFormAction) ?>" method="post" class="grid grid-cols-1 gap-5 md:grid-cols-2">
		<div class="md:col-span-1">
			<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="review_name">Nama Lengkap</label>
			<input class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-accent focus:bg-white" id="review_name" name="name" placeholder="Apa nama kamu?" required type="text">
		</div>
		<div class="md:col-span-1">
			<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="review_trip">Jenis Trip</label>
			<input class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-accent focus:bg-white" id="review_trip" name="trip" placeholder="Contoh: Liburan keluarga di Bali" required type="text">
		</div>

		<div class="md:col-span-1">
			<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="review_rating">Rating Pengalaman</label>
			<select class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-accent focus:bg-white" id="review_rating" name="rating" required>
				<?php foreach (['5.0', '4.9', '4.8'] as $ratingOption): ?>
					<option value="<?= $ratingOption ?>" <?= $ratingOption === $defaultRating ? 'selected' : '' ?>><?= $ratingOption ?> ★</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="md:col-span-1">
			<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="review_avatar">URL Foto (opsional)</label>
			<input class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-accent focus:bg-white" id="review_avatar" name="avatar" placeholder="https://..." type="url">
		</div>

		<div class="md:col-span-2">
			<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="review_quote">Cerita Singkat</label>
			<textarea class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-relaxed text-slate-800 outline-none transition focus:border-accent focus:bg-white" id="review_quote" name="quote" placeholder="Ceritakan highlight terbaik selama menggunakan Trevio" required rows="4"></textarea>
		</div>

		<div class="md:col-span-2 flex flex-col gap-3 rounded-2xl bg-slate-50/80 p-4 text-xs text-slate-500">
			<p>Catatan: Tim kurasi Trevio hanya menampilkan review dengan rating &ge; 4.8 untuk menjaga kualitas testimoni.</p>
			<p>Dengan menekan tombol kirim, kamu setuju ulasanmu dapat dipublikasikan di laman promosi Trevio.</p>
		</div>

		<div class="md:col-span-2">
			<button class="flex w-full items-center justify-center gap-2 rounded-full bg-accent px-6 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-accent/30 transition hover:bg-accentLight" type="submit">
				Kirim Review
			</button>
		</div>
	</form>
</section>

<?php if ($includeLayout): ?>
	<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php endif; ?>
