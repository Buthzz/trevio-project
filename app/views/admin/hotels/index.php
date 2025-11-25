<?php
// Tentukan judul halaman agar header menampilkan konteks modul hotel.
$pageTitle = 'Admin Hotels - Trevio';
// Set tautan kembali ke dashboard utama admin untuk tombol logo.
$homeLink  = '../dashboard.php';
// Muat header global supaya gaya dan navigasi konsisten.
include __DIR__ . '/../../layouts/header.php';
?>

<!-- Daftar hotel untuk tim admin -->
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
    <!-- Sidebar modul admin -->
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
            <a href="<?= BASE_URL ?>/admin/dashboard" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                </svg>
                Dashboard
            </a>
                <a href="<?= BASE_URL ?>/admin/hotels" 
               class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                </svg>
                Hotels
            </a>
                <a href="<?= BASE_URL ?>/admin/payments" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Payments
            </a>
                <a href="<?= BASE_URL ?>/admin/refunds" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                </svg>
                Refunds
            </a>
                <a href="<?= BASE_URL ?>/admin/users" 
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
    <!-- Konten utama: filter + tabel hotel -->
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Hotels Management</h1>
                    <p class="mt-2 text-slate-600">Kelola dan pantau semua hotel yang terdaftar</p>
                </div>
                <button class="inline-flex items-center gap-2 rounded-lg bg-accent px-6 py-3 text-white font-semibold hover:bg-accentLight transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Hotel
                </button>
            </div>

            <!-- Filter & Search Bar -->
            <div class="mb-6 flex gap-4">
                <input type="text" placeholder="Cari hotel..." 
                       class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                <select class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option>Semua Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                    <option>Pending</option>
                </select>
                <button class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-300 transition">
                    Filter
                </button>
            </div>

            <!-- Hotels Table -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-[960px] w-full table-auto">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Hotel Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Owner</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Location</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Rooms</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Rating</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-900 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php 
                        // Data contoh sementara sebelum diganti query database.
                        $hotels = [
                            ['id' => 1, 'name' => 'Grand Hotel Jakarta', 'owner' => 'Budi Santoso', 'location' => 'Jakarta Pusat', 'rooms' => 45, 'status' => 'active', 'rating' => 4.8],
                            ['id' => 2, 'name' => 'Luxury Resort Bali', 'owner' => 'Made Wijaya', 'location' => 'Ubud, Bali', 'rooms' => 60, 'status' => 'active', 'rating' => 4.9],
                            ['id' => 3, 'name' => 'Business Inn Surabaya', 'owner' => 'Siti Nurhaliza', 'location' => 'Surabaya', 'rooms' => 30, 'status' => 'active', 'rating' => 4.5],
                            ['id' => 4, 'name' => 'Boutique Hotel Bandung', 'owner' => 'Ahmad Dahlan', 'location' => 'Bandung', 'rooms' => 25, 'status' => 'pending', 'rating' => 4.6],
                            ['id' => 5, 'name' => 'Beach Resort Lombok', 'owner' => 'Ni Ketut Sari', 'location' => 'Lombok', 'rooms' => 55, 'status' => 'inactive', 'rating' => 4.7],
                        ];
                        // Palet warna untuk avatar hotel agar visual variatif.
                        $hotelPalettes = [
                            ['bg' => 'bg-blue-100', 'icon' => 'text-blue-600'],
                            ['bg' => 'bg-indigo-100', 'icon' => 'text-indigo-600'],
                            ['bg' => 'bg-teal-100', 'icon' => 'text-teal-600'],
                            ['bg' => 'bg-purple-100', 'icon' => 'text-purple-600'],
                        ];
                        foreach ($hotels as $index => $hotel): 
                            // Pilih kombinasi warna berdasarkan indeks agar bergantian.
                            $hotelPalette = $hotelPalettes[$index % count($hotelPalettes)];
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg <?= htmlspecialchars($hotelPalette['bg']) ?> shadow-inner">
                                        <svg class="h-5 w-5 <?= htmlspecialchars($hotelPalette['icon']) ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 10h14M10 21v-8m4 8v-8m-7 0l5-5 5 5"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($hotel['name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($hotel['owner']) ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($hotel['location']) ?></td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700"><?= $hotel['rooms'] ?> rooms</span>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                // Petakan status hotel ke warna badge yang tepat.
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'inactive' => 'bg-red-100 text-red-700'
                                ];
                                // Gunakan kelas default abu jika status tidak dikenal.
                                $statusClass = $statusColors[$hotel['status']] ?? 'bg-slate-100 text-slate-700';
                                ?>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $statusClass ?>">
                                    <?= ucfirst($hotel['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="font-semibold text-slate-900"><?= $hotel['rating'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="rounded-lg bg-blue-100 p-2 text-blue-600 hover:bg-blue-200 transition" title="View">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="rounded-lg bg-yellow-100 p-2 text-yellow-600 hover:bg-yellow-200 transition" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 transition" title="Delete">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-slate-600">Showing 1 to 5 of 247 hotels</p>
                <div class="flex gap-2">
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Previous</button>
                    <button class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white hover:bg-accentLight transition">1</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">2</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">3</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Next</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        // Tampilkan atau sembunyikan sidebar saat tombol hamburger ditekan.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        // Pastikan sidebar tertutup, dipakai saat klik overlay atau navigasi.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Tutup sidebar otomatis saat user memilih menu navigasi.
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Sesuaikan perilaku saat window diperbesar agar sidebar tetap terlihat di desktop.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>