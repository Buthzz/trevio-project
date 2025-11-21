<?php
$pageTitle = 'Trevio | Form Pemesanan';
$reservation = $reservation ?? [
    'hotel' => 'Aurora Peaks Resort',
    'room' => 'Premier Onsen Suite',
    'check_in' => '2025-12-18',
    'check_out' => '2025-12-21',
    'nights' => 3,
    'guests' => '2 dewasa',
    'price_per_night' => 520000,
    'tax' => 0.1,
    'service' => 0.05,
];
$totalBase = $reservation['price_per_night'] * $reservation['nights'];
$totalTax = $totalBase * $reservation['tax'];
$totalService = $totalBase * $reservation['service'];
$totalAmount = $totalBase + $totalTax + $totalService;
require __DIR__ . '/partials/header.php';
?>
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
            <div class="space-y-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                        <span class="step-badge">1</span>
                        Data Tamu
                    </div>
                    <form class="mt-6 grid gap-4 sm:grid-cols-2">
                        <label class="form-group">
                            <span>Nama Lengkap</span>
                            <input class="input-control" type="text" placeholder="Nama sesuai KTP / Paspor" />
                        </label>
                        <label class="form-group">
                            <span>Email</span>
                            <input class="input-control" type="email" placeholder="nama@email.com" />
                        </label>
                        <label class="form-group">
                            <span>No. Telepon</span>
                            <input class="input-control" type="tel" placeholder="08xxxxxxxxxx" />
                        </label>
                        <label class="form-group">
                            <span>Kebangsaan</span>
                            <input class="input-control" type="text" placeholder="Indonesia" />
                        </label>
                        <label class="form-group sm:col-span-2">
                            <span>Permintaan Khusus</span>
                            <textarea class="input-control h-28" placeholder="Contoh: high floor, late check-in"></textarea>
                        </label>
                    </form>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                        <span class="step-badge">2</span>
                        Rincian Pembayaran
                    </div>
                    <form class="mt-6 grid gap-4">
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
                    </form>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                        <span class="step-badge">3</span>
                        Konfirmasi
                    </div>
                    <div class="mt-6 space-y-4 text-sm text-slate-600">
                        <p>Pastikan seluruh data sudah benar. Setelah menekan tombol "Bayar Sekarang", e-ticket akan terkirim ke email dan WhatsApp Anda.</p>
                        <button class="inline-flex items-center gap-2 rounded-full bg-accent px-6 py-3 text-sm font-semibold text-white transition hover:bg-accentLight" type="button" onclick="Swal.fire({title: 'Berhasil!', text: 'Reservasi kamu sudah dikonfirmasi. Detail booking terkirim ke email.', icon: 'success'})">Bayar Sekarang</button>
                        <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-600 transition hover:border-accent hover:text-accent" type="button">Simpan untuk nanti</button>
                    </div>
                </div>
            </div>
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
<?php require __DIR__ . '/partials/footer.php'; ?>
