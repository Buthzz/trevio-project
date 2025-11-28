<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$refunds = $data['refunds'] ?? [];
$currentStatus = $data['current_status'] ?? 'all';
$pendingCount = $data['pending_count'] ?? 0;

$statusColors = [
    'requested' => 'bg-yellow-100 text-yellow-700',
    'approved' => 'bg-blue-100 text-blue-700',
    'completed' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
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
                <a href="<?= $baseUrl ?>/admin/refunds" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path></svg>
                    Refunds
                </a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 overflow-auto p-6 md:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Manajemen Refund</h1>
            <p class="mt-2 text-slate-600">Kelola permintaan pengembalian dana.</p>
        </div>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="mb-6 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700 border border-emerald-200">
                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Pending Requests</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900"><?= $pendingCount ?></p>
                        <p class="mt-1 text-xs text-red-600">Membutuhkan tindakan</p>
                    </div>
                    <div class="rounded-full bg-red-100 p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 border-b border-slate-200">
            <div class="flex gap-6 overflow-x-auto">
                <a href="<?= $baseUrl ?>/admin/refunds?status=requested" 
                   class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'requested' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                    Menunggu (Requested)
                </a>
                <a href="<?= $baseUrl ?>/admin/refunds?status=approved" 
                   class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'approved' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                    Disetujui (Approved)
                </a>
                <a href="<?= $baseUrl ?>/admin/refunds?status=completed" 
                   class="border-b-2 pb-3 text-sm font-medium transition whitespace-nowrap <?= $currentStatus === 'completed' ? 'border-accent text-accent' : 'border-transparent text-slate-500 hover:text-slate-700' ?>">
                    Selesai (Completed)
                </a>
                <a href="<?= $baseUrl ?>/admin/refunds?status=rejected" 
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
                            <th class="px-6 py-4 font-semibold">Booking / Hotel</th>
                            <th class="px-6 py-4 font-semibold">Customer</th>
                            <th class="px-6 py-4 font-semibold">Nominal</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Tanggal Request</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($refunds)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Tidak ada data refund pada status ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($refunds as $refund): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">#<?= htmlspecialchars($refund['booking_code']) ?></p>
                                    <p class="text-xs text-slate-500 truncate max-w-[200px]"><?= htmlspecialchars($refund['hotel_name']) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-900 font-medium"><?= htmlspecialchars($refund['customer_name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($refund['customer_email']) ?></p>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <?= 'Rp ' . number_format($refund['refund_amount'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $statusColors[$refund['refund_status']] ?? 'bg-gray-100' ?>">
                                        <?= ucfirst($refund['refund_status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    <?= date('d M Y H:i', strtotime($refund['requested_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= $baseUrl ?>/admin/refunds/process/<?= $refund['id'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                        Proses
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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