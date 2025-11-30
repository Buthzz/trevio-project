<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$payment = $data['payment'] ?? null;
// Cek apakah ini mode Deep Link atau Admin Dashboard biasa
$isDeepLink = $data['is_deep_link'] ?? false; 
$securityToken = $data['security_token'] ?? '';

// Jika BUKAN deep link, load header admin seperti biasa
if (!$isDeepLink) {
    require_once __DIR__ . '/../../layouts/header.php';
    $user = $data['user'] ?? [];
    $csrf_token = $data['csrf_token'] ?? '';
} else {
    // Jika Deep Link, buat HTML wrapper sederhana + Tailwind CDN (karena header admin tidak dimuat)
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verifikasi Cepat</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100">';
    // Header Sederhana untuk Mode Tamu
    echo '<div class="bg-white shadow-sm py-4 px-6 mb-6 flex justify-between items-center"><h1 class="font-bold text-xl text-blue-600">TREVIO Quick Verify</h1><span class="text-xs text-gray-500">Mode Verifikasi Tanpa Login</span></div>';
}

if (!$payment) {
    echo "<div class='p-6 text-center text-red-500'>Data pembayaran tidak ditemukan.</div>";
    return;
}

$statusColors = [
    'pending' => 'bg-gray-100 text-gray-700',
    'uploaded' => 'bg-amber-100 text-amber-700', 
    'verified' => 'bg-green-100 text-green-700', 
    'rejected' => 'bg-red-100 text-red-700',
];
$statusKey = $payment['payment_status'] ?? 'pending';
$statusClass = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-600';
?>

<div class="min-h-[calc(100vh-4rem)] p-4 md:p-8">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <?php if(!$isDeepLink): ?>
                <a href="<?= $baseUrl ?>/admin/payments" class="flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar
                </a>
            <?php else: ?>
                <span class="text-sm text-gray-500">Silakan review bukti di bawah ini</span>
            <?php endif; ?>

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
                            </a>
                        <?php else: ?>
                            <div class="flex h-64 w-full flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-300 rounded-lg">
                                <span>Belum ada bukti upload</span>
                            </div>
                        <?php endif; ?>
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
                        <div class="px-6 py-4">
                            <span class="block text-sm text-slate-500 mb-1">Customer</span>
                            <p class="font-bold"><?= htmlspecialchars($payment['customer_name'] ?? 'Unknown') ?></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <h3 class="font-bold text-slate-900">Tindakan</h3>
                    </div>
                    <div class="p-6">
                        <?php if (in_array($statusKey, ['pending', 'uploaded'])): ?>
                            
                            <?php 
                                // Tentukan URL Target Form
                                $targetUrlReject = $isDeepLink ? "$baseUrl/admin/payment/process_deep_link" : "$baseUrl/admin/payments/reject";
                                $targetUrlConfirm = $isDeepLink ? "$baseUrl/admin/payment/process_deep_link" : "$baseUrl/admin/payments/confirm";
                            ?>

                            <div class="grid grid-cols-2 gap-4">
                                <form action="<?= $targetUrlReject ?>" method="POST" onsubmit="return confirm('Yakin tolak?');">
                                    <?php if($isDeepLink): ?>
                                        <input type="hidden" name="security_token" value="<?= $securityToken ?>">
                                        <input type="hidden" name="action" value="reject">
                                    <?php else: ?>
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <?php endif; ?>
                                    
                                    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                    
                                    <div class="mb-3">
                                         <select name="reason" class="w-full rounded border-slate-300 text-sm">
                                             <option value="Bukti tidak valid">Bukti tidak valid</option>
                                             <option value="Nominal salah">Nominal salah</option>
                                         </select>
                                    </div>
                                    <button type="submit" class="w-full rounded border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50">
                                        Tolak
                                    </button>
                                </form>

                                <form action="<?= $targetUrlConfirm ?>" method="POST" onsubmit="return confirm('Konfirmasi Valid?');">
                                    <?php if($isDeepLink): ?>
                                        <input type="hidden" name="security_token" value="<?= $securityToken ?>">
                                        <input type="hidden" name="action" value="confirm">
                                    <?php else: ?>
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <?php endif; ?>

                                    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                    
                                    <div class="mb-3 h-[38px]"></div> 
                                    
                                    <button type="submit" class="w-full rounded bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700">
                                        Konfirmasi Valid
                                    </button>
                                </form>
                            </div>

                        <?php else: ?>
                            <div class="text-center p-4 bg-slate-50 rounded">
                                <span class="font-bold text-slate-500">Transaksi Selesai</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
if ($isDeepLink) {
    echo '</body></html>';
} else {
    require_once __DIR__ . '/../../layouts/footer.php'; 
}
?>