<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-50 overflow-hidden">
    <div class="flex-shrink-0 hidden md:block">
        <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    </div>
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden overflow-x-hidden">
        
        <main class="flex-1 overflow-y-auto bg-gray-50 overflow-x-hidden">
            
            <div class="bg-white border-b border-gray-200 px-4 md:px-8 py-6 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>/owner/rooms" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Kamar Baru</h1>
                        <p class="text-sm text-gray-500">Isi detail di bawah untuk menambahkan tipe kamar baru.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="<?= BASE_URL ?>/owner/rooms" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" form="createRoomForm" class="px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Kamar
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-8 max-w-5xl mx-auto">
                <form id="createRoomForm" action="/owner/rooms/store" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informasi Dasar</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="hotel_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Hotel <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="hotel_id" id="hotel_id" class="block w-full appearance-none rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors cursor-pointer" required>
                                        <option value="">-- Pilih Hotel --</option>
                                        <?php if (!empty($hotels)): ?>
                                            <?php foreach ($hotels as $h): ?>
                                                <option value="<?= $h['id'] ?>" <?= (isset($selected_hotel) && $selected_hotel == $h['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($h['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Anda belum mendaftarkan hotel</option>
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
                                           class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="Contoh: Deluxe Room 101" required>
                                </div>
                                <div>
                                    <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kamar <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select name="room_type" id="room_type" class="block w-full appearance-none rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors cursor-pointer" required>
                                            <option value="">-- Pilih Tipe --</option>
                                            <option value="single">Single Room</option>
                                            <option value="double">Double Room</option>
                                            <option value="twin">Twin Room</option>
                                            <option value="suite">Suite</option>
                                            <option value="deluxe">Deluxe</option>
                                            <option value="family">Family Room</option>
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
                                          placeholder="Jelaskan fasilitas, pemandangan, dan keunggulan kamar ini..."></textarea>
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
                                               class="block w-full pl-10 rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                               placeholder="0" min="0" step="1000" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                                    <input type="number" name="capacity" id="capacity" 
                                           class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="1" min="1" required>
                                </div>
                                <div>
                                    <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Unit (Stok) <span class="text-red-500">*</span></label>
                                    <input type="number" name="total_rooms" id="total_rooms" 
                                           class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="1" min="1" required>
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
                                foreach ($facilities as $key => $label): 
                                ?>
                                <label class="relative flex items-center p-4 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition-all hover:border-blue-300 select-none">
                                    <input type="checkbox" name="facilities[]" value="<?= $key ?>" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition duration-150 ease-in-out">
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
                            <label class="flex flex-col items-center justify-center w-full h-48 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-2xl appearance-none cursor-pointer hover:border-blue-500 hover:bg-blue-50 group" onclick="document.getElementById('room_photo').click()">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="p-3 rounded-full bg-gray-100 group-hover:bg-blue-100 mb-3 transition-colors">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <p class="mb-2 text-sm text-gray-600"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG, JPEG (Maksimal 5MB)</p>
                                </div>
                                <input type="file" name="room_photo" id="room_photo" class="hidden" accept="image/jpeg,image/png,image/jpg" required>
                            </label>
                        </div>
                    </div>

                </form>
                
                <div class="h-20"></div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>