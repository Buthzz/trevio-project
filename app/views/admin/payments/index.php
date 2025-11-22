<?php
// Simpan tautan dasar ke dashboard untuk navigasi logo header.
$homeLink = '../dashboard.php';

// Dataset dummy pembayaran sebagai pengganti data dari database.
$payments = [
    [
        'id' => 'TXN-001',
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
        'date' => '2024-01-15',
        'status' => 'verified',
        'proofUrl' => '../../../public/images/payment-proof.jpg',
        'notes' => 'Payment received from customer',
    ],
    [
        'id' => 'TXN-002',
        'user' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone' => '+62822334455',
        'hotel' => 'Luxury Resort Bali',
        'hotelLocation' => 'Bali Selatan',
        'bookingDates' => '20 Jan 2024 - 23 Jan 2024',
        'rooms' => 1,
        'guests' => 2,
        'amount' => 'Rp 4.200.000',
        'amountNumeric' => 4200000,
        'method' => 'Credit Card',
        'bankName' => 'Visa Platinum',
        'accountNumber' => '**** 8890',
        'transferDate' => '2024-01-15 09:05:00',
        'date' => '2024-01-15',
        'status' => 'verified',
        'proofUrl' => '../../../public/images/payment-proof.jpg',
        'notes' => 'Auto verified via gateway',
    ],
    [
        'id' => 'TXN-003',
        'user' => 'Ahmad Reza',
        'email' => 'ahmad@example.com',
        'phone' => '+628567889900',
        'hotel' => 'Business Inn Surabaya',
        'hotelLocation' => 'Surabaya Pusat',
        'bookingDates' => '12 Jan 2024 - 14 Jan 2024',
        'rooms' => 1,
        'guests' => 1,
        'amount' => 'Rp 1.800.000',
        'amountNumeric' => 1800000,
        'method' => 'E-Wallet',
        'bankName' => 'OVO',
        'accountNumber' => 'OVO-334455',
        'transferDate' => '2024-01-14 19:20:00',
        'date' => '2024-01-14',
        'status' => 'pending',
        'proofUrl' => '../../../public/images/payment-proof.jpg',
        'notes' => 'Waiting manual confirmation',
    ],
    [
        'id' => 'TXN-004',
        'user' => 'Siti Rahma',
        'email' => 'siti@example.com',
        'phone' => '+628778899001',
        'hotel' => 'Boutique Hotel Bandung',
        'hotelLocation' => 'Bandung Utara',
        'bookingDates' => '18 Jan 2024 - 20 Jan 2024',
        'rooms' => 2,
        'guests' => 3,
        'amount' => 'Rp 3.100.000',
        'amountNumeric' => 3100000,
        'method' => 'Bank Transfer',
        'bankName' => 'Bank Mandiri',
        'accountNumber' => '9876543210',
        'transferDate' => '2024-01-14 11:45:00',
        'date' => '2024-01-14',
        'status' => 'verified',
        'proofUrl' => '../../../public/images/payment-proof.jpg',
        'notes' => 'Verified by admin',
    ],
    [
        'id' => 'TXN-005',
        'user' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '+628990011223',
        'hotel' => 'Beach Resort Lombok',
        'hotelLocation' => 'Lombok Tengah',
        'bookingDates' => '05 Feb 2024 - 08 Feb 2024',
        'rooms' => 1,
        'guests' => 2,
        'amount' => 'Rp 5.600.000',
        'amountNumeric' => 5600000,
        'method' => 'Credit Card',
        'bankName' => 'Mastercard Signature',
        'accountNumber' => '**** 7721',
        'transferDate' => '2024-01-13 08:25:00',
        'date' => '2024-01-13',
        'status' => 'pending',
        'proofUrl' => '../../../public/images/payment-proof.jpg',
        'notes' => 'Need follow up with bank',
    ],
    [
        'id' => 'TXN-006',
        'user' => 'Dewi Lestari',
        'email' => 'dewi@example.com',
        'phone' => '+628334455667',
        'hotel' => 'Grand Hotel Jakarta',
        'hotelLocation' => 'Jakarta Pusat',
        'bookingDates' => '27 Jan 2024 - 29 Jan 2024',
        'rooms' => 1,
        'guests' => 1,
        'amount' => 'Rp 2.900.000',
        'amountNumeric' => 2900000,
        'method' => 'E-Wallet',
        'bankName' => 'GoPay',
        'accountNumber' => 'GOPAY-556677',
        'transferDate' => '2024-01-13 21:10:00',
        'date' => '2024-01-13',
        'status' => 'verified',
        'proofUrl' => '../../../public/images/payment-proof.jpg',
        'notes' => 'Auto verified via e-wallet',
    ],
];

