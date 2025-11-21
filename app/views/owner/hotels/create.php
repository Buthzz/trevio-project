<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <div class="bg-white shadow-sm p-6 flex items-center gap-4">
            <a href="hotels" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Tambah Hotel Baru</h1>
                <p class="text-gray-500 text-sm mt-1">Isi form di bawah untuk menambah hotel baru.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <div class="bg-white rounded-lg shadow max-w-4xl">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Informasi Hotel</h2>
                </div>

                <form action="/owner/hotels/store" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- CSRF Token for form security - prevents Cross-Site Request Forgery attacks -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <!-- Nama Hotel -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Hotel *</label>
                            <input type="text" name="hotel_name" placeholder="Masukkan nama hotel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten *</label>
                            <input type="text" name="city" placeholder="Contoh: Surabaya" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                        <input type="text" name="address" placeholder="Jalan Sudirman No. 1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Hotel *</label>
                        <textarea name="description" placeholder="Jelaskan fasilitas dan keunggulan hotel Anda..." rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>

                    <!-- Kontak -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                            <input type="tel" name="phone" placeholder="+62..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" placeholder="hotel@example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Fasilitas Hotel</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="wifi" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">WiFi Gratis</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="pool" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Kolam Renang</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="gym" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Gym</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="restaurant" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Restoran</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="bar" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Bar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facilities[]" value="parking" class="w-4 h-4 text-blue-500 rounded">
                                <span class="text-gray-700">Parkir Gratis</span>
                            </label>
                        </div>
                    </div>

                    <!-- Foto Hotel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Hotel *</label>
                        <!-- Note: Server-side validation required for file type, size (5MB max), and content verification -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition" onclick="document.getElementById('hotel_photo').click()">
                            <input type="file" name="hotel_photo" id="hotel_photo" class="hidden" accept="image/jpeg,image/png,image/jpg" required>
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <p class="text-gray-600">Drag dan drop foto di sini atau klik untuk browse</p>
                            <p class="text-gray-500 text-sm mt-1">Format: JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-6">
                        <a href="hotels" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition">
                            Simpan Hotel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
