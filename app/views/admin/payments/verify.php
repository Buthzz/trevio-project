<?php
// Tentukan judul dan layout
$pageTitle = 'Verifikasi Pembayaran - Admin';
require_once __DIR__ . '/../../layouts/header.php';

// Helper format rupiah (jika belum ada di global)
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return 'Rp ' . number_format((float)$angka, 0, ',', '.'); }
}

$payment = $data['payment'] ?? null;
$csrf_token = $data['csrf_token'] ?? '';
?>

<div class="flex min-h-[calc(100vh-4rem)] bg-slate-50">
    <!-- Sidebar -->
    <aside class="hidden w-64 border-r border-slate-200 bg-white lg:block">
        <div class="p-6">
            <nav class="space-y-1">
                <a href="<?= BASE_URL ?>/admin/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path></svg>
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/hotels" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path></svg>
                    Hotels
                </a>
                <a href="<?= BASE_URL ?>/admin/payments" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Payments
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto p-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="<?= BASE_URL ?>/admin/payments" class="text-sm font-medium text-accent hover:underline">← Kembali ke Daftar</a>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">Verifikasi Pembayaran #<?= $payment['id'] ?? '-' ?></h1>
                <p class="text-slate-500">Booking Code: <?= htmlspecialchars($payment['booking_code'] ?? 'N/A') ?></p>
            </div>
        </div>

        <?php if (!$payment): ?>
            <div class="rounded-lg bg-red-50 p-4 text-red-700">Data pembayaran tidak ditemukan.</div>
        <?php else: ?>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Kolom Kiri: Detail -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Info Transaksi -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Detail Transfer</h2>
                        <div class="grid grid-cols-2 gap-y-4 text-sm">
                            <div>
                                <p class="text-slate-500">Jumlah Transfer</p>
                                <p class="text-lg font-bold text-slate-900"><?= formatRupiah($payment['transfer_amount'] ?? 0) ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500">Metode</p>
                                <p class="font-semibold text-slate-900 uppercase"><?= htmlspecialchars($payment['payment_method'] ?? '-') ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500">Bank Pengirim</p>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($payment['transfer_from_bank'] ?? '-') ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500">Atas Nama / No. Rek</p>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($payment['payment_notes'] ?? '-') ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500">Tanggal Upload</p>
                                <p class="font-semibold text-slate-900"><?= isset($payment['uploaded_at']) ? date('d M Y H:i', strtotime($payment['uploaded_at'])) : '-' ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500">Status Saat Ini</p>
                                <?php
                                $status = $payment['payment_status'] ?? 'unknown';
                                $badges = [
                                    'verified' => 'bg-emerald-100 text-emerald-700',
                                    'pending_verification' => 'bg-amber-100 text-amber-700',
                                    'failed' => 'bg-red-100 text-red-700'
                                ];
                                $badgeClass = $badges[$status] ?? 'bg-slate-100 text-slate-600';
                                ?>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $badgeClass ?>">
                                    <?= ucwords(str_replace('_', ' ', $status)) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Booking -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Data Booking</h2>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Tamu</span>
                                <span class="font-medium text-slate-900"><?= htmlspecialchars($payment['customer_name'] ?? 'Data User Terhapus') ?></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Hotel</span>
                                <span class="font-medium text-slate-900"><?= htmlspecialchars($payment['hotel_name'] ?? 'Data Hotel Terhapus') ?> (<?= htmlspecialchars($payment['hotel_location'] ?? '-') ?>)</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Kamar</span>
                                <span class="font-medium text-slate-900"><?= $payment['num_rooms'] ?? 0 ?>x <?= htmlspecialchars($payment['room_type'] ?? 'Standard') ?></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Check-in</span>
                                <span class="font-medium text-slate-900"><?= isset($payment['check_in_date']) ? date('d M Y', strtotime($payment['check_in_date'])) : '-' ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Tagihan</span>
                                <span class="font-medium text-slate-900"><?= formatRupiah($payment['total_price'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Foto -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Bukti Pembayaran</h2>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                            <?php 
                            $proofFile = $payment['payment_proof'] ?? '';
                            $proofUrl = BASE_URL . '/uploads/payments/' . $proofFile;
                            $ext = pathinfo($proofFile, PATHINFO_EXTENSION);
                            ?>
                            
                            <?php if (!empty($proofFile) && in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])): ?>
                                <img src="<?= $proofUrl ?>" class="h-auto w-full object-contain" alt="Bukti Transfer">
                            <?php elseif (!empty($proofFile) && strtolower($ext) === 'pdf'): ?>
                                <iframe src="<?= $proofUrl ?>" class="h-96 w-full"></iframe>
                            <?php else: ?>
                                <div class="p-10 text-center">
                                    <p class="text-slate-500 mb-2">File tidak tersedia atau format tidak didukung.</p>
                                    <?php if(!empty($proofFile)): ?>
                                        <a href="<?= $proofUrl ?>" target="_blank" class="text-accent underline">Download File</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($proofFile)): ?>
                        <div class="mt-4 text-center">
                            <a href="<?= $proofUrl ?>" target="_blank" class="text-sm font-medium text-accent hover:text-accentLight">
                                Buka Ukuran Penuh ↗
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kolom Kanan: Aksi -->
                <div class="space-y-6">
                    <div class="sticky top-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Aksi Admin</h2>
                        
                        <?php if (($payment['payment_status'] ?? '') !== 'pending_verification'): ?>
                            <div class="rounded-lg bg-slate-100 p-4 text-center text-sm text-slate-600">
                                Transaksi ini sudah diproses.<br>
                                Status: <strong><?= ucwords(str_replace('_', ' ', $payment['payment_status'] ?? 'Unknown')) ?></strong>
                            </div>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>/admin/payments/process" method="POST" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Catatan (Opsional)</label>
                                    <textarea name="admin_note" rows="3" class="w-full rounded-lg border border-slate-300 p-2 text-sm focus:border-accent focus:ring-accent" placeholder="Alasan terima/tolak..."></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <button type="submit" name="action" value="approve" 
                                            class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                            onclick="return confirm('Yakin ingin menerima pembayaran ini?')">
                                        Terima
                                    </button>
                                    <button type="submit" name="action" value="reject" 
                                            class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            onclick="return confirm('Yakin ingin menolak? User akan diminta upload ulang.')">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>