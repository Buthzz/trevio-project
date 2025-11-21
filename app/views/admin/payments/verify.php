<?php
$pageTitle = 'Verify Payment - Trevio';
include __DIR__ . '/../../layouts/header.php';

// Sample payment data - ganti dengan data dari database berdasarkan ID
$paymentId = $_GET['id'] ?? 'TXN-001';
$payment = [
    'id' => $paymentId,
    'user' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+62812345678',
    'hotel' => 'Grand Hotel Jakarta',
    'hotelLocation' => 'Jakarta Pusat',
    'bookingDates' => '15 Jan 2024 - 17 Jan 2024',
    'rooms' => 2,
    'guests' => 4,
    'amount' => 'Rp 2.500.000',
    'amountNumeric' => 2500000,
    'method' => 'Bank Transfer',
    'bankName' => 'Bank Central Asia',
    'accountNumber' => '1234567890',
    'transferDate' => '2024-01-15 14:30:00',
    'proofUrl' => '../../../public/images/payment-proof.jpg',
    'status' => 'pending',
    'notes' => 'Payment received from customer',
];
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
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <a href="/admin/payments" class="text-accent hover:underline text-sm font-medium">← Back to Payments</a>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">Verify Payment</h1>
                    <p class="mt-1 text-slate-600">Transaction ID: <?= htmlspecialchars($payment['id']) ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column - Payment Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Details -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Transaction Details</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Transaction ID</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['id']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Status</p>
                                    <p class="mt-1">
                                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-700">
                                            <?= ucfirst($payment['status']) ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Amount</p>
                                    <p class="mt-1 text-xl font-bold text-slate-900"><?= htmlspecialchars($payment['amount']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Payment Method</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['method']) ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Bank Name</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['bankName']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Account Number</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['accountNumber']) ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-600">Transfer Date & Time</p>
                                <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['transferDate']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Customer Information</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Full Name</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['user']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Email</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['email']) ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-600">Phone Number</p>
                                <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['phone']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Booking Details</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Hotel Name</p>
                                <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($payment['hotel']) ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Location</p>
                                    <p class="mt-1 text-slate-900"><?= htmlspecialchars($payment['hotelLocation']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Check-in & Check-out</p>
                                    <p class="mt-1 text-slate-900"><?= htmlspecialchars($payment['bookingDates']) ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Rooms</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= $payment['rooms'] ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Number of Guests</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= $payment['guests'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Payment Proof</h2>
                        <div class="rounded-lg overflow-hidden bg-slate-100 h-80">
                            <img src="<?= $payment['proofUrl'] ?>" alt="Payment Proof" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22320%22%3E%3Crect fill=%22%23e2e8f0%22 width=%22400%22 height=%22320%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%239ca3af%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EPayment Proof Image%3C/text%3E%3C/svg%3E'">
                        </div>
                        <p class="mt-4 text-sm text-slate-600">Download or view original proof image for verification</p>
                    </div>
                </div>

                <!-- Right Column - Action Panel -->
                <div class="space-y-6">
                    <!-- Verification Status -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-lg font-bold text-slate-900">Verification Status</h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                                <svg class="h-5 w-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2M15 9h2m0 4h2m0 0h2M9 9h2m0 4h2m0 0h2"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-900">Awaiting Verification</p>
                                    <p class="text-xs text-yellow-800">Review payment proof carefully</p>
                                </div>
                            </div>

                            <div class="space-y-3 pt-4 border-t border-slate-200">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="check1" class="rounded">
                                    <label for="check1" class="text-sm text-slate-700">Amount matches booking</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="check2" class="rounded">
                                    <label for="check2" class="text-sm text-slate-700">Customer verified</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="check3" class="rounded">
                                    <label for="check3" class="text-sm text-slate-700">Proof document valid</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="check4" class="rounded">
                                    <label for="check4" class="text-sm text-slate-700">Payment received</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Actions</h2>
                        <div class="space-y-3">
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 text-white font-semibold hover:bg-green-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Approve Payment
                            </button>
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-white font-semibold hover:bg-red-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l5.5-5.5M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Reject Payment
                            </button>
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-slate-200 px-4 py-3 text-slate-700 font-semibold hover:bg-slate-300 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Request Information
                            </button>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-slate-900">Admin Notes</h2>
                        <textarea placeholder="Add notes about this payment..." rows="5" class="w-full rounded-lg border border-slate-300 p-3 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"></textarea>
                        <button class="mt-3 w-full rounded-lg bg-accent px-4 py-2 text-white font-medium hover:bg-accentLight transition">
                            Save Notes
                        </button>
                    </div>
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
