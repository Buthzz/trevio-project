<?php
// Pastikan BASE_URL terdefinisi
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Helper untuk format rupiah (jika belum ada di global helper)
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

// PERBAIKAN: Path include disesuaikan
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex min-h-[calc(100vh-var(--header-height,4rem))] bg-slate-50">
    <button id="sidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden rounded-lg bg-accent p-2 text-white shadow-lg hover:bg-accentLight transition" onclick="toggleSidebar()">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <div id="sidebarOverlay" class="fixed inset-0 z-20 hidden bg-black/50 lg:hidden" onclick="closeSidebar()"></div>

    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-30 w-64 border-r border-slate-200 bg-white overflow-y-auto transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:pt-0" style="top: var(--header-height, 4rem);">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 lg:hidden">
            <h3 class="font-bold text-slate-900">Menu</h3>
            <button class="rounded-lg p-1 hover:bg-slate-100 transition" onclick="closeSidebar()">
                <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <nav class="space-y-2">
                <a href="<?= $baseUrl ?>/admin/dashboard" 
                   class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="<?= $baseUrl ?>/admin/hotels" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                    </svg>
                    Hotels
                </a>
                <a href="<?= $baseUrl ?>/admin/payments" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Payments
                </a>
                <a href="<?= $baseUrl ?>/admin/refunds" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                    </svg>
                    Refunds
                </a>
                <a href="<?= $baseUrl ?>/admin/users" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                    </svg>
                    Users
                </a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
                <p class="mt-2 text-slate-600">Selamat datang kembali! Berikut adalah ringkasan sistem Trevio.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Hotels</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900"><?= number_format($data['stats']['total_hotels'] ?? 0) ?></p>
                            <p class="mt-1 text-xs text-slate-500">Terdaftar di sistem</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Users</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900"><?= number_format($data['stats']['total_users'] ?? 0) ?></p>
                            <p class="mt-1 text-xs text-slate-500">Customer & Owner</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Revenue</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900"><?= formatRupiah($data['stats']['total_revenue'] ?? 0) ?></p>
                            <p class="mt-1 text-xs text-green-600">Akumulasi pendapatan</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Need Action</p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-2xl font-bold text-slate-900"><?= ($data['stats']['pending_payments'] ?? 0) + ($data['stats']['pending_refunds'] ?? 0) ?></span>
                                <span class="text-xs text-slate-500">Tasks</span>
                            </div>
                            <p class="mt-1 text-xs text-red-600">
                                <?= $data['stats']['pending_payments'] ?? 0 ?> Payments, <?= $data['stats']['pending_refunds'] ?? 0 ?> Refunds
                            </p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <h2 class="mb-4 text-lg font-bold text-slate-900">System Overview</h2>
                    <div class="h-64 flex items-center justify-center p-2 relative">
                        <canvas id="adminActionChart"></canvas>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-slate-900">Booking Terbaru</h2>
                        <a href="<?= $baseUrl ?>/admin/payments" class="text-sm text-accent hover:underline">Lihat Semua</a>
                    </div>
                    
                    <div class="space-y-4 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        <?php if (!empty($data['recent_bookings'])): ?>
                            <?php foreach ($data['recent_bookings'] as $booking): ?>
                                <div class="flex items-center gap-4 pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                                    <div class="rounded-full bg-blue-50 p-2 shrink-0">
                                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-900 truncate">
                                            #<?= htmlspecialchars($booking['booking_code'] ?? '-') ?>
                                        </p>
                                        <p class="text-xs text-slate-500 truncate">
                                            <?= htmlspecialchars($booking['customer_name'] ?? 'Guest') ?> - 
                                            <?= htmlspecialchars($booking['hotel_name'] ?? 'Unknown Hotel') ?>
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">
                                            <?= ucfirst($booking['booking_status'] ?? 'pending') ?>
                                        </span>
                                        <p class="text-xs text-slate-400 mt-1">
                                            <?= isset($booking['created_at']) ? date('d M H:i', strtotime($booking['created_at'])) : '-' ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-8 text-slate-500 text-sm">
                                Belum ada aktivitas booking terbaru.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Debug: Log BASE_URL untuk memastikan path benar
    console.log('BASE_URL:', '<?= BASE_URL ?>');
    console.log('Chart.js URL:', '<?= BASE_URL ?>/js/chart.min.js');
    console.log('Charts.js URL:', '<?= BASE_URL ?>/js/charts.js');
    
    // Load Chart.js dengan fallback
    function loadScript(src, callback, fallback) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = callback;
        script.onerror = function() {
            console.warn(`Failed to load ${src}, trying fallback...`);
            if (fallback) {
                const fallbackScript = document.createElement('script');
                fallbackScript.src = fallback;
                fallbackScript.onload = callback;
                fallbackScript.onerror = function() {
                    console.error(`Both ${src} and ${fallback} failed to load`);
                };
                document.head.appendChild(fallbackScript);
            }
        };
        document.head.appendChild(script);
    }

    // Load Chart.js pertama
    loadScript(
        '<?= BASE_URL ?>/js/chart.min.js',
        function() {
            console.log('Chart.js loaded successfully');
            // Kemudian load Charts.js
            loadScript('<?= BASE_URL ?>/js/charts.js', function() {
                console.log('Charts.js loaded successfully');
            });
        },
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js'
    );
