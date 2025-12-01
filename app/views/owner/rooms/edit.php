<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-[calc(100vh-70px)] bg-gray-50 overflow-hidden">
    <div class="flex-shrink-0 hidden md:block">
        <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    </div>
    
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <main class="flex-1 bg-gray-50">
            
            <div class="bg-white border-b border-gray-200 px-8 py-6 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>/owner/rooms" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Kamar</h1>
                        <p class="text-sm text-gray-500">Perbarui detail, harga, dan ketersediaan kamar.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="<?= BASE_URL ?>/owner/rooms" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" form="editRoomForm" class="px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <div class="p-8 max-w-5xl mx-auto">
                <form id="editRoomForm" action="/owner/rooms/update" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id'] ?? ''); ?>">
                    
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informasi Dasar</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="hotel_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Hotel <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="hotel_id" id="hotel_id" class="block w-full appearance-none rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors cursor-pointer" required>
                                        <option value="">-- Pilih Hotel --</option>
                                        <?php if (!empty($hotels)): ?>
                                            <?php foreach ($hotels as $h): ?>
                                                <option value="<?= $h['id'] ?>" <?= ($h['id'] == ($room['hotel_id'] ?? '')) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($h['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Tidak ada hotel tersedia</option>
                                        <?php endif; ?>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="room_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kamar / Nomor <span class="text-red-500">*</span></label>
                                    <input type="text" name="room_name" id="room_name" 
                                           value="<?php echo htmlspecialchars($room['room_name'] ?? $room['room_type'] ?? ''); ?>" 
                                           class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="Contoh: Deluxe Room 101" required>
                                </div>
                                <div>
                                    <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kamar <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select name="room_type" id="room_type" class="block w-full appearance-none rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors cursor-pointer" required>
                                            <?php 
                                            $types = ['Single', 'Double', 'Twin', 'Suite', 'Deluxe', 'Family'];
                                            $currentType = $room['room_type'] ?? '';
                                            foreach ($types as $type): 
                                            ?>
                                                <option value="<?= strtolower($type) ?>" <?= (strtolower($currentType) == strtolower($type)) ? 'selected' : '' ?>><?= $type ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kamar</label>
                                <textarea name="description" id="description" rows="4" 
                                          class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                          placeholder="Jelaskan fasilitas, pemandangan, dan keunggulan kamar ini..."><?php echo htmlspecialchars($room['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Harga & Ketersediaan</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga per Malam (Rp) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="price" id="price" 
                                               value="<?php echo htmlspecialchars($room['price_per_night'] ?? ''); ?>" 
                                               class="block w-full pl-10 rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                               placeholder="0" min="0" step="1000" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                                    <input type="number" name="capacity" id="capacity" 
                                           value="<?php echo htmlspecialchars($room['capacity'] ?? '2'); ?>" 
                                           class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           min="1" required>
                                </div>
                                <div>
                                    <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Unit (Stok) <span class="text-red-500">*</span></label>
                                    <div class="space-y-3">
                                        <input type="number" name="total_rooms" id="total_rooms" 
                                               value="<?php echo htmlspecialchars($room['total_slots'] ?? '1'); ?>" 
                                               class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                               min="1" required>
                                        
                                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                            <label class="block text-xs font-semibold text-blue-900 mb-2">Tambah Stok</label>
                                            <div class="flex gap-2">
                                                <input type="number" id="add_stock_amount" placeholder="0" min="1"
                                                       class="block w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                                                <button type="button" id="btn_add_stock"
                                                        class="whitespace-nowrap px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    + Tambah
                                                </button>
                                            </div>
                                            <p class="mt-1 text-xs text-blue-700">Masukkan jumlah penambahan, lalu klik tombol Tambah.</p>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Total kamar fisik yang tersedia.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Fasilitas Kamar</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php 
                                $facilities = [
                                    'ac' => 'AC', 'wifi' => 'WiFi', 'tv' => 'TV', 'bathroom' => 'Kamar Mandi Dalam',
                                    'shower' => 'Shower Air Panas', 'balcony' => 'Balkon', 'minibar' => 'Mini Bar',
                                    'safe' => 'Brankas', 'desk' => 'Meja Kerja', 'hairdryer' => 'Hair Dryer'
                                ];
                                
                                // Handle JSON decode if needed
                                $currentFacilities = $room['facilities'] ?? [];
                                if (is_string($currentFacilities)) {
                                    $currentFacilities = json_decode($currentFacilities, true) ?? [];
                                }
                                
                                foreach ($facilities as $key => $label): 
                                ?>
                                <label class="relative flex items-center p-4 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition-all hover:border-blue-300 select-none">
                                    <input type="checkbox" name="facilities[]" value="<?= $key ?>" 
                                           class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition duration-150 ease-in-out"
                                           <?php echo in_array($key, $currentFacilities) ? 'checked' : ''; ?>>
                                    <span class="ml-3 text-base text-gray-700 font-medium"><?= $label ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Foto Kamar</h3>
                        </div>
                        <div class="p-8">
                            <div class="flex flex-col md:flex-row items-start gap-8">
                                <div class="flex-shrink-0 w-full md:w-auto">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</p>
                                    <div class="aspect-video w-full md:w-64 rounded-lg border border-gray-200 overflow-hidden bg-gray-100 relative group">
                                        <img src="<?php echo htmlspecialchars($room['main_image'] ?? 'https://via.placeholder.com/300x200?text=No+Image'); ?>" 
                                             alt="Current Room Image" 
                                             class="w-full h-full object-cover">
                                    </div>
                                </div>
                                <div class="flex-1 w-full">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Ganti Foto</p>
                                    <label class="flex flex-col items-center justify-center w-full h-40 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-xl appearance-none cursor-pointer hover:border-blue-500 hover:bg-blue-50 group" onclick="document.getElementById('room_photo').click()">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <div class="p-3 rounded-full bg-gray-100 group-hover:bg-blue-100 mb-3 transition-colors">
                                                <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <p class="mb-2 text-sm text-gray-600"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag and drop</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF (Max 5MB)</p>
                                        </div>
                                        <input type="file" name="room_photo" id="room_photo" class="hidden" accept="image/png, image/jpeg, image/jpg">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
                
                <div class="h-20"></div>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('btn_add_stock').addEventListener('click', function() {
        const totalInput = document.getElementById('total_rooms');
        const addInput = document.getElementById('add_stock_amount');
        
        // Ambil nilai saat ini, jika kosong anggap 0
        const currentStock = parseInt(totalInput.value) || 0;
        const addAmount = parseInt(addInput.value) || 0;
        
        if (addAmount > 0) {
            // Hitung total baru
            const newTotal = currentStock + addAmount;
            
            // Update nilai input utama
            totalInput.value = newTotal;
            
            // Reset input penambah
            addInput.value = '';
            
            // Efek visual (Flash Hijau) untuk memberitahu user bahwa stok sudah bertambah
            const originalBg = totalInput.style.backgroundColor;
            const originalBorder = totalInput.style.borderColor;
            
            totalInput.style.transition = 'all 0.5s ease';
            totalInput.style.backgroundColor = '#dcfce7'; // Hijau muda
            totalInput.style.borderColor = '#16a34a'; // Hijau
            
            // Kembalikan warna normal setelah 1 detik
            setTimeout(() => {
                totalInput.style.backgroundColor = originalBg;
                totalInput.style.borderColor = originalBorder;
            }, 1000);
            
            // Optional: Focus kembali ke input penambah jika ingin nambah lagi
            addInput.focus();
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>