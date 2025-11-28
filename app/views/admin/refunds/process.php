<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$refund = $data['refund'] ?? null;
$csrf_token = $data['csrf_token'] ?? '';

if (!$refund) {
    echo "Refund not found.";
    exit;
}

$statusColors = [
    'requested' => 'bg-yellow-100 text-yellow-700',
    'approved' => 'bg-blue-100 text-blue-700',
    'completed' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
];

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="flex min-h-[calc(100vh-var(--header-height,4rem))] bg-slate-50">
    <main class="flex-1 container mx-auto p-6 md:p-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="<?= $baseUrl ?>/admin/refunds" class="text-accent hover:underline text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar
                </a>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">Proses Refund #<?= $refund['id'] ?></h1>
            </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-lg font-bold text-slate-900">Detail Permintaan</h2>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusColors[$refund['refund_status']] ?>">
                            <?= ucfirst($refund['refund_status']) ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-slate-500">Nominal Refund</p>
                            <p class="text-xl font-bold text-slate-900">Rp <?= number_format($refund['refund_amount'], 0, ',', '.') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Alasan</p>
                            <p class="text-base text-slate-900"><?= htmlspecialchars($refund['reason']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Bank Tujuan</p>
                            <p class="font-medium text-slate-900"><?= htmlspecialchars($refund['bank_name']) ?></p>
                            <p class="text-sm"><?= htmlspecialchars($refund['account_number']) ?> a.n <?= htmlspecialchars($refund['account_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Tanggal Request</p>
                            <p class="font-medium text-slate-900"><?= date('d M Y H:i', strtotime($refund['requested_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Informasi Booking Asal</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Booking Code</span>
                            <span class="font-medium text-accent">#<?= htmlspecialchars($refund['booking_code']) ?></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Hotel</span>
                            <span class="font-medium"><?= htmlspecialchars($refund['hotel_name']) ?></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Tipe Kamar</span>
                            <span class="font-medium"><?= htmlspecialchars($refund['room_type']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Customer</span>
                            <span class="font-medium"><?= htmlspecialchars($refund['customer_name']) ?> (<?= htmlspecialchars($refund['customer_email']) ?>)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <?php if ($refund['refund_status'] === 'requested'): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4">Aksi Admin</h2>
                        
                        <form action="<?= $baseUrl ?>/admin/refunds/approve" method="POST" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">
                            
                            <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Persetujuan (Opsional)</label>
                            <textarea name="admin_notes" rows="2" class="w-full rounded-lg border border-slate-300 p-2 text-sm mb-3" placeholder="OK, data valid..."></textarea>
                            
                            <button type="submit" class="w-full flex justify-center items-center gap-2 bg-green-600 text-white px-4 py-2.5 rounded-lg hover:bg-green-700 transition font-medium" onclick="return confirm('Setujui permintaan refund ini?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Setujui Refund
                            </button>
                        </form>

                        <hr class="my-4 border-slate-100">

                        <form action="<?= $baseUrl ?>/admin/refunds/reject" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">
                            
                            <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                            <textarea name="rejection_reason" rows="2" class="w-full rounded-lg border border-slate-300 p-2 text-sm mb-3" required placeholder="Data tidak sesuai..."></textarea>
                            
                            <button type="submit" class="w-full flex justify-center items-center gap-2 bg-red-50 text-red-600 border border-red-200 px-4 py-2.5 rounded-lg hover:bg-red-100 transition font-medium" onclick="return confirm('Tolak permintaan refund ini?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak Refund
                            </button>
                        </form>
                    </div>

                <?php elseif ($refund['refund_status'] === 'approved'): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-l-4 border-l-blue-500">
                        <h2 class="text-lg font-bold text-slate-900 mb-2">Upload Bukti Transfer</h2>
                        <p class="text-sm text-slate-600 mb-4">Permintaan telah disetujui. Silakan transfer dana ke rekening customer lalu upload bukti di sini untuk menyelesaikan proses.</p>
                        
                        <form action="<?= $baseUrl ?>/admin/refunds/complete" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-2">File Bukti (JPG/PNG/PDF)</label>
                                <input type="file" name="refund_receipt" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition font-medium">
                                Selesaikan Refund
                            </button>
                        </form>
                    </div>

                <?php elseif ($refund['refund_status'] === 'completed'): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-green-700 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Refund Selesai
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-slate-500">Diselesaikan Oleh</p>
                                <p class="font-medium text-slate-900">Admin ID: <?= $refund['processed_by'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Waktu Selesai</p>
                                <p class="font-medium text-slate-900"><?= date('d M Y H:i', strtotime($refund['completed_at'])) ?></p>
                            </div>
                            <?php if (!empty($refund['refund_receipt'])): ?>
                                <div class="pt-2">
                                    <a href="<?= $baseUrl . '/' . $refund['refund_receipt'] ?>" target="_blank" class="block w-full text-center bg-slate-100 text-slate-700 py-2 rounded-lg text-sm hover:bg-slate-200">
                                        Lihat Bukti Transfer
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($refund['refund_status'] === 'rejected'): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-red-700 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Refund Ditolak
                        </h2>
                        <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                            <p class="text-sm text-red-800 font-medium">Alasan Penolakan:</p>
                            <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($refund['rejection_reason']) ?></p>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-slate-500">Ditolak Oleh</p>
                            <p class="font-medium text-slate-900">Admin ID: <?= $refund['processed_by'] ?></p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>