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
                    <a href="<?= BASE_URL ?>/owner/hotels" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Hotel Baru</h1>
                        <p class="text-sm text-gray-500">Isi formulir di bawah untuk mendaftarkan properti baru Anda.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="<?= BASE_URL ?>/owner/hotels" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit" form="createHotelForm" class="px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Hotel
                    </button>
                </div>
            </div>

            <div class="p-8 max-w-5xl mx-auto">
                <form id="createHotelForm" action="/owner/hotels/store" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informasi Dasar</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="hotel_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Hotel <span class="text-red-500">*</span></label>
                                    <input type="text" name="hotel_name" id="hotel_name" 
                                           class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="Contoh: Grand Hotel Trevio" required>
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" name="city" id="city" list="city_list"
                                               class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 pr-10 text-base transition-colors" 
                                               placeholder="Ketik atau pilih kota..." required>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                            </div>
                                    </div>
                                    <datalist id="city_list">
                                        <option value="Jakarta">
                                        <option value="Surabaya">
                                        <option value="Bandung">
                                        <option value="Medan">
                                        <option value="Semarang">
                                        <option value="Makassar">
                                        <option value="Palembang">
                                        <option value="Denpasar">
                                        <option value="Yogyakarta">
                                        <option value="Malang">
                                        <option value="Batam">
                                        <option value="Balikpapan">
                                        <option value="Bogor">
                                        <option value="Solo">
                                    </datalist>
                                </div>
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="address" id="address" 
                                       class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                       placeholder="Nama Jalan, Nomor, Kecamatan" required>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                                <textarea name="description" id="description" rows="5" 
                                          class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                          placeholder="Jelaskan fasilitas dan keunggulan hotel Anda..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Kontak Resmi</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" id="phone" 
                                           class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="+62..." required>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Bisnis <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" id="email" 
                                           class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-base transition-colors" 
                                           placeholder="email@hotel.com" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Fasilitas</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php 
                                $facilities = ['wifi' => 'WiFi Gratis', 'pool' => 'Kolam Renang', 'gym' => 'Gym/Fitness', 'restaurant' => 'Restoran', 'bar' => 'Bar/Lounge', 'parking' => 'Parkir Gratis', 'ac' => 'AC', 'spa' => 'Spa'];
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
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Foto Utama</h3>
                        </div>
                        <div class="p-8">
                            <label class="flex flex-col items-center justify-center w-full h-48 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-2xl appearance-none cursor-pointer hover:border-blue-500 hover:bg-blue-50 group" onclick="document.getElementById('hotel_photo').click()">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="p-3 rounded-full bg-gray-100 group-hover:bg-blue-100 mb-3 transition-colors">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <p class="mb-2 text-sm text-gray-600"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG, JPEG (Maksimal 5MB)</p>
                                </div>
                                <input type="file" name="hotel_photo" id="hotel_photo" class="hidden" accept="image/jpeg,image/png,image/jpg" required>
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