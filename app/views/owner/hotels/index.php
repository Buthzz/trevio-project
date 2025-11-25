<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <div class="bg-white shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Hotel</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola semua hotel Anda di sini.</p>
            </div>
            <a href="<?= BASE_URL ?>/owner/hotels/create" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2 transition">
                <span>+</span> Tambah Hotel
            </a>
        </div>

        <div class="flex-1 overflow-auto p-6">
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($data['hotels'])): ?>
                <div class="bg-white rounded-lg shadow p-12 text-center border-2 border-dashed border-gray-300">
                    <div class="text-6xl mb-4">🏨</div>
                    <h3 class="text-xl font-bold text-gray-700">Belum ada hotel</h3>
                    <p class="text-gray-500 mb-6">Anda belum mendaftarkan properti apapun.</p>
                    <a href="<?= BASE_URL ?>/owner/hotels/create" class="text-blue-600 font-semibold hover:underline">Mulai Tambah Hotel</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($data['hotels'] as $hotel): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                            <div class="relative h-48 bg-gray-200">
                                <img src="<?= htmlspecialchars($hotel['main_image']) ?>" 
                                     alt="<?= htmlspecialchars($hotel['name']) ?>" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'">
                                <span class="absolute top-3 right-3 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                    <?= $hotel['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-gray-800 mb-1 truncate"><?= htmlspecialchars($hotel['name']) ?></h3>
                                <p class="text-gray-600 text-sm flex items-center gap-1 mb-3">
                                    <span>📍</span> <?= htmlspecialchars($hotel['city']) ?>
                                </p>
                                
                                <div class="flex gap-2 mt-4">
                                    <a href="#" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg font-semibold text-sm text-center transition">
                                        Edit
                                    </a>
                                    <a href="<?= BASE_URL ?>/owner/rooms?hotel_id=<?= $hotel['id'] ?>" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 px-3 rounded-lg font-semibold text-sm text-center transition">
                                        Kamar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>