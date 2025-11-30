<?php include __DIR__ . '/../../layouts/header.php'; ?>
<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<div class="flex h-screen bg-gray-50 overflow-hidden">
    
    <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-y-auto relative transition-all duration-300">
        
        <div class="bg-white/80 backdrop-blur-md border-b border-gray-200 p-6 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Laporan & Analitik</h1>
                <p class="text-gray-500 text-sm mt-1">Pantau performa bisnis dan statistik pendapatan hotel.</p>
            </div>
            <button class="group bg-white border border-gray-200 hover:border-blue-500 text-gray-700 hover:text-blue-600 font-semibold py-2.5 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Laporan
            </button>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <h3 class="font-bold text-gray-700">Filter Data</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Properti</label>
                        <div class="relative">
                            <select class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors appearance-none cursor-pointer">
                                <option>Semua Hotel</option>
                                <option>Aria Centra Surabaya</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Periode Waktu</label>
                        <div class="relative">
                            <select class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors appearance-none cursor-pointer">
                                <option>Bulan Ini</option>
                                <option>Bulan Lalu</option>
                                <option>3 Bulan Terakhir</option>
                                <option>Tahun Ini</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Dari Tanggal</label>
                        <input type="date" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Sampai Tanggal</label>
                        <div class="flex gap-2">
                            <input type="date" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 flex items-center justify-center transition-colors shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-green-50 p-3 rounded-xl text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">+12.5% ↗</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp 45.200.000</h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">128 Baru</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Total Pemesanan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">342</h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-purple-50 p-3 rounded-xl text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-1 rounded-full">Sedang</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Tingkat Okupansi</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">68%</h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-yellow-50 p-3 rounded-xl text-yellow-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                        <span class="text-gray-400 text-xs font-medium px-2 py-1">24 Review</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Rating Rata-rata</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">4.8 <span class="text-sm text-gray-400 font-normal">/ 5.0</span></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Grafik Pendapatan</h3>
                        <button class="text-blue-600 text-sm font-medium hover:underline">Lihat Detail</button>
                    </div>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <canvas id="revenueChart" class="w-full h-full"></canvas>
                        <span class="absolute text-gray-400 text-sm" style="z-index:-1">Chart Loading...</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Tren Pemesanan</h3>
                        <button class="text-blue-600 text-sm font-medium hover:underline">Lihat Detail</button>
                    </div>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <canvas id="bookingChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Performa Tipe Kamar</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <canvas id="roomTypeChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Top Performa Hotel</h3>
                    <div class="space-y-4 flex-1 overflow-y-auto pr-2">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition-colors cursor-pointer group">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-yellow-400 text-white flex items-center justify-center font-bold shadow-sm">1</div>
                                <div>
                                    <p class="font-bold text-gray-800 group-hover:text-blue-700">Aria Centra Surabaya</p>
                                    <p class="text-xs text-gray-500">Okupansi: 85%</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800">Rp 120.5jt</p>
                                <p class="text-xs text-green-600">▲ 12%</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold">2</div>
                                <div>
                                    <p class="font-bold text-gray-800">Grand City Hotel</p>
                                    <p class="text-xs text-gray-500">Okupansi: 72%</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800">Rp 98.2jt</p>
                                <p class="text-xs text-green-600">▲ 5%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800">Rincian Transaksi Terakhir</h3>
                    <button class="text-sm text-blue-600 font-semibold hover:underline">Lihat Semua Data</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100/70 border-b border-gray-200 text-gray-600 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-4 font-bold">Tanggal</th>
                                <th class="px-6 py-4 font-bold">Booking ID</th>
                                <th class="px-6 py-4 font-bold">Hotel</th>
                                <th class="px-6 py-4 font-bold">Pendapatan</th>
                                <th class="px-6 py-4 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="font-medium">Belum ada data transaksi pada periode ini.</p>
                                        <p class="text-xs mt-1">Coba ubah filter tanggal di atas.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-auto">
            <?php include __DIR__ . '/../../layouts/footer.php'; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Contoh inisialisasi dummy chart agar tampilan tidak kosong saat preview
    // Anda bisa menggantinya dengan data real dari PHP/Database
    document.addEventListener('DOMContentLoaded', function() {
        const ctxRev = document.getElementById('revenueChart');
        if(ctxRev) {
            new Chart(ctxRev, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Pendapatan (Juta Rp)',
                        data: [12, 19, 15, 25, 22, 30, 45],
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    });
</script>