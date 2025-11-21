<?php
$pageTitle = 'Admin Refunds - Trevio';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="flex h-screen bg-slate-50">
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

        <nav class="space-y-2">
            <a href="/admin/dashboard" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                </svg>
                Dashboard
            </a>
            <a href="/admin/hotels" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                </svg>
                Hotels
            </a>
            <a href="/admin/payments" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Payments
            </a>
            <a href="/admin/refunds" 
               class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                </svg>
                Refunds
            </a>
            <a href="/admin/users" 
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
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Refund Management</h1>
                <p class="mt-2 text-slate-600">Proses dan kelola permintaan refund dari pelanggan</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Refunds</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">Rp 450.5M</p>
                            <p class="mt-1 text-xs text-slate-500">All time</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Processed</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">186</p>
                            <p class="mt-1 text-xs text-green-600">Completed</p>
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
                            <p class="text-sm font-medium text-slate-600">Pending</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">23</p>
                            <p class="mt-1 text-xs text-red-600">Need processing</p>
                        </div>
                        <div class="rounded-full bg-red-100 p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2M15 9h2m0 4h2m0 0h2M9 9h2m0 4h2m0 0h2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="mb-6 flex flex-col gap-4 lg:flex-row">
                <input type="text" placeholder="Cari refund request..." 
                       class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                <select class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option>Semua Status</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Rejected</option>
                    <option>Completed</option>
                </select>
                <input type="date" 
                       class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                <button class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-300 transition">
                    Filter
                </button>
            </div>

            <!-- Refunds Table -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Refund ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Customer</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Booking</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Reason</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Request Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php 
                        $refunds = [
                            ['id' => 'REF-001', 'customer' => 'John Doe', 'booking' => 'BK-001', 'amount' => 'Rp 2.500.000', 'reason' => 'Cancelled', 'date' => '2024-01-15', 'status' => 'pending'],
                            ['id' => 'REF-002', 'customer' => 'Jane Smith', 'booking' => 'BK-002', 'amount' => 'Rp 4.200.000', 'reason' => 'Unable to travel', 'date' => '2024-01-14', 'status' => 'approved'],
                            ['id' => 'REF-003', 'customer' => 'Ahmad Reza', 'booking' => 'BK-003', 'amount' => 'Rp 1.800.000', 'reason' => 'Better rate found', 'date' => '2024-01-14', 'status' => 'pending'],
                            ['id' => 'REF-004', 'customer' => 'Siti Rahma', 'booking' => 'BK-004', 'amount' => 'Rp 3.100.000', 'reason' => 'Room unavailable', 'date' => '2024-01-13', 'status' => 'completed'],
                            ['id' => 'REF-005', 'customer' => 'Budi Santoso', 'booking' => 'BK-005', 'amount' => 'Rp 5.600.000', 'reason' => 'Service complaint', 'date' => '2024-01-13', 'status' => 'rejected'],
                            ['id' => 'REF-006', 'customer' => 'Dewi Lestari', 'booking' => 'BK-006', 'amount' => 'Rp 2.900.000', 'reason' => 'Cancelled', 'date' => '2024-01-12', 'status' => 'completed'],
                        ];
                        foreach ($refunds as $refund): 
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-900"><?= htmlspecialchars($refund['id']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($refund['customer']) ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($refund['booking']) ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-900"><?= htmlspecialchars($refund['amount']) ?></td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700"><?= htmlspecialchars($refund['reason']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($refund['date']) ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'approved' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700'
                                ];
                                $statusClass = $statusColors[$refund['status']] ?? 'bg-slate-100 text-slate-700';
                                ?>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $statusClass ?>">
                                    <?= ucfirst($refund['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="rounded-lg bg-blue-100 p-2 text-blue-600 hover:bg-blue-200 transition" title="View">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <a href="/admin/refunds/process?id=<?= $refund['id'] ?>" class="rounded-lg bg-yellow-100 p-2 text-yellow-600 hover:bg-yellow-200 transition" title="Process">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </a>
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

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-slate-600">Showing 1 to 6 of 209 refund requests</p>
                <div class="flex gap-2">
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Previous</button>
                    <button class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white hover:bg-accentLight transition">1</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">2</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">...</button>
                    <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Next</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
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

    // Close sidebar when navigating
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
