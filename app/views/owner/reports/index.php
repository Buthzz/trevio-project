<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-100">
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <div class="bg-white shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Laporan & Analitik</h1>
                <p class="text-gray-500 text-sm mt-1">Pantau performa dan pendapatan hotel Anda.</p>
            </div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2">
                📥 Export Laporan
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <!-- Date Range Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hotel</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Semua Hotel</option>
                            <option>Aria Centra Surabaya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Bulan Ini</option>
                            <option>Bulan Lalu</option>
                            <option>3 Bulan Terakhir</option>
                            <option>6 Bulan Terakhir</option>
                            <option>Tahun Ini</option>
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
                <button class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Apply Filter
                </button>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-800">Rp 0</p>
                    <p class="text-green-600 text-sm mt-2">+0% dari bulan lalu</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">Total Pemesanan</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                    <p class="text-gray-600 text-sm mt-2">Booking rate: 0%</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">Tingkat Okupansi</p>
                    <p class="text-3xl font-bold text-gray-800">0%</p>
                    <p class="text-gray-600 text-sm mt-2">0 kamar terisi</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">Rating Rata-rata</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                    <p class="text-gray-600 text-sm mt-2">Dari 0 review</p>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Pendapatan Harian</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <canvas id="revenueChart"></canvas>
                        <p class="text-gray-500">Chart akan ditampilkan di sini</p>
                    </div>
                </div>

                <!-- Booking Trends -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tren Pemesanan</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <canvas id="bookingChart"></canvas>
                        <p class="text-gray-500">Chart akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Room Type Performance -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Performa Tipe Kamar</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <canvas id="roomTypeChart"></canvas>
                        <p class="text-gray-500">Chart akan ditampilkan di sini</p>
                    </div>
                </div>

                <!-- Top Performing Hotels -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Hotel Terbaik</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-bold text-gray-400">1</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Aria Centra Surabaya</p>
                                    <p class="text-sm text-gray-600">Rp 0</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">0%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Detail Pemesanan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hotel</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pemesanan</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pendapatan</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p>Belum ada data</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/chart.min.js"></script>
<script>
    // Charts akan diinisialisasi di sini
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
