<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-[calc(100vh-70px)] bg-gray-100 overflow-hidden">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-y-auto">
        <div class="bg-white shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Pemesanan</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola pemesanan dari tamu Anda.</p>
            </div>
        </div>

        <div class="p-6">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hotel</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Hotel</option>
                                <option>Aria Centra Surabaya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Status</option>
                                <option>Confirmed</option>
                                <option>Pending</option>
                                <option>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="p-6 border-b border-gray-200 grid grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm font-medium">Total Pemesanan</p>
                        <p class="text-2xl font-bold text-blue-600">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm font-medium">Confirmed</p>
                        <p class="text-2xl font-bold text-green-600">0</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm font-medium">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">0</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm font-medium">Cancelled</p>
                        <p class="text-2xl font-bold text-red-600">0</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">No. Pemesanan</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Tamu</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hotel</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Check-in</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Check-out</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <p>Belum ada pemesanan</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>