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
                <h1 class="text-3xl font-bold text-gray-800">Tambah Kamar Baru</h1>
                <p class="text-gray-500 text-sm mt-1">Tambahkan kamar baru ke hotel Anda.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <div class="bg-white rounded-lg shadow max-w-4xl">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Detail Kamar</h2>
                </div>

                <form action="/owner/rooms/store" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- CSRF Token for form security - prevents Cross-Site Request Forgery attacks -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <!-- Pilih Hotel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hotel *</label>
                        <select name="hotel_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Hotel --</option>
                            <option value="1">Aria Centra Surabaya</option>
                        </select>
                    </div>

                    <!-- Nama dan Tipe Kamar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kamar *</label>
                            <input type="text" name="room_name" placeholder="Contoh: Room 101" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kamar *</label>
                            <select name="room_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="single">Single Room</option>
                                <option value="double">Double Room</option>
                                <option value="twin">Twin Room</option>
                                <option value="suite">Suite</option>
                                <option value="deluxe">Deluxe</option>
                            </select>
                        </div>
                    </div>

                    <!-- Harga dan Kapasitas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga per Malam (Rp) *</label>
                            <input type="number" name="price" placeholder="100000" min="0" step="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas Penghuni *</label>
                            <input type="number" name="capacity" placeholder="1" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Kamar *</label>
                            <input type="number" name="total_rooms" placeholder="5" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kamar</label>
                        <textarea name="description" placeholder="Jelaskan detail dan fitur kamar..." rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- Fasilitas Kamar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Fasilitas Kamar</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="ac" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">AC</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="wifi" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">WiFi</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="tv" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">TV</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="bathroom" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Kamar Mandi Pribadi</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="balcony" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Balkon</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="minibar" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Mini Bar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="safe" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Safe Box</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="shower" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Shower</span>
                            </label>
                        </div>
                    </div>

                    <!-- Foto Kamar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kamar *</label>
                        <!-- Note: Server-side validation required for file type, size (5MB max), and content verification -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition" onclick="document.getElementById('room_photo').click()">
                            <input type="file" name="room_photo" id="room_photo" class="hidden" accept="image/jpeg,image/png,image/jpg" required>
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <p class="text-gray-600">Drag dan drop foto di sini atau klik untuk browse</p>
                            <p class="text-gray-500 text-sm mt-1">Format: JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-6">
                        <a href="index" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition">
                            Simpan Kamar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
