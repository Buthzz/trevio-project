<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <div class="bg-white shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Kamar</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola semua kamar di hotel Anda.</p>
            </div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2">
                <span>+</span> Tambah Kamar
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <div class="bg-white rounded-lg shadow">
                <!-- Filter Section -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex gap-4 items-center flex-wrap">
                        <div class="flex-1 min-w-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hotel</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Aria Centra Surabaya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Kamar</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tipe Kamar</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Harga / Malam</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kapasitas</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Stok</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-800">Test Room</td>
                                <td class="px-6 py-4 text-sm text-gray-800">Single</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600">Rp 100,000</td>
                                <td class="px-6 py-4 text-sm text-gray-800">1 Orang</td>
                                <td class="px-6 py-4 text-sm text-gray-800">9 Unit</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <button class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 font-semibold">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div class="p-12 text-center text-gray-500">
                    <p class="text-lg">Belum ada kamar. <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">Tambah kamar</a> untuk memulai.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
