<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-50 overflow-hidden">
    <div class="flex-shrink-0 hidden md:block">
        <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    </div>
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="bg-white border-b border-gray-200 shadow-sm z-20 flex-shrink-0">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Kamar</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola tipe kamar, harga, dan ketersediaan.</p>
                </div>
                <a href="<?= BASE_URL ?>/owner/rooms/create" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Kamar
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
            
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800">
                        <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
                <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex-shrink-0">
                        <span class="text-sm font-medium text-gray-500">Filter Hotel:</span>
                    </div>
                    <div class="flex-1 w-full sm:w-auto relative">
                        <form action="" method="GET">
                            <select name="hotel_id" onchange="this.form.submit()" class="block w-full sm:w-64 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 pl-3 pr-10 cursor-pointer bg-gray-50 hover:bg-white transition-colors">
                                <option value="">Semua Hotel</option>
                                <?php if (!empty($data['hotels'])): ?>
                                    <?php foreach ($data['hotels'] as $hotel): ?>
                                        <option value="<?= $hotel['id'] ?>" <?= (isset($_GET['hotel_id']) && $_GET['hotel_id'] == $hotel['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hotel['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (empty($data['rooms'])): ?>
                <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white py-16 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada data kamar</h3>
                    <p class="mt-1 max-w-sm text-sm text-gray-500">Mulai tambahkan tipe kamar untuk hotel Anda agar tamu bisa mulai memesan.</p>
                    <a href="<?= BASE_URL ?>/owner/rooms/create" class="mt-6 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                        + Tambah Kamar Baru
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Info Kamar</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hotel</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga/Malam</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kapasitas</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($data['rooms'] as $room): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-12 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200">
                                                    <img class="h-full w-full object-cover" 
                                                         src="<?= htmlspecialchars($room['main_image']) ?>" 
                                                         alt="<?= htmlspecialchars($room['room_type']) ?>"
                                                         loading="lazy"
                                                         onerror="this.src='https://via.placeholder.com/100x80?text=No+Img'">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($room['room_type']) ?></div>
                                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($room['room_name'] ?? '') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600"><?= htmlspecialchars($room['hotel_name'] ?? '-') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-blue-600">
                                                Rp <?= number_format($room['price_per_night'], 0, ',', '.') ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    <?= $room['capacity'] ?> Orang
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <?php 
                                                $available = $room['available_slots'] ?? 0;
                                                $total = $room['total_slots'] ?? 0;
                                                $stockColor = $available > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $stockColor ?>">
                                                <?= $available ?> / <?= $total ?> Unit
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-3">
                                                <a href="<?= BASE_URL ?>/owner/rooms/edit/<?= $room['id'] ?>" class="text-blue-600 hover:text-blue-900 transition-colors">Edit</a>
                                                <span class="text-gray-300">|</span>
                                                <a href="<?= BASE_URL ?>/owner/rooms/delete/<?= $room['id'] ?>" class="text-red-600 hover:text-red-900 transition-colors" onclick="return confirm('Yakin ingin menghapus kamar ini?')">Hapus</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Menampilkan <span class="font-medium"><?= count($data['rooms']) ?></span> hasil
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="h-12"></div>
            
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>