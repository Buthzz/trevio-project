<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <div class="bg-white shadow-sm p-6 flex items-center gap-4">
            <a href="index" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Check-in Tamu</h1>
                <p class="text-gray-500 text-sm mt-1">Lakukan proses check-in untuk tamu.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Check-in -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800">Form Check-in</h2>
                        </div>

                        <form action="/owner/bookings/checkin/process" method="POST" class="p-6 space-y-6">
                            <!-- CSRF Token for form security - prevents Cross-Site Request Forgery attacks -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            
                            <!-- Nomor Pemesanan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Pemesanan / Booking ID *</label>
                                <div class="flex gap-2">
                                    <input type="text" name="booking_id" placeholder="Masukkan nomor pemesanan" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <button type="button" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition">
                                        Cari
                                    </button>
                                </div>
                            </div>

                            <!-- Info Tamu -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-semibold text-gray-800 mb-3">Informasi Tamu</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Nama Tamu</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">No. Telepon</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tipe Identitas</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Pemesanan -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-semibold text-gray-800 mb-3">Informasi Pemesanan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Hotel</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tipe Kamar</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Check-in</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Check-out</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Jumlah Malam</p>
                                        <p class="font-semibold text-gray-800">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Harga</p>
                                        <p class="font-semibold text-gray-800 text-blue-600">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Kamar yang Ditugaskan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kamar yang Ditugaskan *</label>
                                <select name="room_assignment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">-- Pilih Kamar --</option>
                                    <option value="101">Room 101</option>
                                    <option value="102">Room 102</option>
                                    <option value="103">Room 103</option>
                                </select>
                            </div>

                            <!-- Status Pembayaran -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="payment_status" value="paid" class="w-4 h-4" checked>
                                        <span class="text-gray-700">Sudah Lunas</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="payment_status" value="partial" class="w-4 h-4">
                                        <span class="text-gray-700">Pembayaran Sebagian</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="payment_status" value="pending" class="w-4 h-4">
                                        <span class="text-gray-700">Belum Dibayar</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Catatan Check-in -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Check-in</label>
                                <textarea name="notes" placeholder="Tambahkan catatan jika ada..." rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3 pt-6">
                                <a href="index" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                                    Batal
                                </a>
                                <button type="submit" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition">
                                    ✓ Selesaikan Check-in
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Instruksi & Tips -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Petunjuk Check-in</h3>
                        <div class="space-y-4 text-sm text-gray-600">
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">1. Cari Pemesanan</p>
                                <p>Masukkan nomor pemesanan tamu dan klik tombol cari untuk memuat data pemesanan.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">2. Verifikasi Data</p>
                                <p>Periksa kembali informasi tamu dan detail pemesanan yang tertera.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">3. Pilih Kamar</p>
                                <p>Tentukan kamar yang akan ditugaskan untuk tamu sesuai dengan tipe kamar yang dipesan.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">4. Konfirmasi Pembayaran</p>
                                <p>Pastikan status pembayaran sudah sesuai sebelum menyelesaikan check-in.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">5. Selesaikan</p>
                                <p>Klik tombol "Selesaikan Check-in" untuk menyelesaikan proses.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Check-ins -->
                    <div class="bg-white rounded-lg shadow p-6 mt-6">
                        <h3 class="font-bold text-gray-800 mb-4">Check-in Terakhir</h3>
                        <div class="space-y-3 text-sm">
                            <div class="pb-3 border-b border-gray-200">
                                <p class="font-semibold text-gray-800">Budi Santoso</p>
                                <p class="text-gray-600">Room 101 • Kemarin 14:30</p>
                            </div>
                            <div class="pb-3 border-b border-gray-200">
                                <p class="font-semibold text-gray-800">Siti Nurhaliza</p>
                                <p class="text-gray-600">Room 205 • 2 hari lalu</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Ahmad Wijaya</p>
                                <p class="text-gray-600">Room 102 • 3 hari lalu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
