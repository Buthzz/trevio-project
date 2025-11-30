<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$payments = $data['payments'] ?? [];
$currentStatus = $data['current_status'] ?? 'pending';
$pendingCount = $data['pending_count'] ?? 0;

// Warna badge status (Sesuaikan dengan ENUM di Database)
$statusColors = [
    'pending' => 'bg-gray-100 text-gray-700',
    'uploaded' => 'bg-amber-100 text-amber-700', // Status saat user sudah upload
    'verified' => 'bg-emerald-100 text-emerald-700', // Pengganti 'paid'
    'rejected' => 'bg-red-100 text-red-700', // Pengganti 'failed'
];

// Helper untuk label status yang lebih user friendly
$statusLabels = [
    'pending' => 'Menunggu Upload',
    'uploaded' => 'Perlu Verifikasi',
    'verified' => 'Diterima',
    'rejected' => 'Ditolak'
];

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="flex min-h-[calc(100vh-var(--header-height,4rem))] bg-slate-50">
    <aside class="hidden w-64 border-r border-slate-200 bg-white lg:block">
        <div class="p-6">
            <nav class="space-y-1">
                <a href="<?= $baseUrl ?>/admin/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path></svg>
                    Dashboard
                </a>
                <a href="<?= $baseUrl ?>/admin/payments" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Payments
                </a>
                </nav>
        </div>
    </aside>

    <main class="flex-1 overflow-auto p-6 md:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Verifikasi Pembayaran</h1>
            <p class="mt-2 text-slate-600">Cek bukti transfer dari customer.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Perlu Verifikasi</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900"><?= $pendingCount ?></p>
                        <p class="mt-1 text-xs text-amber-600">Pending & Uploaded</p>
                    </div>
                    <div class="rounded-full bg-amber-100 p-3">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 border-b border-slate-200">
            <div class="flex gap-6 overflow-x-auto">
                <a href="<?= $baseUrl ?>/admin/payments?status=pending" 
                   class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'pending' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                    Menunggu Verifikasi
                </a>
                <a href="<?= $baseUrl ?>/admin/payments?status=verified" 
                   class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'verified' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                    Berhasil (Verified)
                </a>
                <a href="<?= $baseUrl ?>/admin/payments?status=rejected" 
                   class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'rejected' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                    Ditolak (Rejected)
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Booking ID</th>
                            <th class="px-6 py-4 font-semibold">Customer</th>
                            <th class="px-6 py-4 font-semibold">Total Bayar</th>
                            <th class="px-6 py-4 font-semibold">Metode</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <p>Tidak ada data pembayaran pada status ini.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900">#<?= htmlspecialchars($p['booking_code'] ?? '-') ?></span>
                                    <div class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($p['hotel_name'] ?? 'Unknown Hotel') ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900"><?= htmlspecialchars($p['customer_name'] ?? 'Guest') ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($p['customer_email'] ?? '-') ?></div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    Rp <?= number_format($p['total_price'] ?? $p['booking_total'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= htmlspecialchars(str_replace('_', ' ', $p['payment_method'] ?? 'Transfer')) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $statusKey = $p['payment_status'] ?? 'pending';
                                        $statusClass = $statusColors[$statusKey] ?? 'bg-slate-100 text-slate-600';
                                        $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                                    ?>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    <?= isset($p['created_at']) ? date('d M Y H:i', strtotime($p['created_at'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if(in_array($p['payment_status'], ['pending', 'uploaded'])): ?>
                                        <a href="<?= $baseUrl ?>/admin/payments/verify/<?= $p['id'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition shadow-sm">
                                            Verifikasi
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $baseUrl ?>/admin/payments/verify/<?= $p['id'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-200 transition shadow-sm">
                                            Detail
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>