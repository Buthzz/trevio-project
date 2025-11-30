<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-[calc(100vh-70px)] bg-gray-100 overflow-hidden">
    
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-y-auto transition-all duration-300">
        
        <div class="bg-white shadow-sm p-6 flex items-center gap-4 sticky top-0 z-10">
            <a href="index" class="text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Check-in Tamu</h1>
                <p class="text-gray-500 text-sm mt-1">Proses kedatangan tamu dan penugasan kamar.</p>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gray-50">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Form Data
                            </h2>
                        </div>

                        <form action="/owner/bookings/checkin/process" method="POST" class="p-6 space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Pemesanan / Booking ID <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">#</span>
                                        </div>
                                        <input type="text" name="booking_id" placeholder="Contoh: TRV-2023001" class="w-full pl-7 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                                    </div>
                                    <button type="button" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Cari
                                    </button>
                                </div>
                            </div>

                            <div class="bg-blue-50/50 p-5 rounded-xl border border-blue-100 border-dashed">
                                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wide mb-4">Ringkasan Data Tamu</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase">Nama Tamu</p>
                                        <p class="font-medium text-gray-900 text-lg">-</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase">Kontak</p>
                                        <p class="font-medium text-gray-900">-</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase">Tipe Kamar</p>
                                        <p class="font-medium text-gray-900">-</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase">Durasi</p>
                                        <p class="font-medium text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kamar <span class="text-red-500">*</span></label>
                                    <select name="room_assignment" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-shadow bg-white" required>
                                        <option value="">-- Pilih Nomor Kamar --</option>
                                        <option value="101">Room 101 (Deluxe)</option>
                                        <option value="102">Room 102 (Deluxe)</option>
                                        <option value="103">Room 103 (Standard)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Hanya menampilkan kamar yang tersedia & bersih.</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pembayaran</label>
                                    <div class="flex flex-col gap-2 p-1">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="payment_status" value="paid" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500" checked>
                                            <span class="ml-2 text-sm text-gray-700">✅ Sudah Lunas (Paid)</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="payment_status" value="partial" class="w-4 h-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                            <span class="ml-2 text-sm text-gray-700">⚠️ Deposit / Sebagian</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="payment_status" value="pending" class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                            <span class="ml-2 text-sm text-gray-700">❌ Belum Dibayar (Pay at Hotel)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                                <textarea name="notes" placeholder="Misal: Tamu meminta early check-out, alergi makanan, dll." rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-shadow"></textarea>
                            </div>

                            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                                <a href="index" class="px-6 py-2.5 border border-gray-300 text-gray-600 rounded-lg font-medium hover:bg-gray-50 transition text-center">
                                    Batalkan
                                </a>
                                <button type="submit" class="flex-1 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Konfirmasi Check-in
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            SOP Check-in
                        </h3>
                        <ol class="relative border-l border-gray-200 ml-2 space-y-6">                  
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-4 ring-white">
                                    <span class="text-xs font-bold text-blue-600">1</span>
                                </span>
                                <h4 class="font-semibold text-gray-900 text-sm">Validasi Identitas</h4>
                                <p class="text-xs text-gray-500 mt-1">Minta KTP/Paspor tamu dan cocokkan dengan data booking.</p>
                            </li>
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-4 ring-white">
                                    <span class="text-xs font-bold text-blue-600">2</span>
                                </span>
                                <h4 class="font-semibold text-gray-900 text-sm">Deposit Kunci</h4>
                                <p class="text-xs text-gray-500 mt-1">Pastikan tamu membayar deposit kunci jika diperlukan.</p>
                            </li>
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-4 ring-white">
                                    <span class="text-xs font-bold text-blue-600">3</span>
                                </span>
                                <h4 class="font-semibold text-gray-900 text-sm">Penyerahan Kunci</h4>
                                <p class="text-xs text-gray-500 mt-1">Berikan kunci fisik atau kartu akses kamar.</p>
                            </li>
                        </ol>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800 text-sm">Baru Saja Check-in</h3>
                            <a href="#" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-50">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-xs">BS</div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Budi Santoso</p>
                                    <p class="text-xs text-gray-500">Room 101 • <span class="text-green-600">Lunas</span></p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Check-in: 14:30 WIB</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xs">SN</div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Siti Nurhaliza</p>
                                    <p class="text-xs text-gray-500">Room 205 • <span class="text-yellow-600">Deposit</span></p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Check-in: 12:15 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-auto">
            <?php include __DIR__ . '/../../layouts/footer.php'; ?>
        </div>
    </div>
</div>