</script>

<script>
    // Sidebar Toggle Logic
    function toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Close sidebar when clicking links on mobile
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });

    // Handle Resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });

    // Initialize Chart
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Periksa apakah Chart.js dimuat
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');
        console.log('Charts module loaded:', typeof Charts !== 'undefined');
        
        // Ambil data statistik dari PHP
        const pendingPayments = <?= $data['stats']['pending_payments'] ?? 0 ?>;
        const pendingRefunds = <?= $data['stats']['pending_refunds'] ?? 0 ?>;
        
        console.log('Chart data:', {pendingPayments, pendingRefunds});

        const chartElement = document.getElementById('adminActionChart');
        console.log('Chart element found:', !!chartElement);
        
        if (chartElement) {
            // Tunggu sebentar untuk memastikan semua library dimuat
            setTimeout(() => {
                // Cek apakah Chart.js tersedia
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js library not loaded!');
                    chartElement.innerHTML = `
                        <div class="flex items-center justify-center h-full text-slate-500">
                            <div class="text-center">
                                <svg class="h-12 w-12 mx-auto mb-3 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-red-600">Chart.js library gagal dimuat</p>
                                <p class="text-xs mt-1 text-slate-500">Pending Payments: ${pendingPayments}</p>
                                <p class="text-xs text-slate-500">Pending Refunds: ${pendingRefunds}</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                // Cek apakah Charts module tersedia
                if (typeof Charts === 'undefined') {
                    console.error('Charts module not loaded!');
                    chartElement.innerHTML = `
                        <div class="flex items-center justify-center h-full text-slate-500">
                            <div class="text-center">
                                <svg class="h-12 w-12 mx-auto mb-3 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-red-600">Charts module gagal dimuat</p>
                                <p class="text-xs mt-1 text-slate-500">Pending Payments: ${pendingPayments}</p>
                                <p class="text-xs text-slate-500">Pending Refunds: ${pendingRefunds}</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                try {
                    // Kita gunakan Bar Chart untuk perbandingan yang jelas
                    const chart = Charts.createBarChart('#adminActionChart', {
                        labels: ['Pending Payments', 'Pending Refunds'],
                        datasets: [{
                            label: 'Jumlah Request',
                            data: [pendingPayments, pendingRefunds],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)', // Blue for Payments
                                'rgba(239, 68, 68, 0.8)'   // Red for Refunds
                            ],
                            borderColor: [
                                'rgb(59, 130, 246)',
                                'rgb(239, 68, 68)'
                            ],
                            borderWidth: 1,
                            barThickness: 60, // Lebar bar fixed agar rapi
                        }]
                    }, {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false // Sembunyikan legend karena label sumbu X sudah jelas
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' Tasks';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1 // Pastikan sumbu Y bilangan bulat (karena jumlah task)
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    });
                    
                    console.log('Chart created successfully:', !!chart);
                } catch (error) {
                    console.error('Error creating chart:', error);
                    // Fallback: tampilkan pesan jika chart gagal
                    chartElement.innerHTML = `
                        <div class="flex items-center justify-center h-full text-slate-500">
                            <div class="text-center">
                                <svg class="h-12 w-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-sm">Chart tidak dapat dimuat</p>
                                <p class="text-xs mt-1">Pending Payments: ${pendingPayments}</p>
                                <p class="text-xs">Pending Refunds: ${pendingRefunds}</p>
                            </div>
                        </div>
                    `;
                }
            }, 100);
        }
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>