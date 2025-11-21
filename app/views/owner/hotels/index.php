<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <div class="bg-white shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Hotel</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola semua hotel Anda di sini.</p>
            </div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2">
                <span>+</span> Tambah Hotel
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Hotel Card -->
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="relative h-48 bg-gray-200">
                        <img src="https://images.unsplash.com/photo-1564501049351-005e2b3e547d?w=400&h=300&fit=crop" alt="Aria Centra Surabaya" class="w-full h-full object-cover">
                        <span class="absolute top-3 right-3 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">ACTIVE</span>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Aria Centra Surabaya</h3>
                        <p class="text-gray-600 text-sm flex items-center gap-1 mb-3">
                            <span>📍</span> Surabaya
                        </p>
                        <p class="text-gray-600 text-sm line-clamp-2 mb-4">Aria Centra Surabaya terletak di Surabaya dan menawarkan akomodasi dengan akses Wi-Fi gratis.</p>
                        
                        <div class="grid grid-cols-3 gap-2 mb-4 py-3 border-t border-b border-gray-200">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-800">0</p>
                                <p class="text-xs text-gray-600">Kamar</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-800">0</p>
                                <p class="text-xs text-gray-600">Pemesanan</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-800">0</p>
                                <p class="text-xs text-gray-600">Review</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-lg font-semibold text-sm transition">
                                Edit
                            </button>
                            <button class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-lg font-semibold text-sm transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Add Hotel Card -->
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-center min-h-80 border-2 border-dashed border-gray-300">
                    <button class="flex flex-col items-center justify-center hover:opacity-75 transition">
                        <div class="text-4xl text-gray-400 mb-2">+</div>
                        <p class="text-gray-600 font-semibold">Tambah Hotel</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
