<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/navbar.php'; ?>

<div class="flex h-[calc(100vh-70px)] bg-gray-50 overflow-hidden">
    
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-y-auto relative transition-all duration-300">
        
        <div class="bg-white/80 backdrop-blur-md border-b border-gray-200 p-6 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Dashboard Owner</h1>
                <p class="text-gray-500 text-sm mt-1">Ringkasan performa bisnis dan manajemen properti.</p>
            </div>
            <a href="<?= BASE_URL ?>/owner/hotels/" class="group bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                <span class="bg-blue-500 group-hover:bg-blue-600 rounded-lg p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </span>
                Kelola Hotel
            </a>
        </div>

        <div class="p-6 md:p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-50 text-green-600 p-3 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Pendapatan Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">
                        Rp <?= number_format($stats['revenue_month'] ?? 0, 0, ',', '.') ?>
                    </h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-50 text-orange-600 p-3 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Booking Aktif</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['active_bookings'] ?? 0 ?></h3>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Aktif</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Properti</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">
                        <?= $stats['my_hotels'] ?? 0 ?> <span class="text-lg text-gray-400 font-normal">Unit</span>
                    </h3>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                 <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Analitik Pendapatan</h3>
                        <p class="text-sm text-gray-500">Statistik pendapatan dalam 7 hari terakhir</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                        <span class="text-sm text-gray-600">Revenue</span>
                    </div>
                 </div>
                 
                 <div class="h-80 w-full relative">
                     <canvas id="revenueChart"></canvas>
                 </div>
            </div>

        </div>
        <div class="mt-auto">
            <?php include __DIR__ . '/../layouts/footer.php'; ?>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/js/chart.min.js"></script>
<script src="<?= BASE_URL ?>/js/charts.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data dari PHP
        const rawData = <?= json_encode($chart_data ?? []) ?>;
        
        // Setup default data jika kosong (untuk demo/fallback)
        const labels = (rawData.labels && rawData.labels.length > 0) 
            ? rawData.labels 
            : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            
        const dataValues = (rawData.revenue && rawData.revenue.length > 0) 
            ? rawData.revenue 
            : [0, 0, 0, 0, 0, 0, 0];

        // Format Rupiah Helper
        const formatRupiah = (value) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        };

        // Render Chart menggunakan helper Charts.createAreaChart
        if (document.getElementById('revenueChart')) {
            Charts.createAreaChart('#revenueChart', {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: dataValues,
                    borderColor: '#4f46e5', // Indigo-600
                    gradientColor: ['rgba(79, 70, 229, 0.2)', 'rgba(79, 70, 229, 0.0)'],
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }]
            }, {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Kita sembunyikan default legend agar lebih bersih
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatRupiah(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                // Format sumbu Y menjadi format singkat (e.g., 1jt, 500rb)
                                if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + ' Rb';
                                return value;
                            },
                            font: { family: "'Inter', sans-serif", size: 11 }, 
                            color: '#94a3b8'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 11 }, 
                            color: '#94a3b8'
                        }
                    }
                }
            });
        }
    });
</script>