// Index pembayaran per ID supaya mudah ambil detail tertentu.
$paymentMap = [];
foreach ($payments as $payment) {
    $paymentMap[$payment['id']] = $payment;
}

// Baca parameter id pada query string untuk menentukan mode detail/verifikasi.
$requestedPaymentId = isset($_GET['id']) ? (string) $_GET['id'] : null;
// Ambil data pembayaran yang sesuai jika ID valid.
$selectedPayment = ($requestedPaymentId && isset($paymentMap[$requestedPaymentId])) ? $paymentMap[$requestedPaymentId] : null;
// Tandai ketika ID diminta namun tidak ditemukan.
$detailNotFound = $requestedPaymentId && !$selectedPayment;

// Ubah judul halaman sesuai konteks (daftar atau verifikasi).
$pageTitle = $selectedPayment ? 'Verify Payment - Trevio' : 'Admin Payments - Trevio';

// Mapping status pembayaran ke warna badge.
$statusColors = [
    'verified' => 'bg-green-100 text-green-700',
    'pending' => 'bg-yellow-100 text-yellow-700',
    'failed' => 'bg-red-100 text-red-700',
];

include __DIR__ . '/../../layouts/header.php';
?>

<!-- Daftar pembayaran + detail admin -->
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
                <a href="index.php" 
               class="flex items-center gap-3 rounded-lg bg-accent/10 px-4 py-3 text-accent font-semibold transition">
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
                <a href="../users/index.php" 
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
    <!-- Area konten tabel/preview pembayaran -->
    <main class="flex-1 overflow-auto">
        <div class="p-6 md:p-8">
            <?php if ($selectedPayment): ?>
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <a href="index.php" class="text-accent hover:underline text-sm font-medium">← Back to Payments</a>
                        <h1 class="mt-2 text-3xl font-bold text-slate-900">Verify Payment</h1>
                        <p class="mt-1 text-slate-600">Transaction ID: <?= htmlspecialchars($selectedPayment['id']) ?></p>
                    </div>
                </div>

                <?php
                // Pilih kelas warna untuk badge status detail.
                $detailBadgeClass = $statusColors[$selectedPayment['status']] ?? 'bg-slate-100 text-slate-700';

                // Banner informasi tambahan berdasarkan status verifikasi.
                $stateBanners = [
                    'verified' => [
                        'wrapper' => 'bg-green-50 border-green-200',
                        'icon' => 'text-green-600',
                        'title' => 'Payment Verified',
                        'subtitle' => 'This transaction has been approved',
                    ],
                    'failed' => [
                        'wrapper' => 'bg-red-50 border-red-200',
                        'icon' => 'text-red-600',
                        'title' => 'Verification Failed',
                        'subtitle' => 'Follow up with customer for resolution',
                    ],
                    'pending' => [
                        'wrapper' => 'bg-yellow-50 border-yellow-200',
                        'icon' => 'text-yellow-600',
                        'title' => 'Awaiting Verification',
                        'subtitle' => 'Review payment proof carefully',
                    ],
                ];
                // Default ke banner pending jika status belum terdaftar.
                $banner = $stateBanners[$selectedPayment['status']] ?? $stateBanners['pending'];
                ?>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Transaction Details</h2>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Transaction ID</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['id']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Status</p>
                                        <p class="mt-1">
                                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $detailBadgeClass ?>">
                                                <?= ucfirst($selectedPayment['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Amount</p>
                                        <p class="mt-1 text-xl font-bold text-slate-900"><?= htmlspecialchars($selectedPayment['amount']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Payment Method</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['method']) ?></p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Bank Name</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['bankName']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Account Number</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['accountNumber']) ?></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Transfer Date &amp; Time</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['transferDate']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Customer Information</h2>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Full Name</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['user']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Email</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['email']) ?></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Phone Number</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['phone']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Booking Details</h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Hotel Name</p>
                                    <p class="mt-1 font-semibold text-slate-900"><?= htmlspecialchars($selectedPayment['hotel']) ?></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Location</p>
                                        <p class="mt-1 text-slate-900"><?= htmlspecialchars($selectedPayment['hotelLocation']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Check-in &amp; Check-out</p>
                                        <p class="mt-1 text-slate-900"><?= htmlspecialchars($selectedPayment['bookingDates']) ?></p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Rooms</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= (int) $selectedPayment['rooms'] ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Number of Guests</p>
                                        <p class="mt-1 font-semibold text-slate-900"><?= (int) $selectedPayment['guests'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Payment Proof</h2>
                            <div class="rounded-lg overflow-hidden bg-slate-100 h-80">
                                <img src="<?= htmlspecialchars($selectedPayment['proofUrl']) ?>" alt="Payment Proof" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22320%22%3E%3Crect fill=%22%23e2e8f0%22 width=%22400%22 height=%22320%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%239ca3af%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EPayment Proof Image%3C/text%3E%3C/svg%3E'">
                            </div>
                            <p class="mt-4 text-sm text-slate-600">Download or view original proof image for verification</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-lg font-bold text-slate-900">Verification Status</h2>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 p-3 rounded-lg <?= htmlspecialchars($banner['wrapper']) ?>">
                                    <svg class="h-5 w-5 <?= htmlspecialchars($banner['icon']) ?> flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2M15 9h2m0 4h2m0 0h2M9 9h2m0 4h2m0 0h2"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($banner['title']) ?></p>
                                        <p class="text-xs text-slate-700"><?= htmlspecialchars($banner['subtitle']) ?></p>
                                    </div>
                                </div>

                                <?php if ($selectedPayment['status'] === 'pending'): ?>
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
                                <?php endif; ?>
                            </div>
                        </div>

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

                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-lg font-bold text-slate-900">Admin Notes</h2>
                            <textarea placeholder="Add notes about this payment..." rows="5" class="w-full rounded-lg border border-slate-300 p-3 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"></textarea>
                            <button class="mt-3 w-full rounded-lg bg-accent px-4 py-2 text-white font-medium hover:bg-accentLight transition">
                                Save Notes
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-slate-900">Payment Verification</h1>
                    <p class="mt-2 text-slate-600">Verifikasi dan kelola semua transaksi pembayaran</p>
                </div>

                <?php if ($detailNotFound): ?>
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        Data transaksi tidak ditemukan. Menampilkan seluruh pembayaran.
                    </div>
                <?php endif; ?>

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

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-[1024px] w-full table-auto">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Transaction ID</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">User</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Hotel</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Amount</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Payment Method</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Date</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 whitespace-nowrap">Status</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-900 whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php
                            // Variasi warna avatar user agar tabel tidak monoton.
                            $userPalettes = [
                                ['bg' => 'bg-blue-100', 'icon' => 'text-blue-600'],
                                ['bg' => 'bg-indigo-100', 'icon' => 'text-indigo-600'],
                                ['bg' => 'bg-teal-100', 'icon' => 'text-teal-600'],
                                ['bg' => 'bg-purple-100', 'icon' => 'text-purple-600'],
                            ];
                            foreach ($payments as $index => $payment):
                                // Pilih warna avatar berdasarkan indeks baris.
                                $userPalette = $userPalettes[$index % count($userPalettes)];
                                // Tentukan kelas warna badge status untuk setiap baris.
                                $statusClass = $statusColors[$payment['status']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-900"><?= htmlspecialchars($payment['id']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full <?= htmlspecialchars($userPalette['bg']) ?> shadow-inner">
                                            <svg class="h-5 w-5 <?= htmlspecialchars($userPalette['icon']) ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804"></path>
                                            </svg>
                                        </div>
                                        <span class="text-slate-600"><?= htmlspecialchars($payment['user']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($payment['hotel']) ?></td>
                                <td class="px-6 py-4 font-semibold text-slate-900"><?= htmlspecialchars($payment['amount']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700"><?= htmlspecialchars($payment['method']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($payment['date']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium <?= $statusClass ?>">
                                        <?= ucfirst($payment['status']) ?>
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
                                        <a href="index.php?id=<?= urlencode($payment['id']) ?>" class="rounded-lg bg-green-100 p-2 text-green-600 hover:bg-green-200 transition" title="Verify">
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
                </div>

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
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        // Kendalikan visibilitas sidebar saat tombol hamburger ditekan.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function closeSidebar() {
        // Paksa sidebar menutup, dipanggil dari overlay maupun navigasi.
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    // Saat user klik menu, tutup sidebar agar pengalaman mobile nyaman.
    document.querySelectorAll('#adminSidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Jika layar melebar ke desktop, pastikan sidebar tampil dan overlay hilang.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.getElementById('adminSidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.add('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
