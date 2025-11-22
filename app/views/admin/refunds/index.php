<?php
// Tautan kembali ke dashboard untuk digunakan header global.
$homeLink = '../dashboard.php';

// Data dummy refund agar UI bisa diuji tanpa database.
$refunds = [
    [
        'id' => 'REF-001',
        'customer' => 'John Doe',
        'email' => 'john@example.com',
        'booking' => 'BK-001',
        'hotel' => 'Grand Hotel Jakarta',
        'amount' => 'Rp 2.500.000',
        'amountNumeric' => 2500000,
        'reason' => 'Cancelled due to personal reasons',
        'requestDate' => '2024-01-15',
        'requestTime' => '14:30:00',
        'status' => 'pending',
        'bookingDates' => '15 Jan 2024 - 17 Jan 2024',
        'paymentMethod' => 'Bank Transfer',
        'bankAccount' => 'BCA - 1234567890',
        'originalTransaction' => 'TXN-001',
        'notes' => 'Customer requested cancellation',
        'date' => '2024-01-15',
    ],
    [
        'id' => 'REF-002',
        'customer' => 'Jane Smith',
        'email' => 'jane@example.com',
        'booking' => 'BK-002',
        'hotel' => 'Luxury Resort Bali',
        'amount' => 'Rp 4.200.000',
        'amountNumeric' => 4200000,
        'reason' => 'Unable to travel',
        'requestDate' => '2024-01-14',
        'requestTime' => '10:05:00',
        'status' => 'approved',
        'bookingDates' => '22 Jan 2024 - 25 Jan 2024',
        'paymentMethod' => 'Credit Card',
        'bankAccount' => 'Visa **** 8890',
        'originalTransaction' => 'TXN-002',
        'notes' => 'Awaiting finance release',
        'date' => '2024-01-14',
    ],
    [
        'id' => 'REF-003',
        'customer' => 'Ahmad Reza',
        'email' => 'ahmad@example.com',
        'booking' => 'BK-003',
        'hotel' => 'Business Inn Surabaya',
        'amount' => 'Rp 1.800.000',
        'amountNumeric' => 1800000,
        'reason' => 'Better rate found',
        'requestDate' => '2024-01-14',
        'requestTime' => '18:45:00',
        'status' => 'pending',
        'bookingDates' => '02 Feb 2024 - 04 Feb 2024',
        'paymentMethod' => 'E-Wallet',
        'bankAccount' => 'OVO-112233',
        'originalTransaction' => 'TXN-003',
        'notes' => 'Need confirmation from property',
        'date' => '2024-01-14',
    ],
    [
        'id' => 'REF-004',
        'customer' => 'Siti Rahma',
        'email' => 'siti@example.com',
        'booking' => 'BK-004',
        'hotel' => 'Boutique Hotel Bandung',
        'amount' => 'Rp 3.100.000',
        'amountNumeric' => 3100000,
        'reason' => 'Room unavailable',
        'requestDate' => '2024-01-13',
        'requestTime' => '07:55:00',
        'status' => 'completed',
        'bookingDates' => '10 Jan 2024 - 12 Jan 2024',
        'paymentMethod' => 'Bank Transfer',
        'bankAccount' => 'Bri - 5566778899',
        'originalTransaction' => 'TXN-004',
        'notes' => 'Refund completed last week',
        'date' => '2024-01-13',
    ],
    [
        'id' => 'REF-005',
        'customer' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'booking' => 'BK-005',
        'hotel' => 'Beach Resort Lombok',
        'amount' => 'Rp 5.600.000',
        'amountNumeric' => 5600000,
        'reason' => 'Service complaint',
        'requestDate' => '2024-01-13',
        'requestTime' => '16:20:00',
        'status' => 'rejected',
        'bookingDates' => '05 Feb 2024 - 08 Feb 2024',
        'paymentMethod' => 'Credit Card',
        'bankAccount' => 'Mastercard **** 7721',
        'originalTransaction' => 'TXN-005',
        'notes' => 'Rejected due to policy',
        'date' => '2024-01-13',
    ],
    [
        'id' => 'REF-006',
        'customer' => 'Dewi Lestari',
        'email' => 'dewi@example.com',
        'booking' => 'BK-006',
        'hotel' => 'Grand Hotel Jakarta',
        'amount' => 'Rp 2.900.000',
        'amountNumeric' => 2900000,
        'reason' => 'Cancelled',
        'requestDate' => '2024-01-12',
        'requestTime' => '12:00:00',
        'status' => 'completed',
        'bookingDates' => '27 Jan 2024 - 29 Jan 2024',
        'paymentMethod' => 'E-Wallet',
        'bankAccount' => 'GOPAY-778899',
        'originalTransaction' => 'TXN-006',
        'notes' => 'Refund settled via wallet',
        'date' => '2024-01-12',
    ],
];

