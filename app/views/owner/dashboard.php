<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/navbar.php'; ?>

<div class="flex min-h-screen bg-gray-50">
    
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col relative transition-all duration-300">
        
        <div class="bg-white/80 backdrop-blur-md border-b border-gray-200 p-6 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Dashboard Owner</h1>
                <p class="text-gray-500 text-sm mt-1">Ringkasan performa bisnis dan manajemen properti.</p>
            </div>
            <button class="group bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                <span class="bg-blue-500 group-hover:bg-blue-600 rounded-lg p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </span>
                Tambah Hotel
            </button>
        </div>

        <div class="flex-1 p-6 md:p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-50 text-green-600 p-3 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">+12%</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Pendapatan</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">Rp 45.2jt<span class="text-gray-300 text-lg">,00</span></h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-50 text-orange-600 p-3 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">Pending</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Sales Tertahan</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">Rp 1.5jt<span class="text-gray-300 text-lg">,00</span></h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Aktif</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Properti</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">1 <span class="text-lg text-gray-400 font-normal">Unit Hotel</span></h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Daftar Properti
                    </h2>
                    <div class="flex gap-2">
                        <input type="text" placeholder="Cari properti..." class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="border border-gray-200 rounded-xl overflow-hidden mb-6 hover:border-blue-300 transition-colors duration-300">
                        <div class="flex flex-col md:flex-row">
                            <div class="w-full md:w-64 h-48 md:h-auto relative group">
                                <img src="https://images.unsplash.com/photo-1564501049351-005e2b3e547d?w=300&h=200&fit=crop" alt="Hotel Image" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button class="text-white text-sm font-semibold border border-white px-4 py-2 rounded-lg hover:bg-white hover:text-black transition">Lihat Detail</button>
                                </div>
                            </div>
                            
                            <div class="flex-1 p-5 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">Aria Centra Surabaya</h3>
                                            <p class="text-gray-500 text-sm flex items-center gap-1 mt-1">
                                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                                Surabaya, Jawa Timur
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                            Operational
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mt-3 line-clamp-2">
                                        Hotel modern yang terletak strategis di pusat kota dengan akses mudah ke pusat perbelanjaan dan area bisnis. Fasilitas lengkap termasuk kolam renang dan gym.
                                    </p>
                                </div>
                                
                                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
                                    <button class="text-sm font-medium text-gray-600 hover:text-blue-600 flex items-center gap-1 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit Info
                                    </button>
                                    <button class="text-sm font-medium text-gray-600 hover:text-red-600 flex items-center gap-1 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 border-t border-gray-200">
                            <div class="px-5 py-2 flex justify-between items-center bg-gray-100/50">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ketersediaan Kamar</h4>
                                <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">+ Tambah Tipe Kamar</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 font-semibold">Tipe Kamar</th>
                                            <th class="px-6 py-3 font-semibold">Harga Dasar</th>
                                            <th class="px-6 py-3 font-semibold">Kapasitas</th>
                                            <th class="px-6 py-3 font-semibold">Stok</th>
                                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-white hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                Deluxe Double Room
                                                <span class="block text-xs text-gray-400 font-normal">Include Breakfast</span>
                                            </td>
                                            <td class="px-6 py-4 text-blue-600 font-bold">Rp 450,000</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                                                    2 Orang
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    9 Unit
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button class="text-gray-400 hover:text-gray-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
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

        <div class="mt-auto">
            <?php include __DIR__ . '/../layouts/footer.php'; ?>
        </div>

    </div>
</div>