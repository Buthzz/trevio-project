<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <div class="bg-white shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Owner</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola properti, kamar, dan pantau pendapatan.</p>
            </div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2">
                <span>+</span> Tambah Hotel
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-3 gap-6 mb-8">
                <!-- Total Pendapatan -->
                <div class="bg-white rounded-lg shadow p-6 flex items-start">
                    <div class="bg-green-100 text-green-600 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.45-2.34-1.12 0-.72.6-1.08 1.52-1.08 1.39 0 1.8.84 2.15 1.26.14.17.43.12.53-.04l.75-1.23c.1-.15.04-.39-.15-.46-.6-.3-1.64-.68-2.86-.68-1.87 0-3.39 1.08-3.39 2.72 0 1.53.73 2.15 2.58 2.73 1.64.48 2.04.6 2.04 1.35 0 .86-.64 1.4-1.76 1.4-1.3 0-1.84-.5-2.15-1.28-.11-.3-.45-.41-.65-.25l-.71 1.15c-.15.12-.1.39.08.46.75.36 1.9.65 3.38.65 2.1 0 3.6-1.08 3.6-2.8-.01-1.63-.73-2.13-2.54-2.66z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">TOTAL PENDAPATAN</p>
                        <p class="text-2xl font-bold text-gray-800">Rp 0</p>
                    </div>
                </div>

                <!-- Sales Tertahan -->
                <div class="bg-white rounded-lg shadow p-6 flex items-start">
                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">SALES TERTAHAN</p>
                        <p class="text-2xl font-bold text-gray-800">Rp 0</p>
                    </div>
                </div>

                <!-- Total Properti -->
                <div class="bg-white rounded-lg shadow p-6 flex items-start">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-full mr-4">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">TOTAL PROPERTI</p>
                        <p class="text-2xl font-bold text-gray-800">1 Hotel</p>
                    </div>
                </div>
            </div>

            <!-- Daftar Properti -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Properti</h2>
                </div>
                
                <!-- Property Card -->
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Property Item -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="grid grid-cols-4 gap-4">
                                <!-- Property Image -->
                                <div class="col-span-1">
                                    <img src="https://images.unsplash.com/photo-1564501049351-005e2b3e547d?w=300&h=200&fit=crop" alt="Aria Centra Surabaya" class="w-full h-40 object-cover rounded-lg">
                                </div>
                                
                                <!-- Property Info -->
                                <div class="col-span-3 p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">Aria Centra Surabaya</h3>
                                            <p class="text-gray-600 text-sm flex items-center gap-1 mt-1">
                                                <span>📍</span> Surabaya
                                            </p>
                                        </div>
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">ACTIVE</span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm mb-4">Aria Centra Surabaya terletak di Surabaya dan menawarkan akomodasi dengan akses Wi-Fi gratis di seluruh areanya. Akomodasi ini memiliki layanan resepsionis 24 jam dan parker pribadi gratis di lokasi.</p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <button class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                                            ✏️ Edit Info
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 font-semibold">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Management Table -->
                            <div class="border-t border-gray-200 p-4 bg-gray-50">
                                <p class="text-sm font-semibold text-gray-700 mb-3">MANAJEMEN KAMAR</p>
                                <table class="w-full text-sm">
                                    <thead class="border-b border-gray-300">
                                        <tr>
                                            <th class="text-left py-2 px-2 text-gray-700 font-semibold">Tipe Kamar</th>
                                            <th class="text-left py-2 px-2 text-gray-700 font-semibold">Harga / Malam</th>
                                            <th class="text-left py-2 px-2 text-gray-700 font-semibold">Kapasitas</th>
                                            <th class="text-left py-2 px-2 text-gray-700 font-semibold">Stok</th>
                                            <th class="text-left py-2 px-2 text-gray-700 font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="py-3 px-2">Test</td>
                                            <td class="py-3 px-2 text-blue-600 font-semibold">Rp 100,000</td>
                                            <td class="py-3 px-2">1 Orang</td>
                                            <td class="py-3 px-2">9 Unit</td>
                                            <td class="py-3 px-2">
                                                <button class="text-gray-600 hover:text-gray-800">
                                                    ⋯
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
