<?php
// Base URL Helper
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Helper Rupiah
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return 'Rp ' . number_format((float)$angka, 0, ',', '.'); }
}

// Ambil data dari controller
$payments = $data['payments'] ?? [];
$stats = $data['stats'] ?? ['revenue' => 0, 'verified' => 0, 'pending' => 0];
$filters = $data['filters'] ?? [];

// Status Colors
$statusColors = [
    'verified' => 'bg-emerald-100 text-emerald-700',
    'pending_verification' => 'bg-amber-100 text-amber-700',
    'failed' => 'bg-red-100 text-red-700',
    'uploaded' => 'bg-blue-100 text-blue-700'
];

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="flex min-h-[calc(100vh-4rem)] bg-slate-50">
    <!-- Sidebar -->
    <aside class="hidden w-64 border-r border-slate-200 bg-white lg:block">
        <div class="p-6">
            <nav class="space-y-1">
                <a href="<?= $baseUrl ?>/admin/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path></svg>
                    Dashboard
                </a>
                <a href="<?= $baseUrl ?>/admin/hotels" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path></svg>
                    Hotels
                </a>
                <a href="<?= $baseUrl ?>/admin/payments" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Payments
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto p-6 md:p-8">
        
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Verifikasi Pembayaran</h1>
            <p class="mt-2 text-slate-600">Verifikasi dan kelola semua transaksi pembayaran masuk.</p>
        </div>

        <!-- Flash Message -->
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Total Revenue</p>
                <p class="mt-2 text-2xl font-bold text-slate-900"><?= formatRupiah($stats['revenue']) ?></p>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Verified</p>
                <p class="mt-2 text-2xl font-bold text-slate-900"><?= number_format($stats['verified']) ?></p>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                <p class="text-sm font-medium text-slate-600">Pending</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 text-amber-600"><?= number_format($stats['pending']) ?></p>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col gap-4 lg:flex-row">
            <form method="GET" class="flex flex-1 gap-4 items-center">
                <select name="status" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:ring-accent" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending_verification" <?= ($filters['status'] === 'pending_verification') ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                    <option value="verified" <?= ($filters['status'] === 'verified') ? 'selected' : '' ?>>Terverifikasi</option>
                    <option value="failed" <?= ($filters['status'] === 'failed') ? 'selected' : '' ?>>Ditolak</option>
                </select>
                <a href="<?= $baseUrl ?>/admin/payments" class="text-sm text-slate-500 hover:text-accent hover:underline">Reset Filter</a>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Booking ID</th>
                            <th class="px-6 py-4 font-semibold">User / Customer</th>
                            <th class="px-6 py-4 font-semibold">Jumlah</th>
                            <th class="px-6 py-4 font-semibold">Tanggal Upload</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p>Tidak ada data pembayaran yang ditemukan.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): 
                                $statusBadge = $statusColors[$p['payment_status'] ?? 'uploaded'] ?? 'bg-slate-100 text-slate-600';
                                $statusText = ucfirst(str_replace('_', ' ', $p['payment_status'] ?? 'unknown'));
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-900">#<?= htmlspecialchars($p['booking_code'] ?? 'N/A') ?></span>
                                    <p class="text-xs text-slate-500">Pay ID: <?= $p['id'] ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900"><?= htmlspecialchars($p['customer_name'] ?? 'Unknown User') ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($p['hotel_name'] ?? 'Unknown Hotel') ?></p>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?= formatRupiah($p['transfer_amount'] ?? 0) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= isset($p['uploaded_at']) ? date('d M Y H:i', strtotime($p['uploaded_at'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $statusBadge ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= $baseUrl ?>/admin/payments/verify/<?= $p['id'] ?>" 
                                       class="inline-flex items-center rounded-lg bg-accent px-3 py-1.5 text-xs font-semibold text-white hover:bg-accentLight transition">
                                        <?= ($p['payment_status'] === 'pending_verification') ? 'Verifikasi' : 'Detail' ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Footer Table (Pagination Placeholder) -->
            <div class="border-t border-slate-200 px-6 py-4 bg-slate-50">
                <p class="text-xs text-slate-500">Menampilkan <?= count($payments) ?> data</p>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>