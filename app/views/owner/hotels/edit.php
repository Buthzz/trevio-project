// Fixed Perbaikan untuk edit.php
<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-50 overflow-hidden">
    
    <div class="flex-shrink-0 hidden md:block">
        <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    </div>
    
    <div class="flex-1 flex flex-col min-w-0 h-full">
        
        <header class="bg-white border-b border-gray-200 shadow-sm z-20 flex-shrink-0">
            <div class="px-6 py-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>/owner/hotels" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Hotel</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Perbarui informasi properti Anda.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="<?= BASE_URL ?>/owner/hotels" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                        Batal
                    </a>
                    <button type="submit" form="editHotelForm" class="px-6 py-2.5 border border-transparent rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8 scroll-smooth">
            <div class="max-w-5xl mx-auto pb-10">
                
                <form id="editHotelForm" action="/owner/hotels/update" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" name="hotel_id" value="<?php echo htmlspecialchars($hotel['id'] ?? ''); ?>">

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">Informasi Dasar</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700">Nama Hotel <span class="text-red-500">*</span></label>
                                    <input type="text" name="hotel_name" value="<?php echo htmlspecialchars($hotel['name'] ?? ''); ?>" 
                                           class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="Contoh: Grand Hotel Trevio" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" name="city" list="city_list" value="<?php echo htmlspecialchars($hotel['city'] ?? ''); ?>" 
                                               class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors" 
                                               placeholder="Ketik untuk mencari..." required>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                            <!-- <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg> -->
                                        </div>
                                    </div>
                                    <datalist id="city_list">
                                        <option value="Jakarta"><option value="Surabaya"><option value="Bandung"><option value="Yogyakarta"><option value="Bali"><option value="Medan"><option value="Semarang"><option value="Makassar">
                                    </datalist>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="address" value="<?php echo htmlspecialchars($hotel['address'] ?? ''); ?>" 
                                       class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                       placeholder="Nama Jalan, Nomor, Kecamatan" required>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Deskripsi <span class="text-red-500">*</span></label>
                                <textarea name="description" rows="5" 
                                          class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                          placeholder="Jelaskan keunggulan hotel Anda..." required><?php echo htmlspecialchars($hotel['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">Kontak & Status</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700">Nomor Telepon</label>
                                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($hotel['phone'] ?? ''); ?>" 
                                           class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           required>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700">Email Bisnis</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($hotel['email'] ?? ''); ?>" 
                                           class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700">Status Operasional</label>
                                <div class="relative">
                                    <select name="status" class="block w-full appearance-none rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors cursor-pointer">
                                        <option value="active" <?php echo ($hotel['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active (Dapat Dipesan)</option>
                                        <option value="inactive" <?php echo ($hotel['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive (Disembunyikan)</option>
                                        <option value="maintenance" <?php echo ($hotel['status'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance (Perbaikan)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">Fasilitas</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php 
                                $facilities = ['wifi' => 'WiFi Gratis', 'pool' => 'Kolam Renang', 'gym' => 'Gym/Fitness', 'restaurant' => 'Restoran', 'bar' => 'Bar/Lounge', 'parking' => 'Parkir Gratis', 'ac' => 'AC', 'spa' => 'Spa'];
                                $currentFacilities = $hotel['facilities'] ?? []; 
                                foreach ($facilities as $key => $label): 
                                ?>
                                <label class="relative flex items-start p-4 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition-all hover:border-blue-300">
                                    <div class="flex h-6 items-center">
                                        <input type="checkbox" name="facilities[]" value="<?= $key ?>" 
                                               class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                                               <?php echo in_array($key, $currentFacilities) ? 'checked' : ''; ?>>
                                    </div>
                                    <div class="ml-3 text-sm leading-6">
                                        <span class="font-medium text-gray-900"><?= $label ?></span>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900">Foto Utama</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-3">Foto Saat Ini</p>
                                    <div class="aspect-video w-full rounded-lg border border-gray-200 overflow-hidden bg-gray-100">
                                        <img src="<?php echo htmlspecialchars($hotel['photo'] ?? 'https://via.placeholder.com/400x300?text=No+Image'); ?>" 
                                             alt="Preview" class="w-full h-full object-cover">
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-700 mb-3">Ganti Foto</p>
                                    <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-xl appearance-none cursor-pointer hover:border-blue-500 focus:outline-none hover:bg-blue-50">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-6 h-6 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="font-medium text-gray-600">Klik untuk upload file baru</span>
                                        </span>
                                        <input type="file" name="hotel_photo" class="hidden" accept="image/png, image/jpeg, image/jpg">
                                    </label>
                                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maksimal 5MB.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Script tambahan jika perlu, misal untuk preview image realtime
</script>
</body>
</html>