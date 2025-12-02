<?php require_once '../app/views/layouts/header.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?= BASE_URL ?>" class="text-gray-700 hover:text-blue-600">Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <a href="<?= BASE_URL ?>/booking/history" class="text-gray-700 hover:text-blue-600">Riwayat</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-gray-500">Detail #<?= htmlspecialchars($booking['booking_code']) ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline"><?= $_SESSION['flash_success']; ?></span>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline"><?= $_SESSION['flash_error']; ?></span>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-white">Invoice Booking</h2>
                    <span class="bg-white text-blue-600 px-3 py-1 rounded-full text-sm font-bold">
                        <?= htmlspecialchars($booking['booking_status']) ?>
                    </span>
                </div>
                
                <div class="p-6">
                    <div class="flex items-start mb-6 pb-6 border-b border-gray-100">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($booking['hotel_name']) ?></h3>
                            <p class="text-gray-600"><?= htmlspecialchars($booking['room_type']) ?></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Check-in</p>
                            <p class="font-semibold text-gray-800"><?= date('d M Y', strtotime($booking['check_in_date'])) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Check-out</p>
                            <p class="font-semibold text-gray-800"><?= date('d M Y', strtotime($booking['check_out_date'])) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Durasi</p>
                            <p class="font-semibold text-gray-800"><?= $booking['num_nights'] ?> Malam</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jumlah Kamar</p>
                            <p class="font-semibold text-gray-800"><?= $booking['num_rooms'] ?> Unit</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Rincian Pembayaran</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Harga per malam (x<?= $booking['num_rooms'] ?>)</span>
                                <span>Rp <?= number_format($booking['price_per_night'] * $booking['num_rooms'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Total <?= $booking['num_nights'] ?> malam</span>
                                <span>Rp <?= number_format($booking['subtotal'], 0, ',', '.') ?></span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between items-center">
                                <span class="font-bold text-lg text-gray-800">Total Pembayaran</span>
                                <span class="font-bold text-xl text-blue-600">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-1 space-y-6">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Status Pesanan</h3>
                
                <?php if (isset($refund) && $refund): ?>
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-4">
                        <p class="font-bold text-orange-800">Pengajuan Refund: <?= ucfirst($refund['refund_status']) ?></p>
                        <p class="text-sm text-orange-700 mt-1">Alasan: <?= htmlspecialchars($refund['reason']) ?></p>
                        <?php if ($refund['refund_status'] == 'approved'): ?>
                            <p class="text-sm text-green-600 font-bold mt-2">Disetujui. Menunggu transfer admin.</p>
                        <?php elseif ($refund['refund_status'] == 'completed'): ?>
                             <p class="text-sm text-blue-600 font-bold mt-2">Selesai. Dana telah dikembalikan.</p>
                        <?php elseif ($refund['refund_status'] == 'rejected'): ?>
                            <p class="text-sm text-red-600 font-bold mt-2">Ditolak: <?= htmlspecialchars($refund['rejection_reason'] ?? '-') ?></p>
                        <?php else: ?>
                            <p class="text-sm text-orange-600 mt-2">Mohon tunggu verifikasi admin.</p>
                        <?php endif; ?>
                    </div>

                <?php elseif ($booking['booking_status'] == 'confirmed'): ?>
                    <div class="text-center py-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Pembayaran Lunas</h3>
                        <p class="mt-2 text-sm text-gray-500 mb-4">Transaksi berhasil. E-Ticket tersedia.</p>
                        
                        <a href="<?= BASE_URL ?>/booking/ticket/<?= $booking['booking_code'] ?>" class="block w-full text-center bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition mb-3">Download E-Ticket</a>
                        
                        <button onclick="toggleRefundModal()" class="block w-full text-center bg-white border border-red-500 text-red-500 font-bold py-2 px-4 rounded hover:bg-red-50 transition">
                            Ajukan Cancel & Refund
                        </button>
                    </div>

                <?php elseif ($booking['booking_status'] == 'pending_payment'): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <p class="text-sm text-yellow-700 mb-2">Segera lakukan pembayaran.</p>
                        <p class="font-bold">BCA: 8293-2910-2212</p>
                    </div>
                    <form action="<?= BASE_URL ?>/booking/uploadPayment" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        <input type="text" name="bank_name" required placeholder="Bank Pengirim" class="w-full mb-2 px-3 py-2 border rounded">
                        <input type="text" name="account_name" required placeholder="Nama Pemilik" class="w-full mb-2 px-3 py-2 border rounded">
                        <input type="file" name="payment_proof" required class="w-full mb-2 text-sm">
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded">Konfirmasi Bayar</button>
                    </form>
                <?php else: ?>
                    <div class="text-center py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full font-medium">Status: <?= $booking['booking_status'] ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="refundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-red-600 text-center mb-4">Pengajuan Refund</h3>
            <div class="mt-2 px-2 text-sm text-gray-600 mb-4">
                <p>Dana akan dikembalikan sesuai kebijakan. Slot kamar akan dikembalikan setelah refund disetujui.</p>
            </div>
            
            <form action="<?= BASE_URL ?>/booking/requestRefund" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Alasan Pembatalan</label>
                    <textarea name="reason" rows="3" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Jelaskan alasan refund..."></textarea>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Rekening Tujuan Refund</h4>
                    
                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs mb-1">Nama Bank</label>
                        <input type="text" name="bank_name" required placeholder="Contoh: BCA" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs mb-1">Nomor Rekening</label>
                        <input type="number" name="account_number" required placeholder="1234xxxx" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-xs mb-1">Atas Nama</label>
                        <input type="text" name="account_name" required placeholder="Nama Pemilik Rekening" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="toggleRefundModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Ajukan Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleRefundModal() {
        const modal = document.getElementById('refundModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>