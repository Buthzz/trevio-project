<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-[calc(100vh-70px)] bg-gray-50 overflow-hidden">
    <div class="flex-shrink-0">
        <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    </div>
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <main class="flex-1 overflow-y-auto p-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Hotel</h1>
                    <p class="mt-1 text-sm text-gray-500">Kelola daftar properti dan status operasional Anda.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="<?= BASE_URL ?>/owner/hotels/create" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Hotel
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($data['hotels'])): ?>
                <div class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="mt-2 block text-sm font-medium text-gray-900">Belum ada properti</span>
                    <span class="mt-1 block text-sm text-gray-500">Mulai dengan mendaftarkan hotel pertama Anda.</span>
                    <div class="mt-6">
                        <a href="<?= BASE_URL ?>/owner/hotels/create" class="text-blue-600 font-medium hover:text-blue-500 text-sm">
                            + Tambah Hotel Baru
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php foreach ($data['hotels'] as $hotel): ?>
                        <div class="flex flex-col overflow-hidden rounded-lg shadow-sm bg-white border border-gray-200">
                            <div class="flex-shrink-0 relative h-48">
                                <img class="h-full w-full object-cover" 
                                     src="<?= htmlspecialchars($hotel['main_image']) ?>" 
                                     alt="<?= htmlspecialchars($hotel['name']) ?>"
                                     loading="lazy"
                                     onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                                
                                <div class="absolute top-2 right-2">
                                    <?php if ($hotel['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex-1 bg-white p-5 flex flex-col justify-between">
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-gray-900 truncate">
                                        <?= htmlspecialchars($hotel['name']) ?>
                                    </h3>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars($hotel['city']) ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
                                    <a href="<?= BASE_URL ?>/owner/hotels/edit/<?= $hotel['id'] ?>" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                        Edit Detail
                                    </a>
                                    <a href="<?= BASE_URL ?>/owner/rooms?hotel_id=<?= $hotel['id'] ?>" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                        Kelola Kamar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>