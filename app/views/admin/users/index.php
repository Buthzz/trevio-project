<?php
// Judul halaman untuk modul manajemen user.
$pageTitle = 'Admin Users - Trevio';
// Link referensi ke dashboard untuk elemen logo/header.
$homeLink  = '../dashboard.php';
// Sertakan header global agar gaya konsisten.
include __DIR__ . '/../../layouts/header.php';
?>

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
                <a href="../dashboard.php" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                    </svg>
                    Dashboard
                </a>
                     <a href="../hotels/index.php" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                    </svg>
                    Hotels
                </a>
                     <a href="../payments/index.php" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Payments
                </a>
                     <a href="../refunds/index.php" 
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                    </svg>
                    Refunds
                </a>
                     <a href="index.php" 
                   class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                    </svg>
                    Users
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Users Management</h1>
                    <p class="mt-2 text-slate-600">Kelola dan monitor semua pengguna sistem</p>
                </div>
                <button class="inline-flex items-center gap-2 rounded-lg bg-accent px-6 py-3 text-white font-semibold hover:bg-accentLight transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add User
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4 mb-8">
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Users</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">5,678</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Active</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">5,423</p>
                            <p class="mt-1 text-xs text-green-600">95.5%</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Inactive</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">255</p>
                            <p class="mt-1 text-xs text-yellow-600">4.5%</p>
                        </div>
                        <div class="rounded-full bg-yellow-100 p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Suspended</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">10</p>
                            <p class="mt-1 text-xs text-red-600">0.2%</p>
                        </div>
                        <div class="rounded-full bg-red-100 p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.343 17.657l1.414-1.414m2.828 2.828l1.414-1.414m5.656 0l1.414 1.414m2.828-2.828l1.414-1.414M6 12a6 6 0 1112 0 6 6 0 01-12 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="mb-6 flex flex-col gap-4 lg:flex-row">
                <input type="text" placeholder="Cari pengguna..." 
                       class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                <select class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option>Semua Role</option>
                    <option>Customer</option>
                    <option>Hotel Owner</option>
                    <option>Admin</option>
                </select>
                <select class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option>Semua Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                    <option>Suspended</option>
                </select>
                <button class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-300 transition">
                    Filter
                </button>
            </div>

            <!-- Users Table -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-[1024px] w-full table-auto">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">User</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Role</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Join Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Last Active</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-900 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php 
                        // Dataset dummy pengguna sebagai pengganti data database.
                        $users = [
                            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Customer', 'status' => 'active', 'joinDate' => '2023-06-15', 'lastActive' => '5 mins ago'],
                            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Hotel Owner', 'status' => 'active', 'joinDate' => '2023-07-20', 'lastActive' => '2 hours ago'],
                            ['id' => 3, 'name' => 'Ahmad Reza', 'email' => 'ahmad@example.com', 'role' => 'Customer', 'status' => 'active', 'joinDate' => '2023-08-10', 'lastActive' => '1 day ago'],
                            ['id' => 4, 'name' => 'Siti Rahma', 'email' => 'siti@example.com', 'role' => 'Hotel Owner', 'status' => 'inactive', 'joinDate' => '2023-05-12', 'lastActive' => '30 days ago'],
                            ['id' => 5, 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'role' => 'Customer', 'status' => 'active', 'joinDate' => '2023-09-01', 'lastActive' => '3 hours ago'],
                            ['id' => 6, 'name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'role' => 'Customer', 'status' => 'suspended', 'joinDate' => '2023-04-05', 'lastActive' => 'Never'],
                            ['id' => 7, 'name' => 'Rudi Hartono', 'email' => 'rudi@example.com', 'role' => 'Hotel Owner', 'status' => 'active', 'joinDate' => '2023-10-15', 'lastActive' => '12 mins ago'],
                            ['id' => 8, 'name' => 'Maya Indah', 'email' => 'maya@example.com', 'role' => 'Customer', 'status' => 'active', 'joinDate' => '2023-11-22', 'lastActive' => '45 mins ago'],
                        ];
                        // Variasi warna avatar supaya list lebih hidup.
                        $avatarPalettes = [
                            ['bg' => 'bg-blue-100', 'icon' => 'text-blue-600'],
                            ['bg' => 'bg-purple-100', 'icon' => 'text-purple-600'],
                            ['bg' => 'bg-teal-100', 'icon' => 'text-teal-600'],
                            ['bg' => 'bg-amber-100', 'icon' => 'text-amber-600'],
                        ];
                        foreach ($users as $index => $user): 
                            // Pilih kombinasi warna avatar berdasarkan indeks baris.
                            $palette = $avatarPalettes[$index % count($avatarPalettes)];
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full <?= htmlspecialchars($palette['bg']) ?> shadow-inner">
                                        <svg class="h-5 w-5 <?= htmlspecialchars($palette['icon']) ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($user['name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                // Mapping role pengguna ke warna badge masing-masing.
                                $roleColors = [
                                    'Customer' => 'bg-blue-100 text-blue-700',
                                    'Hotel Owner' => 'bg-purple-100 text-purple-700',
                                    'Admin' => 'bg-red-100 text-red-700'
                                ];
                                $roleClass = $roleColors[$user['role']] ?? 'bg-slate-100 text-slate-700';
                                ?>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $roleClass ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                // Mapping status akun ke warna badge status.
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'inactive' => 'bg-yellow-100 text-yellow-700',
                                    'suspended' => 'bg-red-100 text-red-700'
                                ];
                                $statusClass = $statusColors[$user['status']] ?? 'bg-slate-100 text-slate-700';
                                ?>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $statusClass ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($user['joinDate']) ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($user['lastActive']) ?></td>
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
                                    <button class="rounded-lg bg-orange-100 p-2 text-orange-600 hover:bg-orange-200 transition" title="Suspend">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
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
                <p class="text-sm text-slate-600">Showing 1 to 8 of 5,678 users</p>
                <div class="flex gap-2">
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Previous</button>
                    <button class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white hover:bg-accentLight transition">1</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">2</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">3</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">...</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Next</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Referensi elemen yang dibutuhkan untuk mengendalikan sidebar.
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const adminSidebar = document.getElementById('adminSidebar');

    function toggleSidebar() {
        // Buka/tutup sidebar dan overlay saat tombol utama ditekan.
        adminSidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        // Pastikan sidebar tertutup dan overlay disembunyikan.
        adminSidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }

    // Tutup sidebar secara otomatis ketika user menekan salah satu link menu.
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Saat ukuran layar melebar, tampilkan sidebar permanen dan sembunyikan overlay.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
