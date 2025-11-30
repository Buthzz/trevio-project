<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$payment = $data['payment'] ?? null;
$user = $data['user'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';

// Pastikan data payment ada
if (!$payment) {
    echo "<div class='p-6 text-center text-red-500'>Data pembayaran tidak ditemukan.</div>";
    return;
}

// Status badge color
// FIX: Mengganti 'emerald' ke 'green' agar warna muncul di semua versi Tailwind
$statusColors = [
    'pending' => 'bg-gray-100 text-gray-700',
    'uploaded' => 'bg-amber-100 text-amber-700', 
    'verified' => 'bg-green-100 text-green-700', // Ganti emerald -> green
    'rejected' => 'bg-red-100 text-red-700',
];
$statusKey = $payment['payment_status'] ?? 'pending';
$statusClass = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-600';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="min-h-[calc(100vh-4rem)] bg-slate-50 p-6 md:p-8">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <a href="<?= $baseUrl ?>/admin/payments" class="flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar
            </a>
            <span class="rounded-full px-3 py-1 text-sm font-bold shadow-sm <?= $statusClass ?>">
                Status: <?= ucfirst($statusKey) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="space-y-6">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200">
                    <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <h3 class="font-bold text-slate-900">Bukti Transfer</h3>
                    </div>
                    <div class="p-4 flex justify-center bg-slate-100/50">
                        <?php if (!empty($payment['payment_proof'])): ?>
                            <a href="<?= $baseUrl ?>/uploads/payments/<?= htmlspecialchars($payment['payment_proof']) ?>" target="_blank" class="group relative block overflow-hidden rounded-lg">
                                <img src="<?= $baseUrl ?>/uploads/payments/<?= htmlspecialchars($payment['payment_proof']) ?>" 
                                     alt="Bukti Transfer" 
                                     class="max-h-[500px] w-auto object-contain shadow-md transition group-hover:scale-105">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100">
                                    <span class="rounded-full bg-white/90 px-4 py-2 text-xs font-bold text-slate-900 shadow-lg">Klik untuk Memperbesar</span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="flex h-64 w-full flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-300 rounded-lg">
                                <svg class="h-12 w-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Belum ada bukti upload</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="bg-white px-6 py-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Catatan User</p>
                        <p class="text-slate-700 italic">"<?= !empty($payment['payment_notes']) ? htmlspecialchars($payment['payment_notes']) : 'Tidak ada catatan' ?>"</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <h3 class="font-bold text-slate-900">Informasi Transaksi</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-sm text-slate-500">Booking ID</span>
                            <span class="font-mono font-bold text-slate-900">#<?= htmlspecialchars($payment['booking_code'] ?? '-') ?></span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-sm text-slate-500">Total Tagihan</span>
                            <span class="font-bold text-slate-900">Rp <?= number_format($payment['total_price'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                         <div class="flex justify-between px-6 py-4 bg-yellow-50/50">
                            <span class="text-sm text-slate-600">Jumlah Ditransfer</span>
                            <span class="font-bold text-green-600 text-lg">Rp <?= number_format($payment['transfer_amount'] ?? $payment['total_price'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-sm text-slate-500">Tanggal Upload</span>
                            <span class="text-sm font-medium text-slate-900">
                                <?= isset($payment['created_at']) ? date('d M Y, H:i', strtotime($payment['created_at'])) : '-' ?>
                            </span>
                        </div>
                        <div class="px-6 py-4">
                            <span class="block text-sm text-slate-500 mb-1">Customer</span>
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                    <?= strtoupper(substr($payment['customer_name'] ?? 'U', 0, 2)) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($payment['customer_name'] ?? 'Unknown') ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($payment['customer_email'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                         <div class="px-6 py-4">
                            <span class="block text-sm text-slate-500 mb-1">Hotel</span>
                            <p class="text-sm font-medium text-slate-900"><?= htmlspecialchars($payment['hotel_name'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <h3 class="font-bold text-slate-900">Aksi Admin</h3>
                    </div>
                    <div class="p-6">
                        <?php if (in_array($statusKey, ['pending', 'uploaded'])): ?>
                            
                            <p class="mb-6 text-sm text-slate-600">
                                Harap periksa bukti transfer dengan teliti. Jika sesuai, klik <strong>Konfirmasi</strong>. Trigger sistem akan otomatis mengubah status booking menjadi "Confirmed".
                            </p>

                            <div class="grid grid-cols-2 gap-4">
                                <form action="<?= $baseUrl ?>/admin/payments/reject" method="POST" onsubmit="return confirm('Yakin ingin menolak pembayaran ini?');">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                    <div class="mb-3">
                                         <label class="sr-only" for="reason">Alasan Penolakan</label>
                                         <select name="reason" id="reason" class="w-full rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                                             <option value="Bukti tidak valid/buram">Bukti tidak valid/buram</option>
                                             <option value="Jumlah transfer tidak sesuai">Jumlah transfer tidak sesuai</option>
                                             <option value="Rekening tujuan salah">Rekening tujuan salah</option>
                                             <option value="Indikasi penipuan">Indikasi penipuan</option>
                                         </select>
                                    </div>
                                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 hover:border-red-300 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Tolak
                                    </button>
                                </form>

                                <form action="<?= $baseUrl ?>/admin/payments/confirm" method="POST" onsubmit="return confirm('Konfirmasi pembayaran ini valid?');">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                    <div class="mb-3 h-[38px]"></div> 
                                    
                                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-green-700 hover:shadow-lg transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Konfirmasi Valid
                                    </button>
                                </form>
                            </div>

                        <?php else: ?>
                            <div class="rounded-lg bg-slate-50 border border-slate-100 p-4 text-center">
                                <div class="mb-2 flex justify-center">
                                    <?php if ($statusKey === 'verified'): ?>
                                        <div class="rounded-full bg-green-100 p-3 text-green-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    <?php else: ?>
                                        <div class="rounded-full bg-red-100 p-3 text-red-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <h4 class="font-bold text-slate-900">Transaksi Selesai</h4>
                                <p class="text-sm text-slate-500 mt-1">
                                    Diproses pada: <?= isset($payment['verified_at']) ? date('d M Y, H:i', strtotime($payment['verified_at'])) : '-' ?>
                                </p>
                                <?php if ($statusKey === 'rejected' && !empty($payment['rejection_reason'])): ?>
                                    <p class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded border border-red-100">
                                        Alasan: <?= htmlspecialchars($payment['rejection_reason']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>