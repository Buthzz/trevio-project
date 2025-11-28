<?php
// Base URL Helper
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Helper Rupiah
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return 'Rp ' . number_format($angka, 0, ',', '.'); }
}

// Ambil data dari controller
$selectedPayment = $data['selectedPayment'] ?? null;
$payments = $data['payments'] ?? [];
$stats = $data['stats'] ?? ['revenue' => 0, 'verified' => 0, 'pending' => 0];
$filters = $data['filters'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';

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

    <main class="flex-1 overflow-auto p-6 md:p-8">
        
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

        <?php if ($selectedPayment): ?>
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <a href="<?= $baseUrl ?>/admin/payments" class="text-accent hover:underline text-sm font-medium">← Kembali ke List</a>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">Verifikasi Pembayaran</h1>
                    <p class="mt-1 text-slate-600">ID: #<?= $selectedPayment['id'] ?> | Booking: #<?= $selectedPayment['booking_code'] ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Detail Transaksi</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-slate-500">Total Transfer</p>
                                <p class="text-xl font-bold text-slate-900"><?= formatRupiah($selectedPayment['transfer_amount']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Metode</p>
                                <p class="font-semibold text-slate-900 capitalize"><?= str_replace('_', ' ', $selectedPayment['payment_method']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Bank Asal</p>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['transfer_from_bank']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Detail Akun</p>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['payment_notes'] ?? '-') ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Waktu Upload</p>
                                <p class="font-semibold text-slate-900"><?= date('d M Y H:i', strtotime($selectedPayment['uploaded_at'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Detail Booking</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-slate-500">Hotel</p>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['hotel_name']) ?></p>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars($selectedPayment['hotel_location']) ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-slate-500">Check-in / Out</p>
                                    <p class="font-medium text-slate-900">
                                        <?= date('d M', strtotime($selectedPayment['check_in_date'])) ?> - 
                                        <?= date('d M Y', strtotime($selectedPayment['check_out_date'])) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Kamar</p>
                                    <p class="font-medium text-slate-900"><?= $selectedPayment['num_rooms'] ?>x <?= $selectedPayment['room_type'] ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Tamu</p>
                                <p class="font-medium text-slate-900"><?= htmlspecialchars($selectedPayment['customer_name']) ?> (<?= $selectedPayment['customer_email'] ?>)</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Bukti Transfer</h2>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                            <?php 
                            $proofPath = $baseUrl . '/uploads/payments/' . $selectedPayment['payment_proof']; 
                            $ext = pathinfo($selectedPayment['payment_proof'], PATHINFO_EXTENSION);
                            ?>
                            
                            <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])): ?>
                                <img src="<?= $proofPath ?>" alt="Bukti Transfer" class="w-full h-auto object-contain max-h-[500px]">
                            <?php elseif (strtolower($ext) === 'pdf'): ?>
                                <embed src="<?= $proofPath ?>" type="application/pdf" width="100%" height="500px" />
                            <?php else: ?>
                                <div class="p-8 text-center">
                                    <p class="text-slate-500">File tidak dapat dipreview. <a href="<?= $proofPath ?>" target="_blank" class="text-accent underline">Download File</a></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 text-center">
                             <a href="<?= $proofPath ?>" target="_blank" class="text-sm font-semibold text-accent hover:underline">Buka Gambar Asli di Tab Baru</a>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sticky top-6">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Aksi Verifikasi</h2>
                        
                        <?php if ($selectedPayment['payment_status'] === 'verified'): ?>
                            <div class="rounded-lg bg-emerald-50 p-4 text-center border border-emerald-200">
                                <p class="font-bold text-emerald-700">Sudah Diverifikasi</p>
                                <p class="text-xs text-emerald-600 mt-1">Oleh Admin pada <?= date('d M Y H:i', strtotime($selectedPayment['verified_at'])) ?></p>
                            </div>
                        <?php else: ?>
                            <form action="<?= $baseUrl ?>/admin/payments/process" method="POST" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="payment_id" value="<?= $selectedPayment['id'] ?>">
                                
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Catatan Admin</label>
                                    <textarea name="admin_note" rows="3" class="w-full rounded-lg border border-slate-300 p-2 text-sm focus:border-accent focus:ring-accent" placeholder="Contoh: Pembayaran valid, data sesuai."></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <button type="submit" name="action" value="approve" class="rounded-lg bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700 transition" onclick="return confirm('Terima pembayaran ini dan konfirmasi booking?')">
                                        Terima
                                    </button>
                                    <button type="submit" name="action" value="reject" class="rounded-lg bg-red-600 px-4 py-2 text-white font-semibold hover:bg-red-700 transition" onclick="return confirm('Tolak pembayaran ini? User harus upload ulang.')">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Payment Verification</h1>
                <p class="mt-2 text-slate-600">Verifikasi pembayaran masuk dari customer.</p>
            </div>

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

            <div class="mb-6 flex flex-col gap-4 lg:flex-row">
                <form method="GET" class="flex flex-1 gap-4">
                    <select name="status" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:ring-accent" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending_verification" <?= ($filters['status'] === 'pending_verification') ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                        <option value="verified" <?= ($filters['status'] === 'verified') ? 'selected' : '' ?>>Terverifikasi</option>
                        <option value="failed" <?= ($filters['status'] === 'failed') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </form>
            </div>

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
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Tidak ada data pembayaran.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $p): 
                                    $statusBadge = $statusColors[$p['payment_status']] ?? 'bg-slate-100 text-slate-600';
                                    $statusText = ucfirst(str_replace('_', ' ', $p['payment_status']));
                                ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-slate-900">#<?= htmlspecialchars($p['booking_code']) ?></span>
                                        <p class="text-xs text-slate-500">Pay ID: <?= $p['id'] ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-slate-900"><?= htmlspecialchars($p['customer_name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($p['hotel_name']) ?></p>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        <?= formatRupiah($p['transfer_amount']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <?= date('d M Y H:i', strtotime($p['uploaded_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $statusBadge ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="?id=<?= $p['id'] ?>" class="inline-flex items-center rounded-lg bg-accent px-3 py-1.5 text-xs font-semibold text-white hover:bg-accentLight transition">
                                            <?= ($p['payment_status'] === 'pending_verification') ? 'Verifikasi' : 'Detail' ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>