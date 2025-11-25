<?php
// Judul halaman utama admin agar mudah dikenali di tab browser.
$pageTitle = 'Admin Dashboard - Trevio';
// Tautan home digunakan oleh header untuk kembali ke dashboard.
$homeLink  = 'dashboard.php';
// Sertakan header global demi keseragaman layout.
include __DIR__ . '/../layouts/header.php';
?>

<!-- Layout dashboard admin utama -->
<div class="flex min-h-[calc(100vh-var(--header-height,4rem))] bg-slate-50">
    <!-- Sidebar Toggle Button (Mobile) -->
    <button id="sidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden rounded-lg bg-accent p-2 text-white shadow-lg hover:bg-accentLight transition" onclick="toggleSidebar()">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 z-20 hidden bg-black/50 lg:hidden" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <!-- Sidebar navigasi modul admin -->
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-30 w-64 border-r border-slate-200 bg-white overflow-y-auto transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:pt-0" style="top: var(--header-height, 4rem);">
        <!-- Sidebar Header with Close Button (Mobile) -->
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
            <a href="dashboard.php" 
               class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                </svg>
                Dashboard
            </a>
                <a href="hotels/index.php" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                </svg>
                Hotels
            </a>
                <a href="payments/index.php" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Payments
            </a>
                <a href="refunds/index.php" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                </svg>
                Refunds
            </a>
                <a href="users/index.php" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                </svg>
                Users
            </a>
        </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <!-- Area konten dinamis -->
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
                <p class="mt-2 text-slate-600">Selamat datang kembali! Berikut adalah ringkasan sistem Trevio.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Total Hotels -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Hotels</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">1,234</p>
                            <p class="mt-1 text-xs text-green-600">+12% dari bulan lalu</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Users</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">5,678</p>
                            <p class="mt-1 text-xs text-green-600">+8% dari bulan lalu</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Payments -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Payments</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">Rp 2.5M</p>
                            <p class="mt-1 text-xs text-green-600">+23% dari bulan lalu</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Refunds -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Pending Refunds</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">42</p>
                            <p class="mt-1 text-xs text-red-600">Perlu ditindaklanjuti</p>
                        </div>
                        <div class="rounded-full bg-red-100 p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Revenue Chart -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <h2 class="mb-4 text-lg font-bold text-slate-900">Revenue Trend</h2>
                    <div class="h-64 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                        <p class="text-slate-500">Chart akan ditampilkan di sini</p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <h2 class="mb-4 text-lg font-bold text-slate-900">Aktivitas Terbaru</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                            <div class="rounded-full bg-blue-100 p-2">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Payment Verified</p>
                                <p class="text-xs text-slate-600">5 menit yang lalu</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                            <div class="rounded-full bg-purple-100 p-2">
                                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">New User Registered</p>
                                <p class="text-xs text-slate-600">15 menit yang lalu</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-green-100 p-2">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Hotel Listed</p>
                                <p class="text-xs text-slate-600">1 jam yang lalu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        // Mengontrol buka/tutup sidebar ketika tombol hamburger ditekan.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        // Paksa sidebar menutup agar konten utama lebih leluasa.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Tutup sidebar saat user memilih link navigasi.
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Pastikan sidebar otomatis tampil di resolusi desktop.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>