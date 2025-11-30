<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$payments = $data['payments'] ?? [];
$currentStatus = $data['current_status'] ?? 'pending';
$pendingCount = $data['pending_count'] ?? 0;

$statusColors = [
    'pending' => 'bg-gray-100 text-gray-700',
    'uploaded' => 'bg-amber-100 text-amber-700',
    'verified' => 'bg-emerald-100 text-emerald-700',
    'rejected' => 'bg-red-100 text-red-700',
];

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="flex min-h-[calc(100vh-var(--header-height,4rem))] bg-slate-50">
    <aside class="hidden w-64 border-r border-slate-200 bg-white lg:block">
        <div class="p-6">
            <nav class="space-y-1">
                <a href="<?= $baseUrl ?>/admin/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">Dashboard</a>
                <a href="<?= $baseUrl ?>/admin/payments" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">Payments</a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 overflow-auto p-6 md:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Verifikasi Pembayaran</h1>
            <p class="mt-2 text-slate-600">Kelola konfirmasi pembayaran booking.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Perlu Verifikasi</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900"><?= $pendingCount ?></p>
                        <p class="mt-1 text-xs text-amber-600">Status Pending & Uploaded</p>
                    </div>
                    <div class="rounded-full bg-amber-100 p-3">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 border-b border-slate-200">
            <div class="flex gap-6 overflow-x-auto">
                <a href="<?= $baseUrl ?>/admin/payments?status=pending" class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'pending' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">Menunggu Verifikasi</a>
                <a href="<?= $baseUrl ?>/admin/payments?status=verified" class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'verified' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">Diterima (Verified)</a>
                <a href="<?= $baseUrl ?>/admin/payments?status=rejected" class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'rejected' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">Ditolak (Rejected)</a>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Booking ID</th>
                            <th class="px-6 py-4 font-semibold">Customer</th>
                            <th class="px-6 py-4 font-semibold">Jumlah Transfer</th>
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
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p>Belum ada data pembayaran.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900">
                                        <?= htmlspecialchars($p['booking_code'] ?? 'Unknown') ?>
                                    </span>
                                    <div class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($p['hotel_name'] ?? '-') ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900"><?= htmlspecialchars($p['customer_name'] ?? 'Guest') ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($p['customer_email'] ?? '-') ?></div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?php 
                                        // Prioritaskan 'transfer_amount' dari tabel payments, fallback ke 'booking_total'
                                        $amount = $p['transfer_amount'] ?? $p['booking_total'] ?? 0;
                                        echo 'Rp ' . number_format($amount, 0, ',', '.');
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= htmlspecialchars(str_replace('_', ' ', $p['payment_method'] ?? '-')) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $s = $p['payment_status'] ?? 'pending';
                                        $color = $statusColors[$s] ?? 'bg-slate-100 text-slate-600';
                                    ?>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $color ?>">
                                        <?= ucfirst($s) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    <?= isset($p['created_at']) ? date('d M Y H:i', strtotime($p['created_at'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= $baseUrl ?>/admin/payments/verify/<?= $p['id'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition shadow-sm">
                                        Periksa
                                    </a>
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