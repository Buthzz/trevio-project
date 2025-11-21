<?php
$pageTitle = 'Admin Payments - Trevio';
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
        <div class="p-6">
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
               class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Payments
            </a>
            <a href="/admin/refunds" 
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
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
                <h1 class="text-3xl font-bold text-slate-900">Payment Verification</h1>
                <p class="mt-2 text-slate-600">Verifikasi dan kelola semua transaksi pembayaran</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Total Payments</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">Rp 2.5M</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600">Verified</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">248</p>
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
                            <p class="mt-2 text-2xl font-bold text-slate-900">12</p>
                            <p class="mt-1 text-xs text-yellow-600">Need verification</p>
                        </div>
                        <div class="rounded-full bg-yellow-100 p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="mb-6 flex flex-col gap-4 lg:flex-row">
                <input type="text" placeholder="Cari pembayaran..." 
                       class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                <select class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option>Semua Status</option>
                    <option>Verified</option>
                    <option>Pending</option>
                    <option>Failed</option>
                </select>
                <input type="date" 
                       class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                <button class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-300 transition">
                    Filter
                </button>
            </div>

            <!-- Payments Table -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Transaction ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">User</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Hotel</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Payment Method</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php 
                        $payments = [
                            ['id' => 'TXN-001', 'user' => 'John Doe', 'hotel' => 'Grand Hotel Jakarta', 'amount' => 'Rp 2.500.000', 'method' => 'Bank Transfer', 'date' => '2024-01-15', 'status' => 'verified'],
                            ['id' => 'TXN-002', 'user' => 'Jane Smith', 'hotel' => 'Luxury Resort Bali', 'amount' => 'Rp 4.200.000', 'method' => 'Credit Card', 'date' => '2024-01-15', 'status' => 'verified'],
                            ['id' => 'TXN-003', 'user' => 'Ahmad Reza', 'hotel' => 'Business Inn Surabaya', 'amount' => 'Rp 1.800.000', 'method' => 'E-Wallet', 'date' => '2024-01-14', 'status' => 'pending'],
                            ['id' => 'TXN-004', 'user' => 'Siti Rahma', 'hotel' => 'Boutique Hotel Bandung', 'amount' => 'Rp 3.100.000', 'method' => 'Bank Transfer', 'date' => '2024-01-14', 'status' => 'verified'],
                            ['id' => 'TXN-005', 'user' => 'Budi Santoso', 'hotel' => 'Beach Resort Lombok', 'amount' => 'Rp 5.600.000', 'method' => 'Credit Card', 'date' => '2024-01-13', 'status' => 'pending'],
                            ['id' => 'TXN-006', 'user' => 'Dewi Lestari', 'hotel' => 'Grand Hotel Jakarta', 'amount' => 'Rp 2.900.000', 'method' => 'E-Wallet', 'date' => '2024-01-13', 'status' => 'verified'],
                        ];
                        foreach ($payments as $payment): 
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-900"><?= htmlspecialchars($payment['id']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($payment['user']) ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($payment['hotel']) ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-900"><?= htmlspecialchars($payment['amount']) ?></td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700"><?= htmlspecialchars($payment['method']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($payment['date']) ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                $statusColors = [
                                    'verified' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'failed' => 'bg-red-100 text-red-700'
                                ];
                                $statusClass = $statusColors[$payment['status']] ?? 'bg-slate-100 text-slate-700';
                                ?>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $statusClass ?>">
                                    <?= ucfirst($payment['status']) ?>
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
                                    <a href="/admin/payments/verify?id=<?= $payment['id'] ?>" class="rounded-lg bg-green-100 p-2 text-green-600 hover:bg-green-200 transition" title="Verify">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </a>
                                    <button class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 transition" title="Reject">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l5.5-5.5M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
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
                <p class="text-sm text-slate-600">Showing 1 to 6 of 260 payments</p>
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