// Susun map ID refund ke data detail untuk akses cepat.
$refundMap = [];
foreach ($refunds as $refund) {
    $refundMap[$refund['id']] = $refund;
}

// Tangkap parameter id jika halaman dipanggil dalam mode detail.
$requestedRefundId = isset($_GET['id']) ? (string) $_GET['id'] : null;
// Ambil data refund terkait jika ID tersedia di daftar.
$selectedRefund = ($requestedRefundId && isset($refundMap[$requestedRefundId])) ? $refundMap[$requestedRefundId] : null;
// Tandai kondisi ketika ID diminta tetapi tidak ditemukan.
$detailNotFound = $requestedRefundId && !$selectedRefund;

// Ubah judul halaman sesuai konteks (daftar umum vs proses).
$pageTitle = $selectedRefund ? 'Process Refund - Trevio' : 'Admin Refunds - Trevio';

// Warna badge berdasarkan status refund.
$statusColors = [
    'pending' => 'bg-yellow-100 text-yellow-700',
    'approved' => 'bg-blue-100 text-blue-700',
    'completed' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
];

include __DIR__ . '/../../layouts/header.php';
?>

<!-- Daftar refund + detail (mode admin) -->
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
                <a href="../dashboard.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="../hotels/index.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                    </svg>
                    Hotels
                </a>
                <a href="../payments/index.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Payments
                </a>
                <a href="index.php" class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                    </svg>
                    Refunds
                </a>
                <a href="../users/index.php" class="flex items-center gap-3 rounded-lg px-4 py-3 text-slate-600 hover:bg-slate-100 transition font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm6-11a4 4 0 110 5.292M21 21h-8v-2a6 6 0 018-5.73"></path>
                    </svg>
                    Users
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <!-- Tabel/ref detail refund -->
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <?php if ($selectedRefund): ?>
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <a href="index.php" class="text-accent hover:underline text-sm font-medium">← Back to Refunds</a>
                        <h1 class="mt-2 text-3xl font-bold text-slate-900">Process Refund</h1>
                        <p class="mt-1 text-slate-600">Refund ID: <?= htmlspecialchars($selectedRefund['id']) ?></p>
                    </div>
                </div>

                <?php $detailBadgeClass = $statusColors[$selectedRefund['status']] ?? 'bg-slate-100 text-slate-700'; ?>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Refund Request Details</h2>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Refund ID</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['id']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Status</p>
                                        <p class="mt-1">
                                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $detailBadgeClass ?>">
                                                <?= ucfirst($selectedRefund['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Refund Amount</p>
                                        <p class="mt-1 text-xl font-bold text-slate-900"><?= htmlspecialchars($selectedRefund['amount']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Original Transaction</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['originalTransaction']) ?></p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Request Date</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['requestDate']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Request Time</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['requestTime']) ?></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Reason for Refund</p>
                                    <p class="mt-1 text-slate-700"><?= htmlspecialchars($selectedRefund['reason']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Customer &amp; Booking Information</h2>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Customer Name</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['customer']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Email</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['email']) ?></p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Booking ID</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['booking']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Hotel</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['hotel']) ?></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Check-in &amp; Check-out</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['bookingDates']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Refund Bank Account</h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Payment Method</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['paymentMethod']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Bank Account</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedRefund['bankAccount']) ?></p>
                                </div>
                                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                                    <p class="text-sm text-blue-900"><span class="font-semibold">Note:</span> Refund akan ditransfer ke rekening bank pelanggan sesuai metode pembayaran original.</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-lg font-bold text-slate-900">Notes</h2>
                            <textarea placeholder="Tambahkan catatan atau komentar..." rows="4" class="w-full rounded-lg border border-slate-300 p-3 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"><?= htmlspecialchars($selectedRefund['notes']) ?></textarea>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Processing Status</h2>
                            <div class="space-y-4">
                                <div class="space-y-4">
                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">1</div>
                                            <div class="w-0.5 h-12 bg-blue-200 mt-2"></div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Review Request</p>
                                            <p class="text-xs text-slate-600">Completed</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full <?= $selectedRefund['status'] === 'pending' ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white' ?> flex items-center justify-center text-xs font-bold">2</div>
                                            <div class="w-0.5 h-12 bg-slate-300 mt-2"></div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Process Refund</p>
                                            <p class="text-xs text-slate-600"><?= $selectedRefund['status'] === 'pending' ? 'Pending' : 'In progress / done' ?></p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full <?= in_array($selectedRefund['status'], ['completed']) ? 'bg-green-500 text-white' : 'bg-slate-300 text-slate-600' ?> flex items-center justify-center text-xs font-bold">3</div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Confirm Completion</p>
                                            <p class="text-xs text-slate-600"><?= $selectedRefund['status'] === 'completed' ? 'Completed' : 'Waiting' ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-lg font-bold text-slate-900">Actions</h2>
                            <div class="space-y-3">
                                <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 text-white font-semibold hover:bg-green-700 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approve Refund
                                </button>
                                <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Process Payment
                                </button>
                                <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-white font-semibold hover:bg-red-700 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject Refund
                                </button>
                                <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-slate-200 px-4 py-3 text-slate-700 font-semibold hover:bg-slate-300 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Request More Info
                                </button>
                            </div>
                        </div>

                        <div class="rounded-xl bg-red-50 border border-red-200 p-6">
                            <h3 class="text-sm font-bold text-red-900 mb-3">Important Notes</h3>
                            <ul class="text-xs text-red-800 space-y-2">
                                <li class="flex gap-2"><span class="text-red-600 font-bold">•</span><span>Pastikan data bank pelanggan sudah terverifikasi</span></li>
                                <li class="flex gap-2"><span class="text-red-600 font-bold">•</span><span>Refund biasanya memerlukan waktu 3-5 hari kerja</span></li>
                                <li class="flex gap-2"><span class="text-red-600 font-bold">•</span><span>Simpan bukti refund untuk laporan audit</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-slate-900">Refund Management</h1>
                    <p class="mt-2 text-slate-600">Proses dan kelola permintaan refund dari pelanggan</p>
                </div>

                <?php if ($detailNotFound): ?>
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        Data refund tidak ditemukan. Menampilkan seluruh permintaan.
                    </div>
                <?php endif; ?>

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

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-[1024px] w-full table-auto">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Refund ID</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Customer</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Booking</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Amount</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Reason</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Request Date</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Status</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-900 whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php
                            // Pilihan warna avatar pelanggan agar baris mudah dibedakan.
                            $customerPalettes = [
                                ['bg' => 'bg-purple-100', 'icon' => 'text-purple-600'],
                                ['bg' => 'bg-blue-100', 'icon' => 'text-blue-600'],
                                ['bg' => 'bg-teal-100', 'icon' => 'text-teal-600'],
                                ['bg' => 'bg-rose-100', 'icon' => 'text-rose-600'],
                            ];
                            foreach ($refunds as $index => $refund):
                                // Ambil kombinasi warna berdasarkan posisi baris.
                                $customerPalette = $customerPalettes[$index % count($customerPalettes)];
                                // Tentukan kelas warna badge status untuk baris aktif.
                                $statusClass = $statusColors[$refund['status']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($refund['id']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full <?= htmlspecialchars($customerPalette['bg']) ?> shadow-inner">
                                            <svg class="h-5 w-5 <?= htmlspecialchars($customerPalette['icon']) ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804"></path>
                                            </svg>
                                        </div>
                                        <span class="text-slate-600"><?= htmlspecialchars($refund['customer']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($refund['booking']) ?></td>
                                <td class="px-6 py-4 font-semibold text-slate-900"><?= htmlspecialchars($refund['amount']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700"><?= htmlspecialchars($refund['reason']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($refund['date']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $statusClass ?>">
                                        <?= ucfirst($refund['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="rounded-lg bg-blue-100 p-2 text-blue-600 hover:bg-blue-200 transition" title="View">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <a href="index.php?id=<?= urlencode($refund['id']) ?>" class="rounded-lg bg-yellow-100 p-2 text-yellow-600 hover:bg-yellow-200 transition" title="Process">
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
                </div>

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
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        // Mengelola buka/tutup sidebar ketika tombol hamburger digunakan.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        // Menutup sidebar secara paksa agar fokus kembali ke konten.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Tutup sidebar saat user memilih salah satu link navigasi.
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Jika layar lebar, pastikan sidebar tampil dan overlay menghilang.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
